<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FormaFarmaceutica;

class FormaFarmaceuticaFactory extends Factory
{
    protected $model = FormaFarmaceutica::class;

    public function definition(): array
    {
        return [
            'nombre' => mb_strtoupper(fake()->unique()->randomElement([
            'Tableta',
            'Cápsula',
            'Jarabe',
            'Suspensión',
            'Crema',
            'Ungüento',
            'Solución',
            'Gotas',
            'Inyectable',
            'Supositorio',
            'Polvo',
            'Granulado',
            'Spray',
            'Gel',
            'Emulsión',
            'Parches',
            'Pastillas',
            'Pomada',
            'Loción',
            'Óvulos',
            ]), 'UTF-8'),
        ];
    }
}
