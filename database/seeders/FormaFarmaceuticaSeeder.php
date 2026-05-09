<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormaFarmaceutica;

class FormaFarmaceuticaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formas = [
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
        ];

        foreach ($formas as $nombre) {
            $upper = mb_strtoupper($nombre, 'UTF-8');
            // Buscar coincidencias independientemente de mayúsculas/minúsculas y actualizar
            $existing = FormaFarmaceutica::whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre, 'UTF-8')])->first();
            if ($existing) {
                $existing->update(['nombre' => $upper]);
            } else {
                FormaFarmaceutica::create(['nombre' => $upper]);
            }
        }
    }
}
