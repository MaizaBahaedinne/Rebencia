<?php
$prio   = $priorities[$task['priority']] ?? ['label' => $task['priority'], 'color' => '#6c757d', 'icon' => 'bi-dash'];
$type   = $types[$task['type']]          ?? ['label' => $task['type'], 'icon' => 'bi-tag', 'color' => '#6c757d'];
$status = $statuses[$task['status']]     ?? ['label' => $task['status'], 'color' => 'secondary'];
$overdue = ! empty($task['due_date']) && strtotime($task['due_date']) < time()
           && ! in_array($task['status'], ['done','cancelled']);
?>
<!-- ENTÊTE TÂCHE -->
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <code class="text-muted"><?= esc($task['reference']) ?></code>
            <span class="badge bg-<?= $status['color'] ?>"><?= $status['label'] ?></span>
            <span class="badge" style="background:<?= $type['color'] ?>;">
                <i class="<?= $type['icon'] ?> me-1"></i><?= $type['label'] ?>
            </span>
            <span class="badge" style="background:<?= $prio['color'] ?>;">
                <i class="<?= $prio['icon'] ?> me-1"></i><?= $prio['label'] ?>
            </span>
        </div>
        <h4 class="mb-0 fw-bold"><?= esc($task['title']) ?></h4>
        <small class="text-muted">
            Créé par <?= esc($task['creator_first'] . ' ' . $task['creator_last']) ?>
            &bull; <?= date('d/m/Y à H:i', strtotime($task['created_at'])) ?>
        </small>
    </div>
    <div class="d-flex gap-2">
        <?php if ($auth->hasPermission('tasks.edit')) : ?>
        <a href="<?= base_url('admin/tasks/' . $task['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/tasks') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Colonne principale -->
    <div class="col-lg-8">

        <!-- Description -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent"><strong><i class="bi bi-card-text me-2"></i>Description</strong></div>
            <div class="card-body">
                <?php if (! empty($task['description'])) : ?>
                <div style="white-space:pre-wrap;font-size:.9rem;"><?= esc($task['description']) ?></div>
                <?php else : ?>
                <p class="text-muted mb-0"><em>Aucune description.</em></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Changer statut rapide -->
        <?php if ($auth->hasPermission('tasks.edit')) : ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent"><strong><i class="bi bi-arrow-repeat me-2"></i>Changer le statut</strong></div>
            <div class="card-body d-flex flex-wrap gap-2">
                <?php foreach ($statuses as $k => $s) : ?>
                <button type="button"
                        class="btn btn-sm <?= $task['status'] === $k ? 'btn-' . $s['color'] : 'btn-outline-' . $s['color'] ?> status-btn"
                        data-status="<?= $k ?>"
                        data-task="<?= $task['id'] ?>">
                    <?= $s['label'] ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Commentaires -->
        <div class="card border-0 shadow-sm" id="comments">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-chat-dots me-2"></i>Commentaires</strong>
                <span class="badge bg-secondary"><?= count($task['comments']) ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($task['comments'])) : ?>
                <p class="text-muted text-center mb-3"><em>Aucun commentaire pour l'instant.</em></p>
                <?php endif; ?>
                <?php foreach ($task['comments'] as $c) : ?>
                <div class="d-flex gap-3 mb-3">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width:34px;height:34px;font-size:.75rem;">
                        <?= strtoupper(substr($c['first_name'], 0, 1)) ?>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <strong class="small"><?= esc($c['first_name'] . ' ' . $c['last_name']) ?></strong>
                            <span class="text-muted" style="font-size:.75rem;"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
                        </div>
                        <div class="bg-light rounded p-2" style="font-size:.875rem;white-space:pre-wrap;"><?= esc($c['content']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Formulaire commentaire -->
                <form method="post" action="<?= base_url('admin/tasks/' . $task['id'] . '/comment') ?>" class="mt-3">
                    <?= csrf_field() ?>
                    <textarea name="content" class="form-control mb-2" rows="3"
                              placeholder="Écrire un commentaire…" required></textarea>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-send me-1"></i> Commenter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Colonne métadonnées -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent"><strong>Détails</strong></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Assigné à</span>
                    <?php if (! empty($task['assignee_first'])) : ?>
                    <strong class="small"><?= esc($task['assignee_first'] . ' ' . $task['assignee_last']) ?></strong>
                    <?php else : ?>
                    <span class="text-muted small"><em>Non assigné</em></span>
                    <?php endif; ?>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Échéance</span>
                    <?php if (! empty($task['due_date'])) : ?>
                    <span class="small <?= $overdue ? 'text-danger fw-bold' : '' ?>">
                        <i class="bi bi-calendar<?= $overdue ? '-x' : '' ?> me-1"></i>
                        <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                        <?= $overdue ? '(En retard)' : '' ?>
                    </span>
                    <?php else : ?>
                    <span class="text-muted small">—</span>
                    <?php endif; ?>
                </li>
                <?php if (! empty($task['labels'])) : ?>
                <li class="list-group-item">
                    <div class="text-muted small mb-1">Labels</div>
                    <?php foreach (explode(',', $task['labels']) as $label) : ?>
                    <span class="badge bg-light text-dark border me-1"><?= esc(trim($label)) ?></span>
                    <?php endforeach; ?>
                </li>
                <?php endif; ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted small">Mis à jour</span>
                    <span class="small"><?= date('d/m/Y H:i', strtotime($task['updated_at'])) ?></span>
                </li>
            </ul>
        </div>

        <?php if ($auth->hasPermission('tasks.delete')) : ?>
        <form method="post" action="<?= base_url('admin/tasks/' . $task['id'] . '/delete') ?>"
              onsubmit="return confirm('Supprimer cette tâche ?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                <i class="bi bi-trash me-1"></i> Supprimer la tâche
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

document.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const taskId = this.dataset.task;
        const status = this.dataset.status;

        fetch(`/admin/tasks/${taskId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: new URLSearchParams({ status, [document.querySelector('input[name="csrf_token_name"]')?.name || 'csrf_rebencia']: csrfToken })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    });
});
</script>
