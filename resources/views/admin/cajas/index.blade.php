@extends('layouts.admin')

@section('content')
<div class="page-heading d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h3 class="mb-0">Cajas</h3>
    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#nueva-caja">
        <i class="bi bi-plus-circle me-1" aria-hidden="true"></i>Nueva caja
    </button>
</div>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

<section class="card">
    <div class="card-body">
        <h5 class="mb-3">Cajas registradas</h5>
        <form action="{{ route('admin.cajas.index') }}" method="GET" class="row g-2 align-items-end mb-3">
            <div class="col">
                <label for="buscar-caja" class="form-label">Buscar por nombre de caja</label>
                <input id="buscar-caja" name="buscar" type="search" maxlength="100" value="{{ $buscar }}" class="form-control">
            </div>
            <div class="col-auto"><button class="btn btn-primary">Buscar</button></div>
            <div class="col-auto"><a href="{{ route('admin.cajas.index') }}" class="btn btn-outline-secondary">Limpiar</a></div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead><tr><th scope="col">#</th><th scope="col">Nombre</th><th scope="col">Sucursal</th><th scope="col">Estado</th><th scope="col" class="text-end">Acciones</th></tr></thead>
                <tbody>
                    @forelse ($cajas as $caja)
                        <tr>
                            <td>{{ $cajas->firstItem() + $loop->index }}</td>
                            <td>{{ $caja->nombre }}</td>
                            <td>{{ $caja->sucursal?->nombre ?? '—' }}</td>
                            <td><span class="badge {{ $caja->estado === 'abierta' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($caja->estado) }}</span></td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editar-caja-{{ $caja->id }}" title="Editar caja" aria-label="Editar {{ $caja->nombre }}">
                                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                </button>
                                @if ($caja->estado === 'cerrada' && !$caja->arqueos_exists)
                                    <form class="d-inline" method="POST" action="{{ route('admin.cajas.destroy', $caja->id) }}"
                                        data-eliminar-caja="{{ $caja->nombre }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Eliminar caja" aria-label="Eliminar {{ $caja->nombre }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled aria-label="Eliminación bloqueada">
                                        <i class="bi bi-lock" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">{{ $buscar === '' ? 'Todavía no hay cajas registradas.' : 'No se encontraron cajas con ese nombre.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $cajas->links('pagination::bootstrap-5') }}
    </div>
</section>

<div class="modal fade" id="nueva-caja" tabindex="-1" aria-labelledby="nueva-caja-titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.cajas.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="nueva-caja-titulo">Registrar caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                @if ($errors->any())
                    <p class="text-danger small" role="alert">{{ $errors->first() }}</p>
                @endif
                <label for="caja-sucursal" class="form-label">Sucursal</label>
                <select id="caja-sucursal" name="sucursal_id" class="form-select mb-3" required>
                    <option value="">Seleccionar sucursal</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" @selected(old('sucursal_id') == $sucursal->id)>{{ $sucursal->nombre }}</option>
                    @endforeach
                </select>
                <label for="caja-nombre" class="form-label">Nombre de la caja</label>
                <input id="caja-nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" maxlength="255" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
@foreach ($cajas as $caja)
    <div class="modal fade" id="editar-caja-{{ $caja->id }}" tabindex="-1" aria-hidden="true"
        aria-labelledby="editar-caja-titulo-{{ $caja->id }}">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.cajas.update', $caja->id) }}" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" name="caja_edicion" value="{{ $caja->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="editar-caja-titulo-{{ $caja->id }}">Editar caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">{{ $caja->sucursal?->nombre }}</p>
                    <label for="editar-nombre-{{ $caja->id }}" class="form-label">Nombre de la caja</label>
                    <input id="editar-nombre-{{ $caja->id }}" name="nombre" class="form-control" maxlength="255" required
                        value="{{ (string) old('caja_edicion') === (string) $caja->id ? old('nombre') : $caja->nombre }}">
                    @if ((string) old('caja_edicion') === (string) $caja->id && $errors->editarCaja->has('nombre'))
                        <p class="text-danger small mt-2" role="alert">{{ $errors->editarCaja->first('nombre') }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection

@if ($errors->editarCaja->any())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('editar-caja-' + @json((int) old('caja_edicion')));
                if (modal) bootstrap.Modal.getOrCreateInstance(modal).show();
            });
        </script>
    @endpush
@endif

@if ($errors->has('sucursal_id') || $errors->has('nombre'))
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('nueva-caja')).show();
            });
        </script>
    @endpush
@endif

@push('scripts')
    <script>
        document.querySelectorAll('form[data-eliminar-caja]').forEach(formulario => {
            let confirmacionPendiente = false;
            formulario.addEventListener('submit', async evento => {
                evento.preventDefault();
                if (confirmacionPendiente) return;

                confirmacionPendiente = true;
                const titulo = '¿Eliminar esta caja?';
                const mensaje = 'Se eliminará «' + formulario.dataset.eliminarCaja + '».';
                try {
                    const confirmada = typeof Swal === 'undefined'
                        ? window.confirm(titulo + '\n' + mensaje)
                        : (await Swal.fire({
                            titleText: titulo,
                            text: mensaje,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#dc3545',
                            focusCancel: true,
                            reverseButtons: true
                        })).isConfirmed;

                    if (confirmada) formulario.submit();
                } finally {
                    confirmacionPendiente = false;
                }
            });
        });
    </script>
@endpush
