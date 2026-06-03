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
        $medicamentos = [
            ['comercial' => 'PARACET PLUS', 'generico' => 'PARACETAMOL', 'accion' => 'ANALGESICO'],
            ['comercial' => 'IBUREL', 'generico' => 'IBUPROFENO', 'accion' => 'ANTIINFLAMATORIO'],
            ['comercial' => 'AMOXILIN', 'generico' => 'AMOXICILINA', 'accion' => 'ANTIBIOTICO'],
            ['comercial' => 'CLARITRON', 'generico' => 'CLARITROMICINA', 'accion' => 'ANTIBIOTICO'],
            ['comercial' => 'LORATEX', 'generico' => 'LORATADINA', 'accion' => 'ANTIALERGICO'],
            ['comercial' => 'OMEPRAL', 'generico' => 'OMEPRAZOL', 'accion' => 'ANTACIDO'],
            ['comercial' => 'METFORMEX', 'generico' => 'METFORMINA', 'accion' => 'ANTIDIABETICO'],
            ['comercial' => 'ATORVAST', 'generico' => 'ATORVASTATINA', 'accion' => 'HIPOLIPEMICO'],
            ['comercial' => 'SALBUVENT', 'generico' => 'SALBUTAMOL', 'accion' => 'BRONCODILATADOR'],
            ['comercial' => 'AZITROMAX', 'generico' => 'AZITROMICINA', 'accion' => 'ANTIBIOTICO'],
            ['comercial' => 'DICLOFEN', 'generico' => 'DICLOFENACO', 'accion' => 'ANTIINFLAMATORIO'],
            ['comercial' => 'PREDNISOL', 'generico' => 'PREDNISONA', 'accion' => 'CORTICOIDE'],
            ['comercial' => 'RANITID', 'generico' => 'RANITIDINA', 'accion' => 'ANTACIDO'],
            ['comercial' => 'LIDOCARE', 'generico' => 'LIDOCAINA', 'accion' => 'ANESTESICO'],
            ['comercial' => 'CLORINOL', 'generico' => 'CLORHEXIDINA', 'accion' => 'ANTISEPTICO'],
            ['comercial' => 'VITAMAX D', 'generico' => 'VITAMINA D', 'accion' => 'VITAMINICO'],
            ['comercial' => 'CALCIOR', 'generico' => 'CARBONATO DE CALCIO', 'accion' => 'SUPLEMENTO'],
            ['comercial' => 'PROBIOLAC', 'generico' => 'LACTOBACILLUS', 'accion' => 'PROBIOTICO'],
            ['comercial' => 'ANEXAL', 'generico' => 'CICLOFERON', 'accion' => 'INMUNOMODULADOR'],
            ['comercial' => 'RENOXIN', 'generico' => 'LEVOTIROXINA', 'accion' => 'HORMONAL'],
        ];

        $medicamento = $this->faker->randomElement($medicamentos);

        $concentraciones = ['500', '250', '100', '50', '20', '10', '5', '2', '1', '0.5'];
        $unidades = ['mg', 'ml', 'g', 'mcg', 'UI'];

        return [
            'categoria_id' => $this->getRandomId(Categoria::class),
            'laboratorio_id' => $this->faker->boolean(85) ? $this->getRandomId(Laboratorio::class) : null,
            'forma_farmaceutica_id' => $this->faker->boolean(85) ? $this->getRandomId(FormaFarmaceutica::class) : null,
            'presentacion_id' => $this->faker->boolean(85) ? $this->getRandomId(Presentacion::class) : null,
            'codigo_producto' => strtoupper($this->faker->unique()->bothify('PRD-#####')),
            'codigo_barra' => $this->faker->boolean(80) ? $this->faker->unique()->numerify('#############') : null,
            'nombre_comercial' => $medicamento['comercial'],
            'nombre_generico' => $medicamento['generico'],
            'concentracion' => $this->faker->optional(0.9)->randomElement($concentraciones),
            'accion_terapeutica' => $this->faker->optional(0.9)->randomElement([$medicamento['accion'], 'ANTIBIOTICO', 'ANTIINFLAMATORIO', 'ANALGESICO', 'ANTIALERGICO', 'ANTIGRIPAL', 'ANTIPIRETICO', 'ANTISEPTICO', 'VITAMINICO', 'REHIDRATANTE', 'BRONCODILATADOR', 'ANTIDIABETICO', 'HIPOLIPEMICO', 'CORTICOIDE', 'SUPLEMENTO', 'INMUNOMODULADOR', 'HORMONAL']),
            'unidad_medida' => $this->faker->optional(0.95)->randomElement($unidades),
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
