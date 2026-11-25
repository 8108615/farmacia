@extends('layouts.admin')

@section('content')
<div class="page-heading"><h3>Nueva venta</h3></div>

@if ($errors->any())
    <ul class="alert alert-danger" role="alert">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<noscript><p class="alert alert-warning">Activa JavaScript para usar el carrito.</p></noscript>

<form id="form-venta" method="POST" action="{{ route('admin.ventas.store') }}" data-divisa="{{ $divisa }}">
    @csrf
    <input type="hidden" name="operacion" value="{{ $operacion }}">

    <section id="catalogo-venta" class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="sucursal_id" class="form-label">Sucursal</label>
                    <select id="sucursal_id" name="sucursal_id" class="form-select" required>
                        <option value="">Elige una sucursal</option>
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" @selected(old('sucursal_id') == $sucursal->id)>{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label for="buscar-producto" class="form-label">Buscar producto</label>
                    <input id="buscar-producto" type="search" class="form-control" placeholder="Nombre, código, laboratorio, categoría, forma, presentación o lote">
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table id="tabla-catalogo-venta" class="table table-hover table-sm align-middle small">
                    <thead>
                        <tr>
                            <th>#</th><th>Código / Barra</th><th>Nombre comercial</th><th>Nombre genérico</th>
                            <th>Concentración</th><th>Laboratorio</th><th>Categoría</th><th>Acción terapéutica</th>
                            <th>Receta</th><th>Forma / Presentación</th><th class="text-end">Precio de venta</th>
                            <th class="text-end">Stock</th><th>Sucursal / Ubicación</th>
                            <th>Lote / Partida</th><th>Vencimiento</th><th>Días</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $hoy = today(); ?>
                    @foreach ($partidas as $partida)
                        @php
                            $producto = $partida->producto;
                            $vencimiento = $partida->lote?->fecha_vencimiento;
                            $diasVencimiento = $vencimiento ? (int) $hoy->diffInDays($vencimiento, false) : null;
                            $estado = !$vencimiento ? 'sin-fecha' : ($vencimiento->lt($hoy) ? 'vencido' : 'vigente');
                        @endphp
                        <tr @class(['table-danger' => $estado === 'vencido']) style="cursor: pointer"
                            data-estado="{{ $estado }}" data-vencimiento="{{ $vencimiento?->format('d/m/Y') }}"
                            data-partida="{{ $partida->id }}" data-sucursal="{{ $partida->sucursal_id }}"
                            data-stock="{{ $partida->stock_actual }}" data-precio="{{ $partida->precio_venta_unidad }}"
                            data-producto="{{ $partida->producto?->nombre_comercial }}"
                            data-codigo="{{ $partida->producto?->codigo_producto }}"
                            data-barra="{{ $partida->producto?->codigo_barra }}"
                            data-generico="{{ $partida->producto?->nombre_generico }}"
                            data-concentracion="{{ trim(($partida->producto?->concentracion ?? '') . ' ' . ($partida->producto?->unidad_medida ?? '')) }}"
                            data-laboratorio="{{ $partida->producto?->laboratorio?->nombre }}"
                            data-forma="{{ $partida->producto?->formaFarmaceutica?->nombre }}"
                            data-presentacion="{{ $partida->producto?->presentacion?->nombre }}"
                            data-lote="{{ $partida->lote?->numero_lote }}"
                            data-busqueda="{{ $partida->producto?->nombre_comercial }} {{ $partida->producto?->nombre_generico }} {{ $partida->producto?->codigo_producto }} {{ $partida->producto?->codigo_barra }} {{ $partida->lote?->numero_lote }} {{ $producto?->laboratorio?->nombre }} {{ $producto?->categoria?->nombre }} {{ $producto?->formaFarmaceutica?->nombre }} {{ $producto?->presentacion?->nombre }}" hidden>
                            <td class="numero-catalogo"></td>
                            <td class="text-nowrap">
                                <span class="badge bg-info text-dark">{{ $producto?->codigo_producto ?? '—' }}</span>
                                <small class="d-block text-muted">{{ $producto?->codigo_barra ?: '—' }}</small>
                            </td>
                            <td>
                                <button type="button" class="btn btn-link p-0 text-start text-reset fw-semibold"
                                    aria-label="Agregar {{ $producto?->nombre_comercial }}, partida {{ $partida->id }}">
                                    {{ $producto?->nombre_comercial ?? '—' }}
                                </button>
                            </td>
                            <td>{{ $producto?->nombre_generico ?: '—' }}</td>
                            <td>{{ trim(($producto?->concentracion ?? '') . ' ' . ($producto?->unidad_medida ?? '')) ?: '—' }}</td>
                            <td>{{ $producto?->laboratorio?->nombre ?? '—' }}</td>
                            <td>{{ $producto?->categoria?->nombre ?? '—' }}</td>
                            <td>{{ $producto?->accion_terapeutica ?: '—' }}</td>
                            <td class="text-nowrap">
                                @if (!$producto)
                                    —
                                @elseif ($producto->usa_receta)
                                    <span class="badge bg-warning text-dark">Con receta</span>
                                @else
                                    <span class="badge bg-success">Sin receta</span>
                                @endif
                            </td>
                            <td>{{ collect([$producto?->formaFarmaceutica?->nombre, $producto?->presentacion?->nombre])->filter()->implode(' / ') ?: '—' }}</td>
                            <td class="text-end text-nowrap">{{ $divisa }} {{ $partida->precio_venta_unidad }}</td>
                            <td class="text-end">{{ $partida->stock_actual }}</td>
                            <td>{{ $partida->sucursal?->nombre ?? '—' }}<small class="d-block">{{ $partida->ubicacionFisica?->nombre ?? 'Sin ubicación' }}</small></td>
                            <td class="text-nowrap">{{ $partida->lote?->numero_lote ?? '—' }} <small>(#{{ $partida->id }})</small></td>
                            <td class="text-nowrap">{{ $vencimiento?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                            <td class="text-nowrap">
                                @if ($estado === 'vencido')
                                    <span class="badge bg-danger">Vencido hace {{ abs($diasVencimiento) }} d</span>
                                @elseif ($diasVencimiento !== null)
                                    {{ $diasVencimiento === 0 ? 'Vence hoy' : $diasVencimiento . ' d' }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr id="sin-resultados"><td colspan="16" class="text-muted">Elige una sucursal para consultar sus existencias.</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="paginacion-catalogo" class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                <span id="resumen-catalogo" class="small text-muted" role="status"></span>
                <nav id="controles-catalogo" aria-label="Páginas de productos" hidden>
                    <button id="catalogo-anterior" type="button" class="btn btn-sm btn-outline-primary">Anterior</button>
                    <span id="pagina-catalogo" class="small mx-2"></span>
                    <button id="catalogo-siguiente" type="button" class="btn btn-sm btn-outline-primary">Siguiente</button>
                </nav>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <h4 class="card-title mb-0">Carrito</h4>
                <button id="limpiar-carrito" type="button" class="btn btn-outline-danger" disabled>
                    <i class="bi bi-trash me-1" aria-hidden="true"></i> Limpiar carrito
                </button>
            </div>
            <p id="carrito-vacio" class="text-muted">Agrega una partida para comenzar.</p>
            <div class="table-responsive">
                <table id="tabla-carrito" class="table table-sm align-middle" hidden>
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Código / Barra</th>
                            <th scope="col">Nombre comercial</th>
                            <th scope="col">Nombre genérico</th>
                            <th scope="col">Concentración</th>
                            <th scope="col">Laboratorio</th>
                            <th scope="col">Forma / Presentación</th>
                            <th scope="col">Lote / Partida</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col" class="text-end">Precio unitario</th>
                            <th scope="col" class="text-end">Subtotal</th>
                            <th scope="col">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="lineas-carrito"></tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <strong>Total <output id="total-venta">{{ $divisa }} 0.00</output></strong>
                <div class="d-flex flex-wrap gap-2">
                    <a id="cancelar-venta" href="{{ route('admin.ventas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button id="revisar-venta" type="button" class="btn btn-primary" disabled>Siguiente</button>
                </div>
            </div>
        </div>
    </section>

    <section id="confirmacion-venta" class="card" hidden>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center gap-2 mb-3"><h4 class="card-title mb-0">Confirmar venta</h4><button type="button" id="volver-productos" class="btn btn-outline-secondary">Volver a productos</button></div>
            <div class="row g-3">
                <div class="col-md-4 cliente-venta">
                    <label id="etiqueta-cliente-venta" for="cliente_id" class="form-label">Cliente</label>
                    <select id="cliente_id" name="cliente_id" class="form-select" required>
                        <option value="">Selecciona por nombre o CI/NIT</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>{{ $cliente->nombres_apellidos }}{{ filled($cliente->ci_nit) ? ' · ' . $cliente->ci_nit : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tipo_pago" class="form-label">Forma de pago</label>
                    <select id="tipo_pago" name="tipo_pago" class="form-select">
                        @foreach (['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia'] as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected(old('tipo_pago') === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="pago-efectivo" class="col-md-4" hidden>
                    <label for="monto_recibido" class="form-label">Monto recibido ({{ $divisa }})</label>
                    <input id="monto_recibido" name="monto_recibido" type="number" min="0" max="9999999999.99"
                        step="0.01" inputmode="decimal" class="form-control"
                        value="{{ old('monto_recibido') }}" aria-describedby="texto-cambio" disabled>
                    <div id="texto-cambio" class="form-text">
                        Cambio <output id="cambio-venta" aria-live="polite">—</output>
                    </div>
                </div>
                <div class="col-12">
                    <button id="guardar-venta" class="btn btn-success" disabled>Confirmar venta</button>
                </div>
            </div>
        </div>
    </section>
</form>

<template id="plantilla-linea">
    <tr>
        <td class="numero-linea"></td>
        <td class="text-nowrap">
            <span class="badge bg-info text-dark" data-campo="codigo"></span>
            <small class="d-block text-muted" data-campo="barra"></small>
        </td>
        <td class="fw-semibold" data-campo="producto"></td>
        <td data-campo="generico"></td>
        <td data-campo="concentracion"></td>
        <td data-campo="laboratorio"></td>
        <td data-campo="formaPresentacion"></td>
        <td class="text-nowrap">
            <input type="hidden" class="id-partida">
            <span data-campo="lotePartida"></span>
        </td>
        <td><input type="number" min="1" class="form-control cantidad-partida" required></td>
        <td class="precio-unitario text-end text-nowrap"></td>
        <td class="subtotal text-end text-nowrap"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm quitar-partida">Quitar</button></td>
    </tr>
</template>

<script>
(() => {
    const form = document.querySelector('#form-venta');
    const sucursal = document.querySelector('#sucursal_id');
    const buscar = document.querySelector('#buscar-producto');
    const filas = [...form.querySelectorAll('[data-partida]')];
    const lineas = document.querySelector('#lineas-carrito');
    const limpiarCarrito = document.querySelector('#limpiar-carrito');
    const cancelarVenta = document.querySelector('#cancelar-venta');
    const siguiente = document.querySelector('#revisar-venta');
    const confirmacion = document.querySelector('#confirmacion-venta');
    const guardar = document.querySelector('#guardar-venta');
    const operacionAnterior = {{ Illuminate\Support\Js::from(old('items', [])) }};
    let sucursalAnterior = sucursal.value;

    const catalogo = document.querySelector('#catalogo-venta');
    const volver = document.querySelector('#volver-productos');
    const cliente = document.querySelector('#cliente_id');
    const tipoPago = document.querySelector('#tipo_pago');
    const efectivo = document.querySelector('#pago-efectivo');
    const recibido = document.querySelector('#monto_recibido');
    const cambio = document.querySelector('#cambio-venta');

    function actualizarPago(total) {
        const pideEfectivo = !confirmacion.hidden && tipoPago.value === 'efectivo';
        efectivo.hidden = !pideEfectivo;
        recibido.disabled = !pideEfectivo;
        recibido.required = pideEfectivo;
        recibido.setCustomValidity('');
        if (!pideEfectivo || !recibido.value || !recibido.validity.valid) {
            cambio.textContent = '—';
            return !pideEfectivo;
        }

        const diferencia = Math.round(Number(recibido.value) * 100) - total;
        if (diferencia < 0) {
            cambio.textContent = 'Faltan ' + moneda(-diferencia);
            recibido.setCustomValidity('El monto recibido debe cubrir el total de la venta.');
            return false;
        }

        cambio.textContent = moneda(diferencia);
        return true;
    }

    cliente.addEventListener('change', actualizar);
    tipoPago.addEventListener('change', actualizar);
    recibido.addEventListener('input', actualizar);

    function mostrarConfirmacion(mostrar) {
        confirmacion.hidden = !mostrar;
        catalogo.hidden = mostrar;
        siguiente.hidden = mostrar;
        actualizar();
    }


    const porPagina = 10;
    const anteriorCatalogo = document.querySelector('#catalogo-anterior');
    const siguienteCatalogo = document.querySelector('#catalogo-siguiente');
    let paginaCatalogo = 1;
    const normalizarBusqueda = texto => texto.trim().toLocaleLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    function filtrar() {
        const texto = normalizarBusqueda(buscar.value);
        const coincidencias = filas.filter(fila => fila.dataset.sucursal === sucursal.value &&
            normalizarBusqueda(fila.dataset.busqueda).includes(texto));
        const paginas = Math.max(1, Math.ceil(coincidencias.length / porPagina));
        paginaCatalogo = Math.min(Math.max(1, paginaCatalogo), paginas);
        const inicio = (paginaCatalogo - 1) * porPagina;
        const visibles = coincidencias.slice(inicio, inicio + porPagina);

        filas.forEach(fila => { fila.hidden = true; });
        visibles.forEach((fila, indice) => {
            fila.hidden = false;
            fila.querySelector('.numero-catalogo').textContent = inicio + indice + 1;
        });

        const aviso = document.querySelector('#sin-resultados');
        aviso.hidden = coincidencias.length > 0;
        aviso.firstElementChild.textContent = sucursal.value
            ? 'No hay partidas disponibles para esta búsqueda.'
            : 'Elige una sucursal para consultar sus existencias.';
        document.querySelector('#resumen-catalogo').textContent = coincidencias.length
            ? 'Mostrando ' + (inicio + 1) + '–' + (inicio + visibles.length) + ' de ' + coincidencias.length + ' partidas'
            : '';
        document.querySelector('#controles-catalogo').hidden = paginas <= 1;
        document.querySelector('#pagina-catalogo').textContent = paginaCatalogo + ' de ' + paginas;
        anteriorCatalogo.disabled = paginaCatalogo === 1;
        siguienteCatalogo.disabled = paginaCatalogo === paginas;
    }

    anteriorCatalogo.addEventListener('click', () => {
        paginaCatalogo--;
        filtrar();
    });
    siguienteCatalogo.addEventListener('click', () => {
        paginaCatalogo++;
        filtrar();
    });


    function moneda(centavos) {
        return form.dataset.divisa + ' ' + (centavos / 100).toFixed(2);
    }

    function actualizar() {
        let total = 0;
        let valido = lineas.rows.length > 0;
        for (const linea of lineas.rows) {
            linea.querySelector('.numero-linea').textContent = linea.sectionRowIndex + 1;
            const cantidad = linea.querySelector('.cantidad-partida');
            const subtotal = cantidad.validity.valid ? Number(cantidad.value) * Number(linea.dataset.centavos) : 0;
            valido = valido && cantidad.validity.valid;
            linea.querySelector('.subtotal').textContent = moneda(subtotal);
            total += subtotal;
        }
        document.querySelector('#total-venta').textContent = moneda(total);
        document.querySelector('#carrito-vacio').hidden = lineas.rows.length > 0;
        document.querySelector('#tabla-carrito').hidden = lineas.rows.length === 0;
        limpiarCarrito.disabled = lineas.rows.length === 0;
        siguiente.disabled = !valido;
        const pagoValido = actualizarPago(total);
        guardar.disabled = !valido || confirmacion.hidden || !pagoValido || !cliente.value;
    }

    function agregar(fila, unidades = 1) {
        const datos = fila.dataset;
        const linea = document.querySelector('#plantilla-linea').content.firstElementChild.cloneNode(true);
        linea.dataset.id = datos.partida;
        linea.dataset.centavos = Math.round(Number(datos.precio) * 100);
        const campos = {
            ...datos,
            formaPresentacion: [datos.forma, datos.presentacion].filter(Boolean).join(' / '),
            lotePartida: (datos.lote || '—') + ' (#' + datos.partida + ')',
        };
        for (const celda of linea.querySelectorAll('[data-campo]')) {
            celda.textContent = campos[celda.dataset.campo] || '—';
        }
        linea.querySelector('.precio-unitario').textContent = moneda(Number(linea.dataset.centavos));
        const prefijo = 'items[' + datos.partida + ']';
        const id = linea.querySelector('.id-partida');
        id.name = prefijo + '[inventario_id]';
        id.value = datos.partida;
        const cantidad = linea.querySelector('.cantidad-partida');
        cantidad.name = prefijo + '[cantidad]';
        cantidad.setAttribute('aria-label', 'Cantidad de la partida ' + datos.partida);
        cantidad.max = datos.stock;
        cantidad.value = unidades;
        cantidad.addEventListener('input', actualizar);
        linea.querySelector('.quitar-partida').addEventListener('click', () => {
            linea.remove();
            actualizar();
        });
        lineas.append(linea);
        actualizar();
    }

    function avisar(titulo, mensaje) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ titleText: titulo, text: mensaje, icon: 'error', confirmButtonText: 'Entendido' });
        } else {
            window.alert(titulo + '\n' + mensaje);
        }
    }

    function seleccionar(fila) {
        const datos = fila.dataset;
        if (datos.estado === 'vencido') {
            avisar('Producto vencido', datos.producto + ' · ' + datos.lote +
                ' · Venció el ' + datos.vencimiento + '. No se puede vender.');
            return;
        }
        if (datos.estado !== 'vigente') {
            avisar('Vencimiento no registrado', 'Esta partida necesita una fecha de vencimiento para poder venderse.');
            return;
        }

        const existente = lineas.querySelector('[data-id="' + datos.partida + '"]');
        if (existente) {
            const cantidad = existente.querySelector('.cantidad-partida');
            if (!cantidad.validity.valid) {
                cantidad.reportValidity();
                return;
            }
            if (Number(cantidad.value) >= Number(datos.stock)) {
                avisar('Stock máximo alcanzado', 'Ya agregaste todas las unidades disponibles de esta partida.');
                return;
            }
            cantidad.stepUp();
            actualizar();
            return;
        }

        if (lineas.rows.length >= 100) {
            avisar('Carrito lleno', 'Puedes agregar hasta 100 partidas por venta.');
            return;
        }
        agregar(fila);
    }

    for (const fila of filas) {
        fila.addEventListener('click', () => seleccionar(fila));
    }

    sucursal.addEventListener('change', () => {
        if (lineas.rows.length && !window.confirm('Cambiar de sucursal vaciará el carrito. ¿Continuar?')) {
            sucursal.value = sucursalAnterior;
            return;
        }
        sucursalAnterior = sucursal.value;
        paginaCatalogo = 1;
        lineas.replaceChildren();
        confirmacion.hidden = true;
        filtrar();
        actualizar();
    });
    buscar.addEventListener('input', () => {
        paginaCatalogo = 1;
        filtrar();
    });

    async function confirmarDescarte(titulo, mensaje, boton) {
        if (typeof Swal === 'undefined') {
            return window.confirm(titulo + '\n' + mensaje);
        }

        const resultado = await Swal.fire({
            titleText: titulo,
            text: mensaje,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: boton,
            cancelButtonText: 'Volver',
            focusCancel: true,
            reverseButtons: true,
            confirmButtonColor: '#dc3545'
        });
        return resultado.isConfirmed;
    }

    limpiarCarrito.addEventListener('click', async () => {
        const confirmado = await confirmarDescarte(
            '¿Limpiar el carrito?',
            'Se quitarán todas las partidas seleccionadas.',
            'Sí, limpiar'
        );
        if (!confirmado) return;

        lineas.replaceChildren();
        recibido.value = '';
        mostrarConfirmacion(false);
        buscar.focus();
    });

    cancelarVenta.addEventListener('click', async event => {
        if (!lineas.rows.length) return;

        event.preventDefault();
        const confirmado = await confirmarDescarte(
            '¿Cancelar esta venta?',
            'Se descartará el carrito y volverás al listado de ventas.',
            'Sí, cancelar venta'
        );
        if (confirmado) window.location.assign(cancelarVenta.href);
    });


    siguiente.addEventListener('click', () => {
        mostrarConfirmacion(true);
        (confirmacion.querySelector('.choices') ?? cliente).focus();
    });

    volver.addEventListener('click', () => {
        mostrarConfirmacion(false);
        buscar.focus();
    });

    form.addEventListener('submit', event => {
        if (guardar.disabled) {
            event.preventDefault();
            return;
        }
        guardar.disabled = true;
    });

    for (const item of Object.values(operacionAnterior || {})) {
        const fila = filas.find(fila => fila.dataset.partida === String(item?.inventario_id) &&
            fila.dataset.sucursal === sucursal.value);
        if (fila?.dataset.estado === 'vigente' && lineas.rows.length < 100) agregar(fila, item.cantidad);
    }
    mostrarConfirmacion(lineas.rows.length > 0);
    filtrar();
    actualizar();
})();
</script>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/extensions/choices.js/public/assets/styles/choices.min.css') }}">
<style>
@media (min-width: 1200px) {
    #tabla-catalogo-venta {
        width: 100%;
        table-layout: fixed;
        font-size: .76rem;
        line-height: 1.3;
    }

    #tabla-catalogo-venta > :not(caption) > * > * {
        padding: .4rem .32rem;
        overflow-wrap: anywhere;
        white-space: normal !important;
    }

    #tabla-catalogo-venta .badge,
    #tabla-catalogo-venta .btn-link {
        max-width: 100%;
        white-space: normal;
        overflow-wrap: anywhere;
        font-size: inherit;
    }

    #tabla-catalogo-venta th:nth-child(1) { width: 2%; }
    #tabla-catalogo-venta th:nth-child(2) { width: 8%; }
    #tabla-catalogo-venta th:nth-child(3) { width: 8%; }
    #tabla-catalogo-venta th:nth-child(4) { width: 8%; }
    #tabla-catalogo-venta th:nth-child(5) { width: 6%; }
    #tabla-catalogo-venta th:nth-child(6) { width: 7%; }
    #tabla-catalogo-venta th:nth-child(7) { width: 6%; }
    #tabla-catalogo-venta th:nth-child(8) { width: 8%; }
    #tabla-catalogo-venta th:nth-child(9) { width: 6%; }
    #tabla-catalogo-venta th:nth-child(10) { width: 8%; }
    #tabla-catalogo-venta th:nth-child(11) { width: 6%; }
    #tabla-catalogo-venta th:nth-child(12) { width: 4%; }
    #tabla-catalogo-venta th:nth-child(13) { width: 7%; }
    #tabla-catalogo-venta th:nth-child(14) { width: 6%; }
    #tabla-catalogo-venta th:nth-child(15) { width: 6%; }
    #tabla-catalogo-venta th:nth-child(16) { width: 4%; }
}

    .cliente-venta {
        --cliente-fondo: #fff;
        --cliente-texto: #25334d;
        --cliente-borde: #d8ddea;
        --cliente-resaltado: #edf1fb;
    }

    [data-bs-theme="dark"] .cliente-venta {
        --cliente-fondo: #1b1b29;
        --cliente-texto: #e0e3ef;
        --cliente-borde: #414158;
        --cliente-resaltado: #303957;
    }

    .cliente-venta .choices { margin-bottom: 0; }
    .cliente-venta .choices__inner {
        display: flex;
        align-items: center;
        min-height: 38px;
        padding: .375rem 2rem .375rem .75rem;
        border-radius: .375rem;
    }

    .cliente-venta .choices__inner,
    .cliente-venta .choices__list--dropdown,
    .cliente-venta .choices__input {
        background: var(--cliente-fondo);
        color: var(--cliente-texto);
        border-color: var(--cliente-borde);
    }

    .cliente-venta .choices.is-focused .choices__inner,
    .cliente-venta .choices.is-open .choices__inner {
        border-color: #758bed;
        box-shadow: 0 0 0 .2rem rgba(105, 130, 234, .25);
    }

    .cliente-venta .choices__list--dropdown {
        z-index: 12;
        border-radius: .375rem;
        box-shadow: 0 .5rem 1.25rem rgba(0, 0, 0, .2);
    }

    .cliente-venta .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background: var(--cliente-resaltado);
        color: var(--cliente-texto);
    }

    .cliente-venta .choices[data-type*="select-one"]::after {
        border-top-color: var(--cliente-texto);
    }

    .cliente-venta .choices[data-type*="select-one"].is-open::after {
        border-top-color: transparent;
        border-bottom-color: var(--cliente-texto);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/extensions/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script>
    if (typeof Choices === 'function') {
        new Choices('#cliente_id', {
            allowHTML: false,
            labelId: 'etiqueta-cliente-venta',
            shouldSort: false,
            searchEnabled: true,
            searchFields: ['label'],
            searchResultLimit: 50,
            itemSelectText: '',
            searchPlaceholderValue: 'Escribe el nombre o CI/NIT',
            noResultsText: 'No se encontraron clientes',
            noChoicesText: 'No hay clientes disponibles'
        });
    }
</script>
@endpush
