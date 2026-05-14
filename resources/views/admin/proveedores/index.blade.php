@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Proveedores</h3>
            <button type="button" class="btn btn-primary has-tooltip" title="Nuevo proveedor" data-bs-toggle="modal" data-bs-target="#createProveedorModal">
                <i class="bi bi-plus-circle"></i> Nuevo proveedor
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de proveedores</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.proveedores.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar proveedor</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Escribe nombre, email o teléfono">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.proveedores.index') }}" class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $proveedores->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th>Empresa</th>
                                        <th>Direcion</th>
                                        <th style="width: 180px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($proveedores as $proveedor)
                                        <tr>
                                            <td>{{ $proveedores->firstItem() + $loop->index }}</td>
                                            <td>{{ $proveedor->nombre }}</td>
                                            <td>{{ $proveedor->telefono }}</td>
                                            <td>{{ $proveedor->email }}</td>
                                            <td>{{ $proveedor->empresa }}</td>
                                            <td>{{ $proveedor->direccion }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info has-tooltip" title="Ver detalles" data-bs-toggle="modal" data-bs-target="#showProveedorModal-{{ $proveedor->id }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-success has-tooltip" title="Editar proveedor" data-bs-toggle="modal" data-bs-target="#editProveedorModal-{{ $proveedor->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger has-tooltip" title="Eliminar proveedor" data-bs-toggle="modal" data-bs-target="#deleteProveedorModal-{{ $proveedor->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No hay proveedores registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($proveedores->count() > 0)
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $proveedores->firstItem() }} a {{ $proveedores->lastItem() }} de {{ $proveedores->total() }} registros
                                </small>
                                <div>
                                    {{ $proveedores->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createProveedorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.proveedores.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear proveedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="create-nombre">Nombre (*)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light d-flex justify-content-center align-items-center" style="width:46px;"><i class="bi bi-person text-primary"></i></span>
                                <input type="text" name="nombre" id="create-nombre" class="form-control" value="{{ old('nombre') }}" placeholder="Nombre del proveedor" required>
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="create-telefono">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light d-flex justify-content-center align-items-center" style="width:46px;"><i class="bi bi-telephone text-primary"></i></span>
                                <input type="text" name="telefono" id="create-telefono" class="form-control" value="{{ old('telefono') }}" maxlength="50" placeholder="Telefono de contacto">
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="create-email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light d-flex justify-content-center align-items-center" style="width:46px;"><i class="bi bi-envelope text-primary"></i></span>
                                <input type="email" name="email" id="create-email" class="form-control" value="{{ old('email') }}" maxlength="150" placeholder="correo@dominio.com">
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="create-contacto">Empresa</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light d-flex justify-content-center align-items-center" style="width:46px;"><i class="bi bi-building text-primary"></i></span>
                                <input type="text" name="empresa" id="create-contacto" class="form-control" value="{{ old('empresa', old('contacto')) }}" maxlength="150" placeholder="Empresa asociada">
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('empresa') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="col-12">
                            <label for="create-direccion">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light align-items-start" style="width:46px; padding-top:10px;"><i class="bi bi-geo-alt text-primary"></i></span>
                                <textarea name="direccion" id="create-direccion" class="form-control" rows="3" placeholder="Direccion del proveedor">{{ old('direccion') }}</textarea>
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="col-12">
                            <label for="create-notas">Notas</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light align-items-start" style="width:46px; padding-top:10px;"><i class="bi bi-journal-text text-primary"></i></span>
                                <textarea name="notas" id="create-notas" class="form-control" rows="2" placeholder="Notas adicionales">{{ old('notas') }}</textarea>
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('notas') <small class="text-danger">{{ $message }}</small> @enderror
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

    @foreach ($proveedores as $proveedor)
        <div class="modal fade modal-top" id="showProveedorModal-{{ $proveedor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" style="color:white">Detalle del proveedor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" value="{{ $proveedor->nombre }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" value="{{ $proveedor->telefono ?? '-' }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" value="{{ $proveedor->email ?? '-' }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Empresa</label>
                                    <input type="text" class="form-control" value="{{ $proveedor->empresa ?? '-' }}" readonly>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" class="form-control" value="{{ $proveedor->direccion ?? '-' }}" readonly>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Notas</label>
                                    <textarea class="form-control" rows="2" readonly>{{ $proveedor->notas ?? 'Sin notas' }}</textarea>
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
        <div class="modal fade" id="editProveedorModal-{{ $proveedor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('admin.proveedores.update', $proveedor->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar proveedor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="edit-nombre-{{ $proveedor->id }}">Nombre (*)</label>
                            <div class="input-group">
                                <span class="input-group-text d-flex justify-content-center align-items-center" style="width: 46px;"><i class="bi bi-person"></i></span>
                                <input type="text" name="nombre" id="edit-nombre-{{ $proveedor->id }}" class="form-control" value="{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('nombre', $proveedor->nombre) : $proveedor->nombre }}" required>
                            </div>
                            @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label for="edit-telefono-{{ $proveedor->id }}">Teléfono</label>
                                <input type="text" name="telefono" id="edit-telefono-{{ $proveedor->id }}" class="form-control" value="{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('telefono', $proveedor->telefono) : $proveedor->telefono }}" maxlength="50">
                                @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                    @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                                @endif
                            </div>
                            <div class="col-6">
                                <label for="edit-email-{{ $proveedor->id }}">Email</label>
                                <input type="email" name="email" id="edit-email-{{ $proveedor->id }}" class="form-control" value="{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('email', $proveedor->email) : $proveedor->email }}" maxlength="150">
                                @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                @endif
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <label for="edit-empresa-{{ $proveedor->id }}">Empresa</label>
                            <input type="text" name="empresa" id="edit-empresa-{{ $proveedor->id }}" class="form-control" value="{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('empresa', $proveedor->empresa) : $proveedor->empresa }}" maxlength="150">
                            @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                @error('empresa') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="form-group mt-2">
                            <label for="edit-direccion-{{ $proveedor->id }}">Dirección</label>
                            <textarea name="direccion" id="edit-direccion-{{ $proveedor->id }}" class="form-control" rows="3">{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('direccion', $proveedor->direccion) : $proveedor->direccion }}</textarea>
                            @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>

                        <div class="form-group mt-2">
                            <label for="edit-notas-{{ $proveedor->id }}">Notas</label>
                            <textarea name="notas" id="edit-notas-{{ $proveedor->id }}" class="form-control" rows="2">{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('notas', $proveedor->notas) : $proveedor->notas }}</textarea>
                            @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                @error('notas') <small class="text-danger">{{ $message }}</small> @enderror
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteProveedorModal-{{ $proveedor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.proveedores.destroy', $proveedor->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar proveedor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">¿Está seguro de eliminar al proveedor <strong>{{ $proveedor->nombre }}</strong>?</p>
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

@push('styles')
    <style>
        /* Top-aligned modal and smooth entrance animation */
        .modal.modal-top {
            align-items: flex-start; /* place modal at top */
        }

        .modal.modal-top .modal-dialog {
            margin: 1.25rem auto;
            transform: translateY(-12px);
            opacity: 0;
            transition: transform .22s ease, opacity .22s ease;
            transform-origin: top center;
        }

        .modal.modal-top.show .modal-dialog {
            transform: translateY(0);
            opacity: 1;
        }

        /* Fallback: smoother entrance for other modals too */
        .modal .modal-dialog {
            transition: transform .22s ease, opacity .22s ease;
        }

        .modal.show .modal-dialog {
            transform: none;
            opacity: 1;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const openModalId = @json(session('open_modal'));
            if (!openModalId) return;
            const modalElement = document.getElementById(openModalId);
            if (!modalElement || typeof bootstrap === 'undefined') return;
            // Small delay so CSS transition produces a smoother entrance
            setTimeout(function () {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }, 100);
        })();

        (function() {
            if (typeof bootstrap === 'undefined') return;
            const tooltipElements = document.querySelectorAll('.has-tooltip');
            tooltipElements.forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        })();
    </script>
@endpush
