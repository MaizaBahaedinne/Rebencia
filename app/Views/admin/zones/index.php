<?php
$perms = session()->get('permissions') ?? [];

// ── En-tête ─────────────────────────────────────────────────────────
?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-map-fill me-2 text-primary"></i>Zones géographiques
        </h4>
        <p class="text-muted mb-0">Pays · Régions · Villes · Quartiers</p>
    </div>
</div>

<!-- ── FLASH MESSAGES ───────────────────────────────────────────────── -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ── COMPTEURS ────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <?php foreach ($typeMeta as $typeKey => $meta): ?>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-2 bg-<?= $meta['color'] ?> bg-opacity-10 flex-shrink-0">
                    <i class="bi <?= $meta['icon'] ?> fs-4 text-<?= $meta['color'] ?>"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fs-3 fw-bold lh-1"><?= $counts[$typeKey] ?? 0 ?></div>
                    <div class="text-muted small"><?= esc($meta['label']) ?></div>
                </div>
                <?php if (in_array('zones.create', $perms)): ?>
                <a href="<?= base_url('admin/zones/create/' . $typeKey) ?>"
                   class="btn btn-sm btn-outline-<?= $meta['color'] ?> flex-shrink-0"
                   title="Ajouter <?= esc($meta['label']) ?>">
                    <i class="bi bi-plus-lg"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── ONGLETS ──────────────────────────────────────────────────────── -->
<div class="card shadow-sm">

    <div class="card-header p-0 border-bottom-0">
        <nav>
            <ul class="nav nav-tabs px-3 pt-2" id="zonesTabs">
                <?php foreach ($typeMeta as $typeKey => $meta): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $activeTab === $typeKey ? 'active' : '' ?>"
                       href="<?= base_url('admin/zones?tab=' . $typeKey) ?>">
                        <i class="bi <?= $meta['icon'] ?> me-1"></i>
                        <?= esc($meta['label']) ?>
                        <span class="badge rounded-pill ms-1
                            <?= $activeTab === $typeKey ? 'bg-' . $meta['color'] : 'text-bg-light' ?>">
                            <?= $counts[$typeKey] ?? 0 ?>
                        </span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>

    <?php
    $currentList = match ($activeTab) {
        'region'   => $region_list,
        'ville'    => $ville_list,
        'quartier' => $quartier_list,
        default    => $pays_list,
    };
    $meta = $typeMeta[$activeTab];
    ?>

    <!-- Barre d'outils du tab actif -->
    <div class="card-header bg-light d-flex justify-content-between align-items-center gap-2 flex-wrap py-2">
        <span class="text-muted small">
            <i class="bi <?= $meta['icon'] ?> text-<?= $meta['color'] ?> me-1"></i>
            <strong><?= count($currentList) ?></strong> entrée(s) — <?= esc($meta['label']) ?>
        </span>
        <?php if (in_array('zones.create', $perms)): ?>
        <a href="<?= base_url('admin/zones/create/' . $activeTab) ?>"
           class="btn btn-sm btn-<?= $meta['color'] ?> <?= $meta['color'] === 'warning' ? 'text-dark' : '' ?>">
            <i class="bi bi-plus-circle me-1"></i>Ajouter <?= esc($meta['label']) ?>
        </a>
        <?php endif; ?>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
                <tr>
                    <th class="ps-3">Nom</th>
                    <?php if ($activeTab === 'pays'): ?>
                    <th>Code ISO</th>
                    <?php endif; ?>
                    <?php if ($activeTab === 'ville'): ?>
                    <th>Code postal</th>
                    <?php endif; ?>
                    <?php if ($activeTab !== 'pays'): ?>
                    <th>Zone parente</th>
                    <?php endif; ?>
                    <th>Statut</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($currentList)): ?>
                <tr>
                    <td colspan="6" class="py-5 text-center text-muted">
                        <i class="bi <?= $meta['icon'] ?> fs-1 d-block mb-2 opacity-25"></i>
                        Aucun(e) <?= strtolower(esc($meta['label'])) ?> enregistré(e).
                        <?php if (in_array('zones.create', $perms)): ?>
                        <div class="mt-2">
                            <a href="<?= base_url('admin/zones/create/' . $activeTab) ?>"
                               class="btn btn-sm btn-outline-<?= $meta['color'] ?>">
                                <i class="bi bi-plus-circle me-1"></i>Ajouter
                            </a>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($currentList as $z): ?>
                <tr>
                    <td class="ps-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi <?= $meta['icon'] ?> text-<?= $meta['color'] ?> fs-5"></i>
                            <div>
                                <a href="<?= base_url('admin/zones/' . $z['id']) ?>"
                                   class="fw-semibold text-dark text-decoration-none stretched-link-inner">
                                    <?= esc($z['name']) ?>
                                </a>
                            </div>
                        </div>
                    </td>

                    <?php if ($activeTab === 'pays'): ?>
                    <td>
                        <?php if ($z['code']): ?>
                        <code class="text-primary"><?= esc($z['code']) ?></code>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>

                    <?php if ($activeTab === 'ville'): ?>
                    <td>
                        <?php if ($z['code']): ?>
                        <code><?= esc($z['code']) ?></code>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>

                    <?php if ($activeTab !== 'pays'): ?>
                    <td>
                        <?php if ($z['parent_name']): ?>
                        <span class="badge bg-light text-dark border">
                            <?= esc($z['parent_name']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted small fst-italic">—</span>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>

                    <td>
                        <?php if ($z['is_active']): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border">Inactif</span>
                        <?php endif; ?>
                    </td>

                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <!-- Voir -->
                            <a href="<?= base_url('admin/zones/' . $z['id']) ?>"
                               class="btn btn-sm btn-light" title="Voir le détail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <!-- Modifier -->
                            <?php if (in_array('zones.edit', $perms)): ?>
                            <a href="<?= base_url('admin/zones/' . $z['id'] . '/edit') ?>"
                               class="btn btn-sm btn-light" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- Toggle statut -->
                            <form method="POST"
                                  action="<?= base_url('admin/zones/' . $z['id'] . '/toggle-status') ?>"
                                  class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light"
                                        title="<?= $z['is_active'] ? 'Désactiver' : 'Activer' ?>">
                                    <i class="bi <?= $z['is_active'] ? 'bi-toggle-on text-success' : 'bi-toggle-off text-secondary' ?>"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <!-- Supprimer -->
                            <?php if (in_array('zones.delete', $perms)): ?>
                            <form method="POST"
                                  action="<?= base_url('admin/zones/' . $z['id'] . '/delete') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Supprimer « <?= esc($z['name'], 'js') ?> » ?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-light text-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
