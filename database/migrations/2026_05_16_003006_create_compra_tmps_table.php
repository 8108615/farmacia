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
        Schema::create('compra_tmps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursals')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->decimal('precio_compra_unidad', 12, 2);
            $table->decimal('precio_venta_unidad', 12, 2);
            $table->decimal('porcentaje_ganancia_unidad', 5, 2);
            $table->integer('cantidad');
            $table->dateTime('fecha_creacion');
            $table->enum('estado', ['activo', 'inactivo','pendiente','confirmado','cancelado'])->default('activo');

            $table->index(['usuario_id', 'sucursal_id', 'producto_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compra_tmps');
    }
};
