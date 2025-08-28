<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();

            // Estados del flujo
           $table->enum('estado', [
                'pendiente',     // creado por Operaciones/Supervisor
                'en_proceso',    // asignado a Coordinador TI (u otro)
                'validacion',    // validación por otro responsable
                'finalizado',    // ← antes decía aprobado
                'rechazado',
                'cerrado'
            ])->default('pendiente')->index();


            // Niveles de prioridad
            $table->enum('prioridad', ['urgente','alta','media','baja'])->default('media')->index();

            // Asignaciones y seguimiento
            $table->foreignId('creado_por')->constrained('users');
            $table->foreignId('asignado_a')->nullable()->constrained('users');
            $table->foreignId('aprobado_por')->nullable()->constrained('users');

            $table->timestamp('vence_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
