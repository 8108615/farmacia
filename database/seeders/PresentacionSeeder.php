<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presentacion;

class PresentacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presentaciones = [
            'CAJA',
            'BLÍSTER',
            'FRASCO',
            'AMPOLLA',
            'SACHET',
            'TUBO',
            'VIAL',
            'BOLSA',
            'PÍLDORA',
            'INHALADOR',
            'SUPOSITORIO',
            'POLVO PARA SUSPENSIÓN',
            'GOTAS',
            'CREMA',
            'UNGÜENTO',
            'GEL',
            'EMULSIÓN',
            'PARCHES',
            'PASTILLAS',
            'POMADA',
        ];

        foreach ($presentaciones as $nombre) {
            Presentacion::firstOrCreate([
                'nombre' => $nombre,
            ]);
        }
    }
}

