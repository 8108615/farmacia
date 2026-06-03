<?php

namespace Database\Factories;

use App\Models\Sucursal;
use App\Models\UbicacionFisica;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UbicacionFisica>
 */
class UbicacionFisicaFactory extends Factory
{
    protected $model = UbicacionFisica::class;

    public function definition(): array
    {
        $ubicaciones = [
            'Pasillo 1 - Anaquel A',
            'Pasillo 1 - Anaquel B',
            'Pasillo 2 - Anaquel C',
            'Bodega Central',
            'Depósito 2',
            'Estantería Alta',
            'Sección Fría',
            'Almacén de Reservas',
            'Zona de Preparación',
            'Área de Envíos',
            'Cuarto de Empaque',
            'Stock Auxiliar',
        ];

        $descripciones = [
            'Cerca de la entrada principal, junto a recibo de mercadería.',
            'Ubicación para productos de alta rotación.',
            'Zona con control de temperatura y humedad.',
            'Espacio reservado para mercadería en consignación.',
            'Área de almacenamiento temporal antes de surtido.',
            'Estantería para productos frágiles y controlados.',
            'Sector destinado a devoluciones y reprocesos.',
            'Sección junto a la caja registradora para despacho rápido.',
            'Área secundaria para transferencias entre sucursales.',
            'Ubicación principal de inventario interno.',
        ];

        return [
            'sucursal_id' => Sucursal::query()->inRandomOrder()->value('id') ?? Sucursal::factory()->create()->id,
            'nombre' => fake()->unique()->randomElement($ubicaciones),
            'descripcion' => fake()->optional(0.8)->randomElement($descripciones),
        ];
    }
}
