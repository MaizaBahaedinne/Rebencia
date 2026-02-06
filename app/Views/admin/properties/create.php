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
                            <!-- Nouveau wizard moderne -->
                            <form action="<?= base_url('admin/properties/store') ?>" method="post" enctype="multipart/form-data" id="propertyWizardForm">
                                <?= csrf_field() ?>
                                <div class="wizard-modern mb-4">
                                    <div class="wizard-steps">
                                        <div class="wizard-step-item active" data-step="1"><span class="step-number">1</span><span class="step-label">Infos générales</span></div>
                                        <div class="wizard-step-item" data-step="2"><span class="step-number">2</span><span class="step-label">Localisation</span></div>
                                        <div class="wizard-step-item" data-step="3"><span class="step-number">3</span><span class="step-label">Caractéristiques & prix</span></div>
                                        <div class="wizard-step-item" data-step="4"><span class="step-number">4</span><span class="step-label">Médias</span></div>
                                        <div class="wizard-step-item" data-step="5"><span class="step-number">5</span><span class="step-label">Récapitulatif</span></div>
                                    </div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar" id="wizardProgress" role="progressbar" style="width: 20%"></div>
                                    </div>
                                </div>
                                <div class="wizard-content">
                                    <!-- Step 1: Infos générales -->
                                    <div class="wizard-step-block active-step" data-wizard-step="1">
                                        <?php include __DIR__.'/partials/wizard_step_infos.php'; ?>
                                    </div>
                                    <!-- Step 2: Localisation -->
                                    <div class="wizard-step-block" data-wizard-step="2">
                                        <?php include __DIR__.'/partials/wizard_step_localisation.php'; ?>
                                    </div>
                                    <!-- Step 3: Caractéristiques & prix -->
                                    <div class="wizard-step-block" data-wizard-step="3">
                                        <?php include __DIR__.'/partials/wizard_step_caracteristiques.php'; ?>
                                    </div>
                                    <!-- Step 4: Médias -->
                                    <div class="wizard-step-block" data-wizard-step="4">
                                        <?php include __DIR__.'/partials/wizard_step_medias.php'; ?>
                                    </div>
                                    <!-- Step 5: Récapitulatif -->
                                    <div class="wizard-step-block" data-wizard-step="5">
                                        <?php include __DIR__.'/partials/wizard_step_recap.php'; ?>
                                    </div>
                                </div>
                                <div class="wizard-actions d-flex justify-content-between align-items-center mt-4">
                                    <button type="button" class="btn btn-outline-secondary wizard-prev"><i class="fas fa-arrow-left"></i> Précédent</button>
                                    <button type="button" class="btn btn-primary wizard-next">Suivant <i class="fas fa-arrow-right"></i></button>
                                    <button type="submit" class="btn btn-success wizard-submit d-none"><i class="fas fa-check"></i> Valider et créer le bien</button>
                                </div>
                            </form>
                                            <i class="fas fa-check"></i> Créer le bien
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" data-wizard-step="4">
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i>
                                Enregistrez d’abord le bien. Vous pourrez ensuite compléter les données étendues dans l’édition.
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
const totalSteps = 4;
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