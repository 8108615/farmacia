@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3>Compras realizadas</h3>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.ordenes_compra.create') }}" class="btn btn-success btn-sm shadow-sm">
                    <i class="bi bi-plus-circle me-1 align-middle"></i>
                    Crear orden
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" title="Ver órdenes pendientes"
                    data-bs-toggle="modal" data-bs-target="#pendingOrdersModal">
                    <i class="bi bi-list-check me-1 align-middle"></i>
                    Órdenes pendientes
                    @if (isset($pendingOrders) && $pendingOrders->count() > 0)
                        <span class="badge bg-primary text-white ms-2">{{ $pendingOrders->count() }}</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de compras</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.compras.index') }}" class="mb-3">
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
                                    <a href="{{ route('admin.compras.index') }}"
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
                            <table class="table table-bordered table-striped mb-0">
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

                                                    <a href="{{ url('/admin/compras/' . $compra->id) }}" type="button"
                                                        class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <a href="{{ url('/admin/compras/' . $compra->id . '/edit') }}"
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
                                                Registradas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @foreach ($compras as $compra)
                            <div class="modal fade" id="deleteCompraModal-{{ $compra->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.compras.destroy', $compra->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" style="color: white">Eliminar orden de compra</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p class="mb-0">¿Está seguro de eliminar la compra con total de
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

                        <div class="modal fade" id="pendingOrdersModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Órdenes pendientes</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body py-4">
                                        @if ($pendingOrders->isEmpty())
                                            <div
                                                class="d-flex flex-column justify-content-center align-items-center gap-3 bg-info bg-opacity-10 border border-info rounded-3 p-4 mb-3 text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-3">
                                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width:56px;height:56px;">
                                                        <span
                                                            style="font-size:24px; font-weight:700; line-height:1; display:inline-block;">i</span>
                                                    </div>
                                                    <div>
                                                        <div class="mb-1 fw-semibold">No hay órdenes pendientes para
                                                            convertir en compra.</div>
                                                        <div class="small text-muted">Crea una orden nueva para comenzar.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-100 d-flex justify-content-center">
                                                    <a href="{{ route('admin.ordenes_compra.create') }}"
                                                        class="btn btn-info btn-sm shadow-sm px-4 py-2">
                                                        <i class="bi bi-plus-circle"></i> Crear orden de compra
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Sucursal</th>
                                                            <th>Proveedor</th>
                                                            <th>Usuario</th>
                                                            <th>Fecha</th>
                                                            <th>Total</th>
                                                            <th>Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($pendingOrders as $order)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $order->sucursal->nombre }}</td>
                                                                <td>{{ $order->proveedor->nombre }}</td>
                                                                <td>{{ $order->usuario->name }}</td>
                                                                <td>{{ $order->fecha_compra->format('d/m/Y') }}</td>
                                                                <td>{{ $ajuste->divisa }}
                                                                    {{ number_format((float) $order->total, 2, '.', ',') }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ url('/admin/compras/' . $order->id . '/create') }}"
                                                                        class="btn btn-sm btn-success">
                                                                        Convertir a Compra
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary"
                                            data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
