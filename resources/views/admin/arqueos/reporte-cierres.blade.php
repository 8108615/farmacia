@extends('layouts.admin')

@section('content')
@php
    $simbolo = $divisa ?? 'Bs';
    $moneda = fn ($valor) => $simbolo . ' ' . number_format($valor, 2);
@endphp

<div class="page-heading d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h3 class="mb-0">Reporte de cierres de caja</h3>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.arqueos.reporte-cierres', array_replace($filtros, ['desde' => $desde, 'hasta' => $hasta, 'exportar' => 'xlsx'])) }}"
            class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i>Exportar Excel
        </a>
        <a href="{{ route('admin.arqueos.index') }}" class="btn btn-outline-secondary">Volver a arqueos</a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.arqueos.reporte-cierres') }}" class="row g-3 align-items-end">
            <div class="col-sm-6 col-lg-2">
                <label for="desde" class="form-label">Cierre desde</label>
                <input type="date" id="desde" name="desde" value="{{ old('desde', $desde) }}" class="form-control" required>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label for="hasta" class="form-label">Cierre hasta</label>
                <input type="date" id="hasta" name="hasta" value="{{ old('hasta', $hasta) }}" class="form-control" required>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label for="sucursal" class="form-label">Sucursal</label>
                <select name="sucursal" id="sucursal" class="form-select">
                    <option value="">Todas las sucursales</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" @selected((string) old('sucursal', $filtros['sucursal'] ?? '') === (string) $sucursal->id)>{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label for="caja" class="form-label">Caja</label>
                <select name="caja" id="caja" class="form-select">
                    <option value="">Todas las cajas</option>
                    @foreach ($cajas as $caja)
                        <option value="{{ $caja->id }}" @selected((string) old('caja', $filtros['caja'] ?? '') === (string) $caja->id)>{{ $caja->nombre }} · {{ $caja->sucursal?->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 d-flex gap-2 flex-wrap">
                <button class="btn btn-primary">Filtrar</button>
                <a href="{{ route('admin.arqueos.reporte-cierres') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    @foreach ([
        ['Turnos cerrados', number_format($resumen->turnos), 'primary'],
        ['Sobrantes', $moneda($resumen->sobrantes), 'success'],
        ['Faltantes', $moneda($resumen->faltantes), 'danger'],
    ] as [$titulo, $valor, $color])
        <div class="col-sm-4">
            <div class="card border-start border-{{ $color }} mb-0 h-100">
                <div class="card-body">
                    <div>{{ $titulo }}</div>
                    <strong class="fs-4">{{ $valor }}</strong>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Turnos cerrados</h5>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th scope="col">Turno</th><th scope="col">Caja / Sucursal</th><th scope="col">Responsable</th>
                        <th scope="col">Cierre</th><th scope="col" class="text-end">Esperado</th>
                        <th scope="col" class="text-end">Contado</th><th scope="col" class="text-end">Diferencia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cierres as $cierre)
                        <tr>
                            <td><a href="{{ route('admin.arqueos.show', $cierre->id) }}" aria-label="Ver detalle del turno {{ $cierre->id }}">#{{ $cierre->id }}</a></td>
                            <td>{{ $cierre->caja }}<small class="d-block text-muted">{{ $cierre->sucursal }}</small></td>
                            <td>{{ $cierre->responsable }}</td>
                            <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($cierre->fecha_cierre)->format('d/m/Y H:i') }}</td>
                            <td class="text-end text-nowrap">{{ $moneda($cierre->esperado) }}</td>
                            <td class="text-end text-nowrap">{{ $moneda($cierre->monto_contado) }}</td>
                            <td class="text-end text-nowrap {{ $cierre->diferencia < 0 ? 'text-danger' : ($cierre->diferencia > 0 ? 'text-success' : '') }}">
                                {{ $cierre->diferencia > 0 ? '+' : '' }}{{ $moneda($cierre->diferencia) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No hay turnos cerrados para estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $cierres->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
