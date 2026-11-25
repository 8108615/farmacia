<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Cliente;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaReporteController extends Controller
{
    public function index(Request $request)
    {
        $reportes = [
            'dia' => ['Ventas por día', 'Totales agrupados por fecha.', 'calendar3', 'primary'],
            'cliente' => ['Ventas por cliente', 'Consulta las ventas emitidas de un cliente.', 'person', 'success'],
            'producto' => ['Ventas por producto', 'Unidades e importe por producto.', 'box-seam', 'info'],
            'sucursal' => ['Ventas por sucursal', 'Compara el importe vendido por sucursal.', 'geo-alt', 'warning'],
            'pago' => ['Ventas por forma de pago', 'Consulta los importes por medio de pago.', 'credit-card', 'danger'],
            'productos' => ['Productos más vendidos', 'Los 20 productos con más unidades vendidas.', 'graph-up', 'primary'],
            'general' => ['Reporte general', 'Consulta las ventas emitidas del período.', 'file-earmark-text', 'success'],
        ];
        $filtros = $request->validate([
            'cliente' => ['required_if:reporte,cliente', 'nullable', 'integer', 'exists:clientes,id'],
            'desde' => ['required_with:hasta', 'date_format:Y-m-d'],
            'hasta' => ['required_with:desde', 'date_format:Y-m-d', 'after_or_equal:desde'],
            'sucursal' => ['nullable', 'integer', 'exists:sucursals,id'],
            'reporte' => ['nullable', 'in:' . implode(',', array_keys($reportes))],
        ]);

        $hoy = today();
        $filtros['desde'] ??= $hoy->copy()->startOfMonth()->toDateString();
        $filtros['hasta'] ??= $hoy->toDateString();
        $reporte = $filtros['reporte'] ?? null;
        $sucursales = Sucursal::query()->orderBy('nombre')->get(['id', 'nombre']);
        $clientes = Cliente::query()->orderBy('nombres_apellidos')->get(['id', 'nombres_apellidos', 'ci_nit']);
        if ($reporte === null) {
            return view('admin.ventas.reportes', compact('reportes', 'filtros', 'reporte', 'clientes', 'sucursales'));
        }

        $listaVentas = in_array($reporte, ['cliente', 'general'], true);
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';

        $ventas = DB::table('ventas')
            ->where('ventas.estado', 'emitida')
            ->whereBetween('ventas.fecha_venta', [$filtros['desde'], $filtros['hasta']])
            ->when($filtros['sucursal'] ?? null, fn ($query, $id) => $query->where('ventas.sucursal_id', $id));

        if ($reporte === 'cliente') {
            $ventas->where('ventas.cliente_id', $filtros['cliente']);
        }

        $resumen = (clone $ventas)
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(total_venta), 0) as importe')
            ->first();

        if ($listaVentas) {
            $filas = $ventas
                ->leftJoin('clientes', 'clientes.id', '=', 'ventas.cliente_id')
                ->leftJoin('sucursals', 'sucursals.id', '=', 'ventas.sucursal_id')
                ->select('ventas.id', 'ventas.fecha_venta', 'ventas.total_venta', 'ventas.tipo_pago',
                    'clientes.nombres_apellidos as cliente', 'sucursals.nombre as sucursal')
                ->orderByDesc('ventas.fecha_venta')->orderByDesc('ventas.id')
                ->paginate(20)->appends($filtros);
        } elseif (in_array($reporte, ['producto', 'productos'], true)) {
            $consultaProductos = $ventas
                ->join('venta_detalles', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->leftJoin('inventarios', 'inventarios.id', '=', 'venta_detalles.inventario_id')
                ->leftJoin('productos', 'productos.id', '=', 'inventarios.producto_id')
                ->select('productos.nombre_comercial as etiqueta', 'productos.codigo_producto as codigo')
                ->selectRaw('SUM(venta_detalles.cantidad) as cantidad, SUM(venta_detalles.cantidad * venta_detalles.precio_venta_unidad) as importe')
                ->groupBy('productos.id', 'productos.nombre_comercial', 'productos.codigo_producto')
                ->orderByDesc('cantidad')->orderByDesc('importe')->orderBy('productos.id');
            $filas = $reporte === 'productos'
                ? $consultaProductos->limit(20)->get()
                : $consultaProductos->paginate(20)->appends($filtros);
        } else {
            $columna = match ($reporte) {
                'sucursal' => 'sucursals.nombre',
                'pago' => 'ventas.tipo_pago',
                default => 'ventas.fecha_venta',
            };
            if ($reporte === 'sucursal') {
                $ventas->leftJoin('sucursals', 'sucursals.id', '=', 'ventas.sucursal_id')
                    ->groupBy('ventas.sucursal_id');
            }
            $filas = $ventas->select($columna . ' as etiqueta')
                ->selectRaw('COUNT(*) as cantidad, SUM(ventas.total_venta) as importe')
                ->groupBy($columna)->orderBy($columna)->get();
        }

        return view('admin.ventas.reportes', compact(
            'reportes', 'filtros', 'reporte', 'sucursales', 'divisa', 'resumen', 'filas', 'listaVentas', 'clientes'
        ));
    }
}
