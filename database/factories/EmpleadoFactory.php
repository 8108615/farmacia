<?php

namespace Database\Factories;

use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Empleado>
 */
class EmpleadoFactory extends Factory
{
    protected $model = Empleado::class;

    public function definition(): array
    {
        $nombre = fake()->firstName();
        $apellidos = fake()->lastName();

        return [
            'usuario_id' => User::factory(),
            'sucursal_id' => function () {
                return Sucursal::query()->inRandomOrder()->value('id') ?? Sucursal::factory()->create()->id;
            },
            'nombres' => $nombre,
            'apellidos' => $apellidos,
            'tipo_doc' => fake()->randomElement(['CI', 'DNI', 'RUC', 'PASAPORTE', 'OTRO']),
            'numero_doc' => fake()->unique()->numerify('##########'),
            'telefono' => fake()->phoneNumber(),
            'direccion' => fake()->address(),
            'profesion' => fake()->jobTitle(),
            'fecha_nacimiento' => fake()->date('Y-m-d', '-18 years'),
            'genero' => fake()->randomElement(['M', 'F']),
            'avatar' => null,
            'estado' => fake()->boolean(80) ? 'activo' : 'inactivo',
        ];
    }
}
