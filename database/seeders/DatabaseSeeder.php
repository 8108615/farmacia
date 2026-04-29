<?php

namespace Database\Seeders;

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
         $this->call([
            RoleSeeder::class,
            AjusteSeeder::class,
            SucursalSeeder::class,
            EmpleadoSeeder::class,
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

        Sucursal::query()->firstOrCreate(
            ['nombre' => 'LA VILLA'],
            [
                'direccion' => 'Vila primero de Mayo',
                'telefono' => '76658532',
                'estado' => true,
            ]
        );
    }
}
