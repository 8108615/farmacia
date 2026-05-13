<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\FormaFarmaceutica;
use App\Models\Presentacion;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $categoriaId = Categoria::query()->inRandomOrder()->value('id') ?? Categoria::factory()->create()->id;
        $laboratorioId = Laboratorio::query()->inRandomOrder()->value('id') ?? Laboratorio::factory()->create()->id;
        $formaId = FormaFarmaceutica::query()->inRandomOrder()->value('id') ?? FormaFarmaceutica::factory()->create()->id;
        $presentacionId = Presentacion::query()->inRandomOrder()->value('id') ?? Presentacion::factory()->create()->id;

        $nombreComercial = mb_strtoupper($this->faker->unique()->words(2, true), 'UTF-8');
        $nombreGenerico = mb_strtoupper($this->faker->words(2, true), 'UTF-8');

        return [
            'categoria_id' => $categoriaId,
            'laboratorio_id' => $laboratorioId,
            'forma_farmaceutica_id' => $formaId,
            'presentacion_id' => $presentacionId,
            'codigo_producto' => strtoupper($this->faker->unique()->bothify('PRO-###')),
            'codigo_barra' => $this->faker->unique()->numerify(str_repeat('#', 9)),
            'nombre_comercial' => $nombreComercial,
            'nombre_generico' => $nombreGenerico,
            'concentracion' => $this->faker->optional(0.6)->randomElement(['50 MG','100 MG','200 MG','5 ML','10 ML','250 MG']) ,
            'accion_terapeutica' => $this->faker->optional(0.6)->sentence(3),
            'unidad_medida' => $this->faker->optional(0.7)->randomElement(['mg','ml','g','UI','mcg']),
            'usa_receta' => $this->faker->boolean(30),
            'imagen' => null,
        ];
    }
}
