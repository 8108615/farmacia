@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Compra completada #{{ $compra->id }}</h3>
                    <p class="text-subtitle text-muted">Resumen profesional de la compra ya registrada y finalizada.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.compras.index') }}">Compras</a></li>
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
                <a href="{{ route('admin.compras.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card resumen-card resumen-card-head shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Fecha de la compra</small>
                        <h6 class="mb-0">{{ optional($compra->fecha_compra)->format('d/m/Y') ?? 'N/A' }}</h6>
                        <br>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card resumen-card resumen-card-head shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Sucursal</small>
                        <h6 class="mb-0">{{ $compra->sucursal?->nombre ?? 'N/A' }}</h6>
                        <small class="subline d-block mt-1">{{ $compra->sucursal?->direccion ?? '&nbsp;' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card resumen-card resumen-card-head shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Proveedor</small>
                        <h6 class="mb-0">{{ $compra->proveedor?->nombre ?? 'N/A' }}</h6>
                        <small
                            class="text-muted subline d-block mt-1">{{ $compra->proveedor?->empresa ?? 'Sin empresa' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card resumen-card resumen-card-head shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Registrado por</small>
                        <h6 class="mb-0">{{ $compra->usuario?->name ?? 'Usuario desconocido' }}</h6>
                        <small class="subline d-block mt-1">{{ $compra->usuario?->email ?? '&nbsp;' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card resumen-card resumen-card-metric border-start border-primary border-3 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Productos</small>
                        <h4 class="mb-0">{{ $totalItems }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card resumen-card resumen-card-metric border-start border-info border-3 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Cantidad total</small>
                        <h4 class="mb-0">{{ $totalCantidad }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card resumen-card resumen-card-metric border-start border-success border-3 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Monto total</small>
                        <h4 class="mb-0">{{ $ajuste->divisa ?? 'Bs.' }}
                            {{ number_format((float) $compra->total, 2, '.', ',') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-xl-4">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Información</h5>
                        @php
                            $estadoClass = match (strtolower($compra->estado ?? '')) {
                                'completada' => 'bg-success',
                                'pendiente' => 'bg-warning text-dark',
                                'cancelada' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $estadoClass }} fs-6">{{ ucfirst($compra->estado ?? 'N/A') }}</span>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">Comprobante</dt>
                            <dd class="col-7">{{ $compra->comprobante ?? 'No disponible' }}</dd>

                            <dt class="col-5 text-muted">Total de ítems</dt>
                            <dd class="col-7">{{ $totalItems }}</dd>

                            <dt class="col-5 text-muted">Total de unidades</dt>
                            <dd class="col-7">{{ $totalCantidad }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Resumen y notas</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Aquí está el detalle completo de la compra terminada, junto con cualquier
                            comentario adicional registrado al momento de la compra.</p>

                        @if (!empty($compra->nota))
                            <div class="alert alert-light border-start border-info border-3 shadow-sm">
                                <h6 class="mb-1">Nota del registro</h6>
                                <p class="mb-0">{{ $compra->nota }}</p>
                            </div>
                        @else
                            <div class="alert alert-secondary mb-0">
                                <i class="bi bi-info-circle me-2"></i> No se registraron notas para esta compra.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalle de productos</h5>
                <span class="text-muted">{{ $detalles->count() }} registros</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Producto</th>
                                <th>Lote</th>
                                <th>Fabricación</th>
                                <th>Vencimiento</th>
                                <th>Ubicación física</th>
                                <th class="text-center">Stock mín.</th>
                                <th class="text-center">Stock máx.</th>
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
                                        <div class="fw-semibold">{{ $detalle->producto?->nombre_comercial ?? 'Producto' }}
                                        </div>
                                        <small class="text-muted d-block">Código:
                                            {{ $detalle->producto?->codigo_producto ?? 'N/A' }}</small>
                                        @if (!empty($detalle->producto?->nombre_generico))
                                            <small class="text-muted d-block">Genérico:
                                                {{ $detalle->producto->nombre_generico }}</small>
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
                                    <td>{{ $detalle->lote?->nombre ?? 'N/A' }}</td>
                                    <td>{{ $detalle->lote?->fecha_fabricacion ? $detalle->lote->fecha_fabricacion->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td>{{ $detalle->lote?->fecha_vencimiento ? $detalle->lote->fecha_vencimiento->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td>{{ $detalle->ubicacion_nombre ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $detalle->stock_minimo ?? 0 }}</td>
                                    <td class="text-center">{{ $detalle->stock_maximo ?? 0 }}</td>
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
                                    <td colspan="13" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox"></i> No se encontraron artículos en esta compra.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="12" class="text-end fw-bold">Total compra</td>
                                <td class="text-end fw-bold">{{ $ajuste->divisa ?? 'Bs.' }}
                                    {{ number_format((float) $compra->total, 2, '.', ',') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .resumen-card {
            min-height: 0 !important;
            margin-bottom: 0 !important;
        }

        .resumen-card .card-body {
            padding: 0.9rem 1rem;
            min-height: 0 !important;
        }

        .resumen-card small {
            font-size: 0.78rem;
            line-height: 1.25;
        }

        .resumen-card h6 {
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .resumen-card-head .subline {
            min-height: 1.15rem;
            line-height: 1.2;
            font-size: 0.8rem;
        }

        .resumen-card-metric {
            min-height: 0 !important;
        }

        .resumen-card-metric .card-body {
            padding: 0.9rem 1rem !important;
        }

        .resumen-card-metric h4 {
            font-size: 1.8rem;
            margin-bottom: 0;
        }
    </style>
@endpush
