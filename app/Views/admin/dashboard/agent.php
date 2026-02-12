<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>


<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-user-chart me-2"></i><?= esc($title) ?></h1>
        <div>
            <span class="text-muted me-3"><i class="far fa-calendar"></i> <?= date('F Y') ?></span>
            <?php if (!empty($stats['my_objective'])): ?>
            <a href="/admin/objectives/edit/<?= $stats['my_objective']['id'] ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-bullseye"></i> Mon Objectif
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- My Personal Objective -->
    <?php if (!empty($stats['my_objective']) && !empty($stats['objective_progress'])): ?>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Mon objectif personnel - <?= date('F Y') ?></h5>
                </div>
                <div class="card-body">
                    <?php
                    $obj = $stats['my_objective'];
                    $progress = $stats['objective_progress'];
                    $overallProgress = $progress['overall'];
                    ?>

                    <!-- Overall Progress -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Progression globale</strong>
                            <strong class="text-primary"><?= $overallProgress ?>%</strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar <?= $overallProgress >= 100 ? 'bg-success' : 'bg-primary' ?>" 
                                 style="width: <?= min($overallProgress, 100) ?>%">
                                <strong><?= $overallProgress ?>%</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="row g-3">
                        <?php if ($obj['revenue_target'] > 0): ?>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-coins text-warning me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <small class="text-muted d-block">Chiffre d'affaires</small>
                                        <h6 class="mb-0"><?= number_format($obj['revenue_achieved'], 0) ?> / <?= number_format($obj['revenue_target'], 0) ?> DT</h6>
                                    </div>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-warning" style="width: <?= min($progress['revenue'], 100) ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $progress['revenue'] ?>%</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($obj['new_contacts_target'] > 0): ?>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-info me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <small class="text-muted d-block">Nouveaux contacts</small>
                                        <h6 class="mb-0"><?= $obj['new_contacts_achieved'] ?> / <?= $obj['new_contacts_target'] ?></h6>
                                    </div>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-info" style="width: <?= min($progress['contacts'], 100) ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $progress['contacts'] ?>%</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($obj['transactions_target'] > 0): ?>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-handshake text-success me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <small class="text-muted d-block">Transactions</small>
                                        <h6 class="mb-0"><?= $obj['transactions_achieved'] ?> / <?= $obj['transactions_target'] ?></h6>
                                    </div>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: <?= min($progress['transactions'], 100) ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $progress['transactions'] ?>%</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($obj['properties_rent_target'] > 0): ?>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-key text-primary me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <small class="text-muted d-block">Biens en location</small>
                                        <h6 class="mb-0"><?= $obj['properties_rent_achieved'] ?> / <?= $obj['properties_rent_target'] ?></h6>
                                    </div>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" style="width: <?= min($progress['rent'], 100) ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $progress['rent'] ?>%</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($obj['properties_sale_target'] > 0): ?>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-home text-danger me-2" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <small class="text-muted d-block">Biens en vente</small>
                                        <h6 class="mb-0"><?= $obj['properties_sale_achieved'] ?> / <?= $obj['properties_sale_target'] ?></h6>
                                    </div>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-danger" style="width: <?= min($progress['sale'], 100) ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $progress['sale'] ?>%</small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Mes clients</p>
                            <h3 class="mb-0"><?= number_format($stats['my_clients']) ?></h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> <?= number_format($stats['clients_month']) ?> ce mois
                            </small>
                        </div>
                        <i class="fas fa-users text-primary" style="font-size: 2.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Mes biens</p>
                            <h3 class="mb-0"><?= number_format($stats['my_properties']) ?></h3>
                            <small class="text-success">
                                <?= number_format($stats['properties_available']) ?> disponibles
                            </small>
                        </div>
                        <i class="fas fa-home text-info" style="font-size: 2.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Transactions</p>
                            <h3 class="mb-0"><?= number_format($stats['my_transactions']) ?></h3>
                            <small class="text-muted">Complétées</small>
                        </div>
                        <i class="fas fa-handshake text-success" style="font-size: 2.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Mon CA</p>
                            <h3 class="mb-0"><?= number_format($stats['my_revenue'], 0) ?> <small>DT</small></h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> <?= number_format($stats['revenue_month'], 0) ?> DT ce mois
                            </small>
                        </div>
                        <i class="fas fa-coins text-warning" style="font-size: 2.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list text-primary mb-2" style="font-size: 2rem;"></i>
                    <h4 class="mb-1"><?= number_format($stats['my_requests']) ?></h4>
                    <small class="text-muted">Mes demandes</small>
                    <hr class="my-2">
                    <small>
                        <span class="badge bg-warning"><?= $stats['requests_pending'] ?> en attente</span>
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calculator text-info mb-2" style="font-size: 2rem;"></i>
                    <h4 class="mb-1"><?= number_format($stats['my_estimations']) ?></h4>
                    <small class="text-muted">Mes estimations</small>
                    <hr class="my-2">
                    <small>
                        <span class="badge bg-warning"><?= $stats['estimations_pending'] ?> en attente</span>
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check text-success mb-2" style="font-size: 2rem;"></i>
                    <h4 class="mb-1"><?= number_format($stats['upcoming_appointments']) ?></h4>
                    <small class="text-muted">Rendez-vous à venir</small>
                    <hr class="my-2">
                    <small>
                        <a href="/admin/appointments" class="text-decoration-none">Voir l'agenda</a>
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-tasks text-warning mb-2" style="font-size: 2rem;"></i>
                    <h4 class="mb-1"><?= number_format($stats['my_tasks']) ?></h4>
                    <small class="text-muted">Tâches en cours</small>
                    <hr class="my-2">
                    <small>
                        <a href="/admin/tasks" class="text-decoration-none">Gérer les tâches</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Commissions -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Mes commissions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center">
                            <h3 class="text-success mb-0"><?= number_format($stats['commissions_paid'], 0) ?> <small>DT</small></h3>
                            <small class="text-muted">Payées</small>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class="text-warning mb-0"><?= number_format($stats['commissions_pending'], 0) ?> <small>DT</small></h3>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Mon évolution</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Mes transactions récentes</h5>
                    <a href="/admin/transactions?agent_id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-secondary">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Bien</th>
                                    <th>Client</th>
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
                                        <td class="text-end fw-bold">
                                            <?= number_format($transaction['amount'], 0) ?>
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
                                        <td colspan="5" class="text-center text-muted py-4">
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
        return new Date(year, month - 1).toLocaleDateString('fr-FR', { month: 'short' });
    });
    const revenues = monthlyData.map(item => parseFloat(item.revenue));

    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'CA mensuel (DT)',
                data: revenues,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
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
