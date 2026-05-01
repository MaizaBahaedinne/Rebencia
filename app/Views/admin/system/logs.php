
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Journaux système</h2>
        <small class="text-muted">Activités et logs applicatifs</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/system/logs/export?tab=' . ($activeTab ?? 'activity') . '&' . http_build_query($filters ?? [])) ?>"
           class="btn btn-outline-success">
            <i class="bi bi-download me-1"></i> Exporter CSV
        </a>
    </div>
</div>

<!-- Onglets -->
<ul class="nav nav-tabs mb-3" id="logTabs">
    <li class="nav-item">
        <a class="nav-link <?= ($activeTab ?? 'activity') === 'activity' ? 'active' : '' ?>"
           href="?tab=activity">
            <i class="bi bi-activity me-1"></i> Activités
            <span class="badge bg-secondary ms-1"><?= number_format($activityStats['total'] ?? 0) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($activeTab ?? '') === 'system' ? 'active' : '' ?>"
           href="?tab=system">
            <i class="bi bi-shield me-1"></i> Système
            <span class="badge bg-secondary ms-1"><?= number_format($systemStats['total'] ?? 0) ?></span>
        </a>
    </li>
</ul>

<?php $isActivity = ($activeTab ?? 'activity') === 'activity'; ?>

<!-- Résumé par niveau (système) -->
<?php if (!$isActivity && !empty($systemLevelStats)): ?>
<div class="row g-3 mb-3">
    <?php
    $levelConfig = [
        'critical' => ['Critique', 'danger'],
        'error'    => ['Erreur',   'warning'],
        'warning'  => ['Alerte',   'orange'],
        'info'     => ['Info',     'info'],
        'debug'    => ['Debug',    'secondary'],
    ];
    foreach ($levelConfig as $lvl => [$lbl, $bg]):
        if (empty($systemLevelStats[$lvl])) continue;
    ?>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="card border-0 shadow-sm text-center py-2">
            <div class="card-body p-0">
                <div class="fs-4 fw-bold text-<?= $bg ?>"><?= $systemLevelStats[$lvl] ?></div>
                <small class="text-muted"><?= $lbl ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Filtres -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="<?= esc($activeTab ?? 'activity') ?>">
            <div class="col-sm-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Rechercher…" value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <?php if ($isActivity): ?>
            <div class="col-sm-2">
                <select name="module" class="form-select form-select-sm">
                    <option value="">Tous modules</option>
                    <?php foreach ($modules ?? [] as $mod): ?>
                        <option value="<?= esc($mod) ?>" <?= ($filters['module'] ?? '') === $mod ? 'selected' : '' ?>><?= esc($mod) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Tous utilisateurs</option>
                    <?php foreach ($users ?? [] as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= esc($u['first_name'] . ' ' . $u['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
            <div class="col-sm-2">
                <select name="level" class="form-select form-select-sm">
                    <option value="">Tous niveaux</option>
                    <?php foreach (['debug'=>'Debug','info'=>'Info','warning'=>'Alerte','error'=>'Erreur','critical'=>'Critique'] as $val=>$lbl): ?>
                        <option value="<?= $val ?>" <?= ($filters['level'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="channel" class="form-select form-select-sm">
                    <option value="">Tous les fichiers</option>
                    <?php foreach ($channels ?? [] as $ch): ?>
                        <option value="<?= esc($ch) ?>" <?= ($filters['channel'] ?? '') === $ch ? 'selected' : '' ?>><?= esc($ch) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-sm-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= esc($filters['date_from'] ?? '') ?>">
            </div>
            <div class="col-sm-2">
                <input type="date" name="date_to"   class="form-control form-control-sm" value="<?= esc($filters['date_to'] ?? '') ?>">
            </div>
            <div class="col-sm-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                <a href="?tab=<?= esc($activeTab ?? 'activity') ?>" class="btn btn-sm btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Tableau logs -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0 font-monospace" style="font-size:.82rem">
            <thead class="table-light">
                <tr>
                    <th style="width:140px">Date</th>
                    <?php if ($isActivity): ?>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>IP</th>
                    <?php else: ?>
                    <th style="width:90px">Niveau</th>
                    <th style="width:110px">Fichier</th>
                    <th>Message</th>
                    <th style="width:40px">Ctx</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i> Aucun log trouvé
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="text-muted"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                    <?php if ($isActivity): ?>
                    <td><?= esc($log['user_name'] ?? '—') ?></td>
                    <td><code><?= esc($log['action']) ?></code></td>
                    <td><?= esc($log['module'] ?? '—') ?></td>
                    <td><?= esc($log['description'] ?? '—') ?></td>
                    <td class="text-muted"><?= esc($log['ip_address'] ?? '') ?></td>
                    <?php else: ?>
                    <?php
                    $lvlBadge = ['critical'=>'danger','error'=>'warning','warning'=>'warning text-dark','info'=>'info','debug'=>'secondary'];
                    $lvl = $log['level'] ?? 'info';
                    ?>
                    <td><span class="badge bg-<?= $lvlBadge[$lvl] ?? 'secondary' ?>"><?= strtoupper($lvl) ?></span></td>
                    <td><?= esc($log['channel'] ?? '—') ?></td>
                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                        title="<?= esc($log['message']) ?>">
                        <?= esc($log['message']) ?>
                    </td>
                    <td>
                        <?php if (!empty($log['context'])): ?>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1"
                                onclick="showCtx(<?= htmlspecialchars(json_encode($log['context']), ENT_QUOTES) ?>)">
                            <i class="bi bi-code"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">Total : <strong><?= number_format($total ?? 0) ?></strong> entrées</small>
        <nav><ul class="pagination pagination-sm mb-0">
            <?php if (($curPage ?? 1) > 1): ?>
            <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $curPage - 1])) ?>">‹</a></li>
            <?php endif; ?>
            <?php for ($p = max(1, ($curPage ?? 1) - 2); $p <= min($totalPages, ($curPage ?? 1) + 2); $p++): ?>
            <li class="page-item <?= $p === ($curPage ?? 1) ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $p])) ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
            <?php if (($curPage ?? 1) < ($totalPages ?? 1)): ?>
            <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $curPage + 1])) ?>">›</a></li>
            <?php endif; ?>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>

<!-- Modal contexte JSON -->
<div class="modal fade" id="ctxModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Contexte</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="ctxBody" class="bg-light p-3 rounded" style="font-size:.8rem;max-height:400px;overflow-y:auto"></pre>
            </div>
        </div>
    </div>
</div>

<script>
function showCtx(data) {
    document.getElementById('ctxBody').textContent = JSON.stringify(JSON.parse(data), null, 2);
    new bootstrap.Modal(document.getElementById('ctxModal')).show();
}
</script>

