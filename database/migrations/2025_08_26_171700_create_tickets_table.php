<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Datos del formulario
            $table->string('titulo');                 // Asunto
            $table->string('puesto');                 // Puesto
            $table->string('cargo');                  // Cargo
            $table->string('nombre_guarda');          // Nombre del guarda
            $table->string('cedula_guarda', 30);      // Cédula

            $table->text('descripcion')->nullable();

            // Estados del flujo
            $table->enum('estado', [
                'pendiente',
                'en_proceso',
                'finalizado',
            ])->default('pendiente')->index();

            // Niveles de prioridad
            $table->enum('prioridad', ['urgente','alta','media','baja'])->default('media')->index();

            // Relaciones con usuarios
            $table->foreignId('creado_por')->constrained('users');   // quién crea
            $table->foreignId('asignado_a')->nullable()->constrained('users'); // responsable
            $table->foreignId('aprobado_por')->nullable()->constrained('users'); // gerencia u otro

            $table->timestamp('vence_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};