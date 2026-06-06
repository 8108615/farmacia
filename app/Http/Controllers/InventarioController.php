<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventario::query();

        $totalItems = (clone $query)->count();
        $totalStock = (int) (clone $query)->sum('stock_actual');
        $totalBajoMinimo = (clone $query)
            ->whereColumn('stock_actual', '<', 'stock_minimo')
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

    
}
