<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Compra;
use App\Models\Empleado;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;

class Admin extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();
        $global = $usuario->hasRole('SUPER ADMIN');
        $empleado = $usuario->empleado()->with('sucursal')->first();
        $sucursal = $empleado?->estado === 'activo' ? $empleado->sucursal : null;
        $config = Ajuste::query()->first();
        $divisa = $config?->divisa ?: 'Bs';
        $hoy = today();
        $inicioMes = $hoy->copy()->startOfMonth()->toDateString();
        $fechaHoy = $hoy->toDateString();

        $ventas = Venta::query();
        $compras = Compra::query();
        $inventario = Inventario::query();
        if (!$global) {
            foreach ([$ventas, $compras, $inventario] as $consulta) {
                $sucursal ? $consulta->where($consulta->qualifyColumn('sucursal_id'), $sucursal->id) : $consulta->whereRaw('1 = 0');
            }
            if ($usuario->hasRole('VENDEDOR')) {
                $ventas->where('ventas.usuario_id', $usuario->id);
            }
        }

        $emitidas = (clone $ventas)->where('estado', 'emitida');
        $mes = (clone $emitidas)->whereBetween('fecha_venta', [$inicioMes, $fechaHoy]);
        $ventasHoy = (clone $mes)->where('fecha_venta', $fechaHoy)
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(total_venta), 0) as importe')->first();
        $ventasMes = (clone $mes)->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(total_venta), 0) as importe')->first();
        $canceladas = (clone $ventas)->whereBetween('fecha_venta', [$inicioMes, $fechaHoy])
            ->where('estado', 'cancelada')->count();
        $pagos = (clone $mes)->selectRaw('tipo_pago, COUNT(*) as cantidad')
            ->groupBy('tipo_pago')->orderBy('tipo_pago')->pluck('cantidad', 'tipo_pago');

        $inicioSemana = $hoy->copy()->subDays(6);
        $porDia = (clone $emitidas)->whereBetween('fecha_venta', [$inicioSemana->toDateString(), $fechaHoy])
            ->selectRaw('fecha_venta, COUNT(*) as cantidad, SUM(total_venta) as importe')
            ->groupBy('fecha_venta')->get()->keyBy('fecha_venta');
        $semana = collect(range(0, 6))->map(function ($i) use ($inicioSemana, $porDia) {
            $dia = $inicioSemana->copy()->addDays($i);
            $datos = $porDia->get($dia->toDateString());

            return ['fecha' => $dia->format('d/m'), 'cantidad' => (int) ($datos->cantidad ?? 0),
                'importe' => (float) ($datos->importe ?? 0)];
        });

        $recientes = $ventas->leftJoin('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->leftJoin('sucursals', 'sucursals.id', '=', 'ventas.sucursal_id')
            ->select('ventas.id', 'ventas.fecha_venta', 'ventas.total_venta', 'ventas.estado',
                'clientes.nombres_apellidos as cliente', 'sucursals.nombre as sucursal')
            ->orderByDesc('ventas.fecha_venta')->orderByDesc('ventas.id')->limit(6)->get();
        $latestCompras = (clone $compras)->with(['proveedor:id,nombre', 'sucursal:id,nombre'])
            ->orderByDesc('fecha_compra')->orderByDesc('id')->limit(6)->get();

        $stats = [
            'compras_mes' => $compras->whereDate('fecha_compra', '>=', $inicioMes)->whereDate('fecha_compra', '<=', $fechaHoy)->count(),
            'productos' => $global ? Producto::count() : (clone $inventario)->distinct()->count('producto_id'),
            'inventarios' => (clone $inventario)->count(),
        ];
        if ($global) {
            $stats += ['usuarios' => User::count(), 'empleados' => Empleado::count(), 'proveedores' => Proveedor::count()];
        }

        $lotesConStock = $inventario->join('lotes', 'lotes.id', '=', 'inventarios.lote_id')->where('stock_actual', '>', 0);
        $vencidos = (clone $lotesConStock)->where('lotes.fecha_vencimiento', '<', $fechaHoy)->distinct()->count('lotes.id');
        $proximos = $lotesConStock->whereBetween('lotes.fecha_vencimiento', [$fechaHoy, $hoy->copy()->addDays(30)->toDateString()])
            ->distinct()->count('lotes.id');

        return view('admin.index', compact(
            'usuario', 'sucursal', 'global', 'config', 'divisa', 'ventasHoy', 'ventasMes', 'canceladas',
            'pagos', 'semana', 'recientes', 'latestCompras', 'stats', 'vencidos', 'proximos',
        ));
    }
}
