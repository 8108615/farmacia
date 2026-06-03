<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Inventario;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\UbicacionFisica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ajuste = Ajuste::query()->first();
        $compras = Compra::with(['sucursal', 'proveedor', 'usuario'])
            ->where('estado', 'completada')
            ->orderBy('id', 'desc')
            ->paginate(10);
        $pendingOrders = Compra::with(['sucursal', 'proveedor', 'usuario'])->where('estado', 'pendiente')->orderBy('fecha_compra', 'desc')->get();
        return view('admin.compras.index', compact('ajuste', 'compras', 'pendingOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('admin.compras.create', $this->prepareCompraFormData($id));
    }

    public function create_lote(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $compra = Compra::findOrFail($id);
            $detalleCompraId = $request->input('compra_detalle_id');

            $request->validate([
                'producto_id' => ['required', 'integer', 'exists:productos,id'],
                'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
                'nombre' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('lotes', 'numero_lote')->where(function ($query) use ($request) {
                        return $query->where('producto_id', $request->input('producto_id'));
                    }),
                ],
                'fecha_vencimiento' => ['nullable', 'date'],
                'fecha_fabricacion' => ['nullable', 'date'],
            ]);

            $detalleCompra = CompraDetalle::where('id', $detalleCompraId)
                ->where('compra_id', $compra->id)
                ->firstOrFail();

            if ((int) $detalleCompra->producto_id !== (int) $request->input('producto_id')) {
                $message = 'El producto no coincide con el detalle de compra.';
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 422);
                }

                return redirect()
                    ->route('admin.compras.create', ['id' => $compra->id])
                    ->with('error', $message);
            }

            $lote = new Lote();
            $lote->producto_id = $request->input('producto_id');
            $lote->proveedor_id = $request->input('proveedor_id');
            $lote->nombre = trim((string) $request->input('nombre'));
            $lote->fecha_vencimiento = $request->input('fecha_vencimiento');
            $lote->fecha_fabricacion = $request->input('fecha_fabricacion');
            $lote->save();

            $detalleCompra->lote_id = $lote->id;
            $detalleCompra->save();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lote creado correctamente.',
                    'lote_id' => $lote->id,
                    'lote_nombre' => $lote->nombre,
                    'compra_detalle_id' => $detalleCompraId,
                ], 200);
            }

            return redirect()
                ->route('admin.compras.create', ['id' => $compra->id])
                ->with('success', 'Lote creado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo crear el lote: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('admin.compras.create', ['id' => $id])
                ->with('error', 'No se pudo crear el lote: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        return $this->guardarCompra($request, Compra::with('detalles')->findOrFail($id));
    }

    private function prepareCompraFormData($id)
    {
        $compra = Compra::with(['detalles.producto', 'detalles.lote'])->findOrFail($id);

        $sucursales = Sucursal::all();
        $proveedores = Proveedor::all();
        $productos = Producto::with(['categoria', 'laboratorio', 'formaFarmaceutica', 'presentacion'])->get();
        $lotes = Lote::all();
        $ubicacionesFisicas = UbicacionFisica::with('sucursal')->get();

        $carritoInicial = $compra->detalles->map(function ($detalle) use ($compra) {
            $producto = $detalle->producto;
            $inventario = Inventario::where('producto_id', $detalle->producto_id)
                ->where('lote_id', $detalle->lote_id)
                ->where('sucursal_id', $compra->sucursal_id)
                ->where('compra_id', $compra->id)
                ->latest('id')
                ->first();

            $nombreComercial = $producto?->nombre_comercial ?? 'Producto';
            $nombreGenerico = $producto?->nombre_generico ?? 'N/A';
            $codigo = $producto?->codigo_producto ?? 'N/A';

            return [
                'id' => $detalle->id,
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $nombreComercial . ' (' . $nombreGenerico . ') - Código: ' . $codigo,
                'cantidad' => (int) $detalle->cantidad,
                'precio_compra' => (float) $detalle->precio_compra_unidad,
                'precio_venta' => (float) $detalle->precio_venta_unidad,
                'porcentaje_ganancia' => (float) $detalle->porcentaje_ganancia_unidad,
                'lote_id' => $detalle->lote_id,
                'lote_nombre' => $detalle->lote?->nombre,
                'lote_fecha_vencimiento' => $detalle->lote?->fecha_vencimiento?->format('Y-m-d') ?? '',
                'lote_fecha_fabricacion' => $detalle->lote?->fecha_fabricacion?->format('Y-m-d') ?? '',
                'subtotal' => (float) $detalle->cantidad * (float) $detalle->precio_compra_unidad,
                'ubicacion_fisica_id' => $inventario?->ubicacion_fisica_id,
                'stock_minimo' => $inventario?->stock_minimo ?? '',
                'stock_maximo' => $inventario?->stock_maximo ?? '',
                'observaciones' => $detalle->observaciones ?? '',
            ];
        })->values();

        $ajuste = Ajuste::query()->first();

        return compact(
            'compra',
            'sucursales',
            'proveedores',
            'productos',
            'carritoInicial',
            'ajuste',
            'lotes',
            'ubicacionesFisicas'
        );
    }

    public function edit($id)
    {
        return view('admin.compras.edit', $this->prepareCompraFormData($id));
    }

    public function update(Request $request, $id)
    {
        //return response()->json([$request->all(), $id]);
        $compra = Compra::with('detalles')->findOrFail($id);
        return $this->guardarCompra($request, $compra);
    }

    private function guardarCompra(Request $request, Compra $compra)
    {
        try {
            $validated = $request->validate([
                'sucursal_id' => 'required|exists:sucursals,id',
                'proveedor_id' => 'required|exists:proveedores,id',
                'comprobante' => 'nullable|string|max:255',
                'nota' => 'nullable|string|max:1000',
                'carrito' => 'required|json',
            ]);

            $carrito = json_decode($request->input('carrito'), true) ?? [];

            if (empty($carrito)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El carrito está vacío.'
                ], 400);
            }

            $itemsValidos = collect($carrito)
                ->filter(function ($item) {
                    return isset($item['producto_id'], $item['cantidad'])
                        && (int) $item['cantidad'] > 0;
                })
                ->values();

            if ($itemsValidos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe ingresar al menos un item con cantidad mayor a 0.'
                ], 422);
            }

            foreach ($itemsValidos as $item) {
                if (empty($item['lote_id']) || !is_numeric($item['lote_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cada producto debe tener un lote asignado.'
                    ], 422);
                }

                $lotePertenece = Lote::where('id', (int) $item['lote_id'])
                    ->where('producto_id', (int) $item['producto_id'])
                    ->exists();

                if (!$lotePertenece) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El lote seleccionado no pertenece al producto.'
                    ], 422);
                }

                if (empty($item['ubicacion_fisica_id']) || !is_numeric($item['ubicacion_fisica_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cada producto debe tener una ubicación física seleccionada.'
                    ], 422);
                }

                $ubicacionExiste = UbicacionFisica::where('id', (int) $item['ubicacion_fisica_id'])->exists();
                if (!$ubicacionExiste) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La ubicación física seleccionada no existe.'
                    ], 422);
                }
            }

            DB::beginTransaction();

            try {
                $compra->load('detalles');

                $totalCompra = $itemsValidos->sum(function ($item) {
                    return ((float) ($item['cantidad'] ?? 0)) * ((float) ($item['precio_compra'] ?? 0));
                });

                $compra->sucursal_id = $validated['sucursal_id'];
                $compra->proveedor_id = $validated['proveedor_id'];
                $compra->total = $totalCompra;
                $compra->comprobante = $validated['comprobante'] ?? null;
                $compra->nota = $validated['nota'] ?? null;
                $compra->estado = 'completada';
                if (empty($compra->fecha_compra)) {
                    $compra->fecha_compra = Carbon::now()->toDateString();
                }
                $compra->save();

                $detallesExistentes = $compra->detalles->keyBy('id');
                $detallesMantener = [];

                foreach ($itemsValidos as $item) {
                    $detalleId = isset($item['id']) && is_numeric($item['id']) ? (int) $item['id'] : null;

                    $detalle = null;
                    if ($detalleId && $detallesExistentes->has($detalleId)) {
                        $detalle = $detallesExistentes->get($detalleId);
                    }

                    if (!$detalle) {
                        $detalle = new CompraDetalle();
                        $detalle->compra_id = $compra->id;
                    }

                    $precioCompra = (float) ($item['precio_compra'] ?? 0);
                    $precioVenta = (float) ($item['precio_venta'] ?? 0);
                    $porcentajeGanancia = (float) ($item['porcentaje_ganancia'] ?? 0);

                    if ($precioCompra > 0) {
                        $porcentajeGanancia = (($precioVenta - $precioCompra) / $precioCompra) * 100;
                    }

                    $detalle->producto_id = (int) $item['producto_id'];
                    $detalle->lote_id = (int) $item['lote_id'];
                    $detalle->precio_compra_unidad = $precioCompra;
                    $detalle->precio_venta_unidad = $precioVenta;
                    $detalle->porcentaje_ganancia_unidad = $porcentajeGanancia;
                    $detalle->cantidad = (int) $item['cantidad'];
                    $detalle->save();

                    $detallesMantener[] = $detalle->id;
                    $this->crearOActualizarInventario($item, $compra->sucursal_id, (int) $item['producto_id'], $compra->id);
                }

                if (empty($detallesMantener)) {
                    $detallesEliminar = $compra->detalles;
                } else {
                    $detallesEliminar = $compra->detalles->whereNotIn('id', $detallesMantener);
                }

                foreach ($detallesEliminar as $detalleEliminar) {
                    Inventario::where('compra_id', $compra->id)
                        ->where('producto_id', $detalleEliminar->producto_id)
                        ->when(
                            $detalleEliminar->lote_id === null,
                            fn ($query) => $query->whereNull('lote_id'),
                            fn ($query) => $query->where('lote_id', $detalleEliminar->lote_id)
                        )
                        ->delete();
                }

                if (empty($detallesMantener)) {
                    $compra->detalles()->delete();
                } else {
                    $compra->detalles()->whereNotIn('id', $detallesMantener)->delete();
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Compra registrada exitosamente',
                    'redirect' => route('admin.compras.index')
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la compra: ' . $e->getMessage()
            ], 500);
        }
    }

    private function crearOActualizarInventario(array $item, int $sucursalId, int $productoId, int $compraId)
    {
        $loteId = isset($item['lote_id']) && is_numeric($item['lote_id']) ? (int) $item['lote_id'] : null;
        $ubicacionFisicaId = isset($item['ubicacion_fisica_id']) && is_numeric($item['ubicacion_fisica_id']) ? (int) $item['ubicacion_fisica_id'] : null;
        $stockMinimo = isset($item['stock_minimo']) && $item['stock_minimo'] !== '' ? (int) $item['stock_minimo'] : 0;
        $stockMaximo = isset($item['stock_maximo']) && $item['stock_maximo'] !== '' ? (int) $item['stock_maximo'] : 0;
        $stockActual = isset($item['cantidad']) ? (int) $item['cantidad'] : 0;

        $inventario = Inventario::where([
            'lote_id' => $loteId,
            'sucursal_id' => $sucursalId,
            'producto_id' => $productoId,
            'compra_id' => $compraId,
        ])->latest('id')->first();

        if ($inventario) {
            $inventario->ubicacion_fisica_id = $ubicacionFisicaId;
            $inventario->precio_compra_unidad = (float) ($item['precio_compra'] ?? 0);
            $inventario->precio_venta_unidad = (float) ($item['precio_venta'] ?? 0);
            $inventario->stock_actual = $stockActual;
            $inventario->stock_minimo = $stockMinimo;
            $inventario->stock_maximo = $stockMaximo;
            $inventario->fecha_registro = Carbon::now()->toDateString();
            $inventario->estado = 'compra';
            $inventario->save();

            return;
        }

        Inventario::create([
            'compra_id' => $compraId,
            'lote_id' => $loteId,
            'ubicacion_fisica_id' => $ubicacionFisicaId,
            'sucursal_id' => $sucursalId,
            'producto_id' => $productoId,
            'precio_compra_unidad' => (float) ($item['precio_compra'] ?? 0),
            'precio_venta_unidad' => (float) ($item['precio_venta'] ?? 0),
            'stock_actual' => $stockActual,
            'stock_minimo' => $stockMinimo,
            'stock_maximo' => $stockMaximo,
            'fecha_registro' => Carbon::now()->toDateString(),
            'estado' => 'compra',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $compra = Compra::with([
            'sucursal',
            'proveedor',
            'usuario',
            'detalles.producto.categoria',
            'detalles.producto.laboratorio',
            'detalles.producto.formaFarmaceutica',
            'detalles.producto.presentacion',
            'detalles.lote',
        ])->findOrFail($id);

        $ubicaciones = UbicacionFisica::all()->keyBy('id');

        $detalles = $compra->detalles->map(function ($detalle) use ($compra, $ubicaciones) {
            $inventario = Inventario::where('producto_id', $detalle->producto_id)
                ->where('lote_id', $detalle->lote_id)
                ->where('sucursal_id', $compra->sucursal_id)
                ->where('compra_id', $compra->id)
                ->latest('id')
                ->first();

            $ubicacionFisicaId = $inventario?->ubicacion_fisica_id;
            $detalle->ubicacion_nombre = $ubicacionFisicaId ? ($ubicaciones->get($ubicacionFisicaId)?->nombre ?? 'N/A') : 'N/A';
            $detalle->stock_minimo = $inventario?->stock_minimo ?? 0;
            $detalle->stock_maximo = $inventario?->stock_maximo ?? 0;

            return $detalle;
        });

        $totalItems = $detalles->count();
        $totalCantidad = $detalles->sum('cantidad');
        $ajuste = Ajuste::query()->first();

        return view('admin.compras.show', compact('compra', 'detalles', 'totalItems', 'totalCantidad', 'ajuste'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $compra = Compra::with('detalles')->findOrFail($id);
        $loteIds = $compra->detalles
            ->pluck('lote_id')
            ->filter()
            ->unique()
            ->values();

        DB::transaction(function () use ($compra) {
            Inventario::where('compra_id', $compra->id)->delete();
            $compra->delete();
        });

        foreach ($loteIds as $loteId) {
            $tieneDetalles = CompraDetalle::where('lote_id', $loteId)->exists();
            $tieneInventario = Inventario::where('lote_id', $loteId)->exists();

            if (!$tieneDetalles && !$tieneInventario) {
                Lote::where('id', $loteId)->delete();
            }
        }

        return redirect()->route('admin.compras.index')
            ->with('success', 'Compra e inventario asociado eliminados exitosamente');
    }
}
