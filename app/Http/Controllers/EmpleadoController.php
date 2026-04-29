<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    public function index()
    {
        $search = trim((string) request('search', ''));

        $empleados = Empleado::query()
            ->with(['usuario:id,name,email', 'sucursal:id,nombre'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nombres', 'like', '%' . $search . '%')
                        ->orWhere('apellidos', 'like', '%' . $search . '%')
                        ->orWhere('numero_doc', 'like', '%' . $search . '%')
                        ->orWhere('telefono', 'like', '%' . $search . '%')
                        ->orWhereHas('sucursal', function ($query) use ($search) {
                            $query->where('nombre', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        $sucursales = Sucursal::query()->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.empleados.index', compact('empleados', 'search', 'sucursales'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sucursal_id' => ['required', 'exists:sucursals,id'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'tipo_doc' => ['required', 'in:CI,DNI,RUC,PASAPORTE,OTRO'],
            'numero_doc' => ['required', 'string', 'max:50', 'unique:empleados,numero_doc'],
            'telefono' => ['required', 'string', 'max:50'],
            'direccion' => ['required', 'string'],
            'profesion' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'in:M,F'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.empleados.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'createEmpleadoModal');
        }

        $user = User::query()->create([
            'name' => trim((string) $request->input('nombres')) . ' ' . trim((string) $request->input('apellidos')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'password' => trim((string) $request->input('numero_doc')),
        ]);

        $user->assignRole('EMPLEADO');

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('empleados', 'public');
        }

        Empleado::query()->create([
            'usuario_id' => $user->id,
            'sucursal_id' => (int) $request->input('sucursal_id'),
            'nombres' => trim((string) $request->input('nombres')),
            'apellidos' => trim((string) $request->input('apellidos')),
            'tipo_doc' => $request->input('tipo_doc'),
            'numero_doc' => trim((string) $request->input('numero_doc')),
            'telefono' => filled($request->input('telefono')) ? trim((string) $request->input('telefono')) : null,
            'direccion' => filled($request->input('direccion')) ? trim((string) $request->input('direccion')) : null,
            'profesion' => filled($request->input('profesion')) ? trim((string) $request->input('profesion')) : null,
            'fecha_nacimiento' => filled($request->input('fecha_nacimiento')) ? $request->input('fecha_nacimiento') : null,
            'genero' => filled($request->input('genero')) ? $request->input('genero') : null,
            'avatar' => $avatarPath,
            'estado' => $request->input('estado'),
        ]);

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $empleado = Empleado::query()->findOrFail($id);

        $emailRules = ['required', 'string', 'email', 'max:150'];

        if ($empleado->usuario_id !== null) {
            $emailRules[] = 'unique:users,email,' . $empleado->usuario_id;
        } else {
            $emailRules[] = 'unique:users,email';
        }

        $validator = Validator::make($request->all(), [
            'sucursal_id' => ['required', 'exists:sucursals,id'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
            'tipo_doc' => ['required', 'in:CI,DNI,RUC,PASAPORTE,OTRO'],
            'numero_doc' => ['required', 'string', 'max:50', 'unique:empleados,numero_doc,' . $empleado->id],
            'telefono' => ['required', 'string', 'max:50'],
            'direccion' => ['required', 'string'],
            'profesion' => ['required', 'string', 'max:150'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'in:M,F'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.empleados.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', 'editEmpleadoModal-' . $empleado->id);
        }

        $user = $empleado->usuario;

        if ($user === null) {
            $user = User::query()->create([
                'name' => trim((string) $request->input('nombres')) . ' ' . trim((string) $request->input('apellidos')),
                'email' => strtolower(trim((string) $request->input('email'))),
                'password' => trim((string) $request->input('numero_doc')),
            ]);
            $user->syncRoles(['EMPLEADO']);
        } else {
            $user->update([
                'name' => trim((string) $request->input('nombres')) . ' ' . trim((string) $request->input('apellidos')),
                'email' => strtolower(trim((string) $request->input('email'))),
                'password' => trim((string) $request->input('numero_doc')),
            ]);
            $user->syncRoles(['EMPLEADO']);
        }

        if ($request->hasFile('avatar')) {
            if (!empty($empleado->avatar) && Storage::disk('public')->exists($empleado->avatar)) {
                Storage::disk('public')->delete($empleado->avatar);
            }
            $avatarPath = $request->file('avatar')->store('empleados', 'public');
        } else {
            $avatarPath = $empleado->avatar;
        }

        $empleado->update([
            'usuario_id' => $user->id,
            'sucursal_id' => (int) $request->input('sucursal_id'),
            'nombres' => trim((string) $request->input('nombres')),
            'apellidos' => trim((string) $request->input('apellidos')),
            'tipo_doc' => $request->input('tipo_doc'),
            'numero_doc' => trim((string) $request->input('numero_doc')),
            'telefono' => filled($request->input('telefono')) ? trim((string) $request->input('telefono')) : null,
            'direccion' => filled($request->input('direccion')) ? trim((string) $request->input('direccion')) : null,
            'profesion' => filled($request->input('profesion')) ? trim((string) $request->input('profesion')) : null,
            'fecha_nacimiento' => filled($request->input('fecha_nacimiento')) ? $request->input('fecha_nacimiento') : null,
            'genero' => filled($request->input('genero')) ? $request->input('genero') : null,
            'avatar' => $avatarPath,
            'estado' => $request->input('estado'),
        ]);

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $empleado = Empleado::query()->findOrFail($id);

        if ($empleado->usuario) {
            $empleado->usuario->syncRoles([]);
            $empleado->usuario->delete();
        }

        $empleado->delete();

        return redirect()
            ->route('admin.empleados.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}
