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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->string('patente', 10)->index();
            $table->string('marca', 50);
            $table->string('modelo', 100);
            $table->year('anio')->nullable();
            $table->string('color', 30)->nullable();
            $table->foreignId('cliente_id')->nullable()->constrained('users');
            $table->foreignId('mecanico_id')->nullable()->constrained('users');
            $table->integer('kilometraje')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['patente', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
