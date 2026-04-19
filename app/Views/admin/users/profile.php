
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Mon profil</h2>
        <small class="text-muted">Modifier mes informations personnelles</small>
    </div>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Informations personnelles -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <strong><i class="bi bi-person me-2"></i>Informations personnelles</strong>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/profile/update') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('first_name', $user['first_name'])) ?>" required>
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['first_name'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('last_name', $user['last_name'])) ?>" required>
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['last_name'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('email', $user['email'])) ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="phone" class="form-control"
                                   value="<?= esc(old('phone', $user['phone'] ?? '')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Avatar</label>
                            <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPEG, PNG ou WebP — max 2 Mo</div>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Avatar + Changer mot de passe -->
    <div class="col-lg-4">
        <!-- Avatar actuel -->
        <div class="card border-0 shadow-sm mb-3 text-center">
            <div class="card-body p-4">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= site_url('uploads/avatars/' . $user['avatar']) ?>"
                         class="rounded-circle mb-3" width="80" height="80" style="object-fit:cover">
                <?php else: ?>
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:80px;height:80px;font-size:2rem;">
                        <?= strtoupper(mb_substr($user['first_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <p class="mb-0 fw-semibold"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></p>
                <small class="text-muted"><?= esc($user['role_name'] ?? '') ?></small>
            </div>
        </div>

        <!-- Changer mot de passe -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <strong><i class="bi bi-key me-2"></i>Changer le mot de passe</strong>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/profile/password') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" required>
                        <?php if (isset($errors['current_password'])): ?>
                            <div class="invalid-feedback"><?= $errors['current_password'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="new_password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>"
                               minlength="8" required>
                        <?php if (isset($errors['new_password'])): ?>
                            <div class="invalid-feedback"><?= $errors['new_password'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                    </div>
                    <button type="submit" class="btn btn-outline-warning w-100">
                        <i class="bi bi-shield-lock me-1"></i> Mettre à jour
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

