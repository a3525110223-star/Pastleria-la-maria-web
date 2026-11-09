<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_spanish_and_english_user_routes_are_available_with_internal_names(): void
    {
        $this->assertSame('/usuarios', parse_url(route('users.index'), PHP_URL_PATH));
        $this->assertSame('/users', parse_url(route('users.english.index'), PHP_URL_PATH));
        $this->assertSame('/usuarios/15/edit', parse_url(route('users.edit', 15), PHP_URL_PATH));
    }

    public function test_guest_is_redirected_to_login_for_admin_routes(): void
    {
        $this->get('/usuarios')->assertRedirect(route('login'));
        $this->get('/empresa')->assertRedirect(route('login'));
    }

    public function test_login_and_logout_complete_the_authenticated_session_flow(): void
    {
        $user = $this->userWithRole('Administrador');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_employee_is_forbidden_from_user_and_empresa_routes_and_menu_items_are_hidden(): void
    {
        $employee = $this->userWithRole('Empleado');
        $this->actingAs($employee);

        $this->get('/usuarios')->assertForbidden();
        $this->get('/users')->assertForbidden();
        $this->get('/empresa')->assertForbidden();
        $this->get('/roles')->assertForbidden();
        $this->get('/dashboard')
            ->assertOk()
            ->assertDontSee(route('users.index'))
            ->assertDontSee(route('empresa.edit'))
            ->assertDontSee(route('roles.index'));
    }

    public function test_admin_can_access_users_and_empresa_and_sees_admin_menu_items(): void
    {
        $this->actingAs($this->userWithRole('Administrador'));

        $this->get('/usuarios')
            ->assertOk()
            ->assertSee(route('empresa.edit'))
            ->assertSee(route('roles.index'));
        $this->get('/empresa')->assertOk();
    }

    public function test_user_creation_hashes_password_and_only_accepts_allowed_roles(): void
    {
        $admin = $this->userWithRole('Administrador');
        $employeeRole = Role::where('nombre', 'Empleado')->firstOrFail();
        $clientRole = Role::firstOrCreate(['nombre' => 'Cliente'], ['descripcion' => 'Cliente']);

        $this->actingAs($admin)->post('/usuarios', [
            'name' => 'Nuevo empleado',
            'email' => 'empleado@lamaria.com',
            'role_id' => $employeeRole->id_rol,
            'password' => 'Pasteleria123!',
            'password_confirmation' => 'Pasteleria123!',
        ])->assertRedirect(route('users.index'));

        $created = User::where('email', 'empleado@lamaria.com')->firstOrFail();
        $this->assertTrue(Hash::check('Pasteleria123!', $created->password));
        $this->assertTrue($created->hasRole('Empleado'));

        $this->post('/usuarios', [
            'name' => 'Cliente',
            'email' => 'cliente@lamaria.com',
            'role_id' => $clientRole->id_rol,
            'password' => 'Pasteleria123!',
            'password_confirmation' => 'Pasteleria123!',
        ])->assertSessionHasErrors('role_id');
    }

    public function test_last_administrator_cannot_be_deleted_or_demoted(): void
    {
        $admin = $this->userWithRole('Administrador');
        $employeeRole = Role::firstOrCreate(
            ['nombre' => 'Empleado'],
            ['descripcion' => 'Rol de prueba: Empleado']
        );
        $this->actingAs($admin);

        $this->delete(route('users.destroy', $admin))->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $this->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_id' => $employeeRole->id_rol,
        ])->assertRedirect(route('users.index'));

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_empresa_form_saves_the_central_record(): void
    {
        $this->actingAs($this->userWithRole('Administrador'));
        $this->put('/empresa', [
            'nombre_comercial' => 'Pastelería La María',
            'razon_social' => 'La María S.A.',
            'rfc' => 'ABC010203AB1',
            'telefono' => '5551112233',
            'numero_whatsapp' => '5551112233',
            'email' => 'contacto@lamaria.com',
            'ubicacion' => 'Av. Principal 123',
            'horario_normal' => 'Lunes a sábado',
            'horario_especial' => 'Domingos festivos',
            'descripcion' => 'Pastelería familiar',
            'logotipo' => 'logos/lamaria.png',
            'sitio_web' => 'https://lamaria.example',
        ])->assertRedirect(route('empresa.edit'));

        $this->assertDatabaseHas('empresa', [
            'id' => 1,
            'nombre_comercial' => 'Pastelería La María',
            'sitio_web' => 'https://lamaria.example',
        ]);
        $this->assertSame(1, Empresa::count());
    }

    public function test_role_module_prevents_deleting_core_roles(): void
    {
        $admin = $this->userWithRole('Administrador');
        $this->actingAs($admin);
        $role = Role::where('nombre', 'Administrador')->firstOrFail();

        $this->get('/roles')->assertOk();
        $this->delete(route('roles.destroy', $role))->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('rol', ['id_rol' => $role->id_rol, 'nombre' => 'Administrador']);
    }

    public function test_role_module_can_create_update_and_delete_custom_roles(): void
    {
        $this->actingAs($this->userWithRole('Administrador'));
        $this->post('/roles', [
            'nombre' => 'Supervisor',
            'descripcion' => 'Supervisión de turno',
        ])->assertRedirect(route('roles.index'));

        $role = Role::where('nombre', 'Supervisor')->firstOrFail();
        $this->put(route('roles.update', $role), [
            'nombre' => 'Supervisor de turno',
            'descripcion' => 'Responsable del turno',
        ])->assertRedirect(route('roles.index'));

        $role = $role->fresh();
        $this->assertSame('Responsable del turno', $role->descripcion);
        $this->delete(route('roles.destroy', $role))->assertRedirect(route('roles.index'));
        $this->assertDatabaseMissing('rol', ['id_rol' => $role->id_rol]);
    }

    public function test_seeder_creates_core_roles_and_an_admin_with_a_hashed_password(): void
    {
        config(['auth.seed_admin_password' => 'SeederPassword123!']);
        $this->seed(RoleSeeder::class);

        $this->assertDatabaseHas('rol', ['nombre' => 'Administrador']);
        $this->assertDatabaseHas('rol', ['nombre' => 'Empleado']);
        $this->assertDatabaseHas('rol', ['nombre' => 'Cliente']);

        $admin = User::where('email', 'admin@lamaria.com')->firstOrFail();
        $this->assertTrue(Hash::check('SeederPassword123!', $admin->password));
        $this->assertTrue($admin->isAdmin());
    }

    private function userWithRole(string $roleName): User
    {
        foreach (['Administrador', 'Empleado', 'Cliente'] as $name) {
            Role::firstOrCreate(
                ['nombre' => $name],
                ['descripcion' => "Rol de prueba: {$name}"]
            );
        }

        $role = Role::where('nombre', $roleName)->firstOrFail();
        $user = User::create([
            'name' => "Usuario {$roleName}",
            'email' => strtolower($roleName).'@lamaria.test',
            'password' => Hash::make('Password123!'),
        ]);
        $user->roles()->attach($role);

        return $user;
    }
}
