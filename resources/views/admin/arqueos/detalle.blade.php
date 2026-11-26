@extends('layouts.admin')

@section('content')
@php
    $simbolo = $divisa ?? 'Bs';
    $moneda = fn ($valor) => $simbolo . ' ' . number_format($valor, 2);
    $fecha = fn ($valor) => $valor ? \Illuminate\Support\Carbon::parse($valor)->format('d/m/Y H:i') : '—';
    $cerrado = $detalle->estado === 'cerrado';
@endphp

<div class="page-heading d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h3 class="mb-0">{{ $cerrando ? 'Cerrar turno' : 'Detalle del turno' }} #{{ $detalle->id }}</h3>
    <div class="d-flex gap-2">
        @if (!$cerrando && $puedeCerrar)
            <a href="{{ route('admin.arqueos.cierre', $detalle->id) }}" class="btn btn-success">Cerrar turno</a>
        @endif
        <a href="{{ $cerrando ? route('admin.arqueos.show', $detalle->id) : route('admin.arqueos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

<div class="row g-3">
    <div class="col-lg-5">
        <section class="card">
            <div class="card-body">
                <h5 class="mb-3">Resumen del turno</h5>
                <dl class="row mb-0">
                    <dt class="col-5">Caja</dt><dd class="col-7">{{ $detalle->caja }}</dd>
                    <dt class="col-5">Sucursal</dt><dd class="col-7">{{ $detalle->sucursal }}</dd>
                    <dt class="col-5">Responsable</dt><dd class="col-7">{{ $detalle->usuario }}</dd>
                    <dt class="col-5">Apertura</dt><dd class="col-7">{{ $fecha($detalle->fecha_apertura) }}</dd>
                    <dt class="col-5">Cierre</dt><dd class="col-7">{{ $fecha($detalle->fecha_cierre) }}</dd>
                    <dt class="col-5">Estado</dt>
                    <dd class="col-7"><span class="badge {{ $cerrado ? 'bg-secondary' : 'bg-success' }}">{{ ucfirst($detalle->estado) }}</span></dd>
                </dl>
                <hr>
                <dl class="row mb-0">
                    <dt class="col-7">Monto inicial</dt><dd class="col-5 text-end">{{ $moneda($detalle->monto_inicial) }}</dd>
                    <dt class="col-7">Ingresos</dt><dd class="col-5 text-end">{{ $moneda($detalle->total_ingresos) }}</dd>
                    <dt class="col-7">Egresos</dt><dd class="col-5 text-end">{{ $moneda($detalle->total_egresos) }}</dd>
                    <dt class="col-7">Efectivo esperado</dt><dd class="col-5 text-end fw-bold">{{ $moneda($esperado) }}</dd>
                    @if ($cerrado)
                        <dt class="col-7">Efectivo contado</dt><dd class="col-5 text-end">{{ $moneda($detalle->monto_contado) }}</dd>
                        <dt class="col-7">Diferencia</dt><dd class="col-5 text-end">{{ $moneda($detalle->diferencia) }}</dd>
                        @if ($detalle->observaciones)
                            <dt class="col-12 mt-2">Observaciones</dt><dd class="col-12">{{ $detalle->observaciones }}</dd>
                        @endif
                    @endif
                </dl>
            </div>
        </section>
    </div>

    <div class="col-lg-7">
        @if ($cerrando)
            <section class="card">
                <div class="card-body">
                    <h5 class="mb-3">Conteo de efectivo</h5>
                    <form method="POST" action="{{ route('admin.cajas.cerrar', $detalle->id) }}">
                        @csrf
                        <label for="monto-contado" class="form-label">Efectivo contado ({{ $simbolo }})</label>
                        <input id="monto-contado" class="form-control mb-3" type="number" name="monto_contado"
                            min="0" max="9999999999.99" step="0.01" value="{{ old('monto_contado') }}" required>
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea id="observaciones" class="form-control mb-3" name="observaciones" rows="3" maxlength="2000">{{ old('observaciones') }}</textarea>
                        <button class="btn btn-success">Confirmar cierre</button>
                    </form>
                </div>
            </section>
        @else
            <section class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                        <h5 class="mb-0">Movimientos</h5>
                        @if ($puedeCerrar)
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#movimiento"
                                aria-expanded="false" aria-controls="movimiento">Agregar movimiento</button>
                        @endif
                    </div>
                    @if ($puedeCerrar)
                        <form id="movimiento" class="collapse {{ $errors->any() ? 'show' : '' }} mb-3" method="POST"
                            action="{{ route('admin.cajas.movimiento', $detalle->id) }}">
                            @csrf
                            <label for="tipo" class="form-label">Tipo</label>
                            <select id="tipo" name="tipo" class="form-select mb-2">
                                <option value="ingreso" @selected(old('tipo') === 'ingreso')>Ingreso</option>
                                <option value="egreso" @selected(old('tipo') === 'egreso')>Retiro</option>
                            </select>
                            <label for="concepto" class="form-label">Concepto</label>
                            <input id="concepto" name="concepto" class="form-control mb-2" maxlength="255" value="{{ old('concepto') }}" required>
                            <label for="monto" class="form-label">Monto ({{ $simbolo }})</label>
                            <input id="monto" name="monto" class="form-control mb-2" type="number" min="0.01" max="9999999999.99"
                                step="0.01" value="{{ old('monto') }}" required>
                            <button class="btn btn-primary btn-sm">Registrar movimiento</button>
                        </form>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead><tr><th>Fecha y hora</th><th>Tipo</th><th>Concepto</th><th>Referencia</th><th class="text-end">Monto</th></tr></thead>
                            <tbody>
                                @forelse ($movimientos as $movimiento)
                                    <tr>
                                        <td class="text-nowrap">{{ $fecha($movimiento->created_at) }}</td>
                                        <td>{{ ucfirst($movimiento->tipo) }}</td>
                                        <td>{{ $movimiento->concepto }}</td>
                                        <td>{{ $movimiento->referencia ?: '—' }}</td>
                                        <td class="text-end text-nowrap">{{ $moneda($movimiento->monto) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">Todavía no hay movimientos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $movimientos->links('pagination::bootstrap-5') }}
                </div>
            </section>
        @endif
    </div>
</div>
@endsection
