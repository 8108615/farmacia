@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detalle de Orden #{{ $compra->id }}</h3>
                    <p class="text-subtitle text-muted">Vista detallada y profesional de la compra registrada.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.ordenes_compra.index') }}">Ordenes de
                                    Compra</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('admin.ordenes_compra.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-3">
                <div class="card resumen-card resumen-card-head">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Fecha de Compra</small>
                        <h6 class="mb-0">{{ \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y') }}</h6>
                        <small class="subline d-block mt-1" aria-hidden="true">&nbsp;</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card resumen-card resumen-card-head">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Sucursal</small>
                        <h6 class="mb-0">{{ $compra->sucursal?->nombre ?? 'N/A' }}</h6>
                        <small class="subline d-block mt-1" aria-hidden="true">&nbsp;</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card resumen-card resumen-card-head">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Proveedor</small>
                        <h6 class="mb-0">{{ $compra->proveedor?->nombre ?? 'N/A' }}</h6>
                        @if (!empty($compra->proveedor?->empresa))
                            <small class="text-muted subline d-block mt-1">{{ $compra->proveedor?->empresa }}</small>
                        @else
                            <small class="subline d-block mt-1" aria-hidden="true">&nbsp;</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card resumen-card resumen-card-head">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Registrado por</small>
                        <h6 class="mb-0">{{ $compra->usuario?->name ?? 'N/A' }}</h6>
                        <small class="subline d-block mt-1" aria-hidden="true">&nbsp;</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="card resumen-card resumen-card-metric border-start border-primary border-3">
                    <div class="card-body">
                        <small class="text-muted d-block mb-0">Items</small>
                        <h4 class="mb-0">{{ $totalItems }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card resumen-card resumen-card-metric border-start border-info border-3">
                    <div class="card-body">
                        <small class="text-muted d-block mb-0">Cantidad Total</small>
                        <h4 class="mb-0">{{ $totalCantidad }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card resumen-card resumen-card-metric border-start border-success border-3">
                    <div class="card-body">
                        <small class="text-muted d-block mb-0">Monto Total</small>
                        <h4 class="mb-0">{{ $ajuste->divisa ?? 'Bs.' }}
                            {{ number_format((float) $compra->total, 2, '.', ',') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalle de Productos</h5>
                @php
                    $estadoClass = match (strtolower($compra->estado ?? '')) {
                        'activo' => 'bg-success',
                        'pendiente' => 'bg-warning text-dark',
                        'confirmado' => 'bg-primary',
                        'cancelado' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                @endphp
                <span class="badge {{ $estadoClass }} fs-6">{{ ucfirst($compra->estado ?? 'N/A') }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">P. Compra</th>
                                <th class="text-end">P. Venta</th>
                                <th class="text-center">% Ganancia</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detalles as $detalle)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $detalle->producto?->nombre_comercial ?? 'N/A' }}</div>
                                        <small class="text-muted d-block">Código: {{ $detalle->producto?->codigo_producto ?? 'N/A' }}</small>
                                        @if (!empty($detalle->producto?->nombre_generico))
                                            <small class="text-muted d-block">Genérico: {{ $detalle->producto->nombre_generico }}</small>
                                        @endif
                                        <small class="text-muted d-block">
                                            Presentación: {{ $detalle->producto?->presentacion?->nombre ?? 'N/A' }}
                                            | Forma: {{ $detalle->producto?->formaFarmaceutica?->nombre ?? 'N/A' }}
                                        </small>
                                        <small class="text-muted d-block">
                                            Categoría: {{ $detalle->producto?->categoria?->nombre ?? 'N/A' }}
                                            | Laboratorio: {{ $detalle->producto?->laboratorio?->nombre ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-end">{{ $ajuste->divisa ?? 'Bs.' }}
                                        {{ number_format((float) $detalle->precio_compra_unidad, 2, '.', ',') }}</td>
                                    <td class="text-end">{{ $ajuste->divisa ?? 'Bs.' }}
                                        {{ number_format((float) $detalle->precio_venta_unidad, 2, '.', ',') }}</td>
                                    <td class="text-center">
                                        {{ number_format((float) $detalle->porcentaje_ganancia_unidad, 2, '.', ',') }}%
                                    </td>
                                    <td class="text-end fw-semibold">{{ $ajuste->divisa ?? 'Bs.' }}
                                        {{ number_format((float) $detalle->cantidad * (float) $detalle->precio_compra_unidad, 2, '.', ',') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox"></i> Esta compra no tiene detalles registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Total Compra:</td>
                                <td class="text-end fw-bold">{{ $ajuste->divisa ?? 'Bs.' }}
                                    {{ number_format((float) $compra->total, 2, '.', ',') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if (!empty($compra->nota))
                    <div class="alert alert-light border mt-3 mb-0">
                        <strong>Nota:</strong> {{ $compra->nota }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .resumen-card {
            height: auto !important;
            min-height: 0 !important;
            margin-bottom: 0 !important;
        }

        .resumen-card .card-body {
            padding: 0.7rem 1rem;
            min-height: 0 !important;
        }

        .resumen-card small {
            font-size: 0.78rem;
            line-height: 1.2;
        }

        .resumen-card h6 {
            font-size: 1.15rem;
            line-height: 1.25;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .resumen-card-head .subline {
            min-height: 1.1rem;
            line-height: 1.15;
            font-size: 0.75rem;
        }

        .resumen-card h4 {
            font-size: 1.75rem;
            line-height: 1.15;
        }

        .resumen-card-metric .card-body {
            padding: 0.65rem 1rem !important;
            min-height: 0 !important;
        }

        .resumen-card-metric small {
            margin-bottom: 0.2rem !important;
            line-height: 1.2;
        }

        .resumen-card-metric h4 {
            line-height: 1.15;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .resumen-card-metric {
            height: auto !important;
            min-height: 0 !important;
        }
    </style>
@endpush
