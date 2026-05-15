<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition()
    {
        return [
            'ci_nit' => $this->faker->unique()->bothify('##########'),
            'nombres_apellidos' => $this->faker->name(),
            'email' => $this->faker->optional()->safeEmail(),
            'telefono' => $this->faker->optional()->phoneNumber('7#######'),
        ];
    }
}
