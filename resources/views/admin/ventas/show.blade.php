@extends('layouts.admin')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-start flex-wrap gap-2">
    <h3>Venta #{{ $venta->id }}</h3>
    <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-primary">Volver a ventas</a>
</div>
<div class="card">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Cliente</dt><dd class="col-sm-9">{{ $cliente?->nombres_apellidos ?? '—' }}</dd>
            <dt class="col-sm-3">Sucursal</dt><dd class="col-sm-9">{{ $sucursal?->nombre ?? '—' }}</dd>
            <dt class="col-sm-3">Fecha</dt><dd class="col-sm-9">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</dd>
            <dt class="col-sm-3">Forma de pago</dt><dd class="col-sm-9">{{ ucfirst($venta->tipo_pago) }}</dd>
            <dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ ucfirst($venta->estado) }}</dd>
        </dl>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr><th>Producto</th><th>Lote</th><th>Partida</th><th class="text-end">Cantidad</th><th class="text-end">Precio unitario</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                    @forelse ($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto ?? '—' }}</td>
                            <td>{{ $detalle->lote ?? '—' }}</td>
                            <td>#{{ $detalle->inventario_id }}</td>
                            <td class="text-end">{{ $detalle->cantidad }}</td>
                            <td class="text-end">{{ $divisa }} {{ number_format($detalle->precio_venta_unidad, 2) }}</td>
                            <td class="text-end">{{ $divisa }} {{ number_format($detalle->cantidad * $detalle->precio_venta_unidad, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Esta venta no tiene partidas disponibles.</td></tr>
                    @endforelse
                </tbody>
                <tfoot><tr><th colspan="5" class="text-end">Total</th><td class="text-end fw-bold">{{ $divisa }} {{ number_format($venta->total_venta, 2) }}</td></tr></tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
