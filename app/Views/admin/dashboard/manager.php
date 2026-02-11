<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-chart-area me-2"></i><?= esc($title) ?></h1>
        <div>
            <span class="text-muted me-3"><i class="far fa-calendar"></i> <?= date('F Y') ?></span>
            <a href="/admin/objectives?agency_id=<?= $user['agency_id'] ?>" class="btn btn-sm btn-outline-primary">
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
                            <p class="text-muted mb-1 small">Clients</p>
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
                            <p class="text-muted mb-1 small">Biens</p>
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
                    <small class="text-muted">Complétées</small>
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

    <!-- Agency Objective -->
    <?php if (!empty($stats['agency_objective'])): ?>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Objectif de l'agence - <?= date('F Y') ?></h5>
                </div>
                <div class="card-body">
                    <?php
                    $obj = $stats['agency_objective'];
                    $progress = [];
                    if ($obj['revenue_target'] > 0) {
                        $progress['revenue'] = round(($obj['revenue_achieved'] / $obj['revenue_target']) * 100, 1);
                    }
                    if ($obj['new_contacts_target'] > 0) {
                        $progress['contacts'] = round(($obj['new_contacts_achieved'] / $obj['new_contacts_target']) * 100, 1);
                    }
                    if ($obj['transactions_target'] > 0) {
                        $progress['transactions'] = round(($obj['transactions_achieved'] / $obj['transactions_target']) * 100, 1);
                    }
                    $overallProgress = !empty($progress) ? round(array_sum($progress) / count($progress), 1) : 0;
                    ?>

                    <!-- Overall Progress -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Progression globale</strong>
                            <strong class="text-primary"><?= $overallProgress ?>%</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar <?= $overallProgress >= 100 ? 'bg-success' : 'bg-primary' ?>" 
                                 style="width: <?= min($overallProgress, 100) ?>%">
                                <?= $overallProgress ?>%
                            </div>
                        </div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="row g-3">
                        <?php if ($obj['revenue_target'] > 0): ?>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Chiffre d'affaires</small>
                                <h6 class="mb-2"><?= number_format($obj['revenue_achieved'], 0) ?> / <?= number_format($obj['revenue_target'], 0) ?> DT</h6>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: <?= min($progress['revenue'], 100) ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $progress['revenue'] ?>%</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($obj['new_contacts_target'] > 0): ?>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Nouveaux contacts</small>
                                <h6 class="mb-2"><?= $obj['new_contacts_achieved'] ?> / <?= $obj['new_contacts_target'] ?></h6>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: <?= min($progress['contacts'], 100) ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $progress['contacts'] ?>%</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($obj['transactions_target'] > 0): ?>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Transactions</small>
                                <h6 class="mb-2"><?= $obj['transactions_achieved'] ?> / <?= $obj['transactions_target'] ?></h6>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?= min($progress['transactions'], 100) ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $progress['transactions'] ?>%</small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Charts and Stats -->
    <div class="row g-3 mb-4">
        <!-- Monthly Revenue -->
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

        <!-- Team Stats -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Équipe</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="mb-0"><?= number_format($stats['active_agents']) ?></h2>
                        <small class="text-muted">Agents actifs</small>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Demandes en attente</small>
                        <span class="badge bg-warning"><?= number_format($stats['pending_requests']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agents Performance -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Performance des agents</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Agent</th>
                                    <th class="text-end">Deals</th>
                                    <th class="text-end">Revenue (DT)</th>
                                    <th class="text-center">Progression</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stats['agents_performance'])): ?>
                                    <?php foreach ($stats['agents_performance'] as $agent): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-user text-muted me-2"></i>
                                            <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-primary"><?= number_format($agent['deals']) ?></span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= number_format($agent['revenue'], 0) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($agent['deals'] > 0): ?>
                                                <div class="progress" style="height: 8px; width: 100px; margin: 0 auto;">
                                                    <?php $percent = min(($agent['deals'] / 10) * 100, 100); ?>
                                                    <div class="progress-bar bg-success" style="width: <?= $percent ?>%"></div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
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
</script>
<?= $this->endSection() ?>
