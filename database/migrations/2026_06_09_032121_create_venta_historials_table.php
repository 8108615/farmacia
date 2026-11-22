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
        Schema::create('venta_historials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_documento', 50)->nullable();
            $table->string('cliente_email', 150)->nullable();
            $table->string('cliente_celular', 150)->nullable();
            $table->string('sucursal_nombre');
            $table->string('usuario_nombre');
            $table->date('fecha_venta');
            $table->decimal('total_venta',12, 2);
            $table->string('estado', 20);
            $table->string('tipo_pago'. 20);
            $table->string('notas')->nullable();
            $table->json('detalles');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_historials');
    }
};
