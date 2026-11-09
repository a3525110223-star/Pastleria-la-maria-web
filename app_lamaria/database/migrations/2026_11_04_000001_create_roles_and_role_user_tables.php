<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('nombre', 50)->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('rol_usuario', function (Blueprint $table) {
            $table->id('id_rol_usuario');
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_rol')->constrained('rol', 'id_rol')->cascadeOnDelete();
            $table->unique(['id_usuario', 'id_rol']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_usuario');
        Schema::dropIfExists('rol');
    }
};
