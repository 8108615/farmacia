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
        if ($request->filled('arqueo')) {
            return redirect()->route('admin.arqueos.show', $request->integer('arqueo'));
        }

        $buscar = $this->busqueda($request);
        $cajas = Caja::with('sucursal')->withExists('arqueos')
            ->when($buscar !== '', fn ($query) => $query->where('nombre', 'like', '%' . $buscar . '%'))
            ->orderBy('nombre')->orderBy('id')->paginate(10)->withQueryString();
        $sucursales = Sucursal::orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.cajas.index', compact('cajas', 'sucursales', 'buscar'));
    }

    public function arqueos(Request $request)
    {
        if ($request->filled('arqueo')) {
            return redirect()->route('admin.arqueos.show', $request->integer('arqueo'));
        }

        $buscar = $this->busqueda($request);
        $cajas = Caja::with('sucursal')->where('estado', 'cerrada')
            ->whereDoesntHave('arqueos', fn ($query) => $query->where('estado', 'abierto'))
            ->whereNotIn('sucursal_id', DB::table('arqueos')->select('sucursal_id')
                ->where('usuario_id', $request->user()->id)->where('estado', 'abierto'))
            ->orderBy('nombre')->orderBy('id')->get();
        $arqueos = DB::table('arqueos')
            ->join('cajas', 'cajas.id', '=', 'arqueos.caja_id')
            ->join('sucursals', 'sucursals.id', '=', 'arqueos.sucursal_id')
            ->join('users', 'users.id', '=', 'arqueos.usuario_id')
            ->select('arqueos.*', 'cajas.nombre as caja', 'sucursals.nombre as sucursal', 'users.name as usuario')
            ->when($buscar !== '', fn ($query) => $query->where('cajas.nombre', 'like', '%' . $buscar . '%'));

        foreach ($this->actividadArqueo() as $alias => $actividad) {
            $arqueos->selectRaw("EXISTS ({$actividad->toSql()}) as {$alias}", $actividad->getBindings());
        }

        $arqueos = $arqueos->orderByDesc('arqueos.id')->paginate(10)->appends(['buscar' => $buscar]);
        foreach ($arqueos as $arqueo) {
            $arqueo->puedeModificar = $arqueo->estado === 'abierto'
                && (int) $arqueo->usuario_id === (int) $request->user()->id
                && !$arqueo->tiene_movimientos && !$arqueo->tiene_ventas
                && (float) $arqueo->total_ingresos === 0.0 && (float) $arqueo->total_egresos === 0.0;
        }

        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';

        return view('admin.arqueos.index', compact('cajas', 'arqueos', 'divisa', 'buscar'));
    }

    public function verArqueo(Request $request, int $arqueo)
    {
        return $this->detalleArqueo($request, $arqueo, false);
    }

    public function formularioCierre(Request $request, int $arqueo)
    {
        return $this->detalleArqueo($request, $arqueo, true);
    }

    private function detalleArqueo(Request $request, int $arqueo, bool $cerrando)
    {
        $detalle = $this->consultarArqueo($arqueo);

        $esResponsable = (int) $detalle->usuario_id === (int) $request->user()->id;
        if ($cerrando) {
            abort_unless($esResponsable, 403);
            if ($detalle->estado !== 'abierto') {
                return redirect()->route('admin.arqueos.show', $arqueo)->with('error', 'El turno ya está cerrado.');
            }
        }

        $puedeCerrar = $esResponsable && $detalle->estado === 'abierto';
        $esperado = $this->saldo($detalle) / 100;
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';
        $movimientos = DB::table('arqueo_detalles')->where('arqueo_id', $arqueo)->orderByDesc('id')->paginate(20);

        return view('admin.arqueos.detalle', compact(
            'detalle', 'movimientos', 'divisa', 'cerrando', 'puedeCerrar', 'esperado',
        ));
    }

    public function reporteArqueo(Request $request, int $id)
    {
        $detalle = $this->consultarArqueo($id);
        abort_unless($detalle->estado === 'cerrado', 404);

        $esperado = $this->saldo($detalle) / 100;
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';
        $pdf = new \Dompdf\Dompdf();
        $pdf->setPaper('a4');
        $pdf->loadHtml(view('admin.arqueos.comprobante', compact('detalle', 'esperado', 'divisa'))->render(), 'UTF-8');
        $pdf->render();

        $modo = $request->boolean('descargar') ? 'attachment' : 'inline';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $modo . '; filename="cierre-arqueo-' . $id . '.pdf"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function consultarArqueo(int $arqueo): object
    {
        $detalle = DB::table('arqueos')
            ->join('cajas', 'cajas.id', '=', 'arqueos.caja_id')
            ->join('sucursals', 'sucursals.id', '=', 'arqueos.sucursal_id')
            ->join('users', 'users.id', '=', 'arqueos.usuario_id')
            ->select('arqueos.*', 'cajas.nombre as caja', 'sucursals.nombre as sucursal', 'users.name as usuario')
            ->where('arqueos.id', $arqueo)->first();
        abort_unless($detalle, 404);

        return $detalle;
    }

    private function busqueda(Request $request): string
    {
        return trim($request->validate(['buscar' => ['nullable', 'string', 'max:100']])['buscar'] ?? '');
    }

    public function abrirTurno(Request $request)
    {
        $datos = $request->validate(['caja_id' => ['required', 'integer', 'exists:cajas,id']]);

        return $this->abrir($request, Caja::findOrFail($datos['caja_id']));
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

    public function update(Request $request, int $id)
    {
        $datos = $request->validateWithBag('editarCaja', [
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($id, $datos) {
            $sucursalId = Caja::findOrFail($id)->sucursal_id;
            Sucursal::lockForUpdate()->findOrFail($sucursalId);
            $caja = Caja::lockForUpdate()->findOrFail($id);

            if (Caja::where('sucursal_id', $sucursalId)->where('nombre', $datos['nombre'])
                ->where('id', '<>', $id)->lockForUpdate()->first(['id'])) {
                throw ValidationException::withMessages([
                    'nombre' => 'Ya existe una caja con ese nombre en la sucursal.',
                ])->errorBag('editarCaja');
            }

            $caja->update($datos);
        }, 3);

        return redirect()->route('admin.cajas.index')->with('success', 'Caja actualizada.');
    }

    public function destroy(int $id)
    {
        $eliminada = DB::transaction(function () use ($id) {
            $caja = Caja::lockForUpdate()->findOrFail($id);

            if ($caja->estado !== 'cerrada' || $caja->arqueos()->exists()) {
                return false;
            }

            return $caja->delete();
        }, 3);

        return redirect()->route('admin.cajas.index')->with(
            $eliminada ? 'success' : 'error',
            $eliminada ? 'Caja eliminada.' : 'Solo puedes eliminar cajas cerradas sin historial de arqueos.',
        );
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

        return redirect()->route('admin.arqueos.show', $id)->with('success', 'Caja abierta.');
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

        return redirect()->route('admin.arqueos.show', $arqueo)->with('success', 'Movimiento registrado.');
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

        return redirect()->route('admin.arqueos.index')->with('success', 'Arqueo cerrado.');
    }

    public function editarArqueo(Request $request, int $id)
    {
        $datos = $request->validateWithBag('editarArqueo', [
            'monto_inicial' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
        ]);

        DB::transaction(function () use ($request, $id, $datos) {
            $this->turnoSinActividad($request, $id, 'editarArqueo');
            DB::table('arqueos')->where('id', $id)->update($datos + ['updated_at' => now()]);
        }, 3);

        return redirect()->route('admin.arqueos.index')->with('success', 'Monto inicial actualizado.');
    }

    public function eliminarArqueo(Request $request, int $id)
    {
        DB::transaction(function () use ($request, $id) {
            $caja = $this->turnoSinActividad($request, $id);
            DB::table('arqueos')->where('id', $id)->delete();
            $caja->update(['estado' => 'cerrada']);
        }, 3);

        return redirect()->route('admin.arqueos.index')->with('success', 'Turno eliminado. La caja está disponible.');
    }

    private function actividadArqueo(): array
    {
        return [
            'tiene_movimientos' => DB::table('arqueo_detalles')->selectRaw('1')
                ->whereColumn('arqueo_id', 'arqueos.id'),
            // Las ventas sin efectivo no generan un movimiento en el arqueo.
            'tiene_ventas' => DB::table('ventas')->selectRaw('1')
                ->whereColumn('ventas.usuario_id', 'arqueos.usuario_id')
                ->whereColumn('ventas.sucursal_id', 'arqueos.sucursal_id')
                ->whereColumn('ventas.created_at', '>=', 'arqueos.fecha_apertura'),
        ];
    }

    private function turnoSinActividad(Request $request, int $id, string $bolsa = 'default'): Caja
    {
        $usuario = $request->user();
        $usuario->newQuery()->lockForUpdate()->findOrFail($usuario->id);

        try {
            [$caja, $turno] = $this->turnoAbierto($request, $id);
            $consulta = DB::table('arqueos')->where('id', $id);
            foreach ($this->actividadArqueo() as $actividad) {
                $consulta->whereNotExists($actividad);
            }

            if (!$consulta->exists() || (float) $turno->total_ingresos !== 0.0 || (float) $turno->total_egresos !== 0.0) {
                throw ValidationException::withMessages(['arqueo' => 'El turno ya tiene ventas o movimientos.']);
            }
        } catch (ValidationException $error) {
            throw $error->errorBag($bolsa);
        }

        return $caja;
    }

    private function turnoAbierto(Request $request, int $id): array
    {
        $turno = DB::table('arqueos')->find($id);
        abort_unless($turno, 404);
        abort_unless((int) $turno->usuario_id === (int) $request->user()->id, 403);
        $caja = Caja::lockForUpdate()->findOrFail($turno->caja_id);
        $turno = DB::table('arqueos')->lockForUpdate()->find($id);
        abort_unless($turno, 404);

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
