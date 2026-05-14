<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\FormaFarmaceutica;
use App\Models\Laboratorio;
use App\Models\Presentacion;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'categoria_id' => $this->getRandomId(Categoria::class),
            'laboratorio_id' => $this->faker->boolean(85) ? $this->getRandomId(Laboratorio::class) : null,
            'forma_farmaceutica_id' => $this->faker->boolean(85) ? $this->getRandomId(FormaFarmaceutica::class) : null,
            'presentacion_id' => $this->faker->boolean(85) ? $this->getRandomId(Presentacion::class) : null,
            'codigo_producto' => strtoupper($this->faker->unique()->bothify('PRD-#####')),
            'codigo_barra' => $this->faker->boolean(80) ? $this->faker->unique()->numerify('#############') : null,
            'nombre_comercial' => strtoupper($this->faker->words($this->faker->numberBetween(1, 3), true)),
            'nombre_generico' => strtoupper($this->faker->words($this->faker->numberBetween(1, 3), true)),
            'concentracion' => $this->faker->optional(0.8)->randomElement([
                '100',
                '250',
                '500',
                '5',
                '10',
                '1',
                '20',
                '200',
                '1',
                '0.9',
            ]),
            'accion_terapeutica' => $this->faker->optional(0.75)->randomElement([
                'ANALGESICO',
                'ANTIINFLAMATORIO',
                'ANTIBIOTICO',
                'ANTIGRIPAL',
                'ANTIALERGICO',
                'ANTIPIRETICO',
                'DESCONGESTIONANTE',
                'ANTISEPTICO',
                'VITAMINICO',
                'REHIDRATANTE',
            ]),
            'unidad_medida' => $this->faker->optional(0.85)->randomElement([
                'kg',
                'g',
                'mg',
                'mcg',
                'l',
                'ml',
                'mmol',
                'mEq',
                'UI',
                '%',
            ]),
            'usa_receta' => $this->faker->boolean(45),
            'imagen' => null,
        ];
    }

    private function getRandomId(string $modelClass): int
    {
        static $cachedIds = [];

        if (!isset($cachedIds[$modelClass])) {
            $cachedIds[$modelClass] = $modelClass::query()->pluck('id')->all();
        }

        return $this->faker->randomElement($cachedIds[$modelClass]);
    }
}
