@extends('layouts.admin')

@section('content')
    <div class="page-heading d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h3>Existencias por producto</h3>
            <p class="text-muted mb-0">Consulta el stock por producto, lote y sucursal.</p>
        </div>

        <a href="{{ route('admin.inventarios.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Volver a Inventario
        </a>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.inventarios.existencias') }}"
                      class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="sucursal_id" class="form-label">Sucursal</label>
                        <select id="sucursal_id" name="sucursal_id" class="form-select">
                            <option value="">Todas las sucursales</option>
                            @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}"
                                    @selected((string) $sucursalId === (string) $sucursal->id)>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('sucursal_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md">
                        <label for="buscar" class="form-label">Buscar</label>
                        <input type="search" id="buscar" name="buscar" class="form-control"
                               value="{{ $buscar }}" maxlength="150"
                               placeholder="Nombre, código, código de barras o lote">
                        @error('buscar')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('admin.inventarios.existencias') }}"
                           class="btn btn-outline-secondary">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h4 class="card-title mb-0">Stock por lote y sucursal</h4>
                <span class="text-muted">{{ $existencias->total() }} registros</span>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Producto</th>
                                <th scope="col">Laboratorio</th>
                                <th scope="col">Categoría</th>
                                <th scope="col">Lote</th>
                                <th scope="col">Vencimiento</th>
                                <th scope="col">Sucursal</th>
                                <th scope="col">Ubicación</th>
                                <th scope="col" class="text-end">Stock</th>
                                <th scope="col" class="text-end">P. compra</th>
                                <th scope="col" class="text-end">P. venta</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($existencias as $registro)
                                <tr>
                                    <td>{{ $existencias->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $registro->producto?->nombre_comercial ?? '—' }}</strong>
                                        @if ($registro->producto?->codigo_producto)
                                            <div class="small text-muted">{{ $registro->producto->codigo_producto }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $registro->producto?->laboratorio?->nombre ?? '—' }}</td>
                                    <td>{{ $registro->producto?->categoria?->nombre ?? '—' }}</td>
                                    <td>{{ $registro->lote?->numero_lote ?? '—' }}</td>
                                    <td>{{ $registro->lote?->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                                    <td>{{ $registro->sucursal?->nombre ?? '—' }}</td>
                                    <td>{{ $registro->ubicacionFisica?->nombre ?? 'Sin ubicación' }}</td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($registro->stock_actual, 0, '.', ',') }}
                                    </td>
                                    <td class="text-end text-nowrap">
                                        {{ $divisa }} {{ number_format($registro->precio_compra_unidad, 2, '.', ',') }}
                                    </td>
                                    <td class="text-end text-nowrap">
                                        {{ $divisa }} {{ number_format($registro->precio_venta_unidad, 2, '.', ',') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        No hay registros de inventario que coincidan con la consulta.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($existencias->hasPages())
                    <div class="mt-3">
                        {{ $existencias->links('vendor.pagination.bootstrap-5-no-summary') }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
