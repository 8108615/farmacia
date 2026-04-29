@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Sucursales</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSucursalModal">
                <i class="bi bi-plus-circle"></i> Nueva sucursal
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de sucursales registradas</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.sucursales.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar sucursal</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="Escribe nombre, direccion o telefono">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.sucursales.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $sucursales->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Nombre</th>
                                        <th>Direccion</th>
                                        <th>Telefono</th>
                                        <th>Estado</th>
                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sucursales as $sucursal)
                                        <tr>
                                            <td>{{ $sucursales->firstItem() + $loop->index }}</td>
                                            <td>{{ $sucursal->nombre }}</td>
                                            <td>{{ $sucursal->direccion }}</td>
                                            <td>{{ $sucursal->telefono ?: 'Sin telefono' }}</td>
                                            <td>
                                                @if ($sucursal->estado)
                                                    <span class="badge bg-success">Activa</span>
                                                @else
                                                    <span class="badge bg-danger">Inactiva</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editSucursalModal-{{ $sucursal->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteSucursalModal-{{ $sucursal->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No hay sucursales
                                                registradas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($sucursales->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $sucursales->firstItem() }} a {{ $sucursales->lastItem() }} de
                                    {{ $sucursales->total() }}
                                    registros
                                </small>
                                <div>
                                    {{ $sucursales->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createSucursalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.sucursales.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear sucursal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label for="create-nombre">Nombre (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <input type="text" name="nombre" id="create-nombre" class="form-control"
                                value="{{ old('nombre') }}" placeholder="Nombre de la sucursal" required>
                        </div>
                        @if (session('open_modal') === 'createSucursalModal')
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="create-direccion">Direccion (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                            <textarea name="direccion" id="create-direccion" class="form-control" rows="3"
                                placeholder="Direccion de la sucursal" required>{{ old('direccion') }}</textarea>
                        </div>
                        @if (session('open_modal') === 'createSucursalModal')
                            @error('direccion')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label for="create-telefono">Telefono</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" name="telefono" id="create-telefono" class="form-control"
                                value="{{ old('telefono') }}" placeholder="Telefono de contacto">
                        </div>
                        @if (session('open_modal') === 'createSucursalModal')
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="create-estado">Estado (*)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                            <select name="estado" id="create-estado" class="form-select" required>
                                <option value="1" {{ old('estado', '1') == '1' ? 'selected' : '' }}>Activa</option>
                                <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                        </div>
                        @if (session('open_modal') === 'createSucursalModal')
                            @error('estado')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($sucursales as $sucursal)
        <div class="modal fade" id="editSucursalModal-{{ $sucursal->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST"
                    action="{{ route('admin.sucursales.update', $sucursal->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar sucursal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="edit-nombre-{{ $sucursal->id }}">Nombre (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" name="nombre" id="edit-nombre-{{ $sucursal->id }}"
                                    class="form-control"
                                    value="{{ session('open_modal') === 'editSucursalModal-' . $sucursal->id ? old('nombre', $sucursal->nombre) : $sucursal->nombre }}"
                                    required>
                            </div>
                            @if (session('open_modal') === 'editSucursalModal-' . $sucursal->id)
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group mb-2">
                            <label for="edit-direccion-{{ $sucursal->id }}">Direccion (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <textarea name="direccion" id="edit-direccion-{{ $sucursal->id }}" class="form-control" rows="3" required>{{ session('open_modal') === 'editSucursalModal-' . $sucursal->id ? old('direccion', $sucursal->direccion) : $sucursal->direccion }}</textarea>
                            </div>
                            @if (session('open_modal') === 'editSucursalModal-' . $sucursal->id)
                                @error('direccion')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group mb-2">
                            <label for="edit-telefono-{{ $sucursal->id }}">Telefono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="telefono" id="edit-telefono-{{ $sucursal->id }}"
                                    class="form-control" required
                                    value="{{ session('open_modal') === 'editSucursalModal-' . $sucursal->id ? old('telefono', $sucursal->telefono) : $sucursal->telefono }}">
                            </div>
                            @if (session('open_modal') === 'editSucursalModal-' . $sucursal->id)
                                @error('telefono')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="edit-estado-{{ $sucursal->id }}">Estado (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                <select name="estado" id="edit-estado-{{ $sucursal->id }}" class="form-select"
                                    required>
                                    <option value="1"
                                        {{ (session('open_modal') === 'editSucursalModal-' . $sucursal->id ? old('estado', (int) $sucursal->estado) : (int) $sucursal->estado) == 1 ? 'selected' : '' }}>
                                        Activa
                                    </option>
                                    <option value="0"
                                        {{ (session('open_modal') === 'editSucursalModal-' . $sucursal->id ? old('estado', (int) $sucursal->estado) : (int) $sucursal->estado) == 0 ? 'selected' : '' }}>
                                        Inactiva
                                    </option>
                                </select>
                            </div>
                            @if (session('open_modal') === 'editSucursalModal-' . $sucursal->id)
                                @error('estado')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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

        <div class="modal fade" id="deleteSucursalModal-{{ $sucursal->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST"
                    action="{{ route('admin.sucursales.destroy', $sucursal->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar sucursal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">Esta seguro de eliminar la sucursal <strong>{{ $sucursal->nombre }}</strong>?
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
