<?php
$perms    = session()->get('permissions') ?? [];
$meta     = $typeMeta[$zone['type']] ?? ['label' => $zone['type'], 'icon' => 'bi-geo-alt', 'color' => 'secondary'];

// Construire le fil d'Ariane (pays › région › ville ›  actuel)
$breadcrumb = array_filter([
    $chain['pays']   ?? null,
    $chain['region'] ?? null,
    $chain['ville']  ?? null,
]);
?>

<!-- ── EN-TÊTE ─────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/zones?tab=' . $zone['type']) ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi <?= $meta['icon'] ?> me-2 text-<?= $meta['color'] ?>"></i>
            <?= esc($zone['name']) ?>
        </h4>
        <p class="text-muted mb-0 small"><?= esc($meta['label']) ?></p>
    </div>
    <!-- Actions rapides -->
    <div class="ms-auto d-flex gap-2">
        <?php if (in_array('zones.edit', $perms)): ?>
        <a href="<?= base_url('admin/zones/' . $zone['id'] . '/edit') ?>"
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <?php endif; ?>
        <?php if (in_array('zones.create', $perms)): ?>
        <a href="<?= base_url('admin/zones/create/' . $zone['type']) ?>"
           class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nouveau(elle) <?= esc($meta['label']) ?>
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- ── FLASH ─────────────────────────────────────────────────────────── -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- ── INFORMATIONS ────────────────────────────────────────────── -->
    <div class="col-md-5">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-info-circle me-1"></i> Informations
            </div>
            <div class="card-body">

                <!-- Fil d'Ariane hiérarchique -->
                <?php if (! empty($breadcrumb)): ?>
                <div class="mb-3 p-2 bg-light rounded small">
                    <i class="bi bi-diagram-3 me-1 text-muted"></i>
                    <?php
                    $parts = [];
                    foreach ($breadcrumb as $ancestor) {
                        $aMeta  = $typeMeta[$ancestor['type']] ?? ['color' => 'secondary', 'icon' => 'bi-geo'];
                        $parts[] = '<span class="text-' . $aMeta['color'] . '">'
                                 . '<i class="bi ' . $aMeta['icon'] . ' me-1"></i>'
                                 . esc($ancestor['name']) . '</span>';
                    }
                    echo implode(' <i class="bi bi-chevron-right text-muted mx-1 small"></i> ', $parts);
                    ?>
                    <i class="bi bi-chevron-right text-muted mx-1 small"></i>
                    <strong class="text-<?= $meta['color'] ?>"><?= esc($zone['name']) ?></strong>
                </div>
                <?php endif; ?>

                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal small">Nom</dt>
                    <dd class="col-7 fw-semibold"><?= esc($zone['name']) ?></dd>

                    <dt class="col-5 text-muted fw-normal small">Type</dt>
                    <dd class="col-7">
                        <span class="badge text-bg-<?= $meta['color'] ?>">
                            <i class="bi <?= $meta['icon'] ?> me-1"></i><?= esc($meta['label']) ?>
                        </span>
                    </dd>

                    <?php if ($zone['code']): ?>
                    <dt class="col-5 text-muted fw-normal small">
                        <?= $zone['type'] === 'pays' ? 'Code ISO' : ($zone['type'] === 'ville' ? 'Code postal' : 'Code') ?>
                    </dt>
                    <dd class="col-7"><code><?= esc($zone['code']) ?></code></dd>
                    <?php endif; ?>

                    <dt class="col-5 text-muted fw-normal small">Statut</dt>
                    <dd class="col-7">
                        <?php if ($zone['is_active']): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border">Inactif</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-5 text-muted fw-normal small">Créé le</dt>
                    <dd class="col-7 small"><?= date('d/m/Y', strtotime($zone['created_at'])) ?></dd>
                </dl>
            </div>

            <!-- Actions -->
            <?php if (in_array('zones.edit', $perms) || in_array('zones.delete', $perms)): ?>
            <div class="card-footer bg-transparent d-flex gap-2 flex-wrap">
                <?php if (in_array('zones.edit', $perms)): ?>
                <form method="POST"
                      action="<?= base_url('admin/zones/' . $zone['id'] . '/toggle-status') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-<?= $zone['is_active'] ? 'secondary' : 'success' ?>">
                        <i class="bi <?= $zone['is_active'] ? 'bi-toggle-off' : 'bi-toggle-on' ?> me-1"></i>
                        <?= $zone['is_active'] ? 'Désactiver' : 'Activer' ?>
                    </button>
                </form>
                <?php endif; ?>
                <?php if (in_array('zones.delete', $perms) && empty($children)): ?>
                <form method="POST"
                      action="<?= base_url('admin/zones/' . $zone['id'] . '/delete') ?>"
                      onsubmit="return confirm('Supprimer « <?= esc($zone['name'], 'js') ?> » définitivement ?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Supprimer
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── SOUS-ZONES ──────────────────────────────────────────────── -->
    <div class="col-md-7">
        <?php
        $childTypes   = ['pays' => 'region', 'region' => 'ville', 'ville' => 'quartier', 'quartier' => null];
        $childType    = $childTypes[$zone['type']] ?? null;
        $childMeta    = $childType ? ($typeMeta[$childType] ?? null) : null;
        ?>
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-list-nested me-1"></i>
                    Sous-zones
                    <span class="badge bg-secondary ms-1"><?= count($children) ?></span>
                </span>
                <?php if ($childMeta && in_array('zones.create', $perms)): ?>
                <a href="<?= base_url('admin/zones/create/' . $childType) ?>"
                   class="btn btn-sm btn-outline-<?= $childMeta['color'] ?>">
                    <i class="bi bi-plus-lg me-1"></i>Ajouter <?= esc($childMeta['label']) ?>
                </a>
                <?php endif; ?>
            </div>

            <?php if (empty($children)): ?>
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                Aucune sous-zone pour le moment.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase text-muted" style="font-size:.7rem">
                        <tr>
                            <th class="ps-3">Nom</th>
                            <th>Type</th>
                            <?php if ($childType === 'ville'): ?><th>Code postal</th><?php endif; ?>
                            <th>Statut</th>
                            <th class="pe-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($children as $child): ?>
                    <?php $cMeta = $typeMeta[$child['type']] ?? ['label' => $child['type'], 'icon' => 'bi-geo', 'color' => 'secondary']; ?>
                    <tr>
                        <td class="ps-3">
                            <a href="<?= base_url('admin/zones/' . $child['id']) ?>"
                               class="fw-semibold text-dark text-decoration-none">
                                <i class="bi <?= $cMeta['icon'] ?> text-<?= $cMeta['color'] ?> me-1"></i>
                                <?= esc($child['name']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge text-bg-<?= $cMeta['color'] ?> bg-opacity-75">
                                <?= esc($cMeta['label']) ?>
                            </span>
                        </td>
                        <?php if ($childType === 'ville'): ?>
                        <td><code><?= $child['code'] ? esc($child['code']) : '—' ?></code></td>
                        <?php endif; ?>
                        <td>
                            <?php if ($child['is_active']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                            <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-3 text-end">
                            <a href="<?= base_url('admin/zones/' . $child['id']) ?>"
                               class="btn btn-sm btn-light">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.row -->
