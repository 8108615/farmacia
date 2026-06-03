<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LoteController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $lotes = Lote::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('numero_lote', 'like', '%' . $search . '%');
            })
            ->orderBy('numero_lote')
            ->paginate(10)
            ->withQueryString();

        $productos = Producto::orderBy('nombre_comercial')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('admin.lotes.index', compact('lotes', 'search', 'productos', 'proveedores'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return redirect()
                ->route('admin.lotes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createLoteModal');
        }

        $lote = new Lote();
        $lote->producto_id = $request->input('producto_id');
        $lote->proveedor_id = $request->input('proveedor_id');
        $lote->nombre = trim((string) $request->input('nombre'));
        $lote->fecha_vencimiento = $request->input('fecha_vencimiento');
        $lote->fecha_fabricacion = $request->input('fecha_fabricacion');
        $lote->save();

        return redirect()
            ->route('admin.lotes.index')
            ->with('success', 'Lote creado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $lote = Lote::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'producto_id' => ['required', 'integer', 'exists:productos,id'],
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('lotes', 'numero_lote')->ignore($lote->id)->where(function ($query) use ($request) {
                    return $query->where('producto_id', $request->input('producto_id'));
                }),
            ],
            'fecha_vencimiento' => ['nullable', 'date'],
            'fecha_fabricacion' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la validación',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()
                ->route('admin.lotes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editLoteModal-' . $lote->id);
        }

        $lote->producto_id = $request->input('producto_id');
        $lote->proveedor_id = $request->input('proveedor_id');
        $lote->nombre = trim((string) $request->input('nombre'));
        $lote->fecha_vencimiento = $request->input('fecha_vencimiento');
        $lote->fecha_fabricacion = $request->input('fecha_fabricacion');
        $lote->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lote actualizado correctamente.',
                'lote_id' => $lote->id,
                'lote_nombre' => $lote->nombre,
            ], 200);
        }

        return redirect()
            ->route('admin.lotes.index')
            ->with('success', 'Lote actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $lote = Lote::query()->findOrFail($id);
        $lote->delete();

        return redirect()
            ->route('admin.lotes.index')
            ->with('success', 'Lote eliminado correctamente.');
    }
}
