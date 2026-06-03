<?php

namespace App\Http\Controllers;

use App\Models\CompraDetalle;
use App\Models\Compra;
use App\Models\Inventario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompraDetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Agregar item a compra_detalles para una compra existente
     */
    public function addItem(Request $request, $compra_id)
    {
        try {
            Log::info('=== START addItem CompraDetalle ===', ['compra_id' => $compra_id, 'user_id' => Auth::id()]);
            Log::info('Request data:', $request->all());
            
            // Verificar que la compra exista y pertenezca al usuario
            $compra = Compra::where('id', $compra_id)
                ->where('usuario_id', Auth::id())
                ->first();
            
            if (!$compra) {
                Log::warning('Compra no encontrada o no pertenece al usuario');
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada o no tienes permiso'
                ], 404);
            }

            $validated = $request->validate([
                'producto_id' => 'required|exists:productos,id',
            ]);

            Log::info('Validated data:', $validated);

            // Verificar si el producto ya existe en la compra
            $existente = CompraDetalle::where('compra_id', $compra_id)
                ->where('producto_id', $validated['producto_id'])
                ->first();

            if ($existente) {
                Log::warning('Producto ya existe en la compra');
                return response()->json([
                    'success' => false,
                    'message' => 'Este producto ya está en la compra'
                ], 422);
            }

            $item = CompraDetalle::create([
                'compra_id' => $compra_id,
                'producto_id' => $validated['producto_id'],
                'cantidad' => 0,
                'precio_compra_unidad' => 0,
                'precio_venta_unidad' => 0,
                'porcentaje_ganancia_unidad' => 0,
            ]);

            $totalCompra = $this->actualizarTotalCompra($compra);

            Log::info('Item creado:', ['id' => $item->id, 'item' => $item->toArray()]);

            return response()->json([
                'success' => true,
                'item_id' => $item->id,
                'total' => $totalCompra,
                'message' => 'Producto agregado'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ValidationException en addItem:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error en validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Exception en addItem:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar item en compra_detalles
     */
    public function updateItem(Request $request, $compra_id, $item_id)
    {
        try {
            Log::info('=== START updateItem CompraDetalle ===', ['compra_id' => $compra_id, 'itemId' => $item_id, 'user_id' => Auth::id()]);
            Log::info('Request data:', $request->all());
            
            // Verificar que la compra exista y pertenezca al usuario
            $compra = Compra::where('id', $compra_id)
                ->where('usuario_id', Auth::id())
                ->first();
            
            if (!$compra) {
                Log::warning('Compra no encontrada o no pertenece al usuario');
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada o no tienes permiso'
                ], 404);
            }

            $validated = $request->validate([
                'cantidad' => 'nullable|numeric|min:0',
                'precio_compra_unidad' => 'nullable|numeric|min:0',
                'precio_venta_unidad' => 'nullable|numeric|min:0',
                'porcentaje_ganancia_unidad' => 'nullable|numeric',
                'lote_id' => 'nullable|exists:lotes,id',
                'ubicacion_fisica_id' => 'nullable|exists:ubicacion_fisicas,id',
            ]);

            Log::info('Validated data:', $validated);

            $item = CompraDetalle::where('id', $item_id)
                ->where('compra_id', $compra_id)
                ->first();
            
            if (!$item) {
                Log::warning('Item no encontrado:', ['itemId' => $item_id, 'compra_id' => $compra_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            $item->update($validated);

            if (!empty($validated['ubicacion_fisica_id']) && !empty($validated['lote_id'])) {
                $inventario = Inventario::where([
                    'lote_id' => (int) $validated['lote_id'],
                    'sucursal_id' => $compra->sucursal_id,
                    'producto_id' => $item->producto_id,
                        'compra_id' => $compra->id,
                    ])->latest('id')->first();

                    if ($inventario) {
                        $inventario->ubicacion_fisica_id = (int) $validated['ubicacion_fisica_id'];
                        $inventario->precio_compra_unidad = (float) ($validated['precio_compra_unidad'] ?? $item->precio_compra_unidad);
                        $inventario->precio_venta_unidad = (float) ($validated['precio_venta_unidad'] ?? $item->precio_venta_unidad);
                        $inventario->stock_actual = (int) ($validated['cantidad'] ?? $item->cantidad);
                        $inventario->fecha_registro = Carbon::now()->toDateString();
                        $inventario->estado = 'compra';
                        $inventario->save();
                    } else {
                        Inventario::create([
                            'lote_id' => (int) $validated['lote_id'],
                            'ubicacion_fisica_id' => (int) $validated['ubicacion_fisica_id'],
                            'sucursal_id' => $compra->sucursal_id,
                            'producto_id' => $item->producto_id,
                            'compra_id' => $compra->id,
                            'precio_compra_unidad' => (float) ($validated['precio_compra_unidad'] ?? $item->precio_compra_unidad),
                            'precio_venta_unidad' => (float) ($validated['precio_venta_unidad'] ?? $item->precio_venta_unidad),
                            'stock_actual' => (int) ($validated['cantidad'] ?? $item->cantidad),
                            'stock_minimo' => 0,
                            'stock_maximo' => 0,
                            'fecha_registro' => Carbon::now()->toDateString(),
                            'estado' => 'compra',
                    ]);
                }
            }

            $totalCompra = $this->actualizarTotalCompra($compra);
            Log::info('Item actualizado:', $item->toArray());

            return response()->json([
                'success' => true,
                'total' => $totalCompra,
                'message' => 'Producto actualizado'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ValidationException en updateItem:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error en validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Exception en updateItem:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar item de compra_detalles
     */
    public function removeItem($compra_id, $item_id)
    {
        try {
            Log::info('RemoveItem CompraDetalle llamado:', ['compra_id' => $compra_id, 'itemId' => $item_id, 'usuario' => Auth::id()]);
            
            // Verificar que la compra exista y pertenezca al usuario
            $compra = Compra::where('id', $compra_id)
                ->where('usuario_id', Auth::id())
                ->first();
            
            if (!$compra) {
                Log::warning('Compra no encontrada o no pertenece al usuario');
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada o no tienes permiso'
                ], 404);
            }

            $item = CompraDetalle::where('id', $item_id)
                ->where('compra_id', $compra_id)
                ->first();
            
            if (!$item) {
                Log::warning('Item no encontrado en compra');
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado en esta compra',
                ], 404);
            }

            $item->delete();

            Inventario::where('compra_id', $compra_id)
                ->where('producto_id', $item->producto_id)
                ->when(
                    $item->lote_id === null,
                    fn ($query) => $query->whereNull('lote_id'),
                    fn ($query) => $query->where('lote_id', $item->lote_id)
                )
                ->delete();

            $totalCompra = $this->actualizarTotalCompra($compra);

            return response()->json([
                'success' => true,
                'total' => $totalCompra,
                'message' => 'Producto eliminado'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en removeItem CompraDetalle: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar múltiples items de compra_detalles
     */
    public function clearItems(Request $request, $compra_id)
    {
        try {
            // Verificar que la compra exista y pertenezca al usuario
            $compra = Compra::where('id', $compra_id)
                ->where('usuario_id', Auth::id())
                ->first();
            
            if (!$compra) {
                Log::warning('Compra no encontrada o no pertenece al usuario');
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada o no tienes permiso'
                ], 404);
            }

            $validated = $request->validate([
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'integer|min:1',
            ]);

            $deleted = CompraDetalle::where('compra_id', $compra_id)
                ->whereIn('id', $validated['item_ids'])
                ->delete();

            $totalCompra = $this->actualizarTotalCompra($compra);

            return response()->json([
                'success' => true,
                'message' => 'Carrito limpiado correctamente',
                'deleted_count' => $deleted,
                'total' => $totalCompra,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos para limpiar carrito',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en clearItems CompraDetalle: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar carrito: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CompraDetalle $compraDetalle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompraDetalle $compraDetalle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompraDetalle $compraDetalle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompraDetalle $compraDetalle)
    {
        //
    }

    private function actualizarTotalCompra(Compra $compra): float
    {
        $total = (float) CompraDetalle::where('compra_id', $compra->id)
            ->get()
            ->sum(function ($detalle) {
                return ((float) $detalle->cantidad) * ((float) $detalle->precio_compra_unidad);
            });

        $compra->total = $total;
        $compra->save();

        return $total;
    }
}
