<?php

namespace Database\Factories;

use App\Models\FormaFarmaceutica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormaFarmaceutica>
 */
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
                'Pomada',
                'Crema',
                'Solución',
                'Suspensión',
                'Inhalador',
                'Gotas',
                'Spray',
                'Ungüento',
                'Polvo',
                'Tableta masticable',
                'Tableta sublingual',
                'Comprimido',
                'Inyección',
                'Parches',
                'Supositorio',
                'Gel',
                'Emulsión',
                'Espuma',
                'Nebulizador',
                'Pastilla',
                'Barrilla',
                'Granulado',
                'Nasal',
                'Óvulos',
                'Loción',
                'Aerosol',
            ])),
        ];
    }
}
