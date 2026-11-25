<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CajaDeVenta
{
    // Debe ejecutarse dentro de la transacción de venta o cancelación.
    public function turnoAbierto(int $sucursal, int $usuario): object
    {
        $turnos = DB::table('arqueos')->where('sucursal_id', $sucursal)
            ->where('usuario_id', $usuario)->where('estado', 'abierto')
            ->orderBy('id')->limit(2)->lockForUpdate()->get();

        if ($turnos->count() !== 1) {
            $mensaje = $turnos->isEmpty()
                ? 'Abre una caja en la sucursal seleccionada antes de continuar.'
                : 'Hay más de una caja abierta a tu nombre en esta sucursal. Deja solo una abierta antes de continuar.';
            throw ValidationException::withMessages(['caja' => $mensaje]);
        }

        return $turnos->first();
    }

    public function registrarCobro(object $turno, int $venta, int $centavos): void
    {
        $this->registrar($turno, $venta, $centavos, 'ingreso', 'Cobro de venta en efectivo');
    }

    public function registrarDevolucion(int $venta, int $sucursal, int $usuario): void
    {
        $cobro = DB::table('arqueo_detalles')->where('referencia', 'Venta #' . $venta)
            ->where('tipo', 'ingreso')->where('concepto', 'Cobro de venta en efectivo')->first();

        // Las ventas anteriores a la integración carecen de un cobro registrado en caja.
        if (!$cobro) {
            return;
        }

        $turno = $this->turnoAbierto($sucursal, $usuario);
        $this->registrar($turno, $venta, $this->centavos($cobro->monto), 'egreso', 'Devolución por cancelación de venta');
    }

    private function registrar(object $turno, int $venta, int $importe, string $tipo, string $concepto): void
    {
        $ingresos = $this->centavos($turno->total_ingresos);
        $egresos = $this->centavos($turno->total_egresos);

        if ($tipo === 'ingreso') {
            $ingresos += $importe;
        } else {
            $egresos += $importe;
        }

        $saldo = $this->centavos($turno->monto_inicial) + $ingresos - $egresos;
        if ($saldo < 0) {
            throw ValidationException::withMessages(['caja' => 'La caja abierta no tiene efectivo suficiente para devolver el importe.']);
        }
        if (max($ingresos, $egresos, $saldo) > 999999999999) {
            throw ValidationException::withMessages(['caja' => 'El movimiento supera el límite de importe de la caja.']);
        }

        $ahora = now();
        DB::table('arqueo_detalles')->insert([
            'arqueo_id' => $turno->id, 'tipo' => $tipo, 'concepto' => $concepto,
            'monto' => number_format($importe / 100, 2, '.', ''), 'referencia' => 'Venta #' . $venta,
            'created_at' => $ahora, 'updated_at' => $ahora,
        ]);
        DB::table('arqueos')->where('id', $turno->id)->update([
            'total_ingresos' => number_format($ingresos / 100, 2, '.', ''),
            'total_egresos' => number_format($egresos / 100, 2, '.', ''),
            'updated_at' => $ahora,
        ]);
    }

    private function centavos(string|int|float $monto): int
    {
        return (int) round((float) $monto * 100);
    }
}
