<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante de venta #{{ $venta->id }}</title>
    <style>
        body { font: 11pt DejaVu Sans, sans-serif; color: #17243b; margin: 24px; }
        h1 { font-size: 19pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; border-bottom: 1px solid #dce1e9; padding: 7px; }
        th { background: #e9eef6; }
        .importe { text-align: right; white-space: nowrap; }
        @media print { .acciones { display: none; } }
    </style>
</head>
<body>
    @unless ($descarga)
        <div class="acciones">
            <button type="button" onclick="window.print()">Imprimir comprobante</button>
            <a href="{{ route('admin.ventas.index') }}">Volver a ventas</a>
        </div>
    @endunless

    <h1>Comprobante de venta #{{ $venta->id }}</h1>
    <p>
        <strong>Sucursal</strong> {{ $sucursal?->nombre ?? '—' }}<br>
        <strong>Cliente</strong> {{ $cliente?->nombres_apellidos ?? 'Sin cliente' }}<br>
        <strong>Usuario</strong> {{ $vendedor ?? '—' }}<br>
        <strong>Fecha</strong> {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}<br>
        <strong>Forma de pago</strong> {{ ucfirst($venta->tipo_pago) }}<br>
        <strong>Estado</strong> {{ ucfirst($venta->estado) }}
    </p>

    <table>
        <thead>
            <tr><th>Producto</th><th>Lote</th><th>Partida</th><th>Cantidad</th><th class="importe">Precio unitario</th><th class="importe">Subtotal</th></tr>
        </thead>
        <tbody>
            @forelse ($detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto ?? '—' }}</td>
                    <td>{{ $detalle->lote ?? '—' }}</td>
                    <td>#{{ $detalle->inventario_id }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td class="importe">{{ $divisa }} {{ number_format($detalle->precio_venta_unidad, 2) }}</td>
                    <td class="importe">{{ $divisa }} {{ number_format($detalle->cantidad * $detalle->precio_venta_unidad, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Esta venta no tiene partidas disponibles.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="importe"><strong>Total {{ $divisa }} {{ number_format($venta->total_venta, 2) }}</strong></p>
</body>
</html>
