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
        Schema::create('arqueo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arqueo_id')->constrained('arqueos')->onDelete('cascade');
            $table->enum('tipo', ['ingreso', 'egreso'])->default('ingreso');
            $table->string('concepto');
            $table->decimal('monto', 12, 2)->default(0);
            $table->string('referencia')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arqueo_detalles');
    }
};
