<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Services\AccesoCaja;
use App\Services\CajaDeVenta;
use App\Services\RegistroMovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $estados = ['pendiente', 'emitida', 'cancelada', 'devuelta'];
        $formato = $request->validate(['exportar' => ['nullable', 'in:xlsx,pdf']])['exportar'] ?? null;
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:' . implode(',', $estados)],
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', ...($request->filled('desde') ? ['after_or_equal:desde'] : [])],
        ]);

        $buscar = trim($filtros['buscar'] ?? '');
        $consulta = Venta::query()
            ->leftJoin('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->leftJoin('sucursals', 'sucursals.id', '=', 'ventas.sucursal_id')
            ->leftJoin('users', 'users.id', '=', 'ventas.usuario_id')
            ->select('ventas.*', 'clientes.nombres_apellidos as cliente', 'sucursals.nombre as sucursal', 'users.name as vendedor')
            ->when($filtros['estado'] ?? null, fn ($query, $estado) => $query->where('ventas.estado', $estado))
            ->when($filtros['desde'] ?? null, fn ($query, $fecha) => $query->where('ventas.fecha_venta', '>=', $fecha))
            ->when($filtros['hasta'] ?? null, fn ($query, $fecha) => $query->where('ventas.fecha_venta', '<=', $fecha));

        if ($buscar !== '') {
            $patron = '%' . $buscar . '%';
            $id = filter_var(ltrim($buscar, '#'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $consulta->where(function ($query) use ($patron, $id) {
                $query->where('clientes.nombres_apellidos', 'like', $patron)
                    ->orWhere('sucursals.nombre', 'like', $patron)
                    ->orWhere('users.name', 'like', $patron);
                if ($id !== false) {
                    $query->orWhere('ventas.id', $id);
                }
            });
        }

        $consulta->orderByDesc('ventas.fecha_venta')->orderByDesc('ventas.id');
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';

        if ($formato !== null) {
            return $this->exportarVentas($consulta, $formato, $divisa);
        }

        $ventas = $consulta->paginate(10)->appends($filtros);

        $hoy = today();
        $emitidas = Venta::query()->where('estado', 'emitida');
        $resumenHoy = (clone $emitidas)->where('fecha_venta', $hoy->toDateString())
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(total_venta), 0) as importe')->first();
        $resumenMes = (clone $emitidas)
            ->whereBetween('fecha_venta', [$hoy->copy()->startOfMonth()->toDateString(), $hoy->copy()->endOfMonth()->toDateString()])
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(total_venta), 0) as importe')->first();
        $recaudado = (clone $emitidas)->sum('total_venta');
        $canceladas = Venta::query()->where('estado', 'cancelada')->count();

        $inicio = $hoy->copy()->subDays(6);
        $importes = (clone $emitidas)->whereBetween('fecha_venta', [$inicio->toDateString(), $hoy->toDateString()])
            ->selectRaw('fecha_venta, SUM(total_venta) as importe')->groupBy('fecha_venta')
            ->pluck('importe', 'fecha_venta');
        $dias = collect(range(0, 6))->map(function ($desfase) use ($inicio, $importes) {
            $fecha = $inicio->copy()->addDays($desfase);
            return ['fecha' => $fecha->format('d/m'), 'importe' => (float) ($importes[$fecha->toDateString()] ?? 0)];
        });
        $pagos = (clone $emitidas)->selectRaw('tipo_pago, COUNT(*) as cantidad')->groupBy('tipo_pago')
            ->pluck('cantidad', 'tipo_pago');

        return view('admin.ventas.index', compact(
            'ventas', 'divisa', 'estados', 'filtros', 'resumenHoy', 'resumenMes',
            'recaudado', 'canceladas', 'dias', 'pagos',
        ));
    }


    private function exportarVentas(\Illuminate\Database\Eloquent\Builder $consulta, string $formato, string $divisa)
    {
        $columnas = ['Venta', 'Sucursal', 'Cliente', 'Usuario', 'Fecha', 'Forma de pago', 'Estado', 'Total', 'Moneda'];
        $filas = $consulta->cursor()->map(fn (Venta $venta) => [
            (int) $venta->id,
            $venta->sucursal ?? '',
            $venta->cliente ?? 'Sin cliente',
            $venta->vendedor ?? '',
            \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y'),
            ucfirst($venta->tipo_pago),
            ucfirst($venta->estado),
            (float) $venta->total_venta,
            $divisa,
        ]);
        $nombre = 'ventas-' . today()->format('Y-m-d');

        if ($formato === 'pdf') {
            $pdf = new \Dompdf\Dompdf();
            $pdf->setPaper('a4', 'landscape');
            $pdf->loadHtml(view('admin.ventas.exportar-pdf', compact('columnas', 'filas'))->render(), 'UTF-8');
            $pdf->render();

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $nombre . '.pdf"',
                'Cache-Control' => 'private, no-store',
            ]);
        }

        $libro = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $hoja = $libro->getActiveSheet();
        $hoja->setTitle('Ventas');
        $hoja->fromArray($columnas, null, 'A1');
        $numeroFila = 2;
        foreach ($filas as $fila) {
            foreach ($fila as $columna => $valor) {
                $tipo = is_string($valor)
                    ? \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    : \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC;
                $hoja->setCellValueExplicit([$columna + 1, $numeroFila], $valor, $tipo);
            }
            $numeroFila++;
        }
        $hoja->getStyle('A1:I1')->getFont()->setBold(true);
        $hoja->setAutoFilter('A1:I' . ($numeroFila - 1));
        $hoja->freezePane('A2');
        if ($numeroFila > 2) {
            $hoja->getStyle('H2:H' . ($numeroFila - 1))->getNumberFormat()->setFormatCode('#,##0.00');
        }
        foreach (['A' => 10, 'B' => 24, 'C' => 34, 'D' => 30, 'E' => 15, 'F' => 19, 'G' => 17, 'H' => 16, 'I' => 12] as $columna => $ancho) {
            $hoja->getColumnDimension($columna)->setWidth($ancho);
        }

        return response()->streamDownload(function () use ($libro) {
            try {
                (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($libro))->save('php://output');
            } finally {
                $libro->disconnectWorksheets();
            }
        }, $nombre . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store',
        ]);
    }


    public function comprobante(Request $request, Venta $venta)
    {
        $datos = $this->datosVenta($venta);
        $descarga = $request->query('formato') === 'pdf';
        $vista = view('admin.ventas.comprobante', $datos + ['descarga' => $descarga]);

        if (!$descarga) {
            return $vista;
        }

        $pdf = new \Dompdf\Dompdf();
        $pdf->setPaper('a4');
        $pdf->loadHtml($vista->render(), 'UTF-8');
        $pdf->render();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="venta-' . $venta->id . '.pdf"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function cancelar(Request $request, Venta $venta, RegistroMovimientoInventario $registro, CajaDeVenta $caja)
    {
        $usuario = $request->user();
        $usuarioId = (int) $usuario->id;

        $cancelada = DB::transaction(function () use ($venta, $usuario, $usuarioId, $registro, $caja) {
            $usuario->newQuery()->lockForUpdate()->findOrFail($usuarioId);
            $actual = Venta::query()->lockForUpdate()->findOrFail($venta->id);
            abort_unless((int) $actual->usuario_id === $usuarioId, 403);

            if ($actual->estado !== 'emitida') {
                return false;
            }

            $salidas = MovimientoInventario::query()
                ->where('tipo', 'salida_venta')->where('referencia', 'Venta #' . $actual->id)
                ->orderBy('inventario_id')->get();
            $detalles = VentaDetalle::query()->where('venta_id', $actual->id)
                ->orderBy('inventario_id')->lockForUpdate()->get();

            if ($salidas->isEmpty() || $salidas->count() !== $detalles->count()
                || $salidas->pluck('inventario_id')->unique()->count() !== $salidas->count()) {
                return false;
            }

            $partidas = Inventario::query()->whereKey($salidas->pluck('inventario_id')->all())
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            foreach ($salidas as $indice => $salida) {
                $partida = $partidas->get($salida->inventario_id);
                $detalle = $detalles[$indice];
                if (!$partida || $salida->cantidad >= 0
                    || (int) $detalle->inventario_id !== (int) $salida->inventario_id
                    || (int) $detalle->cantidad !== -$salida->cantidad
                    || (int) $partida->producto_id !== (int) $salida->producto_id
                    || (int) $partida->lote_id !== (int) $salida->lote_id
                    || (int) $partida->sucursal_id !== (int) $salida->sucursal_id) {
                    return false;
                }
            }

            if ($actual->tipo_pago === 'efectivo') {
                $caja->registrarDevolucion($actual->id, $actual->sucursal_id, $usuarioId);
            }

            foreach ($salidas as $salida) {
                $partida = $partidas[$salida->inventario_id];
                $anterior = (int) $partida->stock_actual;
                $partida->stock_actual = $anterior - $salida->cantidad;
                $partida->save();

                $registro->registrar(
                    $partida, 'cancelacion_venta', $anterior, $partida->stock_actual,
                    'cancelacion-venta:' . $actual->id . ':' . $partida->id,
                    $usuarioId, 'Venta #' . $actual->id,
                );
            }

            $actual->estado = 'cancelada';
            $actual->save();

            return true;
        }, 3);

        return $cancelada
            ? redirect()->route('admin.ventas.index')->with('success', 'Venta cancelada. El stock fue devuelto a sus partidas.')
            : redirect()->route('admin.ventas.index')->with('error', 'La venta ya no está emitida o sus partidas no coinciden con el Kardex.');
    }


    public function show(Venta $venta)
    {
        return view('admin.ventas.show', $this->datosVenta($venta));
    }

    private function datosVenta(Venta $venta): array
    {
        $cliente = Cliente::find($venta->cliente_id);
        $sucursal = Sucursal::find($venta->sucursal_id);
        $detalles = VentaDetalle::query()
            ->where('venta_id', $venta->id)
            ->leftJoin('inventarios', 'inventarios.id', '=', 'venta_detalles.inventario_id')
            ->leftJoin('productos', 'productos.id', '=', 'inventarios.producto_id')
            ->leftJoin('lotes', 'lotes.id', '=', 'inventarios.lote_id')
            ->select(
                'venta_detalles.*',
                'productos.nombre_comercial as producto',
                'productos.codigo_producto',
                'productos.nombre_generico',
                'productos.concentracion',
                'productos.unidad_medida',
                'lotes.numero_lote as lote',
            )
            ->orderBy('venta_detalles.id')
            ->get();
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';

        return compact('venta', 'cliente', 'sucursal', 'detalles', 'divisa') + ['vendedor' => \App\Models\User::find($venta->usuario_id)?->name];
    }


    public function create(Request $request)
    {
        $acceso = new AccesoCaja($request->user());
        abort_unless($acceso->puedeOperar, 403);

        $sucursalesConCaja = DB::table('arqueos')->select('sucursal_id')
            ->where('usuario_id', $request->user()->id)->where('estado', 'abierto')
            ->groupBy('sucursal_id')->havingRaw('COUNT(*) = 1');

        $sucursales = $acceso->limitarSucursal(Sucursal::query(), 'id')->whereIn('id', $sucursalesConCaja)->orderBy('nombre')->get();

        if ($sucursales->isEmpty()) {
            return view('admin.ventas.create', compact('sucursales'));
        }

        $clientes = Cliente::query()->orderBy('nombres_apellidos')->get();
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';
        $partidas = Inventario::query()->with(['producto.laboratorio', 'producto.formaFarmaceutica', 'producto.presentacion', 'producto.categoria', 'lote', 'sucursal', 'ubicacionFisica'])
            ->whereIn('sucursal_id', $sucursales->modelKeys())
            ->where('stock_actual', '>', 0)
            ->orderBy('id')->get();
        $operacion = (string) Str::uuid();

        return view('admin.ventas.create', compact('clientes', 'sucursales', 'partidas', 'divisa', 'operacion'));
    }

    public function store(Request $request, RegistroMovimientoInventario $registro, CajaDeVenta $caja)
    {
        $datos = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'sucursal_id' => ['required', 'integer', 'exists:sucursals,id'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['required', 'array:inventario_id,cantidad'],
            'items.*.inventario_id' => ['required', 'integer', 'distinct'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'tipo_pago' => ['required', 'in:efectivo,tarjeta,transferencia'],
            'monto_recibido' => ['exclude_unless:tipo_pago,efectivo', 'required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
            'operacion' => ['required', 'uuid'],
        ]);

        $usuario = $request->user();
        $clave = 'venta:' . $usuario->id . ':' . $datos['operacion'];
        $items = collect($datos['items'])->keyBy('inventario_id')->sortKeys();

        DB::transaction(function () use ($datos, $usuario, $clave, $items, $registro, $caja) {
            // Serializa los reenvíos de la misma venta, incluso si cambia el carrito.
            $usuario->newQuery()->lockForUpdate()->findOrFail($usuario->id);

            if (MovimientoInventario::query()->where('clave_operacion', $clave)->exists()) {
                return;
            }

            $turno = $caja->turnoAbierto($datos['sucursal_id'], $usuario->id);
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

            $recibidoCentavos = null;
            if ($datos['tipo_pago'] === 'efectivo') {
                $recibidoCentavos = (int) str_replace('.', '', number_format((float) $datos['monto_recibido'], 2, '.', ''));
                if ($recibidoCentavos < $totalCentavos) {
                    throw ValidationException::withMessages([
                        'monto_recibido' => 'El monto recibido debe cubrir el total de la venta.',
                    ]);
                }
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
            if ($recibidoCentavos !== null) {
                $cambioCentavos = $recibidoCentavos - $totalCentavos;
                $venta->monto_recibido = sprintf('%d.%02d', intdiv($recibidoCentavos, 100), $recibidoCentavos % 100);
                $venta->cambio = sprintf('%d.%02d', intdiv($cambioCentavos, 100), $cambioCentavos % 100);
            }
            $venta->save();

            if ($datos['tipo_pago'] === 'efectivo') {
                $caja->registrarCobro($turno, $venta->id, $totalCentavos);
            }

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

        return redirect()->route('admin.ventas.index')->with('success', 'Venta registrada.');
    }
}
