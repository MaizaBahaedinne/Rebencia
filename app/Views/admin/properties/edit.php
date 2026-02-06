<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/properties') ?>">Biens</a></li>
        <li class="breadcrumb-item active">Modifier le bien #<?= $property['reference'] ?></li>
    </ol>
</nav>

<!-- Page Title -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit"></i> Modifier le Bien - <?= esc($property['title']) ?>
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

<!-- Formulaire -->
<form action="<?= base_url('admin/properties/update/' . $property['id']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="property-wizard mb-4">
        <div class="wizard-steps">
            <div class="wizard-step-item active" data-step="1">
                <span class="step-number">1</span>
                <span class="step-label">Infos & prix</span>
            </div>
            <div class="wizard-step-item" data-step="2">
                <span class="step-number">2</span>
                <span class="step-label">Localisation</span>
            </div>
            <div class="wizard-step-item" data-step="3">
                <span class="step-number">3</span>
                <span class="step-label">Médias & publication</span>
            </div>
            <div class="wizard-step-item" data-step="4">
                <span class="step-number">4</span>
                <span class="step-label">Données étendues</span>
            </div>
        </div>
        <div class="progress mt-2">
            <div class="progress-bar" id="wizardProgress" role="progressbar" style="width: 25%"></div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne Gauche -->
        <div class="col-lg-8">
            <!-- Informations Générales -->
            <div class="card mb-4" data-wizard-step="1">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Titre du bien <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= esc($property['title']) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" class="form-control" id="reference" name="reference" 
                                   value="<?= esc($property['reference']) ?>" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= esc($property['description']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="apartment" <?= $property['type'] == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                <option value="villa" <?= $property['type'] == 'villa' ? 'selected' : '' ?>>Villa</option>
                                <option value="house" <?= $property['type'] == 'house' ? 'selected' : '' ?>>Maison</option>
                                <option value="land" <?= $property['type'] == 'land' ? 'selected' : '' ?>>Terrain</option>
                                <option value="commercial" <?= $property['type'] == 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                <option value="office" <?= $property['type'] == 'office' ? 'selected' : '' ?>>Bureau</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="transaction_type" class="form-label">Transaction <span class="text-danger">*</span></label>
                            <select class="form-select" id="transaction_type" name="transaction_type" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="sale" <?= $property['transaction_type'] == 'sale' ? 'selected' : '' ?>>Vente</option>
                                <option value="rent" <?= $property['transaction_type'] == 'rent' ? 'selected' : '' ?>>Location</option>
                                <option value="both" <?= $property['transaction_type'] == 'both' ? 'selected' : '' ?>>Vente ou Location</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" <?= $property['status'] == 'available' ? 'selected' : '' ?>>Disponible</option>
                                <option value="reserved" <?= $property['status'] == 'reserved' ? 'selected' : '' ?>>Réservé</option>
                                <option value="sold" <?= $property['status'] == 'sold' ? 'selected' : '' ?>>Vendu</option>
                                <option value="rented" <?= $property['status'] == 'rented' ? 'selected' : '' ?>>Loué</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prix -->
            <div class="card mb-4" data-wizard-step="1">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Prix</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Prix de Vente (TND)</label>
                            <input type="number" class="form-control" id="price" name="price" 
                                   value="<?= $property['price'] ?>" min="0" step="0.01">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rent_price" class="form-label">Prix de Location (TND/mois)</label>
                            <input type="number" class="form-control" id="rent_price" name="rent_price" 
                                   value="<?= $property['rental_price'] ?? '' ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Caractéristiques -->
            <div class="card mb-4" data-wizard-step="1">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-ruler-combined"></i> Caractéristiques</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="bedrooms" class="form-label">Chambres</label>
                            <input type="number" class="form-control" id="bedrooms" name="bedrooms" 
                                   value="<?= $property['bedrooms'] ?>" min="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="bathrooms" class="form-label">Salles de bain</label>
                            <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                   value="<?= $property['bathrooms'] ?>" min="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="area" class="form-label">Surface (m²) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="area" name="area" 
                                   value="<?= $property['area_total'] ?? '' ?>" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="floor" class="form-label">Étage</label>
                            <input type="number" class="form-control" id="floor" name="floor" 
                                   value="<?= $property['floor'] ?>" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div class="card mb-4" data-wizard-step="2">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Localisation</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="zone_id" class="form-label">Zone <span class="text-danger">*</span></label>
                            <select class="form-select" id="zone_id" name="zone_id" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($zones as $zone): ?>
                                    <option value="<?= $zone['id'] ?>" 
                                            data-lat="<?= $zone['latitude'] ?? '' ?>" 
                                            data-lng="<?= $zone['longitude'] ?? '' ?>"
                                            <?= $property['zone_id'] == $zone['id'] ? 'selected' : '' ?>>
                                        <?= esc($zone['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Adresse <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?= esc($property['address']) ?>" placeholder="Adresse complète du bien" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleAddressVisibility">
                                    <i class="fas fa-eye" id="addressVisibilityIcon"></i>
                                </button>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="hide_address" name="hide_address" value="1" 
                                       <?= !empty($property['hide_address']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="hide_address">
                                    <i class="fas fa-shield-alt"></i> Masquer l'adresse exacte sur les annonces publiques
                                    <small class="text-muted d-block">L'adresse ne sera visible que pour les agents et les clients confirmés</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- GPS Configuration -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marked-alt"></i> Localisation GPS
                            </label>
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="gps_mode" id="gps_manual" value="manual" 
                                       <?= !empty($property['latitude']) && !empty($property['longitude']) ? 'checked' : '' ?>>
                                <label class="btn btn-outline-primary" for="gps_manual">
                                    <i class="fas fa-keyboard"></i> Saisie manuelle
                                </label>
                                
                                <input type="radio" class="btn-check" name="gps_mode" id="gps_zone" value="zone">
                                <label class="btn btn-outline-primary" for="gps_zone">
                                    <i class="fas fa-map"></i> Utiliser la zone
                                </label>
                                
                                <input type="radio" class="btn-check" name="gps_mode" id="gps_map" value="map">
                                <label class="btn btn-outline-primary" for="gps_map">
                                    <i class="fas fa-mouse-pointer"></i> Cliquer sur carte
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Manual GPS Input -->
                    <div class="row" id="gps_manual_fields">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number" class="form-control" id="latitude" name="latitude" 
                                   value="<?= esc($property['latitude'] ?? '') ?>" step="0.000001" placeholder="Ex: 36.8065">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number" class="form-control" id="longitude" name="longitude" 
                                   value="<?= esc($property['longitude'] ?? '') ?>" step="0.000001" placeholder="Ex: 10.1815">
                        </div>
                    </div>

                    <!-- Zone GPS Info -->
                    <div class="row d-none" id="gps_zone_info">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Utilisation GPS de la zone</strong>
                                <p class="mb-0" id="zone_gps_text">Sélectionnez une zone pour utiliser ses coordonnées GPS</p>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Map -->
                    <div class="row d-none" id="gps_map_container">
                        <div class="col-12 mb-3">
                            <div class="alert alert-success">
                                <i class="fas fa-hand-pointer"></i> Cliquez sur la carte pour définir la position exacte
                            </div>
                            <div id="gps_picker_map" style="height: 400px; border-radius: 8px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite -->
        <div class="col-lg-4">
            <!-- Images Actuelles -->
            <div class="card mb-4" data-wizard-step="3">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-images"></i> Images Actuelles</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($property_images)): ?>
                        <div class="row g-2">
                            <?php foreach ($property_images as $image): ?>
                                <div class="col-6">
                                    <div class="position-relative">
                                        <img src="<?= base_url($image['file_path']) ?>" class="img-thumbnail" alt="Image">
                                        <?php if ($image['is_primary']): ?>
                                            <span class="badge bg-primary position-absolute top-0 start-0 m-1">Principal</span>
                                        <?php endif ?>
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                                onclick="deleteImage(<?= $image['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Aucune image</p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Ajouter des Images -->
            <div class="card mb-4" data-wizard-step="3">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Ajouter des Images</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control" id="new_images" name="new_images[]" 
                               accept="image/*" multiple>
                        <small class="text-muted">Formats acceptés: JPG, PNG, WebP (Max 5MB par image)</small>
                    </div>
                    <div id="new-image-preview" class="row g-2"></div>
                </div>
            </div>

            <!-- Visibilité -->
            <div class="card mb-4" data-wizard-step="3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Visibilité</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                               <?= $property['featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">
                            <i class="fas fa-star text-warning"></i> Mettre en avant
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" 
                               <?= $property['status'] == 'published' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_published">
                            <i class="fas fa-globe text-success"></i> Publier sur le site
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="card" data-wizard-step="4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Supprimer ce bien
                </button>
                <div>
                    <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary me-2">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="button" class="btn btn-outline-secondary wizard-prev me-2">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="button" class="btn btn-primary wizard-next me-2">
                        Suivant <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-primary wizard-submit d-none">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card" data-wizard-step="4">
        <div class="card-body">
            <?= $this->include('admin/properties/extended_tabs') ?>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .property-wizard .wizard-steps {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .property-wizard .wizard-step-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 600;
        cursor: pointer;
    }
    .property-wizard .wizard-step-item.active {
        background: #2563eb;
        color: #fff;
    }
    .property-wizard .step-number {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        font-size: 12px;
    }
    [data-wizard-step] { display: none; }
    [data-wizard-step].active-step { display: block; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let currentStep = 1;
const totalSteps = 4;
const stepItems = document.querySelectorAll('.wizard-step-item');
const stepBlocks = document.querySelectorAll('[data-wizard-step]');
const prevBtn = document.querySelector('.wizard-prev');
const nextBtn = document.querySelector('.wizard-next');
const submitBtn = document.querySelector('.wizard-submit');
const progressBar = document.getElementById('wizardProgress');

function setStep(step) {
    currentStep = step;
    stepItems.forEach(item => {
        item.classList.toggle('active', Number(item.dataset.step) === step);
    });
    stepBlocks.forEach(block => {
        block.classList.toggle('active-step', Number(block.dataset.wizardStep) === step);
    });
    prevBtn.classList.toggle('d-none', step === 1);
    nextBtn.classList.toggle('d-none', step === totalSteps);
    submitBtn.classList.toggle('d-none', step !== totalSteps);
    progressBar.style.width = `${Math.round((step / totalSteps) * 100)}%`;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    const currentFields = document.querySelectorAll(`[data-wizard-step="${step}"] input[required], [data-wizard-step="${step}"] select[required], [data-wizard-step="${step}"] textarea[required]`);
    for (const field of currentFields) {
        if (!field.reportValidity()) {
            return false;
        }
    }
    return true;
}

stepItems.forEach(item => {
    item.addEventListener('click', () => {
        const step = Number(item.dataset.step);
        if (step <= currentStep || validateStep(currentStep)) {
            setStep(step);
        }
    });
});

prevBtn.addEventListener('click', () => setStep(Math.max(1, currentStep - 1)));
nextBtn.addEventListener('click', () => {
    if (validateStep(currentStep)) {
        setStep(Math.min(totalSteps, currentStep + 1));
    }
});

setStep(1);

let gpsPickerMap = null;
let gpsMarker = null;

// GPS Mode Toggle
document.querySelectorAll('input[name="gps_mode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const mode = this.value;
        
        // Hide all sections
        document.getElementById('gps_manual_fields').classList.add('d-none');
        document.getElementById('gps_zone_info').classList.add('d-none');
        document.getElementById('gps_map_container').classList.add('d-none');
        
        // Show selected section
        if (mode === 'manual') {
            document.getElementById('gps_manual_fields').classList.remove('d-none');
        } else if (mode === 'zone') {
            document.getElementById('gps_zone_info').classList.remove('d-none');
            updateZoneGPS();
        } else if (mode === 'map') {
            document.getElementById('gps_map_container').classList.remove('d-none');
            initGPSPickerMap();
        }
    });
});

// Zone GPS Update
document.getElementById('zone_id').addEventListener('change', function() {
    if (document.getElementById('gps_zone').checked) {
        updateZoneGPS();
    }
});

function updateZoneGPS() {
    const zoneSelect = document.getElementById('zone_id');
    const selectedOption = zoneSelect.options[zoneSelect.selectedIndex];
    
    if (zoneSelect.value && selectedOption.dataset.lat && selectedOption.dataset.lng) {
        const lat = selectedOption.dataset.lat;
        const lng = selectedOption.dataset.lng;
        const zoneName = selectedOption.text;
        
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('zone_gps_text').innerHTML = `
            <strong>Zone : ${zoneName}</strong><br>
            GPS : ${lat}, ${lng}
        `;
    } else {
        document.getElementById('zone_gps_text').innerHTML = 
            'La zone sélectionnée n\'a pas de coordonnées GPS définies. Veuillez choisir une autre zone ou utiliser la saisie manuelle.';
    }
}

// Initialize GPS Picker Map
function initGPSPickerMap() {
    if (!gpsPickerMap) {
        const lat = parseFloat(document.getElementById('latitude').value) || 36.8065;
        const lng = parseFloat(document.getElementById('longitude').value) || 10.1815;
        
        gpsPickerMap = L.map('gps_picker_map').setView([lat, lng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(gpsPickerMap);
        
        // Add marker
        gpsMarker = L.marker([lat, lng], { draggable: true }).addTo(gpsPickerMap);
        
        // Update coordinates on marker drag
        gpsMarker.on('dragend', function(e) {
            const position = e.target.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(6);
            document.getElementById('longitude').value = position.lng.toFixed(6);
        });
        
        // Update marker on map click
        gpsPickerMap.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            gpsMarker.setLatLng(e.latlng);
        });
    }
    
    // Fix map size
    setTimeout(() => gpsPickerMap.invalidateSize(), 100);
}

// Address Visibility Toggle
document.getElementById('toggleAddressVisibility').addEventListener('click', function() {
    const addressInput = document.getElementById('address');
    const icon = document.getElementById('addressVisibilityIcon');
    
    if (addressInput.type === 'password') {
        addressInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        addressInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Preview des nouvelles images
document.getElementById('new_images').addEventListener('change', function(e) {
    const preview = document.getElementById('new-image-preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" alt="Preview ${index + 1}">
                        <span class="badge bg-success position-absolute top-0 start-0 m-1">Nouveau</span>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Confirmation de suppression
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible.')) {
        window.location.href = '<?= base_url('admin/properties/delete/' . $property['id']) ?>';
    }
}

// Supprimer une image
function deleteImage(imageId) {
    if (confirm('Supprimer cette image ?')) {
        fetch('<?= base_url('admin/properties/delete-image') ?>/' + imageId, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression');
            }
        });
    }
}
</script>
<?= $this->endSection() ?>
