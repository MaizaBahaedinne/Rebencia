<!-- FORMULAIRE UTILISATEUR (Création + Édition) -->
<?php $isEdit = ! empty($user['id']); ?>
<?php $currentRoleIds = old('role_ids', $userRoleIds ?? []); ?>

<style>
.role-card { cursor:pointer; border:2px solid #dee2e6; border-radius:.75rem; transition:border-color .15s,background .15s; }
.role-card:hover { border-color:#0d6efd; background:#f0f5ff; }
.role-card input[type=checkbox]:checked ~ .role-card-inner,
.role-card.checked { border-color:#0d6efd; background:#eef3ff; }
.role-card input[type=checkbox] { position:absolute;opacity:0;width:0;height:0; }
.avatar-preview { width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid #dee2e6; }
.avatar-initials { width:100px;height:100px;border-radius:50%;background:var(--rb-primary,#1a3c5e);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:#fff;border:3px solid #dee2e6; }
</style>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="mb-0 fw-bold"><?= $isEdit ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' ?></h4>
        <?php if ($isEdit): ?>
        <p class="text-muted mb-0 small"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></p>
        <?php endif; ?>
    </div>
</div>

<form method="POST" enctype="multipart/form-data"
      action="<?= $isEdit ? base_url('admin/users/' . $user['id'] . '/update') : base_url('admin/users/store') ?>">
    <?= csrf_field() ?>

    <div class="row g-4">

        <!-- ── Colonne gauche : avatar + infos ── -->
        <div class="col-lg-4">

            <!-- Avatar -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-image me-2"></i>Photo de profil</div>
                <div class="card-body text-center">
                    <div class="mb-3" id="avatarWrap">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= base_url(esc($user['avatar'])) ?>" class="avatar-preview" id="avatarPreview" alt="Avatar">
                        <?php else: ?>
                        <div class="avatar-initials mx-auto" id="avatarInitials">
                            <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <img src="" class="avatar-preview mx-auto d-none" id="avatarPreview" alt="Avatar">
                        <?php endif; ?>
                    </div>

                    <label for="avatarInput" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="bi bi-upload me-1"></i>Choisir une photo
                    </label>
                    <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/gif,image/webp"
                           class="d-none" onchange="previewAvatar(this)">
                    <small class="text-muted d-block">JPG, PNG, GIF, WEBP — max 2 Mo</small>

                    <?php if ($isEdit && !empty($user['avatar'])): ?>
                    <div class="form-check mt-2 justify-content-center d-flex gap-2">
                        <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="removeAvatar">
                        <label class="form-check-label text-danger small" for="removeAvatar">Supprimer la photo</label>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statut -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-toggle-on me-2"></i>Statut</div>
                <div class="card-body">
                    <?php foreach (['active'=>['Actif','success'],'pending'=>['En attente','warning'],'suspended'=>['Suspendu','danger']] as $val=>[$lbl,$col]): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="status" id="status_<?= $val ?>" value="<?= $val ?>"
                               <?= old('status', $user['status'] ?? 'active') === $val ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status_<?= $val ?>">
                            <span class="badge bg-<?= $col ?> me-1"><?= $lbl ?></span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ── Colonne droite : champs + rôles ── -->
        <div class="col-lg-8">

            <!-- Informations -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-person me-2"></i>Informations</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control"
                                   value="<?= esc(old('first_name', $user['first_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control"
                                   value="<?= esc(old('last_name', $user['last_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?= esc(old('phone', $user['phone'] ?? '')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Mot de passe
                                <?= $isEdit
                                    ? '<span class="text-muted fw-normal small">(laisser vide = inchangé)</span>'
                                    : '<span class="text-danger">*</span>' ?>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="pwdInput" class="form-control"
                                       autocomplete="new-password" <?= $isEdit ? '' : 'required' ?> minlength="8">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePwd()">
                                    <i class="bi bi-eye" id="pwdEyeIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agence -->
            <?php if (! empty($agencies ?? [])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-buildings me-2 text-primary"></i>Agence de rattachement</div>
                <div class="card-body">
                    <select name="agency_id" class="form-select">
                        <option value="">— Sans agence (admin global) —</option>
                        <?php foreach ($agencies as $ag): ?>
                        <option value="<?= $ag['id'] ?>"
                            <?= (int) old('agency_id', $user['agency_id'] ?? 0) === (int) $ag['id'] ? 'selected' : '' ?>>
                            <?= esc($ag['name']) ?><?= $ag['city'] ? ' (' . esc($ag['city']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Les biens et la visibilité de cet utilisateur seront limités à cette agence.</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Rôles -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="bi bi-shield-check me-2"></i>Rôles assignés</span>
                    <small class="text-muted">Le premier coché = rôle principal</small>
                </div>
                <div class="card-body">
                    <div class="row g-2" id="roleCards">
                        <?php foreach ($roles as $role):
                            $checked  = in_array((int)$role['id'], array_map('intval', $currentRoleIds));
                            $color    = $role['color'] ?? '#6c757d';
                            $label    = esc($role['label'] ?? $role['name']);
                        ?>
                        <div class="col-sm-6 col-md-4">
                            <label class="role-card d-block p-3 position-relative <?= $checked ? 'checked' : '' ?>"
                                   onclick="toggleRoleCard(this)">
                                <input type="checkbox" name="role_ids[]" value="<?= $role['id'] ?>"
                                       <?= $checked ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill" style="background:<?= esc($color) ?>;width:10px;height:10px;padding:0;flex-shrink:0;"></span>
                                    <span class="fw-semibold" style="font-size:.875rem;"><?= $label ?></span>
                                </div>
                                <?php if (!empty($role['description'] ?? '')): ?>
                                <div class="text-muted mt-1" style="font-size:.75rem;"><?= esc($role['description']) ?></div>
                                <?php endif; ?>
                                <i class="bi bi-check-circle-fill text-primary position-absolute top-0 end-0 m-2 role-check <?= $checked ? '' : 'd-none' ?>"
                                   style="font-size:1rem;"></i>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="noRoleWarning" class="alert alert-warning mt-2 py-2 d-none small">
                        <i class="bi bi-exclamation-triangle me-1"></i>Veuillez sélectionner au moins un rôle.
                    </div>
                    <div class="mt-2 small text-muted" id="primaryRoleInfo">
                        <?php if (!empty($currentRoleIds)):
                            $primary = array_filter($roles, fn($r) => (int)$r['id'] === (int)($currentRoleIds[0] ?? 0));
                            $primary = reset($primary);
                        ?>
                        Rôle principal actuel : <strong><?= esc($primary['label'] ?? $primary['name'] ?? '—') ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>
                    <?= $isEdit ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' ?>
                </button>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </div>
    </div>
</form>

<script>
function previewAvatar(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const prev = document.getElementById('avatarPreview');
        const init = document.getElementById('avatarInitials');
        if (prev) { prev.src = e.target.result; prev.classList.remove('d-none'); }
        if (init) init.classList.add('d-none');
    };
    reader.readAsDataURL(file);
}

function toggleRoleCard(label) {
    const cb = label.querySelector('input[type=checkbox]');
    cb.checked = !cb.checked;
    label.classList.toggle('checked', cb.checked);
    const icon = label.querySelector('.role-check');
    if (icon) icon.classList.toggle('d-none', !cb.checked);
    validateRoles();
}

function validateRoles() {
    const any = document.querySelectorAll('#roleCards input[type=checkbox]:checked').length > 0;
    document.getElementById('noRoleWarning').classList.toggle('d-none', any);
}

function togglePwd() {
    const inp  = document.getElementById('pwdInput');
    const icon = document.getElementById('pwdEyeIcon');
    if (inp.type === 'password') { inp.type = 'text'; icon.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; icon.className = 'bi bi-eye'; }
}

// Prevent submit if no role selected
document.querySelector('form').addEventListener('submit', function(e) {
    const any = document.querySelectorAll('#roleCards input[type=checkbox]:checked').length > 0;
    if (!any) { e.preventDefault(); validateRoles(); }
});
</script>
