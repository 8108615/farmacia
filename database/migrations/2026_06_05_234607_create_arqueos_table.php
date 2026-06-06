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
        Schema::create('arqueos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursals')->onDelete('cascade');

            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_inicial', 12, 2)->default(0);
            $table->decimal('total_ingresos', 12, 2)->default(0);
            $table->decimal('total_egresos', 12, 2)->default(0);
            $table->decimal('monto_contado', 12, 2)->default(0);
            $table->decimal('diferencia', 12, 2)->default(0);
            $table->enum('estado', ['abierto', 'cerrado'])->default('abierto');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arqueos');
    }
};
