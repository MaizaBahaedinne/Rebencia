<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Fiche utilisateur</h2>
        <small class="text-muted">Détails du compte</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/users/' . $user['id'] . '/edit') ?>" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Profil -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body p-4">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= site_url('uploads/avatars/' . $user['avatar']) ?>" class="rounded-circle mb-3" width="100" height="100" style="object-fit:cover">
                <?php else: ?>
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px;font-size:2.5rem;">
                        <?= strtoupper(mb_substr($user['first_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <h5 class="mb-0"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                <small class="text-muted"><?= esc($user['email']) ?></small>
                <div class="mt-2">
                    <span class="badge bg-info"><?= esc($user['role_name'] ?? 'N/A') ?></span>
                    <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?> ms-1">
                        <?= $user['status'] === 'active' ? 'Actif' : 'Inactif' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent">
                <strong>Statistiques</strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-building me-2 text-primary"></i>Propriétés</span>
                    <strong><?= (int)($stats['properties_count'] ?? 0) ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-people me-2 text-success"></i>Leads</span>
                    <strong><?= (int)($stats['leads_count'] ?? 0) ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-activity me-2 text-warning"></i>Activités</span>
                    <strong><?= (int)($stats['activities_count'] ?? 0) ?></strong>
                </li>
            </ul>
        </div>
    </div>

    <!-- Informations -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent">
                <strong>Informations générales</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small">Prénom</label>
                        <p class="mb-0 fw-semibold"><?= esc($user['first_name']) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Nom</label>
                        <p class="mb-0 fw-semibold"><?= esc($user['last_name']) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0 fw-semibold"><?= esc($user['email']) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Téléphone</label>
                        <p class="mb-0 fw-semibold"><?= esc($user['phone'] ?? '—') ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Rôle</label>
                        <p class="mb-0 fw-semibold"><?= esc($user['role_name'] ?? '—') ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Statut</label>
                        <p class="mb-0">
                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= $user['status'] === 'active' ? 'Actif' : 'Inactif' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Date de création</label>
                        <p class="mb-0 fw-semibold"><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Dernière connexion</label>
                        <p class="mb-0 fw-semibold"><?= !empty($user['last_login']) ? date('d/m/Y H:i', strtotime($user['last_login'])) : '—' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <?php if (!empty($permissions)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <strong>Permissions du rôle</strong>
                <span class="badge bg-secondary"><?= count($permissions) ?></span>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($permissions as $perm): ?>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-shield-check me-1 text-success"></i><?= esc($perm['name']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->endSection(); ?>
