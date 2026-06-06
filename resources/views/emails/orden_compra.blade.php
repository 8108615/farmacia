<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden de compra #{{ $compra->id }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f2f4f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #354052;
        }

        .email-wrapper {
            width: 100%;
            padding: 20px 0;
            background-color: #f2f4f8;
        }

        .email-content {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 16px 48px rgba(52, 64, 82, 0.12);
        }

        .email-header {
            background-color: #0d6efd;
            color: #ffffff;
            padding: 28px 32px;
        }

        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .email-header p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 15px;
        }

        .section {
            padding: 24px 32px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #102a43;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .detail-card {
            background-color: #fbfcfd;
            border: 1px solid #e6ebf1;
            border-radius: 12px;
            padding: 18px;
        }

        .detail-card p {
            margin: 6px 0;
            font-size: 14px;
            line-height: 1.6;
        }

        .detail-card strong {
            color: #102a43;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        table th,
        table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e8eef6;
            text-align: left;
        }

        table th {
            background-color: #f4f6fb;
            color: #102a43;
            font-weight: 700;
        }

        tbody tr:nth-child(even) {
            background-color: #fbfcfd;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .summary-card {
            background-color: #e7f1ff;
            border-radius: 12px;
            padding: 16px 20px;
            min-width: 220px;
        }

        .summary-card strong {
            display: block;
            font-size: 14px;
            color: #102a43;
            margin-bottom: 6px;
        }

        .summary-card span {
            font-size: 18px;
            font-weight: 700;
            color: #0d6efd;
        }

        .note {
            background-color: #f8f9fb;
            border: 1px solid #dae1e8;
            border-radius: 12px;
            padding: 18px 20px;
            font-size: 14px;
            line-height: 1.7;
            color: #354052;
        }

        .footer {
            padding: 20px 32px 28px;
            font-size: 13px;
            color: #66788a;
            background-color: #f9fbff;
        }

        .footer p {
            margin: 0;
        }

        @media (max-width: 600px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .summary {
                justify-content: stretch;
            }

            .summary-card {
                min-width: auto;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <h1>Orden de compra #{{ $compra->id }}</h1>
                <p>Fecha: {{ $compra->fecha_compra->format('d/m/Y') }}</p>
            </div>

            <div class="section">
                <div class="section-title">Información de la orden</div>
                <div class="details-grid">
                    <div class="detail-card">
                        <p><strong>Proveedor</strong></p>
                        <p>{{ $compra->proveedor->nombre }}</p>
                        <p><strong>Email</strong></p>
                        <p>{{ $compra->proveedor->email ?? 'No disponible' }}</p>
                        <p><strong>Teléfono</strong></p>
                        <p>{{ $compra->proveedor->telefono ?? 'No disponible' }}</p>
                    </div>
                    <div class="detail-card">
                        <p><strong>Sucursal</strong></p>
                        <p>{{ $compra->sucursal->nombre }}</p>
                        <p><strong>Usuario</strong></p>
                        <p>{{ $compra->usuario->name }}</p>
                        <p><strong>Total</strong></p>
                        <p>{{ $ajuste->divisa ?? 'Bs.' }} {{ number_format((float) $compra->total, 2, '.', ',') }}</p>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">P. Compra</th>
                                <th class="text-right">P. Venta</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($compra->detalles as $detalle)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $detalle->producto?->nombre_comercial ?? 'N/A' }}</strong>
                                        @if (!empty($detalle->producto?->nombre_generico))
                                            <div style="font-size: 13px; color: #5f6f88; margin-top: 4px;">
                                                {{ $detalle->producto->nombre_generico }}</div>
                                        @endif
                                        <div
                                            style="font-size: 12px; color: #7a869a; margin-top: 8px; line-height: 1.4;">
                                            @if (!empty($detalle->producto?->laboratorio?->nombre))
                                                Laboratorio: {{ $detalle->producto->laboratorio->nombre }}<br>
                                            @endif
                                            @if (!empty($detalle->producto?->formaFarmaceutica?->nombre))
                                                Forma: {{ $detalle->producto->formaFarmaceutica->nombre }}<br>
                                            @endif
                                            @if (!empty($detalle->producto?->presentacion?->nombre))
                                                Presentación: {{ $detalle->producto->presentacion->nombre }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-right">{{ $detalle->cantidad }}</td>
                                    <td class="text-right">{{ $ajuste->divisa ?? 'Bs.' }}
                                        {{ number_format((float) $detalle->precio_compra_unidad, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ $ajuste->divisa ?? 'Bs.' }}
                                        {{ number_format((float) $detalle->precio_venta_unidad, 2, '.', ',') }}</td>
                                    <td class="text-right">{{ $ajuste->divisa ?? 'Bs.' }}
                                        {{ number_format((float) $detalle->cantidad * (float) $detalle->precio_compra_unidad, 2, '.', ',') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="summary">
                    <div class="summary-card">
                        <strong>Subtotal</strong>
                        <span>{{ $ajuste->divisa ?? 'Bs.' }}
                            {{ number_format((float) $compra->total, 2, '.', ',') }}</span>
                    </div>
                </div>

                @if (!empty($compra->nota))
                    <div class="note">
                        <strong>Nota de la orden</strong>
                        <p>{{ $compra->nota }}</p>
                    </div>
                @endif
            </div>

            <div class="footer">
                <p>Gracias por usar el sistema de farmacia. Si necesitas más información sobre esta orden, responde
                    a este correo.</p>
            </div>
        </div>
    </div>
</body>

</html>
