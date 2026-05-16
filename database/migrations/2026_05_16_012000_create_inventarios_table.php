<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->foreignId('ubicacion_fisica_id')->constrained('ubicacion_fisicas')->onDelete('cascade');

            $table->unsignedBigInteger('sucursal_id');
            $table->unsignedBigInteger('producto_id');

            $table->decimal('precio_compra_unidad', 12, 2);
            $table->decimal('precio_venta_unidad', 12, 2);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_maximo')->default(0);
            $table->date('fecha_registro')->nullable();

            $table->enum('estado', ['compra', 'ajuste', 'traslado']);

            $table->index(['sucursal_id', 'producto_id', 'lote_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
