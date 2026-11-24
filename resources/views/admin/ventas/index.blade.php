@extends('layouts.admin')

@section('content')
@php
    $simboloDivisa = $divisa ?? 'Bs';
    $moneda = fn ($valor) => $simboloDivisa . ' ' . number_format($valor, 2);
    $maximo = max(1, $dias->max('importe'));
    $totalPagos = $pagos->sum();
    $tipos = ['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'mixto' => 'Mixto'];
    $colores = ['emitida' => 'success', 'cancelada' => 'danger', 'pendiente' => 'warning', 'devuelta' => 'secondary'];
@endphp

<div class="page-heading d-flex justify-content-between flex-wrap gap-2">
    <h3>Ventas realizadas</h3>
    <a href="{{ route('admin.ventas.create') }}" class="btn btn-primary">Nueva venta</a>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['Ventas de hoy', $resumenHoy->cantidad, $moneda($resumenHoy->importe), 'primary'],
        ['Ventas del mes', $resumenMes->cantidad, $moneda($resumenMes->importe), 'success'],
        ['Total recaudado', $moneda($recaudado), 'Ventas emitidas', 'info'],
        ['Canceladas', $canceladas, 'Ventas canceladas', 'danger'],
    ] as [$titulo, $valor, $detalle, $color])
        <div class="col-6 col-xl-3">
            <div class="card h-100 mb-0 border-start border-3 border-{{ $color }}">
                <div class="card-body">
                    <h4 class="h6">{{ $titulo }}</h4>
                    <div class="fs-5 fw-bold">{{ $valor }}</div>
                    <small class="text-muted">{{ $detalle }}</small>
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
                @if (!$totalPagos)
                    <p class="text-muted">Todavía no hay ventas emitidas.</p>
                @endif
                @foreach ($tipos as $tipo => $etiqueta)
                    @php($cantidad = $pagos[$tipo] ?? 0)
                    <div class="d-flex justify-content-between mt-3">
                        <span>{{ $etiqueta }}</span><strong>{{ $cantidad }}</strong>
                    </div>
                    <div class="progress" aria-hidden="true">
                        <div class="progress-bar bg-success" style="width: {{ $totalPagos ? 100 * $cantidad / $totalPagos : 0 }}%"></div>
                    </div>
                @endforeach
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
