<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </h1>
    <div class="text-muted">
        <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i') ?>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-gradient-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Total Biens</h6>
                    <h3 class="mb-0 fw-bold"><?= $stats['total_properties'] ?? 0 ?></h3>
                </div>
                <div style="font-size: 3rem; opacity: 0.3;">
                    <i class="fas fa-home"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top border-white-50">
                <small>
                    <i class="fas fa-check-circle"></i> 
                    <?= $stats['properties_published'] ?? 0 ?> publiés
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card bg-gradient-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Clients</h6>
                    <h3 class="mb-0 fw-bold"><?= $stats['total_clients'] ?? 0 ?></h3>
                </div>
                <div style="font-size: 3rem; opacity: 0.3;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top border-white-50">
                <small>
                    <i class="fas fa-star"></i> 
                    <?= $stats['leads_count'] ?? 0 ?> leads actifs
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card bg-gradient-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Transactions</h6>
                    <h3 class="mb-0 fw-bold"><?= $stats['total_transactions'] ?? 0 ?></h3>
                </div>
                <div style="font-size: 3rem; opacity: 0.3;">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top border-white-50">
                <small>
                    <i class="fas fa-clock"></i> 
                    <?= $stats['transactions_pending'] ?? 0 ?> en attente
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card bg-gradient-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Revenus du Mois</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($revenue_stats['monthly_revenue'] ?? 0, 0, ',', ' ') ?> TND</h3>
                </div>
                <div style="font-size: 3rem; opacity: 0.3;">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top border-white-50">
                <small>
                    <i class="fas fa-coins"></i> 
                    <?= number_format($revenue_stats['total_commission'] ?? 0, 0, ',', ' ') ?> TND commissions
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Tables -->
<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Revenus Mensuels</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Revenus</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Revenus Totaux</small>
                    <h4 class="mb-0 text-success"><?= number_format($revenue_stats['total_revenue'] ?? 0, 0, ',', ' ') ?> TND</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Commissions Totales</small>
                    <h4 class="mb-0 text-primary"><?= number_format($revenue_stats['total_commission'] ?? 0, 0, ',', ' ') ?> TND</h4>
                </div>
                <div>
                    <small class="text-muted">Taux Commission Moyen</small>
                    <h4 class="mb-0 text-info">
                        <?php 
                        $avgRate = $revenue_stats['total_revenue'] > 0 
                            ? ($revenue_stats['total_commission'] / $revenue_stats['total_revenue']) * 100 
                            : 0;
                        echo number_format($avgRate, 1);
                        ?>%
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions & Clients -->
<div class="row g-4">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Dernières Transactions</h5>
                <a href="<?= base_url('admin/transactions') ?>" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Bien</th>
                                <th>Client</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_transactions)): ?>
                                <?php foreach ($recent_transactions as $transaction): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?= esc($transaction['reference'] ?? 'N/A') ?></span></td>
                                        <td><?= esc($transaction['property_title'] ?? 'N/A') ?></td>
                                        <td><?= esc($transaction['buyer_name'] . ' ' . $transaction['buyer_lastname']) ?></td>
                                        <td class="fw-bold text-success"><?= number_format($transaction['amount'], 0, ',', ' ') ?> TND</td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabel = [
                                                'pending' => 'En attente',
                                                'completed' => 'Complétée',
                                                'cancelled' => 'Annulée'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusClass[$transaction['status']] ?? 'secondary' ?>">
                                                <?= $statusLabel[$transaction['status']] ?? $transaction['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucune transaction</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Nouveaux Clients</h5>
                <a href="<?= base_url('admin/clients') ?>" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($recent_clients)): ?>
                        <?php foreach ($recent_clients as $client): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= esc($client['first_name'] . ' ' . $client['last_name']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> <?= esc($client['phone']) ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <?php
                                        $typeLabels = [
                                            'buyer' => 'Acheteur',
                                            'seller' => 'Vendeur',
                                            'tenant' => 'Locataire',
                                            'landlord' => 'Bailleur'
                                        ];
                                        ?>
                                        <span class="badge bg-info"><?= $typeLabels[$client['type']] ?? $client['type'] ?></span><br>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($client['created_at'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php else: ?>
                        <div class="list-group-item text-center text-muted py-4">Aucun client récent</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    padding: 1.5rem;
    border-radius: 12px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($monthly_revenue['labels']) ?>,
        datasets: [{
            label: 'Revenus (TND)',
            data: <?= json_encode($monthly_revenue['data']) ?>,
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgba(102, 126, 234, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                callbacks: {
                    label: function(context) {
                        return 'Revenus: ' + context.parsed.y.toLocaleString('fr-TN') + ' TND';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('fr-TN') + ' TND';
                    }
                }
            }
        }
    }
});
</script>
                <h5 class="mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="<?= base_url('admin/properties/create') ?>" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle"></i> Nouvelle Propriété
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= base_url('admin/clients/create') ?>" class="btn btn-info w-100">
                            <i class="fas fa-user-plus"></i> Nouveau Client
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= base_url('admin/transactions/create') ?>" class="btn btn-success w-100">
                            <i class="fas fa-handshake"></i> Nouvelle Transaction
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Profil</h5>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                <h5><?= esc($user['first_name'] ?? 'Admin') ?> <?= esc($user['last_name'] ?? '') ?></h5>
                <p class="text-muted mb-0"><?= esc($user['role_display_name'] ?? 'Administrateur') ?></p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
