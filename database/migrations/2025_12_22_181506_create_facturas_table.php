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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->integer('timbrado');
            $table->integer('sucursal');
            $table->integer('punto_emision');
            $table->integer('numero');
            $table->dateTime('emision');
            $table->enum('estado', ['emitida', 'anulada'])->default('emitida');
            $table->enum('tipo', ['factura', 'nota_debito', 'nota_credito'])->default('factura');
            $table->enum('condicion_venta', ['contado', 'credito'])->default('contado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
