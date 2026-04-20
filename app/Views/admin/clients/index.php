<?php
$perms = session()->get('permissions') ?? [];
?>

<!-- EN-TÊTE -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-people me-2 text-primary"></i>Clients
        </h4>
        <p class="text-muted mb-0 small"><?= $result['total'] ?> client(s) — Page <?= $result['page'] ?>/<?= max(1, $result['pages']) ?></p>
    </div>
    <?php if (in_array('clients.create', $perms)): ?>
    <a href="<?= base_url('admin/clients/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau client
    </a>
    <?php endif; ?>
</div>

<!-- Compteurs par type -->
<div class="row g-3 mb-4">
    <?php foreach ($typeLabels as $key => $meta): ?>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-<?= $meta['color'] ?>-subtle p-2 d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                    <i class="bi <?= $meta['icon'] ?> text-<?= $meta['color'] ?> fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= $typeCounts[$key] ?? 0 ?></div>
                    <div class="text-muted small"><?= $meta['label'] ?>s</div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filtres -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="client_type" class="form-select form-select-sm">
                    <option value="">Tous types</option>
                    <?php foreach ($typeLabels as $k => $m): ?>
                    <option value="<?= $k ?>" <?= ($filters['client_type'] ?? '') === $k ? 'selected' : '' ?>><?= $m['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous statuts</option>
                    <?php foreach ($statusLabels as $k => $m): ?>
                    <option value="<?= $k ?>" <?= ($filters['status'] ?? '') === $k ? 'selected' : '' ?>><?= $m['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="assigned_to" class="form-select form-select-sm">
                    <option value="">Tous agents</option>
                    <?php foreach ($agents as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($filters['assigned_to'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                        <?= esc($a['first_name'] . ' ' . $a['last_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="<?= esc($filters['search'] ?? '') ?>"
                       placeholder="Nom, téléphone, email…">
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
                <a href="<?= base_url('admin/clients') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase text-muted" style="font-size:.72rem">
                    <tr>
                        <th class="ps-3">Client</th>
                        <th>Type</th>
                        <th>Téléphone</th>
                        <th>Zone / Ville</th>
                        <th>Type bien</th>
                        <th>Statut</th>
                        <th>Agent</th>
                        <th>Source</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($result['data'])): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                        Aucun client trouvé.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($result['data'] as $c):
                    $tMeta = $typeLabels[$c['client_type']] ?? ['label' => $c['client_type'], 'color' => 'secondary', 'icon' => 'bi-person'];
                    $sMeta = $statusLabels[$c['status']] ?? ['label' => $c['status'], 'color' => 'secondary'];
                ?>
                <tr>
                    <td class="ps-3">
                        <a href="<?= base_url('admin/clients/' . $c['id']) ?>"
                           class="fw-semibold text-dark text-decoration-none">
                            <?= esc($c['first_name'] . ' ' . $c['last_name']) ?>
                        </a>
                        <?php if ($c['email']): ?>
                        <div class="text-muted small"><?= esc($c['email']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge text-bg-<?= $tMeta['color'] ?>">
                            <i class="bi <?= $tMeta['icon'] ?> me-1"></i><?= $tMeta['label'] ?>
                        </span>
                    </td>
                    <td class="small"><?= esc($c['phone']) ?></td>
                    <td class="small text-muted">
                        <?= $c['ville_name'] ? esc($c['ville_name']) : ($c['region_name'] ? esc($c['region_name']) : '—') ?>
                    </td>
                    <td class="small text-muted"><?= $c['property_type_name'] ? esc($c['property_type_name']) : '—' ?></td>
                    <td>
                        <span class="badge bg-<?= $sMeta['color'] ?>-subtle text-<?= $sMeta['color'] ?> border border-<?= $sMeta['color'] ?>-subtle">
                            <?= $sMeta['label'] ?>
                        </span>
                    </td>
                    <td class="small text-muted">
                        <?= $c['agent_first'] ? esc($c['agent_first'] . ' ' . $c['agent_last']) : '—' ?>
                    </td>
                    <td class="small text-muted">
                        <?= \App\Models\ClientModel::SOURCE_LABELS[$c['source']] ?? $c['source'] ?>
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="<?= base_url('admin/clients/' . $c['id']) ?>"
                               class="btn btn-sm btn-light" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (in_array('clients.edit', $perms)): ?>
                            <a href="<?= base_url('admin/clients/' . $c['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (in_array('clients.delete', $perms)): ?>
                            <form method="POST"
                                  action="<?= base_url('admin/clients/' . $c['id'] . '/delete') ?>"
                                  onsubmit="return confirm('Supprimer ce client ?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer">
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

    <!-- Pagination -->
    <?php if ($result['pages'] > 1): ?>
    <div class="card-footer bg-transparent">
        <nav>
            <ul class="pagination pagination-sm mb-0 justify-content-center">
                <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
                <li class="page-item <?= $p === $result['page'] ? 'active' : '' ?>">
                    <a class="page-link"
                       href="<?= base_url('admin/clients?' . http_build_query(array_merge($filters, ['page' => $p]))) ?>">
                        <?= $p ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
