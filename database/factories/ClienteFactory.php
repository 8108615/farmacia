<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'ci_nit' => fake()->unique()->numerify('#########'),
            'nombres_apellidos' => fake()->name(),
            'email' => fake()->boolean(70) ? fake()->unique()->safeEmail() : null,
            'telefono' => fake()->optional()->phoneNumber(),
        ];
    }
}