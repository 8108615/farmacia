<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Sucursal;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\CompraTmp;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompraTmpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ajuste = Ajuste::query()->first();
        $compras = Compra::with(['sucursal', 'proveedor', 'usuario'])->where('estado', 'pendiente')->paginate(10);
        return view('admin.ordenes_compra.index', compact('compras', 'ajuste'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sucursales = Sucursal::all();
        $proveedores = Proveedor::all();
        $productos = Producto::with(['categoria', 'laboratorio', 'formaFarmaceutica', 'presentacion'])->get();
        $ajuste = Ajuste::query()->first();
        return view('admin.ordenes_compra.create', compact('sucursales', 'proveedores', 'productos', 'ajuste'));
    }

    /**
     * Agregar item a compra_tmps en tiempo real
     */
    public function addItems(Request $request)
    {
        try {
            Log::info('=== START addItems ===');
            Log::info('Usuario autenticado:', ['user_id' => Auth::id(), 'user' => Auth::user()]);
            Log::info('Request data:', $request->all());
            
            $validated = $request->validate([
                'sucursal_id' => 'required|exists:sucursals,id',
                'producto_id' => 'required|exists:productos,id',
            ]);

            Log::info('Validated data:', $validated);

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
                'item_id' => $item->id,
                'message' => 'Producto agregado'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ValidationException en addItems:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error en validación',
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
                'message' => 'Error al agregar producto: ' . $e->getMessage()
            ], 500);
        }
    }

     /**
     * Actualizar item en compra_tmps
     */
    public function updateItem(Request $request, $itemId)
    {
        try {
            Log::info('=== START updateItem ===', ['itemId' => $itemId, 'user_id' => Auth::id()]);
            Log::info('Request data:', $request->all());
            
            $validated = $request->validate([
                'cantidad' => 'nullable|numeric|min:0',
                'precio_compra_unidad' => 'nullable|numeric|min:0',
                'precio_venta_unidad' => 'nullable|numeric|min:0',
                'porcentaje_ganancia_unidad' => 'nullable|numeric',
            ]);

            Log::info('Validated data:', $validated);

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
     * Eliminar item de compra_tmps
     */
    public function removeItem($itemId)
    {
        try {
            Log::info('RemoveItem llamado con itemId: ' . $itemId . ', usuario: ' . Auth::id());
            
            // Verificar si el registro existe
            $existsAny = CompraTmp::where('id', $itemId)->first();
            if (!$existsAny) {
                Log::warning('Item no encontrado con id: ' . $itemId);
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado en la base de datos',
                    'debug' => [
                        'itemId_buscado' => $itemId,
                        'usuario_actual' => Auth::id()
                    ]
                ], 404);
            }

            $item = CompraTmp::where('id', $itemId)
                ->where('usuario_id', Auth::id())
                ->first();
            
            if (!$item) {
                Log::warning('Item encontrado pero no pertenece al usuario. ItemId: ' . $itemId . ', Usuario: ' . Auth::id());
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este producto',
                ], 403);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en removeItem: ' . $e->getMessage() . ' | ItemId: ' . $itemId);
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage(),
                'debug' => [
                    'exception' => get_class($e),
                    'itemId' => $itemId
                ]
            ], 500);
        }
    }

    /**
     * Eliminar múltiples items de compra_tmps
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
                'message' => 'Carrito limpiado correctamente',
                'deleted_count' => $deleted,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos para limpiar carrito',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en clearItems: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar carrito: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // ===== VALIDAR DATOS =====
            $validated = $request->validate([
                'sucursal_id' => 'required|exists:sucursals,id',
                'proveedor_id' => 'required|exists:proveedores,id',
                'carrito' => 'required|json',
            ]);

            // ===== DECODIFICAR CARRITO =====
            $carrito = json_decode($request->input('carrito'), true) ?? [];

            if (empty($carrito)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El carrito está vacío'
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

            // ===== INICIAR TRANSACCIÓN =====
            DB::beginTransaction();

            try {
                $usuarioId = Auth::id();
                $sucursalId = $validated['sucursal_id'];
                $proveedorId = $validated['proveedor_id'];
                $ahora = Carbon::now();

                $totalCompra = $itemsValidos->sum(function ($item) {
                    return ((float) ($item['cantidad'] ?? 0)) * ((float) ($item['precio_compra'] ?? 0));
                });

                // ===== CREAR CABECERA DE COMPRA =====
                $compra = new Compra();
                $compra->sucursal_id = $sucursalId;
                $compra->proveedor_id = $proveedorId;
                $compra->usuario_id = $usuarioId;
                $compra->fecha_compra = $ahora->toDateString();
                $compra->total = $totalCompra;
                $compra->estado = 'pendiente';
                $compra->comprobante = null;
                $compra->nota = null;
                $compra->save();

                $compraId = $compra->id;

                // ===== CREAR DETALLES DE COMPRA =====
                foreach ($itemsValidos as $item) {
                    $productoId = (int) $item['producto_id'];

                    $detalle = new CompraDetalle();
                    $detalle->compra_id = $compraId;
                    $detalle->producto_id = $productoId;
                    $detalle->lote_id = isset($item['lote_id']) && is_numeric($item['lote_id']) ? (int) $item['lote_id'] : null;
                    $detalle->precio_compra_unidad = (float) ($item['precio_compra'] ?? 0);
                    $detalle->precio_venta_unidad = (float) ($item['precio_venta'] ?? 0);
                    $detalle->porcentaje_ganancia_unidad = (float) ($item['porcentaje_ganancia'] ?? 0);
                    $detalle->cantidad = (int) ($item['cantidad'] ?? 0);
                    $detalle->save();
                }

                // ===== LIMPIAR TEMPORALES USADOS =====
                $idsTmp = collect($carrito)
                    ->pluck('id')
                    ->filter()
                    ->map(fn($id) => (int) $id)
                    ->values()
                    ->all();

                if (!empty($idsTmp)) {
                    CompraTmp::where('usuario_id', $usuarioId)
                        ->whereIn('id', $idsTmp)
                        ->delete();
                }

                // ===== COMMIT TRANSACCIÓN =====
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Compra registrada exitosamente',
                    'compra_id' => $compraId,
                    'redirect' => route('admin.ordenes_compra.index')
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
            Log::error('Error en CompraTmpController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
        ])->where('usuario_id', Auth::id())->findOrFail($id);
        $detalles = $compra->detalles->sortBy('id')->values();

        $totalItems = $detalles->sum('cantidad');
        $totalCantidad = $detalles->sum('cantidad');
        
        $ajuste = Ajuste::query()->first();
        return view('admin.ordenes_compra.show', compact('compra', 'detalles', 'totalItems', 'totalCantidad', 'ajuste'));
    }

    /**
     * Send the purchase order by email.
     */
    public function sendEmail(string $id)
    {
        $compra = Compra::with(['sucursal', 'proveedor', 'usuario', 'detalles.producto'])->findOrFail($id);

        if (empty($compra->proveedor->email)) {
            return redirect()->back()->with('error', 'El proveedor no tiene un email configurado.');
        }

        $ajuste = Ajuste::query()->first();

        try {
            $subject = "Orden de compra #{$compra->id}";

            Mail::send('emails.orden_compra', compact('compra', 'ajuste'), function ($message) use ($compra, $subject) {
                $message->to($compra->proveedor->email, $compra->proveedor->nombre ?? null)
                    ->subject($subject);
            });

            return redirect()->back()->with('success', 'Orden de compra enviada por correo exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al enviar orden de compra por email', ['error' => $e->getMessage(), 'compra_id' => $compra->id]);
            return redirect()->back()->with('error', 'Ocurrió un error al enviar el correo: ' . $e->getMessage());
        }
    }

    /**
     * Send the purchase order by WhatsApp.
     */
    public function sendWhatsapp(string $id)
    {
        $compra = Compra::with(['sucursal', 'proveedor', 'usuario', 'detalles.producto'])->findOrFail($id);

        $telefono = $compra->proveedor->telefono;
        if (empty($telefono)) {
            return redirect()->back()->with('error', 'El proveedor no tiene teléfono configurado para WhatsApp.');
        }

        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
        if (empty($telefonoLimpio)) {
            return redirect()->back()->with('error', 'El teléfono del proveedor no es válido para WhatsApp.');
        }

        $ajuste = Ajuste::query()->first();
        $lineas = $compra->detalles->map(function ($detalle) use ($ajuste) {
            $producto = $detalle->producto;
            $nombre = $producto?->nombre_comercial ?? 'Producto';
            $codigo = $producto?->codigo_producto ?? 'N/A';
            $subtotal = (float) $detalle->cantidad * (float) $detalle->precio_compra_unidad;

            return "- {$nombre} (Código: {$codigo}) x {$detalle->cantidad} | Precio compra: {$ajuste->divisa} " . number_format((float) $detalle->precio_compra_unidad, 2, '.', ',') . " | Subtotal: {$ajuste->divisa} " . number_format($subtotal, 2, '.', ',');
        })->implode("\n");

        $mensaje = "Orden de compra #{$compra->id}\n";
        $mensaje .= "Proveedor: {$compra->proveedor->nombre}\n";
        $mensaje .= "Sucursal: {$compra->sucursal->nombre}\n";
        $mensaje .= "Usuario: {$compra->usuario->name}\n";
        $mensaje .= "Fecha: {$compra->fecha_compra->format('d/m/Y')}\n\n";
        $mensaje .= "Detalle de productos:\n{$lineas}\n\n";
        $mensaje .= "Total: {$ajuste->divisa} " . number_format((float) $compra->total, 2, '.', ',') . "\n";

        if (!empty($compra->nota)) {
            $mensaje .= "Nota: {$compra->nota}\n";
        }

        $url = 'https://wa.me/' . $telefonoLimpio . '?text=' . rawurlencode($mensaje);
        return redirect()->away($url);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $compra = Compra::with(['detalles.producto'])
            ->where('usuario_id', Auth::id())
            ->findOrFail($id);

        $sucursales = Sucursal::all();
        $proveedores = Proveedor::all();
        $productos = Producto::with(['categoria', 'laboratorio', 'formaFarmaceutica', 'presentacion'])->get();

        $carritoInicial = $compra->detalles->map(function ($detalle) {
            $producto = $detalle->producto;

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
                'subtotal' => (float) $detalle->cantidad * (float) $detalle->precio_compra_unidad,
            ];
        })->values();

        $ajuste = Ajuste::query()->first();
        return view('admin.ordenes_compra.edit', compact(
            'compra',
            'sucursales',
            'proveedores',
            'productos',
            'carritoInicial',
            'ajuste'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'sucursal_id' => 'required|exists:sucursals,id',
                'proveedor_id' => 'required|exists:proveedores,id',
                'carrito' => 'required|json',
            ]);

            $carrito = json_decode($request->input('carrito'), true) ?? [];

            if (empty($carrito)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El carrito está vacío'
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

            DB::beginTransaction();

            try {
                    $compra = Compra::with('detalles')
                        ->where('usuario_id', Auth::id())
                        ->findOrFail($id);

                $totalCompra = $itemsValidos->sum(function ($item) {
                    return ((float) ($item['cantidad'] ?? 0)) * ((float) ($item['precio_compra'] ?? 0));
                });

                $compra->sucursal_id = $validated['sucursal_id'];
                $compra->proveedor_id = $validated['proveedor_id'];
                $compra->total = $totalCompra;
                $compra->save();

                $detallesExistentes = $compra->detalles->keyBy('id');
                $detallesMantener = [];

                foreach ($itemsValidos as $item) {
                    $detalleId = isset($item['id']) && is_numeric($item['id']) ? (int) $item['id'] : null;

                    $precioCompra = (float) ($item['precio_compra'] ?? 0);
                    $precioVenta = (float) ($item['precio_venta'] ?? 0);
                    $porcentajeGanancia = (float) ($item['porcentaje_ganancia'] ?? 0);

                    if ($precioCompra > 0) {
                        $porcentajeGanancia = (($precioVenta - $precioCompra) / $precioCompra) * 100;
                    }

                    if ($detalleId && $detallesExistentes->has($detalleId)) {
                        $detalle = $detallesExistentes->get($detalleId);
                    } else {
                        $detalle = new CompraDetalle();
                        $detalle->compra_id = $compra->id;
                    }

                    $detalle->producto_id = (int) $item['producto_id'];
                    $detalle->lote_id = isset($item['lote_id']) && is_numeric($item['lote_id']) ? (int) $item['lote_id'] : null;
                    $detalle->precio_compra_unidad = $precioCompra;
                    $detalle->precio_venta_unidad = $precioVenta;
                    $detalle->porcentaje_ganancia_unidad = $porcentajeGanancia;
                    $detalle->cantidad = (int) $item['cantidad'];
                    $detalle->save();

                    $detallesMantener[] = $detalle->id;
                }

                if (empty($detallesMantener)) {
                    $compra->detalles()->delete();
                } else {
                    $compra->detalles()->whereNotIn('id', $detallesMantener)->delete();
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Compra actualizada exitosamente',
                    'compra_id' => $compra->id,
                    'redirect' => route('admin.ordenes_compra.index')
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
            Log::error('Error en CompraTmpController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $compra = Compra::where('usuario_id', Auth::id())->findOrFail($id);
       $compra->delete();

       return redirect()->route('admin.ordenes_compra.index')
       ->with('success', 'Orden de compra eliminada exitosamente');
    }
}
