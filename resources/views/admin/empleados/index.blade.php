@extends('layouts.admin')

@push('styles')

@endpush

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Empleados</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEmpleadoModal">
                <i class="bi bi-plus-circle"></i> Nuevo empleado
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de empleados</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.empleados.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar empleado</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}"
                                            placeholder="Nombre, documento, telefono o sucursal">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.empleados.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $empleados->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Empleado</th>
                                        <th>Usuario</th>
                                        <th>Sucursal</th>
                                        <th>Documento</th>
                                        <th>Telefono</th>
                                        <th>Estado</th>
                                        <th style="width: 180px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($empleados as $empleado)
                                        <tr>
                                            <td>{{ $empleados->firstItem() + $loop->index }}</td>
                                            <td>{{ $empleado->nombres }} {{ $empleado->apellidos }}</td>
                                            <td>{{ $empleado->usuario->name ?? 'Sin usuario' }}</td>
                                            <td>{{ $empleado->sucursal->nombre ?? 'Sin sucursal' }}</td>
                                            <td>{{ $empleado->tipo_doc }} {{ $empleado->numero_doc }}</td>
                                            <td>{{ $empleado->telefono ?? 'Sin telefono' }}</td>
                                            <td>
                                                @if ($empleado->estado === 'activo')
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editEmpleadoModal-{{ $empleado->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteEmpleadoModal-{{ $empleado->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No hay empleados
                                                registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($empleados->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $empleados->firstItem() }} a {{ $empleados->lastItem() }} de
                                    {{ $empleados->total() }} registros
                                </small>
                                <div>
                                    {{ $empleados->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createEmpleadoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form class="modal-content" method="POST" action="{{ route('admin.empleados.store') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear empleado</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-12 col-md-6">
                            <label for="create-nombres" class="form-label">Nombres (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="nombres" id="create-nombres" class="form-control"
                                    value="{{ old('nombres') }}" placeholder="Nombres del empleado">
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('nombres')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-apellidos" class="form-label">Apellidos (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="apellidos" id="create-apellidos" class="form-control"
                                    value="{{ old('apellidos') }}" placeholder="Apellidos del empleado">
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('apellidos')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="create-tipo_doc" class="form-label">Tipo de documento (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <select name="tipo_doc" id="create-tipo_doc" class="form-select">
                                    <option value="">Selecciona tipo</option>
                                    @foreach (['CI', 'DNI', 'RUC', 'PASAPORTE', 'OTRO'] as $tipo)
                                        <option value="{{ $tipo }}"
                                            {{ old('tipo_doc') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('tipo_doc')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="create-numero_doc" class="form-label">Número de documento (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" name="numero_doc" id="create-numero_doc" class="form-control"
                                    value="{{ old('numero_doc') }}" placeholder="Número de documento">
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('numero_doc')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="create-telefono" class="form-label">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="telefono" id="create-telefono" class="form-control"
                                    value="{{ old('telefono') }}" placeholder="Teléfono">
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('telefono')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-profesion" class="form-label">Profesión</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                <input type="text" name="profesion" id="create-profesion" class="form-control"
                                    value="{{ old('profesion') }}" placeholder="Profesión">
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('profesion')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="create-fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                                <input type="date" name="fecha_nacimiento" id="create-fecha_nacimiento"
                                    class="form-control" value="{{ old('fecha_nacimiento') }}">
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('fecha_nacimiento')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="create-genero" class="form-label">Género</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                <select name="genero" id="create-genero" class="form-select">
                                    <option value="">Selecciona género</option>
                                    <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>Masculino
                                    </option>
                                    <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>Femenino</option>
                                </select>
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('genero')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-email" class="form-label">Email (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="create-email" class="form-control"
                                    value="{{ old('email') }}" placeholder="Correo del empleado">
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-sucursal_id" class="form-label">Sucursal (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <select name="sucursal_id" id="create-sucursal_id" class="form-select">
                                    <option value="">Selecciona una sucursal</option>
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}"
                                            {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('sucursal_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-12">
                            <label for="create-direccion" class="form-label">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <textarea name="direccion" id="create-direccion" class="form-control" rows="2" placeholder="Dirección">{{ old('direccion') }}</textarea>
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('direccion')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-avatar" class="form-label">Avatar</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-image"></i></span>
                                <input id="create-avatar" type="file" name="avatar"
                                    class="form-control avatar-input" accept="image/*" data-preview="createAvatarPreview"
                                    data-placeholder="createAvatarPlaceholder">
                            </div>
                            <div class="mt-3 text-center">
                                <img id="createAvatarPreview" src="" alt="Previsualización del avatar"
                                    style="max-width: 100%; max-height: 240px; border: 1px dashed #dce7f1; border-radius: 8px; padding: 8px; background: #f7fbff; display: none;">
                                <p id="createAvatarPlaceholder" class="text-muted mb-0">Aquí se mostrará la
                                    previsualización del avatar.</p>
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('avatar')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="create-estado" class="form-label">Estado (*)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                <select name="estado" id="create-estado" class="form-select">
                                    <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>
                                        Activo</option>
                                    <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo
                                    </option>
                                </select>
                            </div>
                            @if (session('open_modal') === 'createEmpleadoModal')
                                @error('estado')
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

    @foreach ($empleados as $empleado)
        <div class="modal fade" id="editEmpleadoModal-{{ $empleado->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form class="modal-content" method="POST" action="{{ route('admin.empleados.update', $empleado->id) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar empleado</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label for="edit-nombres-{{ $empleado->id }}" class="form-label">Nombres (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" name="nombres" id="edit-nombres-{{ $empleado->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('nombres', $empleado->nombres) : $empleado->nombres }}"
                                        placeholder="Nombres del empleado">
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('nombres')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-apellidos-{{ $empleado->id }}" class="form-label">Apellidos (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" name="apellidos" id="edit-apellidos-{{ $empleado->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('apellidos', $empleado->apellidos) : $empleado->apellidos }}"
                                        placeholder="Apellidos del empleado">
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('apellidos')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="edit-tipo_doc-{{ $empleado->id }}" class="form-label">Tipo de documento
                                    (*)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <select name="tipo_doc" id="edit-tipo_doc-{{ $empleado->id }}" class="form-select">
                                        <option value="">Selecciona tipo</option>
                                        @foreach (['CI', 'DNI', 'RUC', 'PASAPORTE', 'OTRO'] as $tipo)
                                            <option value="{{ $tipo }}"
                                                {{ (session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('tipo_doc', $empleado->tipo_doc) : $empleado->tipo_doc) === $tipo ? 'selected' : '' }}>
                                                {{ $tipo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('tipo_doc')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="edit-numero_doc-{{ $empleado->id }}" class="form-label">Número de documento
                                    (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                    <input type="text" name="numero_doc" id="edit-numero_doc-{{ $empleado->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('numero_doc', $empleado->numero_doc) : $empleado->numero_doc }}"
                                        placeholder="Número de documento">
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('numero_doc')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="edit-telefono-{{ $empleado->id }}" class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" name="telefono" id="edit-telefono-{{ $empleado->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('telefono', $empleado->telefono) : $empleado->telefono }}"
                                        placeholder="Teléfono">
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('telefono')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-profesion-{{ $empleado->id }}" class="form-label">Profesión</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                    <input type="text" name="profesion" id="edit-profesion-{{ $empleado->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('profesion', $empleado->profesion) : $empleado->profesion }}"
                                        placeholder="Profesión">
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('profesion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="edit-fecha_nacimiento-{{ $empleado->id }}" class="form-label">Fecha de
                                    nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                                    <input type="date" name="fecha_nacimiento"
                                        id="edit-fecha_nacimiento-{{ $empleado->id }}" class="form-control"
                                        value="{{ session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('fecha_nacimiento', optional($empleado->fecha_nacimiento)->format('Y-m-d')) : optional($empleado->fecha_nacimiento)->format('Y-m-d') }}">
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('fecha_nacimiento')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="edit-genero-{{ $empleado->id }}" class="form-label">Género</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                    <select name="genero" id="edit-genero-{{ $empleado->id }}" class="form-select">
                                        <option value="">Selecciona género</option>
                                        <option value="M"
                                            {{ (session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('genero', $empleado->genero) : $empleado->genero) === 'M' ? 'selected' : '' }}>
                                            Masculino</option>
                                        <option value="F"
                                            {{ (session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('genero', $empleado->genero) : $empleado->genero) === 'F' ? 'selected' : '' }}>
                                            Femenino</option>
                                    </select>
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('genero')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-email-{{ $empleado->id }}" class="form-label">Email (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="edit-email-{{ $empleado->id }}"
                                        class="form-control"
                                        value="{{ session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('email', $empleado->usuario->email ?? '') : $empleado->usuario->email ?? '' }}"
                                        placeholder="Correo del empleado">
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-sucursal_id-{{ $empleado->id }}" class="form-label">Sucursal (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <select name="sucursal_id" id="edit-sucursal_id-{{ $empleado->id }}"
                                        class="form-select">
                                        <option value="">Selecciona una sucursal</option>
                                        @foreach ($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}"
                                                {{ (session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('sucursal_id', $empleado->sucursal_id) : $empleado->sucursal_id) == $sucursal->id ? 'selected' : '' }}>
                                                {{ $sucursal->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('sucursal_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-12">
                                <label for="edit-direccion-{{ $empleado->id }}" class="form-label">Dirección</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                    <textarea name="direccion" id="edit-direccion-{{ $empleado->id }}" class="form-control" rows="2"
                                        placeholder="Dirección">{{ session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('direccion', $empleado->direccion) : $empleado->direccion }}</textarea>
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('direccion')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-avatar-{{ $empleado->id }}" class="form-label">Avatar</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-image"></i></span>
                                    <input id="edit-avatar-{{ $empleado->id }}" type="file" name="avatar"
                                        class="form-control avatar-input" accept="image/*"
                                        data-preview="editAvatarPreview-{{ $empleado->id }}"
                                        data-placeholder="editAvatarPlaceholder-{{ $empleado->id }}">
                                </div>
                                <div class="mt-3 text-center">
                                    <img id="editAvatarPreview-{{ $empleado->id }}"
                                        src="{{ $empleado->avatar ? asset('storage/' . $empleado->avatar) : '' }}"
                                        alt="Previsualización del avatar"
                                        style="max-width: 100%; max-height: 240px; border: 1px dashed #dce7f1; border-radius: 8px; padding: 8px; background: #f7fbff; {{ $empleado->avatar ? '' : 'display:none;' }}">
                                    <p id="editAvatarPlaceholder-{{ $empleado->id }}"
                                        class="text-muted mb-0 {{ $empleado->avatar ? 'd-none' : '' }}">
                                        Aquí se mostrará la previsualización del avatar.
                                    </p>
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('avatar')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edit-estado-{{ $empleado->id }}" class="form-label">Estado (*)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                    <select name="estado" id="edit-estado-{{ $empleado->id }}" class="form-select">
                                        <option value="activo"
                                            {{ (session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('estado', $empleado->estado) : $empleado->estado) === 'activo' ? 'selected' : '' }}>
                                            Activo</option>
                                        <option value="inactivo"
                                            {{ (session('open_modal') === 'editEmpleadoModal-' . $empleado->id ? old('estado', $empleado->estado) : $empleado->estado) === 'inactivo' ? 'selected' : '' }}>
                                            Inactivo</option>
                                    </select>
                                </div>
                                @if (session('open_modal') === 'editEmpleadoModal-' . $empleado->id)
                                    @error('estado')
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

        <div class="modal fade" id="deleteEmpleadoModal-{{ $empleado->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST"
                    action="{{ route('admin.empleados.destroy', $empleado->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar empleado</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de eliminar al empleado <strong>{{ $empleado->nombres }}
                                {{ $empleado->apellidos }}</strong>?</p>
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
            document.querySelectorAll('.avatar-input').forEach(function(input) {
                input.addEventListener('change', function(event) {
                    const file = event.target.files && event.target.files[0];
                    const previewId = event.target.dataset.preview;
                    const placeholderId = event.target.dataset.placeholder;
                    const preview = document.getElementById(previewId);
                    const placeholder = document.getElementById(placeholderId);

                    if (!preview || !placeholder) {
                        return;
                    }

                    if (!file) {
                        preview.src = '';
                        preview.style.display = 'none';
                        placeholder.classList.remove('d-none');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'inline-block';
                        placeholder.classList.add('d-none');
                    };
                    reader.readAsDataURL(file);
                });
            });
        })();
    </script>
@endpush
