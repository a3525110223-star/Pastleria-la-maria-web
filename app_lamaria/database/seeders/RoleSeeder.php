<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Administrador' => 'Acceso total al sistema.',
            'Empleado' => 'Acceso operativo a Productos y Categorías.',
            'Cliente' => 'Rol para clientes de la pastelería.',
        ] as $nombre => $descripcion) {
            Role::firstOrCreate(['nombre' => $nombre], ['descripcion' => $descripcion]);
        }

        $admin = User::where('email', 'admin@lamaria.com')->first();
        $password = config('auth.seed_admin_password');

        if (! $admin && (! is_string($password) || strlen($password) < 12)) {
            throw new RuntimeException(
                'Define SEED_ADMIN_PASSWORD con al menos 12 caracteres en .env antes de ejecutar db:seed.'
            );
        }

        $admin ??= User::create([
            'name' => 'Administrador',
            'email' => 'admin@lamaria.com',
            'password' => Hash::make($password),
        ]);

        $adminRole = Role::where('nombre', 'Administrador')->firstOrFail();
        $admin->roles()->syncWithoutDetaching([$adminRole->id_rol]);
    }
}
