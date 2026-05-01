<?php $perms = session()->get('permissions') ?? []; ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-buildings me-2 text-primary"></i>Agences</h4>
        <p class="text-muted small mb-0">Gestion des agences immobilières et de leurs équipes</p>
    </div>
    <?php if (in_array('agencies.create', $perms)): ?>
    <a href="<?= base_url('admin/agencies/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Nouvelle agence
    </a>
    <?php endif; ?>
</div>

<?php if (session()->has('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= session('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= session('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filtres -->
<?php if (in_array('agencies.create', $perms)): ?>
<form method="GET" class="row g-2 mb-4">
    <div class="col-sm-6 col-lg-4">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Rechercher…"
               value="<?= esc($filters['search'] ?? '') ?>">
    </div>
    <div class="col-sm-4 col-lg-3">
        <select name="is_active" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            <option value="1" <?= ($filters['is_active'] ?? '') === '1' ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= ($filters['is_active'] ?? '') === '0' ? 'selected' : '' ?>>Inactive</option>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        <a href="<?= base_url('admin/agencies') ?>" class="btn btn-sm btn-light">Réinitialiser</a>
    </div>
</form>
<?php endif; ?>

<!-- Grille des agences -->
<?php if (empty($agencies)): ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-buildings fs-1 opacity-25 d-block mb-3"></i>
    <p>Aucune agence trouvée.</p>
    <?php if (in_array('agencies.create', $perms)): ?>
    <a href="<?= base_url('admin/agencies/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Créer la première agence
    </a>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="row g-4">
    <?php foreach ($agencies as $ag): ?>
    <?php $isActive = (bool) $ag['is_active']; ?>
    <div class="col-sm-6 col-xl-4">
        <div class="card shadow-sm h-100 <?= $isActive ? '' : 'opacity-65' ?>" style="border-top: 4px solid <?= $isActive ? '#0d6efd' : '#adb5bd' ?>;">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <!-- Logo ou initiale -->
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-bold"
                         style="width:52px;height:52px;font-size:1.25rem;background:<?= $isActive ? '#0d6efd' : '#adb5bd' ?>;">
                        <?php if (! empty($ag['logo'])): ?>
                        <img src="<?= base_url($ag['logo']) ?>" alt="" class="rounded-2" style="width:52px;height:52px;object-fit:cover;">
                        <?php else: ?>
                        <?= strtoupper(mb_substr($ag['name'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <h6 class="fw-bold mb-0 text-truncate"><?= esc($ag['name']) ?></h6>
                        <?php if ($ag['city']): ?>
                        <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= esc($ag['city']) ?></div>
                        <?php endif; ?>
                        <span class="badge <?= $isActive ? 'text-bg-success' : 'text-bg-secondary' ?> mt-1" style="font-size:.7rem;">
                            <?= $isActive ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 rounded-2 text-center" style="background:#f0f4ff;">
                            <div class="fw-bold text-primary"><?= (int) ($ag['users_count'] ?? 0) ?></div>
                            <div class="text-muted" style="font-size:.7rem;">Membres</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 text-center" style="background:#f0fff4;">
                            <div class="fw-bold text-success"><?= (int) ($ag['properties_count'] ?? 0) ?></div>
                            <div class="text-muted" style="font-size:.7rem;">Biens</div>
                        </div>
                    </div>
                </div>

                <?php if ($ag['email'] || $ag['phone']): ?>
                <div class="text-muted small mb-3">
                    <?php if ($ag['email']): ?>
                    <div class="text-truncate"><i class="bi bi-envelope me-1"></i><?= esc($ag['email']) ?></div>
                    <?php endif; ?>
                    <?php if ($ag['phone']): ?>
                    <div><i class="bi bi-telephone me-1"></i><?= esc($ag['phone']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= base_url('admin/agencies/' . $ag['id']) ?>" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="bi bi-eye me-1"></i>Voir
                    </a>
                    <?php if (in_array('agencies.edit', $perms)): ?>
                    <a href="<?= base_url('admin/agencies/' . $ag['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="<?= base_url('admin/agencies/' . $ag['id'] . '/toggle') ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm <?= $isActive ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $isActive ? 'Désactiver' : 'Activer' ?>">
                            <i class="bi bi-<?= $isActive ? 'pause' : 'play' ?>-circle"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php if (in_array('agencies.delete', $perms)): ?>
                    <form method="POST" action="<?= base_url('admin/agencies/' . $ag['id'] . '/delete') ?>"
                          onsubmit="return confirm('Supprimer cette agence ?')">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>


