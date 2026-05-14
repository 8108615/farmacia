@extends('layouts.admin')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Productos</h3>
            <button type="button" class="btn btn-primary" onclick="window.location='{{ route('admin.productos.create') }}'">
                <i class="bi bi-plus-circle"></i> Nuevo producto
            </button>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Listado de productos</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.productos.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="search" class="form-label mb-1">Buscar producto</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-search"></i></span>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ $search ?? '' }}" placeholder="Escribe el nombre del producto">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    <a href="{{ route('admin.productos.index') }}"
                                        class="btn btn-light-secondary w-100">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        @if (!empty($search))
                            <div class="alert alert-info py-2 mb-3" role="alert">
                                Se encontraron {{ $productos->total() }} resultado(s) para "{{ $search }}".
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>

                                        <th>Categoría</th>
                                        <th>Laboratorio</th>
                                        <th>Forma farmacéutica</th>
                                        <th>Presentación</th>
                                        <th>Código producto</th>
                                        <th>Código barra</th>
                                        <th>Nombre comercial</th>
                                        <th>Nombre genérico</th>
                                        <th>Concentración</th>


                                        <th>Receta</th>


                                        <th style="width: 160px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($productos as $producto)
                                        <tr>
                                            <td>{{ $productos->firstItem() + $loop->index }}</td>

                                            <td>{{ optional($producto->categoria)->nombre ?? 'No definida' }}</td>
                                            <td>{{ optional($producto->laboratorio)->nombre ?? 'No definido' }}</td>
                                            <td>{{ optional($producto->formaFarmaceutica)->nombre ?? 'No definida' }}</td>
                                            <td>{{ optional($producto->presentacion)->nombre ?? 'No definida' }}</td>
                                            <td>{{ $producto->codigo_producto }}</td>
                                            <td>{{ $producto->codigo_barra ?? 'No definido' }}</td>
                                            <td>{{ $producto->nombre_comercial }}</td>
                                            <td>{{ $producto->nombre_generico }}</td>
                                            <td>{{ $producto->concentracion . ' ' . ($producto->unidad_medida ?? '') }}
                                            </td>



                                            <td>
                                                <span
                                                    class="badge {{ $producto->usa_receta ? 'bg-warning text-dark' : 'bg-success' }}">
                                                    {{ $producto->usa_receta ? 'Sí' : 'No' }}
                                                </span>
                                            </td>


                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" title="Ver detalle"
                                                    onclick="window.location='{{ route('admin.productos.show', $producto->id) }}'">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success" title="Editar"
                                                    onclick="window.location='{{ route('admin.productos.edit', $producto->id) }}'">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteProductoModal-{{ $producto->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="18" class="text-center text-muted py-4">No hay productos
                                                registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($productos->count() > 0)
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
                                <small class="text-muted">
                                    Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de
                                    {{ $productos->total() }} registros
                                </small>
                                <div>
                                    {{ $productos->links('vendor.pagination.bootstrap-5-no-summary') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>



    @foreach ($productos as $producto)
        <div class="modal fade" id="deleteProductoModal-{{ $producto->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.productos.destroy', $producto->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="color: white">Eliminar producto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">¿Estás seguro de eliminar el producto
                            <strong>{{ $producto->nombre_comercial }}</strong>?
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
