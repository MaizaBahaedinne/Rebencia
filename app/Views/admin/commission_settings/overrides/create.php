<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus"></i> Nouvelle Exception de Commission
    </h1>
    <a href="<?= base_url('admin/commission-settings/overrides') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Erreurs de validation :</strong>
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-layer-group"></i> Créer une Exception Personnalisée</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/commission-settings/store-override') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- Niveau d'exception -->
                    <div class="mb-4">
                        <label class="form-label">Niveau d'Exception <span class="text-danger">*</span></label>
                        <select name="override_level" id="overrideLevel" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="agency">Agence (toute une agence)</option>
                            <option value="role">Rôle (tous les utilisateurs d'un rôle)</option>
                            <option value="user">Utilisateur (un agent spécifique)</option>
                        </select>
                        <small class="text-muted">Priorité : Utilisateur > Rôle > Agence > Système</small>
                    </div>

                    <!-- Cible selon le niveau -->
                    <div id="targetAgency" class="mb-4" style="display: none;">
                        <label class="form-label">Agence <span class="text-danger">*</span></label>
                        <select name="agency_id" class="form-select">
                            <option value="">-- Sélectionner une agence --</option>
                            <?php if (!empty($agencies)): ?>
                                <?php foreach ($agencies as $agency): ?>
                                    <option value="<?= $agency['id'] ?>"><?= esc($agency['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div id="targetRole" class="mb-4" style="display: none;">
                        <label class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select">
                            <option value="">-- Sélectionner un rôle --</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= esc($role['display_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div id="targetUser" class="mb-4" style="display: none;">
                        <label class="form-label">Utilisateur <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Sélectionner un utilisateur --</option>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>">
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> 
                                        (<?= esc($user['role_name'] ?? 'N/A') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <hr class="my-4">

                    <!-- Type de transaction et bien -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Type de Transaction <span class="text-danger">*</span></label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="sale">Vente</option>
                                <option value="rent">Location</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type de Bien <span class="text-danger">*</span></label>
                            <select name="property_type" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="apartment">Appartement</option>
                                <option value="villa">Villa</option>
                                <option value="house">Maison</option>
                                <option value="land">Terrain</option>
                                <option value="commercial">Commercial</option>
                                <option value="office">Bureau</option>
                                <option value="business">Fonds de Commerce</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Commission Acheteur/Locataire -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user"></i> Commission Acheteur / Locataire
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Type</label>
                                    <select name="buyer_commission_type" class="form-select">
                                        <option value="">-- Défaut --</option>
                                        <option value="percentage">Pourcentage (%)</option>
                                        <option value="fixed">Montant Fixe (TND)</option>
                                        <option value="months">Mois de loyer</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valeur</label>
                                    <input type="number" name="buyer_commission_value" class="form-control" step="0.01" min="0" placeholder="Ex: 2.5">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">TVA (%)</label>
                                    <input type="number" name="buyer_commission_vat" class="form-control" step="0.01" min="0" value="19" placeholder="19">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commission Vendeur/Propriétaire -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-home"></i> Commission Vendeur / Propriétaire
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Type</label>
                                    <select name="seller_commission_type" class="form-select">
                                        <option value="">-- Défaut --</option>
                                        <option value="percentage">Pourcentage (%)</option>
                                        <option value="fixed">Montant Fixe (TND)</option>
                                        <option value="months">Mois de loyer</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valeur</label>
                                    <input type="number" name="seller_commission_value" class="form-control" step="0.01" min="0" placeholder="Ex: 3">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">TVA (%)</label>
                                    <input type="number" name="seller_commission_vat" class="form-control" step="0.01" min="0" value="19" placeholder="19">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label class="form-label">Notes / Justification</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Pourquoi cette exception est-elle nécessaire ?"></textarea>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('admin/commission-settings/overrides') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer l'Exception
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Aide contextuelle -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-question-circle"></i> Comment utiliser les exceptions ?</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Agence :</strong> Tous les agents de cette agence bénéficieront de ces taux</li>
                    <li><strong>Rôle :</strong> Tous les utilisateurs avec ce rôle (ex: Directeur) auront ces taux</li>
                    <li><strong>Utilisateur :</strong> Uniquement cet agent aura ces taux spécifiques</li>
                    <li class="mt-2">Laissez vide les commissions pour utiliser les valeurs par défaut</li>
                    <li>Les exceptions sont cumulables : un utilisateur peut avoir une exception personnelle ET son rôle avoir une exception</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Afficher les bons champs selon le niveau sélectionné
document.getElementById('overrideLevel').addEventListener('change', function() {
    const level = this.value;
    
    // Cacher tous les champs cibles
    document.getElementById('targetAgency').style.display = 'none';
    document.getElementById('targetRole').style.display = 'none';
    document.getElementById('targetUser').style.display = 'none';
    
    // Désactiver les champs non utilisés
    document.querySelector('select[name="agency_id"]').removeAttribute('required');
    document.querySelector('select[name="role_id"]').removeAttribute('required');
    document.querySelector('select[name="user_id"]').removeAttribute('required');
    
    // Afficher et activer le bon champ
    if (level === 'agency') {
        document.getElementById('targetAgency').style.display = 'block';
        document.querySelector('select[name="agency_id"]').setAttribute('required', 'required');
    } else if (level === 'role') {
        document.getElementById('targetRole').style.display = 'block';
        document.querySelector('select[name="role_id"]').setAttribute('required', 'required');
    } else if (level === 'user') {
        document.getElementById('targetUser').style.display = 'block';
        document.querySelector('select[name="user_id"]').setAttribute('required', 'required');
    }
});
</script>
<?= $this->endSection() ?>
