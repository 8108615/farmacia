<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Empleado;
use App\Models\Inventario;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class Admin extends Controller
{
    public function index()
    {
        $config = Ajuste::query()->first();

        $totalEmpleados = Empleado::count();
        $empleadosActivos = Empleado::where('estado', 'activo')->count();
        $empleadosInactivos = Empleado::where('estado', 'inactivo')->count();

        $totalCompras = Compra::count();
        $comprasPendientes = Compra::where('estado', 'pendiente')->count();
        $comprasCompletadas = Compra::where('estado', 'completada')->count();

        $stats = [
            'configuracion' => $config ? 1 : 0,
            'roles' => Role::count(),
            'usuarios' => User::count(),
            'sucursales' => Sucursal::count(),
            'empleados' => $totalEmpleados,
            'empleados_activos' => $empleadosActivos,
            'empleados_inactivos' => $empleadosInactivos,
            'proveedores' => Proveedor::count(),
            'productos' => Producto::count(),
            'lotes' => Lote::count(),
            'inventarios' => Inventario::count(),
            'compras_total' => $totalCompras,
            'compras_pendientes' => $comprasPendientes,
            'compras_completadas' => $comprasCompletadas,
            'compras_mes' => Compra::whereBetween('fecha_compra', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString(),
            ])->count(),
            'compras_hoy' => Compra::whereDate('fecha_compra', Carbon::today()->toDateString())->count(),
            'items_compra' => CompraDetalle::count(),
        ];

        $activeRatio = $totalEmpleados > 0
            ? round(($empleadosActivos / $totalEmpleados) * 100, 1)
            : 0;

        $comprasPendientesRatio = $totalCompras > 0
            ? round(($comprasPendientes / $totalCompras) * 100, 1)
            : 0;

        $latestEmployees = Empleado::with(['usuario:id,name,email', 'sucursal:id,nombre'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $latestCompras = Compra::with(['proveedor:id,nombre', 'sucursal:id,nombre', 'usuario:id,name'])
            ->orderByDesc('fecha_compra')
            ->limit(6)
            ->get();

        return view('admin.index', compact(
            'config',
            'stats',
            'latestEmployees',
            'latestCompras',
            'activeRatio',
            'comprasPendientesRatio'
        ));
    }
}
