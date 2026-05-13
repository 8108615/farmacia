@extends('layouts.admin')

@section('content')
	<div class="page-heading mb-4">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h3 class="mb-0">Detalle del producto</h3>
				<small class="text-muted">Ficha detallada y profesional</small>
			</div>
			<div>
				<a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary">
					<i class="bi bi-arrow-left"></i> Volver al listado
				</a>
			</div>
		</div>
	</div>

	<section class="section">
		<div class="card shadow-sm" style="border-radius:14px;">
			<div class="card-body p-4">
				<div class="row g-4">
					<!-- LEFT: Imagen -->
					<div class="col-lg-4">
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
								<img src="{{ $imgSrc }}" alt="{{ $producto->nombre_comercial }}" class="img-fluid rounded" style="max-height:280px; object-fit:contain;">
							@else
								<div class="text-center text-muted">Sin imagen</div>
							@endif
						</div>
					</div>

					<!-- RIGHT: Datos -->
					<div class="col-lg-8">
						<div class="d-flex justify-content-between align-items-start mb-3">
							<div>
								<h4 class="mb-1 fw-bold">{{ $producto->nombre_comercial }}</h4>
								<small class="text-muted">{{ $producto->nombre_generico }}</small>
							</div>
							<div>
								@if($producto->usa_receta)
									<span class="badge bg-danger">Con receta</span>
								@else
									<span class="badge bg-success">Venta libre</span>
								@endif
							</div>
						</div>



						<div class="row g-3">
							<div class="col-md-6">
								<div class="p-3 rounded" style="background:#ffffff; border:1px solid #eef1f5;">
									<small class="text-muted">CÓDIGO DE PRODUCTO</small>
									<div class="fw-semibold">{{ $producto->codigo_producto ?? 'No Definida' }}</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="p-3 rounded" style="background:#ffffff; border:1px solid #eef1f5;">
									<small class="text-muted">CÓDIGO DE BARRA</small>
									<div class="fw-semibold">{{ $producto->codigo_barra ?? 'No Definida' }}</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="p-3 rounded" style="background:#ffffff; border:1px solid #eef1f5;">
									<small class="text-muted">CONCENTRACIÓN</small>
									<div class="fw-semibold">{{ $producto->concentracion ?? 'No Definida' }}</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="p-3 rounded" style="background:#ffffff; border:1px solid #eef1f5;">
									<small class="text-muted">UNIDAD DE MEDIDA</small>
									<div class="fw-semibold">{{ $producto->unidad_medida ?? 'No Definida' }}</div>
								</div>
							</div>

							<div class="col-12">
								<div class="p-3 rounded" style="border-radius:10px; background:#ffffff; border:1px solid #eef1f5;">
									<small class="text-muted">ACCIÓN TERAPÉUTICA</small>
									<div class="fw-semibold">{{ $producto->accion_terapeutica ?? 'No Definida' }}</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<hr class="my-4">

				<!-- FILA INFERIOR: Categoria / Laboratorio / Forma / Presentacion -->
				<div class="row g-3">
					<div class="col-md-3">
						<div class="p-3 rounded" style="background:#ebebeb; border:1px solid #eef1f5;">
							<small class="text-muted">CATEGORÍA</small>
							<div class="fw-semibold">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="p-3 rounded" style="background:#ebebeb; border:1px solid #eef1f5;">
							<small class="text-muted">LABORATORIO</small>
							<div class="fw-semibold">{{ $producto->laboratorio->nombre ?? '-' }}</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="p-3 rounded" style="background:#ebebeb; border:1px solid #eef1f5;">
							<small class="text-muted">FORMA FARMACÉUTICA</small>
							<div class="fw-semibold">{{ optional($producto->forma_farmaceutica)->nombre ?? ($producto->forma_farmaceutica ?? '-') }}</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="p-3 rounded d-flex flex-column justify-content-between" style="background:#ebebeb; border:1px solid #eef1f5;">
							<div>
								<small class="text-muted">PRESENTACIÓN</small>
								<div class="fw-semibold">{{ $producto->presentacion->nombre ?? '-' }}</div>
							</div>
						</div>
					</div>

                    <div class="col-md-3">
						<div class="p-3 rounded" style="background:#ebebeb; border:1px solid #eef1f5;">
							<small class="text-muted">FECHA Y HORA DE CREACIÓN:</small>
							<div class="fw-semibold">{{ $producto->created_at ? $producto->created_at->format('d/m/Y H:i') : '-' }}</div>
						</div>
					</div>

                    <div class="col-md-3">
						<div class="p-3 rounded" style="background:#ebebeb; border:1px solid #eef1f5;">
							<small class="text-muted">FECHA Y HORA DE ACTUALIZACIÓN:</small>
							<div class="fw-semibold">{{ $producto->updated_at ? $producto->updated_at->format('d/m/Y H:i') : '-' }}</div>
						</div>
					</div>


                    <br>
                    <div class="text-end mt-2">
							<a href="{{ route('admin.productos.index') }}" class="btn btn-sm btn-outline-secondary">Cerrar</a>
						</div>
				</div>
			</div>
		</div>
	</section>

@endsection

