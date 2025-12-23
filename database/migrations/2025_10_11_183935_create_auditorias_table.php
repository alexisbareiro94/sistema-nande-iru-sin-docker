<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Información de la entidad afectada
            $table->morphs('entidad');

            // Información legible
            $table->string('modulo')->nullable()->index(); // ventas, inventario, clientes, etc.
            $table->string('accion'); // crear, actualizar, eliminar, login, logout, etc.
            $table->text('descripcion')->nullable(); // "Creó factura #001-001 0000089"

            // Datos del cambio
            $table->json('datos_anteriores')->nullable(); // Estado antes del cambio
            $table->json('datos_nuevos')->nullable(); // Estado después del cambio

            // Información del contexto
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index('accion');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
