@extends('layouts.admin')

@section('title', 'Reportes')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-bar-chart me-2"></i>Reportes</h1>
</div>

<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#ventas-fecha" style="color: #1a1a1a;">
            Ventas por fecha
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#ventas-categoria" style="color: #1a1a1a;">
            Ventas por categoría
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#top-lentes" style="color: #1a1a1a;">
            Lentes más vendidos
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#top-usuarios" style="color: #1a1a1a;">
            Usuarios top
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="ventas-fecha">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: #1a1a1a; color: #D4AF37;">
                <h5 class="mb-0"><i class="bi bi-calendar me-2"></i>Ventas por fecha</h5>
                <a href="{{ route('admin.reportes.exportar', 'ventas-fecha') }}" class="btn btn-sm btn-gold">
                    <i class="bi bi-download me-1"></i>Exportar
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha desde</label>
                        <input type="date" class="form-control" id="desde" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha hasta</label>
                        <input type="date" class="form-control" id="hasta" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-gold w-100" onclick="filtrarVentas()">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
                <canvas id="ventasFechaChart" height="200"></canvas>
                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead><tr><th>Fecha</th><th>Pedidos</th><th>Ingresos</th></tr></thead>
                        <tbody id="ventas-fecha-body">
                            <tr><td colspan="3" class="text-center text-muted">Selecciona fechas y filtra</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="ventas-categoria">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: #1a1a1a; color: #D4AF37;">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Ventas por categoría</h5>
                <a href="{{ route('admin.reportes.exportar', 'ventas-categoria') }}" class="btn btn-sm btn-gold">
                    <i class="bi bi-download me-1"></i>Exportar
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="categoriaChart" height="250"></canvas>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <thead><tr><th>Categoría</th><th>Unidades</th><th>Ingresos</th></tr></thead>
                            <tbody id="categoria-body">
                                <tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="top-lentes">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: #1a1a1a; color: #D4AF37;">
                <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Lentes más vendidos</h5>
                <a href="{{ route('admin.reportes.exportar', 'top-lentes') }}" class="btn btn-sm btn-gold">
                    <i class="bi bi-download me-1"></i>Exportar
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7">
                        <canvas id="topLentesChart" height="250"></canvas>
                    </div>
                    <div class="col-md-5">
                        <table class="table table-sm">
                            <thead><tr><th>#</th><th>Lente</th><th>Vendidos</th><th>Total</th></tr></thead>
                            <tbody id="top-lentes-body">
                                <tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="top-usuarios">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: #1a1a1a; color: #D4AF37;">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Usuarios con más compras</h5>
                <a href="{{ route('admin.reportes.exportar', 'top-usuarios') }}" class="btn btn-sm btn-gold">
                    <i class="bi bi-download me-1"></i>Exportar
                </a>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead><tr><th>#</th><th>Usuario</th><th>Email</th><th>Compras</th><th>Total gastado</th></tr></thead>
                    <tbody id="top-usuarios-body">
                        <tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let charts = {};

async function loadReport(url, chartId, tableBody, chartConfig) {
    try {
        const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await resp.json();
        if (data.success) {
            if (tableBody) {
                document.getElementById(tableBody).innerHTML = data.data.map((item, i) =>
                    `<tr>${Object.values(item).map(v => `<td>${v ?? '-'}</td>`).join('')}</tr>`
                ).join('');
            }
            if (chartConfig && data.data.length) {
                const ctx = document.getElementById(chartId);
                if (charts[chartId]) charts[chartId].destroy();
                charts[chartId] = new Chart(ctx, chartConfig(data.data));
            }
        }
    } catch(e) { console.error('Error loading report:', e); }
}

document.addEventListener('DOMContentLoaded', function() {
    loadReport(
        '{{ route("admin.reportes.ventas-categoria") }}',
        'categoriaChart',
        'categoria-body',
        (data) => ({
            type: 'doughnut',
            data: {
                labels: data.map(d => d.nombre),
                datasets: [{
                    data: data.map(d => d.total),
                    backgroundColor: ['#D4AF37', '#1a1a1a', '#666', '#f0d060', '#999'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        })
    );

    loadReport(
        '{{ route("admin.reportes.lentes-top") }}',
        'topLentesChart',
        'top-lentes-body',
        (data) => ({
            type: 'bar',
            data: {
                labels: data.map(d => d.nombre),
                datasets: [{
                    label: 'Vendidos',
                    data: data.map(d => d.veces_vendido),
                    backgroundColor: '#D4AF37',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
            }
        })
    );

    loadReport(
        '{{ route("admin.reportes.usuarios-top") }}',
        null,
        'top-usuarios-body',
        null
    );
});

async function filtrarVentas() {
    const desde = document.getElementById('desde').value;
    const hasta = document.getElementById('hasta').value;
    if (!desde || !hasta) { alert('Selecciona ambas fechas'); return; }

    const url = `{{ route("admin.reportes.ventas-fecha") }}?desde=${desde}&hasta=${hasta}`;
    await loadReport(url, 'ventasFechaChart', 'ventas-fecha-body', (data) => ({
        type: 'line',
        data: {
            labels: data.map(d => d.fecha),
            datasets: [{
                label: 'Ingresos (Bs)',
                data: data.map(d => d.ingresos),
                borderColor: '#D4AF37',
                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
        }
    }));
}

document.addEventListener('DOMContentLoaded', () => setTimeout(filtrarVentas, 100));
</script>
@endpush
