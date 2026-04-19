<?php
$typeMeta = [
    'pays'        => ['primary',          'Pays',        'bi-globe2'],
    'region'      => ['success',          'Région',      'bi-map'],
    'ville'       => ['info',             'Ville',       'bi-buildings'],
    'code_postal' => ['warning text-dark','Code postal', 'bi-mailbox'],
];

$childTypeMeta = $typeMeta; // même mapping pour les enfants

[$badgeClass, $typeLabel, $typeIcon] = $typeMeta[$zone['type']] ?? ['secondary', $zone['type'], 'bi-pin-map'];
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/zones') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-grow-1">
        <h4 class="mb-0 fw-bold">
            <i class="bi <?= $typeIcon ?> me-2"></i><?= esc($zone['name']) ?>
        </h4>
        <span class="badge bg-<?= $badgeClass ?> mt-1"><?= $typeLabel ?></span>
    </div>
    <?php if (in_array('zones.edit', session()->get('permissions') ?? [])) : ?>
    <a href="<?= base_url('admin/zones/' . $zone['id'] . '/edit') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-pencil me-1"></i>Modifier
    </a>
    <?php endif; ?>
</div>

<div class="row g-4">

    <!-- Informations de la zone -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-light">
                <i class="bi bi-info-circle me-1"></i> Détails
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Type</dt>
                    <dd class="col-7">
                        <span class="badge bg-<?= $badgeClass ?>"><?= $typeLabel ?></span>
                    </dd>

                    <dt class="col-5 text-muted">Nom</dt>
                    <dd class="col-7 fw-semibold"><?= esc($zone['name']) ?></dd>

                    <dt class="col-5 text-muted">Code</dt>
                    <dd class="col-7">
                        <?= $zone['code'] ? '<code>' . esc($zone['code']) . '</code>' : '<span class="text-muted">–</span>' ?>
                    </dd>

                    <dt class="col-5 text-muted">Parent</dt>
                    <dd class="col-7">
                        <?php if ($parent) : ?>
                        <?php [$pBadge, $pLabel] = $typeMeta[$parent['type']] ?? ['secondary', $parent['type']]; ?>
                        <span class="badge bg-<?= $pBadge ?> me-1"><?= $pLabel ?></span>
                        <a href="<?= base_url('admin/zones/' . $parent['id']) ?>"
                           class="text-decoration-none fw-semibold">
                            <?= esc($parent['name']) ?>
                        </a>
                        <?php else : ?>
                        <span class="text-muted">Aucun (racine)</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-5 text-muted">Statut</dt>
                    <dd class="col-7">
                        <?php if ($zone['is_active']) : ?>
                        <span class="badge bg-success">Actif</span>
                        <?php else : ?>
                        <span class="badge bg-secondary">Inactif</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-5 text-muted">Créé le</dt>
                    <dd class="col-7 text-muted small">
                        <?= $zone['created_at'] ? date('d/m/Y à H:i', strtotime($zone['created_at'])) : '–' ?>
                    </dd>

                    <dt class="col-5 text-muted">Modifié le</dt>
                    <dd class="col-7 text-muted small">
                        <?= $zone['updated_at'] ? date('d/m/Y à H:i', strtotime($zone['updated_at'])) : '–' ?>
                    </dd>
                </dl>
            </div>
            <?php if (in_array('zones.edit', session()->get('permissions') ?? [])
                   || in_array('zones.delete', session()->get('permissions') ?? [])) : ?>
            <div class="card-footer d-flex gap-2">
                <?php if (in_array('zones.edit', session()->get('permissions') ?? [])) : ?>
                <form method="POST"
                      action="<?= base_url('admin/zones/' . $zone['id'] . '/toggle-status') ?>">
                    <?= csrf_field() ?>
                    <button type="submit"
                            class="btn btn-sm btn-outline-<?= $zone['is_active'] ? 'warning' : 'success' ?>">
                        <i class="bi bi-toggle-<?= $zone['is_active'] ? 'on' : 'off' ?> me-1"></i>
                        <?= $zone['is_active'] ? 'Désactiver' : 'Activer' ?>
                    </button>
                </form>
                <?php endif; ?>
                <?php if (in_array('zones.delete', session()->get('permissions') ?? []) && empty($children)) : ?>
                <form method="POST"
                      action="<?= base_url('admin/zones/' . $zone['id'] . '/delete') ?>"
                      onsubmit="return confirm('Supprimer définitivement « <?= esc($zone['name'], 'js') ?> » ?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Supprimer
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sous-zones (enfants) -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-light fw-semibold">
                <span><i class="bi bi-diagram-3 me-1"></i> Sous-zones (<?= count($children) ?>)</span>
                <?php if (in_array('zones.create', session()->get('permissions') ?? []) && $zone['type'] !== 'code_postal') : ?>
                <?php
                $childTypeLabels = [
                    'pays'   => ['region',      'Ajouter une région'],
                    'region' => ['ville',       'Ajouter une ville'],
                    'ville'  => ['code_postal', 'Ajouter un code postal'],
                ];
                if (isset($childTypeLabels[$zone['type']])) :
                    [$childType, $childBtnLabel] = $childTypeLabels[$zone['type']];
                ?>
                <a href="<?= base_url('admin/zones/create?parent_id=' . $zone['id'] . '&type=' . $childType) ?>"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus me-1"></i><?= $childBtnLabel ?>
                </a>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($children)) : ?>
                <p class="text-muted text-center py-4 mb-0">
                    <i class="bi bi-inbox fs-3 d-block mb-2 opacity-25"></i>
                    Aucune sous-zone.
                </p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Code</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($children as $child) : ?>
                            <?php [$cBadge, $cLabel, $cIcon] = $childTypeMeta[$child['type']] ?? ['secondary', $child['type'], 'bi-pin-map']; ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url('admin/zones/' . $child['id']) ?>"
                                       class="fw-semibold text-decoration-none text-dark">
                                        <i class="bi <?= $cIcon ?> me-1 opacity-50"></i>
                                        <?= esc($child['name']) ?>
                                    </a>
                                </td>
                                <td><span class="badge bg-<?= $cBadge ?>"><?= $cLabel ?></span></td>
                                <td class="text-muted small">
                                    <?= $child['code'] ? '<code>' . esc($child['code']) . '</code>' : '–' ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $child['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $child['is_active'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="<?= base_url('admin/zones/' . $child['id']) ?>"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (in_array('zones.edit', session()->get('permissions') ?? [])) : ?>
                                        <a href="<?= base_url('admin/zones/' . $child['id'] . '/edit') ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
