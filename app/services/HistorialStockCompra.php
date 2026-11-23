<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Inventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class HistorialStockCompra
{
    public function __construct(private RegistroMovimientoInventario $registro)
    {
    }

    public function ejecutar(int $compraId, string $tipo, ?int $usuarioId, callable $operacion): mixed
    {
        return DB::transaction(function () use ($compraId, $tipo, $usuarioId, $operacion) {
            $compra = Compra::query()->lockForUpdate()->findOrFail($compraId);
            $antes = $this->partidas($compraId);

            $resultado = $operacion($compra);

            $despues = $this->partidas($compraId);
            $operacionId = (string) Str::uuid();
            $referencia = 'Compra #' . $compraId;

            foreach ($antes->keys()->merge($despues->keys())->unique() as $id) {
                $previa = $antes->get($id);
                $actual = $despues->get($id);
                $stockAnterior = $previa?->stock_actual ?? 0;
                $stockPosterior = $actual?->stock_actual ?? 0;

                if ($previa && $actual && (
                    $previa->producto_id !== $actual->producto_id
                    || $previa->lote_id !== $actual->lote_id
                    || $previa->sucursal_id !== $actual->sucursal_id
                )) {
                    throw new LogicException('Una partida existente no puede cambiar de producto, lote o sucursal.');
                }

                if ($stockAnterior !== $stockPosterior) {
                    $this->registro->registrar(
                        inventario: $actual ?? $previa,
                        tipo: $tipo,
                        stockAnterior: $stockAnterior,
                        stockPosterior: $stockPosterior,
                        claveOperacion: "compra:{$operacionId}:{$id}",
                        usuarioId: $usuarioId,
                        referencia: $referencia,
                    );
                }
            }

            return $resultado;
        });
    }

    private function partidas(int $compraId): Collection
    {
        return Inventario::query()
            ->where('compra_id', $compraId)
            ->with(['producto', 'lote', 'sucursal'])
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }
}

