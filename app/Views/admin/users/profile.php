<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-user-circle me-2"></i>Mon Profil
    </h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Personnelles</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <form action="<?= base_url('admin/profile/update') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= old('first_name', $user['first_name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= old('last_name', $user['last_name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email', $user['email']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?= old('phone', $user['phone'] ?? '') ?>" placeholder="+216 XX XXX XXX">
                            </div>

                            <?php if (session()->get('role_level') >= 100): ?>
                            <div class="col-md-6">
                                <label for="agency_id" class="form-label">Agence</label>
                                <select class="form-select" id="agency_id" name="agency_id">
                                    <option value="">-- Non assigné --</option>
                                    <?php foreach ($agencies as $agency): ?>
                                        <option value="<?= $agency['id'] ?>" <?= $user['agency_id'] == $agency['id'] ? 'selected' : '' ?>>
                                            <?= esc($agency['name']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <?php endif ?>

                            <div class="col-md-12">
                                <label for="avatar" class="form-label">Photo de Profil</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Changer le Mot de Passe</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('password_error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('password_error') ?>
                        </div>
                    <?php endif ?>

                    <?php if (session()->getFlashdata('password_errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('password_errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <form action="<?= base_url('admin/profile/change-password') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="current_password" class="form-label">Mot de Passe Actuel <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>

                            <div class="col-md-6">
                                <label for="new_password" class="form-label">Nouveau Mot de Passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                       required minlength="8">
                                <small class="text-muted">Minimum 8 caractères</small>
                            </div>

                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirmer le Mot de Passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       required minlength="8">
                            </div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Changer le Mot de Passe
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Avatar -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= base_url('uploads/avatars/' . $user['avatar']) ?>" 
                             alt="Avatar" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 150px; height: 150px; font-size: 4rem;">
                            <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                        </div>
                    <?php endif ?>
                    
                    <h5 class="mb-1"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                    <p class="text-muted mb-2"><?= esc($user['role_name']) ?></p>
                    <?php if (!empty($user['agency_name'])): ?>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-building me-1"></i><?= esc($user['agency_name']) ?>
                        </p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Compte</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Nom d'utilisateur</label>
                        <p class="mb-0"><strong><?= esc($user['username']) ?></strong></p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0"><?= esc($user['email']) ?></p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="text-muted small">Statut</label>
                        <p class="mb-0">
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge bg-success">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactif</span>
                            <?php endif ?>
                        </p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="text-muted small">Membre depuis</label>
                        <p class="mb-0">
                            <i class="fas fa-calendar me-1"></i>
                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </p>
                    </div>
                    <?php if (!empty($user['hire_date'])): ?>
                        <hr>
                        <div class="mb-0">
                            <label class="text-muted small">Date d'embauche</label>
                            <p class="mb-0">
                                <i class="fas fa-briefcase me-1"></i>
                                <?= date('d/m/Y', strtotime($user['hire_date'])) ?>
                            </p>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Mes Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-building text-success me-2"></i>Propriétés</span>
                            <strong class="text-success">-</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-users text-info me-2"></i>Clients</span>
                            <strong class="text-info">-</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-handshake text-warning me-2"></i>Transactions</span>
                            <strong class="text-warning">-</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
