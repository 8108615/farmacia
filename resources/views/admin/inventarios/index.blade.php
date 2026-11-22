@extends('layouts.admin')

@push('styles')

    <style>
        .inventory-shell {
            --inv-text: #0f172a;
            --inv-muted: #52647b;
            --inv-surface: #fff;
            --inv-border: #dbe4ef;
            --inv-chip: #fff;
            --inv-badge: #e8efff;
            --inv-badge-text: #2855bc;
            --inv-hero: linear-gradient(110deg, #d5effc, #f9fbfd 50%, #d6e1fb);
            --inv-shadow: 0 14px 28px rgba(32, 59, 105, .09);
            --inv-blue: #2563eb;
            --inv-cyan: #087e9e;
            --inv-amber: #a85b05;
            --inv-rose: #d62350;
        }

        [data-bs-theme="dark"] .inventory-shell {
            --inv-text: #edf2ff;
            --inv-muted: #afbdd4;
            --inv-surface: #222538;
            --inv-border: #353c54;
            --inv-chip: #263250;
            --inv-badge: #2c3c62;
            --inv-badge-text: #a8c2ff;
            --inv-hero: linear-gradient(110deg, #173c52, #22283c 50%, #2a365a);
            --inv-shadow: 0 14px 28px rgba(0, 0, 0, .18);
        }

        .inventory-shell .inventory-hero,
        .inventory-shell .inventory-stat,
        .inventory-shell .module-card {
            border: 1px solid var(--inv-border);
            border-radius: 1.1rem;
            box-shadow: var(--inv-shadow);
        }

        .inventory-shell .inventory-hero {
            background: var(--inv-hero);
            color: var(--inv-text);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .inventory-shell .inventory-hero h3,
        .inventory-shell .module-title,
        .inventory-shell .value {
            color: var(--inv-text);
            font-weight: 700;
        }

        .inventory-shell .inventory-hero p,
        .inventory-shell .inventory-hero .text-muted,
        .inventory-shell .label,
        .inventory-shell .module-desc {
            color: var(--inv-muted) !important;
        }

        .inventory-shell .hero-chip-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
            margin-top: 1rem;
        }

        .inventory-shell .hero-chip {
            background: var(--inv-chip);
            border: 1px solid var(--inv-border);
            border-radius: 999px;
            color: var(--inv-text);
            font-size: .82rem;
            font-weight: 600;
            padding: .35rem .7rem;
        }

        .inventory-shell .inventory-stat-grid,
        .inventory-shell .module-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1fr);
        }

        .inventory-shell .inventory-stat-grid {
            margin-bottom: 3rem;
        }

        .inventory-shell .inventory-stat,
        .inventory-shell .module-card {
            background: var(--inv-surface);
            margin: 0;
            min-width: 0;
        }

        .inventory-shell .inventory-stat .card-body {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: 1rem;
        }

        .inventory-shell .inventory-icon,
        .inventory-shell .module-badge {
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .inventory-shell .inventory-icon {
            width: 3rem;
            height: 3rem;
            border-radius: .9rem;
            color: #fff;
        }

        .inventory-shell .label,
        .inventory-shell .value,
        .inventory-shell .module-title {
            margin: 0;
        }

        .inventory-shell .label,
        .inventory-shell .module-desc {
            font-size: .875rem;
        }

        .inventory-shell .value {
            font-size: 1.5rem;
            line-height: 1.2;
        }

        .inventory-shell .module-card .card-body {
            display: flex;
            flex-direction: column;
            min-height: 12rem;
            padding: 1rem;
        }

        .inventory-shell .module-header {
            display: flex;
            align-items: center;
            gap: .65rem;
        }

        .inventory-shell .module-badge {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: .65rem;
            background: var(--inv-badge);
            color: var(--inv-badge-text);
        }

        .inventory-shell .module-title {
            font-size: 1rem;
        }

        .inventory-shell .module-desc {
            margin: .65rem 0 1.1rem;
        }

        @media (min-width: 576px) {
            .inventory-shell .inventory-stat-grid,
            .inventory-shell .module-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 992px) {
            .inventory-shell .module-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 1200px) {
            .inventory-shell .inventory-stat-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (min-width: 1600px) {
            .inventory-shell .module-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }

        .inventory-shell .inventory-icon > .bi,
        .inventory-shell .module-badge > .bi {
            display: grid;
            place-items: center;
            width: 1em;
            height: 1em;
            line-height: 1;
        }

        .inventory-shell .inventory-icon > .bi::before,
        .inventory-shell .module-badge > .bi::before {
            display: block;
            line-height: 1;
            vertical-align: baseline;
        }
    </style>
@endpush

@section('content')
    @php
        $modulos = [
            [
                'titulo' => 'Resumen de inventario',
                'descripcion' => 'Vista consolidada de indicadores principales del inventario.',
                'icono' => 'bi bi-speedometer2',
                'ruta' => route('admin.inventarios.resumen'),
            ],
            [
                'titulo' => 'Existencias por producto',
                'descripcion' => 'Consulta de stock por producto, lote y sucursal.',
                'icono' => 'bi bi-box-seam',
                'ruta' => route('admin.inventarios.existencias'),
            ],
            [
                'titulo' => 'Movimientos',
                'descripcion' => 'Entradas, salidas y ajustes de inventario.',
                'icono' => 'bi bi-arrow-left-right',
                'ruta' => route('admin.inventarios.movimientos'),
            ],
            [
                'titulo' => 'Kardex por producto/lote',
                'descripcion' => 'Historial cronologico de movimientos y saldo.',
                'icono' => 'bi bi-journal-text',
                'ruta' => route('admin.inventarios.kardex'),
            ],
            [
                'titulo' => 'Lotes y vencimientos',
                'descripcion' => 'Control sanitario de lotes vigentes y vencidos.',
                'icono' => 'bi bi-calendar-event',
                'ruta' => route('admin.inventarios.lotesVencimiento'),
            ],

            [
                'titulo' => 'Traslados entre sucursales',
                'descripcion' => 'Transferencias de inventario entre almacenes.',
                'icono' => 'bi bi-truck',
                'ruta' => route('admin.inventarios.traslados'),
            ],
            [
                'titulo' => 'Alertas',
                'descripcion' => 'Stock minimo, por vencer, vencidos y sin ubicacion.',
                'icono' => 'bi bi-bell',
                'ruta' => route('admin.inventarios.alertas'),
            ],
            [
                'titulo' => 'Reportes y exportaciones',
                'descripcion' => 'Descarga reportes operativos en diferentes formatos.',
                'icono' => 'bi bi-file-earmark-spreadsheet',
                'ruta' => route('admin.inventarios.reportes'),
            ],
        ];
    @endphp
    <div class="inventory-shell">
        <div class="page-heading">
            <div class="inventory-hero">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                    <div>
                        <h3>Inventario</h3>
                        <p>Panel resumen del inventario y acceso a cada vista especializada.</p>
                        <div class="hero-chip-wrap">
                            <span class="hero-chip">Registros: {{ $totalItems }}</span>
                            <span class="hero-chip">Stock total: {{ number_format($totalStock, 0, '.', ',') }}</span>
                            <span class="hero-chip">Bajo mínimo: {{ $totalBajoMinimo }}</span>
                            <span class="hero-chip">Por vencer: {{ $totalPorVencer }}</span>
                            <span class="hero-chip">Sin ubicación: {{ $totalSinUbicacion }}</span>
                        </div>
                    </div>
                    <div class="text-lg-end">
                        <h6 class="mb-1 text-muted">Valorización global</h6>
                        <div><strong>Costo:</strong> {{ $divisa }} {{ number_format($valorCompra, 2, '.', ',') }}
                        </div>
                        <div><strong>Venta:</strong> {{ $divisa }} {{ number_format($valorVenta, 2, '.', ',') }}</div>
                        <div><strong>Margen:</strong>
                            {{ $divisa }} {{ number_format(max($valorVenta - $valorCompra, 0), 2, '.', ',') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="inventory-stat-grid">
                <div class="card inventory-stat">
                    <div class="card-body">
                        <div class="inventory-icon" style="background: var(--inv-blue);">
                            <i class="bi bi-boxes fs-5"></i>
                        </div>
                    <div>
                        <p class="label">Registros</p>
                        <p class="value">{{ number_format($totalItems, 0, '.', ',') }}</p>
                    </div>
                </div>
            </div>
            <div class="card inventory-stat">
                <div class="card-body">
                    <div class="inventory-icon" style="background: var(--inv-cyan);">
                        <i class="bi bi-layers fs-5"></i>
                    </div>
                    <div>
                        <p class="label">Unidades en stock</p>
                        <p class="value">{{ number_format($totalStock, 0, '.', ',') }}</p>
                    </div>
                </div>
            </div>
            <div class="card inventory-stat">
                <div class="card-body">
                    <div class="inventory-icon" style="background: var(--inv-amber);">
                        <i class="bi bi-exclamation-triangle fs-5"></i>
                    </div>
                    <div>
                        <p class="label">Stock bajo mínimo</p>
                        <p class="value">{{ number_format($totalBajoMinimo, 0, '.', ',') }}</p>
                    </div>
                </div>
            </div>

            <div class="card inventory-stat">
                <div class="card-body">
                    <div class="inventory-icon" style="background: var(--inv-rose);">
                        <i class="bi bi-calendar-x fs-5"></i>
                    </div>
                    <div>
                        <p class="label">Lotes por vencer</p>
                        <p class="value">{{ number_format($totalPorVencer, 0, '.', ',') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="module-grid">
            @foreach ($modulos as $modulo)
                <div class="card module-card">
                    <div class="card-body">
                        <div class="module-header">
                            <span class="module-badge">
                                <i class="{{ $modulo['icono'] }}"></i>
                            </span>
                            <h5 class="module-title">{{ $modulo['titulo'] }}</h5>
                        </div>
                        <p class="module-desc">{{ $modulo['descripcion'] }}</p>
                        <div class="mt-auto">
                            <a href="{{ $modulo ['ruta'] }}" class="btn btn-primary btn-sm w-100">
                                Ver más
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection


