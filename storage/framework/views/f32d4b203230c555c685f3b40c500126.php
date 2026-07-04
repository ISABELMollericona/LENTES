<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
    <p class="text-muted">Panel de control - Óptica Golden</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-kpi">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label">Usuarios registrados</p>
                        <div class="kpi-value"><span class="counter-value" data-target="<?php echo e($dashboard['usuarios_registrados']); ?>" data-duration="1200">0</span></div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-kpi">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label">Lentes disponibles</p>
                        <div class="kpi-value" style="color: #2b8a3e;"><span class="counter-value" data-target="<?php echo e($dashboard['lentes_disponibles']); ?>" data-duration="1200">0</span></div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-eyeglasses"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-kpi">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label">Lentes vendidos</p>
                        <div class="kpi-value" style="color: #c92a2a;"><span class="counter-value" data-target="<?php echo e($dashboard['lentes_vendidos']); ?>" data-duration="1200">0</span></div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-bag-check"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-kpi">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label">Ingresos totales</p>
                        <div class="kpi-value">Bs <span class="counter-value" data-target="<?php echo e($dashboard['ingresos_totales']); ?>" data-duration="1500" data-suffix="">0</span></div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-kpi">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label">Pedidos realizados</p>
                        <div class="kpi-value"><span class="counter-value" data-target="<?php echo e($dashboard['pedidos_realizados']); ?>" data-duration="1200">0</span></div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-box"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-kpi">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label">Ventas totales</p>
                        <div class="kpi-value"><span class="counter-value" data-target="<?php echo e($dashboard['ventas_totales']); ?>" data-duration="1200">0</span></div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-graph-up"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-kpi">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label">Recomendaciones IA</p>
                        <div class="kpi-value"><span class="counter-value" data-target="<?php echo e($dashboard['recomendaciones_realizadas']); ?>" data-duration="1200">0</span></div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-robot"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-kpi">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label">Análisis faciales</p>
                        <div class="kpi-value"><span class="counter-value" data-target="<?php echo e($dashboard['analisis_faciales']); ?>" data-duration="1200">0</span></div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-camera"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="chart-container">
            <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2" style="color: #D4AF37;"></i>Ventas por mes</h5>
            <canvas id="ventasChart" height="250"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-container">
            <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2" style="color: #D4AF37;"></i>Categorías</h5>
            <canvas id="categoriasChart" height="250"></canvas>
        </div>
    </div>
</div>

<div class="row g-3 mt-3">
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="fw-bold mb-3"><i class="bi bi-trophy me-2" style="color: #D4AF37;"></i>Lentes más vendidos</h5>
            <canvas id="topLentesChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600;">
                <i class="bi bi-activity me-1"></i> Actividad reciente
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                <p class="text-muted text-center py-3">Últimas transacciones y eventos del sistema</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function createGoldParticles() {
    const container = document.querySelector('.container-fluid');
    if (!container) return;
    const colors = ['#D4AF37', '#F0D060', '#B8962E', '#FFD700', '#E8C547'];
    for (let i = 0; i < 6; i++) {
        const dot = document.createElement('div');
        dot.style.cssText = `
            position: fixed;
            width: ${Math.random() * 4 + 2}px;
            height: ${Math.random() * 4 + 2}px;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            opacity: 0;
            left: ${Math.random() * 100}vw;
            top: ${Math.random() * 100}vh;
            animation: goldParticle ${Math.random() * 3 + 2}s ease-out ${Math.random() * 3}s infinite;
        `;
        container.appendChild(dot);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    createGoldParticles();

    new Chart(document.getElementById('ventasChart'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Ventas (Bs)',
                data: [1200, 1900, 2300, 1800, 2800, 2100],
                backgroundColor: '#D4AF37',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
        }
    });

    new Chart(document.getElementById('categoriasChart'), {
        type: 'doughnut',
        data: {
            labels: ['Ópticos', 'Sol', 'Deportivos', 'Progresivos'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: ['#D4AF37', '#1a1a1a', '#666', '#f0d060'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('topLentesChart'), {
        type: 'bar',
        data: {
            labels: ['Ray-Ban', 'Oakley', 'Polaroid', 'Vogue', 'Carrera'],
            datasets: [{
                label: 'Unidades vendidas',
                data: [12, 9, 7, 5, 4],
                backgroundColor: '#D4AF37',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#f0f0f0' } } }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>