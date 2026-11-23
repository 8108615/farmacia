<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            // Conservamos la referencia aunque la partida se elimine después.
            $table->unsignedBigInteger('inventario_id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('lote_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->unsignedBigInteger('usuario_id')->nullable();

            $table->string('codigo_producto', 50);
            $table->string('nombre_producto');
            $table->string('numero_lote', 50);
            $table->string('nombre_sucursal', 150);

            $table->string('tipo', 40);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_posterior');
            $table->string('referencia')->nullable();
            $table->string('clave_operacion', 191)->unique();
            $table->timestamp('ocurrido_en');

            $table->index(['inventario_id', 'id']);
            $table->index(
                ['sucursal_id', 'producto_id', 'lote_id', 'ocurrido_en'],
                'mov_inv_sucursal_producto_lote_fecha_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
