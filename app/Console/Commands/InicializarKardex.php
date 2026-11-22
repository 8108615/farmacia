<?php

namespace App\Console\Commands;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Services\RegistroMovimientoInventario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InicializarKardex extends Command
{
    protected $signature = 'inventario:inicializar-kardex';

    protected $description = 'Registra el saldo inicial de las partidas sin historial';

    public function handle(RegistroMovimientoInventario $registro): int
    {
        $creados = DB::transaction(function () use ($registro): int {
            $creados = 0;

            Inventario::query()
                ->with(['producto', 'lote', 'sucursal'])
                ->lockForUpdate()
                ->chunkById(200, function ($partidas) use ($registro, &$creados): void {
                    $partidasConHistorial = MovimientoInventario::query()
                        ->whereIn('inventario_id', $partidas->modelKeys())
                        ->distinct()
                        ->pluck('inventario_id')
                        ->flip();

                    /** @var Inventario $partida */
                    foreach ($partidas as $partida) {
                        if ($partidasConHistorial->has($partida->id)) {
                            continue;
                        }

                        $registro->registrar(
                            inventario: $partida,
                            tipo: 'saldo_inicial',
                            stockAnterior: 0,
                            stockPosterior: $partida->stock_actual,
                            claveOperacion: 'saldo-inicial:' . $partida->id,
                            referencia: 'Saldo al iniciar el Kardex',
                        );

                        $creados++;
                    }
                });

            return $creados;
        });

        $this->info("Saldos iniciales registrados: {$creados}.");

        return self::SUCCESS;
    }
}
