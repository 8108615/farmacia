<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de cierre #{{ $detalle->id }}</title>
    <style>
        body { font: 12px "DejaVu Sans", sans-serif; color: #17243b; margin: 38px; }
        h1 { font-size: 24px; margin: 0 0 6px; }
        h2 { font-size: 15px; margin: 24px 0 8px; }
        p { margin: 6px 0; }
        .referencia { color: #475569; border-bottom: 2px solid #0f766e; padding-bottom: 14px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 9px 10px; border-bottom: 1px solid #d6dee8; }
        th { text-align: left; font-weight: normal; }
        .importe { text-align: right; }
        .resultado { background: #e9f5f3; font-weight: bold; }
        .observaciones { white-space: pre-wrap; overflow-wrap: break-word; }
        .firma { width: 260px; margin: 64px auto 0; text-align: center; page-break-inside: avoid; }
        .firma-nombre { border-top: 1px solid #17243b; padding-top: 8px; font-weight: bold; }
    </style>
</head>
<body>
@php
    $simbolo = $divisa ?? 'Bs';
    $moneda = fn ($valor) => $simbolo . ' ' . number_format($valor, 2);
    $fecha = fn ($valor) => $valor ? \Illuminate\Support\Carbon::parse($valor)->format('d/m/Y H:i') : '—';
@endphp

<h1>Comprobante de cierre de caja</h1>
<p class="referencia">Arqueo #{{ $detalle->id }} · Cerrado</p>

<h2>Datos del turno</h2>
<table>
    <tr><th scope="row">Caja</th><td>{{ $detalle->caja }}</td></tr>
    <tr><th scope="row">Sucursal</th><td>{{ $detalle->sucursal }}</td></tr>
    <tr><th scope="row">Responsable</th><td>{{ $detalle->usuario }}</td></tr>
    <tr><th scope="row">Apertura</th><td>{{ $fecha($detalle->fecha_apertura) }}</td></tr>
    <tr><th scope="row">Cierre</th><td>{{ $fecha($detalle->fecha_cierre) }}</td></tr>
</table>

<h2>Resumen de efectivo</h2>
<table>
    <tr><th scope="row">Fondo inicial</th><td class="importe">{{ $moneda($detalle->monto_inicial) }}</td></tr>
    <tr><th scope="row">Ingresos</th><td class="importe">{{ $moneda($detalle->total_ingresos) }}</td></tr>
    <tr><th scope="row">Egresos</th><td class="importe">{{ $moneda($detalle->total_egresos) }}</td></tr>
    <tr><th scope="row">Efectivo esperado</th><td class="importe">{{ $moneda($esperado) }}</td></tr>
    <tr><th scope="row">Efectivo contado</th><td class="importe">{{ $moneda($detalle->monto_contado) }}</td></tr>
    <tr class="resultado"><th scope="row">Diferencia</th><td class="importe">{{ $moneda($detalle->diferencia) }}</td></tr>
</table>

@if (filled($detalle->observaciones))
    <h2>Observaciones</h2>
    <p class="observaciones">{{ $detalle->observaciones }}</p>
@endif
<div class="firma">
    <p class="firma-nombre">{{ $detalle->usuario }}</p>
    <p>Firma del responsable</p>
</div>
</body>
</html>
