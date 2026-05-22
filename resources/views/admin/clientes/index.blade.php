@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Clientes</h3>
            <button type="button" class="btn btn-primary has-tooltip" title="Nuevo cliente" data-bs-toggle="modal" data-bs-target="#createClienteModal">
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
                                        <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Escribe CI/NIT, nombre o teléfono">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-light-secondary w-100">Limpiar</a>
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
                                        <th style="width: 80px;">#</th>
                                        <th>CI / NIT</th>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th style="width: 180px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clientes as $cliente)
                                        <tr>
                                            <td>{{ $clientes->firstItem() + $loop->index }}</td>
                                            <td>{{ $cliente->ci_nit }}</td>
                                            <td>{{ $cliente->nombres_apellidos }}</td>
                                            <td>{{ $cliente->telefono ?? '-' }}</td>
                                            <td>{{ $cliente->email ?? '-' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info has-tooltip" title="Ver detalles" data-bs-toggle="modal" data-bs-target="#showClienteModal-{{ $cliente->id }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-success has-tooltip" title="Editar cliente" data-bs-toggle="modal" data-bs-target="#editClienteModal-{{ $cliente->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger has-tooltip" title="Eliminar cliente" data-bs-toggle="modal" data-bs-target="#deleteClienteModal-{{ $cliente->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No hay clientes registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($clientes->count() > 0)
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} registros
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="create-ci_nit">CI / NIT (*)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light d-flex justify-content-center align-items-center" style="width:46px;"><i class="bi bi-card-text text-primary"></i></span>
                                <input type="text" name="ci_nit" id="create-ci_nit" class="form-control" value="{{ old('ci_nit') }}" placeholder="CI o NIT" required>
                            </div>
                            @if (session('open_modal') === 'createClienteModal')
                                @error('ci_nit') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="create-nombres_apellidos">Nombres y apellidos (*)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light d-flex justify-content-center align-items-center" style="width:46px;"><i class="bi bi-person text-primary"></i></span>
                                <input type="text" name="nombres_apellidos" id="create-nombres_apellidos" class="form-control" value="{{ old('nombres_apellidos') }}" placeholder="Nombre completo" required>
                            </div>
                            @if (session('open_modal') === 'createClienteModal')
                                @error('nombres_apellidos') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="create-telefono">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light d-flex justify-content-center align-items-center" style="width:46px;"><i class="bi bi-telephone text-primary"></i></span>
                                <input type="text" name="telefono" id="create-telefono" class="form-control" value="{{ old('telefono') }}" maxlength="50" placeholder="Teléfono de contacto">
                            </div>
                            @if (session('open_modal') === 'createClienteModal')
                                @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="create-email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light d-flex justify-content-center align-items-center" style="width:46px;"><i class="bi bi-envelope text-primary"></i></span>
                                <input type="email" name="email" id="create-email" class="form-control" value="{{ old('email') }}" maxlength="150" placeholder="correo@dominio.com">
                            </div>
                            @if (session('open_modal') === 'createClienteModal')
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
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
        <div class="modal fade modal-top" id="showClienteModal-{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" style="color:white">Detalle del cliente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">CI / NIT</label>
                                    <input type="text" class="form-control" value="{{ $cliente->ci_nit }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" value="{{ $cliente->nombres_apellidos }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" value="{{ $cliente->telefono ?? '-' }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" value="{{ $cliente->email ?? '-' }}" readonly>
                                </div>
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
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('admin.clientes.update', $cliente->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar cliente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="edit-ci_nit-{{ $cliente->id }}">CI / NIT (*)</label>
                            <div class="input-group">
                                <span class="input-group-text d-flex justify-content-center align-items-center" style="width: 46px;"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="ci_nit" id="edit-ci_nit-{{ $cliente->id }}" class="form-control" value="{{ session('open_modal') === 'editClienteModal-' . $cliente->id ? old('ci_nit', $cliente->ci_nit) : $cliente->ci_nit }}" required>
                            </div>
                            @if (session('open_modal') === 'editClienteModal-' . $cliente->id)
                                @error('ci_nit') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="form-group mb-2">
                            <label for="edit-nombres_apellidos-{{ $cliente->id }}">Nombres y apellidos (*)</label>
                            <div class="input-group">
                                <span class="input-group-text d-flex justify-content-center align-items-center" style="width: 46px;"><i class="bi bi-person"></i></span>
                                <input type="text" name="nombres_apellidos" id="edit-nombres_apellidos-{{ $cliente->id }}" class="form-control" value="{{ session('open_modal') === 'editClienteModal-' . $cliente->id ? old('nombres_apellidos', $cliente->nombres_apellidos) : $cliente->nombres_apellidos }}" required>
                            </div>
                            @if (session('open_modal') === 'editClienteModal-' . $cliente->id)
                                @error('nombres_apellidos') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label for="edit-telefono-{{ $cliente->id }}">Teléfono</label>
                                <input type="text" name="telefono" id="edit-telefono-{{ $cliente->id }}" class="form-control" value="{{ session('open_modal') === 'editClienteModal-' . $cliente->id ? old('telefono', $cliente->telefono) : $cliente->telefono }}" maxlength="50">
                                @if (session('open_modal') === 'editClienteModal-' . $cliente->id)
                                    @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                                @endif
                            </div>
                            <div class="col-6">
                                <label for="edit-email-{{ $cliente->id }}">Email</label>
                                <input type="email" name="email" id="edit-email-{{ $cliente->id }}" class="form-control" value="{{ session('open_modal') === 'editClienteModal-' . $cliente->id ? old('email', $cliente->email) : $cliente->email }}" maxlength="150">
                                @if (session('open_modal') === 'editClienteModal-' . $cliente->id)
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
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
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">¿Está seguro de eliminar al cliente <strong>{{ $cliente->nombres_apellidos }}</strong>?</p>
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
            const openModalId = @json(session('open_modal'));
            if (!openModalId) return;
            const modalElement = document.getElementById(openModalId);
            if (!modalElement || typeof bootstrap === 'undefined') return;
            setTimeout(function () {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }, 100);
        })();
    </script>
@endpush
