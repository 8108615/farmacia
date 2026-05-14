@extends('layouts.admin')

@section('content')
    @php
        $imagenUrl = null;

        if (!empty($producto->imagen)) {
            $imagenUrl = \Illuminate\Support\Str::startsWith($producto->imagen, ['http://', 'https://'])
                ? $producto->imagen
                : asset('storage/' . ltrim($producto->imagen, '/'));
        }
    @endphp

    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="mb-0">Detalle del producto</h3>
            <a href="{{ route('admin.productos.index') }}" class="btn btn-light-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card product-show-card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <h4 class="card-title mb-1">{{ $producto->nombre_comercial }}</h4>
                                <p class="text-muted mb-0">{{ $producto->nombre_generico }}</p>
                            </div>
                            <span
                                class="badge {{ $producto->usa_receta ? 'bg-warning text-dark' : 'bg-success' }} px-3 py-2">
                                {{ $producto->usa_receta ? 'Requiere receta' : 'Venta libre' }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="row g-4">
                            <div class="col-12 col-lg-4">
                                <div class="product-image-wrapper">
                                    @if ($imagenUrl)
                                        <img src="{{ $imagenUrl }}" alt="{{ $producto->nombre_comercial }}"
                                            class="img-fluid product-image">
                                    @else
                                        <div class="product-image-placeholder">
                                            <i class="bi bi-image fs-2"></i>
                                            <p class="mb-0 mt-2">Sin imagen registrada</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-lg-8">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <div class="info-block h-100">
                                            <small class="info-label">Código de producto</small>
                                            <p class="info-value">{{ $producto->codigo_producto }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="info-block h-100">
                                            <small class="info-label">Código de barra</small>
                                            <p class="info-value">{{ $producto->codigo_barra ?: 'No definido' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="info-block h-100">
                                            <small class="info-label">Concentración</small>
                                            <p class="info-value">{{ $producto->concentracion ?: 'No definida' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="info-block h-100">
                                            <small class="info-label">Unidad de medida</small>
                                            <p class="info-value">{{ $producto->unidad_medida ?: 'No definida' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="info-block h-100">
                                            <small class="info-label">Acción terapéutica</small>
                                            <p class="info-value">{{ $producto->accion_terapeutica ?: 'No definida' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="meta-card">
                                    <small class="meta-label">Categoría</small>
                                    <p class="meta-value">{{ optional($producto->categoria)->nombre ?: 'No definida' }}</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="meta-card">
                                    <small class="meta-label">Laboratorio</small>
                                    <p class="meta-value">{{ optional($producto->laboratorio)->nombre ?: 'No definido' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="meta-card">
                                    <small class="meta-label">Forma farmacéutica</small>
                                    <p class="meta-value">
                                        {{ optional($producto->formaFarmaceutica)->nombre ?: 'No definida' }}</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="meta-card">
                                    <small class="meta-label">Presentación</small>
                                    <p class="meta-value">{{ optional($producto->presentacion)->nombre ?: 'No definida' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="meta-card">
                                    <small class="meta-label">Fecha y hora de creación</small>
                                    <p class="meta-value">
                                        {{ optional($producto->created_at)->format('d/m/Y H:i:s') ?: 'No definida' }}</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="meta-card">
                                    <small class="meta-label">Fecha y hora de actualización</small>
                                    <p class="meta-value">
                                        {{ optional($producto->updated_at)->format('d/m/Y H:i:s') ?: 'No definida' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.productos.index') }}" class="btn btn-light-secondary">Cerrar</a>
                            <a href="{{ route('admin.productos.edit', $producto->id) }}" class="btn btn-success">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .product-show-card {
            border-radius: 0.8rem;
        }

        .product-image-wrapper {
            min-height: 280px;
            border: 1px solid #e9ecef;
            border-radius: 0.75rem;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .product-image {
            max-height: 260px;
            width: 100%;
            object-fit: contain;
        }

        .product-image-placeholder {
            text-align: center;
            color: #6c757d;
        }

        .info-block {
            border: 1px solid #e9ecef;
            border-radius: 0.65rem;
            padding: 0.8rem 1rem;
            background-color: #ffffff;
        }

        .info-label,
        .meta-label {
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .info-value,
        .meta-value {
            margin: 0.35rem 0 0;
            color: #25396f;
            font-size: 1rem;
            font-weight: 600;
            word-break: break-word;
        }

        .meta-card {
            height: 100%;
            border: 1px solid #e9ecef;
            border-radius: 0.65rem;
            padding: 0.8rem 0.9rem;
            background-color: #f8f9fa;
        }
    </style>
@endpush
