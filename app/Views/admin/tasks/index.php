<?php
$statusColors = array_column($statuses, 'color', 0);
$priorityIcons = array_column($priorities, 'icon', 0);
?>
<!-- SUIVI DES TÂCHES -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-kanban text-primary me-2"></i>Suivi des tâches</h4>
        <p class="text-muted mb-0"><?= count($tasks) ?> tâche(s) trouvée(s)</p>
    </div><?php if (!isset($migration_pending) && $auth->hasPermission('tasks.create')) : ?>
    <a href="<?= base_url('admin/tasks/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle tâche
    </a>
    <?php endif; ?>
</div>

<?php if (!empty($migration_pending)) : ?>
<div class="alert alert-warning d-flex align-items-start gap-3">
    <i class="bi bi-hourglass-split fs-4 flex-shrink-0 mt-1"></i>
    <div>
        <strong>Migration en attente</strong><br>
        Les tables nécessaires n'existent pas encore. Lancez un déploiement depuis
        <a href="<?= base_url('admin/system/deploy') ?>">Système → Déploiement</a>
        pour appliquer les migrations (<code>php spark migrate</code>).
    </div>
</div>
<?php return; ?>
<?php endif; ?>

<!-- Statistiques par statut -->
<div class="row g-3 mb-4">
<?php foreach ($statuses as $key => $s) : ?>
    <div class="col-6 col-lg-2">
        <div class="card border-0 shadow-sm text-center py-2">
            <div class="fw-bold fs-4"><?= $stats[$key] ?? 0 ?></div>
            <div class="small"><span class="badge bg-<?= $s['color'] ?>"><?= $s['label'] ?></span></div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<!-- Filtres -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Rechercher…" value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous les statuts</option>
                    <?php foreach ($statuses as $k => $s) : ?>
                    <option value="<?= $k ?>" <?= ($filters['status'] ?? '') === $k ? 'selected' : '' ?>><?= $s['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous les types</option>
                    <?php foreach ($types as $k => $t) : ?>
                    <option value="<?= $k ?>" <?= ($filters['type'] ?? '') === $k ? 'selected' : '' ?>><?= $t['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">Toutes priorités</option>
                    <?php foreach ($priorities as $k => $p) : ?>
                    <option value="<?= $k ?>" <?= ($filters['priority'] ?? '') === $k ? 'selected' : '' ?>><?= $p['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="assigned_to" class="form-select form-select-sm">
                    <option value="">Tous les membres</option>
                    <?php foreach ($users as $u) : ?>
                    <option value="<?= $u['id'] ?>" <?= ($filters['assigned_to'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                        <?= esc($u['first_name'] . ' ' . $u['last_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Board Kanban (par statut) -->
<?php
$grouped = [];
foreach ($tasks as $t) { $grouped[$t['status']][] = $t; }
$kanbanStatuses = ['todo', 'in_progress', 'review', 'done'];
?>

<div class="d-flex gap-3 overflow-auto pb-3" style="align-items:flex-start;">
<?php foreach ($kanbanStatuses as $colStatus) :
    $col = $statuses[$colStatus] ?? ['label' => $colStatus, 'color' => 'secondary'];
    $colTasks = $grouped[$colStatus] ?? [];
?>
<div class="flex-shrink-0" style="width:280px;">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="badge bg-<?= $col['color'] ?> px-3 py-2"><?= $col['label'] ?></span>
        <span class="badge bg-light text-dark border"><?= count($colTasks) ?></span>
    </div>
    <div class="d-flex flex-column gap-2">
    <?php if (empty($colTasks)) : ?>
        <div class="card border-dashed text-center text-muted py-4 border-0 bg-light" style="border:2px dashed #dee2e6!important;">
            <i class="bi bi-inbox fs-4 mb-1"></i><div class="small">Aucune tâche</div>
        </div>
    <?php endif; ?>
    <?php foreach ($colTasks as $task) :
        $prio   = $priorities[$task['priority']] ?? ['label' => $task['priority'], 'color' => '#6c757d', 'icon' => 'bi-dash'];
        $type   = $types[$task['type']]          ?? ['label' => $task['type'], 'icon' => 'bi-tag', 'color' => '#6c757d'];
    ?>
    <div class="card border-0 shadow-sm task-card" style="border-left:3px solid <?= $prio['color'] ?>!important;">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <code class="text-muted" style="font-size:.7rem;"><?= esc($task['reference']) ?></code>
                <i class="<?= $type['icon'] ?> small" style="color:<?= $type['color'] ?>;" title="<?= $type['label'] ?>"></i>
            </div>
            <a href="<?= base_url('admin/tasks/' . $task['id']) ?>" class="text-dark fw-semibold text-decoration-none"
               style="font-size:.875rem;line-height:1.4;">
                <?= esc($task['title']) ?>
            </a>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="badge" style="background:<?= $prio['color'] ?>;font-size:.65rem;">
                    <i class="<?= $prio['icon'] ?> me-1"></i><?= $prio['label'] ?>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($task['comment_count'] > 0) : ?>
                    <span class="text-muted small"><i class="bi bi-chat me-1"></i><?= $task['comment_count'] ?></span>
                    <?php endif; ?>
                    <?php if (! empty($task['assignee_first'])) : ?>
                    <span class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white"
                          style="width:22px;height:22px;font-size:.6rem;" title="<?= esc($task['assignee_first'] . ' ' . $task['assignee_last']) ?>">
                        <?= strtoupper(substr($task['assignee_first'], 0, 1)) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (! empty($task['due_date'])) : ?>
            <?php $overdue = strtotime($task['due_date']) < time() && ! in_array($task['status'], ['done','cancelled']); ?>
            <div class="mt-1 small <?= $overdue ? 'text-danger' : 'text-muted' ?>">
                <i class="bi bi-calendar<?= $overdue ? '-x' : '' ?> me-1"></i><?= date('d/m/Y', strtotime($task['due_date'])) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<!-- Colonne Backlog + Annulé -->
<div class="flex-shrink-0" style="width:280px;">
    <?php foreach (['backlog','cancelled'] as $colStatus) :
        $col = $statuses[$colStatus] ?? ['label' => $colStatus, 'color' => 'secondary'];
        $colTasks = $grouped[$colStatus] ?? [];
    ?>
    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-<?= $col['color'] ?> px-3 py-2"><?= $col['label'] ?></span>
            <span class="badge bg-light text-dark border"><?= count($colTasks) ?></span>
        </div>
        <?php foreach ($colTasks as $task) :
            $prio = $priorities[$task['priority']] ?? ['label' => $task['priority'], 'color' => '#6c757d', 'icon' => 'bi-dash'];
            $type = $types[$task['type']]          ?? ['label' => $task['type'], 'icon' => 'bi-tag', 'color' => '#6c757d'];
        ?>
        <div class="card border-0 shadow-sm mb-2" style="border-left:3px solid <?= $prio['color'] ?>!important;">
            <div class="card-body p-2">
                <code class="text-muted d-block" style="font-size:.65rem;"><?= esc($task['reference']) ?></code>
                <a href="<?= base_url('admin/tasks/' . $task['id']) ?>" class="text-dark small text-decoration-none">
                    <?= esc($task['title']) ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>
</div>
