@extends('layouts.admin')

@section('content')
<h3 class="page-heading">Caja y arqueos</h3>
@php
    $simbolo = $divisa ?? 'Bs';
    $moneda = fn ($valor) => $simbolo . ' ' . number_format($valor, 2);
@endphp

@if ($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

<section class="card">
    <div class="card-body">
        <h5>Registrar caja</h5>
        <form method="POST" action="{{ route('admin.cajas.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-5">
                <label for="caja-sucursal" class="form-label">Sucursal</label>
                <select name="sucursal_id" id="caja-sucursal" class="form-select" required>
                    <option value="">Seleccionar sucursal</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" @selected(old('sucursal_id') == $sucursal->id)>{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label for="caja-nombre" class="form-label">Nombre de la caja</label>
                <input name="nombre" id="caja-nombre" class="form-control" value="{{ old('nombre') }}" maxlength="255" required>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Registrar</button></div>
        </form>
    </div>
</section>

<div class="row g-3 mb-4">
    @foreach ($cajas as $caja)
        <div class="col-md-6 col-xl-4">
            <section class="card h-100 mb-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between gap-2">
                        <h5>{{ $caja->nombre }}</h5>
                        <span class="badge align-self-start {{ $caja->estado === 'abierta' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($caja->estado) }}</span>
                    </div>
                    <p>{{ $caja->sucursal?->nombre }}</p>
                    @if ($caja->estado === 'cerrada')
                        <form method="POST" action="{{ route('admin.cajas.abrir', $caja) }}">
                            @csrf
                            <label class="form-label" for="inicial-{{ $caja->id }}">Monto inicial ({{ $divisa }})</label>
                            <div class="input-group">
                                <input type="number" name="monto_inicial" id="inicial-{{ $caja->id }}" class="form-control" min="0" max="9999999999.99" step="0.01" value="0" required>
                                <button class="btn btn-outline-primary">Abrir caja</button>
                            </div>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    @endforeach
</div>

@if ($detalle)
    @php
        $abiertoPorMi = $detalle->estado === 'abierto' && (int) $detalle->usuario_id === (int) auth()->id();
        $esperado = $detalle->monto_inicial + $detalle->total_ingresos - $detalle->total_egresos;
    @endphp
    <section id="arqueo" class="card">
        <div class="card-body">
            <h5>Arqueo #{{ $detalle->id }}</h5>
            <p class="small">El saldo incluye el monto inicial, los cobros y devoluciones en efectivo y los movimientos manuales.</p>
            <dl class="row">
                <div class="col-sm-3"><dt>Inicial</dt><dd>{{ $moneda($detalle->monto_inicial) }}</dd></div>
                <div class="col-sm-3"><dt>Ingresos</dt><dd>{{ $moneda($detalle->total_ingresos) }}</dd></div>
                <div class="col-sm-3"><dt>Egresos</dt><dd>{{ $moneda($detalle->total_egresos) }}</dd></div>
                <div class="col-sm-3"><dt>Efectivo esperado</dt><dd>{{ $moneda($esperado) }}</dd></div>
                @if ($detalle->estado === 'cerrado')
                    <div class="col-sm-3"><dt>Efectivo contado</dt><dd>{{ $moneda($detalle->monto_contado) }}</dd></div>
                    <div class="col-sm-3"><dt>Diferencia</dt><dd>{{ $moneda($detalle->diferencia) }}</dd></div>
                    @if ($detalle->observaciones)
                        <div class="col-sm-6"><dt>Observaciones</dt><dd>{{ $detalle->observaciones }}</dd></div>
                    @endif
                @endif
            </dl>
            @if ($abiertoPorMi)
                <div class="row g-4 mb-4">
                    <form class="col-lg-6" method="POST" action="{{ route('admin.cajas.movimiento', $detalle->id) }}">
                        @csrf
                        <h6>Registrar movimiento</h6>
                        <label class="form-label" for="movimiento-tipo">Tipo</label>
                        <select name="tipo" id="movimiento-tipo" class="form-select mb-2"><option value="ingreso">Ingreso</option><option value="egreso">Retiro</option></select>
                        <label class="form-label" for="movimiento-concepto">Concepto</label>
                        <input name="concepto" id="movimiento-concepto" class="form-control mb-2" maxlength="255" required>
                        <label class="form-label" for="movimiento-monto">Monto ({{ $divisa }})</label>
                        <input type="number" name="monto" id="movimiento-monto" class="form-control mb-3" min="0.01" max="9999999999.99" step="0.01" required>
                        <button class="btn btn-primary">Registrar movimiento</button>
                    </form>
                    <form class="col-lg-6" method="POST" action="{{ route('admin.cajas.cerrar', $detalle->id) }}">
                        @csrf
                        <h6>Cerrar arqueo</h6>
                        <label class="form-label" for="arqueo-contado">Efectivo contado ({{ $divisa }})</label>
                        <input type="number" name="monto_contado" id="arqueo-contado" class="form-control mb-2" min="0" max="9999999999.99" step="0.01" required>
                        <label class="form-label" for="arqueo-observaciones">Observaciones</label>
                        <textarea name="observaciones" id="arqueo-observaciones" class="form-control mb-3" rows="3" maxlength="2000"></textarea>
                        <button class="btn btn-outline-danger" onclick="return confirm('¿Cerrar este arqueo?')">Cerrar arqueo</button>
                    </form>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Fecha y hora</th><th>Tipo</th><th>Concepto</th><th class="text-end">Monto</th></tr></thead>
                    <tbody>
                        @forelse ($movimientos as $movimiento)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($movimiento->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ ucfirst($movimiento->tipo) }}</td>
                                <td>
                                    {{ $movimiento->concepto }}
                                    @if ($movimiento->referencia)
                                        <small class="d-block text-muted">{{ $movimiento->referencia }}</small>
                                    @endif
                                </td>
                                <td class="text-end">{{ $moneda($movimiento->monto) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Todavía no hay movimientos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endif

<section class="card">
    <div class="card-body">
        <h5>Historial de arqueos</h5>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead><tr><th>Caja</th><th>Sucursal</th><th>Responsable</th><th>Apertura</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                <tbody>
                    @forelse ($arqueos as $arqueo)
                        <tr>
                            <td>{{ $arqueo->caja }}</td><td>{{ $arqueo->sucursal }}</td><td>{{ $arqueo->usuario }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($arqueo->fecha_apertura)->format('d/m/Y H:i') }}</td>
                            <td>{{ ucfirst($arqueo->estado) }}</td>
                            <td class="text-end"><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.cajas.index', ['arqueo' => $arqueo->id]) }}#arqueo">Ver arqueo #{{ $arqueo->id }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Todavía no hay arqueos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $arqueos->links('pagination::bootstrap-5') }}
    </div>
</section>
@endsection
