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
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Rechercher par nom, email, téléphone...">
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="lead">Lead</option>
                    <option value="prospect">Prospect</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Agent</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
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
