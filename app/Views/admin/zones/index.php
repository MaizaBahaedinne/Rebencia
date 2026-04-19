<?php
// Type metadata : badge couleur + libellé + icône
$typeMeta = [
    'pays'         => ['primary',         'Pays',        'bi-globe2'],
    'region'       => ['success',         'Région',      'bi-map'],
    'ville'        => ['info',            'Ville',       'bi-buildings'],
    'code_postal'  => ['warning text-dark','Code postal','bi-mailbox'],
];
?>

<!-- EN-TÊTE -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Gestion des zones</h4>
        <p class="text-muted mb-0"><?= count($zones) ?> zone(s) trouvée(s)</p>
    </div>
    <?php if (in_array('zones.create', session()->get('permissions') ?? [])) : ?>
    <a href="<?= base_url('admin/zones/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-1"></i>Nouvelle zone
    </a>
    <?php endif; ?>
</div>

<!-- COMPTEURS PAR TYPE -->
<div class="row g-3 mb-4">
    <?php foreach ($typeMeta as $typeKey => [$color, $label, $icon]) : ?>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-<?= explode(' ', $color)[0] ?> bg-opacity-10">
                    <i class="bi <?= $icon ?> text-<?= explode(' ', $color)[0] ?>"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $counts[$typeKey] ?? 0 ?></div>
                    <div class="text-muted small"><?= $label ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- FILTRES -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous les types</option>
                    <?php foreach ($typeMeta as $typeKey => [, $typeLabel]) : ?>
                    <option value="<?= $typeKey ?>" <?= ($filters['type'] ?? '') === $typeKey ? 'selected' : '' ?>>
                        <?= $typeLabel ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="<?= esc($filters['search'] ?? '') ?>"
                       placeholder="Rechercher par nom ou code…">
            </div>
            <div class="col-md-4 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filtrer</button>
                <a href="<?= base_url('admin/zones') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- TABLEAU -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Code</th>
                        <th>Parent</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($zones)) : ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-geo-alt fs-2 d-block mb-2 opacity-25"></i>
                            Aucune zone trouvée.
                        </td>
                    </tr>
                    <?php else : ?>
                    <?php foreach ($zones as $z) : ?>
                    <?php [$badgeClass, $typeLabel, $typeIcon] = $typeMeta[$z['type']] ?? ['secondary', $z['type'], 'bi-pin-map']; ?>
                    <tr>
                        <td>
                            <a href="<?= base_url('admin/zones/' . $z['id']) ?>"
                               class="fw-semibold text-decoration-none text-dark">
                                <i class="bi <?= $typeIcon ?> me-1 opacity-50"></i>
                                <?= esc($z['name']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-<?= $badgeClass ?>"><?= $typeLabel ?></span>
                        </td>
                        <td class="text-muted small">
                            <?= $z['code'] ? '<code>' . esc($z['code']) . '</code>' : '–' ?>
                        </td>
                        <td class="text-muted small">
                            <?php if ($z['parent_name']) : ?>
                            <?php [$pBadge, $pLabel] = $typeMeta[$z['parent_type']] ?? ['secondary', $z['parent_type']]; ?>
                            <span class="badge bg-<?= $pBadge ?> opacity-75 me-1"><?= $pLabel ?></span>
                            <?= esc($z['parent_name']) ?>
                            <?php else : ?>
                            <span class="text-muted">–</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($z['is_active']) : ?>
                            <span class="badge bg-success">Actif</span>
                            <?php else : ?>
                            <span class="badge bg-secondary">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?= $z['created_at'] ? date('d/m/Y', strtotime($z['created_at'])) : '–' ?>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?= base_url('admin/zones/' . $z['id']) ?>"
                                   class="btn btn-sm btn-outline-secondary" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if (in_array('zones.edit', session()->get('permissions') ?? [])) : ?>
                                <a href="<?= base_url('admin/zones/' . $z['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST"
                                      action="<?= base_url('admin/zones/' . $z['id'] . '/toggle-status') ?>"
                                      class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-<?= $z['is_active'] ? 'warning' : 'success' ?>"
                                            title="<?= $z['is_active'] ? 'Désactiver' : 'Activer' ?>">
                                        <i class="bi bi-toggle-<?= $z['is_active'] ? 'on' : 'off' ?>"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php if (in_array('zones.delete', session()->get('permissions') ?? [])) : ?>
                                <form method="POST"
                                      action="<?= base_url('admin/zones/' . $z['id'] . '/delete') ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer « <?= esc($z['name'], 'js') ?> » ?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
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
</div>
