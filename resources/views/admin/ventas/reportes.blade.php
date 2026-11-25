@extends('layouts.admin')

@section('content')
<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h3 class="mb-0">Reportes de ventas</h3>
    <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al listado
    </a>
</div>

<div class="page-content">
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3 mb-4">
        @foreach ($reportes as $clave => [$titulo, $descripcion, $icono, $color])
            <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
                <section class="card h-100 mb-0 {{ $reporte === $clave ? 'border border-primary' : '' }}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="d-flex align-items-center gap-2 mb-2">
                            <span class="d-inline-flex p-2 rounded bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                <i class="bi bi-{{ $icono }}" aria-hidden="true"></i>
                            </span>
                            {{ $titulo }}
                        </h5>
                        <p class="small mb-3">{{ $descripcion }}</p>
                        <form class="mt-auto" action="{{ route('admin.ventas.reportes') }}#resultado-reporte" method="GET">
                            <input type="hidden" name="reporte" value="{{ $clave }}">
                            @if ($clave === 'cliente')
                                <label for="reporte-cliente" class="form-label small">Cliente</label>
                                <select id="reporte-cliente" name="cliente" class="form-select form-select-sm mb-3" required>
                                    <option value="">Seleccionar cliente</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" @selected((string) ($filtros['cliente'] ?? '') === (string) $cliente->id)>
                                            {{ $cliente->nombres_apellidos }}{{ $cliente->ci_nit ? ' · ' . $cliente->ci_nit : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small" for="{{ $clave }}-desde">Desde</label>
                                    <input type="date" class="form-control form-control-sm" name="desde"
                                        id="{{ $clave }}-desde" value="{{ $filtros['desde'] }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small" for="{{ $clave }}-hasta">Hasta</label>
                                    <input type="date" class="form-control form-control-sm" name="hasta"
                                        id="{{ $clave }}-hasta" value="{{ $filtros['hasta'] }}" required>
                                </div>
                            </div>
                            @if ($clave === 'sucursal')
                                <label for="reporte-sucursal" class="form-label small">Sucursal</label>
                                <select id="reporte-sucursal" name="sucursal" class="form-select form-select-sm mb-3">
                                    <option value="">Todas las sucursales</option>
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}" @selected((string) ($filtros['sucursal'] ?? '') === (string) $sucursal->id)>
                                            {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100" aria-label="Ver {{ $titulo }}">
                                <i class="bi bi-eye me-1" aria-hidden="true"></i> Ver
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        @endforeach
    </div>

    @if ($reporte !== null)
        @php
            $claveReporte = (string) ($reporte ?? '');
            $simbolo = $divisa ?? 'Bs';
            $moneda = fn ($importe) => $simbolo . ' ' . number_format($importe, 2);
            $fecha = fn ($valor) => \Illuminate\Support\Carbon::parse($valor)->format('d/m/Y');
            $esProducto = in_array($reporte, ['producto', 'productos'], true);
            $encabezado = ['dia' => 'Fecha', 'sucursal' => 'Sucursal', 'pago' => 'Forma de pago'];
        @endphp
        <section class="card" id="resultado-reporte">
            <div class="card-body">
                <h4>{{ $reportes[$claveReporte][0] }}</h4>
                <p>{{ $fecha($filtros['desde']) }} – {{ $fecha($filtros['hasta']) }}</p>
                <div class="d-flex flex-wrap gap-4 mb-3">
                    <div>Ventas emitidas <strong>{{ number_format($resumen->cantidad) }}</strong></div>
                    <div>Total vendido <strong>{{ $moneda($resumen->importe) }}</strong></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                @if ($listaVentas)
                                    <th scope="col">Venta</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Sucursal</th>
                                    <th scope="col">Forma de pago</th>
                                @else
                                    <th scope="col">{{ $esProducto ? 'Producto' : $encabezado[$claveReporte] }}</th>
                                    <th scope="col" class="text-end">{{ $esProducto ? 'Unidades' : 'Ventas' }}</th>
                                @endif
                                <th scope="col" class="text-end">Importe vendido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($filas as $fila)
                                <tr>
                                    @if ($listaVentas)
                                        <td><a href="{{ route('admin.ventas.show', $fila->id) }}">#{{ $fila->id }}</a></td>
                                        <td>{{ $fecha($fila->fecha_venta) }}</td>
                                        <td>{{ $fila->cliente ?? 'Sin cliente' }}</td>
                                        <td>{{ $fila->sucursal ?? 'Sin identificar' }}</td>
                                        <td>{{ ucfirst($fila->tipo_pago ?? 'Sin identificar') }}</td>
                                    @else
                                        <td>
                                            {{ $reporte === 'dia' ? $fecha($fila->etiqueta) : ucfirst($fila->etiqueta ?? 'Sin identificar') }}
                                            @if (!empty($fila->codigo))
                                                <small class="d-block text-muted">{{ $fila->codigo }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($fila->cantidad) }}</td>
                                    @endif
                                    <td class="text-end text-nowrap">{{ $moneda($listaVentas ? $fila->total_venta : $fila->importe) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $listaVentas ? 6 : 3 }}" class="text-center py-4">No hay resultados para estos filtros.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($filas instanceof \Illuminate\Contracts\Pagination\Paginator)
                    {{ $filas->fragment('resultado-reporte')->links('pagination::bootstrap-5') }}
                @endif
            </div>
        </section>
    @endif
</div>
@endsection
