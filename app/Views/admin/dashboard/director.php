<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-chart-pie me-2"></i><?= esc($title) ?></h1>
        <div>
            <span class="text-muted me-3"><i class="far fa-calendar"></i> <?= date('F Y') ?></span>
            <a href="/admin/objectives" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-bullseye"></i> Objectifs
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Clients total</p>
                            <h3 class="mb-0"><?= number_format($stats['total_clients']) ?></h3>
                        </div>
                        <div class="text-primary" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i> <?= number_format($stats['clients_month']) ?> ce mois
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Biens immobiliers</p>
                            <h3 class="mb-0"><?= number_format($stats['total_properties']) ?></h3>
                        </div>
                        <div class="text-info" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                    <small class="text-success">
                        <?= number_format($stats['properties_available']) ?> disponibles
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Transactions</p>
                            <h3 class="mb-0"><?= number_format($stats['total_transactions']) ?></h3>
                        </div>
                        <div class="text-success" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-handshake"></i>
                        </div>
                    </div>
                    <small class="text-muted">Total complétées</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">CA Total</p>
                            <h3 class="mb-0"><?= number_format($stats['total_revenue'], 0) ?> <small>DT</small></h3>
                        </div>
                        <div class="text-warning" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i> <?= number_format($stats['revenue_month'], 0) ?> DT ce mois
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Monthly Revenue Chart -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Évolution du CA (12 derniers mois)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Properties by Type Chart -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Biens par type</h5>
                </div>
                <div class="card-body">
                    <canvas id="propertyTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Agency -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Performance par agence</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Agence</th>
                                    <th class="text-end">Transactions</th>
                                    <th class="text-end">Revenue (DT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stats['revenue_by_agency'])): ?>
                                    <?php foreach ($stats['revenue_by_agency'] as $agency): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-building text-muted me-2"></i>
                                            <?= esc($agency['name'] ?? 'N/A') ?>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-primary"><?= number_format($agency['transactions']) ?></span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= number_format($agency['revenue'], 0) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Aucune donnée disponible
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Agents -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 agents</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Agent</th>
                                    <th class="text-end">Deals</th>
                                    <th class="text-end">Revenue (DT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stats['top_agents'])): ?>
                                    <?php foreach ($stats['top_agents'] as $index => $agent): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index < 3): ?>
                                                <i class="fas fa-medal text-<?= $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'bronze') ?> me-2"></i>
                                            <?php else: ?>
                                                <i class="fas fa-user text-muted me-2"></i>
                                            <?php endif; ?>
                                            <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-success"><?= number_format($agent['deals']) ?></span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= number_format($agent['revenue'], 0) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Aucune donnée disponible
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Transactions récentes</h5>
                    <a href="/admin/transactions" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Bien</th>
                                    <th>Client</th>
                                    <th>Agent</th>
                                    <th class="text-end">Montant (DT)</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stats['recent_transactions'])): ?>
                                    <?php foreach ($stats['recent_transactions'] as $transaction): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($transaction['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td><?= esc($transaction['property_title'] ?? 'N/A') ?></td>
                                        <td><?= esc($transaction['first_name'] . ' ' . $transaction['last_name']) ?></td>
                                        <td>
                                            <small><?= esc($transaction['agent_first_name'] . ' ' . $transaction['agent_last_name']) ?></small>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= number_format($transaction['total_amount'], 0) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = 'secondary';
                                            if ($transaction['status'] == 'completed') $statusClass = 'success';
                                            elseif ($transaction['status'] == 'pending') $statusClass = 'warning';
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>"><?= esc($transaction['status']) ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Aucune transaction récente
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx) {
    const monthlyData = <?= json_encode($stats['monthly_revenue']) ?>;
    const labels = monthlyData.map(item => {
        const [year, month] = item.month.split('-');
        return new Date(year, month - 1).toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
    });
    const revenues = monthlyData.map(item => parseFloat(item.revenue));

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Chiffre d\'affaires (DT)',
                data: revenues,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' DT';
                        }
                    }
                }
            }
        }
    });
}

// Property Type Chart
const propertyTypeCtx = document.getElementById('propertyTypeChart');
if (propertyTypeCtx) {
    const propertyData = <?= json_encode($stats['properties_by_type']) ?>;
    const labels = propertyData.map(item => item.property_type || 'N/A');
    const counts = propertyData.map(item => parseInt(item.count));

    new Chart(propertyTypeCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
</script>
<?= $this->endSection() ?>
