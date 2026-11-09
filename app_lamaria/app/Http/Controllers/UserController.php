<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Solo se permite asignar Administrador o Empleado
        $roles = Role::whereIn('nombre', ['Administrador', 'Empleado'])->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id' => [
                'required',
                Rule::exists('rol', 'id_rol')->whereIn('nombre', ['Administrador', 'Empleado']),
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->sync([$validated['role_id']]);

        return redirect()->route('users.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::whereIn('nombre', ['Administrador', 'Empleado'])->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role_id' => [
                'required',
                Rule::exists('rol', 'id_rol')->whereIn('nombre', ['Administrador', 'Empleado']),
            ],
        ]);

        $result = DB::transaction(function () use ($validated, $user): ?string {
            $adminRole = Role::where('nombre', 'Administrador')->lockForUpdate()->first();
            $isLastAdmin = $adminRole
                && $user->hasRole('Administrador')
                && $adminRole->users()->count() <= 1;

            if ($isLastAdmin && (int) $validated['role_id'] !== (int) $adminRole->id_rol) {
                return 'No se puede quitar el rol de Administrador al único administrador activo.';
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
            $user->roles()->sync([$validated['role_id']]);

            return null;
        });

        if ($result !== null) {
            return redirect()->route('users.index')->with('error', $result);
        }

        return redirect()->route('users.index')->with('success', 'Datos actualizados correctamente.');
    }

    public function destroy(User $user)
    {
        $result = DB::transaction(function () use ($user): bool {
            $adminRole = Role::where('nombre', 'Administrador')->lockForUpdate()->first();
            $isLastAdmin = $adminRole
                && $user->hasRole('Administrador')
                && $adminRole->users()->count() <= 1;

            if ($isLastAdmin) {
                return false;
            }

            $user->delete();

            return true;
        });

        if (! $result) {
            return redirect()->route('users.index')->with('error', 'No se puede eliminar al único usuario Administrador.');
        }

        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}
