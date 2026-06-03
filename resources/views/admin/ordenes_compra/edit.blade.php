@extends('layouts.admin')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Editar Orden de Compra #{{ $compra->id }}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.ordenes_compra.index') }}">Orden de
                                    Compra</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Carrito de la Orden</h4>
                        <a href="{{ route('admin.ordenes_compra.show', $compra->id) }}"
                            class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver al Detalle
                        </a>
                    </div>
                    <div class="card-body">
                        <form id="formCarrito" method="POST" action="">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sucursal_id" class="form-label">Sucursal <span
                                            class="text-danger">*</span></label>
                                    <select id="sucursal_id" name="sucursal_id"
                                        class="form-select form-select-lg @error('sucursal_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Selecciona una Sucursal --</option>
                                        @foreach ($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}"
                                                {{ (string) old('sucursal_id', $compra->sucursal_id) === (string) $sucursal->id ? 'selected' : '' }}>
                                                {{ $sucursal->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sucursal_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor <span
                                            class="text-danger">*</span></label>
                                    <select id="proveedor_id" name="proveedor_id"
                                        class="form-select form-select-lg @error('proveedor_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Selecciona un Proveedor --</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}"
                                                {{ (string) old('proveedor_id', $compra->proveedor_id) === (string) $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->nombre }} - {{ $proveedor->empresa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Seleccionar Producto <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="buscarProducto" class="form-control form-control-lg"
                                            placeholder="Busca por codigo, nombre, laboratorio, categoria, forma, presentacion...">
                                        <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="table-responsive" style="min-height: auto;">
                                        <table id="tablaProductos" class="table table-hover table-sm mb-0">
                                            <thead class="table-light" style="position: sticky; top: 0;">
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 10%;">Codigo</th>
                                                    <th style="width: 10%;">Codigo Barra</th>
                                                    <th style="width: 12%;">Nombre Comercial</th>
                                                    <th style="width: 12%;">Nombre Generico</th>
                                                    <th style="width: 10%;">Concentracion</th>
                                                    <th style="width: 10%;">Laboratorio</th>
                                                    <th style="width: 10%;">Categoria</th>
                                                    <th style="width: 10%;">Forma Farmaceutica</th>
                                                    <th style="width: 8%;">Presentacion</th>
                                                    <th style="width: 7%;">Accion Terapeutica</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productosBody">
                                                @foreach ($productos as $producto)
                                                    <tr class="producto-row" data-id="{{ $producto->id }}"
                                                        data-codigo="{{ $producto->codigo_producto }}"
                                                        data-codigo-barra="{{ $producto->codigo_barra ?? '' }}"
                                                        data-nombre-comercial="{{ $producto->nombre_comercial }}"
                                                        data-nombre-generico="{{ $producto->nombre_generico }}"
                                                        data-laboratorio="{{ $producto->laboratorio ? $producto->laboratorio->nombre : 'N/A' }}"
                                                        data-categoria="{{ $producto->categoria ? $producto->categoria->nombre : 'N/A' }}"
                                                        data-forma-farmaceutica="{{ $producto->formaFarmaceutica ? $producto->formaFarmaceutica->nombre : 'N/A' }}"
                                                        data-presentacion="{{ $producto->presentacion ? $producto->presentacion->nombre : 'N/A' }}"
                                                        data-concentracion="{{ $producto->concentracion ?? '' }}"
                                                        data-unidad-medida="{{ $producto->unidad_medida ?? '' }}"
                                                        data-accion-terapeutica="{{ $producto->accion_terapeutica ?? '' }}">
                                                        <td><small class="text-muted">{{ $loop->iteration }}</small></td>
                                                        <td><small
                                                                class="badge bg-info">{{ $producto->codigo_producto }}</small>
                                                        </td>
                                                        <td><small>{{ $producto->codigo_barra ?? '-' }}</small></td>
                                                        <td><small><strong>{{ $producto->nombre_comercial }}</strong></small>
                                                        </td>
                                                        <td><small
                                                                class="text-muted">{{ $producto->nombre_generico }}</small>
                                                        </td>
                                                        <td><small>{{ $producto->concentracion ?? '-' }}{{ $producto->unidad_medida ? ' ' . $producto->unidad_medida : '' }}</small>
                                                        </td>
                                                        <td><small>{{ $producto->laboratorio ? $producto->laboratorio->nombre : '-' }}</small>
                                                        </td>
                                                        <td><small>{{ $producto->categoria ? $producto->categoria->nombre : '-' }}</small>
                                                        </td>
                                                        <td><small>{{ $producto->formaFarmaceutica ? $producto->formaFarmaceutica->nombre : '-' }}</small>
                                                        </td>
                                                        <td><small>{{ $producto->presentacion ? $producto->presentacion->nombre : '-' }}</small>
                                                        </td>
                                                        <td><small>{{ $producto->accion_terapeutica ?? '-' }}</small></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <small class="text-muted">Mostrando <span id="infoProductos">0</span>
                                            productos</small>
                                        <nav aria-label="Paginacion de productos">
                                            <ul class="pagination pagination-sm mb-0" id="paginacionProductos"></ul>
                                        </nav>
                                    </div>
                                </div>

                                <input type="hidden" id="producto_id" name="producto_id" value="">
                                <input type="hidden" id="cantidad" value="1">
                                <input type="hidden" id="precio_compra" value="0">
                                <input type="hidden" id="precio_venta" value="0">
                                <input type="hidden" id="porcentaje_ganancia" value="0">
                                <input type="hidden" id="observaciones" value="">
                            </div>

                            <input type="hidden" id="estado" name="estado" value="activo">

                            <hr>

                            <div class="table-responsive">
                                <table id="tablaCarrito" class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">P. Compra</th>
                                            <th class="text-center">P. Venta</th>
                                            <th class="text-center">% Ganancia</th>
                                            <th class="text-end">Subtotal</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="carritoBody">
                                        <tr id="filaVacia" class="text-center">
                                            <td colspan="8" class="py-4 text-muted">
                                                <i class="bi bi-inbox"></i> Carrito vacio. Agrega productos para comenzar.
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-light" id="tablaFooter" style="display: none;">
                                        <tr style="border-top: 2px solid #dee2e6;">
                                            <td colspan="1"></td>
                                            <td><strong>Totales:</strong></td>
                                            <td class="text-center"><strong><span id="totalCantidad">0</span></strong>
                                            </td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-end"><strong>{{ $ajuste->divisa ?? 'Bs.' }} <span
                                                        id="totalCompra">0.00</span></strong>
                                            </td>
                                            <td class="text-center"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-secondary btn-lg" onclick="limpiarCarrito()">
                                        <i class="bi bi-trash"></i> Limpiar
                                    </button>
                                    <button type="submit" id="btnConfirmar" class="btn btn-primary btn-lg" disabled>
                                        <i class="bi bi-check-circle"></i> Actualizar Orden
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .form-control-lg,
        .form-select-lg {
            font-size: 1rem;
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        #porcentaje_ganancia {
            font-weight: bold;
            color: #28a745;
        }

        .text-warning-custom {
            color: #ff9800;
        }

        .text-success-custom {
            color: #28a745;
        }

        #tablaProductos {
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        #tablaProductos thead {
            background-color: #e9ecef;
        }

        #tablaProductos thead th {
            padding: 0.35rem 0.5rem;
            font-weight: 600;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        #tablaProductos tbody td {
            padding: 0.25rem 0.5rem;
            vertical-align: middle;
        }

        #tablaProductos tbody tr {
            height: auto;
        }

        .producto-row {
            cursor: pointer;
            transition: background-color 0.2s ease;
            user-select: none;
        }

        #tablaProductos .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.4rem;
        }

        .producto-row:hover {
            background-color: #e8f4f8 !important;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.05);
        }

        .producto-row.table-active {
            background-color: #cfe2ff !important;
            font-weight: 500;
        }

        .producto-row td small {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.8rem;
            line-height: 1.2;
        }

        #buscarProducto {
            border-radius: 0.375rem 0 0 0.375rem;
        }

        #btnBuscar {
            border-radius: 0 0.375rem 0.375rem 0;
        }

        .cantidad-input {
            transition: background-color 0.2s ease, border-color 0.2s ease;
            text-align: right !important;
        }

        .cantidad-input:focus {
            background-color: #fff3cd !important;
            border-color: #ffc107 !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        const COMPRA_ID = {{ $compra->id }};
        const COMPRA_ADD_URL = @json(route('admin.compras.items.add', ['compra_id' => $compra->id]));
        const COMPRA_CLEAR_URL = @json(route('admin.compras.items.clear', ['compra_id' => $compra->id]));
        const COMPRA_ITEM_UPDATE_URL_TEMPLATE = @json(route('admin.compras.items.update', ['compra_id' => $compra->id, 'item_id' => '__ITEM_ID__']));
        const COMPRA_ITEM_REMOVE_URL_TEMPLATE = @json(route('admin.compras.items.remove', ['compra_id' => $compra->id, 'item_id' => '__ITEM_ID__']));
        const compraItemUpdateUrl = (itemId) => COMPRA_ITEM_UPDATE_URL_TEMPLATE.replace('__ITEM_ID__', itemId);
        const compraItemRemoveUrl = (itemId) => COMPRA_ITEM_REMOVE_URL_TEMPLATE.replace('__ITEM_ID__', itemId);
        let carrito = [];
        const MONEDA = "{{ $ajuste->divisa ?? 'Bs.' }}";
        let contadorLinea = 0;
        let paginaActual = 1;
        let itemsPorPagina = 10;
        let productosOriginales = [];
        let productosVisibles = [];
        let updateItemTimers = {};
        let addingProductIds = new Set();

        document.addEventListener('DOMContentLoaded', function() {
            const carritoInicial = @json($carritoInicial ?? []);

            carrito = (carritoInicial || []).map(item => ({
                ...item,
                sucursal_id: document.getElementById('sucursal_id').value,
                proveedor_id: document.getElementById('proveedor_id').value,
                cantidad: parseInt(item.cantidad) || 0,
                precio_compra: parseFloat(item.precio_compra) || 0,
                precio_venta: parseFloat(item.precio_venta) || 0,
                porcentaje_ganancia: parseFloat(item.porcentaje_ganancia) || 0,
                observaciones: item.observaciones || '',
                subtotal: (parseInt(item.cantidad) || 0) * (parseFloat(item.precio_compra) || 0)
            }));

            productosOriginales = Array.from(document.querySelectorAll('.producto-row'));
            productosVisibles = [...productosOriginales];

            inicializarPaginacion();
            mostrarPagina(1);

            document.getElementById('buscarProducto').addEventListener('input', filtrarProductos);
            document.getElementById('btnBuscar').addEventListener('click', function() {
                filtrarProductos();
            });

            document.getElementById('productosBody').addEventListener('click', function(e) {
                const fila = e.target.closest('.producto-row');
                if (!fila || fila.style.display === 'none') return;

                const productoId = fila.getAttribute('data-id');
                seleccionarProducto(productoId, fila);
            });

            renderizarCarrito();
        });

        function inicializarPaginacion() {
            const totalPaginas = Math.ceil(productosVisibles.length / itemsPorPagina);
            const paginacionContainer = document.getElementById('paginacionProductos');
            paginacionContainer.innerHTML = '';

            const btnAnterior = document.createElement('li');
            btnAnterior.className = 'page-item';
            btnAnterior.innerHTML = '<a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>';
            btnAnterior.addEventListener('click', function(e) {
                e.preventDefault();
                if (paginaActual > 1) {
                    mostrarPagina(paginaActual - 1);
                }
            });
            paginacionContainer.appendChild(btnAnterior);

            for (let i = 1; i <= totalPaginas; i++) {
                const btnPagina = document.createElement('li');
                btnPagina.className = 'page-item' + (i === paginaActual ? ' active' : '');
                btnPagina.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                btnPagina.addEventListener('click', function(e) {
                    e.preventDefault();
                    mostrarPagina(i);
                });
                paginacionContainer.appendChild(btnPagina);
            }

            const btnSiguiente = document.createElement('li');
            btnSiguiente.className = 'page-item';
            btnSiguiente.innerHTML = '<a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>';
            btnSiguiente.addEventListener('click', function(e) {
                e.preventDefault();
                if (paginaActual < totalPaginas) {
                    mostrarPagina(paginaActual + 1);
                }
            });
            paginacionContainer.appendChild(btnSiguiente);
        }

        function mostrarPagina(numeroPagina) {
            paginaActual = numeroPagina;
            const inicio = (numeroPagina - 1) * itemsPorPagina;
            const fin = inicio + itemsPorPagina;

            productosOriginales.forEach(fila => {
                fila.style.display = 'none';
            });

            productosVisibles.slice(inicio, fin).forEach(fila => {
                fila.style.display = '';
            });

            inicializarPaginacion();

            const totalVisibles = productosVisibles.length;
            const mostrandoDesde = totalVisibles > 0 ? inicio + 1 : 0;
            const mostrandoHasta = Math.min(fin, totalVisibles);
            document.getElementById('infoProductos').textContent =
                `${mostrandoDesde}-${mostrandoHasta} de ${totalVisibles}`;
        }

        function calcularGanancia() {
            const precioCompra = parseFloat(document.getElementById('precio_compra').value) || 0;
            const precioVenta = parseFloat(document.getElementById('precio_venta').value) || 0;

            let ganancia = 0;
            if (precioCompra > 0) {
                ganancia = ((precioVenta - precioCompra) / precioCompra) * 100;
            }

            document.getElementById('porcentaje_ganancia').value = ganancia.toFixed(2);
        }

        function filtrarProductos() {
            const busqueda = document.getElementById('buscarProducto').value.toLowerCase();

            if (busqueda === '') {
                productosVisibles = [...productosOriginales];
            } else {
                productosVisibles = productosOriginales.filter(fila => {
                    const codigo = fila.getAttribute('data-codigo').toLowerCase();
                    const codigoBarraText = fila.getAttribute('data-codigo-barra').toLowerCase();
                    const nombreComercial = fila.getAttribute('data-nombre-comercial').toLowerCase();
                    const nombreGenerico = fila.getAttribute('data-nombre-generico').toLowerCase();
                    const laboratorio = fila.getAttribute('data-laboratorio').toLowerCase();
                    const categoria = fila.getAttribute('data-categoria').toLowerCase();
                    const formaFarmaceutica = fila.getAttribute('data-forma-farmaceutica').toLowerCase();
                    const presentacion = fila.getAttribute('data-presentacion').toLowerCase();
                    const concentracion = fila.getAttribute('data-concentracion').toLowerCase();
                    const unidadMedida = fila.getAttribute('data-unidad-medida').toLowerCase();
                    const accionTerapeutica = fila.getAttribute('data-accion-terapeutica').toLowerCase();

                    return codigo.includes(busqueda) || codigoBarraText.includes(busqueda) ||
                        nombreComercial.includes(busqueda) || nombreGenerico.includes(busqueda) ||
                        laboratorio.includes(busqueda) || categoria.includes(busqueda) ||
                        formaFarmaceutica.includes(busqueda) || presentacion.includes(busqueda) ||
                        concentracion.includes(busqueda) || unidadMedida.includes(busqueda) ||
                        accionTerapeutica.includes(busqueda);
                });
            }

            paginaActual = 1;
            mostrarPagina(1);
        }

        function enfocarCantidadItem(itemId) {
            const filaItem = document.querySelector(`tr[data-item-id="${itemId}"]`);
            if (!filaItem) return;

            filaItem.classList.add('table-active');
            setTimeout(() => filaItem.classList.remove('table-active'), 800);

            const cantidadInput = filaItem.querySelector('.cantidad-input');
            if (cantidadInput) {
                cantidadInput.focus();
                cantidadInput.select();
            }
        }

        function seleccionarProducto(productoId, fila) {
            const nombreComercial = fila.getAttribute('data-nombre-comercial');
            const nombreGenerico = fila.getAttribute('data-nombre-generico');
            const codigo = fila.getAttribute('data-codigo');
            const sucursalId = document.getElementById('sucursal_id').value;

            if (addingProductIds.has(productoId)) {
                return;
            }

            if (!sucursalId) {
                Swal.fire('Atencion', 'Por favor selecciona una sucursal primero', 'warning');
                return;
            }

            const itemExistente = carrito.find(item =>
                String(item.producto_id) === String(productoId) &&
                String(item.sucursal_id) === String(sucursalId)
            );

            if (itemExistente) {
                enfocarCantidadItem(itemExistente.id);
                return;
            }

            addingProductIds.add(productoId);
            document.querySelectorAll('.producto-row').forEach(f => f.classList.remove('table-active'));
            fila.classList.add('table-active');

            const nombreProducto = `${nombreComercial} (${nombreGenerico}) - Codigo: ${codigo}`;

            // Agregar a la BD
            fetch(COMPRA_ADD_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        producto_id: productoId
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        Swal.fire('Error', data.message || 'No se pudo agregar el producto', 'error');
                        addingProductIds.delete(productoId);
                        fila.classList.remove('table-active');
                        return;
                    }

                    const item = {
                        id: data.item_id,
                        sucursal_id: sucursalId,
                        proveedor_id: document.getElementById('proveedor_id').value,
                        producto_id: productoId,
                        producto_nombre: nombreProducto,
                        cantidad: 0,
                        precio_compra: 0,
                        precio_venta: 0,
                        porcentaje_ganancia: 0,
                        observaciones: '',
                        subtotal: 0
                    };

                    carrito.push(item);
                    renderizarCarrito(true);
                    addingProductIds.delete(productoId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Error al agregar el producto: ' + error.message, 'error');
                    addingProductIds.delete(productoId);
                    fila.classList.remove('table-active');
                });
        }

        function calcularGananciaItem(precio_compra, precio_venta) {
            if (precio_compra <= 0) return 0;
            return ((precio_venta - precio_compra) / precio_compra) * 100;
        }

        function calcularPrecioVentaDesdeGanancia(precio_compra, porcentaje_ganancia) {
            if (precio_compra <= 0) return 0;
            return precio_compra * (1 + (porcentaje_ganancia / 100));
        }

        function agregarAlCarrito() {
            return false;
        }

        function enfocarBuscadorProducto() {
            const buscarProductoInput = document.getElementById('buscarProducto');
            if (buscarProductoInput) {
                setTimeout(() => {
                    buscarProductoInput.focus();
                    buscarProductoInput.select();
                }, 0);
            }
        }

        function renderizarCarrito(autoFocusCantidad = false) {
            const carritoBody = document.getElementById('carritoBody');
            const btnConfirmar = document.getElementById('btnConfirmar');
            const tablaFooter = document.getElementById('tablaFooter');

            if (carrito.length === 0) {
                carritoBody.innerHTML =
                    '<tr id="filaVacia" class="text-center"><td colspan="8" class="py-4 text-muted"><i class="bi bi-inbox"></i> Carrito vacio. Agrega productos para comenzar.</td></tr>';
                btnConfirmar.disabled = true;
                tablaFooter.style.display = 'none';
                return;
            }

            carritoBody.innerHTML = '';
            tablaFooter.style.display = '';
            carrito.forEach((item, index) => {
                const ganancia = calcularGananciaItem(item.precio_compra, item.precio_venta);
                const subtotal = item.cantidad * item.precio_compra;
                const clasGanancia = ganancia >= 30 ? 'text-success-custom' : 'text-warning-custom';

                const fila = document.createElement('tr');
                fila.setAttribute('data-item-id', item.id);
                fila.innerHTML = `
                <td><small class="text-muted">${index + 1}</small></td>
                <td><small>${item.producto_nombre}</small></td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm cantidad-input" value="${item.cantidad === 0 ? '' : item.cantidad}" min="0" style="width: 100px; margin: 0 auto; display: block;">
                </td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm precio-compra-input" value="${item.precio_compra > 0 ? item.precio_compra.toFixed(2) : ''}" placeholder="0.00" min="0" step="0.01" style="width: 90px; margin: 0 auto; display: block; text-align: center;">
                </td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm precio-venta-input" value="${item.precio_venta > 0 ? item.precio_venta.toFixed(2) : ''}" placeholder="0.00" min="0" step="0.01" style="width: 90px; margin: 0 auto; display: block; text-align: center;">
                </td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm ganancia-input ${clasGanancia}" value="${ganancia > 0 ? ganancia.toFixed(2) : ''}" placeholder="0.00" min="0" step="0.01" style="width: 100px; margin: 0 auto; display: block; text-align: center;">
                </td>
                <td class="text-end"><strong class="subtotal-valor">${MONEDA} ${subtotal.toFixed(2)}</strong></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDelCarrito('${item.id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

                const cantidadInput = fila.querySelector('.cantidad-input');
                const precioCompraInput = fila.querySelector('.precio-compra-input');
                const precioVentaInput = fila.querySelector('.precio-venta-input');
                const gananciaInput = fila.querySelector('.ganancia-input');

                cantidadInput.addEventListener('input', function() {
                    actualizarItemCarrito(item.id, {
                        cantidad: parseInt(this.value) || 0
                    });
                });

                cantidadInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || (e.key === 'Tab' && !e.shiftKey)) {
                        e.preventDefault();
                        precioCompraInput.focus();
                    }
                });

                precioCompraInput.addEventListener('input', function() {
                    actualizarItemCarrito(item.id, {
                        precio_compra: parseFloat(this.value) || 0
                    });
                });

                precioCompraInput.addEventListener('focus', function() {
                    this.select();
                });

                precioCompraInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || (e.key === 'Tab' && !e.shiftKey)) {
                        e.preventDefault();
                        precioVentaInput.focus();
                    } else if (e.key === 'Tab' && e.shiftKey) {
                        e.preventDefault();
                        cantidadInput.focus();
                    }
                });

                precioVentaInput.addEventListener('input', function() {
                    actualizarItemCarrito(item.id, {
                        precio_venta: parseFloat(this.value) || 0
                    });
                });

                precioVentaInput.addEventListener('focus', function() {
                    this.select();
                });

                precioVentaInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || (e.key === 'Tab' && !e.shiftKey)) {
                        e.preventDefault();
                        gananciaInput.focus();
                    } else if (e.key === 'Tab' && e.shiftKey) {
                        e.preventDefault();
                        precioCompraInput.focus();
                    }
                });

                gananciaInput.addEventListener('input', function() {
                    actualizarItemCarrito(item.id, {
                        porcentaje_ganancia: parseFloat(this.value) || 0
                    });
                });

                gananciaInput.addEventListener('focus', function() {
                    this.select();
                });

                gananciaInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || (e.key === 'Tab' && !e.shiftKey)) {
                        e.preventDefault();
                        e.stopPropagation();
                        enfocarBuscadorProducto();
                    } else if (e.key === 'Tab' && e.shiftKey) {
                        e.preventDefault();
                        precioVentaInput.focus();
                    }
                });

                carritoBody.appendChild(fila);
            });

            if (autoFocusCantidad) {
                const ultimoInput = carritoBody.querySelector('tr:last-child .cantidad-input');
                if (ultimoInput) {
                    setTimeout(() => ultimoInput.focus(), 100);
                }
            }

            btnConfirmar.disabled = false;
            actualizarResumen();
        }

        function actualizarVistaFila(itemId) {
            const item = carrito.find(i => String(i.id) === String(itemId));
            if (!item) return;

            const fila = document.querySelector(`tr[data-item-id="${itemId}"]`);
            if (!fila) return;

            const ganancia = calcularGananciaItem(item.precio_compra, item.precio_venta);
            const subtotal = item.cantidad * item.precio_compra;
            const clasGanancia = ganancia >= 30 ? 'text-success-custom' : 'text-warning-custom';

            item.porcentaje_ganancia = ganancia;
            item.subtotal = subtotal;

            const precioVentaInput = fila.querySelector('.precio-venta-input');
            const gananciaInput = fila.querySelector('.ganancia-input');
            const subtotalEl = fila.querySelector('.subtotal-valor');

            if (precioVentaInput && document.activeElement !== precioVentaInput) {
                precioVentaInput.value = item.precio_venta > 0 ? item.precio_venta.toFixed(2) : '';
            }

            if (gananciaInput) {
                if (document.activeElement !== gananciaInput) {
                    gananciaInput.value = ganancia > 0 ? ganancia.toFixed(2) : '';
                }
                gananciaInput.classList.remove('text-success-custom', 'text-warning-custom');
                gananciaInput.classList.add(clasGanancia);
            }

            if (subtotalEl) {
                subtotalEl.textContent = `${MONEDA} ${subtotal.toFixed(2)}`;
            }
        }

        function actualizarItemCarrito(itemId, cambios) {
            const item = carrito.find(i => String(i.id) === String(itemId));
            if (!item) return;

            Object.assign(item, cambios);

            const cambioGanancia = Object.prototype.hasOwnProperty.call(cambios, 'porcentaje_ganancia');
            const cambioPrecioVenta = Object.prototype.hasOwnProperty.call(cambios, 'precio_venta');
            const cambioPrecioCompra = Object.prototype.hasOwnProperty.call(cambios, 'precio_compra');

            if (cambioGanancia) {
                item.precio_venta = calcularPrecioVentaDesdeGanancia(item.precio_compra, item.porcentaje_ganancia);
            }

            if (cambioPrecioCompra && !cambioPrecioVenta && !cambioGanancia) {
                item.precio_venta = calcularPrecioVentaDesdeGanancia(item.precio_compra, item.porcentaje_ganancia);
            }

            item.porcentaje_ganancia = calcularGananciaItem(item.precio_compra, item.precio_venta);
            item.subtotal = item.cantidad * item.precio_compra;

            actualizarVistaFila(itemId);
            actualizarResumen();

            if (updateItemTimers[itemId]) {
                clearTimeout(updateItemTimers[itemId]);
            }

            updateItemTimers[itemId] = setTimeout(() => {
                fetch(compraItemUpdateUrl(itemId), {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            cantidad: item.cantidad,
                            precio_compra_unidad: item.precio_compra,
                            precio_venta_unidad: item.precio_venta,
                            porcentaje_ganancia_unidad: item.porcentaje_ganancia
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => console.error('Error al actualizar:', error));

                delete updateItemTimers[itemId];
            }, 350);
        }

        function actualizarResumen() {
            let totalCantidad = 0;
            let totalCompra = 0;

            carrito.forEach(item => {
                totalCantidad += (item.cantidad || 0);
                totalCompra += (item.subtotal || 0);
            });

            document.getElementById('totalCantidad').textContent = totalCantidad;
            document.getElementById('totalCompra').textContent = totalCompra.toFixed(2);
        }

        function eliminarDelCarrito(id) {
            Swal.fire({
                title: 'Eliminar?',
                text: 'Estas seguro de que deseas eliminar este producto del carrito?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;

                if (updateItemTimers[id]) {
                    clearTimeout(updateItemTimers[id]);
                    delete updateItemTimers[id];
                }

                // Eliminar de la BD
                fetch(compraItemRemoveUrl(id), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            carrito = carrito.filter(item => String(item.id) !== String(id));
                            renderizarCarrito();
                            Swal.fire('Eliminado', 'Producto eliminado del carrito', 'success');
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'No se pudo eliminar el producto: ' + error.message, 'error');
                    });
            });
        }

        function limpiarFormulario() {
            document.getElementById('producto_id').value = '';
            document.getElementById('cantidad').value = '1';
            document.getElementById('precio_compra').value = '0.00';
            document.getElementById('precio_venta').value = '0.00';
            document.getElementById('porcentaje_ganancia').value = '0.00';
            document.getElementById('observaciones').value = '';
            document.getElementById('producto_id').focus();
        }

        function limpiarCarrito() {
            Swal.fire({
                title: 'Limpiar carrito?',
                text: 'Esto eliminara todos los productos del carrito',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) return;

                if (carrito.length === 0) {
                    Swal.fire('Info', 'El carrito ya esta vacio', 'info');
                    return;
                }

                Object.keys(updateItemTimers).forEach(key => clearTimeout(updateItemTimers[key]));
                updateItemTimers = {};

                const itemIds = carrito.map(item => item.id);

                // Limpiar en la BD
                fetch(COMPRA_CLEAR_URL, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            item_ids: itemIds
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            carrito = [];
                            contadorLinea = 0;
                            renderizarCarrito();
                            limpiarFormulario();
                            Swal.fire('Limpiado', 'Carrito vacio', 'success');
                        } else {
                            Swal.fire('Error', data.message || 'Error al limpiar el carrito', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'No se pudo limpiar el carrito: ' + error.message, 'error');
                    });
            });
        }

        document.getElementById('formCarrito').addEventListener('submit', function(e) {
            e.preventDefault();

            if (carrito.length === 0) {
                Swal.fire('Error', 'El carrito esta vacio. Agrega productos.', 'error');
                return;
            }

            const sucursal_id = document.getElementById('sucursal_id').value;
            const proveedor_id = document.getElementById('proveedor_id').value;

            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('_method', 'PUT');
            formData.append('sucursal_id', sucursal_id);
            formData.append('proveedor_id', proveedor_id);
            formData.append('carrito', JSON.stringify(carrito));

            fetch('{{ route('admin.ordenes_compra.update', $compra->id) }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Exito', 'Orden de compra actualizada', 'success').then(() => {
                            carrito = [];
                            contadorLinea = 0;
                            window.location.href = data.redirect ||
                                '{{ route('admin.ordenes_compra.show', $compra->id) }}';
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Error al actualizar la orden', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Error en la peticion', 'error');
                });
        });
    </script>
@endpush
