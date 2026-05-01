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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict');
            $table->foreignId('laboratorio_id')->nullable()->constrained('laboratorios')->onDelete('set null');
            
            $table->string('codigo_producto', 50)->unique();
            $table->string('codigo_barra', 50)->nullable()->unique();
            $table->string('nombre_comercial', 255);
            $table->string('nombre_generico', 255);
            $table->string('forma_farmaceutica', 100)->nullable();
            $table->string('presentacion', 100)->nullable();
            $table->string('concentracion', 100)->nullable();
            $table->string('accion_terapeutica', 255)->nullable();
            $table->string('unidad_medida', 50)->nullable();
            $table->boolean('usa_receta')->default(false);
            $table->string('imagen', 255)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
