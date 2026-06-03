@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Ordenes de compras</h3>
            <a href="{{ url('/admin/ordenes_compra/create') }}" class="btn btn-primary "
                title="Registrar nueva orden de compra">
                <i class="bi bi-plus-circle"></i> Nueva orden de compra
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de ordenes de compra</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.ordenes_compra.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar orden de compra</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="CI/NIT, nombre, telefono o email">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.ordenes_compra.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
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
                                        <th style="width: 60px;">#</th>
                                        <th>Sucursal</th>
                                        <th>Proveedor</th>
                                        <th>Usuario</th>
                                        <th>Fecha de compra</th>
                                        <th>Total</th>
                                        <th>Estado</th>

                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($compras as $compra)
                                        <tr>
                                            <td>{{ $compras->firstItem() + $loop->index }}</td>
                                            <td>{{ $compra->sucursal->nombre }}</td>
                                            <td>{{ $compra->proveedor->nombre }}</td>
                                            <td>{{ $compra->usuario->name }}</td>
                                            <td>{{ $compra->fecha_compra->format('d/m/Y') }}</td>
                                            <td>{{ $ajuste->divisa }} {{ $compra->total }}</td>
                                            <td>{{ $compra->estado }}</td>
                                            <td>

                                                <div class="btn-group me-1" role="group">
                                                    <button type="button" class="btn btn-sm btn-warning tooltip-trigger"
                                                        title="Enviar orden" data-bs-toggle="modal"
                                                        data-bs-target="#sendCompraModal-{{ $compra->id }}">
                                                        <i class="bi bi-send"></i>
                                                    </button>
                                                    <a href="{{ url('/admin/ordenes_compra/compra/' . $compra->id) }}"
                                                        type="button" class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <a href="{{ url('/admin/ordenes_compra/compra/' . $compra->id . '/edit') }}"
                                                        class="btn btn-sm btn-success tooltip-trigger"
                                                        title="Editar compra">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger tooltip-trigger"
                                                        title="Eliminar compra" data-bs-toggle="modal"
                                                        data-bs-target="#deleteCompraModal-{{ $compra->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">No hay compras
                                                registradas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @foreach ($compras as $compra)
                            <div class="modal fade" id="sendCompraModal-{{ $compra->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Enviar orden de compra</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Selecciona cómo deseas enviar la orden de compra
                                                <strong>#{{ $compra->id }}</strong>.
                                            </p>
                                            <div class="list-group">
                                                <form action="{{ route('admin.ordenes_compra.sendEmail', $compra->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="list-group-item list-group-item-action {{ empty($compra->proveedor->email) ? 'disabled' : '' }}">
                                                        <i class="bi bi-envelope me-2"></i> Enviar por correo
                                                    </button>
                                                </form>
                                                @if (!empty($compra->proveedor->telefono))
                                                    <a href="{{ route('admin.ordenes_compra.sendWhatsapp', $compra->id) }}"
                                                        target="_blank" class="list-group-item list-group-item-action">
                                                        <i class="bi bi-whatsapp me-2"></i> Enviar por WhatsApp
                                                    </a>
                                                @else
                                                    <div class="list-group-item disabled text-muted">
                                                        <i class="bi bi-whatsapp me-2"></i> Enviar por WhatsApp
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="deleteCompraModal-{{ $compra->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.ordenes_compra.destroy', $compra->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" style="color: white">Eliminar orden de compra</h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p class="mb-0">¿Está seguro de eliminar la orden de compra con total de
                                                <strong>{{ $ajuste->divisa ?? 'Bs.' }} {{ $compra->total }}</strong>?
                                            </p>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach


                        @if ($compras->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
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
