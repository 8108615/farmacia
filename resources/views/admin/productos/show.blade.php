@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-0">Detalles del Producto</h3>
                <small class="text-muted">Ficha detallada y profesional</small>
            </div>
            <div>
                <a href="{{ route('admin.productos.index') }}" class="btn btn-light-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h1 class="h4 mb-1 fw-bold">{{ $producto->nombre_comercial }}</h1>
                                        <div class="text-muted">{{ $producto->nombre_generico }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-2">
                                            <span class="badge bg-primary">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>
                                            @if($producto->presentacion)
                                                <span class="badge bg-secondary ms-1">{{ $producto->presentacion->nombre }}</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">Creado: {{ optional($producto->created_at)->format('d/m/Y') ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <dl class="row mb-0">
                                            <dt class="col-5 text-muted small"><i class="bi bi-upc-scan me-1"></i>Código</dt>
                                            <dd class="col-7 fw-semibold mb-2">{{ $producto->codigo_producto }}</dd>

                                            <dt class="col-5 text-muted small"><i class="bi bi-upc me-1"></i>Código de barra</dt>
                                            <dd class="col-7 mb-2">{{ $producto->codigo_barra ?? '-' }}</dd>

                                            <dt class="col-5 text-muted small"><i class="bi bi-droplet-half me-1"></i>Concentración</dt>
                                            <dd class="col-7 mb-2">{{ $producto->concentracion ?? '-' }}</dd>

                                            <dt class="col-5 text-muted small"><i class="bi bi-rulers me-1"></i>Unidad</dt>
                                            <dd class="col-7 mb-2">{{ $producto->unidad_medida ?? '-' }}</dd>
                                        </dl>
                                    </div>

                                    <div class="col-md-6">
                                        <dl class="row mb-0">
                                            <dt class="col-5 text-muted small"><i class="bi bi-flask me-1"></i>Laboratorio</dt>
                                            <dd class="col-7 fw-semibold mb-2">{{ $producto->laboratorio->nombre ?? '-' }}</dd>

                                            <dt class="col-5 text-muted small"><i class="bi bi-capsule me-1"></i>Forma</dt>
                                            <dd class="col-7 mb-2">{{ optional($producto->forma_farmaceutica)->nombre ?? ($producto->forma_farmaceutica ?? '-') }}</dd>

                                            <dt class="col-5 text-muted small"><i class="bi bi-patch-check me-1"></i>Receta</dt>
                                            <dd class="col-7 mb-2">
                                                @if($producto->usa_receta)
                                                    <span class="badge bg-danger">Sí</span>
                                                @else
                                                    <span class="badge bg-success">No</span>
                                                @endif
                                            </dd>

                                            <dt class="col-5 text-muted small"><i class="bi bi-clock me-1"></i>Creado</dt>
                                            <dd class="col-7 mb-2"><small class="text-muted">{{ optional($producto->created_at)->format('d/m/Y H:i') ?? '-' }}</small></dd>
                                        </dl>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h5 class="mb-2">Acción terapéutica</h5>
                                    <p class="mb-0">{{ $producto->accion_terapeutica ?? '-' }}</p>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <a href="{{ route('admin.productos.edit', $producto) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Editar</a>

                                    <form action="{{ route('admin.productos.destroy', $producto) }}" method="POST" onsubmit="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Eliminar</button>
                                    </form>

                                    <a href="{{ route('admin.productos.index') }}" class="btn btn-light ms-auto"><i class="bi bi-list me-1"></i> Volver al listado</a>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card border-0">
                                    <div class="card-body text-center">
                                        @if($producto->imagen)
                                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre_comercial }}" class="img-fluid rounded mb-3" style="max-height:260px; object-fit:contain; width:100%;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center rounded mb-3" style="height:260px; background:#f1f5f9; color:#6c757d;">Sin imagen</div>
                                        @endif

                                        <ul class="list-group list-group-flush text-start">
                                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Requiere receta</small>
                                                @if($producto->usa_receta)
                                                    <span class="badge bg-danger">Sí</span>
                                                @else
                                                    <span class="badge bg-success">No</span>
                                                @endif
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Creado</small>
                                                <small class="text-muted">{{ optional($producto->created_at)->format('d/m/Y H:i') ?? '-' }}</small>
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Última actualización</small>
                                                <small class="text-muted">{{ optional($producto->updated_at)->format('d/m/Y H:i') ?? '-' }}</small>
                                            </li>
                                        </ul>

                                        <div class="mt-3">
                                            <a href="{{ asset('storage/' . ($producto->imagen ?? '')) }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">Abrir imagen</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
