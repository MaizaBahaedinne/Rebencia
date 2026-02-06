<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/properties') ?>">Biens</a></li>
        <li class="breadcrumb-item active">Nouveau bien</li>
    </ol>
</nav>

<!-- Page Title -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus-circle text-primary"></i> Nouveau Bien
    </h1>
    <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif ?>

<form action="<?= base_url('admin/properties/store') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Informations principales -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary"></i> Informations principales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du bien <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" 
                               value="<?= old('title') ?>" required placeholder="Ex: Villa moderne avec piscine">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description détaillée</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Décrivez le bien en détail..."><?= old('description') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type de bien <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">-- Choisir le type --</option>
                                <option value="apartment" <?= old('type') == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                <option value="villa" <?= old('type') == 'villa' ? 'selected' : '' ?>>Villa</option>
                                <option value="house" <?= old('type') == 'house' ? 'selected' : '' ?>>Maison</option>
                                <option value="land" <?= old('type') == 'land' ? 'selected' : '' ?>>Terrain</option>
                                <option value="commercial" <?= old('type') == 'commercial' ? 'selected' : '' ?>>Local commercial</option>
                                <option value="office" <?= old('type') == 'office' ? 'selected' : '' ?>>Bureau</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="transaction_type" class="form-label">Type de transaction <span class="text-danger">*</span></label>
                            <select class="form-select" id="transaction_type" name="transaction_type" required>
                                <option value="">-- Choisir --</option>
                                <option value="sale" <?= old('transaction_type') == 'sale' ? 'selected' : '' ?>>Vente</option>
                                <option value="rent" <?= old('transaction_type') == 'rent' ? 'selected' : '' ?>>Location</option>
                                <option value="both" <?= old('transaction_type') == 'both' ? 'selected' : '' ?>>Vente ou Location</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt text-info"></i> Localisation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="zone_id" class="form-label">Zone géographique</label>
                        <select class="form-select" id="zone_id" name="zone_id">
                            <option value="">-- Sélectionner une zone --</option>
                            <?php foreach ($zones as $zone): ?>
                                <option value="<?= $zone['id'] ?>" <?= old('zone_id') == $zone['id'] ? 'selected' : '' ?>>
                                    <?= esc($zone['name']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse complète</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= old('address') ?>" placeholder="Ex: 15 Avenue Habib Bourguiba, Tunis">
                    </div>
                </div>
            </div>

            <!-- Caractéristiques & Prix -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-home text-success"></i> Caractéristiques & Prix
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Prix de vente <small class="text-muted">(TND)</small></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?= old('price') ?>" step="0.01">
                                <span class="input-group-text">TND</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="rent_price" class="form-label">Prix de location <small class="text-muted">(TND/mois)</small></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="rent_price" name="rent_price" 
                                       value="<?= old('rent_price') ?>" step="0.01">
                                <span class="input-group-text">TND/mois</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="area" class="form-label">Surface <small class="text-muted">(m²)</small></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="area" name="area" value="<?= old('area') ?>">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="bedrooms" class="form-label">Chambres</label>
                            <input type="number" class="form-control" id="bedrooms" name="bedrooms" 
                                   value="<?= old('bedrooms', 0) ?>" min="0">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="bathrooms" class="form-label">Salles de bain</label>
                            <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                   value="<?= old('bathrooms', 0) ?>" min="0">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="parking" class="form-label">Parkings</label>
                            <input type="number" class="form-control" id="parking" name="parking" 
                                   value="<?= old('parking', 0) ?>" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Médias -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-camera text-warning"></i> Médias
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="images" class="form-label">Photos du bien</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 5MB par image.</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-save"></i> Enregistrer le bien
                </button>
                <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary btn-lg px-4">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card card-modern sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-question-circle text-secondary"></i> Aide
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Conseils pour ajouter un bien :</h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Choisissez un titre descriptif et attractif</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ajoutez une description détaillée</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Indiquez la zone géographique précise</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Renseignez toutes les caractéristiques importantes</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ajoutez des photos de qualité</li>
                    </ul>
                    
                    <hr>
                    
                    <div class="alert alert-light border-0 mb-0">
                        <small class="text-muted">
                            <i class="fas fa-asterisk text-danger me-1"></i> Champs obligatoires
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>