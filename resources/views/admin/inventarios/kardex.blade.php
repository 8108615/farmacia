@extends('layouts.admin')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h3>Kardex de inventario</h3>
            <p class="text-muted mb-0">Historial de movimientos y saldos por partida.</p>
        </div>
        <a href="{{ route('admin.inventarios.index') }}" class="btn btn-outline-primary">Volver a Inventario</a>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.inventarios.kardex') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="sucursal_id" class="form-label">Sucursal</label>
                        <select id="sucursal_id" name="sucursal_id" class="form-select">
                            <option value="">Todas las sucursales</option>
                            @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" @selected((string) ($filtros['sucursal_id'] ?? '') === (string) $sucursal->id)>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md">
                        <label for="buscar" class="form-label">Producto, código o lote</label>
                        <input type="search" id="buscar" name="buscar" class="form-control"
                               value="{{ $filtros['buscar'] ?? '' }}" maxlength="150">
                    </div>
                    @foreach (['desde' => 'Desde', 'hasta' => 'Hasta'] as $campo => $etiqueta)
                        <div class="col-6 col-md-2">
                            <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                            <input type="date" id="{{ $campo }}" name="{{ $campo }}" class="form-control"
                                   value="{{ $filtros[$campo] ?? '' }}">
                        </div>
                    @endforeach
                    <div class="col-12 col-xl-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('admin.inventarios.kardex') }}" class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>

                @if ($errors->any())
                    <ul class="text-danger mb-0 mt-3" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Historial de inventario</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Fecha y hora</th>
                                <th scope="col">Producto</th>
                                <th scope="col">Lote</th>
                                <th scope="col">Sucursal</th>
                                <th scope="col">Partida</th>
                                <th scope="col">Movimiento</th>
                                <th scope="col" class="text-end">Variación</th>
                                <th scope="col" class="text-end">Stock anterior</th>
                                <th scope="col" class="text-end">Stock posterior</th>
                                <th scope="col">Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($movimientos as $movimiento)
                                <tr>
                                    <td class="text-nowrap">{{ $movimiento->ocurrido_en->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <strong>{{ $movimiento->nombre_producto }}</strong>
                                        <div class="text-muted small">{{ $movimiento->codigo_producto }}</div>
                                    </td>
                                    <td>{{ $movimiento->numero_lote }}</td>
                                    <td>{{ $movimiento->nombre_sucursal }}</td>
                                    <td>#{{ $movimiento->inventario_id }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $movimiento->tipo)) }}</td>
                                    <td class="text-end">{{ $movimiento->cantidad > 0 ? '+' : '' }}{{ $movimiento->cantidad }}</td>
                                    <td class="text-end">{{ $movimiento->stock_anterior }}</td>
                                    <td class="text-end fw-semibold">{{ $movimiento->stock_posterior }}</td>
                                    <td>{{ $movimiento->referencia ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        No hay movimientos que coincidan con los filtros.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($movimientos->hasPages())
                    <div class="mt-3">
                        {{ $movimientos->links('vendor.pagination.bootstrap-5-no-summary') }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
