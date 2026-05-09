<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Laboratorio;

class LaboratorioFactory extends Factory
{

    protected $model = Laboratorio::class;

    public function definition()
    {
        return [
            'nombre' => mb_strtoupper(fake()->unique()->company(), 'UTF-8'),
        ];
    }
}
