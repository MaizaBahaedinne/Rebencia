<?php
$perms = session()->get('permissions') ?? [];
?>

<!-- ── EN-TÊTE ───────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar2-event me-2 text-primary"></i>Visites</h4>
        <p class="text-muted small mb-0"><?= number_format($result['total']) ?> visite(s) au total</p>
    </div>
    <div class="d-flex gap-2">
        <?php if (in_array('visits.view', $perms)): ?>
        <a href="<?= base_url('admin/visits/calendar') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-calendar3 me-1"></i>Calendrier
        </a>
        <?php endif; ?>
        <?php if (in_array('visits.create', $perms)): ?>
        <a href="<?= base_url('admin/visits/create') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nouvelle visite
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- ── CARDS STATUTS ─────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <?php foreach ($statusLabels as $sKey => $sMeta): ?>
    <div class="col-6 col-sm-4 col-xl">
        <a href="?status=<?= $sKey ?><?= ! empty($filters['agent_id']) ? '&agent_id=' . $filters['agent_id'] : '' ?>"
           class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 <?= ($filters['status'] ?? '') === $sKey ? 'border-' . $sMeta['color'] . ' border-2' : '' ?>">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-<?= $sMeta['color'] ?>-subtle"
                         style="width:42px;height:42px;flex-shrink:0">
                        <i class="bi <?= $sMeta['icon'] ?> text-<?= $sMeta['color'] ?>"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark"><?= $statusCounts[$sKey] ?? 0 ?></div>
                        <div class="small text-muted"><?= $sMeta['label'] ?></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── FILTRES ───────────────────────────────────────────────────────────── -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small mb-1">Statut</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous les statuts</option>
                    <?php foreach ($statusLabels as $sKey => $sMeta): ?>
                    <option value="<?= $sKey ?>" <?= ($filters['status'] ?? '') === $sKey ? 'selected' : '' ?>>
                        <?= $sMeta['label'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small mb-1">Agent</label>
                <select name="agent_id" class="form-select form-select-sm">
                    <option value="">Tous les agents</option>
                    <?php foreach ($agents as $ag): ?>
                    <option value="<?= $ag['id'] ?>" <?= (string) ($filters['agent_id'] ?? '') === (string) $ag['id'] ? 'selected' : '' ?>>
                        <?= esc($ag['first_name'] . ' ' . $ag['last_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <label class="form-label small mb-1">Du</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="<?= esc($filters['date_from'] ?? '') ?>">
            </div>
            <div class="col-6 col-sm-4 col-md-2">
                <label class="form-label small mb-1">Au</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="<?= esc($filters['date_to'] ?? '') ?>">
            </div>
            <div class="col-12 col-sm-4 col-md-2">
                <label class="form-label small mb-1">Recherche</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Client, bien…"
                       value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button class="btn btn-sm btn-primary flex-fill">
                    <i class="bi bi-search"></i>
                </button>
                <a href="<?= base_url('admin/visits') ?>" class="btn btn-sm btn-light flex-fill" title="Réinitialiser">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ── TABLEAU ───────────────────────────────────────────────────────────── -->
<div class="card shadow-sm">
    <?php if (empty($result['rows'])): ?>
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
        Aucune visite trouvée.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-uppercase text-muted" style="font-size:.7rem">
                <tr>
                    <th class="ps-3">Client</th>
                    <th>Bien</th>
                    <th>Agent</th>
                    <th>Date / Heure</th>
                    <th>Durée</th>
                    <th>Statut</th>
                    <th>Feedback</th>
                    <th class="pe-3"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($result['rows'] as $v): ?>
            <?php
            $sMeta = $statusLabels[$v['status']] ?? ['label' => $v['status'], 'color' => 'secondary', 'icon' => 'bi-circle'];
            $fbMeta = isset($v['feedback']) && $v['feedback'] ? (\App\Models\VisitModel::FEEDBACK_LABELS[$v['feedback']] ?? null) : null;
            $visitDt = strtotime($v['visit_date']);
            ?>
            <tr>
                <td class="ps-3">
                    <a href="<?= base_url('admin/clients/' . $v['client_id']) ?>"
                       class="fw-semibold text-dark text-decoration-none d-block">
                        <?= esc($v['first_name'] . ' ' . $v['last_name']) ?>
                    </a>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $v['client_phone']) ?>"
                       target="_blank" class="text-success small text-decoration-none" title="WhatsApp">
                        <i class="bi bi-whatsapp me-1"></i><?= esc($v['client_phone']) ?>
                    </a>
                </td>
                <td>
                    <a href="<?= base_url('admin/properties/' . $v['property_id']) ?>"
                       class="text-dark text-decoration-none fw-semibold d-block" title="<?= esc($v['property_title']) ?>">
                        <?= esc(mb_strimwidth($v['property_title'], 0, 30, '…')) ?>
                    </a>
                    <span class="text-muted"><?= esc($v['property_ref']) ?> — <?= esc($v['property_city']) ?></span>
                </td>
                <td>
                    <span class="fw-semibold"><?= esc($v['agent_first'] . ' ' . $v['agent_last']) ?></span>
                </td>
                <td>
                    <div class="fw-semibold"><?= date('d/m/Y', $visitDt) ?></div>
                    <div class="text-muted"><?= substr($v['visit_time'], 0, 5) ?></div>
                </td>
                <td class="text-muted"><?= $v['duration'] ?> min</td>
                <td>
                    <span class="badge text-bg-<?= $sMeta['color'] ?>">
                        <i class="bi <?= $sMeta['icon'] ?> me-1"></i><?= $sMeta['label'] ?>
                    </span>
                </td>
                <td>
                    <?php if ($fbMeta): ?>
                    <span class="badge bg-<?= $fbMeta['color'] ?>-subtle text-<?= $fbMeta['color'] ?> border border-<?= $fbMeta['color'] ?>-subtle">
                        <?= $fbMeta['label'] ?>
                    </span>
                    <?php elseif ($v['status'] === 'effectuee'): ?>
                    <a href="<?= base_url('admin/visits/' . $v['id']) ?>" class="small text-warning">
                        <i class="bi bi-exclamation-circle me-1"></i>À remplir
                    </a>
                    <?php else: ?>
                    <span class="text-muted small">—</span>
                    <?php endif; ?>
                </td>
                <td class="pe-3 text-end">
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="<?= base_url('admin/visits/' . $v['id']) ?>"
                           class="btn btn-sm btn-light" title="Voir">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if (in_array('visits.edit', $perms)): ?>
                        <a href="<?= base_url('admin/visits/' . $v['id'] . '/edit') ?>"
                           class="btn btn-sm btn-light" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (in_array('visits.edit', $perms) && ! in_array($v['status'], ['annulee', 'effectuee'])): ?>
                        <form method="POST" action="<?= base_url('admin/visits/' . $v['id'] . '/status') ?>"
                              onsubmit="return confirm('Annuler cette visite ?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="annulee">
                            <button class="btn btn-sm btn-outline-danger" title="Annuler">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($result['pages'] > 1): ?>
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">
            Page <?= $result['page'] ?> / <?= $result['pages'] ?>
            — <?= $result['total'] ?> résultat(s)
        </small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
                <li class="page-item <?= $p === (int) $result['page'] ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?page=<?= $p ?>&<?= http_build_query(array_filter(array_diff_key($filters, ['page' => '']))) ?>">
                        <?= $p ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>


