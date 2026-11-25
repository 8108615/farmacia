@extends('layouts.admin')

@section('content')
@php
    $simboloDivisa = $divisa ?? 'Bs';
    $moneda = fn ($valor) => $simboloDivisa . ' ' . number_format($valor, 2);
    $maximo = max(1, $dias->max('importe'));
    $totalPagos = $pagos->sum();
    $tipos = ['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'mixto' => 'Mixto'];
    $coloresPago = ['efectivo' => '#3478e5', 'tarjeta' => '#16875f', 'transferencia' => '#cf790d', 'mixto' => '#d43356'];
    $segmentosPago = [];
    $acumuladoPago = 0;
    foreach ($coloresPago as $tipo => $colorPago) {
        $cantidadPago = (int) ($pagos[$tipo] ?? 0);
        if ($totalPagos > 0 && $cantidadPago > 0) {
            $inicioPago = 100 * $acumuladoPago / $totalPagos;
            $acumuladoPago += $cantidadPago;
            $finPago = 100 * $acumuladoPago / $totalPagos;
            $segmentosPago[] = sprintf('%s %.6F%% %.6F%%', $colorPago, $inicioPago, $finPago);
        }
    }
    $colores = ['emitida' => 'success', 'cancelada' => 'danger', 'pendiente' => 'warning', 'devuelta' => 'secondary'];
@endphp

<div class="page-heading d-flex justify-content-between flex-wrap gap-2">
    <h3>Ventas realizadas</h3>
    <a href="{{ route('admin.ventas.create') }}" class="btn btn-primary">Nueva venta</a>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['Ventas de hoy', $resumenHoy->cantidad, $moneda($resumenHoy->importe), 'primary', 'bi-cart-check'],
        ['Ventas del mes', $resumenMes->cantidad, $moneda($resumenMes->importe), 'success', 'bi-calendar-check'],
        ['Total recaudado', $moneda($recaudado), 'Ventas emitidas', 'info', 'bi-wallet2'],
        ['Canceladas', $canceladas, 'Ventas canceladas', 'danger', 'bi-x-circle'],
    ] as [$titulo, $valor, $detalle, $color, $icono])
        <div class="col-6 col-xl-3">
            <div class="card h-100 mb-0 border-start border-3 border-{{ $color }}">
                <div class="card-body d-flex flex-wrap flex-sm-nowrap align-items-center gap-3">
                    <span class="ventas-icono bg-{{ $color }} {{ $color === 'info' ? 'text-dark' : 'text-white' }}" aria-hidden="true">
                        <i class="bi {{ $icono }}"></i>
                    </span>
                    <div class="text-break">
                        <h4 class="h6 mb-1">{{ $titulo }}</h4>
                        <div class="fs-5 fw-bold">{{ $valor }}</div>
                        <small class="text-muted">{{ $detalle }}</small>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <section class="card h-100 mb-0">
            <div class="card-body">
                <h4 class="h5">Ventas emitidas en los últimos 7 días</h4>
                @foreach ($dias as $dia)
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <span class="text-nowrap">{{ $dia['fecha'] }}</span>
                        <div class="progress flex-grow-1" aria-hidden="true">
                            <div class="progress-bar" style="width: {{ 100 * $dia['importe'] / $maximo }}%"></div>
                        </div>
                        <span class="text-nowrap">{{ $moneda($dia['importe']) }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
    <div class="col-lg-5">
        <section class="card h-100 mb-0">
            <div class="card-body">
                <h4 class="h5">Ventas emitidas por forma de pago</h4>
                @if ($totalPagos > 0)
                    <div class="ventas-dona mx-auto my-3"
                        style="--ventas-segmentos: {{ implode(', ', $segmentosPago) }}" aria-hidden="true">
                        <strong>{{ $totalPagos }}</strong>
                        <small class="text-muted">{{ $totalPagos == 1 ? 'venta' : 'ventas' }}</small>
                    </div>
                    <ul class="list-unstyled d-flex flex-wrap justify-content-center gap-3 small mb-0">
                        @foreach ($tipos as $tipo => $etiqueta)
                            <li class="d-flex align-items-center gap-1">
                                <span class="ventas-punto" style="--ventas-color: {{ $coloresPago[$tipo] }}" aria-hidden="true"></span>
                                {{ $etiqueta }} <strong>{{ $pagos[$tipo] ?? 0 }}</strong>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">Todavía no hay ventas emitidas.</p>
                @endif
            </div>
        </section>
    </div>
</div>

<section class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="h5 mb-0">Listado de ventas</h4>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-success" href="{{ route('admin.ventas.index', array_merge($filtros, ['exportar' => 'xlsx'])) }}">
                    <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i> Exportar Excel
                </a>
                <a class="btn btn-danger" href="{{ route('admin.ventas.index', array_merge($filtros, ['exportar' => 'pdf'])) }}">
                    <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i> Exportar PDF
                </a>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.ventas.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-md">
                <label for="buscar" class="form-label">Buscar venta</label>
                <input id="buscar" name="buscar" type="search" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}"
                    placeholder="Número, cliente, sucursal o usuario" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado }}" @selected(($filtros['estado'] ?? '') === $estado)>{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
            </div>
            @foreach (['desde' => 'Desde', 'hasta' => 'Hasta'] as $campo => $etiqueta)
                <div class="col-md-auto">
                    <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                    <input id="{{ $campo }}" name="{{ $campo }}" type="date" value="{{ $filtros[$campo] ?? '' }}" class="form-control">
                </div>
            @endforeach
            <div class="col-md-auto">
                <button class="btn btn-primary">Buscar</button>
                <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>

        @if ($errors->any())
            <ul class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr><th>Venta</th><th>Sucursal</th><th>Cliente</th><th>Usuario</th><th>Fecha</th><th>Pago</th><th class="text-end">Total</th><th>Estado</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        <tr>
                            <td>#{{ $venta->id }}</td>
                            <td>{{ $venta->sucursal ?? '—' }}</td>
                            <td>{{ $venta->cliente ?? 'Sin cliente' }}</td>
                            <td>{{ $venta->vendedor ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($venta->tipo_pago) }}</td>
                            <td class="text-end text-nowrap">{{ $moneda($venta->total_venta) }}</td>
                            <td><span class="badge bg-{{ $colores[$venta->estado] ?? 'secondary' }}">{{ ucfirst($venta->estado) }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a class="btn btn-outline-info btn-sm" href="{{ route('admin.ventas.show', $venta) }}" title="Ver detalle" aria-label="Ver detalle">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.ventas.comprobante', ['venta' => $venta, 'formato' => 'pdf']) }}" title="Descargar PDF" aria-label="Descargar PDF">
                                        <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.ventas.comprobante', $venta) }}" title="Abrir comprobante para imprimir" aria-label="Abrir comprobante para imprimir">
                                        <i class="bi bi-printer" aria-hidden="true"></i>
                                    </a>
                                    @if ($venta->estado === 'emitida' && (int) $venta->usuario_id === (int) auth()->id())
                                        <form method="POST" action="{{ route('admin.ventas.cancelar', $venta) }}" data-anular-venta="{{ $venta->id }}" data-importe="{{ $moneda($venta->total_venta) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Cancelar venta" aria-label="Cancelar venta">
                                                <i class="bi bi-x-circle" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No hay ventas para mostrar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($ventas->total())
            <p class="small text-muted">Mostrando {{ $ventas->firstItem() ?? 0 }} a {{ $ventas->lastItem() ?? 0 }} de {{ $ventas->total() }} ventas</p>
        @endif
        {{ $ventas->links('vendor.pagination.bootstrap-5-no-summary') }}
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('form[data-anular-venta]').forEach(form => {
        let confirmando = false;

        form.addEventListener('submit', async event => {
            event.preventDefault();
            if (confirmando) return;

            confirmando = true;
            try {
                const titulo = '¿Anular la venta #' + form.dataset.anularVenta + '?';
                const mensaje = 'Importe: ' + form.dataset.importe + '. Las unidades volverán a sus partidas y la anulación se registrará en el Kardex.';
                const aceptada = typeof Swal === 'undefined'
                    ? window.confirm(titulo + '\n' + mensaje)
                    : (await Swal.fire({
                        titleText: titulo,
                        text: mensaje,
                        icon: 'warning',
                        showCancelButton: true,
                        showCloseButton: true,
                        confirmButtonText: 'Sí, anular venta',
                        cancelButtonText: 'Volver',
                        focusCancel: true,
                        reverseButtons: true,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger m-1',
                            cancelButton: 'btn btn-secondary m-1'
                        }
                    })).isConfirmed;

                if (aceptada) {
                    HTMLFormElement.prototype.submit.call(form);
                    return;
                }
            } catch (error) {
                console.error('No se pudo confirmar la anulación.', error);
            }
            confirmando = false;
        });
    });
</script>
@endpush

@push('styles')
<style>
    .ventas-icono {
        display: grid;
        flex: 0 0 2.75rem;
        width: 2.75rem;
        height: 2.75rem;
        place-items: center;
        border-radius: .75rem;
        font-size: 1.2rem;
    }

    .ventas-icono > .bi {
        display: block;
        line-height: 1;
    }

    .ventas-icono > .bi::before {
        display: block;
        vertical-align: baseline;
    }

    .ventas-dona {
        position: relative;
        display: flex;
        width: 8.5rem;
        height: 8.5rem;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        font-size: 1.4rem;
        line-height: 1.1;
    }

    .ventas-dona::before {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: conic-gradient(var(--ventas-segmentos));
        mask-image: radial-gradient(circle, transparent 53%, #000 54%);
        content: '';
    }

    .ventas-dona small { font-size: .75rem; }
    .ventas-punto {
        width: .65rem;
        height: .65rem;
        border-radius: 50%;
        background: var(--ventas-color);
    }
</style>
@endpush
