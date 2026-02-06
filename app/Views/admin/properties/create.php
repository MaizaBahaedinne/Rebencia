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
    
    <!-- Wizard Simple -->
    <div class="card mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="wizardTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-step="1" href="#step1">
                        <i class="fas fa-info-circle"></i> Infos générales
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-step="2" href="#step2">
                        <i class="fas fa-map-marker-alt"></i> Localisation & Carte
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-step="3" href="#step3">
                        <i class="fas fa-home"></i> Caractéristiques
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-step="4" href="#step4">
                        <i class="fas fa-images"></i> Médias
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Étape 1: Infos générales -->
                <div class="tab-pane fade show active" id="step1">
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
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Prix de vente <small class="text-muted">(TND)</small></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?= old('price') ?>" step="0.01">
                                <span class="input-group-text">TND</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="rent_price" class="form-label">Prix de location <small class="text-muted">(TND/mois)</small></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="rent_price" name="rent_price" 
                                       value="<?= old('rent_price') ?>" step="0.01">
                                <span class="input-group-text">TND/mois</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Étape 2: Localisation & Carte -->
                <div class="tab-pane fade" id="step2">
                    <div class="row">
                        <div class="col-md-6">
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
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" class="form-control" id="latitude" name="latitude" 
                                           value="<?= old('latitude') ?>" step="any" placeholder="36.8065">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" class="form-control" id="longitude" name="longitude" 
                                           value="<?= old('longitude') ?>" step="any" placeholder="10.1815">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary" id="getLocationBtn">
                                    <i class="fas fa-map-marker-alt"></i> Obtenir ma position
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="searchAddressBtn">
                                    <i class="fas fa-search"></i> Géocoder l'adresse
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Carte (cliquez pour placer le marqueur)</label>
                            <div id="map" style="height: 300px; border-radius: 8px;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Étape 3: Caractéristiques -->
                <div class="tab-pane fade" id="step3">
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
                    
                    <hr>
                    
                    <h5>Équipements</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="swimming_pool" name="amenities[]" value="swimming_pool">
                                <label class="form-check-label" for="swimming_pool">
                                    <i class="fas fa-swimmer text-info"></i> Piscine
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="garden" name="amenities[]" value="garden">
                                <label class="form-check-label" for="garden">
                                    <i class="fas fa-tree text-success"></i> Jardin
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="garage" name="amenities[]" value="garage">
                                <label class="form-check-label" for="garage">
                                    <i class="fas fa-car text-secondary"></i> Garage
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="terrace" name="amenities[]" value="terrace">
                                <label class="form-check-label" for="terrace">
                                    <i class="fas fa-building text-warning"></i> Terrasse
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="elevator" name="amenities[]" value="elevator">
                                <label class="form-check-label" for="elevator">
                                    <i class="fas fa-elevator text-primary"></i> Ascenseur
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="air_conditioning" name="amenities[]" value="air_conditioning">
                                <label class="form-check-label" for="air_conditioning">
                                    <i class="fas fa-snowflake text-info"></i> Climatisation
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="security" name="amenities[]" value="security">
                                <label class="form-check-label" for="security">
                                    <i class="fas fa-shield-alt text-success"></i> Sécurité
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="furnished" name="amenities[]" value="furnished">
                                <label class="form-check-label" for="furnished">
                                    <i class="fas fa-couch text-brown"></i> Meublé
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="sea_view" name="amenities[]" value="sea_view">
                                <label class="form-check-label" for="sea_view">
                                    <i class="fas fa-water text-blue"></i> Vue sur mer
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Étape 4: Médias -->
                <div class="tab-pane fade" id="step4">
                    <div class="mb-3">
                        <label for="images" class="form-label">Photos du bien</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 5MB par image.</div>
                    </div>
                    
                    <!-- Zone de prévisualisation des images -->
                    <div id="imagePreview" class="row g-3"></div>
                </div>
            </div>
            
            <!-- Boutons de navigation -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary" id="wizardPrev">
                    <i class="fas fa-arrow-left"></i> Précédent
                </button>
                <button type="button" class="btn btn-primary" id="wizardNext">
                    Suivant <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn btn-success d-none" id="wizardSubmit">
                    <i class="fas fa-save"></i> Créer le bien
                </button>
            </div>
        </div>
    </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 12px 20px;
    }
    
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: #f8f9fa;
        border-bottom: 2px solid #0d6efd;
    }
    
    .nav-tabs .nav-link:hover {
        background-color: #f8f9fa;
    }
    
    .tab-pane {
        min-height: 400px;
    }
    
    #map {
        border: 1px solid #dee2e6;
    }
    
    .image-preview {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .image-preview .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
    }
    
    .form-check-label i {
        margin-right: 5px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wizard Navigation
    let currentStep = 1;
    const totalSteps = 4;
    
    const prevBtn = document.getElementById('wizardPrev');
    const nextBtn = document.getElementById('wizardNext');
    const submitBtn = document.getElementById('wizardSubmit');
    
    function showStep(step) {
        // Masquer toutes les étapes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Masquer tous les onglets actifs
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Afficher l'étape courante
        document.getElementById('step' + step).classList.add('show', 'active');
        document.querySelector('[data-step="' + step + '"]').classList.add('active');
        
        // Gérer les boutons
        prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
        nextBtn.style.display = step === totalSteps ? 'none' : 'inline-block';
        submitBtn.classList.toggle('d-none', step !== totalSteps);
        
        currentStep = step;
    }
    
    // Navigation avec les onglets
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const step = parseInt(this.dataset.step);
            showStep(step);
        });
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });
    
    nextBtn.addEventListener('click', function() {
        if (currentStep < totalSteps) {
            showStep(currentStep + 1);
        }
    });
    
    // Initialisation de la carte
    let map, marker;
    
    function initMap() {
        if (document.getElementById('map')) {
            map = L.map('map').setView([36.8065, 10.1815], 10); // Centre sur Tunis
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Clic sur la carte pour placer le marqueur
            map.on('click', function(e) {
                setMarker(e.latlng.lat, e.latlng.lng);
            });
        }
    }
    
    function setMarker(lat, lng) {
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker([lat, lng]).addTo(map);
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
    }
    
    // Bouton obtenir ma position
    document.getElementById('getLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localisation...';
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    setMarker(lat, lng);
                    map.setView([lat, lng], 15);
                    this.innerHTML = '<i class="fas fa-map-marker-alt"></i> Obtenir ma position';
                },
                () => {
                    alert('Impossible d\'obtenir votre position');
                    this.innerHTML = '<i class="fas fa-map-marker-alt"></i> Obtenir ma position';
                }
            );
        }
    });
    
    // Bouton géocoder l'adresse
    document.getElementById('searchAddressBtn').addEventListener('click', function() {
        const address = document.getElementById('address').value;
        if (!address) {
            alert('Veuillez saisir une adresse');
            return;
        }
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche...';
        
        // Utilisation de l'API Nominatim pour le géocodage
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&countrycodes=tn&limit=1`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    setMarker(lat, lng);
                    map.setView([lat, lng], 15);
                } else {
                    alert('Adresse non trouvée');
                }
                this.innerHTML = '<i class="fas fa-search"></i> Géocoder l\'adresse';
            })
            .catch(() => {
                alert('Erreur lors de la recherche');
                this.innerHTML = '<i class="fas fa-search"></i> Géocoder l\'adresse';
            });
    });
    
    // Prévisualisation des images
    document.getElementById('images').addEventListener('change', function() {
        const files = this.files;
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        Array.from(files).forEach((file, index) => {
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3';
                    col.innerHTML = `
                        <div class="image-preview">
                            <img src="${e.target.result}" class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;">
                            <button type="button" class="remove-image" onclick="removeImage(${index})">×</button>
                        </div>
                    `;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Initialiser la carte quand on arrive à l'étape 2
    document.querySelector('[data-step="2"]').addEventListener('click', function() {
        setTimeout(() => {
            if (!map) {
                initMap();
            } else {
                map.invalidateSize();
            }
        }, 100);
    });
});

function removeImage(index) {
    // Fonction pour supprimer une image de la prévisualisation
    const files = document.getElementById('images').files;
    const dt = new DataTransfer();
    
    Array.from(files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    document.getElementById('images').files = dt.files;
    
    // Relancer la prévisualisation
    document.getElementById('images').dispatchEvent(new Event('change'));
}
</script>
<?= $this->endSection() ?>