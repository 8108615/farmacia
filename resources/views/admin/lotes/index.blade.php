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
                                        <span class="input-group-text d-flex justify-content-center align-items-center" style="width: 46px;"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Escribe el nombre del lote">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.lotes.index') }}" class="btn btn-light-secondary w-100">Limpiar</a>
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
                                        <th style="width: 80px;">#</th>
                                        <th>Nombre</th>
                                        <th style="width: 220px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lotes as $lote)
                                        <tr>
                                            <td>{{ $lotes->firstItem() + $loop->index }}</td>
                                            <td>{{ $lote->nombre }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editLoteModal-{{ $lote->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteLoteModal-{{ $lote->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">No hay lotes registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($lotes->count() > 0)
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $lotes->firstItem() }} a {{ $lotes->lastItem() }} de {{ $lotes->total() }} registros
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre">Nombre (*)</label>
                        <div class="input-group">
                            <span class="input-group-text d-flex justify-content-center align-items-center" style="width: 46px;"><i class="bi bi-tags-fill"></i></span>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" placeholder="Nombre del lote">
                        </div>
                        @if (session('open_modal') === 'createLoteModal')
                            @error('nombre')
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

    @foreach ($lotes as $lote)
        <div class="modal fade" id="editLoteModal-{{ $lote->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('admin.lotes.update', $lote->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar lote</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombre-{{ $lote->id }}">Nombre (*)</label>
                            <div class="input-group">
                                <span class="input-group-text d-flex justify-content-center align-items-center" style="width: 46px;"><i class="bi bi-tags-fill"></i></span>
                                <input type="text" name="nombre" id="nombre-{{ $lote->id }}" class="form-control" value="{{ session('open_modal') === 'editLoteModal-' . $lote->id ? old('nombre', $lote->nombre) : $lote->nombre }}" placeholder="Nombre del lote">
                            </div>
                            @if (session('open_modal') === 'editLoteModal-' . $lote->id)
                                @error('nombre')
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

        <div class="modal fade" id="deleteLoteModal-{{ $lote->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.lotes.destroy', $lote->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar lote</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">¿Estás seguro de eliminar el lote <strong>{{ $lote->nombre }}</strong>?</p>
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
