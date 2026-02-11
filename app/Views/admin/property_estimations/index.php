<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .status-badge {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-in_progress { background-color: #cfe2ff; color: #084298; }
    .status-estimated { background-color: #d1e7dd; color: #0f5132; }
    .status-contacted { background-color: #e2e3e5; color: #41464b; }
    .status-converted { background-color: #d1e7dd; color: #0a3622; }
    .status-cancelled { background-color: #f8d7da; color: #842029; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Demandes d'Estimation</h2>
        <p class="text-muted">Gérer les demandes d'estimation de biens immobiliers</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">En attente</div>
                <h3 class="mb-0 text-warning"><?= count(array_filter($estimations, fn($e) => $e['status'] === 'pending')) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">En cours</div>
                <h3 class="mb-0 text-primary"><?= count(array_filter($estimations, fn($e) => $e['status'] === 'in_progress')) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">Estimés</div>
                <h3 class="mb-0 text-success"><?= count(array_filter($estimations, fn($e) => $e['status'] === 'estimated')) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">Contactés</div>
                <h3 class="mb-0 text-info"><?= count(array_filter($estimations, fn($e) => $e['status'] === 'contacted')) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">Convertis</div>
                <h3 class="mb-0 text-success"><?= count(array_filter($estimations, fn($e) => $e['status'] === 'converted')) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted small">Total</div>
                <h3 class="mb-0"><?= count($estimations) ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="estimationsTable" class="table table-hover">
            <thead>
                <tr>
                    <th>Réf</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Type bien</th>
                    <th>Transaction</th>
                    <th>Localisation</th>
                    <th>Agent</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estimations as $estimation): ?>
                <tr>
                    <td><strong>#<?= $estimation['id'] ?></strong></td>
                    <td>
                        <?= esc($estimation['first_name'] . ' ' . $estimation['last_name']) ?>
                    </td>
                    <td><?= esc($estimation['email']) ?></td>
                    <td><?= esc($estimation['phone']) ?></td>
                    <td>
                        <span class="badge bg-secondary">
                            <?php
                            $types = [
                                'apartment' => 'Appartement',
                                'villa' => 'Villa',
                                'studio' => 'Studio',
                                'office' => 'Bureau',
                                'shop' => 'Commerce',
                                'warehouse' => 'Entrepôt',
                                'land' => 'Terrain',
                                'other' => 'Autre'
                            ];
                            echo $types[$estimation['property_type']] ?? $estimation['property_type'];
                            ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= $estimation['transaction_type'] === 'sale' ? 'success' : 'primary' ?>">
                            <?= $estimation['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?>
                        </span>
                    </td>
                    <td>
                        <?= esc($estimation['city'] ?? '-') ?><br>
                        <small class="text-muted"><?= esc($estimation['zone_name'] ?? '') ?></small>
                    </td>
                    <td>
                        <?php if ($estimation['agent_first_name']): ?>
                            <?= esc($estimation['agent_first_name'] . ' ' . $estimation['agent_last_name']) ?>
                        <?php else: ?>
                            <span class="text-muted">Non assigné</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $estimation['status'] ?>">
                            <?php
                            $statuses = [
                                'pending' => 'En attente',
                                'in_progress' => 'En cours',
                                'estimated' => 'Estimé',
                                'contacted' => 'Contacté',
                                'converted' => 'Converti',
                                'cancelled' => 'Annulé'
                            ];
                            echo $statuses[$estimation['status']] ?? $estimation['status'];
                            ?>
                        </span>
                    </td>
                    <td>
                        <?= date('d/m/Y', strtotime($estimation['created_at'])) ?><br>
                        <small class="text-muted"><?= date('H:i', strtotime($estimation['created_at'])) ?></small>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/property-requests/view/' . \$estimation['id']) ?>"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i> Voir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#estimationsTable').DataTable({
        order: [[9, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        pageLength: 25
    });
});
</script>
<?= $this->endSection() ?>
