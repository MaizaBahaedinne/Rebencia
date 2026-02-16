<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ca-realtime.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-chart-pie me-2"></i><?= esc($title) ?></h1>
        <div>
            <span class="text-muted me-3"><i class="far fa-calendar"></i> <?= date('F Y') ?></span>
            <button id="sync-objectives-btn" class="btn btn-sm btn-outline-secondary me-2" title="Synchroniser les objectifs">
                <i class="fas fa-sync-alt"></i> Sync
            </button>
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
                            <p class="text-muted mb-1 small">CA Réalisé (HT)</p>
                            <h3 class="mb-0 text-primary"><?= number_format($stats['total_revenue'], 0) ?> <small>DT</small></h3>
                        </div>
                        <div class="text-warning" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-sync-alt fa-spin me-1"></i> Mise à jour en temps réel
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Month KPI Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Biens à Louer (mois)</p>
                            <h3 class="mb-0 text-info"><?= number_format($stats['properties_rent_month'] ?? 0) ?></h3>
                        </div>
                        <div class="text-info" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div>
                    <small class="text-muted">Publiés ce mois</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Biens à Vendre (mois)</p>
                            <h3 class="mb-0 text-success"><?= number_format($stats['properties_sale_month'] ?? 0) ?></h3>
                        </div>
                        <div class="text-success" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-gavel"></i>
                        </div>
                    </div>
                    <small class="text-muted">Publiés ce mois</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted mb-1 small">Objectifs Actifs</p>
                            <h3 class="mb-0 text-warning"><?= number_format($stats['active_objectives'] ?? 0) ?></h3>
                        </div>
                        <div class="text-warning" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-bullseye"></i>
                        </div>
                    </div>
                    <small class="text-muted">Période <?= date('m/Y') ?></small>
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
            </div>
        </div>
    </div>

    <!-- CA Realtime Widget -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>CA vs Objectif (Temps Réel)</h5>
                </div>
                <div data-ca-widget></div>
            </div>
        </div>

        <!-- Performance Stats -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Performance par Agent</h5>
                </div>
                <div class="card-body p-0">
                    <div id="agents-ca-list" class="table-responsive">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
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

// Load CA by agents
async function loadAgentsCA() {
    try {
        const response = await fetch('/api/ca/by-agent');
        const result = await response.json();
        
        if (result.status === 'success' && result.data) {
            renderAgentsCAList(result.data);
        }
    } catch (error) {
        console.error('Erreur CA agents:', error);
        document.getElementById('agents-ca-list').innerHTML = 
            '<div class="alert alert-warning m-3">Erreur de chargement</div>';
    }
}

function renderAgentsCAList(agents) {
    if (agents.length === 0) {
        document.getElementById('agents-ca-list').innerHTML = 
            '<div class="text-center text-muted py-4"><small>Aucun CA pour le mois</small></div>';
        return;
    }

    const tableHtml = `
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Agent</th>
                    <th class="text-end">Deals</th>
                    <th class="text-end">CA (HT)</th>
                </tr>
            </thead>
            <tbody>
                ${agents.map((agent, idx) => `
                <tr>
                    <td>
                        ${idx < 3 ? `<i class="fas fa-medal text-${idx === 0 ? 'warning' : (idx === 1 ? 'secondary' : 'bronze')} me-2"></i>` : '<i class="fas fa-user text-muted me-2"></i>'}
                        <strong>${esc(agent.first_name + ' ' + agent.last_name)}</strong>
                        ${agent.agency_name ? `<br><small class="text-muted">${esc(agent.agency_name)}</small>` : ''}
                    </td>
                    <td class="text-end">
                        <span class="badge bg-primary">${agent.deals_count || 0}</span>
                    </td>
                    <td class="text-end fw-bold text-primary">
                        ${new Intl.NumberFormat('fr-TN', {style: 'currency', currency: 'TND', minimumFractionDigits: 0}).format(agent.ca_ht || 0)}
                    </td>
                </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    document.getElementById('agents-ca-list').innerHTML = tableHtml;
}

// Charger les données CA
loadAgentsCA();

// Rafraîchir toutes les 30 secondes
setInterval(loadAgentsCA, 30000);

// Sync objectives button
document.getElementById('sync-objectives-btn')?.addEventListener('click', async function() {
    const btn = this;
    const originalHTML = btn.innerHTML;
    
    try {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sync...';
        
        const response = await fetch('/api/ca/sync-objectives', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                period: new Date().toISOString().slice(0, 7)
            })
        });
        
        const result = await response.json();
        if (result.status === 'success') {
            btn.innerHTML = '<i class="fas fa-check text-success"></i> Synchronisé';
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
                // Recharger les données
                loadAgentsCA();
            }, 2000);
        } else {
            throw new Error(result.message || 'Erreur');
        }
    } catch (error) {
        console.error('Sync error:', error);
        btn.innerHTML = '<i class="fas fa-times text-danger"></i> Erreur';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }, 3000);
    }
});
</script>

<script src="<?= base_url('assets/js/ca-realtime.js') ?>"></script>

<?= $this->endSection() ?>
