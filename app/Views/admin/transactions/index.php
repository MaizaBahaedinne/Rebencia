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
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Rechercher...">
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">Tous les types</option>
                    <option value="sale">Vente</option>
                    <option value="rent">Location</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="draft">Brouillon</option>
                    <option value="pending">En attente</option>
                    <option value="signed">Signé</option>
                    <option value="completed">Complété</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                        <th>Actions</th>
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
                                    <a href="<?= base_url('admin/transactions/edit/' . $transaction['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
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
