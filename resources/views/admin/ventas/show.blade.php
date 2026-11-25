@extends('layouts.admin')

@section('content')
@php
    $simbolo = $divisa ?? 'Bs';
    $moneda = fn ($importe) => $simbolo . ' ' . number_format($importe, 2);
    $colorEstado = match ($venta->estado) {
        'emitida' => 'success',
        'cancelada' => 'danger',
        'pendiente' => 'warning text-dark',
        default => 'secondary',
    };
@endphp

<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0">Detalle de venta #{{ $venta->id }}</h3>
    <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Volver a ventas
    </a>
</div>

<div class="row g-3 mb-3">
    @foreach ([
        ['Fecha de venta', \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y'), null],
        ['Sucursal', $sucursal?->nombre ?? '—', null],
        ['Cliente', $cliente?->nombres_apellidos ?? 'Sin cliente', $cliente?->ci_nit],
        ['Registrado por', $vendedor ?? '—', null],
    ] as [$etiqueta, $valor, $nota])
        <div class="col-6 col-lg-3">
            <div class="card h-100 mb-0">
                <div class="card-body py-3">
                    <div class="small text-muted">{{ $etiqueta }}</div>
                    <div class="fw-semibold text-break">{{ $valor }}</div>
                    @if (filled($nota))
                        <small class="text-muted">CI/NIT {{ $nota }}</small>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-3">
    @foreach ([
        ['Partidas', $detalles->count(), 'primary'],
        ['Unidades', $detalles->sum('cantidad'), 'info'],
        ['Total de venta', $moneda($venta->total_venta), 'success'],
        ['Forma de pago', ucfirst($venta->tipo_pago), 'warning'],
    ] as [$etiqueta, $valor, $color])
        <div class="col-6 col-lg-3">
            <div class="card h-100 mb-0 border-start border-3 border-{{ $color }}">
                <div class="card-body py-3">
                    <div class="small text-muted">{{ $etiqueta }}</div>
                    <div class="fw-bold fs-5">{{ $valor }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<section class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h4 class="h5 mb-0">Detalle de productos</h4>
            <span class="badge bg-{{ $colorEstado }}">{{ ucfirst($venta->estado) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">#</th><th scope="col">Producto</th><th scope="col">Lote / Partida</th>
                        <th scope="col" class="text-end">Cantidad</th>
                        <th scope="col" class="text-end">Precio unitario</th>
                        <th scope="col" class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($detalles as $detalle)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $detalle->producto ?? '—' }}</strong>
                                @if (filled($detalle->codigo_producto))
                                    <small class="d-block text-muted">Código {{ $detalle->codigo_producto }}</small>
                                @endif
                                @if (filled($detalle->nombre_generico))
                                    <small class="d-block text-muted">{{ $detalle->nombre_generico }}</small>
                                @endif
                                @if (filled($detalle->concentracion))
                                    <small class="d-block text-muted">{{ $detalle->concentracion }} {{ $detalle->unidad_medida }}</small>
                                @endif
                            </td>
                            <td>{{ $detalle->lote ?? '—' }} <small class="text-muted">(#{{ $detalle->inventario_id }})</small></td>
                            <td class="text-end">{{ $detalle->cantidad }}</td>
                            <td class="text-end text-nowrap">{{ $moneda($detalle->precio_venta_unidad) }}</td>
                            <td class="text-end text-nowrap">{{ $moneda($detalle->cantidad * $detalle->precio_venta_unidad) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Esta venta no tiene partidas disponibles.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row" colspan="5" class="text-end">Total de venta</th>
                        <td class="text-end text-nowrap fw-bold">{{ $moneda($venta->total_venta) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if ($venta->monto_recibido !== null && $venta->cambio !== null)
            <dl class="d-flex flex-wrap gap-4 mt-3 mb-0">
                <div>
                    <dt class="small text-muted">Monto recibido</dt>
                    <dd class="fw-semibold mb-0">{{ $moneda($venta->monto_recibido) }}</dd>
                </div>
                <div>
                    <dt class="small text-muted">Cambio</dt>
                    <dd class="fw-semibold mb-0">{{ $moneda($venta->cambio) }}</dd>
                </div>
            </dl>
        @endif
    </div>
</section>
@endsection
