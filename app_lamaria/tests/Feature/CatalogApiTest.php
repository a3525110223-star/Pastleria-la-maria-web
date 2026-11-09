<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('categorias', function (Blueprint $table): void {
            $table->id('id_categoria');
            $table->string('nombre', 50);
            $table->string('descripcion', 2000)->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table): void {
            $table->id('id_producto');
            $table->unsignedBigInteger('id_categoria');
            $table->string('nombre', 100);
            $table->text('descripcion');
            $table->decimal('precio', 10, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categorias');
        });
    }

    public function test_categories_endpoint_returns_only_active_categories(): void
    {
        DB::table('categorias')->insert([
            ['nombre' => 'Pasteles', 'activa' => true],
            ['nombre' => 'Temporal', 'activa' => false],
        ]);

        $this->getJson('/api/categorias')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.nombre', 'Pasteles');
    }

    public function test_products_endpoint_returns_active_products_in_active_categories(): void
    {
        DB::table('categorias')->insert([
            ['id_categoria' => 1, 'nombre' => 'Repostería', 'activa' => true],
            ['id_categoria' => 2, 'nombre' => 'Inactiva', 'activa' => false],
        ]);

        DB::table('productos')->insert([
            [
                'id_producto' => 1,
                'id_categoria' => 1,
                'nombre' => 'Pastel de prueba',
                'descripcion' => 'Descripción de prueba',
                'precio' => 350,
                'image_url' => 'productos/pastel.jpg',
                'activa' => true,
            ],
            [
                'id_producto' => 2,
                'id_categoria' => 1,
                'nombre' => 'Producto inactivo',
                'descripcion' => 'No debe aparecer',
                'precio' => 100,
                'image_url' => null,
                'activa' => false,
            ],
            [
                'id_producto' => 3,
                'id_categoria' => 2,
                'nombre' => 'Producto de categoría inactiva',
                'descripcion' => 'No debe aparecer',
                'precio' => 100,
                'image_url' => null,
                'activa' => true,
            ],
        ]);

        $this->getJson('/api/productos')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.id_producto', 1)
            ->assertJsonPath('data.0.categoria', 'Repostería')
            ->assertJsonPath('data.0.imagen', 'http://localhost/storage/productos/pastel.jpg');
    }

    public function test_api_allows_a_configured_live_server_origin(): void
    {
        $this->withHeader('Origin', 'http://localhost:5500')
            ->getJson('/api/categorias')
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5500');
    }

    public function test_json_http_errors_are_returned_in_spanish(): void
    {
        $this->getJson('/api/no-existe')
            ->assertNotFound()
            ->assertJsonPath('message', 'No encontramos lo que buscas. Revisa la dirección e inténtalo de nuevo.');
    }

    public function test_json_authentication_errors_are_returned_in_spanish(): void
    {
        Route::middleware('auth')->get('/api/requiere-sesion', fn () => response()->json(['success' => true]));

        $this->getJson('/api/requiere-sesion')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Tu sesión no está activa. Inicia sesión para continuar.');
    }

    public function test_server_errors_show_the_spanish_page_without_debug_details(): void
    {
        config(['app.debug' => false]);

        Route::get('/error-de-prueba', function () {
            throw new \RuntimeException('English debug detail');
        });

        $this->get('/error-de-prueba')
            ->assertInternalServerError()
            ->assertSee('Algo salió mal')
            ->assertSee('Ocurrió un problema en el servidor.')
            ->assertDontSee('English debug detail');
    }
}
