<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $proveedores = Proveedor::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('telefono', 'like', '%' . $search . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.proveedores.index', compact('proveedores', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // not used (modals in index)
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'direccion' => ['nullable', 'string'],
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

        Proveedor::query()->create([
            'nombre' => trim((string) $request->input('nombre')),
            'telefono' => $request->input('telefono'),
            'email' => $request->input('email'),
            'direccion' => $request->input('direccion'),
            'empresa' => $request->input('empresa', $request->input('contacto')),
            'notas' => $request->input('notas'),
        ]);

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor)
    {
        // not used in modal CRUD
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        // not used (modals in index)
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'direccion' => ['nullable', 'string'],
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

        $proveedor->nombre = $request->input('nombre');
        $proveedor->telefono = $request->input('telefono');
        $proveedor->email = $request->input('email');
        $proveedor->direccion = $request->input('direccion');
        $proveedor->empresa = $request->input('empresa', $request->input('contacto'));
        $proveedor->notas = $request->input('notas');

        $proveedor->save();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
