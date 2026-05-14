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
            ['email' => 'contacto@farmacia.com'],
            [
                'nombre' => 'Farmacia Demo',
                'descripcion' => 'Configuracion inicial del sistema',
                'direccion' => 'Av. Principal 123 - Centro',
                'telefono' => '987654321',
                'divisa' => 'BOB',
                'logo' => 'ajustes/ChsdWiwBPXA9DFr2KthhYgtMMf6aWVVCRFP4oOQT.jpg',
                'web' => 'https://farmacia.test',
            ]
        );
    }
}
