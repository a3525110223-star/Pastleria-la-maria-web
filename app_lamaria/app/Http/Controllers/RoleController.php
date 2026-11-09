<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    private const SYSTEM_ROLES = ['Administrador', 'Empleado', 'Cliente'];

    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('nombre')->paginate(15);

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:rol,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        Role::create($validated);

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role): View
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rol', 'nombre')->ignore($role->id_rol, 'id_rol'),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        if (in_array($role->nombre, self::SYSTEM_ROLES, true) && $validated['nombre'] !== $role->nombre) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede cambiar el nombre de un rol del sistema.');
        }

        $role->update($validated);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->nombre, self::SYSTEM_ROLES, true)) {
            return redirect()->route('roles.index')
                ->with('error', 'No se pueden eliminar roles del sistema.');
        }

        if ($role->users()->exists()) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar un rol asignado a usuarios.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
