<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LoteSeeder extends Seeder
{
    use WithoutModelEvents;
    public function run(): void
    {
        Lote::factory()->count(10)->create();
    }
}
