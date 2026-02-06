<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
                <!-- Breadcrumb -->
                <nav class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/properties') ?>">Biens</a></li>
                        <li class="breadcrumb-item active">Créer un bien</li>
                    </ol>
                </nav>

                <!-- Page Title -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-plus-circle"></i> Créer un Nouveau Bien
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

                <!-- Formulaire -->
                <form action="<?= base_url('admin/properties/store') ?>" method="post" enctype="multipart/form-data">
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
                        </div>
                        <div class="progress mt-2">
                            <div class="progress-bar" id="wizardProgress" role="progressbar" style="width: 33%"></div>
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
                                                   value="<?= old('title') ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="reference" class="form-label">Référence</label>
                                            <input type="text" class="form-control" id="reference" name="reference" 
                                                   value="<?= old('reference') ?>" placeholder="Auto-généré">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" name="description" rows="5" required><?= old('description') ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                            <select class="form-select" id="type" name="type" required>
                                                <option value="">-- Sélectionner --</option>
                                                <option value="apartment" <?= old('type') == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                                <option value="villa" <?= old('type') == 'villa' ? 'selected' : '' ?>>Villa</option>
                                                <option value="house" <?= old('type') == 'house' ? 'selected' : '' ?>>Maison</option>
                                                <option value="land" <?= old('type') == 'land' ? 'selected' : '' ?>>Terrain</option>
                                                <option value="commercial" <?= old('type') == 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                                <option value="office" <?= old('type') == 'office' ? 'selected' : '' ?>>Bureau</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="transaction_type" class="form-label">Transaction <span class="text-danger">*</span></label>
                                            <select class="form-select" id="transaction_type" name="transaction_type" required>
                                                <option value="">-- Sélectionner --</option>
                                                <option value="sale" <?= old('transaction_type') == 'sale' ? 'selected' : '' ?>>Vente</option>
                                                <option value="rent" <?= old('transaction_type') == 'rent' ? 'selected' : '' ?>>Location</option>
                                                <option value="both" <?= old('transaction_type') == 'both' ? 'selected' : '' ?>>Vente ou Location</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="available" selected>Disponible</option>
                                                <option value="reserved">Réservé</option>
                                                <option value="sold">Vendu</option>
                                                <option value="rented">Loué</option>
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
                                                   value="<?= old('price') ?>" min="0" step="0.01">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rent_price" class="form-label">Prix de Location (TND/mois)</label>
                                            <input type="number" class="form-control" id="rent_price" name="rent_price" 
                                                   value="<?= old('rent_price') ?>" min="0" step="0.01">
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
                                                   value="<?= old('bedrooms', 0) ?>" min="0">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="bathrooms" class="form-label">Salles de bain</label>
                                            <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                                   value="<?= old('bathrooms', 0) ?>" min="0">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="area" class="form-label">Surface (m²) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="area" name="area" 
                                                   value="<?= old('area') ?>" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="floor" class="form-label">Étage</label>
                                            <input type="number" class="form-control" id="floor" name="floor" 
                                                   value="<?= old('floor') ?>" min="0">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="year_built" class="form-label">Année de construction</label>
                                            <input type="number" class="form-control" id="year_built" name="year_built" 
                                                   value="<?= old('year_built') ?>" min="1900" max="<?= date('Y') ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="parking" class="form-label">Parking</label>
                                            <input type="number" class="form-control" id="parking" name="parking" 
                                                   value="<?= old('parking', 0) ?>" min="0">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="furnished" class="form-label">Meublé</label>
                                            <select class="form-select" id="furnished" name="furnished">
                                                <option value="0">Non meublé</option>
                                                <option value="1">Meublé</option>
                                            </select>
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
                                                            <?= old('zone_id') == $zone['id'] ? 'selected' : '' ?>>
                                                        <?= esc($zone['name']) ?>
                                                        <?php if (!empty($zone['latitude']) && !empty($zone['longitude'])): ?>
                                                            <i class="fas fa-map-marker-alt text-success"></i>
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Adresse <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="address" name="address" 
                                                       value="<?= old('address') ?>" placeholder="Adresse complète du bien" required>
                                                <button class="btn btn-outline-secondary" type="button" id="toggleAddressVisibility">
                                                    <i class="fas fa-eye" id="addressVisibilityIcon"></i>
                                                </button>
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="hide_address" name="hide_address" value="1" <?= old('hide_address') ? 'checked' : '' ?>>
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
                                                <input type="radio" class="btn-check" name="gps_mode" id="gps_manual" value="manual" checked>
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
                                                   value="<?= old('latitude') ?>" step="0.000001" placeholder="Ex: 36.8065">
                                            <small class="text-muted">Format: 36.8065</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="longitude" class="form-label">Longitude</label>
                                            <input type="number" class="form-control" id="longitude" name="longitude" 
                                                   value="<?= old('longitude') ?>" step="0.000001" placeholder="Ex: 10.1815">
                                            <small class="text-muted">Format: 10.1815</small>
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
                            <!-- Images -->
                            <div class="card mb-4" data-wizard-step="3">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="fas fa-images"></i> Images</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="images" class="form-label">Photos du bien</label>
                                        <input type="file" class="form-control" id="images" name="images[]" 
                                               accept="image/*" multiple>
                                        <small class="text-muted">Formats acceptés: JPG, PNG, WebP (Max 5MB par image)</small>
                                    </div>
                                    <div id="image-preview" class="row g-2"></div>
                                </div>
                            </div>

                            <!-- Agence & Agent -->
                            <div class="card mb-4" data-wizard-step="3">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-building"></i> Agence & Agent</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="agency_id" class="form-label">Agence</label>
                                        <select class="form-select" id="agency_id" name="agency_id">
                                            <option value="">-- Sélectionner --</option>
                                            <?php foreach ($agencies as $agency): ?>
                                                <option value="<?= $agency['id'] ?>" <?= old('agency_id') == $agency['id'] ? 'selected' : '' ?>>
                                                    <?= esc($agency['name']) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="agent_id" class="form-label">Agent responsable</label>
                                        <select class="form-select" id="agent_id" name="agent_id">
                                            <option value="">-- Sélectionner --</option>
                                            <?php foreach ($agents as $agent): ?>
                                                <option value="<?= $agent['id'] ?>" <?= old('agent_id') == $agent['id'] ? 'selected' : '' ?>>
                                                    <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Visibilité -->
                            <div class="card mb-4" data-wizard-step="3">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-eye"></i> Visibilité</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= old('is_featured') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_featured">
                                            <i class="fas fa-star text-warning"></i> Mettre en avant
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" checked>
                                        <label class="form-check-label" for="is_published">
                                            <i class="fas fa-globe text-success"></i> Publier sur le site
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="card" data-wizard-step="3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary wizard-prev">
                                        <i class="fas fa-arrow-left"></i> Précédent
                                    </button>
                                    <button type="button" class="btn btn-primary wizard-next">
                                        Suivant <i class="fas fa-arrow-right"></i>
                                    </button>
                                    <div class="wizard-submit-group d-none">
                                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-save"></i> Enregistrer comme brouillon
                                        </button>
                                        <button type="submit" name="action" value="publish" class="btn btn-primary">
                                            <i class="fas fa-check"></i> Créer le bien
                                        </button>
                                    </div>
                                </div>
                            </div>
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
const totalSteps = 3;
const stepItems = document.querySelectorAll('.wizard-step-item');
const stepBlocks = document.querySelectorAll('[data-wizard-step]');
const prevBtn = document.querySelector('.wizard-prev');
const nextBtn = document.querySelector('.wizard-next');
const submitGroup = document.querySelector('.wizard-submit-group');
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
    submitGroup.classList.toggle('d-none', step !== totalSteps);
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
        const lat = document.getElementById('latitude').value || 36.8065;
        const lng = document.getElementById('longitude').value || 10.1815;
        
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

// Preview des images sélectionnées
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
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
                        ${index === 0 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Principal</span>' : ''}
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Add data attributes to zone options
document.addEventListener('DOMContentLoaded', function() {
    // This will be populated from PHP in the next step
    console.log('Property Create Form Initialized');
});
</script>
<?= $this->endSection() ?>