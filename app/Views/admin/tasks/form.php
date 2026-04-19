<?php
$isEdit  = ! empty($task['id']);
$action  = $isEdit
    ? base_url('admin/tasks/' . $task['id'] . '/update')
    : base_url('admin/tasks/store');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-lg' ?> text-primary me-2"></i>
            <?= $isEdit ? 'Modifier la tâche' : 'Nouvelle tâche' ?>
        </h4>
        <?php if ($isEdit) : ?>
        <small class="text-muted"><?= esc($task['reference'] ?? '') ?></small>
        <?php endif; ?>
    </div>
    <a href="<?= $isEdit ? base_url('admin/tasks/' . $task['id']) : base_url('admin/tasks') ?>"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">
<form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <!-- Titre -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control"
                       value="<?= esc(old('title', $task['title'] ?? '')) ?>"
                       placeholder="Description courte de la tâche…" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="6"
                          placeholder="Détails, contexte, conditions de validation…"><?= esc(old('description', $task['description'] ?? '')) ?></textarea>
            </div>

            <div class="row g-3">
                <!-- Type -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <?php foreach ($types as $k => $t) : ?>
                        <option value="<?= $k ?>" <?= old('type', $task['type'] ?? 'task') === $k ? 'selected' : '' ?>>
                            <?= $t['label'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Priorité -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Priorité <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select" required>
                        <?php foreach ($priorities as $k => $p) : ?>
                        <option value="<?= $k ?>" <?= old('priority', $task['priority'] ?? 'medium') === $k ? 'selected' : '' ?>>
                            <?= $p['label'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Statut -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Statut <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <?php foreach ($statuses as $k => $s) : ?>
                        <option value="<?= $k ?>" <?= old('status', $task['status'] ?? 'todo') === $k ? 'selected' : '' ?>>
                            <?= $s['label'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Assigné à -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Assigné à</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">— Non assigné —</option>
                        <?php foreach ($users as $u) : ?>
                        <option value="<?= $u['id'] ?>"
                            <?= old('assigned_to', $task['assigned_to'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= esc($u['first_name'] . ' ' . $u['last_name']) ?>
                            (<?= esc($u['role_label'] ?? '') ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Échéance -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Échéance</label>
                    <input type="date" name="due_date" class="form-control"
                           value="<?= esc(old('due_date', $task['due_date'] ?? '')) ?>">
                </div>

                <!-- Labels -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Labels <span class="text-muted small">(séparés par des virgules)</span></label>
                    <input type="text" name="labels" class="form-control"
                           placeholder="ex: frontend, api, urgent"
                           value="<?= esc(old('labels', $task['labels'] ?? '')) ?>">
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
            <a href="<?= $isEdit ? base_url('admin/tasks/' . $task['id']) : base_url('admin/tasks') ?>"
               class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-<?= $isEdit ? 'check-lg' : 'plus-lg' ?> me-1"></i>
                <?= $isEdit ? 'Enregistrer' : 'Créer la tâche' ?>
            </button>
        </div>
    </div>
</form>
</div>
</div>
