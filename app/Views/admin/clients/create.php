<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nouveau Client</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/clients') ?>">Clients</a></li>
                    <li class="breadcrumb-item active">Nouveau</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('admin/clients') ?>" class="btn btn-outline-secondary">
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

    <form action="<?= base_url('admin/clients/store') ?>" method="post" id="clientForm">
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
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= old('phone') ?>" placeholder="+216 XX XXX XXX" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone_secondary" class="form-label">Téléphone Secondaire</label>
                                <input type="tel" class="form-control" id="phone_secondary" name="phone_secondary" 
                                       value="<?= old('phone_secondary') ?>" placeholder="+216 XX XXX XXX">
                            </div>
                            <div class="col-md-6">
                                <label for="cin" class="form-label">CIN</label>
                                <input type="text" class="form-control" id="cin" name="cin" 
                                       value="<?= old('cin') ?>" placeholder="12345678">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Adresse</label>
                                <textarea class="form-control" id="address" name="address" rows="2"><?= old('address') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Préférences de Recherche -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Préférences de Recherche</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="property_type_preference" class="form-label">Type de Bien Souhaité</label>
                                <select class="form-select" id="property_type_preference" name="property_type_preference">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="apartment" <?= old('property_type_preference') == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                    <option value="villa" <?= old('property_type_preference') == 'villa' ? 'selected' : '' ?>>Villa</option>
                                    <option value="house" <?= old('property_type_preference') == 'house' ? 'selected' : '' ?>>Maison</option>
                                    <option value="land" <?= old('property_type_preference') == 'land' ? 'selected' : '' ?>>Terrain</option>
                                    <option value="commercial" <?= old('property_type_preference') == 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                    <option value="office" <?= old('property_type_preference') == 'office' ? 'selected' : '' ?>>Bureau</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="transaction_type_preference" class="form-label">Type de Transaction</label>
                                <select class="form-select" id="transaction_type_preference" name="transaction_type_preference">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="sale" <?= old('transaction_type_preference') == 'sale' ? 'selected' : '' ?>>Achat</option>
                                    <option value="rent" <?= old('transaction_type_preference') == 'rent' ? 'selected' : '' ?>>Location</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="budget_min" class="form-label">Budget Minimum (TND)</label>
                                <input type="number" class="form-control" id="budget_min" name="budget_min" 
                                       value="<?= old('budget_min') ?>" step="1000">
                            </div>
                            <div class="col-md-6">
                                <label for="budget_max" class="form-label">Budget Maximum (TND)</label>
                                <input type="number" class="form-control" id="budget_max" name="budget_max" 
                                       value="<?= old('budget_max') ?>" step="1000">
                            </div>
                            <div class="col-md-6">
                                <label for="preferred_zones" class="form-label">Zones Préférées</label>
                                <select class="form-select" id="preferred_zones" name="preferred_zones[]" multiple size="5">
                                    <?php foreach ($zones as $zone): ?>
                                        <option value="<?= $zone['id'] ?>"><?= esc($zone['name']) ?></option>
                                    <?php endforeach ?>
                                </select>
                                <small class="text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs zones</small>
                            </div>
                            <div class="col-md-6">
                                <label for="area_preference" class="form-label">Surface Souhaitée (m²)</label>
                                <input type="number" class="form-control" id="area_preference" name="area_preference" 
                                       value="<?= old('area_preference') ?>" step="10">
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes & Commentaires</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Statut et Attribution -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type de Client <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="buyer" <?= old('type') == 'buyer' ? 'selected' : '' ?>>Acheteur</option>
                                <option value="seller" <?= old('type') == 'seller' ? 'selected' : '' ?>>Vendeur</option>
                                <option value="tenant" <?= old('type') == 'tenant' ? 'selected' : '' ?>>Locataire</option>
                                <option value="landlord" <?= old('type') == 'landlord' ? 'selected' : '' ?>>Bailleur</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="source" class="form-label">Source</label>
                            <select class="form-select" id="source" name="source">
                                <option value="website" <?= old('source') == 'website' ? 'selected' : '' ?>>Site Web</option>
                                <option value="referral" <?= old('source') == 'referral' ? 'selected' : '' ?>>Recommandation</option>
                                <option value="social_media" <?= old('source') == 'social_media' ? 'selected' : '' ?>>Réseaux Sociaux</option>
                                <option value="walk_in" <?= old('source') == 'walk_in' ? 'selected' : '' ?>>Visite Spontanée</option>
                                <option value="phone" <?= old('source') == 'phone' ? 'selected' : '' ?>>Téléphone</option>
                                <option value="other" <?= old('source') == 'other' ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="lead" <?= old('status') == 'lead' ? 'selected' : '' ?>>Prospect</option>
                                <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Actif</option>
                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactif</option>
                                <option value="converted" <?= old('status') == 'converted' ? 'selected' : '' ?>>Converti</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="assigned_agent_id" class="form-label">Agent Assigné</label>
                            <select class="form-select" id="assigned_agent_id" name="assigned_agent_id">
                                <option value="">-- Auto-assignation --</option>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?= $agent['id'] ?>" <?= old('assigned_agent_id') == $agent['id'] ? 'selected' : '' ?>>
                                        <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select class="form-select" id="agency_id" name="agency_id">
                                <option value="">-- Agence par défaut --</option>
                                <?php foreach ($agencies as $agency): ?>
                                    <option value="<?= $agency['id'] ?>" <?= old('agency_id') == $agency['id'] ? 'selected' : '' ?>>
                                        <?= esc($agency['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer le Client
                            </button>
                            <a href="<?= base_url('admin/clients') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
