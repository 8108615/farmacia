<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement([
                'Medicamentos',
                'Cuidado Personal',
                'Higiene',
                'Vitaminas',
                'Bebés',
                'Salud Sexual',
                'Hogar',
                'Belleza',
                'Alimentos',
                'Bebidas',
                'Suplementos',
                'Dermocosmética',
                'Aparatos Médicos',
                'Nutrición',
                'Veterinaria',
            ]),
        ];
    }
}
