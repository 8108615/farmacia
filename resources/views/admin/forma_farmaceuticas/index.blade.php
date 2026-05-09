@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Forma Farmacéutica</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFormaFarmaceuticaModal">
                <i class="bi bi-plus-circle"></i> Nueva Forma Farmacéutica
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de Forma Farmacéuticas</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.forma_farmaceuticas.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar Forma Farmacéuticas</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="Escribe nombre">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.forma_farmaceuticas.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $formaFarmaceuticas->total() }} resultado(s) para "{{ $search }}".
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
                                    @forelse ($formaFarmaceuticas as $formaFarmaceutica)
                                        <tr>
                                            <td>{{ $formaFarmaceuticas->firstItem() + $loop->index }}</td>
                                            <td>{{ $formaFarmaceutica->nombre }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editFormaFarmaceuticaModal-{{ $formaFarmaceutica->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteFormaFarmaceuticaModal-{{ $formaFarmaceutica->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">No hay Formas Farmacéuticas
                                                registradas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($formaFarmaceuticas->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $formaFarmaceuticas->firstItem() }} a {{ $formaFarmaceuticas->lastItem() }} de
                                    {{ $formaFarmaceuticas->total() }}
                                    registros
                                </small>
                                <div>
                                    {{ $formaFarmaceuticas->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createFormaFarmaceuticaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.forma_farmaceuticas.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white">Crear Forma Farmacéutica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label for="create-nombre">Nombre (*)</label>
                        <div class="input-group">
                            <span class="input-group-text d-flex justify-content-center align-items-center"
                                style="width: 46px;"><i class="bi bi-capsule"></i></span>
                            <input type="text" name="nombre" id="create-nombre" class="form-control"
                                value="{{ old('nombre') }}" placeholder="Nombre de la Forma Farmacéutica" required>
                        </div>
                        @if (session('open_modal') === 'createFormaFarmaceuticaModal')
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

    @foreach ($formaFarmaceuticas as $formaFarmaceutica)
        <div class="modal fade" id="editFormaFarmaceuticaModal-{{ $formaFarmaceutica->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST"
                    action="{{ route('admin.forma_farmaceuticas.update', $formaFarmaceutica->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="color: white">Editar Forma Farmacéutica</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="edit-nombre-{{ $formaFarmaceutica->id }}">Nombre (*)</label>
                            <div class="input-group">
                                <span class="input-group-text d-flex justify-content-center align-items-center"
                                    style="width: 46px;"><i class="bi bi-capsule"></i></span>
                                <input type="text" name="nombre" id="edit-nombre-{{ $formaFarmaceutica->id }}" class="form-control"
                                    value="{{ session('open_modal') === 'editFomaFarmaceuticaModal-' . $formaFarmaceutica->id ? old('nombre', $formaFarmaceutica->nombre) : $formaFarmaceutica->nombre }}"
                                    required>
                            </div>
                            @if (session('open_modal') === 'editFormaFarmaceuticaModal-' . $formaFarmaceutica->id)
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

        <div class="modal fade" id="deleteFormaFarmaceuticaModal-{{ $formaFarmaceutica->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST"
                    action="{{ route('admin.forma_farmaceuticas.destroy', $formaFarmaceutica->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar Forma Farmaceuticas</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">Esta seguro de eliminar la Forma Farmaceutica <strong>{{ $formaFarmaceutica->nombre }}</strong>?
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

