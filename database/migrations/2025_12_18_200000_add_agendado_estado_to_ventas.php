<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el enum de estado para incluir 'agendado'
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado ENUM('pendiente', 'completado', 'cancelado', 'agendado') DEFAULT 'completado'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'completado'");
    }
};
