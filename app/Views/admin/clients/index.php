<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-users"></i> Gestion des Clients
    </h1>
    <a href="<?= base_url('admin/clients/create') ?>" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nouveau Client
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
            <table class="table table-hover mb-0" id="clientsTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Agent</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="no-filter">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><strong>#<?= $client['id'] ?></strong></td>
                                <td>
                                    <?php if ($client['type'] === 'company'): ?>
                                        <i class="fas fa-building text-primary"></i> <?= esc($client['company_name']) ?>
                                    <?php else: ?>
                                        <i class="fas fa-user"></i> <?= esc($client['first_name'] . ' ' . $client['last_name']) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($client['email'] ?? '-') ?></td>
                                <td><?= esc($client['phone']) ?></td>
                                <td><?= esc($client['agent_name'] . ' ' . ($client['agent_lastname'] ?? '')) ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match($client['status']) {
                                        'active' => 'success',
                                        'prospect' => 'info',
                                        'lead' => 'warning',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($client['status']) ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($client['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/clients/view/' . $client['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('admin/clients/edit/' . $client['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Aucun client trouvé
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
    initDataTableWithFilters('clientsTable', {
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 7 }
        ]
    });
});
</script>
<?= $this->endSection() ?>
