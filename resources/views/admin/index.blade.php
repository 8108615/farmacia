@extends('layouts.admin')

@push('styles')
    <style>
        @import url('https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap');

        :root {
            --dash-ink: #0f172a;
            --dash-muted: #64748b;
            --dash-soft: #e2e8f0;
            --dash-glow: rgba(15, 23, 42, 0.08);
            --dash-indigo: #1d4ed8;
            --dash-emerald: #10b981;
            --dash-amber: #f59e0b;
            --dash-rose: #f43f5e;
            --dash-sky: #38bdf8;
        }

        .dashboard-shell {
            font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif;
        }

        .dash-hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .hero-card {
            border-radius: 1.5rem;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: radial-gradient(circle at top left, rgba(59, 130, 246, 0.18), transparent 55%),
                linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            box-shadow: 0 24px 45px var(--dash-glow);
            padding: 1.75rem;
        }

        .hero-card h2 {
            font-weight: 700;
            color: var(--dash-ink);
            margin-bottom: 0.5rem;
        }

        .hero-card p {
            color: var(--dash-muted);
            margin-bottom: 1.25rem;
        }

        .hero-metrics {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero-chip {
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            border: 1px solid rgba(15, 23, 42, 0.1);
            background: #fff;
            font-size: 0.85rem;
            color: var(--dash-ink);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            border-radius: 1.25rem;
            border: none;
            box-shadow: 0 18px 40px var(--dash-glow);
            overflow: hidden;
        }

        .stat-card-body {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.4rem;
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
            line-height: 1;
        }

        .stat-title {
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
            color: var(--dash-muted);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dash-ink);
            margin-bottom: 0.15rem;
        }

        .stat-desc {
            margin: 0;
            color: var(--dash-muted);
            font-size: 0.85rem;
        }

        .dash-panel {
            border-radius: 1.5rem;
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: 0 18px 45px var(--dash-glow);
        }

        .dash-panel .card-body {
            padding: 1.6rem;
        }

        .dash-progress {
            height: 12px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .dash-progress span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, #f97316, #f43f5e);
        }

        .dash-table {
            display: grid;
            gap: 0.75rem;
        }

        .dash-table-row {
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .dash-table-row small {
            color: var(--dash-muted);
        }

        .dash-tag {
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(37, 99, 235, 0.12);
            color: var(--dash-indigo);
        }

        .dash-tag.pending {
            background: rgba(249, 115, 22, 0.12);
            color: #c2410c;
        }

        .dash-card-muted {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-shell">
        <div class="page-heading">
            <div class="dash-hero">
                <div class="hero-card">
                    <h2>Dashboard Farmacia</h2>
                    <p>Panorama general de operaciones, compras y stock en tiempo real.</p>
                    <div class="hero-metrics">
                        <span class="hero-chip">Compras hoy: {{ $stats['compras_hoy'] }}</span>
                        <span class="hero-chip">Compras del mes: {{ $stats['compras_mes'] }}</span>
                        <span class="hero-chip">Items registrados: {{ $stats['items_compra'] }}</span>
                    </div>
                </div>
                <div class="hero-card">
                    <h2>Estado del sistema</h2>
                    <p>Configuracion general y estado operativo actual.</p>
                    <div class="dash-card-muted">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <strong>Configuracion</strong>
                            <span class="badge bg-{{ $stats['configuracion'] ? 'success' : 'danger' }}">
                                {{ $stats['configuracion'] ? 'Cargada' : 'Faltante' }}
                            </span>
                        </div>
                        @if ($config)
                            <div class="mb-1"><strong>Nombre:</strong> {{ $config->nombre }}</div>
                            <div class="mb-1"><strong>Email:</strong> {{ $config->email }}</div>
                            <div><strong>Divisa:</strong> {{ $config->divisa }}</div>
                        @else
                            <div class="alert alert-warning mb-0">Aun no hay configuracion general.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="stat-grid">
                <div class="card stat-card">
                    <div class="stat-card-body">
                        <div class="stat-icon" style="background: var(--dash-indigo);">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div>
                            <p class="stat-title">Usuarios</p>
                            <p class="stat-value">{{ $stats['usuarios'] }}</p>
                            <p class="stat-desc">Usuarios activos en plataforma</p>
                        </div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-card-body">
                        <div class="stat-icon" style="background: var(--dash-emerald);">
                            <i class="bi bi-person-badge-fill fs-4"></i>
                        </div>
                        <div>
                            <p class="stat-title">Empleados</p>
                            <p class="stat-value">{{ $stats['empleados'] }}</p>
                            <p class="stat-desc">Equipo operativo total</p>
                        </div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-card-body">
                        <div class="stat-icon" style="background: var(--dash-amber);">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <div>
                            <p class="stat-title">Productos</p>
                            <p class="stat-value">{{ $stats['productos'] }}</p>
                            <p class="stat-desc">Catalogo disponible</p>
                        </div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-card-body">
                        <div class="stat-icon" style="background: var(--dash-sky);">
                            <i class="bi bi-truck fs-4"></i>
                        </div>
                        <div>
                            <p class="stat-title">Proveedores</p>
                            <p class="stat-value">{{ $stats['proveedores'] }}</p>
                            <p class="stat-desc">Red de suministro</p>
                        </div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-card-body">
                        <div class="stat-icon" style="background: var(--dash-rose);">
                            <i class="bi bi-archive-fill fs-4"></i>
                        </div>
                        <div>
                            <p class="stat-title">Lotes</p>
                            <p class="stat-value">{{ $stats['lotes'] }}</p>
                            <p class="stat-desc">Control de vencimientos</p>
                        </div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-card-body">
                        <div class="stat-icon" style="background: #0ea5e9;">
                            <i class="bi bi-stack fs-4"></i>
                        </div>
                        <div>
                            <p class="stat-title">Inventarios</p>
                            <p class="stat-value">{{ $stats['inventarios'] }}</p>
                            <p class="stat-desc">Registros de stock</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-xl-5">
                    <div class="card dash-panel">
                        <div class="card-body">
                            <h5 class="card-title">Compras en foco</h5>
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Total</p>
                                    <h3 class="mb-0">{{ $stats['compras_total'] }}</h3>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1">Completadas</p>
                                    <h3 class="mb-0">{{ $stats['compras_completadas'] }}</h3>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Pendientes</p>
                                    <h3 class="mb-0">{{ $stats['compras_pendientes'] }}</h3>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1">Sucursales</p>
                                    <h3 class="mb-0">{{ $stats['sucursales'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">Pendientes vs total</small>
                                    <small class="text-muted">{{ $comprasPendientesRatio }}%</small>
                                </div>
                                <div class="dash-progress">
                                    <span style="width: {{ $comprasPendientesRatio }}%"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-7">
                    <div class="card dash-panel">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Compras recientes</h5>
                                <span class="dash-tag">Ultimas 6</span>
                            </div>
                            <div class="dash-table">
                                @forelse ($latestCompras as $compra)
                                    <div class="dash-table-row">
                                        <div>
                                            <strong>#{{ $compra->id }}</strong>
                                            <small class="d-block">
                                                {{ $compra->proveedor?->nombre ?? 'Proveedor' }} -
                                                {{ $compra->sucursal?->nombre ?? 'Sucursal' }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <small class="d-block">{{ $compra->fecha_compra?->format('d/m/Y') }}</small>
                                            <span class="dash-tag {{ $compra->estado === 'pendiente' ? 'pending' : '' }}">
                                                {{ ucfirst($compra->estado) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="dash-table-row">
                                        <small class="text-muted">Aun no hay compras registradas.</small>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-xl-6">
                    <div class="card dash-panel">
                        <div class="card-body">
                            <h5 class="card-title">Actividad de empleados</h5>
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Activos</p>
                                    <h3 class="mb-0">{{ $stats['empleados_activos'] }}</h3>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1">Inactivos</p>
                                    <h3 class="mb-0">{{ $stats['empleados_inactivos'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">Participacion activa</small>
                                    <small class="text-muted">{{ $activeRatio }}%</small>
                                </div>
                                <div class="dash-progress">
                                    <span
                                        style="width: {{ $activeRatio }}%; background: linear-gradient(90deg, #22c55e, #14b8a6);"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card dash-panel">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Ultimos empleados</h5>
                                <span class="dash-tag">Ultimos 5</span>
                            </div>
                            <div class="dash-table">
                                @forelse ($latestEmployees as $empleado)
                                    <div class="dash-table-row">
                                        <div>
                                            <strong>{{ $empleado->usuario?->name ?? 'Empleado' }}</strong>
                                            <small
                                                class="d-block">{{ $empleado->sucursal?->nombre ?? 'Sucursal' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="d-block">{{ $empleado->usuario?->email }}</small>
                                            <span class="dash-tag {{ $empleado->estado === 'activo' ? '' : 'pending' }}">
                                                {{ ucfirst($empleado->estado) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="dash-table-row">
                                        <small class="text-muted">Aun no hay empleados registrados.</small>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
