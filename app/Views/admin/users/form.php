<!-- FORMULAIRE UTILISATEUR (Création + Édition) -->
<?php $isEdit = ! empty($user['id']); ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold"><?= $isEdit ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' ?></h4>
        <?php if ($isEdit) : ?>
        <p class="text-muted mb-0"><?= esc($user['first_name']) ?> <?= esc($user['last_name']) ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST"
                      action="<?= $isEdit ? base_url('admin/users/' . $user['id'] . '/update') : base_url('admin/users/store') ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control"
                                   value="<?= esc(old('first_name', $user['first_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control"
                                   value="<?= esc(old('last_name', $user['last_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?= esc(old('phone', $user['phone'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($roles as $role) : ?>
                                <option value="<?= $role['id'] ?>"
                                    <?= old('role_id', $user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                    <?= esc($role['label']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="status" class="form-select">
                                <option value="active"   <?= old('status', $user['status'] ?? 'active') === 'active'    ? 'selected' : '' ?>>Actif</option>
                                <option value="pending"  <?= old('status', $user['status'] ?? '') === 'pending'  ? 'selected' : '' ?>>En attente</option>
                                <option value="suspended"<?= old('status', $user['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspendu</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Mot de passe <?= $isEdit ? '<span class="text-muted fw-normal small">(laisser vide = inchangé)</span>' : '<span class="text-danger">*</span>' ?>
                            </label>
                            <input type="password" name="password" class="form-control"
                                   autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' ?>
                        </button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
