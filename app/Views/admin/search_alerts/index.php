<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Alertes de Recherche</h2>
        <p class="text-muted">Gérer les alertes de recherche des clients</p>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="alertsTable" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Transaction</th>
                    <th>Budget</th>
                    <th>Fréquence</th>
                    <th>Statut</th>
                    <th>Dernière envoi</th>
                    <th>Créée le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alerts as $alert): ?>
                <tr>
                    <td><strong>#<?= $alert['id'] ?></strong></td>
                    <td><?= esc($alert['first_name'] . ' ' . $alert['last_name']) ?></td>
                    <td><?= esc($alert['email']) ?></td>
                    <td>
                        <span class="badge bg-<?= $alert['transaction_type'] === 'sale' ? 'success' : ($alert['transaction_type'] === 'rent' ? 'primary' : 'info') ?>">
                            <?= $alert['transaction_type'] === 'sale' ? 'Vente' : ($alert['transaction_type'] === 'rent' ? 'Location' : 'Les deux') ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($alert['price_min'] || $alert['price_max']): ?>
                            <?= $alert['price_min'] ? number_format($alert['price_min'], 0) . ' TND' : '0' ?> - 
                            <?= $alert['price_max'] ? number_format($alert['price_max'], 0) . ' TND' : '∞' ?>
                        <?php else: ?>
                            <span class="text-muted">Non spécifié</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $frequencies = [
                            'instant' => '⚡ Instantanée',
                            'daily' => '📅 Quotidienne',
                            'weekly' => '📆 Hebdomadaire'
                        ];
                        echo $frequencies[$alert['frequency']] ?? $alert['frequency'];
                        ?>
                    </td>
                    <td>
                        <?php if ($alert['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($alert['last_sent_at']): ?>
                            <?= date('d/m/Y H:i', strtotime($alert['last_sent_at'])) ?>
                        <?php else: ?>
                            <span class="text-muted">Jamais</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($alert['created_at'])) ?></td>
                    <td>
                        <a href="<?= base_url('admin/search-alerts/view/' . $alert['id']) ?>" 
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= base_url('admin/search-alerts/toggleActive/' . $alert['id']) ?>" 
                           class="btn btn-sm btn-<?= $alert['is_active'] ? 'warning' : 'success' ?>">
                            <i class="bi bi-<?= $alert['is_active'] ? 'pause' : 'play' ?>"></i>
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
    $('#alertsTable').DataTable({
        order: [[8, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        pageLength: 25
    });
});
</script>
<?= $this->endSection() ?>
