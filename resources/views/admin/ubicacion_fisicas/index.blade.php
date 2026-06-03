@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Ubicaciones físicas</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUbicacionFisicaModal">
                <i class="bi bi-plus-circle"></i> Nueva ubicación física
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de ubicaciones físicas</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.ubicacion_fisicas.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar ubicación física</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="Escribe sucursal, nombre o descripción">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.ubicacion_fisicas.index') }}" class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $ubicaciones->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Sucursal</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ubicaciones as $ubicacion)
                                        <tr>
                                            <td>{{ $ubicaciones->firstItem() + $loop->index }}</td>
                                            <td>{{ $ubicacion->sucursal->nombre ?? 'Sin sucursal' }}</td>
                                            <td>{{ $ubicacion->nombre }}</td>
                                            <td>{{ $ubicacion->descripcion ?? 'Sin descripción' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editUbicacionFisicaModal-{{ $ubicacion->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteUbicacionFisicaModal-{{ $ubicacion->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No hay ubicaciones físicas registradas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($ubicaciones->count() > 0)
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $ubicaciones->firstItem() }} a {{ $ubicaciones->lastItem() }} de
                                    {{ $ubicaciones->total() }} registros
                                </small>
                                <div>
                                    {{ $ubicaciones->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createUbicacionFisicaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.ubicacion_fisicas.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear ubicación física</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="create-sucursal_id">Sucursal (*)</label>
                        <select name="sucursal_id" id="create-sucursal_id" class="form-select" required>
                            <option value="">-- Selecciona una sucursal --</option>
                            @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}"
                                    {{ old('sucursal_id') === (string) $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if (session('open_modal') === 'createUbicacionFisicaModal')
                            @error('sucursal_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-3">
                        <label for="create-nombre">Nombre (*)</label>
                        <input type="text" name="nombre" id="create-nombre" class="form-control"
                            value="{{ old('nombre') }}" placeholder="Nombre de la ubicación física" required>
                        @if (session('open_modal') === 'createUbicacionFisicaModal')
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group mb-3">
                        <label for="create-descripcion">Descripción</label>
                        <textarea name="descripcion" id="create-descripcion" class="form-control" rows="3"
                            placeholder="Descripción opcional">{{ old('descripcion') }}</textarea>
                        @if (session('open_modal') === 'createUbicacionFisicaModal')
                            @error('descripcion')
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

    @foreach ($ubicaciones as $ubicacion)
        <div class="modal fade" id="editUbicacionFisicaModal-{{ $ubicacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('admin.ubicacion_fisicas.update', $ubicacion->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar ubicación física</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="edit-sucursal_id-{{ $ubicacion->id }}">Sucursal (*)</label>
                            <select name="sucursal_id" id="edit-sucursal_id-{{ $ubicacion->id }}" class="form-select" required>
                                <option value="">-- Selecciona una sucursal --</option>
                                @foreach ($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}"
                                        {{ (session('open_modal') === 'editUbicacionFisicaModal-' . $ubicacion->id ? old('sucursal_id', $ubicacion->sucursal_id) : $ubicacion->sucursal_id) === $sucursal->id ? 'selected' : '' }}>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @if (session('open_modal') === 'editUbicacionFisicaModal-' . $ubicacion->id)
                                @error('sucursal_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit-nombre-{{ $ubicacion->id }}">Nombre (*)</label>
                            <input type="text" name="nombre" id="edit-nombre-{{ $ubicacion->id }}" class="form-control"
                                value="{{ session('open_modal') === 'editUbicacionFisicaModal-' . $ubicacion->id ? old('nombre', $ubicacion->nombre) : $ubicacion->nombre }}"
                                placeholder="Nombre de la ubicación física" required>
                            @if (session('open_modal') === 'editUbicacionFisicaModal-' . $ubicacion->id)
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit-descripcion-{{ $ubicacion->id }}">Descripción</label>
                            <textarea name="descripcion" id="edit-descripcion-{{ $ubicacion->id }}" class="form-control" rows="3"
                                placeholder="Descripción opcional">{{ session('open_modal') === 'editUbicacionFisicaModal-' . $ubicacion->id ? old('descripcion', $ubicacion->descripcion) : $ubicacion->descripcion }}</textarea>
                            @if (session('open_modal') === 'editUbicacionFisicaModal-' . $ubicacion->id)
                                @error('descripcion')
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

        <div class="modal fade" id="deleteUbicacionFisicaModal-{{ $ubicacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.ubicacion_fisicas.destroy', $ubicacion->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar ubicación física</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">¿Estás seguro de eliminar la ubicación física <strong>{{ $ubicacion->nombre }}</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('open_modal'))
                    var modalElement = document.getElementById('{{ session('open_modal') }}');
                    if (modalElement) {
                        var modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    }
                @endif
            });
        </script>
    @endpush
@endsection
