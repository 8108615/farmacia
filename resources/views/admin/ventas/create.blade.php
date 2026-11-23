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

    <section class="card">
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
                    <input id="buscar-producto" type="search" class="form-control" placeholder="Nombre, código, código de barras o lote">
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle">
                    <thead><tr>
                        <th>Producto</th><th>Lote</th><th>Vencimiento</th>
                        <th class="text-end">Stock</th><th class="text-end">Precio unitario</th><th>Acción</th>
                    </tr></thead>
                    <tbody>
                    @foreach ($partidas as $partida)
                        <tr data-partida="{{ $partida->id }}" data-sucursal="{{ $partida->sucursal_id }}"
                            data-stock="{{ $partida->stock_actual }}" data-precio="{{ $partida->precio_venta_unidad }}"
                            data-producto="{{ $partida->producto?->nombre_comercial }}"
                            data-lote="{{ $partida->lote?->numero_lote }}"
                            data-busqueda="{{ $partida->producto?->nombre_comercial }} {{ $partida->producto?->nombre_generico }} {{ $partida->producto?->codigo_producto }} {{ $partida->producto?->codigo_barra }} {{ $partida->lote?->numero_lote }}" hidden>
                            <td>{{ $partida->producto?->nombre_comercial }}<br><small>{{ $partida->producto?->codigo_producto }}</small></td>
                            <td>{{ $partida->lote?->numero_lote }} <small>(#{{ $partida->id }})</small></td>
                            <td>{{ $partida->lote?->fecha_vencimiento?->format('d/m/Y') }}</td>
                            <td class="text-end">{{ $partida->stock_actual }}</td>
                            <td class="text-end">{{ $divisa }} {{ $partida->precio_venta_unidad }}</td>
                            <td><button type="button" class="btn btn-outline-primary btn-sm agregar-partida">Agregar</button></td>
                        </tr>
                    @endforeach
                    <tr id="sin-resultados"><td colspan="6" class="text-muted">Elige una sucursal para consultar sus existencias.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <h4 class="card-title">Carrito</h4>
            <p id="carrito-vacio" class="text-muted">Agrega una partida para comenzar.</p>
            <div class="table-responsive">
                <table id="tabla-carrito" class="table align-middle" hidden>
                    <thead><tr><th>Producto y partida</th><th>Cantidad</th><th class="text-end">Subtotal</th><th>Acción</th></tr></thead>
                    <tbody id="lineas-carrito"></tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <strong>Total <output id="total-venta">{{ $divisa }} 0.00</output></strong>
                <button id="revisar-venta" type="button" class="btn btn-primary" disabled>Siguiente</button>
            </div>
        </div>
    </section>

    <section id="confirmacion-venta" class="card" hidden>
        <div class="card-body">
            <h4 class="card-title">Confirmar venta</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select id="cliente_id" name="cliente_id" class="form-select" required>
                        <option value="">Elige un cliente</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>{{ $cliente->nombres_apellidos }}</option>
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
                <div class="col-12">
                    <button id="guardar-venta" class="btn btn-success" disabled>Confirmar venta</button>
                </div>
            </div>
        </div>
    </section>
</form>

<template id="plantilla-linea">
    <tr>
        <td class="detalle-partida"><input type="hidden" class="id-partida"></td>
        <td><input type="number" min="1" class="form-control cantidad-partida" required></td>
        <td class="subtotal text-end"></td>
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
    const siguiente = document.querySelector('#revisar-venta');
    const confirmacion = document.querySelector('#confirmacion-venta');
    const guardar = document.querySelector('#guardar-venta');
    const operacionAnterior = {{ Illuminate\Support\Js::from(old('items', [])) }};
    let sucursalAnterior = sucursal.value;

    function filtrar() {
        const texto = buscar.value.trim().toLocaleLowerCase();
        for (const fila of filas) {
            fila.hidden = fila.dataset.sucursal !== sucursal.value ||
                !fila.dataset.busqueda.toLocaleLowerCase().includes(texto);
        }
        const aviso = document.querySelector('#sin-resultados');
        aviso.hidden = filas.some(fila => !fila.hidden);
        aviso.firstElementChild.textContent = sucursal.value
            ? 'No hay partidas disponibles para esta búsqueda.'
            : 'Elige una sucursal para consultar sus existencias.';
    }

    function moneda(centavos) {
        return form.dataset.divisa + ' ' + (centavos / 100).toFixed(2);
    }

    function actualizar() {
        let total = 0;
        let valido = lineas.rows.length > 0;
        for (const linea of lineas.rows) {
            const cantidad = linea.querySelector('.cantidad-partida');
            const subtotal = cantidad.validity.valid ? Number(cantidad.value) * Number(linea.dataset.centavos) : 0;
            valido = valido && cantidad.validity.valid;
            linea.querySelector('.subtotal').textContent = moneda(subtotal);
            total += subtotal;
        }
        document.querySelector('#total-venta').textContent = moneda(total);
        document.querySelector('#carrito-vacio').hidden = lineas.rows.length > 0;
        document.querySelector('#tabla-carrito').hidden = lineas.rows.length === 0;
        siguiente.disabled = !valido;
        guardar.disabled = !valido || confirmacion.hidden;
        for (const fila of filas) {
            fila.querySelector('button').disabled = lineas.rows.length >= 100 ||
                Boolean(lineas.querySelector('[data-id="' + fila.dataset.partida + '"]'));
        }
    }

    function agregar(fila, unidades = 1) {
        const datos = fila.dataset;
        const linea = document.querySelector('#plantilla-linea').content.firstElementChild.cloneNode(true);
        linea.dataset.id = datos.partida;
        linea.dataset.centavos = Math.round(Number(datos.precio) * 100);
        linea.querySelector('.detalle-partida').append(
            datos.producto + ' · ' + datos.lote + ' (#' + datos.partida + ')'
        );
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

    for (const fila of filas) {
        fila.querySelector('button').addEventListener('click', () => agregar(fila));
    }

    sucursal.addEventListener('change', () => {
        if (lineas.rows.length && !window.confirm('Cambiar de sucursal vaciará el carrito. ¿Continuar?')) {
            sucursal.value = sucursalAnterior;
            return;
        }
        sucursalAnterior = sucursal.value;
        lineas.replaceChildren();
        confirmacion.hidden = true;
        filtrar();
        actualizar();
    });
    buscar.addEventListener('input', filtrar);

    siguiente.addEventListener('click', () => {
        confirmacion.hidden = false;
        actualizar();
        document.querySelector('#cliente_id').focus();
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
        if (fila && !fila.querySelector('button').disabled) agregar(fila, item.cantidad);
    }
    if (lineas.rows.length) confirmacion.hidden = false;
    filtrar();
    actualizar();
})();
</script>
@endsection
