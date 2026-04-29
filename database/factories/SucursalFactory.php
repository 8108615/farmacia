<?php

namespace Database\Factories;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sucursal>
 */
class SucursalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sucursal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->company() . ' ' . fake()->randomElement(['Sucursal', 'Filial', 'Oficina']),
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'estado' => fake()->boolean(80),
        ];
    }
}
