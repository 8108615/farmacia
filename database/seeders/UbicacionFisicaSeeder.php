<?php

namespace Database\Seeders;

use App\Models\UbicacionFisica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UbicacionFisicaSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        UbicacionFisica::factory()->count(10)->create();
    }
}
