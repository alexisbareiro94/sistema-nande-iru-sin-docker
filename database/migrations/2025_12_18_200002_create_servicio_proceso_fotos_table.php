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
        Schema::create('servicio_proceso_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_proceso_id')->constrained('servicios_proceso')->cascadeOnDelete();
            $table->string('ruta_foto');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['ingreso', 'proceso', 'entrega'])->default('ingreso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio_proceso_fotos');
    }
};
