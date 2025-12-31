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
        Schema::create('dato_mecanicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mecanico_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('datos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dato_mecanicos');
    }
};
