@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Proveedores</h3>
            <button type="button" class="btn btn-primary tooltip-trigger" data-bs-toggle="modal"
                data-bs-target="#createProveedorModal" title="Registrar nuevo proveedor">
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
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="Nombre, telefono, email o empresa">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.proveedores.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
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
                                        <th style="width: 60px;">#</th>
                                        <th>Nombre</th>
                                        <th>Telefono</th>
                                        <th>Email</th>
                                        <th>Empresa</th>
                                        <th>Direccion</th>
                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($proveedores as $proveedor)
                                        <tr>
                                            <td>{{ $proveedores->firstItem() + $loop->index }}</td>
                                            <td>{{ $proveedor->nombre }}</td>
                                            <td>{{ $proveedor->telefono }}</td>
                                            <td>{{ $proveedor->email ?: 'Sin email' }}</td>
                                            <td>{{ $proveedor->empresa ?: 'Sin empresa' }}</td>
                                            <td>{{ $proveedor->direccion ?: 'Sin direccion' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info tooltip-trigger"
                                                    title="Ver detalles" data-bs-toggle="modal"
                                                    data-bs-target="#showProveedorModal-{{ $proveedor->id }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-success tooltip-trigger"
                                                    title="Editar proveedor" data-bs-toggle="modal"
                                                    data-bs-target="#editProveedorModal-{{ $proveedor->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger tooltip-trigger"
                                                    title="Eliminar proveedor" data-bs-toggle="modal"
                                                    data-bs-target="#deleteProveedorModal-{{ $proveedor->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No hay proveedores
                                                registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($proveedores->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $proveedores->firstItem() }} a {{ $proveedores->lastItem() }} de
                                    {{ $proveedores->total() }} registros
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="create-nombre" class="form-label">Nombre (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
                                <input type="text" name="nombre" id="create-nombre" class="form-control"
                                    value="{{ old('nombre') }}" placeholder="Nombre del proveedor">
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-telefono" class="form-label">Telefono (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="telefono" id="create-telefono" class="form-control"
                                    value="{{ old('telefono') }}" placeholder="Telefono de contacto">
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
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
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-empresa" class="form-label">Empresa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" name="empresa" id="create-empresa" class="form-control"
                                    value="{{ old('empresa') }}" placeholder="Empresa asociada">
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('empresa')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12">
                            <label for="create-direccion" class="form-label">Direccion</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <textarea name="direccion" id="create-direccion" class="form-control" rows="2"
                                    placeholder="Direccion del proveedor">{{ old('direccion') }}</textarea>
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('direccion')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12">
                            <label for="create-notas" class="form-label">Notas</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-journal-text"></i></span>
                                <textarea name="notas" id="create-notas" class="form-control" rows="3" placeholder="Notas adicionales">{{ old('notas') }}</textarea>
                            </div>
                            @if (session('open_modal') === 'createProveedorModal')
                                @error('notas')
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

    @foreach ($proveedores as $proveedor)
        <div class="modal fade" id="showProveedorModal-{{ $proveedor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" style="color: white">Detalle del proveedor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">Nombre</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">{{ $proveedor->nombre }}</p>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">Telefono</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">{{ $proveedor->telefono }}</p>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">Email</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">
                                    {{ $proveedor->email ?: 'Sin email' }}
                                </p>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label mb-1">Empresa</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">
                                    {{ $proveedor->empresa ?: 'Sin empresa' }}
                                </p>
                            </div>

                            <div class="col-12">
                                <label class="form-label mb-1">Direccion</label>
                                <p class="form-control-plaintext border rounded px-2 py-1">
                                    {{ $proveedor->direccion ?: 'Sin direccion' }}
                                </p>
                            </div>

                            <div class="col-12">
                                <label class="form-label mb-1">Notas</label>
                                <p class="form-control-plaintext border rounded px-2 py-1 mb-0">
                                    {{ $proveedor->notas ?: 'Sin notas' }}
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

        <div class="modal fade" id="editProveedorModal-{{ $proveedor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form class="modal-content" method="POST"
                    action="{{ route('admin.proveedores.update', $proveedor->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar proveedor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="edit-nombre-{{ $proveedor->id }}" class="form-label">Nombre (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
                                    <input type="text" name="nombre" id="edit-nombre-{{ $proveedor->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('nombre', $proveedor->nombre) : $proveedor->nombre }}">
                                </div>
                                @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                    @error('nombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-telefono-{{ $proveedor->id }}" class="form-label">Telefono (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" name="telefono" id="edit-telefono-{{ $proveedor->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('telefono', $proveedor->telefono) : $proveedor->telefono }}">
                                </div>
                                @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                    @error('telefono')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-email-{{ $proveedor->id }}" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" name="email" id="edit-email-{{ $proveedor->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('email', $proveedor->email) : $proveedor->email }}">
                                </div>
                                @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-empresa-{{ $proveedor->id }}" class="form-label">Empresa</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="empresa" id="edit-empresa-{{ $proveedor->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('empresa', $proveedor->empresa) : $proveedor->empresa }}">
                                </div>
                                @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                    @error('empresa')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12">
                                <label for="edit-direccion-{{ $proveedor->id }}" class="form-label">Direccion</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                    <textarea name="direccion" id="edit-direccion-{{ $proveedor->id }}" class="form-control" rows="2">{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('direccion', $proveedor->direccion) : $proveedor->direccion }}</textarea>
                                </div>
                                @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                    @error('direccion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12">
                                <label for="edit-notas-{{ $proveedor->id }}" class="form-label">Notas</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-journal-text"></i></span>
                                    <textarea name="notas" id="edit-notas-{{ $proveedor->id }}" class="form-control" rows="3">{{ session('open_modal') === 'editProveedorModal-' . $proveedor->id ? old('notas', $proveedor->notas) : $proveedor->notas }}</textarea>
                                </div>
                                @if (session('open_modal') === 'editProveedorModal-' . $proveedor->id)
                                    @error('notas')
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

        <div class="modal fade" id="deleteProveedorModal-{{ $proveedor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST"
                    action="{{ route('admin.proveedores.destroy', $proveedor->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar proveedor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">Esta seguro de eliminar al proveedor
                            <strong>{{ $proveedor->nombre }}</strong>?
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
