<?php

namespace App\Http\Controllers;

use App\Models\Presentacion;
use Illuminate\Http\Request;

class PresentacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $presentaciones = Presentacion::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.presentaciones.index', compact('presentaciones', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // no usado (se manejan modales en la index)
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nombre' => 'required|string|max:150|unique:presentacions,nombre',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.presentaciones.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createPresentacionModal');
        }

        $presentacion = new Presentacion();
        $presentacion->nombre = mb_strtoupper(trim((string) $request->input('nombre')));
        $presentacion->save();


        return redirect()->route('admin.presentaciones.index')->with('success', 'Presentación creada correctamente.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Presentacion $presentacion)
    {
        // no usado
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presentacion $presentacion)
    {
        // no usado (se manejan modales en la index)
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $presentacion = Presentacion::query()->findOrFail($id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nombre' => 'required|string|max:150|unique:presentacions,nombre,' . $presentacion->id,
        ]);
        if ($validator->fails()) {
            return redirect()
                ->route('admin.presentaciones.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editPresentacionModal-' . $presentacion->id);
        }

        $presentacion->nombre = mb_strtoupper(trim((string) $request->input('nombre')));
        $presentacion->save();

        return redirect()->route('admin.presentaciones.index')->with('success', 'Presentación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $presentacion = Presentacion::query()->findOrFail($id);
        $presentacion->delete();

        return redirect()
            ->route('admin.presentaciones.index')
            ->with('success', 'Presentación eliminada correctamente.');
    }
}

