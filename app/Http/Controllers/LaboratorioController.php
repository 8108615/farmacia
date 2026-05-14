<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use Illuminate\Http\Request;

class LaboratorioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $laboratorios = Laboratorio::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.laboratorios.index', compact('laboratorios', 'search'));
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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nombre' => 'required|string|max:150|unique:laboratorios,nombre',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.laboratorios.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createLaboratorioModal');
        }

        $laboratorio = new Laboratorio();
        $laboratorio->nombre = mb_strtoupper($request->nombre);
        $laboratorio->save();

        return redirect()->route('admin.laboratorios.index')->with('success', 'Laboratorio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Laboratorio $laboratorio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laboratorio $laboratorio, Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laboratorio $laboratorio, string $id)
    {
        $laboratorio = Laboratorio::query()->findOrFail($id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nombre' => 'required|string|max:150|unique:laboratorios,nombre,' . $laboratorio->id,
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.laboratorios.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editLaboratorioModal-' . $laboratorio->id);
        }

        $laboratorio->nombre = mb_strtoupper($request->nombre);
        $laboratorio->save();

        return redirect()->route('admin.laboratorios.index')->with('success', 'Laboratorio actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laboratorio $laboratorio, string $id)
    {
        $laboratorio = Laboratorio::query()->findOrFail($id);
        $laboratorio->delete();

        return redirect()->route('admin.laboratorios.index')->with('success', 'Laboratorio eliminado exitosamente.');
    }
}
