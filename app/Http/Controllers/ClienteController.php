<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $clientes = Cliente::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('ci_nit', 'like', '%' . $search . '%')
                        ->orWhere('nombres_apellidos', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('telefono', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nombres_apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('admin.clientes.index', compact('clientes', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ci_nit' => ['required', 'string', 'max:50', 'unique:clientes,ci_nit'],
            'nombres_apellidos' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.clientes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createClienteModal');
        }

        $cliente = new Cliente();
        $cliente->ci_nit = trim((string) $request->input('ci_nit'));
        $cliente->nombres_apellidos = trim((string) $request->input('nombres_apellidos'));
        $cliente->email = filled($request->input('email')) ? trim((string) $request->input('email')) : null;
        $cliente->telefono = filled($request->input('telefono')) ? trim((string) $request->input('telefono')) : null;
        $cliente->save();

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $cliente = Cliente::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'ci_nit' => ['required', 'string', 'max:50', 'unique:clientes,ci_nit,' . $cliente->id],
            'nombres_apellidos' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.clientes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editClienteModal-' . $cliente->id);
        }

        $cliente->ci_nit = trim((string) $request->input('ci_nit'));
        $cliente->nombres_apellidos = trim((string) $request->input('nombres_apellidos'));
        $cliente->email = filled($request->input('email')) ? trim((string) $request->input('email')) : null;
        $cliente->telefono = filled($request->input('telefono')) ? trim((string) $request->input('telefono')) : null;
        $cliente->save();

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $cliente = Cliente::query()->findOrFail($id);
        $cliente->delete();

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}