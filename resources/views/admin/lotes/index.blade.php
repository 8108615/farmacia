@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Lotes</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLoteModal">
                <i class="bi bi-plus-circle"></i> Nuevo lote
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de lotes</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.lotes.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar lote</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="Escribe el nombre del lote">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.lotes.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $lotes->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Producto</th>
                                        <th>Proveedor</th>
                                        <th>Nombre</th>
                                        <th>Vencimiento</th>
                                        <th>Fabricación</th>
                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lotes as $lote)
                                        <tr>
                                            <td>{{ $lotes->firstItem() + $loop->index }}</td>
                                            <td>{{ $lote->producto?->nombre_comercial ?? '-' }}</td>
                                            <td>{{ $lote->proveedor?->nombre ?? '-' }}</td>
                                            <td>{{ $lote->nombre }}</td>
                                            <td>{{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>{{ $lote->fecha_fabricacion ? $lote->fecha_fabricacion->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editLoteModal-{{ $lote->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteLoteModal-{{ $lote->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">No hay lotes
                                                registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($lotes->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $lotes->firstItem() }} a {{ $lotes->lastItem() }} de
                                    {{ $lotes->total() }} registros
                                </small>
                                <div>
                                    {{ $lotes->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createLoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.lotes.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear lote</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="producto_id">Producto (*)</label>
                        <select id="producto_id" name="producto_id" class="form-select">
                            <option value="">-- Selecciona un producto --</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}"
                                    {{ (string) old('producto_id') === (string) $producto->id ? 'selected' : '' }}>
                                    {{ $producto->nombre_comercial }} - {{ $producto->nombre_generico }}
                                </option>
                            @endforeach
                        </select>
                        @if (session('open_modal') === 'createLoteModal')
                            @error('producto_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label for="proveedor_id">Proveedor (*)</label>
                        <select id="proveedor_id" name="proveedor_id" class="form-select">
                            <option value="">-- Selecciona un proveedor --</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}"
                                    {{ (string) old('proveedor_id') === (string) $proveedor->id ? 'selected' : '' }}>
                                    {{ $proveedor->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if (session('open_modal') === 'createLoteModal')
                            @error('proveedor_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label for="nombre">Número de lote (*)</label>
                        <div class="input-group">
                            <span class="input-group-text d-flex justify-content-center align-items-center"
                                style="width: 46px;"><i class="bi bi-box-seam"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="{{ old('nombre') }}" placeholder="Número de lote">
                        </div>
                        @if (session('open_modal') === 'createLoteModal')
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_vencimiento">Fecha de vencimiento</label>
                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control"
                                value="{{ old('fecha_vencimiento') }}">
                            @if (session('open_modal') === 'createLoteModal')
                                @error('fecha_vencimiento')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_fabricacion">Fecha de fabricación</label>
                            <input type="date" name="fecha_fabricacion" id="fecha_fabricacion" class="form-control"
                                value="{{ old('fecha_fabricacion') }}">
                            @if (session('open_modal') === 'createLoteModal')
                                @error('fecha_fabricacion')
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

    @foreach ($lotes as $lote)
        <div class="modal fade" id="editLoteModal-{{ $lote->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('admin.lotes.update', $lote->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar lote</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="producto_id-{{ $lote->id }}">Producto (*)</label>
                            <select id="producto_id-{{ $lote->id }}" name="producto_id" class="form-select">
                                <option value="">-- Selecciona un producto --</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}"
                                        {{ (string) (session('open_modal') === 'editLoteModal-' . $lote->id ? old('producto_id', $lote->producto_id) : $lote->producto_id) === (string) $producto->id ? 'selected' : '' }}>
                                        {{ $producto->nombre_comercial }} - {{ $producto->nombre_generico }}
                                    </option>
                                @endforeach
                            </select>
                            @if (session('open_modal') === 'editLoteModal-' . $lote->id)
                                @error('producto_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <label for="proveedor_id-{{ $lote->id }}">Proveedor (*)</label>
                            <select id="proveedor_id-{{ $lote->id }}" name="proveedor_id" class="form-select">
                                <option value="">-- Selecciona un proveedor --</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}"
                                        {{ (string) (session('open_modal') === 'editLoteModal-' . $lote->id ? old('proveedor_id', $lote->proveedor_id) : $lote->proveedor_id) === (string) $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @if (session('open_modal') === 'editLoteModal-' . $lote->id)
                                @error('proveedor_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <label for="nombre-{{ $lote->id }}">Número de lote (*)</label>
                            <div class="input-group">
                                <span class="input-group-text d-flex justify-content-center align-items-center"
                                    style="width: 46px;"><i class="bi bi-box-seam"></i></span>
                                <input type="text" name="nombre" id="nombre-{{ $lote->id }}"
                                    class="form-control"
                                    value="{{ session('open_modal') === 'editLoteModal-' . $lote->id ? old('nombre', $lote->nombre) : $lote->nombre }}"
                                    placeholder="Número de lote">
                            </div>
                            @if (session('open_modal') === 'editLoteModal-' . $lote->id)
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_vencimiento-{{ $lote->id }}">Fecha de vencimiento</label>
                                <input type="date" name="fecha_vencimiento"
                                    id="fecha_vencimiento-{{ $lote->id }}" class="form-control"
                                    value="{{ session('open_modal') === 'editLoteModal-' . $lote->id ? old('fecha_vencimiento', $lote->fecha_vencimiento?->format('Y-m-d')) : $lote->fecha_vencimiento?->format('Y-m-d') }}">
                                @if (session('open_modal') === 'editLoteModal-' . $lote->id)
                                    @error('fecha_vencimiento')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_fabricacion-{{ $lote->id }}">Fecha de fabricación</label>
                                <input type="date" name="fecha_fabricacion"
                                    id="fecha_fabricacion-{{ $lote->id }}" class="form-control"
                                    value="{{ session('open_modal') === 'editLoteModal-' . $lote->id ? old('fecha_fabricacion', $lote->fecha_fabricacion?->format('Y-m-d')) : $lote->fecha_fabricacion?->format('Y-m-d') }}">
                                @if (session('open_modal') === 'editLoteModal-' . $lote->id)
                                    @error('fecha_fabricacion')
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

        <div class="modal fade" id="deleteLoteModal-{{ $lote->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.lotes.destroy', $lote->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar lote</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Estas seguro de eliminar el lote <strong>{{ $lote->nombre }}</strong>?</p>
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
