<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nouvel Utilisateur</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Utilisateurs</a></li>
                    <li class="breadcrumb-item active">Nouveau</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreurs de validation:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <form action="<?= base_url('admin/users/store') ?>" method="post" id="userForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Section 1: Informations Personnelles -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations Personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= old('first_name') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= old('last_name') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= old('username') ?>" required>
                                <small class="text-muted">Utilisé pour la connexion</small>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= old('phone') ?>" placeholder="+216 XX XXX XXX">
                            </div>
                            <div class="col-md-6">
                                <label for="cin" class="form-label">CIN</label>
                                <input type="text" class="form-control" id="cin" name="cin" 
                                       value="<?= old('cin') ?>" placeholder="12345678">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Sécurité -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Sécurité</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mot de Passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">Minimum 8 caractères</small>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label">Confirmer Mot de Passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Sécurité:</strong> Le mot de passe doit contenir au moins 8 caractères avec majuscules, minuscules et chiffres.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Rôle et Attribution -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Rôle <span class="text-danger">*</span></label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">-- Sélectionner un rôle --</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                        <?= esc($role['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <small class="text-muted">Définit les permissions de l'utilisateur</small>
                        </div>

                        <div class="mb-3">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select class="form-select" id="agency_id" name="agency_id">
                                <option value="">-- Non assigné --</option>
                                <?php foreach ($agencies as $agency): ?>
                                    <option value="<?= $agency['id'] ?>" <?= old('agency_id') == $agency['id'] ? 'selected' : '' ?>>
                                        <?= esc($agency['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Actif</option>
                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactif</option>
                                <option value="suspended" <?= old('status') == 'suspended' ? 'selected' : '' ?>>Suspendu</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="hire_date" class="form-label">Date d'Embauche</label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                   value="<?= old('hire_date', date('Y-m-d')) ?>">
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer l'Utilisateur
                            </button>
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Rôles -->
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Rôles Disponibles</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li><strong>Super Admin:</strong> Accès complet</li>
                            <li><strong>Admin Système:</strong> Gestion système</li>
                            <li><strong>Admin Agence:</strong> Gestion agence</li>
                            <li><strong>Superviseur:</strong> Supervision équipe</li>
                            <li><strong>Agent Senior:</strong> Transactions avancées</li>
                            <li><strong>Agent:</strong> Gestion clients/biens</li>
                            <li><strong>Agent Junior:</strong> Accès limité</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
