<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-contract"></i> Détails de la Transaction
    </h1>
    <div class="btn-group" role="group">
        <a href="<?= base_url('admin/transactions') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <a href="<?= base_url('admin/transactions/edit/' . $transaction['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <?php if (canDelete('transactions')): ?>
        <button onclick="confirmDelete(<?= $transaction['id'] ?>)" class="btn btn-danger">
            <i class="fas fa-trash"></i> Supprimer
        </button>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Property Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-building"></i> Bien Immobilier
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Référence</label>
                        <p class="mb-0"><strong><?= esc($transaction['property_reference']) ?></strong></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Titre</label>
                        <p class="mb-0"><?= esc($transaction['property_title']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice"></i> Détails de la Transaction
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Type</label>
                        <p class="mb-0">
                            <span class="badge bg-<?= $transaction['type'] === 'sale' ? 'success' : 'info' ?>">
                                <?= $transaction['type'] === 'sale' ? 'Vente' : 'Location' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Montant</label>
                        <p class="mb-0"><strong><?= number_format($transaction['amount'], 2, ',', ' ') ?> TND</strong></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Date de Transaction</label>
                        <p class="mb-0"><?= date('d/m/Y', strtotime($transaction['transaction_date'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Statut</label>
                        <p class="mb-0">
                            <?php
                            $badgeClass = match($transaction['status']) {
                                'completed' => 'success',
                                'signed' => 'primary',
                                'pending' => 'warning',
                                default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($transaction['status']) ?></span>
                        </p>
                    </div>
                    <?php if ($transaction['contract_number']): ?>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Numéro de Contrat</label>
                        <p class="mb-0"><?= esc($transaction['contract_number']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($transaction['notary']): ?>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Notaire</label>
                        <p class="mb-0"><?= esc($transaction['notary']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($transaction['notes']): ?>
                    <div class="col-12">
                        <label class="form-label text-muted">Notes</label>
                        <p class="mb-0"><?= esc($transaction['notes']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Parties Information -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users"></i> Parties
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">
                            <i class="fas fa-user-check"></i> Client
                        </label>
                        <p class="mb-0">
                            <strong><?= esc($transaction['client_first_name'] . ' ' . $transaction['client_last_name']) ?></strong>
                        </p>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-phone"></i> <?= esc($transaction['client_phone']) ?>
                        </p>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-envelope"></i> <?= esc($transaction['client_email']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">
                            <i class="fas fa-user-tie"></i> Agent Responsable
                        </label>
                        <p class="mb-0">
                            <strong><?= esc($transaction['agent_first_name'] . ' ' . $transaction['agent_last_name']) ?></strong>
                        </p>
                        <p class="text-muted small mb-0">
                            Agence: <?= esc($transaction['agency_name'] ?? '-') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Details -->
        <?php if (!empty($commissions)): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-dollar-sign"></i> Détails des Commissions
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th class="text-end">Montant HT</th>
                                <th class="text-end">TVA</th>
                                <th class="text-end">Total TTC</th>
                                <th>Payée</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commissions as $commission): ?>
                            <tr>
                                <td><?= esc($commission['commission_type'] ?? 'Commission') ?></td>
                                <td class="text-end"><strong><?= number_format($commission['commission_ht'], 2, ',', ' ') ?> TND</strong></td>
                                <td class="text-end"><?= number_format($commission['commission_vat'], 2, ',', ' ') ?> TND</td>
                                <td class="text-end"><strong><?= number_format($commission['commission_ttc'], 2, ',', ' ') ?> TND</strong></td>
                                <td>
                                    <span class="badge bg-<?= $commission['is_paid'] ? 'success' : 'warning' ?>">
                                        <?= $commission['is_paid'] ? 'Payée' : 'Impayée' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Summary Card -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list"></i> Résumé
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span>Montant:</span>
                    <strong><?= number_format($transaction['amount'], 2, ',', ' ') ?> TND</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span>Commission HT:</span>
                    <strong><?= number_format($transaction['commission_amount'] ?? 0, 2, ',', ' ') ?> TND</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span>Pourcentage:</span>
                    <strong><?= number_format($transaction['commission_percentage'] ?? 0, 2, ',', ' ') ?> %</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Payée:</span>
                    <span class="badge bg-<?= ($transaction['commission_paid'] ?? 0) ? 'success' : 'warning' ?>">
                        <?= ($transaction['commission_paid'] ?? 0) ? 'Oui' : 'Non' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Informations
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Créée le</label>
                    <p class="mb-0 small">
                        <?= date('d/m/Y à H:i', strtotime($transaction['created_at'])) ?>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Modifiée le</label>
                    <p class="mb-0 small">
                        <?= date('d/m/Y à H:i', strtotime($transaction['updated_at'])) ?>
                    </p>
                </div>
                <div>
                    <label class="form-label text-muted small">ID Transaction</label>
                    <p class="mb-0 small">
                        <code><?= $transaction['id'] ?></code>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette transaction?')) {
        window.location.href = '<?= base_url('admin/transactions/delete') ?>/' + id;
    }
}
</script>
<?= $this->endSection() ?>
