<?php

namespace Database\Seeders;

use App\Models\Ajuste;
use Illuminate\Database\Seeder;

class AjusteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ajuste::query()->firstOrCreate(
            ['email' => 'fernando@farmacia.com'],
            [
                'nombre' => 'Farmacia EFMG',
                'descripcion' => 'Sistema de Farmacia',
                'direccion' => 'Villa primero de MAyo calle 7',
                'telefono' => '76658531',
                'divisa' => 'BOB',
                'logo' => 'ajustes/lFrWGU0jNkLqKWWtrhEFyw7C5ODmplTEuNgmkXBW.jpg',
                'web' => 'https://farmacia.test',
            ]
        );
    }
}
