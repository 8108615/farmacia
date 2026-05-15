<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $clientes = Cliente::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombres_apellidos', 'like', '%' . $search . '%')
                      ->orWhere('ci_nit', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('telefono', 'like', '%' . $search . '%');
            })
            ->orderBy('nombres_apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('admin.clientes.index', compact('clientes', 'search'));
    }

    public function create()
    {
        // modal based CRUD, not used
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ci_nit' => ['required', 'string', 'max:50', 'unique:clientes,ci_nit'],
            'nombres_apellidos' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.clientes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createClienteModal');
        }

        Cliente::query()->create([
            'ci_nit' => trim((string) $request->input('ci_nit')),
            'nombres_apellidos' => trim((string) $request->input('nombres_apellidos')),
            'email' => $request->input('email'),
            'telefono' => $request->input('telefono'),
        ]);

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        // modal based
    }

    public function edit(Cliente $cliente)
    {
        // modal based
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validator = Validator::make($request->all(), [
            'ci_nit' => ['required', 'string', 'max:50', 'unique:clientes,ci_nit,' . $cliente->id],
            'nombres_apellidos' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.clientes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editClienteModal-' . $cliente->id);
        }

        $cliente->ci_nit = $request->input('ci_nit');
        $cliente->nombres_apellidos = $request->input('nombres_apellidos');
        $cliente->email = $request->input('email');
        $cliente->telefono = $request->input('telefono');

        $cliente->save();

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
