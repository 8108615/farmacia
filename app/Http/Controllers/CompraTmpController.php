<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraTmp;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompraTmpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compras = Compra::paginate(10);

        return view('admin.ordenes_compra.index', compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sucusales = Sucursal::all();
        $proveedores = Proveedor::all();
        $productos = Producto::with(['categoria', 'laboratorio', 'formaFarmaceutica', 'presentacion'])->get();
        return view('admin.ordenes_compra.create', compact('sucusales', 'proveedores', 'productos'));
    }

    /**
     * Agregar Item a compra_tmps en tiempo real
     */

    public function addItems(Request $request)
    {
        try {
            Log::info('=== START addItems ===');
            Log::info('Usuario autenticado: ', ['user_id' => Auth::id(), 'user' => Auth::user()]);
            Log::info('Request data: ', $request->all());

            $validated = $request->validate([
                'sucursal_id' => 'required|exists:sucursals,id',
                'producto_id' => 'required|exists:productos,id',
            ]);

            Log::info('Validated data: ', $validated);

            $item = CompraTmp::create([
                'usuario_id' => Auth::id(),
                'sucursal_id' => $validated['sucursal_id'],
                'producto_id' => $validated['producto_id'],
                'precio_compra_unidad' => 0,
                'precio_venta_unidad' => 0,
                'porcentaje_ganancia_unidad' => 0,
                'cantidad' => 0,
                'fecha_creacion' => Carbon::now(),
                'estado' => 'activo',
            ]);

            Log::info('Item creado:', ['id' => $item->id, 'item' => $item->toArray()]);

            return response()->json([
                'success' => true,
                'item' => $item,
                'message' => 'Producto agregado'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ValidationException en addItems:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Exception en addItems:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
                ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar el producto' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar item en compra_tmps.
     */

    public function updateItem(Request $request, $itemId)
    {
        try {
            Log::info('=== START updateItem ===', ['itemId' => $itemId, 'user_id' => Auth::id()]);
            Log::info('Request data: ', $request->all());

            $validated = $request->validate([
                'cantidad' => 'nullable|numeric|min:0',
                'precio_compra_unidad' => 'nullable|numeric|min:0',
                'precio_venta_unidad' => 'nullable|numeric|min:0',
                'porcentaje_ganancia_unidad' => 'nullable|numeric|min:0',
            ]);
            Log::info('Validated data: ', $validated);

            $item = CompraTmp::where('id', $itemId)
                ->where('usuario_id', Auth::id())
                ->first();

            if (!$item) {
                Log::warning('Item no encontrado:', ['itemId' => $itemId, 'user_id' => Auth::id()]);

                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado o no tienes permiso'
                ], 404);
            }

            $item->update($validated);
            Log::info('Item actualizado:', $item->toArray());

            return response()->json([
                'success' => true,
                'item' => $item,
                'message' => 'Producto actualizado'
            ], 200);
        }catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ValidationException en updateItem:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
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
                'message' => 'Error al actualizar el producto' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar item de compra_tmps.
     */

    public function removeItem($itemId)
    {
        try {
            Log::info('RemoveItem llamado con itemId: ' . $itemId . ', user_id: ' . Auth::id());

            //Verificar si el registro existe

            $existsAny = CompraTmp::where('id', $itemId)->first();

            if (!$existsAny) {
                Log::warning('Item no encontrado con id:' . $itemId);

                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado en la base de datos',
                    'debug' => [
                        'itemId_buscado' => $itemId,
                        'usuario_actual' => Auth::id(),
                    ]
                ], 404);
            }

            $item = CompraTmp::where('id', $itemId)
                ->where('usuario_id', Auth::id())
                ->first();

            if (!$item) {
                Log::warning('Item encontrado pero no pertenece al usuario.  ItenId: ' . $itemId . 'Usuario: ' . Auth::id());

                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar este producto',
                ], 403);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en removeItem:' . $e->getMessage() . '| ItemId: ' . $itemId);
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto' . $e->getMessage(),
                'debug ' => [
                    'exception' => get_class($e),
                    'itemId' => $itemId,
                ]
            ], 500);
        }
    }

    /**
     * Eliminar multiples items de compra_tmps.
     */

    public function clearItems(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'integer|min:1',
            ]);

            $deleted = CompraTmp::where('usuario_id', Auth::id())
                ->whereIn('id', $validated['item_ids'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Carrito limpiado correctamente",
                'deleted_count' => $deleted,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos invalidos para limpiar el carrito',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en clearItems:' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar el carrito' . $e->getMessage(),
            ], 500);
        }
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
    public function show(CompraTmp $compraTmp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompraTmp $compraTmp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompraTmp $compraTmp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompraTmp $compraTmp)
    {
        //
    }
}
