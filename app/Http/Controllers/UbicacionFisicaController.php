<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\UbicacionFisica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UbicacionFisicaController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $ubicaciones = UbicacionFisica::with('sucursal')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', '%' . $search . '%')
                    ->orWhereHas('sucursal', function ($query) use ($search) {
                        $query->where('nombre', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $sucursales = Sucursal::orderBy('nombre')->get();

        return view('admin.ubicacion_fisicas.index', compact('ubicaciones', 'sucursales', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sucursal_id' => ['required', 'integer', 'exists:sucursals,id'],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ubicacion_fisicas', 'nombre')->where(function ($query) use ($request) {
                    return $query->where('sucursal_id', $request->input('sucursal_id'));
                }),
            ],
            'descripcion' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.ubicacion_fisicas.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createUbicacionFisicaModal');
        }

        UbicacionFisica::create($request->only('sucursal_id', 'nombre', 'descripcion'));

        return redirect()
            ->route('admin.ubicacion_fisicas.index')
            ->with('success', 'Ubicación física creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $ubicacion = UbicacionFisica::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'sucursal_id' => ['required', 'integer', 'exists:sucursals,id'],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ubicacion_fisicas', 'nombre')->ignore($ubicacion->id)->where(function ($query) use ($request) {
                    return $query->where('sucursal_id', $request->input('sucursal_id'));
                }),
            ],
            'descripcion' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.ubicacion_fisicas.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editUbicacionFisicaModal-' . $ubicacion->id);
        }

        $ubicacion->update($request->only('sucursal_id', 'nombre', 'descripcion'));

        return redirect()
            ->route('admin.ubicacion_fisicas.index')
            ->with('success', 'Ubicación física actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $ubicacion = UbicacionFisica::findOrFail($id);
        $ubicacion->delete();

        return redirect()
            ->route('admin.ubicacion_fisicas.index')
            ->with('success', 'Ubicación física eliminada correctamente.');
    }
}
