<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $categorias = Categoria::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categorias.index', compact('categorias', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:150', 'unique:categorias,nombre'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.categorias.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createCategoriaModal');
        }

        Categoria::query()->create([
            'nombre' => trim((string) $request->input('nombre')),
        ]);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $categoria = Categoria::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:150', 'unique:categorias,nombre,' . $categoria->id],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.categorias.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editCategoriaModal-' . $categoria->id);
        }

        $categoria->update([
            'nombre' => trim((string) $request->input('nombre')),
        ]);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $categoria = Categoria::query()->findOrFail($id);
        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
