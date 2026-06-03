<?php

namespace Database\Seeders;

use App\Models\FormaFarmaceutica;
use App\Models\Laboratorio;
use App\Models\Presentacion;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Sucursal::query()->firstOrCreate(
            ['nombre' => 'CASA MATRIZ'],
            [
                'direccion' => 'Av. Cumavi Calle 5, Nro 225',
                'telefono' => '76658531',
                'estado' => true,
            ]
        );

        $this->call([
            RoleSeeder::class,
            AjusteSeeder::class,
            SucursalSeeder::class,
            UbicacionFisicaSeeder::class,
            CategoriaSeeder::class,
            EmpleadoSeeder::class,
            ProveedorSeeder::class,
            ClienteSeeder::class,
        ]);

        Laboratorio::factory()->count(20)->create();
        FormaFarmaceutica::factory()->count(20)->create();
        Presentacion::factory()->count(20)->create();
        $this->call([
            ProductoSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'erick@gmail.com'],
            ['name' => 'Erick Fernando Morales Gil', 'password' => bcrypt('12345678')]
        );

        $superAdminRole = Role::query()->firstOrCreate([
            'name' => 'SUPER ADMIN',
            'guard_name' => 'web',
        ]);

        $admin->syncRoles([$superAdminRole->name]);
    }
}
