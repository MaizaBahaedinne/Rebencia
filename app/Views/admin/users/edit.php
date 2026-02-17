<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Modifier Utilisateur: <?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Utilisateurs</a></li>
                    <li class="breadcrumb-item active">Modifier</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

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

    <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="post" id="userForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- COLONNE GAUCHE: Infos Admin (60%) -->
            <div class="col-lg-7">
                <!-- Section 1: Informations Personnelles -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations Personnelles</h5>
                    </div>
                    <div class="card-body">
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
                                <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= old('username', $user['username']) ?>" required>
                                <small class="text-muted">Utilisé pour la connexion</small>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email', $user['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= old('phone', $user['phone']) ?>" placeholder="+216 XX XXX XXX">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Changement de Mot de Passe -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Changer le Mot de Passe</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Laissez vide si vous ne souhaitez pas modifier le mot de passe.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Nouveau Mot de Passe</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted">Minimum 8 caractères</small>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label">Confirmer Mot de Passe</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLONNE DROITE: Config (40%) -->
            <div class="col-lg-5">
                <!-- Section: Configuration -->
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
                                    <option value="<?= $role['id'] ?>" <?= old('role_id', $user['role_id']) == $role['id'] ? 'selected' : '' ?>>
                                        <?= esc($role['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select class="form-select" id="agency_id" name="agency_id">
                                <option value="">-- Non assigné --</option>
                                <?php foreach ($agencies as $agency): ?>
                                    <option value="<?= $agency['id'] ?>" <?= old('agency_id', $user['agency_id']) == $agency['id'] ? 'selected' : '' ?>>
                                        <?= esc($agency['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= old('status', $user['status']) == 'active' ? 'selected' : '' ?>>Actif</option>
                                <option value="inactive" <?= old('status', $user['status']) == 'inactive' ? 'selected' : '' ?>>Inactif</option>
                                <option value="suspended" <?= old('status', $user['status']) == 'suspended' ? 'selected' : '' ?>>Suspendu</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section: Configuration des Commissions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-percentage me-2"></i>Taux de Commission</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12">
                                <label for="commission_sale_percentage" class="form-label">
                                    Ventes (%)
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="commission_sale_percentage" 
                                           name="commission_sale_percentage" step="0.01" min="0" max="100"
                                           value="<?= old('commission_sale_percentage', $user['commission_sale_percentage'] ?? 10.00) ?>" 
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Défaut: 10%</small>
                            </div>

                            <div class="col-12">
                                <label for="commission_rent_percentage" class="form-label">
                                    Locations (%)
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="commission_rent_percentage" 
                                           name="commission_rent_percentage" step="0.01" min="0" max="100"
                                           value="<?= old('commission_rent_percentage', $user['commission_rent_percentage'] ?? 50.00) ?>" 
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Défaut: 50%</small>
                            </div>

                            <div class="col-12 pt-2 border-top">
                                <h6 class="text-muted mb-3">Répartition Commission TTC</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="agent_commission_share_sale" class="form-label">
                                    Split Ventes (Agent)
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="agent_commission_share_sale" 
                                           name="agent_commission_share_sale" step="0.01" min="0" max="100"
                                           value="<?= old('agent_commission_share_sale', $user['agent_commission_share_sale'] ?? 50.00) ?>" 
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Agent reçoit X%, Agence reçoit (100-X)%</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="agent_commission_share_rent" class="form-label">
                                    Split Locations (Agent)
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="agent_commission_share_rent" 
                                           name="agent_commission_share_rent" step="0.01" min="0" max="100"
                                           value="<?= old('agent_commission_share_rent', $user['agent_commission_share_rent'] ?? 50.00) ?>" 
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Agent reçoit X%, Agence reçoit (100-X)%</small>
                            </div>

                            <div class="col-12 pt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_commission_exceptional" 
                                           name="is_commission_exceptional" value="1"
                                           <?= old('is_commission_exceptional', $user['is_commission_exceptional'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_commission_exceptional">
                                        <strong>Profil Exceptionnel</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="commission_exceptional_note" class="form-label form-label-sm">Note</label>
                                <textarea class="form-control form-control-sm" id="commission_exceptional_note" 
                                          name="commission_exceptional_note" rows="2" 
                                          placeholder="Raison de l'exception..."><?= old('commission_exceptional_note', $user['commission_exceptional_note'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Prévisualisation Commission -->
                <div class="card shadow-sm mb-4 bg-light">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-calculator"></i> Exemple de Calcul</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="preview_amount" class="form-label small">Montant test (TND)</label>
                            <input type="number" class="form-control form-control-sm" id="preview_amount" 
                                   placeholder="Entrez un montant" value="100000" min="0" step="1000">
                        </div>

                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="preview_type" id="preview_sale" value="sale" checked>
                            <label class="btn btn-outline-primary btn-sm" for="preview_sale">
                                Vente
                            </label>

                            <input type="radio" class="btn-check" name="preview_type" id="preview_rent" value="rent">
                            <label class="btn btn-outline-success btn-sm" for="preview_rent">
                                Location
                            </label>
                        </div>

                        <div id="previewResults" class="small">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-muted">Commission HT</div>
                                    <div class="fw-bold" id="preview_ht">-</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted">TVA 19%</div>
                                    <div class="fw-bold" id="preview_vat">-</div>
                                </div>
                                <div class="col-12">
                                    <hr class="my-2">
                                </div>
                                <div class="col-12">
                                    <div class="text-muted">Total TTC</div>
                                    <div class="fw-bold text-success fs-5" id="preview_ttc">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Boutons d'Action -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                            </button>
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <?php if ($user['id'] != session()->get('user_id')): ?>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-2"></i>Supprimer l'Utilisateur
                                </button>
                            <?php endif ?>
                        </div>
                    </div>
                </div>

                <!-- Section: Statistiques -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistiques</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Inscrit le:</th>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            </tr>
                            <tr>
                                <th>Dernière modif:</th>
                                <td><?= date('d/m/Y', strtotime($user['updated_at'])) ?></td>
                            </tr>
                            <?php if (!empty($user['last_login_at'])): ?>
                            <tr>
                                <th>Dernière connexion:</th>
                                <td><?= date('d/m/Y H:i', strtotime($user['last_login_at'])) ?></td>
                            </tr>
                            <?php endif ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Calcul automatique de commission
document.getElementById('preview_amount').addEventListener('input', calculatePreview);
document.getElementById('commission_sale_percentage').addEventListener('input', calculatePreview);
document.getElementById('commission_rent_percentage').addEventListener('input', calculatePreview);
document.querySelectorAll('input[name="preview_type"]').forEach(radio => {
    radio.addEventListener('change', calculatePreview);
});

function calculatePreview() {
    const amount = parseFloat(document.getElementById('preview_amount').value) || 0;
    const type = document.querySelector('input[name="preview_type"]:checked').value;
    
    let percentage;
    if (type === 'sale') {
        percentage = parseFloat(document.getElementById('commission_sale_percentage').value) || 10;
    } else {
        percentage = parseFloat(document.getElementById('commission_rent_percentage').value) || 50;
    }

    // Calcul
    const commissionHT = (amount * percentage) / 100;
    const vat = commissionHT * 0.19;
    const commissionTTC = commissionHT + vat;

    // Format et affichage
    document.getElementById('preview_ht').textContent = formatMoney(commissionHT) + ' TND';
    document.getElementById('preview_vat').textContent = formatMoney(vat) + ' TND';
    document.getElementById('preview_ttc').textContent = formatMoney(commissionTTC) + ' TND';
}

function formatMoney(value) {
    return new Intl.NumberFormat('fr-TN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}

// Initialiser le calcul
calculatePreview();

// Validation du formulaire
document.getElementById('userForm').addEventListener('submit', function(e) {
    const requiredFields = ['first_name', 'last_name', 'username', 'email', 'role_id'];
    let hasErrors = false;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input || !input.value.trim()) {
            hasErrors = true;
        }
    });
    
    if (hasErrors) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires');
        return false;
    }
    
    // Vérifier les mots de passe
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password && password !== passwordConfirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        return false;
    }
});

function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
        window.location.href = '<?= base_url('admin/users/delete/' . $user['id']) ?>';
    }
}
</script>

<?= $this->endSection() ?>
