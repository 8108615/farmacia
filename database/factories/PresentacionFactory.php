<?php

namespace Database\Factories;

use App\Models\Presentacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Presentacion>
 */
class PresentacionFactory extends Factory
{
    protected $model = Presentacion::class;

    public function definition(): array
    {
        return [
            'nombre' => mb_strtoupper(fake()->unique()->randomElement([
                'CAJA',
                'FRASCO',
                'AMPOLLA',
                'BOLSITA',
                'BLÍSTER',
                'PAQUETE',
                'TUBO',
                'SPRAY',
                'GOTERO',
                'UNIÓN',
                'SOBRE',
                'PARCHES',
                'SUPOSITORIO',
                'JERINGA',
                'PASTA',
                'CREMA',
                'GEL',
                'SOLUCIÓN',
                'SUSPENSIÓN',
                'POLVO',
                'COLIRIO',
                'CÁPSULA',
                'TABLETA',
                'COMPRIMIDO',
                'INHALADOR',
                'LOCION',
                'EMULSIÓN',
                'NÁDULA',
                'BARRA',
            ])),
        ];
    }
}
