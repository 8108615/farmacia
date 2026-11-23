<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Services\RegistroMovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    public function create()
    {
        $clientes = Cliente::query()->orderBy('nombres_apellidos')->get();
        $sucursales = Sucursal::query()->orderBy('nombre')->get();
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';
        $partidas = Inventario::query()->with(['producto', 'lote'])
            ->where('stock_actual', '>', 0)
            ->whereHas('lote', fn ($query) => $query->whereDate('fecha_vencimiento', '>=', today()))
            ->orderBy('id')->get();
        $operacion = (string) Str::uuid();

        return view('admin.ventas.create', compact('clientes', 'sucursales', 'partidas', 'divisa', 'operacion'));
    }

    public function store(Request $request, RegistroMovimientoInventario $registro)
    {
        $datos = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'sucursal_id' => ['required', 'integer', 'exists:sucursals,id'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['required', 'array:inventario_id,cantidad'],
            'items.*.inventario_id' => ['required', 'integer', 'distinct'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'tipo_pago' => ['required', 'in:efectivo,tarjeta,transferencia'],
            'operacion' => ['required', 'uuid'],
        ]);

        $usuario = $request->user();
        $clave = 'venta:' . $usuario->id . ':' . $datos['operacion'];
        $items = collect($datos['items'])->keyBy('inventario_id')->sortKeys();

        DB::transaction(function () use ($datos, $usuario, $clave, $items, $registro) {
            // Serializa los reenvíos de la misma venta, incluso si cambia el carrito.
            $usuario->newQuery()->lockForUpdate()->findOrFail($usuario->id);

            if (MovimientoInventario::query()->where('clave_operacion', $clave)->exists()) {
                return;
            }

            $partidas = Inventario::query()->whereKey($items->keys()->all())
                ->where('sucursal_id', $datos['sucursal_id'])
                ->orderBy('id')->lockForUpdate()->get();

            if ($partidas->count() !== $items->count()) {
                throw ValidationException::withMessages(['items' => 'Alguna partida ya no está disponible en la sucursal elegida.']);
            }

            $totalCentavos = 0;
            $hoy = today();
            foreach ($partidas as $partida) {
                $lote = $partida->lote()->lockForUpdate()->first();
                if (!$lote?->fecha_vencimiento || $lote->fecha_vencimiento->lt($hoy)) {
                    throw ValidationException::withMessages(['items' => "La partida #{$partida->id} requiere un vencimiento vigente."]);
                }

                $cantidad = (int) $items[$partida->id]['cantidad'];
                if ($cantidad > $partida->stock_actual) {
                    throw ValidationException::withMessages(['items' => "La partida #{$partida->id} tiene {$partida->stock_actual} unidades disponibles."]);
                }

                $centavos = (int) str_replace('.', '', $partida->precio_venta_unidad);
                if ($centavos < 0 || $centavos > intdiv(999999999999 - $totalCentavos, $cantidad)) {
                    throw ValidationException::withMessages(['items' => 'El precio o el total de la venta está fuera del rango permitido.']);
                }

                $totalCentavos += $centavos * $cantidad;
            }

            $venta = new Venta();
            $venta->sucursal_id = $datos['sucursal_id'];
            $venta->cliente_id = $datos['cliente_id'];
            $venta->usuario_id = $usuario->id;
            $venta->fecha_venta = $hoy;
            $venta->total_venta = sprintf('%d.%02d', intdiv($totalCentavos, 100), $totalCentavos % 100);
            $venta->estado = 'emitida';
            $venta->tipo_pago = $datos['tipo_pago'];
            $venta->notas = '';
            $venta->save();

            foreach ($partidas as $indice => $partida) {
                $cantidad = (int) $items[$partida->id]['cantidad'];
                $detalle = new VentaDetalle();
                $detalle->venta_id = $venta->id;
                $detalle->inventario_id = $partida->id;
                $detalle->cantidad = $cantidad;
                $detalle->precio_venta_unidad = $partida->precio_venta_unidad;
                $detalle->save();

                $anterior = $partida->stock_actual;
                $partida->stock_actual -= $cantidad;
                $partida->save();

                $registro->registrar(
                    $partida, 'salida_venta', $anterior, $partida->stock_actual,
                    $indice === 0 ? $clave : $clave . ':' . $partida->id,
                    $usuario->id, 'Venta #' . $venta->id,
                );
            }
        }, 3);

        return redirect()->route('admin.inventarios.kardex')->with('success', 'Venta registrada.');
    }
}
