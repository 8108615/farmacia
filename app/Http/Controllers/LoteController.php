<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $lotes = Lote::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.lotes.index', compact('lotes', 'search'));
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
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:150', 'unique:lotes,nombre'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.lotes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createLoteModal');
        }

        Lote::query()->create([
            'nombre' => trim((string) $request->input('nombre')),
        ]);

        return redirect()
            ->route('admin.lotes.index')
            ->with('success', 'Lote creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lote $lote)
    {
        // not used (modals in index)
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lote $lote)
    {
        // not used (modals in index)
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lote $lote)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:150', 'unique:lotes,nombre,' . $lote->id],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.lotes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editLoteModal-' . $lote->id);
        }

        $lote->update([
            'nombre' => trim((string) $request->input('nombre')),
        ]);

        return redirect()
            ->route('admin.lotes.index')
            ->with('success', 'Lote actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lote $lote)
    {
        $lote->delete();

        return redirect()
            ->route('admin.lotes.index')
            ->with('success', 'Lote eliminado correctamente.');
    }
}
