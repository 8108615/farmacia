<?php

namespace Database\Factories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{

    protected $model = Proveedor::class;


    public function definition()
    {

        return [
            'nombre' => fake()->name(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'direccion' => fake()->address(),
            'empresa' => fake()->company(),
            'notas' => fake()->optional()->sentence(),
        ];
    }
}
