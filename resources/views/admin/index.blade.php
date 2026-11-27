@extends('layouts.admin')

@push('styles')
<style>
    .inicio-panel {
        --inicio-borde: #d8dff0;
        --inicio-superficie: #edf2fc;
        --inicio-secundario: #475569;
        --inicio-barra: #3656c5;
    }

    [data-bs-theme="dark"] .inicio-panel {
        --inicio-borde: #3a415a;
        --inicio-superficie: #222842;
        --inicio-secundario: #b3bdd4;
        --inicio-barra: #7f9bff;
    }

    .inicio-cabecera {
        border: 1px solid var(--inicio-borde);
        background: var(--inicio-superficie);
    }

    .inicio-secundario { color: var(--inicio-secundario); }
    .inicio-actividad { background: var(--inicio-superficie); border-radius: .7rem; }
    .inicio-icono {
        flex: 0 0 2.8rem;
        height: 2.8rem;
        display: grid;
        place-items: center;
        border-radius: .7rem;
        font-size: 1.2rem;
    }
    .inicio-icono > .bi {
        display: block;
        line-height: 1;
    }
    .inicio-icono > .bi::before {
        display: block;
        line-height: 1;
        vertical-align: baseline;
    }
    .inicio-avatar { background: #344bb7; color: white; font-weight: 700; border-radius: 50%; }
    .inicio-grafico {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: .45rem;
    }
    .inicio-pista {
        height: 8rem;
        display: flex;
        justify-content: center;
        align-items: flex-end;
        background: var(--inicio-superficie);
        border-radius: .5rem;
        overflow: hidden;
    }
    .inicio-barra {
        width: min(62%, 2.4rem);
        height: var(--altura);
        background: var(--inicio-barra);
        border-radius: .25rem .25rem 0 0;
    }
    .inicio-anillo {
        width: 8rem;
        aspect-ratio: 1;
        border-radius: 50%;
        background: var(--pagos);
        display: grid;
        place-items: center;
    }
    .inicio-anillo-centro {
        display: grid;
        place-content: center;
        text-align: center;
        width: 70%;
        height: 70%;
        border-radius: 50%;
        background: var(--inicio-superficie);
        line-height: 1.1;
    }
    .inicio-punto {
        display: inline-block;
        width: .65rem;
        height: .65rem;
        border-radius: 50%;
        background: var(--pago-color);
    }
</style>
@endpush

@section('content')
@php
    $simbolo = $divisa ?? 'Bs';
    $moneda = fn ($importe) => $simbolo . ' ' . number_format($importe, 2);
    $maximo = max(1, $semana->max('importe'));
    $totalPagos = (int) $pagos->sum();
    $colores = ['#3e62db', '#148661', '#c87710', '#9a52c7', '#a94667'];
    $etiquetas = ['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'mixto' => 'Mixto'];
    $tramos = [];
    $leyenda = [];
    $acumulado = 0;
    foreach ($pagos as $tipo => $cantidad) {
        $color = $colores[count($leyenda) % count($colores)];
        $desde = $acumulado * 100 / $totalPagos;
        $acumulado += $cantidad;
        $hasta = $acumulado * 100 / $totalPagos;
        $tramos[] = "$color $desde% $hasta%";
        $leyenda[] = ['nombre' => $etiquetas[$tipo] ?? ($tipo ? ucfirst($tipo) : 'Sin clasificar'), 'cantidad' => $cantidad, 'color' => $color];
    }
    $gradiente = $tramos ? 'conic-gradient(' . implode(', ', $tramos) . ')' : 'var(--inicio-borde)';
@endphp

<div class="inicio-panel">
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <header class="card inicio-cabecera h-100 mb-0">
                <div class="card-body p-4">
                    <p class="small inicio-secundario mb-2">{{ $global ? 'Todas las sucursales' : ($sucursal?->nombre ?? 'Sin sucursal activa asignada') }}</p>
                    <h3 class="mb-3">Panel de {{ $config?->nombre ?: 'Farmacia' }}</h3>
                    <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-primary btn-sm">Ver ventas</a>
                    <a href="{{ route('admin.compras.index') }}" class="btn btn-outline-secondary btn-sm">Ver compras</a>
                </div>
            </header>
        </div>
        <div class="col-lg-5">
            <section class="card h-100 mb-0">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <span class="inicio-icono inicio-avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($usuario->name, 0, 1)) }}</span>
                    <div class="text-break">
                        <h5 class="mb-1">{{ $usuario->name }}</h5>
                        <div class="small inicio-secundario mb-2">{{ $usuario->email }}</div>
                        <span class="badge bg-primary">{{ $usuario->getRoleNames()->implode(' · ') ?: 'Sin rol asignado' }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @php
        $tarjetas = [
            ['Ventas de hoy', number_format($ventasHoy->cantidad), $moneda($ventasHoy->importe), 'cart-check', 'success'],
            ['Ventas del mes', number_format($ventasMes->cantidad), $moneda($ventasMes->importe), 'calendar3', 'primary'],
            ['Productos', number_format($stats['productos']), null, 'box-seam', 'warning'],
            ['Recaudado del mes', $moneda($ventasMes->importe), 'Ventas emitidas', 'wallet2', 'danger'],
            ['Partidas de inventario', number_format($stats['inventarios']), null, 'boxes', 'info'],
        ];
        if (!$global) {
            $tarjetas[] = ['Compras del mes', number_format($stats['compras_mes']), null, 'bag-check', 'primary'];
        }
        foreach (['usuarios' => ['Usuarios', 'people'], 'empleados' => ['Empleados', 'person-badge'], 'proveedores' => ['Proveedores', 'truck']] as $clave => [$etiqueta, $icono]) {
            if (isset($stats[$clave])) {
                $tarjetas[] = [$etiqueta, number_format($stats[$clave]), null, $icono, 'secondary'];
            }
        }
    @endphp
    <div class="row g-3 mb-4">
        @foreach ($tarjetas as [$titulo, $valor, $subtitulo, $icono, $color])
            <div class="col-sm-6 col-xl-3">
                <section class="card h-100 mb-0">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="inicio-icono bg-{{ $color }} text-{{ in_array($color, ['warning', 'info'], true) ? 'dark' : 'white' }}">
                            <i class="bi bi-{{ $icono }}" aria-hidden="true"></i>
                        </span>
                        <div class="text-break">
                            <div class="inicio-secundario small">{{ $titulo }}</div>
                            <div class="fs-4 fw-bold">{{ $valor }}</div>
                            @if ($subtitulo !== null)
                                <div class="inicio-secundario small">{{ $subtitulo }}</div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <section class="card h-100 mb-0">
                <div class="card-body">
                    <h5>Ventas recientes</h5>
                    @forelse ($recientes as $venta)
                        <div class="d-flex justify-content-between gap-2 inicio-actividad p-3 mb-2">
                            <div class="text-break">
                                <a href="{{ route('admin.ventas.show', $venta->id) }}">Venta #{{ $venta->id }}</a>
                                <div class="small inicio-secundario">{{ $venta->cliente ?? 'Sin cliente' }} · {{ $venta->sucursal ?? 'Sin sucursal' }}</div>
                            </div>
                            <div class="small text-end text-nowrap">
                                {{ $moneda($venta->total_venta) }}
                                <div class="inicio-secundario">{{ ucfirst($venta->estado) }} · {{ \Illuminate\Support\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="inicio-secundario mb-0">No hay ventas para mostrar.</p>
                    @endforelse
                </div>
            </section>
        </div>
        <div class="col-lg-6">
            <section class="card h-100 mb-0">
                <div class="card-body">
                    <h5>Compras recientes</h5>
                    @forelse ($latestCompras as $compra)
                        <div class="d-flex justify-content-between gap-2 inicio-actividad p-3 mb-2">
                            <div class="text-break">
                                <strong>Compra #{{ $compra->id }}</strong>
                                <div class="small inicio-secundario">{{ $compra->proveedor?->nombre ?? 'Sin proveedor' }} · {{ $compra->sucursal?->nombre ?? 'Sin sucursal' }}</div>
                            </div>
                            <div class="small text-end text-nowrap">
                                {{ \Illuminate\Support\Carbon::parse($compra->fecha_compra)->format('d/m/Y') }}
                                <div class="inicio-secundario">{{ ucfirst($compra->estado) }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="inicio-secundario mb-0">No hay compras para mostrar.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-12">
            <section class="card h-100 mb-0">
                <div class="card-body">
                    <h5>Ventas de los últimos 7 días</h5>
                    <p class="small inicio-secundario mb-3">Importe y cantidad de ventas emitidas por día</p>
                    <div id="inicio-ventas-semana"></div>
                    <div id="inicio-ventas-tabla" class="inicio-grafico">
                        @foreach ($semana as $dia)
                            <div class="text-center">
                                <div class="inicio-pista" aria-hidden="true">
                                    <span class="inicio-barra" style="--altura: {{ $dia['importe'] / $maximo * 100 }}%"></span>
                                </div>
                                <div class="small fw-semibold mt-2">{{ $dia['fecha'] }}</div>
                                <div class="small text-break">{{ $moneda($dia['importe']) }}</div>
                                <div class="small inicio-secundario">{{ $dia['cantidad'] }} {{ $dia['cantidad'] === 1 ? 'venta' : 'ventas' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-5">
            <section class="card h-100 mb-0">
                <div class="card-body">
                    <h5>Formas de pago del mes</h5>
                    @if ($totalPagos > 0)
                        <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                            <div class="inicio-anillo" style="--pagos: {{ $gradiente }}" aria-hidden="true">
                                <div class="inicio-anillo-centro"><strong>{{ $totalPagos }}</strong><small>ventas</small></div>
                            </div>
                            <ul class="list-unstyled mb-0">
                                @foreach ($leyenda as $pago)
                                    <li class="mb-1">
                                        <span class="inicio-punto me-1" style="--pago-color: {{ $pago['color'] }}" aria-hidden="true"></span>
                                        {{ $pago['nombre'] }} <strong>{{ $pago['cantidad'] }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="inicio-secundario mb-0">Todavía no hay ventas emitidas este mes.</p>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-xl-7">
            <section class="card h-100 mb-0">
                <div class="card-body">
                    <h5>Resumen mensual de ventas</h5>
                    <dl class="row g-2 mt-3 mb-0">
                        @foreach (['Emitidas' => $ventasMes->cantidad, 'Canceladas' => $canceladas, 'Recaudado' => $moneda($ventasMes->importe)] as $etiqueta => $valor)
                            <div class="col-sm-4"><dt class="inicio-secundario small">{{ $etiqueta }}</dt><dd class="fs-5 fw-bold mb-0">{{ $valor }}</dd></div>
                        @endforeach
                    </dl>
                </div>
            </section>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12">
            <section class="card h-100 mb-0">
                <div class="card-body">
                    <h5>Alertas de inventario</h5>
                    <div class="row g-2">
                        <div class="col-6"><strong class="fs-4 text-danger">{{ $vencidos }}</strong><div>Lotes vencidos con stock</div></div>
                        <div class="col-6"><strong class="fs-4">{{ $proximos }}</strong><div>Lotes que vencen en 30 días</div></div>
                    </div>
                    <a class="btn btn-outline-primary btn-sm mt-3" href="{{ route('admin.inventarios.existencias', $global || !$sucursal ? [] : ['sucursal_id' => $sucursal->id]) }}">Ver inventario</a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const destino = document.getElementById('inicio-ventas-semana');
        if (!destino || typeof ApexCharts === 'undefined') return;

        const dias = {{ \Illuminate\Support\Js::from($semana->values()) }};
        const divisa = {{ \Illuminate\Support\Js::from($simbolo) }};
        const moneda = valor => divisa + ' ' + Number(valor).toLocaleString('es-BO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const tema = () => document.documentElement.dataset.bsTheme === 'dark' ? 'dark' : 'light';
        const grafico = new ApexCharts(destino, {
            chart: {type: 'line', height: 285, background: 'transparent', toolbar: {show: false}},
            theme: {mode: tema()},
            colors: ['#6381ef', '#16a985'],
            series: [
                {name: 'Ventas', type: 'column', data: dias.map(dia => dia.cantidad)},
                {name: 'Importe', type: 'line', data: dias.map(dia => dia.importe)}
            ],
            stroke: {width: [0, 3], curve: 'straight'},
            markers: {size: [0, 4]},
            plotOptions: {bar: {columnWidth: '38%', borderRadius: 4}},
            xaxis: {categories: dias.map(dia => dia.fecha)},
            yaxis: [
                {seriesName: 'Ventas', min: 0, decimalsInFloat: 0, title: {text: 'Ventas'}},
                {seriesName: 'Importe', min: 0, opposite: true, title: {text: divisa}, labels: {formatter: moneda}}
            ],
            tooltip: {shared: true, intersect: false, y: [{formatter: valor => valor}, {formatter: moneda}]},
            legend: {position: 'top', horizontalAlign: 'right'}
        });

        try {
            await grafico.render();
            document.getElementById('inicio-ventas-tabla').classList.add('visually-hidden');
        } catch {
            destino.replaceChildren();
            return;
        }

        new MutationObserver(() => grafico.updateOptions({theme: {mode: tema()}}))
            .observe(document.documentElement, {attributes: true, attributeFilter: ['data-bs-theme']});
    });
</script>
@endpush
