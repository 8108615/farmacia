@extends('layouts.admin')

@push('styles')
    <style>
        @import url('https://fonts.bunny.net/css2?family=space-grotesk:400,500,600,700&display=swap');

        :root {
            --inv-ink: #0f172a;
            --inv-muted: #64748b;
            --inv-soft: #e2e8f0;
            --inv-glow: rgba(15, 23, 42, 0.08);
            --inv-blue: #2563eb;
            --inv-cyan: #0891b2;
            --inv-emerald: #059669;
            --inv-amber: #d97706;
            --inv-rose: #e11d48;
        }

        .inventory-shell {
            font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif;
        }

        .inventory-hero {
            border: 1px solid rgba(59, 130, 246, 0.15);
            border-radius: 1.4rem;
            background:
                radial-gradient(circle at 0% 0%, rgba(56, 189, 248, 0.22), transparent 48%),
                radial-gradient(circle at 100% 100%,  ☐ rgba(37, 99, 235, 0.2), transparent 42%),
                linear-gradient(130deg, #ffffff, #f8fafc);
            box-shadow: 0 28px 45px var(--inv-glow);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .inventory-hero h3 {
            margin-bottom: 0.35rem;
            color: var(--inv-ink);
            font-weight: 700;  
        }

        .inventory-hero p {
            margin-bottom: 0;
            color: var(--inv-muted);
        }

        .hero-chip-wrap {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .hero-chip {
            background: var(--inv-soft);
            color: var(--inv-ink);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
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
                            <span class="hero-chip">Bajo minimo: {{ $totalBajoMinimo }}</span>
                            <span class="hero-chip">Por vencer: {{ $totalPorVencer }}</span>
                            <span class="hero-chip">Sin ubicacion: {{ $totalSinUbicacion }}</span>
                        </div>
                    </div>
                    <div class="text-lg-end">
                        <h6 class="mb-1 text-muted">Valorizacion global</h6>
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
                        <p class="label">Stock bajo minimo</p>
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
                                Ver mas
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
    
