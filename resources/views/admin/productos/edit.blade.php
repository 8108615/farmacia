@extends('layouts.admin')

@section('content')
	<div class="page-heading mb-4">
		<div class="d-flex justify-content-between align-items-center">
			<h2 class="mb-0">Pro: {{ $producto->nombre_comercial }}uctos</h2>
			<a href="{{ route('admin.productos.index') }}" class="btn btn-light-secondary">
				<i class="bi bi-arrow-left"></i> Volver al listado
			</a>
		</div>
	</div>

	<section class="section">
		<div class="card shadow-sm" style="border-radius:14px;">
			<div class="card-body p-4">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<div>
						<h5 class="mb-0">Editar producto</h5>
						<small class="text-muted">Modifica los datos del producto</small>
					</div>
					<div>
						<!-- espacio para acciones si se requiere -->
					</div>
				</div>

				<form method="POST" action="{{ route('admin.productos.update', $producto->id) }}" enctype="multipart/form-data">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-lg-8">
							<div class="row g-3">
								<div class="col-md-4">
									<label for="categoria_id" class="form-label">Categoría (*)</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-tags-fill"></i></span>
										<select name="categoria_id" id="categoria_id" class="form-select" required>
											<option value="">Selecciona una categoría</option>
											@foreach($categorias as $categoria)
												<option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
											@endforeach
										</select>
									</div>
									@error('categoria_id')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-4">
									<label for="laboratorio_id" class="form-label">Laboratorio</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-flask"></i></span>
										<select name="laboratorio_id" id="laboratorio_id" class="form-select">
											<option value="">Selecciona un laboratorio</option>
											@foreach($laboratorios as $lab)
												<option value="{{ $lab->id }}" {{ old('laboratorio_id', $producto->laboratorio_id) == $lab->id ? 'selected' : '' }}>{{ $lab->nombre }}</option>
											@endforeach
										</select>
									</div>
									@error('laboratorio_id')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-4">
									<label for="forma_farmaceutica_id" class="form-label">Forma farmacéutica</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-capsule"></i></span>
										<select name="forma_farmaceutica_id" id="forma_farmaceutica_id" class="form-select">
											<option value="">Selecciona una forma farmacéutica</option>
											@foreach($formaFarmaceuticas as $ff)
												<option value="{{ $ff->id }}" {{ old('forma_farmaceutica_id', $producto->forma_farmaceutica_id) == $ff->id ? 'selected' : '' }}>{{ $ff->nombre }}</option>
											@endforeach
										</select>
									</div>
									@error('forma_farmaceutica_id')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-4">
									<label for="presentacion_id" class="form-label">Presentación</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-box-seam"></i></span>
										<select name="presentacion_id" id="presentacion_id" class="form-select">
											<option value="">Selecciona una presentación</option>
											@foreach($presentaciones as $pres)
												<option value="{{ $pres->id }}" {{ old('presentacion_id', $producto->presentacion_id) == $pres->id ? 'selected' : '' }}>{{ $pres->nombre }}</option>
											@endforeach
										</select>
									</div>
									@error('presentacion_id')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-4">
									<label for="codigo_producto" class="form-label">Código de producto (*)</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-upc"></i></span>
										<input type="text" name="codigo_producto" id="codigo_producto" class="form-control" value="{{ old('codigo_producto', $producto->codigo_producto) }}" required maxlength="50" placeholder="Ej: PRD-0001">
									</div>
									@error('codigo_producto')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-4">
									<label for="codigo_barra" class="form-label">Código de barra</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-upc-scan"></i></span>
										<input type="text" name="codigo_barra" id="codigo_barra" class="form-control" value="{{ old('codigo_barra', $producto->codigo_barra) }}" maxlength="50" placeholder="Ej: 7750123456789">
									</div>
									@error('codigo_barra')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-6">
									<label for="nombre_comercial" class="form-label">Nombre comercial (*)</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-bag-fill"></i></span>
										<input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control" value="{{ old('nombre_comercial', $producto->nombre_comercial) }}" required maxlength="255" placeholder="Nombre comercial del producto">
									</div>
									@error('nombre_comercial')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-6">
									<label for="nombre_generico" class="form-label">Nombre genérico (*)</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-capsule-pill"></i></span>
										<input type="text" name="nombre_generico" id="nombre_generico" class="form-control" value="{{ old('nombre_generico', $producto->nombre_generico) }}" required maxlength="255" placeholder="Principio activo">
									</div>
									@error('nombre_generico')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-4">
									<label for="concentracion" class="form-label">Concentración</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-eyedropper"></i></span>
										<input type="text" name="concentracion" id="concentracion" class="form-control" value="{{ old('concentracion', $producto->concentracion) }}" maxlength="100" placeholder="Ej: 500 mg">
									</div>
									@error('concentracion')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-md-4">
									<label for="unidad_medida" class="form-label">Unidad de medida</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-rulers"></i></span>
										<select name="unidad_medida" id="unidad_medida" class="form-select">
											<option value="">Ej: mg, ml, g</option>
											<option value="kg" {{ old('unidad_medida', $producto->unidad_medida) == 'kg' ? 'selected' : '' }}>kg - kilogramo</option>
											<option value="g" {{ old('unidad_medida', $producto->unidad_medida) == 'g' ? 'selected' : '' }}>g - gramo</option>
											<option value="mg" {{ old('unidad_medida', $producto->unidad_medida) == 'mg' ? 'selected' : '' }}>mg - miligramo</option>
											<option value="mcg" {{ old('unidad_medida', $producto->unidad_medida) == 'mcg' ? 'selected' : '' }}>mcg - microgramo</option>
											<option value="l" {{ old('unidad_medida', $producto->unidad_medida) == 'l' ? 'selected' : '' }}>l - litro</option>
											<option value="ml" {{ old('unidad_medida', $producto->unidad_medida) == 'ml' ? 'selected' : '' }}>ml - mililitro</option>
											<option value="mmol" {{ old('unidad_medida', $producto->unidad_medida) == 'mmol' ? 'selected' : '' }}>mmol - milimol</option>
											<option value="mEq" {{ old('unidad_medida', $producto->unidad_medida) == 'mEq' ? 'selected' : '' }}>mEq - miliequivalente</option>
											<option value="UI" {{ old('unidad_medida', $producto->unidad_medida) == 'UI' ? 'selected' : '' }}>UI - Unidad Internacional</option>
											<option value="%" {{ old('unidad_medida', $producto->unidad_medida) == '%' ? 'selected' : '' }}>% - porcentaje</option>
										</select>
									</div>
									@error('unidad_medida')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-12">
									<label for="accion_terapeutica" class="form-label">Acción terapéutica</label>
									<div class="input-group">
										<span class="input-group-text" style="width:46px;"><i class="bi bi-heart-pulse"></i></span>
										<input type="text" name="accion_terapeutica" id="accion_terapeutica" class="form-control" value="{{ old('accion_terapeutica', $producto->accion_terapeutica) }}" maxlength="255" placeholder="Ej: Analgésico, antiinflamatorio">
									</div>
									@error('accion_terapeutica')<small class="text-danger">{{ $message }}</small>@enderror
								</div>

								<div class="col-12">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" id="usa_receta" name="usa_receta" {{ old('usa_receta', $producto->usa_receta) ? 'checked' : '' }}>
										<label class="form-check-label ms-2" for="usa_receta">¿Requiere receta?</label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<label for="imagen" class="form-label">Imagen</label>
							<div class="mb-2">
								<div class="input-group">
									<span class="input-group-text" style="width:46px;"><i class="bi bi-image"></i></span>
									<input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
								</div>
								@error('imagen')<small class="text-danger">{{ $message }}</small>@enderror
							</div>

							<div class="border rounded p-3" style="background:#fafbfd; min-height:320px; display:flex; align-items:center; justify-content:center;">
								@php
									$imgSrc = null;
									if(!empty($producto->imagen)){
										if(filter_var($producto->imagen, FILTER_VALIDATE_URL)){
											$imgSrc = $producto->imagen;
										} elseif(file_exists(public_path('storage/' . $producto->imagen))) {
											$imgSrc = asset('storage/' . $producto->imagen);
										} elseif(file_exists(public_path($producto->imagen))) {
											$imgSrc = asset($producto->imagen);
										}
									}
								@endphp

								@if($imgSrc)
									<img id="imagenPreview" src="{{ $imgSrc }}" alt="{{ $producto->nombre_comercial }}" class="img-fluid rounded" style="max-height:280px; object-fit:contain;">
								@else
									<div id="imagenPlaceholder" class="text-center text-muted">Sin previsualización</div>
									<img id="imagenPreview" src="" alt="Sin Previsualizacion" style="max-width:100%; max-height:100%; object-findex'tain; display:none;">
								@endif
							</div>
						</div>
					</div>

					<hr class="my-4">

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.productos.index') }}" class="btn btn-light-secondary">Cancelar</a>
						<button type="submit" class="btn btn-success">Actualizar producto</button>
					</div>
				</form>
			</div>
		</div>
	</section>
@endsection

@push('scripts')
	<script>
		(function() {
			const imgInput = document.getElementById('imagen');
			const previewImg = document.getElementById('imagenPreview');
			const placeholder = document.getElementById('imagenPlaceholder');

			if (!imgInput) return;

			if (previewImg && previewImg.src) {
				previewImg.style.display = 'block';
				if (placeholder) placeholder.style.display = 'none';
			}

			imgInput.addEventListener('change', function() {
				const file = this.files && this.files[0];
				if (!file) {
					if (previewImg) { previewImg.src = ''; previewImg.style.display = 'none'; }
					if (placeholder) placeholder.style.display = 'block';
					return;
				}

				const reader = new FileReader();
				reader.onload = function(e) {
					if (previewImg) {
						previewImg.src = e.target.result;
						previewImg.style.display = 'block';
					}
					if (placeholder) placeholder.style.display = 'none';
				};
				reader.readAsDataURL(file);
			});
		})();
	</script>
@endpush
