@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Clientes</h3>
            <button type="button" class="btn btn-primary tooltip-trigger" data-bs-toggle="modal"
                data-bs-target="#createClienteModal" title="Registrar nuevo cliente">
                <i class="bi bi-plus-circle"></i> Nuevo cliente
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de clientes</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.clientes.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar cliente</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="CI/NIT, nombre, telefono o email">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.clientes.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $clientes->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>CI/NIT</th>
                                        <th>Nombres y apellidos</th>
                                        <th>Telefono</th>
                                        <th>Email</th>
                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clientes as $cliente)
                                        <tr>
                                            <td>{{ $clientes->firstItem() + $loop->index }}</td>
                                            <td>{{ $cliente->ci_nit }}</td>
                                            <td>{{ $cliente->nombres_apellidos }}</td>
                                            <td>{{ $cliente->telefono ?: 'Sin telefono' }}</td>
                                            <td>{{ $cliente->email ?: 'Sin email' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info tooltip-trigger"
                                                    title="Ver detalles" data-bs-toggle="modal"
                                                    data-bs-target="#showClienteModal-{{ $cliente->id }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-success tooltip-trigger"
                                                    title="Editar cliente" data-bs-toggle="modal"
                                                    data-bs-target="#editClienteModal-{{ $cliente->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger tooltip-trigger"
                                                    title="Eliminar cliente" data-bs-toggle="modal"
                                                    data-bs-target="#deleteClienteModal-{{ $cliente->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No hay clientes
                                                registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($clientes->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de
                                    {{ $clientes->total() }} registros
                                </small>
                                <div>
                                    {{ $clientes->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createClienteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.clientes.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="create-ci-nit" class="form-label">CI/NIT (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="ci_nit" id="create-ci-nit" class="form-control"
                                    value="{{ old('ci_nit') }}" placeholder="Documento del cliente">
                            </div>
                            @if (session('open_modal') === 'createClienteModal')
                                @error('ci_nit')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-nombres-apellidos" class="form-label">Nombres y apellidos (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
                                <input type="text" name="nombres_apellidos" id="create-nombres-apellidos"
                                    class="form-control" value="{{ old('nombres_apellidos') }}"
                                    placeholder="Nombre completo del cliente">
                            </div>
                            @if (session('open_modal') === 'createClienteModal')
                                @error('nombres_apellidos')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-telefono" class="form-label">Telefono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="telefono" id="create-telefono" class="form-control"
                                    value="{{ old('telefono') }}" placeholder="Telefono de contacto">
                            </div>
                            @if (session('open_modal') === 'createClienteModal')
                                @error('telefono')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" id="create-email" class="form-control"
                                    value="{{ old('email') }}" placeholder="correo@dominio.com">
                            </div>
                            @if (session('open_modal') === 'createClienteModal')
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($clientes as $cliente)
        <div class="modal fade" id="showClienteModal-{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" style="color: white">Detalle del cliente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">CI/NIT</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">{{ $cliente->ci_nit }}</p>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">Nombres y apellidos</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">
                                    {{ $cliente->nombres_apellidos }}
                                </p>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">Telefono</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">
                                    {{ $cliente->telefono ?: 'Sin telefono' }}
                                </p>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">Email</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">
                                    {{ $cliente->email ?: 'Sin email' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editClienteModal-{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form class="modal-content" method="POST" action="{{ route('admin.clientes.update', $cliente->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar cliente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="edit-ci-nit-{{ $cliente->id }}" class="form-label">CI/NIT (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <input type="text" name="ci_nit" id="edit-ci-nit-{{ $cliente->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editClienteModal-' . $cliente->id ? old('ci_nit', $cliente->ci_nit) : $cliente->ci_nit }}">
                                </div>
                                @if (session('open_modal') === 'editClienteModal-' . $cliente->id)
                                    @error('ci_nit')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-nombres-apellidos-{{ $cliente->id }}" class="form-label">Nombres y
                                    apellidos (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
                                    <input type="text" name="nombres_apellidos"
                                        id="edit-nombres-apellidos-{{ $cliente->id }}" class="form-control"
                                        value="{{ session('open_modal') === 'editClienteModal-' . $cliente->id ? old('nombres_apellidos', $cliente->nombres_apellidos) : $cliente->nombres_apellidos }}">
                                </div>
                                @if (session('open_modal') === 'editClienteModal-' . $cliente->id)
                                    @error('nombres_apellidos')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-telefono-{{ $cliente->id }}" class="form-label">Telefono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" name="telefono" id="edit-telefono-{{ $cliente->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editClienteModal-' . $cliente->id ? old('telefono', $cliente->telefono) : $cliente->telefono }}">
                                </div>
                                @if (session('open_modal') === 'editClienteModal-' . $cliente->id)
                                    @error('telefono')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-email-{{ $cliente->id }}" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" name="email" id="edit-email-{{ $cliente->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editClienteModal-' . $cliente->id ? old('email', $cliente->email) : $cliente->email }}">
                                </div>
                                @if (session('open_modal') === 'editClienteModal-' . $cliente->id)
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteClienteModal-{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.clientes.destroy', $cliente->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar cliente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">Esta seguro de eliminar al cliente
                            <strong>{{ $cliente->nombres_apellidos }}</strong>?
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        (function() {
            const tooltipElements = [].slice.call(document.querySelectorAll('.tooltip-trigger'));
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                tooltipElements.forEach(function(el) {
                    new bootstrap.Tooltip(el, {
                        placement: 'top',
                        delay: {
                            show: 180,
                            hide: 80,
                        },
                    });
                });
            }

            const openModalId = @json(session('open_modal'));
            if (!openModalId) {
                return;
            }

            const modalElement = document.getElementById(openModalId);
            if (!modalElement || typeof bootstrap === 'undefined') {
                return;
            }

            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        })();
    </script>
@endpush
