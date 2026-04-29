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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('sucursal_id')->constrained('sucursals')->onDelete('restrict');

            $table->string('nombres', 255);
            $table->string('apellidos', 255);
            $table->enum('tipo_doc', ['CI', 'DNI', 'RUC', 'PASAPORTE', 'OTRO']);
            $table->string('numero_doc', 50)->unique();
            $table->string('telefono', 50);
            $table->text('direccion');
            $table->string('profesion', 150);
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['M', 'F']);
            $table->string('avatar', 255)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
