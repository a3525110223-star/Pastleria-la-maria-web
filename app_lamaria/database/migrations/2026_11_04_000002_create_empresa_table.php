<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_comercial', 100);
            $table->string('razon_social', 150)->nullable();
            $table->string('rfc', 13)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('numero_whatsapp', 20);
            $table->string('email', 100);
            $table->string('ubicacion', 255);
            $table->string('horario_normal', 255);
            $table->string('horario_especial', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('logotipo', 255)->nullable();
            $table->string('sitio_web', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
