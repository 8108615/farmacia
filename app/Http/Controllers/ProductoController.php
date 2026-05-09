<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $productos = Producto::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre_comercial', 'like', '%' . $search . '%')
                    ->orWhere('nombre_generico', 'like', '%' . $search . '%')
                    ->orWhere('codigo_producto', 'like', '%' . $search . '%')
                    ->orWhere('codigo_barra', 'like', '%' . $search . '%');
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        //
    }
}
