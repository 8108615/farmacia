<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Caja;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $sucursales = Sucursal::orderBy('nombre')->get(['id', 'nombre']);
        $cajas = Caja::with('sucursal')->orderBy('nombre')->get();
        $arqueos = DB::table('arqueos')
            ->join('cajas', 'cajas.id', '=', 'arqueos.caja_id')
            ->join('sucursals', 'sucursals.id', '=', 'arqueos.sucursal_id')
            ->join('users', 'users.id', '=', 'arqueos.usuario_id')
            ->select('arqueos.*', 'cajas.nombre as caja', 'sucursals.nombre as sucursal', 'users.name as usuario')
            ->orderByDesc('arqueos.id')->paginate(15);
        $detalle = $request->filled('arqueo')
            ? DB::table('arqueos')->where('id', $request->integer('arqueo'))->first()
            : null;
        $movimientos = $detalle
            ? DB::table('arqueo_detalles')->where('arqueo_id', $detalle->id)->orderByDesc('id')->get()
            : collect();
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';

        return view('admin.cajas.index', compact('sucursales', 'cajas', 'arqueos', 'detalle', 'movimientos', 'divisa'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'sucursal_id' => ['required', 'integer', 'exists:sucursals,id'],
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($datos) {
            Sucursal::lockForUpdate()->findOrFail($datos['sucursal_id']);
            if (Caja::where('sucursal_id', $datos['sucursal_id'])->where('nombre', $datos['nombre'])->exists()) {
                throw ValidationException::withMessages(['nombre' => 'Ya existe una caja con ese nombre en la sucursal.']);
            }
            Caja::create($datos + ['estado' => 'cerrada']);
        }, 3);

        return redirect()->route('admin.cajas.index')->with('success', 'Caja registrada.');
    }

    public function abrir(Request $request, Caja $caja)
    {
        $datos = $request->validate([
            'monto_inicial' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
        ]);

        $usuario = $request->user();
        $id = DB::transaction(function () use ($caja, $datos, $usuario) {
            $usuario->newQuery()->lockForUpdate()->findOrFail($usuario->id);
            $caja = Caja::lockForUpdate()->findOrFail($caja->id);
            if ($caja->estado !== 'cerrada' || DB::table('arqueos')->where('caja_id', $caja->id)->where('estado', 'abierto')->exists()) {
                throw ValidationException::withMessages(['caja' => 'La caja ya tiene un arqueo abierto.']);
            }

            if (DB::table('arqueos')->where('sucursal_id', $caja->sucursal_id)
                ->where('usuario_id', $usuario->id)->where('estado', 'abierto')->exists()) {
                throw ValidationException::withMessages(['caja' => 'Ya tienes una caja abierta en esta sucursal. Ciérrala antes de abrir otra.']);
            }

            $ahora = now();
            $id = DB::table('arqueos')->insertGetId([
                'caja_id' => $caja->id, 'sucursal_id' => $caja->sucursal_id,
                'usuario_id' => $usuario->id, 'fecha_apertura' => $ahora,
                'monto_inicial' => $datos['monto_inicial'], 'estado' => 'abierto',
                'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
            $caja->update(['estado' => 'abierta']);

            return $id;
        }, 3);

        return redirect()->route('admin.cajas.index', ['arqueo' => $id])->with('success', 'Caja abierta.');
    }

    public function movimiento(Request $request, int $arqueo)
    {
        $datos = $request->validate([
            'tipo' => ['required', 'in:ingreso,egreso'],
            'concepto' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999.99'],
        ]);

        DB::transaction(function () use ($request, $arqueo, $datos) {
            [$caja, $turno] = $this->turnoAbierto($request, $arqueo);
            $campo = $datos['tipo'] === 'ingreso' ? 'total_ingresos' : 'total_egresos';
            $importe = $this->centavos($datos['monto']);
            $total = $this->centavos($turno->$campo) + $importe;
            $saldo = $this->saldo($turno) + ($datos['tipo'] === 'ingreso' ? $importe : -$importe);

            if ($total > 999999999999 || $saldo > 999999999999 || $saldo < 0) {
                throw ValidationException::withMessages(['monto' => 'El monto excede el saldo disponible o el límite de la caja.']);
            }

            $ahora = now();
            DB::table('arqueo_detalles')->insert($datos + [
                'arqueo_id' => $arqueo, 'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
            DB::table('arqueos')->where('id', $arqueo)->update([
                $campo => $this->decimal($total), 'updated_at' => $ahora,
            ]);
        }, 3);

        return redirect()->route('admin.cajas.index', ['arqueo' => $arqueo])->with('success', 'Movimiento registrado.');
    }

    public function cerrar(Request $request, int $arqueo)
    {
        $datos = $request->validate([
            'monto_contado' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $arqueo, $datos) {
            [$caja, $turno] = $this->turnoAbierto($request, $arqueo);
            $ahora = now();
            DB::table('arqueos')->where('id', $arqueo)->update($datos + [
                'fecha_cierre' => $ahora, 'estado' => 'cerrado', 'updated_at' => $ahora,
                'diferencia' => $this->decimal($this->centavos($datos['monto_contado']) - $this->saldo($turno)),
            ]);
            $caja->update(['estado' => 'cerrada']);
        }, 3);

        return redirect()->route('admin.cajas.index', ['arqueo' => $arqueo])->with('success', 'Arqueo cerrado.');
    }

    private function turnoAbierto(Request $request, int $id): array
    {
        $turno = DB::table('arqueos')->find($id);
        abort_unless($turno, 404);
        abort_unless((int) $turno->usuario_id === (int) $request->user()->id, 403);
        $caja = Caja::lockForUpdate()->findOrFail($turno->caja_id);
        $turno = DB::table('arqueos')->lockForUpdate()->find($id);

        if ($turno->estado !== 'abierto' || $caja->estado !== 'abierta') {
            throw ValidationException::withMessages(['caja' => 'El arqueo ya está cerrado.']);
        }

        return [$caja, $turno];
    }

    private function saldo(object $turno): int
    {
        return $this->centavos($turno->monto_inicial)
            + $this->centavos($turno->total_ingresos) - $this->centavos($turno->total_egresos);
    }

    private function centavos(string|int|float $importe): int
    {
        return (int) round((float) $importe * 100);
    }

    private function decimal(int $centavos): string
    {
        return number_format($centavos / 100, 2, '.', '');
    }
}
