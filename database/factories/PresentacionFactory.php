<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Presentacion;

class PresentacionFactory extends Factory
{
    protected $model = Presentacion::class;

    public function definition(): array
    {
        return [
            'nombre' => mb_strtoupper(fake()->unique()->randomElement([
                'Caja',
                'Blíster',
                'Frasco',
                'Ampolla',
                'Sachet',
                'Tubo',
                'Vial',
                'Bolsa',
                'Píldora',
                'Inhalador',
                'Supositorio',
                'Polvo para suspensión',
                'Gotas',
                'Crema',
                'Ungüento',
                'Gel',
                'Emulsión',
                'Parches',
                'Pastillas',
                'Pomada',
                'Loción',
            ]), 'UTF-8'),
        ];
    }
}
