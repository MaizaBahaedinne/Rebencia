
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Leads / CRM</h2>
        <small class="text-muted">Gestion du pipeline commercial</small>
    </div>
    <?php if (in_array('leads.create', session()->get('permissions') ?? [])) : ?>
    <a href="<?= site_url('admin/leads/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouveau lead
    </a>
    <?php endif; ?>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Pipeline kanban summary -->
<div class="row g-3 mb-4">
    <?php
    $pipelineLabels = [
        'new'         => ['Nouveau',        'primary'],
        'contacted'   => ['Contacté',       'info'],
        'interested'  => ['Intéressé',      'warning'],
        'visit_done'  => ['Visite faite',   'secondary'],
        'negotiating' => ['Négociation',    'orange'],
        'won'         => ['Conclu',         'success'],
        'lost'        => ['Perdu',          'danger'],
    ];
    foreach ($pipelineLabels as $key => [$label, $color]):
        $count = is_array($pipeline[$key] ?? 0) ? count($pipeline[$key]) : ($pipeline[$key] ?? 0);
    ?>
    <div class="col-6 col-sm-4 col-lg">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="card-body p-0">
                <div class="fs-3 fw-bold text-<?= $color ?>"><?= $count ?></div>
                <small class="text-muted"><?= $label ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filtres -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nom, email, tél…" value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-sm-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous statuts</option>
                    <?php foreach ($pipelineLabels as $key => [$label, $_color]): ?>
                        <option value="<?= $key ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="assigned_to" class="form-select form-select-sm">
                    <option value="">Tous agents</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?= $agent['id'] ?>" <?= ($filters['assigned_to'] ?? '') == $agent['id'] ? 'selected' : '' ?>>
                            <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="source" class="form-select form-select-sm">
                    <option value="">Toutes sources</option>
                    <option value="website"    <?= ($filters['source'] ?? '') === 'website'    ? 'selected' : '' ?>>Site web</option>
                    <option value="referral"   <?= ($filters['source'] ?? '') === 'referral'   ? 'selected' : '' ?>>Recommandation</option>
                    <option value="phone"      <?= ($filters['source'] ?? '') === 'phone'      ? 'selected' : '' ?>>Téléphone</option>
                    <option value="email"      <?= ($filters['source'] ?? '') === 'email'      ? 'selected' : '' ?>>Email</option>
                    <option value="walk_in"    <?= ($filters['source'] ?? '') === 'walk_in'    ? 'selected' : '' ?>>Passage</option>
                    <option value="social"     <?= ($filters['source'] ?? '') === 'social'     ? 'selected' : '' ?>>Réseaux sociaux</option>
                    <option value="other"      <?= ($filters['source'] ?? '') === 'other'      ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>
            <div class="col-sm-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i> Filtrer
                </button>
                <a href="<?= site_url('admin/leads') ?>" class="btn btn-sm btn-outline-secondary ms-1">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Leads <span class="badge bg-secondary"><?= $total ?></span></span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Contact</th>
                    <th>Source</th>
                    <th>Propriété</th>
                    <th>Assigné à</th>
                    <th>Statut</th>
                    <th>Budget</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Aucun lead trouvé
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($leads as $lead): ?>
                <?php
                $statusBadge = [
                    'new'         => ['Nouveau',      'primary'],
                    'contacted'   => ['Contacté',     'info'],
                    'interested'  => ['Intéressé',    'warning'],
                    'visit_done'  => ['Visite faite', 'secondary'],
                    'negotiating' => ['Négociation',  'dark'],
                    'won'         => ['Conclu',       'success'],
                    'lost'        => ['Perdu',        'danger'],
                ][$lead['status']] ?? [$lead['status'], 'light'];
                ?>
                <tr>
                    <td>
                        <div class="fw-semibold"><?= esc($lead['first_name'] . ' ' . $lead['last_name']) ?></div>
                        <small class="text-muted"><?= esc($lead['email'] ?? '') ?></small>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= esc($lead['source'] ?? '—') ?></span></td>
                    <td>
                        <?php if (!empty($lead['property_ref'])): ?>
                            <a href="<?= site_url('admin/properties/' . $lead['property_id']) ?>" class="text-decoration-none small">
                                <?= esc($lead['property_ref']) ?>
                            </a>
                        <?php elseif (!empty($lead['property_title'])): ?>
                            <span class="small"><?= esc($lead['property_title']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($lead['agent_name'])): ?>
                            <?= esc($lead['agent_name']) ?>
                        <?php else: ?>
                            <span class="text-muted">Non assigné</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= $statusBadge[1] ?>"><?= $statusBadge[0] ?></span>
                    </td>
                    <td>
                        <?php if (!empty($lead['budget_min']) || !empty($lead['budget_max'])): ?>
                            <small><?= number_format((float)($lead['budget_min'] ?? 0), 0, ',', ' ') ?> – <?= number_format((float)($lead['budget_max'] ?? 0), 0, ',', ' ') ?> TND</small>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= date('d/m/Y', strtotime($lead['created_at'])) ?></td>
                    <td class="text-end">
                        <a href="<?= site_url('admin/leads/' . $lead['id']) ?>" class="btn btn-sm btn-outline-info" title="Voir">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if (in_array('leads.edit', session()->get('permissions') ?? [])) : ?>
                        <a href="<?= site_url('admin/leads/' . $lead['id'] . '/edit') ?>" class="btn btn-sm btn-outline-warning ms-1" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Page <?= $page ?> / <?= $pages ?> &mdash; <?= $total ?> lead<?= $total > 1 ? 's' : '' ?>
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>">&laquo;</a>
                </li>
                <?php endif; ?>
                <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $p])) ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
                <?php if ($page < $pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>">&raquo;</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

