@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Ordenes de Compras </h3>
            <a href="{{ route('admin.ordenes_compra.create') }}" class="btn btn-primary"
                title="Nueva orden de compra">
                <i class="bi bi-plus-circle"></i> Nueva orden de compra
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de Ordenes de Compra</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.ordenes_compra.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar Orden de Compra</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Escribe CI/NIT, nombre o teléfono">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.ordenes_compra.index') }}" class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $compras->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Sucursal</th>
                                        <th>Proveedor</th>
                                        <th>Usuario</th>
                                        <th>Fecha de Compra</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Comprobante</th>
                                        <th style="width: 180px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($compras as $compra)
                                        <tr>
                                            <td>{{ $compras->firstItem() + $loop->index }}</td>
                                            <td>{{ $compra->sucursal->nombre }}</td>
                                            <td>{{ $compra->proveedor->nombre }}</td>
                                            <td>{{ $compra->usuario->nombre }}</td>
                                            <td>{{ $compra->fecha_compra->format('d/m/Y')}}</td>
                                            <td>{{ $compra->total }}</td>
                                            <td>{{ $compra->estado }}</td>
                                            <td>{{ $compra->comprobante }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info tooltip-trigger"
                                                    title="Ver detalles" data-bs-toggle="modal"
                                                    data-bs-target="#showCompraModal-{{ $compra->id }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-success tooltip-trigger"
                                                    title="Editar compra" data-bs-toggle="modal"
                                                    data-bs-target="#editCompraModal-{{ $compra->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger tooltip-trigger"
                                                    title="Eliminar compra" data-bs-toggle="modal"
                                                    data-bs-target="#deleteCompraModal-{{ $compra->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">No hay Compras registradas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($compras->count() > 0)
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $compras->firstItem() }} a {{ $compras->lastItem() }} de
                                    {{ $compras->total() }} registros
                                </small>
                                <div>
                                    {{ $compras->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection


