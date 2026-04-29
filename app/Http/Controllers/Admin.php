<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Admin extends Controller
{
    public function index()
    {
        $config = Ajuste::query()->first();

        $stats = [
            'configuracion' => $config ? 1 : 0,
            'roles' => Role::count(),
            'usuarios' => User::count(),
            'sucursales' => Sucursal::count(),
            'empleados' => Empleado::count(),
            'empleados_activos' => Empleado::where('estado', 'activo')->count(),
            'empleados_inactivos' => Empleado::where('estado', 'inactivo')->count(),
        ];

        $latestEmployees = Empleado::with(['usuario:id,name,email', 'sucursal:id,nombre'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('admin.index', compact('config', 'stats', 'latestEmployees'));
    }
}
