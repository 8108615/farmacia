<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\MovimientoInventario;

class RegistroMovimientoInventario
{
    public function registrar(
        Inventario $inventario,
        string $tipo,
        int $stockAnterior,
        int $stockPosterior,
        string $claveOperacion,
        ?int $usuarioId = null,
        ?string $referencia = null,
    ): MovimientoInventario {
        $inventario->loadMissing(['producto', 'lote', 'sucursal']);

        return MovimientoInventario::create([
            'inventario_id' => $inventario->id,
            'producto_id' => $inventario->producto_id,
            'lote_id' => $inventario->lote_id,
            'sucursal_id' => $inventario->sucursal_id,
            'usuario_id' => $usuarioId,
            'codigo_producto' => $inventario->producto?->codigo_producto ?? '—',
            'nombre_producto' => $inventario->producto?->nombre_comercial ?? 'Producto sin identificar',
            'numero_lote' => $inventario->lote?->numero_lote ?? '—',
            'nombre_sucursal' => $inventario->sucursal?->nombre ?? 'Sucursal sin identificar',
            'tipo' => $tipo,
            'cantidad' => $stockPosterior - $stockAnterior,
            'stock_anterior' => $stockAnterior,
            'stock_posterior' => $stockPosterior,
            'referencia' => $referencia,
            'clave_operacion' => $claveOperacion,
            'ocurrido_en' => now(),
        ]);
    }
}
