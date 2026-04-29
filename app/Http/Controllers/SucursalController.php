<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SucursalController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $sucursales = Sucursal::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nombre', 'like', '%' . $search . '%')
                        ->orWhere('direccion', 'like', '%' . $search . '%')
                        ->orWhere('telefono', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.sucursales.index', compact('sucursales', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'estado' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.sucursales.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createSucursalModal');
        }

        Sucursal::query()->create([
            'nombre' => trim((string) $request->input('nombre')),
            'direccion' => trim((string) $request->input('direccion')),
            'telefono' => filled($request->input('telefono')) ? trim((string) $request->input('telefono')) : null,
            'estado' => (bool) $request->boolean('estado'),
        ]);

        return redirect()
            ->route('admin.sucursales.index')
            ->with('success', 'Sucursal creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $sucursal = Sucursal::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'estado' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.sucursales.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editSucursalModal-' . $sucursal->id);
        }

        $sucursal->update([
            'nombre' => trim((string) $request->input('nombre')),
            'direccion' => trim((string) $request->input('direccion')),
            'telefono' => filled($request->input('telefono')) ? trim((string) $request->input('telefono')) : null,
            'estado' => (bool) $request->boolean('estado'),
        ]);

        return redirect()
            ->route('admin.sucursales.index')
            ->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $sucursal = Sucursal::query()->findOrFail($id);
        $sucursal->delete();

        return redirect()
            ->route('admin.sucursales.index')
            ->with('success', 'Sucursal eliminada correctamente.');
    }
}
