<?php

namespace App\Http\Controllers;

use App\Models\FormaFarmaceutica;
use Illuminate\Http\Request;

class FormaFarmaceuticaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $formaFarmaceuticas = FormaFarmaceutica::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.forma_farmaceuticas.index', compact('formaFarmaceuticas', 'search'));
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
            'nombre' => 'required|string|max:150|unique:forma_farmaceuticas,nombre',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.forma_farmaceuticas.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createFormaFarmaceuticaModal');
        }

        $formaFarmaceutica = new FormaFarmaceutica();
        $formaFarmaceutica->nombre = mb_strtoupper(trim((string) $request->input('nombre')));
        $formaFarmaceutica->save();

        return redirect()->route('admin.forma_farmaceuticas.index')->with('success', 'Forma farmacéutica creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FormaFarmaceutica $formaFarmaceutica)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FormaFarmaceutica $formaFarmaceutica)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $formaFarmaceutica = FormaFarmaceutica::query()->findOrFail($id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nombre' => 'required|string|max:150|unique:forma_farmaceuticas,nombre,' . $formaFarmaceutica->id,
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.forma_farmaceuticas.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editFormaFarmaceuticaModal-' . $formaFarmaceutica->id);
        }

        $formaFarmaceutica->nombre = mb_strtoupper(trim((string) $request->input('nombre')));
        $formaFarmaceutica->save();

        return redirect()->route('admin.forma_farmaceuticas.index')->with('success', 'Forma farmacéutica actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $formaFarmaceutica = FormaFarmaceutica::query()->findOrFail($id);
        $formaFarmaceutica->delete();

        return redirect()->route('admin.forma_farmaceuticas.index')->with('success', 'Forma farmacéutica eliminada correctamente.');
    }
}
