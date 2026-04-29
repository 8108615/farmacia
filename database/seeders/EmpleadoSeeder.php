<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class EmpleadoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empleadoRole = Role::query()->firstOrCreate([
            'name' => 'EMPLEADO',
            'guard_name' => 'web',
        ]);

        $sucursalIds = Sucursal::query()->pluck('id')->all();

        if (empty($sucursalIds)) {
            Sucursal::factory()->count(5)->create();
            $sucursalIds = Sucursal::query()->pluck('id')->all();
        }

        Empleado::factory()
            ->count(10)
            ->create()
            ->each(function (Empleado $empleado) use ($empleadoRole) {
                if ($empleado->usuario) {
                    $empleado->usuario->syncRoles([$empleadoRole->name]);
                }
            });
    }
}
