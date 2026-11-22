<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Inventario;
use App\Models\Lote;
use Illuminate\Http\Request;
use App\Models\Sucursal;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventario::query();

        $totalItems = (clone $query)->count();
        $totalStock = (int) (clone $query)->sum('stock_actual');
        $totalBajoMinimo = (clone $query)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->count();
        $totalSinUbicacion = (clone $query)->whereNull('ubicacion_fisica_id')->count();

        $hoy = now()->toDateString();
        $en30Dias = now()->addDays(30)->toDateString();
        $totalPorVencer = (clone $query)
            ->whereHas('lote', function ($loteQuery) use ($hoy, $en30Dias) {
                $loteQuery->whereNotNull('fecha_vencimiento')
                    ->whereBetween('fecha_vencimiento', [$hoy, $en30Dias]);
            })
            ->count();

        $valorCompra = (float) ((clone $query)
            ->selectRaw('COALESCE(SUM(stock_actual * precio_compra_unidad), 0) as total')
            ->value('total') ?? 0);

        $valorVenta = (float) ((clone $query)
            ->selectRaw('COALESCE(SUM(stock_actual * precio_venta_unidad), 0) as total')
            ->value('total') ?? 0);

        $divisa = Ajuste::query()->value('divisa') ?? 'Bs.';

        return view('admin.inventarios.index', compact(
            'divisa',
            'totalItems',
            'totalStock',
            'totalBajoMinimo',
            'totalPorVencer',
            'totalSinUbicacion',
            'valorCompra',
            'valorVenta'
        ));

    }

    public function existencias(Request $request)
    {
        $filtros = $request->validate([
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursals,id'],
            'buscar' => ['nullable', 'string', 'max:150'],
        ]);

        $sucursalId = $filtros['sucursal_id'] ?? null;
        $buscar = trim($filtros['buscar'] ?? '');

        $consulta = Inventario::query()
            ->with([
                'producto.categoria',
                'producto.laboratorio',
                'lote',
                'sucursal',
                'ubicacionFisica',
            ]);

        if ($sucursalId !== null) {
            $consulta->where('sucursal_id', $sucursalId);
        }

        if ($buscar !== '') {
            $patron = '%' . $buscar . '%';

            $consulta->where(function ($query) use ($patron) {
                $query->whereHas('producto', function ($productos) use ($patron) {
                    $productos->where('nombre_comercial', 'like', $patron)
                        ->orWhere('nombre_generico', 'like', $patron)
                        ->orWhere('codigo_producto', 'like', $patron)
                        ->orWhere('codigo_barra', 'like', $patron);
                })->orWhereHas('lote', function ($lotes) use ($patron) {
                    $lotes->where('numero_lote', 'like', $patron);
                });
            });
        }

        $existencias = $consulta
            ->orderBy('producto_id')
            ->orderBy('sucursal_id')
            ->orderBy('lote_id')
            ->orderBy('id')
            ->paginate(15)
            ->appends($filtros);

        $sucursales = Sucursal::query()->orderBy('nombre')->get(['id', 'nombre']);
        $divisa = Ajuste::query()->value('divisa') ?? 'Bs.';

        return view('admin.inventarios.existencias', compact(
            'existencias',
            'sucursales',
            'sucursalId',
            'buscar',
            'divisa'
        ));
    }

    public function movimientos(Request $request)
    {
        $filtros = $request->validate([
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursals,id'],
            'buscar' => ['nullable', 'string', 'max:150'],
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:desde'],
        ]);

        $consulta = \App\Models\CompraDetalle::query()
            ->join('compras', 'compras.id', '=', 'compra_detalles.compra_id')
            ->where('compras.estado', 'completada')
            ->select('compra_detalles.*')
            ->with(['compra.sucursal', 'producto', 'lote'])
            ->when($filtros['sucursal_id'] ?? null, fn ($query, $id) => $query->where('compras.sucursal_id', $id))
            ->when($filtros['desde'] ?? null, fn ($query, $fecha) => $query->where('compras.fecha_compra', '>=', $fecha))
            ->when($filtros['hasta'] ?? null, fn ($query, $fecha) => $query->where('compras.fecha_compra', '<=', $fecha));

        $buscar = trim($filtros['buscar'] ?? '');

        if ($buscar !== '') {
            $patron = '%' . $buscar . '%';

            $consulta->where(function ($query) use ($patron) {
                $query->whereHas('producto', function ($productos) use ($patron) {
                    $productos->where('nombre_comercial', 'like', $patron)
                        ->orWhere('nombre_generico', 'like', $patron)
                        ->orWhere('codigo_producto', 'like', $patron)
                        ->orWhere('codigo_barra', 'like', $patron);
                })->orWhereHas('lote', fn ($lotes) => $lotes->where('numero_lote', 'like', $patron));
            });
        }

        $movimientos = $consulta->orderByDesc('compras.fecha_compra')
            ->orderByDesc('compra_detalles.id')
            ->paginate(15)
            ->appends($filtros);

        $sucursales = Sucursal::query()->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.inventarios.movimientos', compact('movimientos', 'sucursales', 'filtros'));
    }
}
