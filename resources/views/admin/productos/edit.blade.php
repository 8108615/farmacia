@extends('layouts.admin')

@section('content')
    @php
        $imagenActual = null;

        if (!empty($producto->imagen)) {
            $imagenActual = \Illuminate\Support\Str::startsWith($producto->imagen, ['http://', 'https://'])
                ? $producto->imagen
                : asset('storage/' . ltrim($producto->imagen, '/'));
        }
    @endphp

    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Producto: {{ $producto->nombre_comercial }}</h3>
            <a href="{{ route('admin.productos.index') }}" class="btn btn-light-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Editar producto</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-3" role="alert">
                                <strong>Se encontraron errores en el formulario.</strong>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.productos.update', $producto->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="categoria_id" class="form-label">Categoría (*)</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-tags-fill"></i></span>
                                        <select name="categoria_id" id="categoria_id"
                                            class="form-select @error('categoria_id') is-invalid @enderror" required>
                                            <option value="">Selecciona una categoría</option>
                                            @foreach ($categorias as $categoria)
                                                <option value="{{ $categoria->id }}" @selected((string) old('categoria_id', $producto->categoria_id) === (string) $categoria->id)>
                                                    {{ $categoria->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('categoria_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="laboratorio_id" class="form-label">Laboratorio</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-capsule"></i></span>
                                        <select name="laboratorio_id" id="laboratorio_id"
                                            class="form-select @error('laboratorio_id') is-invalid @enderror">
                                            <option value="">Selecciona un laboratorio</option>
                                            @foreach ($laboratorios as $laboratorio)
                                                <option value="{{ $laboratorio->id }}" @selected((string) old('laboratorio_id', $producto->laboratorio_id) === (string) $laboratorio->id)>
                                                    {{ $laboratorio->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('laboratorio_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="forma_farmaceutica_id" class="form-label">Forma farmacéutica</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-capsule-pill"></i></span>
                                        <select name="forma_farmaceutica_id" id="forma_farmaceutica_id"
                                            class="form-select @error('forma_farmaceutica_id') is-invalid @enderror">
                                            <option value="">Selecciona una forma farmacéutica</option>
                                            @foreach ($formaFarmaceuticas as $formaFarmaceutica)
                                                <option value="{{ $formaFarmaceutica->id }}" @selected((string) old('forma_farmaceutica_id', $producto->forma_farmaceutica_id) === (string) $formaFarmaceutica->id)>
                                                    {{ $formaFarmaceutica->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('forma_farmaceutica_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="presentacion_id" class="form-label">Presentación</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-box-seam"></i></span>
                                        <select name="presentacion_id" id="presentacion_id"
                                            class="form-select @error('presentacion_id') is-invalid @enderror">
                                            <option value="">Selecciona una presentación</option>
                                            @foreach ($presentaciones as $presentacion)
                                                <option value="{{ $presentacion->id }}" @selected((string) old('presentacion_id', $producto->presentacion_id) === (string) $presentacion->id)>
                                                    {{ $presentacion->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('presentacion_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="codigo_producto" class="form-label">Código de producto (*)</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-upc"></i></span>
                                        <input type="text" name="codigo_producto" id="codigo_producto"
                                            class="form-control @error('codigo_producto') is-invalid @enderror"
                                            value="{{ old('codigo_producto', $producto->codigo_producto) }}" maxlength="50"
                                            placeholder="Ej: PRD-0001" required>
                                    </div>
                                    @error('codigo_producto')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="codigo_barra" class="form-label">Código de barra</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" name="codigo_barra" id="codigo_barra"
                                            class="form-control @error('codigo_barra') is-invalid @enderror"
                                            value="{{ old('codigo_barra', $producto->codigo_barra) }}" maxlength="50"
                                            placeholder="Ej: 7750123456789">
                                    </div>
                                    @error('codigo_barra')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="nombre_comercial" class="form-label">Nombre comercial (*)</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-bag-fill"></i></span>
                                        <input type="text" name="nombre_comercial" id="nombre_comercial"
                                            class="form-control @error('nombre_comercial') is-invalid @enderror"
                                            value="{{ old('nombre_comercial', $producto->nombre_comercial) }}"
                                            maxlength="255" placeholder="Nombre comercial del producto" required>
                                    </div>
                                    @error('nombre_comercial')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="nombre_generico" class="form-label">Nombre genérico (*)</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-capsule-pill"></i></span>
                                        <input type="text" name="nombre_generico" id="nombre_generico"
                                            class="form-control @error('nombre_generico') is-invalid @enderror"
                                            value="{{ old('nombre_generico', $producto->nombre_generico) }}"
                                            maxlength="255" placeholder="Principio activo" required>
                                    </div>
                                    @error('nombre_generico')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-8">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label for="concentracion" class="form-label">Concentración</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text d-flex justify-content-center align-items-center"
                                                    style="width: 46px;"><i class="bi bi-eyedropper"></i></span>
                                                <input type="text" name="concentracion" id="concentracion"
                                                    class="form-control @error('concentracion') is-invalid @enderror"
                                                    value="{{ old('concentracion', $producto->concentracion) }}"
                                                    maxlength="100" placeholder="Ej: 500 mg">
                                            </div>
                                            @error('concentracion')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="unidad_medida" class="form-label">Unidad de medida</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text d-flex justify-content-center align-items-center"
                                                    style="width: 46px;"><i class="bi bi-rulers"></i></span>
                                                <select name="unidad_medida" id="unidad_medida"
                                                    class="form-select @error('unidad_medida') is-invalid @enderror">
                                                    <option value="">Selecciona una unidad</option>
                                                    <option value="kg" @selected(old('unidad_medida', $producto->unidad_medida) === 'kg')>kg - kilogramo
                                                    </option>
                                                    <option value="g" @selected(old('unidad_medida', $producto->unidad_medida) === 'g')>g - gramo
                                                    </option>
                                                    <option value="mg" @selected(old('unidad_medida', $producto->unidad_medida) === 'mg')>mg - miligramo
                                                    </option>
                                                    <option value="mcg" @selected(old('unidad_medida', $producto->unidad_medida) === 'mcg')>mcg - microgramo
                                                    </option>
                                                    <option value="l" @selected(old('unidad_medida', $producto->unidad_medida) === 'l')>l - litro
                                                    </option>
                                                    <option value="ml" @selected(old('unidad_medida', $producto->unidad_medida) === 'ml')>ml - mililitro
                                                    </option>
                                                    <option value="mmol" @selected(old('unidad_medida', $producto->unidad_medida) === 'mmol')>mmol - milimol
                                                    </option>
                                                    <option value="mEq" @selected(old('unidad_medida', $producto->unidad_medida) === 'mEq')>mEq -
                                                        miliequivalente
                                                    </option>
                                                    <option value="UI" @selected(old('unidad_medida', $producto->unidad_medida) === 'UI')>UI - Unidad
                                                        Internacional
                                                    </option>
                                                    <option value="%" @selected(old('unidad_medida', $producto->unidad_medida) === '%')>% - porcentaje
                                                    </option>
                                                </select>
                                            </div>
                                            @error('unidad_medida')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="accion_terapeutica" class="form-label">Acción terapéutica</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text d-flex justify-content-center align-items-center"
                                                    style="width: 46px;"><i class="bi bi-heart-pulse"></i></span>
                                                <input type="text" name="accion_terapeutica" id="accion_terapeutica"
                                                    class="form-control @error('accion_terapeutica') is-invalid @enderror"
                                                    value="{{ old('accion_terapeutica', $producto->accion_terapeutica) }}"
                                                    maxlength="255"
                                                    placeholder="Ej: Analgésico, antiinflamatorio, antibiótico">
                                            </div>
                                            @error('accion_terapeutica')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="usa_receta" id="usa_receta"
                                                    class="form-check-input @error('usa_receta') is-invalid @enderror"
                                                    value="1" @checked(old('usa_receta', $producto->usa_receta))>
                                                <label class="form-check-label" for="usa_receta">¿Requiere receta?</label>
                                            </div>
                                            @error('usa_receta')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="imagen" class="form-label">Imagen</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex justify-content-center align-items-center"
                                            style="width: 46px;"><i class="bi bi-image"></i></span>
                                        <input type="file" name="imagen" id="imagen"
                                            class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
                                    </div>
                                    <div class="border rounded bg-light mt-2 d-flex align-items-center justify-content-center"
                                        style="height: 150px; overflow: hidden;">
                                        <img id="imagen_preview" src="{{ $imagenActual ?? '' }}"
                                            alt="Vista previa de la imagen del producto"
                                            class="img-fluid {{ $imagenActual ? '' : 'd-none' }}"
                                            style="max-height: 100%; width: 100%; object-fit: contain;">
                                        <small id="imagen_placeholder"
                                            class="text-muted {{ $imagenActual ? 'd-none' : '' }}">Sin
                                            previsualización</small>
                                    </div>
                                    @error('imagen')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('admin.productos.show', $producto->id) }}"
                                    class="btn btn-light-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-success">Actualizar producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('imagen');
            const imagePreview = document.getElementById('imagen_preview');
            const imagePlaceholder = document.getElementById('imagen_placeholder');
            const initialImageSrc = @json($imagenActual);
            let objectUrl = null;

            if (!imageInput || !imagePreview || !imagePlaceholder) {
                return;
            }

            const updateImagePreview = function() {
                const selectedFile = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }

                if (!selectedFile) {
                    if (initialImageSrc) {
                        imagePreview.src = initialImageSrc;
                        imagePreview.classList.remove('d-none');
                        imagePlaceholder.classList.add('d-none');
                    } else {
                        imagePreview.classList.add('d-none');
                        imagePreview.removeAttribute('src');
                        imagePlaceholder.classList.remove('d-none');
                        imagePlaceholder.textContent = 'Sin previsualización';
                    }
                    return;
                }

                if (!selectedFile.type.startsWith('image/')) {
                    imagePreview.classList.add('d-none');
                    imagePreview.removeAttribute('src');
                    imagePlaceholder.classList.remove('d-none');
                    imagePlaceholder.textContent = 'El archivo seleccionado no es una imagen';
                    return;
                }

                objectUrl = URL.createObjectURL(selectedFile);
                imagePreview.src = objectUrl;
                imagePreview.classList.remove('d-none');
                imagePlaceholder.classList.add('d-none');
            };

            imagePreview.addEventListener('error', function() {
                imagePreview.classList.add('d-none');
                imagePlaceholder.classList.remove('d-none');
                imagePlaceholder.textContent = 'No se pudo cargar la imagen';
            });

            imagePreview.addEventListener('load', function() {
                imagePlaceholder.classList.add('d-none');
                imagePlaceholder.textContent = 'Sin previsualización';
            });

            imageInput.addEventListener('change', updateImagePreview);
            updateImagePreview();
        });
    </script>
@endpush
