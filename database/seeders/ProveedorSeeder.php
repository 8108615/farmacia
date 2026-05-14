<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    use WithoutModelEvents;
    public function run(): void
    {
        Proveedor::factory()->count(10)->create();
    }
}
