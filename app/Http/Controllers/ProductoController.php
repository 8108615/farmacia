<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\FormaFarmaceutica;
use App\Models\Laboratorio;
use App\Models\Presentacion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $productos = Producto::query()
            ->with(['categoria', 'laboratorio', 'formaFarmaceutica', 'presentacion'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre_comercial', 'like', '%' . $search . '%')
                    ->orWhere('nombre_generico', 'like', '%' . $search . '%')
                    ->orWhere('codigo_producto', 'like', '%' . $search . '%')
                    ->orWhere('codigo_barra', 'like', '%' . $search . '%')
                ;
            })
            ->orderBy('nombre_comercial')
            ->paginate(10)
            ->withQueryString();

        return view('admin.productos.index', compact('productos', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::query()->orderBy('nombre')->get(['id', 'nombre']);
        $laboratorios = Laboratorio::query()->orderBy('nombre')->get(['id', 'nombre']);
        $formaFarmaceuticas = FormaFarmaceutica::query()->orderBy('nombre')->get(['id', 'nombre']);
        $presentaciones = Presentacion::query()->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.productos.create', compact(
            'categorias',
            'laboratorios',
            'formaFarmaceuticas',
            'presentaciones'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'laboratorio_id' => 'nullable|exists:laboratorios,id',
            'forma_farmaceutica_id' => 'nullable|exists:forma_farmaceuticas,id',
            'presentacion_id' => 'nullable|exists:presentacions,id',
            'codigo_producto' => 'required|string|max:50|unique:productos,codigo_producto',
            'codigo_barra' => 'nullable|string|max:50|unique:productos,codigo_barra',
            'nombre_comercial' => 'required|string|max:255',
            'nombre_generico' => 'required|string|max:255',
            'concentracion' => 'nullable|string|max:100',
            'accion_terapeutica' => 'nullable|string|max:255',
            'unidad_medida' => 'nullable|string|max:50',
            'usa_receta' => 'boolean',
            'imagen' => 'nullable|image|max:2048', // Máximo 2MB
        ]);

        $producto = new Producto();
        $producto->categoria_id = $request->input('categoria_id');
        $producto->laboratorio_id = $request->input('laboratorio_id');
        $producto->forma_farmaceutica_id = $request->input('forma_farmaceutica_id');
        $producto->presentacion_id = $request->input('presentacion_id');
        $producto->codigo_producto = $request->input('codigo_producto');
        $producto->codigo_barra = $request->input('codigo_barra');
        $producto->nombre_comercial = $request->input('nombre_comercial');
        $producto->nombre_generico = $request->input('nombre_generico');
        $producto->concentracion = $request->input('concentracion');
        $producto->accion_terapeutica = $request->input('accion_terapeutica');
        $producto->unidad_medida = $request->input('unidad_medida');
        $producto->usa_receta = $request->input('usa_receta', false);

        if ($request->hasFile('imagen')) {
            $producto->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto->save();

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::query()
            ->with(['categoria', 'laboratorio', 'formaFarmaceutica', 'presentacion'])
            ->findOrFail($id);

        return view('admin.productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $producto = Producto::query()->findOrFail($id);

        $categorias = Categoria::query()->orderBy('nombre')->get(['id', 'nombre']);
        $laboratorios = Laboratorio::query()->orderBy('nombre')->get(['id', 'nombre']);
        $formaFarmaceuticas = FormaFarmaceutica::query()->orderBy('nombre')->get(['id', 'nombre']);
        $presentaciones = Presentacion::query()->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.productos.edit', compact(
            'producto',
            'categorias',
            'laboratorios',
            'formaFarmaceuticas',
            'presentaciones'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::query()->findOrFail($id);

        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'laboratorio_id' => 'nullable|exists:laboratorios,id',
            'forma_farmaceutica_id' => 'nullable|exists:forma_farmaceuticas,id',
            'presentacion_id' => 'nullable|exists:presentacions,id',
            'codigo_producto' => ['required', 'string', 'max:50', Rule::unique('productos', 'codigo_producto')->ignore($producto->id)],
            'codigo_barra' => ['nullable', 'string', 'max:50', Rule::unique('productos', 'codigo_barra')->ignore($producto->id)],
            'nombre_comercial' => 'required|string|max:255',
            'nombre_generico' => 'required|string|max:255',
            'concentracion' => 'nullable|string|max:100',
            'accion_terapeutica' => 'nullable|string|max:255',
            'unidad_medida' => 'nullable|string|max:50',
            'usa_receta' => 'boolean',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $producto->categoria_id = $request->input('categoria_id');
        $producto->laboratorio_id = $request->input('laboratorio_id');
        $producto->forma_farmaceutica_id = $request->input('forma_farmaceutica_id');
        $producto->presentacion_id = $request->input('presentacion_id');
        $producto->codigo_producto = $request->input('codigo_producto');
        $producto->codigo_barra = $request->input('codigo_barra');
        $producto->nombre_comercial = $request->input('nombre_comercial');
        $producto->nombre_generico = $request->input('nombre_generico');
        $producto->concentracion = $request->input('concentracion');
        $producto->accion_terapeutica = $request->input('accion_terapeutica');
        $producto->unidad_medida = $request->input('unidad_medida');
        $producto->usa_receta = $request->boolean('usa_receta');

        if ($request->hasFile('imagen')) {
            if (!empty($producto->imagen) && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto->save();

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $producto = Producto::query()->findOrFail($id);

        if (!empty($producto->imagen) && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
