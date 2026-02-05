<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-invoice-dollar"></i> Gestion des Transactions
    </h1>
    <a href="<?= base_url('admin/transactions/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle Transaction
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="transactionsTable">
                <thead class="table-light">
                    <tr>
                        <th>Référence</th>
                        <th>Propriété</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Commission</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="no-filter">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transactions)): ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><strong><?= esc($transaction['reference']) ?></strong></td>
                                <td><?= esc($transaction['property_title']) ?></td>
                                <td><?= esc($transaction['client_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $transaction['type'] === 'sale' ? 'success' : 'info' ?>">
                                        <?= $transaction['type'] === 'sale' ? 'Vente' : 'Location' ?>
                                    </span>
                                </td>
                                <td><strong><?= number_format($transaction['amount'], 0, ',', ' ') ?> TND</strong></td>
                                <td><?= number_format($transaction['commission_amount'], 0, ',', ' ') ?> TND</td>
                                <td>
                                    <?php
                                    $badgeClass = match($transaction['status']) {
                                        'completed' => 'success',
                                        'signed' => 'primary',
                                        'pending' => 'warning',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($transaction['status']) ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($transaction['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/transactions/view-commission/' . $transaction['id']) ?>" 
                                           class="btn btn-sm btn-info" title="Voir commission">
                                            <i class="fas fa-dollar-sign"></i>
                                        </a>
                                        <a href="<?= base_url('admin/transactions/edit/' . $transaction['id']) ?>" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (canDelete('transactions')): ?>
                                        <button onclick="confirmDelete(<?= $transaction['id'] ?>)" 
                                                class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Aucune transaction trouvée
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/datatable-filters.js') ?>"></script>
<script>
$(document).ready(function() {
    initDataTableWithFilters('transactionsTable', {
        order: [[7, 'desc']],
        columnDefs: [
            { orderable: false, targets: 8 }
        ]
    });
});

function confirmDelete(transactionId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette transaction ? Les commissions associées seront également supprimées.')) {
        fetch(`<?= base_url('admin/transactions/delete/') ?>${transactionId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    }
}
</script>
<?= $this->endSection() ?>
