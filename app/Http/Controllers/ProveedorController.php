<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $proveedores = Proveedor::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nombre', 'like', '%' . $search . '%')
                        ->orWhere('telefono', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('empresa', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.proveedores.index', compact('proveedores', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'notas' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.proveedores.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createProveedorModal');
        }

        $proveedor = new Proveedor();
        $proveedor->nombre = trim((string) $request->input('nombre'));
        $proveedor->telefono = trim((string) $request->input('telefono'));
        $proveedor->email = filled($request->input('email')) ? trim((string) $request->input('email')) : null;
        $proveedor->direccion = filled($request->input('direccion')) ? trim((string) $request->input('direccion')) : null;
        $proveedor->empresa = filled($request->input('empresa')) ? trim((string) $request->input('empresa')) : null;
        $proveedor->notas = filled($request->input('notas')) ? trim((string) $request->input('notas')) : null;
        $proveedor->save();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'notas' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.proveedores.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editProveedorModal-' . $proveedor->id);
        }

        $proveedor->nombre = trim((string) $request->input('nombre'));
        $proveedor->telefono = trim((string) $request->input('telefono'));
        $proveedor->email = filled($request->input('email')) ? trim((string) $request->input('email')) : null;
        $proveedor->direccion = filled($request->input('direccion')) ? trim((string) $request->input('direccion')) : null;
        $proveedor->empresa = filled($request->input('empresa')) ? trim((string) $request->input('empresa')) : null;
        $proveedor->notas = filled($request->input('notas')) ? trim((string) $request->input('notas')) : null;
        $proveedor->save();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $proveedor = Proveedor::query()->findOrFail($id);
        $proveedor->delete();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
