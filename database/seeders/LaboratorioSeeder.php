<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\laboratorio;

class LaboratorioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actualizar laboratorios existentes a mayúsculas
        laboratorio::query()->get()->each(function ($lab) {
            $upper = mb_strtoupper($lab->nombre, 'UTF-8');
            if ($lab->nombre !== $upper) {
                $lab->update(['nombre' => $upper]);
            }
        });

        // Crear 20 laboratorios nuevos (nombres en mayúsculas gracias a la factory)
        laboratorio::factory()->count(20)->create();
    }
}
