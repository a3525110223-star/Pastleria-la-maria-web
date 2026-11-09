<?php

namespace Tests\Feature;

use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_empresa_endpoint_returns_public_company_information(): void
    {
        Empresa::query()->create([
            'nombre_comercial' => 'Pastelería La María',
            'mision' => 'Hornear momentos especiales.',
            'vision' => 'Ser la pastelería favorita de la comunidad.',
            'telefono' => '5551234567',
            'numero_whatsapp' => '5551234567',
            'email' => 'contacto@lamaria.com',
            'ubicacion' => 'Av. Principal 123',
            'horario_normal' => 'Lunes a sábado, 8:00 a 20:00',
        ]);

        $this->getJson('/api/empresa')
            ->assertOk()
            ->assertExactJson([
                'nombre' => 'Pastelería La María',
                'mision' => 'Hornear momentos especiales.',
                'vision' => 'Ser la pastelería favorita de la comunidad.',
                'telefono' => '5551234567',
                'direccion' => 'Av. Principal 123',
                'email' => 'contacto@lamaria.com',
                'numero_whatsapp' => '5551234567',
                'horario_normal' => 'Lunes a sábado, 8:00 a 20:00',
                'horario_especial' => null,
                'descripcion' => null,
            ]);
    }

    public function test_empresa_endpoint_returns_not_found_when_company_data_does_not_exist(): void
    {
        $this->getJson('/api/empresa')
            ->assertNotFound()
            ->assertExactJson([
                'status' => 'error',
                'message' => 'No se encontraron datos registrados de la empresa.',
            ]);
    }

    public function test_empresa_endpoint_reflects_updated_company_information(): void
    {
        $empresa = Empresa::query()->create([
            'nombre_comercial' => 'Pastelería La María',
            'numero_whatsapp' => '5551234567',
            'email' => 'contacto@lamaria.com',
            'ubicacion' => 'Av. Principal 123',
            'horario_normal' => 'Lunes a sábado, 8:00 a 20:00',
        ]);

        $empresa->update(['telefono' => '5557654321']);

        $this->getJson('/api/empresa')
            ->assertOk()
            ->assertJsonPath('telefono', '5557654321');
    }
}
