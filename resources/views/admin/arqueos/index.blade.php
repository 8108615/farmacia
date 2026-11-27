@extends('layouts.admin')

@section('content')
<div class="page-heading d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h3 class="mb-0">Arqueos</h3>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.arqueos.reporte-cierres') }}" class="btn btn-outline-primary">
            <i class="bi bi-bar-chart me-1" aria-hidden="true"></i>Reporte de cierres
        </a>
        @if ($puedeOperar)
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#abrir-turno">
            <i class="bi bi-plus-circle me-1" aria-hidden="true"></i>Abrir turno
        </button>
        @endif
    </div>
</div>

@if ($errors->any() || $errors->editarArqueo->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() ?: $errors->editarArqueo->first() }}</div>
@endif

@php
    $simbolo = $divisa ?? 'Bs';
    $moneda = fn ($valor) => $simbolo . ' ' . number_format($valor, 2);
    $fecha = fn ($valor) => $valor ? \Illuminate\Support\Carbon::parse($valor)->format('d/m/Y H:i') : '—';
@endphp

<section class="card">
    <div class="card-body">
        <h5 class="mb-3">Historial de arqueos</h5>
        <form action="{{ route('admin.arqueos.index') }}" method="GET" class="row g-2 align-items-end mb-3">
            <div class="col">
                <label for="buscar-caja" class="form-label">Buscar por nombre de caja</label>
                <input id="buscar-caja" name="buscar" type="search" maxlength="100" value="{{ $buscar }}" class="form-control">
            </div>
            <div class="col-auto"><button class="btn btn-primary">Buscar</button></div>
            <div class="col-auto"><a href="{{ route('admin.arqueos.index') }}" class="btn btn-outline-secondary">Limpiar</a></div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th scope="col">Caja / Sucursal</th><th scope="col">Responsable</th>
                        <th scope="col">Apertura</th><th scope="col">Cierre</th>
                        <th scope="col" class="text-end">Monto inicial</th><th scope="col" class="text-end">Monto contado</th>
                        <th scope="col" class="text-end">Diferencia</th><th scope="col">Estado</th><th scope="col" class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($arqueos as $arqueo)
                        @php
                            $cerrado = $arqueo->estado === 'cerrado';
                        @endphp
                        <tr>
                            <td>{{ $arqueo->caja }}<small class="d-block text-muted">{{ $arqueo->sucursal }}</small></td>
                            <td>{{ $arqueo->usuario }}</td>
                            <td class="text-nowrap">{{ $fecha($arqueo->fecha_apertura) }}</td>
                            <td class="text-nowrap">{{ $fecha($arqueo->fecha_cierre) }}</td>
                            <td class="text-end text-nowrap">{{ $moneda($arqueo->monto_inicial) }}</td>
                            <td class="text-end text-nowrap">{{ $cerrado ? $moneda($arqueo->monto_contado) : '—' }}</td>
                            <td class="text-end text-nowrap">{{ $cerrado ? $moneda($arqueo->diferencia) : '—' }}</td>
                            <td><span class="badge {{ $cerrado ? 'bg-secondary' : 'bg-success' }}">{{ ucfirst($arqueo->estado) }}</span></td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-info btn-sm" href="{{ route('admin.arqueos.show', $arqueo->id) }}"
                                    title="Ver detalle" aria-label="Ver detalle del turno {{ $arqueo->id }}">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </a>
                                @if ($puedeOperar && !$cerrado && (int) $arqueo->usuario_id === (int) auth()->id())
                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.arqueos.cierre', $arqueo->id) }}"
                                        title="Cerrar turno" aria-label="Cerrar turno {{ $arqueo->id }}">
                                        <i class="bi bi-check-circle" aria-hidden="true"></i>
                                    </a>
                                @endif
                                @if ($cerrado)
                                    <a class="btn btn-danger btn-sm" href="{{ route('admin.arqueos.reporte', ['id' => $arqueo->id, 'descargar' => 1]) }}"
                                        title="Descargar PDF" aria-label="Descargar comprobante PDF del turno {{ $arqueo->id }}">
                                        <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.arqueos.reporte', $arqueo->id) }}"
                                        target="_blank" rel="noopener" title="Imprimir PDF" aria-label="Abrir comprobante del turno {{ $arqueo->id }} para imprimir">
                                        <i class="bi bi-printer" aria-hidden="true"></i>
                                    </a>
                                @endif
                                @if ($arqueo->puedeModificar)
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editar-arqueo-{{ $arqueo->id }}" title="Editar turno" aria-label="Editar turno {{ $arqueo->id }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </button>
                                    <form class="d-inline" method="POST" action="{{ route('admin.arqueos.destroy', $arqueo->id) }}"
                                        data-eliminar-arqueo="{{ $arqueo->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar turno" aria-label="Eliminar turno {{ $arqueo->id }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9">{{ $buscar === '' ? 'Todavía no hay arqueos.' : 'No se encontraron arqueos para esa caja.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $arqueos->links('pagination::bootstrap-5') }}
    </div>
</section>


@if ($puedeOperar)
<div class="modal fade" id="abrir-turno" tabindex="-1" aria-labelledby="abrir-turno-titulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.arqueos.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="abrir-turno-titulo">Abrir turno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                @if ($errors->any())
                    <p class="text-danger small" role="alert">{{ $errors->first() }}</p>
                @endif
                <label for="turno-caja" class="form-label">Caja disponible</label>
                <select id="turno-caja" name="caja_id" class="form-select mb-3" required @disabled($cajas->isEmpty())>
                    <option value="">Seleccionar caja</option>
                    @foreach ($cajas as $caja)
                        <option value="{{ $caja->id }}" @selected(old('caja_id') == $caja->id)>
                            {{ $caja->nombre }} · {{ $caja->sucursal?->nombre }}
                        </option>
                    @endforeach
                </select>
                @if ($cajas->isEmpty())
                    <p class="small">No hay cajas disponibles para abrir un turno. Puedes consultar las cajas registradas desde el menú Cajas.</p>
                @endif
                <label for="turno-inicial" class="form-label">Monto inicial ({{ $simbolo }})</label>
                <input id="turno-inicial" name="monto_inicial" type="number" class="form-control"
                    min="0" max="9999999999.99" step="0.01" value="{{ old('monto_inicial', '0.00') }}" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" @disabled($cajas->isEmpty())>Abrir turno</button>
            </div>
        </form>
    </div>
</div>
@endif

@foreach ($arqueos as $arqueo)
    @continue(!$arqueo->puedeModificar)
    <div class="modal fade" id="editar-arqueo-{{ $arqueo->id }}" tabindex="-1" aria-hidden="true" aria-labelledby="titulo-editar-{{ $arqueo->id }}">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('admin.arqueos.update', $arqueo->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="turno_edicion" value="{{ $arqueo->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo-editar-{{ $arqueo->id }}">Editar turno #{{ $arqueo->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>{{ $arqueo->caja }} · {{ $arqueo->sucursal }}</p>
                    <label class="form-label" for="fondo-{{ $arqueo->id }}">Monto inicial ({{ $simbolo }})</label>
                    <input id="fondo-{{ $arqueo->id }}" name="monto_inicial" type="number" class="form-control"
                        min="0" max="9999999999.99" step="0.01" required
                        value="{{ (int) old('turno_edicion') === (int) $arqueo->id ? old('monto_inicial') : $arqueo->monto_inicial }}">
                    @if ((int) old('turno_edicion') === (int) $arqueo->id && $errors->editarArqueo->any())
                        <p class="text-danger mt-2 mb-0" role="alert">{{ $errors->editarArqueo->first() }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')
    <script>
        @if ($errors->editarArqueo->any())
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('editar-arqueo-' + @json((int) old('turno_edicion')));
                if (modal) bootstrap.Modal.getOrCreateInstance(modal).show();
            });
        @endif

        document.querySelectorAll('form[data-eliminar-arqueo]').forEach(formulario => {
            let pendiente = false;
            formulario.addEventListener('submit', async evento => {
                evento.preventDefault();
                if (pendiente) return;
                pendiente = true;

                try {
                    const titulo = '¿Eliminar este turno?';
                    const mensaje = 'El turno #' + formulario.dataset.eliminarArqueo + ' se eliminará y la caja quedará disponible.';
                    const confirmado = typeof Swal === 'undefined'
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

                    if (confirmado) formulario.submit();
                } finally {
                    pendiente = false;
                }
            });
        });
    </script>
@endpush

@if ($puedeOperar && (request()->boolean('abrir') || $errors->has('caja_id') || ($errors->any() && session()->hasOldInput('caja_id'))))
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('abrir-turno')).show();
            });
        </script>
    @endpush
@endif
