<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-dollar-sign text-success"></i>
            <?= esc($page_title) ?>
        </h1>
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Commissions</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-coins fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Total Mois</p>
                        <h3 class="mb-0"><?= number_format($stats['total'], 0, ',', ' ') ?> TND</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">En attente</p>
                        <h3 class="mb-0"><?= number_format($stats['by_status']['pending']['amount'], 0, ',', ' ') ?> TND</h3>
                        <small class="text-muted"><?= $stats['by_status']['pending']['count'] ?> commission(s)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Approuvées</p>
                        <h3 class="mb-0"><?= number_format($stats['by_status']['approved']['amount'], 0, ',', ' ') ?> TND</h3>
                        <small class="text-muted"><?= $stats['by_status']['approved']['count'] ?> commission(s)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 small">Payées</p>
                        <h3 class="mb-0"><?= number_format($stats['by_status']['paid']['amount'], 0, ',', ' ') ?> TND</h3>
                        <small class="text-muted"><?= $stats['by_status']['paid']['count'] ?> commission(s)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/commissions') ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Mois</label>
                <input type="month" name="month" class="form-control" value="<?= $selectedMonth ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Agent</label>
                <select name="agent_id" class="form-select">
                    <option value="">Tous les agents</option>
                    <?php foreach($agents as $agent): ?>
                        <option value="<?= $agent['id'] ?>" <?= $selectedAgent == $agent['id'] ? 'selected' : '' ?>>
                            <?= esc(($agent['first_name'] ?? '') . ' ' . ($agent['last_name'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>En attente</option>
                    <option value="approved" <?= $selectedStatus === 'approved' ? 'selected' : '' ?>>Approuvée</option>
                    <option value="paid" <?= $selectedStatus === 'paid' ? 'selected' : '' ?>>Payée</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Commissions Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des Commissions</h5>
        <div>
            <button class="btn btn-sm btn-success" onclick="bulkAction('approve')">
                <i class="fas fa-check"></i> Approuver sélection
            </button>
            <button class="btn btn-sm btn-primary" onclick="bulkAction('pay')">
                <i class="fas fa-money-bill"></i> Payer sélection
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Transaction</th>
                        <th>Propriété</th>
                        <th>Agent</th>
                        <th>Type</th>
                        <th>Pourcentage</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commissions)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Aucune commission pour cette période
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($commissions as $commission): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="commission-checkbox" value="<?= $commission['id'] ?>">
                                </td>
                                <td>
                                    <strong><?= esc($commission['transaction_ref']) ?></strong><br>
                                    <small class="text-muted"><?= number_format($commission['transaction_amount'], 0, ',', ' ') ?> TND</small>
                                </td>
                                <td>
                                    <?= esc($commission['property_ref']) ?><br>
                                    <small class="text-muted"><?= esc($commission['property_title']) ?></small>
                                </td>
                                <td><?= esc($commission['agent_name']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= esc(ucfirst($commission['type'])) ?></span>
                                </td>
                                <td><?= $commission['percentage'] ?>%</td>
                                <td><strong><?= number_format($commission['amount'], 0, ',', ' ') ?> TND</strong></td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'paid' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'En attente',
                                        'approved' => 'Approuvée',
                                        'paid' => 'Payée',
                                        'cancelled' => 'Annulée'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusColors[$commission['status']] ?>">
                                        <?= $statusLabels[$commission['status']] ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($commission['created_at'])) ?>
                                </td>
                                <td>
                                    <?php if ($commission['status'] === 'pending'): ?>
                                        <a href="<?= base_url('admin/commissions/approve/' . $commission['id']) ?>" 
                                           class="btn btn-sm btn-success" title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php elseif ($commission['status'] === 'approved'): ?>
                                        <a href="<?= base_url('admin/commissions/mark-as-paid/' . $commission['id']) ?>" 
                                           class="btn btn-sm btn-primary" title="Marquer payée">
                                            <i class="fas fa-money-bill"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top Agents (if no specific agent selected) -->
<?php if (!$selectedAgent && !empty($stats['top_agents'])): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-trophy text-warning"></i> Top 5 Agents du Mois</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Agent</th>
                        <th>Transactions</th>
                        <th>Total Commissions</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats['top_agents'] as $index => $agent): ?>
                        <tr>
                            <td>
                                <?php if ($index === 0): ?>
                                    <i class="fas fa-trophy text-warning"></i>
                                <?php else: ?>
                                    <?= $index + 1 ?>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($agent['agent_name']) ?></td>
                            <td><?= $agent['transaction_count'] ?></td>
                            <td><strong><?= number_format($agent['total_commission'], 0, ',', ' ') ?> TND</strong></td>
                            <td>
                                <a href="<?= base_url('admin/commissions/agent-report/' . $agent['user_id']) ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-chart-line"></i> Rapport
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.commission-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Bulk actions
function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.commission-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Veuillez sélectionner au moins une commission');
        return;
    }
    
    const actionText = action === 'approve' ? 'approuver' : 'payer';
    if (!confirm(`Voulez-vous vraiment ${actionText} ${ids.length} commission(s) ?`)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= base_url('admin/commissions/bulk-') ?>' + action;
    
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'commission_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}
</script>
<?= $this->endSection() ?>
