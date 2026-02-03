<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-tie"></i> Gestion des Utilisateurs
    </h1>
    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nouvel Utilisateur
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
                <input type="text" class="form-control" placeholder="Rechercher...">
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">Tous les rôles</option>
                    <option>Admin</option>
                    <option>Agent</option>
                    <option>Coordinateur</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">Toutes les agences</option>
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
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Agence</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><strong>#<?= $user['id'] ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-2x text-muted me-2"></i>
                                        <div>
                                            <strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong><br>
                                            <small class="text-muted">@<?= esc($user['username']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['phone'] ?? '-') ?></td>
                                <td><span class="badge bg-primary"><?= esc($user['role_name']) ?></span></td>
                                <td><?= esc($user['agency_name'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id'] != session()->get('user_id')): ?>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $user['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Aucun utilisateur trouvé
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
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        fetch(`<?= base_url('admin/users/delete/') ?>${id}`, {
            method: 'DELETE'
        }).then(() => location.reload());
    }
}
</script>
<?= $this->endSection() ?>
