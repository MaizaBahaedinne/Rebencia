<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/properties') ?>">Biens</a></li>
        <li class="breadcrumb-item active">Modifier #<?= $property['reference'] ?? $property['id'] ?></li>
    </ol>
</nav>

<!-- Page Title -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit text-warning"></i> Modifier le Bien
        <small class="text-muted d-block mt-1"><?= esc($property['title']) ?></small>
    </h1>
    <div>
        <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" class="btn btn-info">
            <i class="fas fa-eye"></i> Voir
        </a>
    </div>
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

<form action="<?= base_url('admin/properties/update/' . $property['id']) ?>" method="post" enctype="multipart/form-data">
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
                               value="<?= old('title', $property['title']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description détaillée</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= old('description', $property['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type de bien <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="apartment" <?= old('type', $property['type']) == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                <option value="villa" <?= old('type', $property['type']) == 'villa' ? 'selected' : '' ?>>Villa</option>
                                <option value="house" <?= old('type', $property['type']) == 'house' ? 'selected' : '' ?>>Maison</option>
                                <option value="land" <?= old('type', $property['type']) == 'land' ? 'selected' : '' ?>>Terrain</option>
                                <option value="commercial" <?= old('type', $property['type']) == 'commercial' ? 'selected' : '' ?>>Local commercial</option>
                                <option value="office" <?= old('type', $property['type']) == 'office' ? 'selected' : '' ?>>Bureau</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="transaction_type" class="form-label">Type de transaction <span class="text-danger">*</span></label>
                            <select class="form-select" id="transaction_type" name="transaction_type" required>
                                <option value="sale" <?= old('transaction_type', $property['transaction_type']) == 'sale' ? 'selected' : '' ?>>Vente</option>
                                <option value="rent" <?= old('transaction_type', $property['transaction_type']) == 'rent' ? 'selected' : '' ?>>Location</option>
                                <option value="both" <?= old('transaction_type', $property['transaction_type']) == 'both' ? 'selected' : '' ?>>Vente ou Location</option>
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
                                <option value="<?= $zone['id'] ?>" <?= old('zone_id', $property['zone_id']) == $zone['id'] ? 'selected' : '' ?>>
                                    <?= esc($zone['name']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse complète</label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= old('address', $property['address']) ?>">
                    </div>
                    
                    <!-- Carte de localisation -->
                    <div class="mb-3">
                        <label class="form-label">Position GPS</label>
                        <div id="map" style="height: 400px; width: 100%; border-radius: 8px; overflow: hidden;"></div>
                        <small class="text-muted">Cliquez sur la carte pour définir la position exacte du bien</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" 
                                   value="<?= old('latitude', $property['latitude'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" 
                                   value="<?= old('longitude', $property['longitude'] ?? '') ?>" readonly>
                        </div>
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
                                       value="<?= old('price', $property['price']) ?>" step="0.01">
                                <span class="input-group-text">TND</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="rent_price" class="form-label">Prix de location <small class="text-muted">(TND/mois)</small></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="rent_price" name="rent_price" 
                                       value="<?= old('rent_price', $property['rental_price'] ?? $property['rent_price'] ?? '') ?>" step="0.01">
                                <span class="input-group-text">TND/mois</span>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3"><i class="fas fa-user-tie text-info"></i> Informations du Propriétaire</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="owner_name" class="form-label">Nom du propriétaire</label>
                            <input type="text" class="form-control" id="owner_name" name="owner_name" 
                                   value="<?= old('owner_name', $property['owner_name'] ?? '') ?>" placeholder="Nom complet du propriétaire">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="owner_phone" class="form-label">Téléphone du propriétaire</label>
                            <input type="tel" class="form-control" id="owner_phone" name="owner_phone" 
                                   value="<?= old('owner_phone', $property['owner_phone'] ?? '') ?>" placeholder="Ex: +216 12 345 678">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="owner_email" class="form-label">Email du propriétaire</label>
                            <input type="email" class="form-control" id="owner_email" name="owner_email" 
                                   value="<?= old('owner_email', $property['owner_email'] ?? '') ?>" placeholder="email@exemple.com">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="area" class="form-label">Surface <small class="text-muted">(m²)</small></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="area" name="area" 
                                       value="<?= old('area', $property['area_total'] ?? $property['area'] ?? '') ?>">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="bedrooms" class="form-label">Chambres</label>
                            <input type="number" class="form-control" id="bedrooms" name="bedrooms" 
                                   value="<?= old('bedrooms', $property['bedrooms']) ?>" min="0">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="bathrooms" class="form-label">Salles de bain</label>
                            <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                   value="<?= old('bathrooms', $property['bathrooms']) ?>" min="0">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="parking" class="form-label">Parkings</label>
                            <input type="number" class="form-control" id="parking" name="parking" 
                                   value="<?= old('parking', $property['parking'] ?? 0) ?>" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statut & Options -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-toggle-on text-secondary"></i> Statut & Options
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" <?= old('is_active', $property['status'] ?? 'available') == 'available' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-eye text-success"></i> Bien actif (visible)
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                       value="1" <?= old('is_featured', $property['featured'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_featured">
                                    <i class="fas fa-star text-warning"></i> Bien à la une
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nouvelles photos -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-camera text-warning"></i> Ajouter des photos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="images" class="form-label">Nouvelles photos</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 5MB par image.</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button type="submit" class="btn btn-warning btn-lg px-4">
                    <i class="fas fa-save"></i> Mettre à jour le bien
                </button>
                <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary btn-lg px-4">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Photos actuelles -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-images text-info"></i> Photos actuelles
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($property['images'])): ?>
                        <div class="row g-2">
                            <?php foreach ($property['images'] as $image): ?>
                                <div class="col-6">
                                    <div class="card border-0 shadow-sm">
                                        <img src="<?= base_url('uploads/properties/' . $image['filename']) ?>" 
                                             class="card-img-top rounded" style="height: 120px; object-fit: cover;">
                                        <div class="card-body p-2 text-center">
                                            <a href="<?= base_url('admin/properties/' . $property['id'] . '/delete-image/' . $image['id']) ?>" 
                                               class="btn btn-outline-danger btn-sm" 
                                               onclick="return confirm('Supprimer cette image ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-image fa-3x mb-3 opacity-50"></i>
                            <p>Aucune image</p>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card card-modern">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-success"></i> Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <div class="flex-shrink-0">
                            <i class="fas fa-eye fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold text-primary">Vues</div>
                            <div class="h4 mb-0"><?= $property['views'] ?? 0 ?></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="fas fa-clock text-muted me-2"></i>
                            <strong>Créé le :</strong> <?= date('d/m/Y H:i', strtotime($property['created_at'])) ?>
                        </div>
                        <div>
                            <i class="fas fa-edit text-muted me-2"></i>
                            <strong>Modifié le :</strong> <?= date('d/m/Y H:i', strtotime($property['updated_at'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Onglets des extensions -->

<?= $this->endSection() ?>