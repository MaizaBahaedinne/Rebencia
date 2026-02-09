<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('title') ?><?= $isEdit ? 'Modifier' : 'Nouveau' ?> Bien Immobilier<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-home text-primary"></i>
                <?= $isEdit ? 'Modifier le bien' : 'Nouveau bien immobilier' ?>
            </h1>
            <?php if ($isEdit): ?>
                <p class="text-muted mb-0">Référence: <strong><?= esc($property['reference']) ?></strong></p>
            <?php endif; ?>
        </div>
        <div>
            <a href="<?= base_url('admin/properties') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Afficher les erreurs de validation -->
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation</h5>
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif ?>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif ?>

    <!-- Wizard Progress -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="wizard-progress">
                <div class="wizard-step active" data-step="1">
                    <div class="wizard-step-icon"><i class="fas fa-info-circle"></i></div>
                    <div class="wizard-step-label">Informations générales</div>
                </div>
                <div class="wizard-step" data-step="2">
                    <div class="wizard-step-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="wizard-step-label">Localisation</div>
                </div>
                <div class="wizard-step" data-step="3">
                    <div class="wizard-step-icon"><i class="fas fa-ruler-combined"></i></div>
                    <div class="wizard-step-label">Caractéristiques</div>
                </div>
                <div class="wizard-step" data-step="4">
                    <div class="wizard-step-icon"><i class="fas fa-euro-sign"></i></div>
                    <div class="wizard-step-label">Prix & Charges</div>
                </div>
                <div class="wizard-step" data-step="5">
                    <div class="wizard-step-icon"><i class="fas fa-th-large"></i></div>
                    <div class="wizard-step-label">Détails des pièces</div>
                </div>
                <div class="wizard-step" data-step="6">
                    <div class="wizard-step-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="wizard-step-label">Documents & Photos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wizard Form -->
    <form id="propertyForm" method="POST" action="<?= base_url('admin/properties/' . ($isEdit ? 'update/' . $property['id'] : 'store')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <!-- Stocker l'ID de la propriété pour les sauvegardes AJAX -->
        <input type="hidden" id="propertyId" name="property_id" value="<?= $property['id'] ?? '' ?>">
        <input type="hidden" id="isEdit" value="<?= $isEdit ? '1' : '0' ?>">

        <!-- Step 1: Informations générales -->
        <div class="wizard-content active" data-step="1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations générales</h5>
                </div>
                <div class="card-body">
                    <?= view('admin/properties/wizard/step1_general', [
                        'property' => $property ?? [],
                        'isEdit' => $isEdit
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Step 2: Localisation -->
        <div class="wizard-content" data-step="2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Localisation & Carte</h5>
                </div>
                <div class="card-body">
                    <?= view('admin/properties/wizard/step2_location', [
                        'property' => $property ?? [],
                        'zones' => $zones,
                        'isEdit' => $isEdit
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Step 3: Caractéristiques -->
        <div class="wizard-content" data-step="3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-ruler-combined"></i> Caractéristiques techniques</h5>
                </div>
                <div class="card-body">
                    <?= view('admin/properties/wizard/step3_features', [
                        'property' => $property ?? [],
                        'isEdit' => $isEdit
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Step 4: Prix & Charges -->
        <div class="wizard-content" data-step="4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-euro-sign"></i> Prix & Charges mensuelles</h5>
                </div>
                <div class="card-body">
                    <?= view('admin/properties/wizard/step4_pricing', [
                        'property' => $property ?? [],
                        'isEdit' => $isEdit
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Step 5: Détails des pièces -->
        <div class="wizard-content" data-step="5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-th-large"></i> Détails des pièces & Proximités</h5>
                </div>
                <div class="card-body">
                    <?= view('admin/properties/wizard/step5_rooms', [
                        'property' => $property ?? [],
                        'rooms' => $rooms ?? [],
                        'proximities' => $proximities ?? [],
                        'isEdit' => $isEdit
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Step 6: Documents & Photos -->
        <div class="wizard-content" data-step="6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Documents & Photos</h5>
                </div>
                <div class="card-body">
                    <?= view('admin/properties/wizard/step6_documents', [
                        'property' => $property ?? [],
                        'documents' => $documents ?? [],
                        'isEdit' => $isEdit
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-lg" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="button" class="btn btn-primary btn-lg" id="nextBtn">
                        Suivant <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" style="display: none;">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Mettre à jour' : 'Enregistrer' ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Wizard Progress Styles */
    .wizard-progress {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        padding: 20px 0;
    }

    .wizard-progress::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #e0e0e0;
        z-index: 0;
        transform: translateY(-50%);
    }

    .wizard-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
        cursor: pointer;
        flex: 1;
    }

    .wizard-step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: white;
        border: 3px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #999;
        transition: all 0.3s;
        margin-bottom: 10px;
    }

    .wizard-step-label {
        font-size: 13px;
        color: #666;
        text-align: center;
        font-weight: 500;
    }

    .wizard-step.active .wizard-step-icon {
        background: #0d6efd;
        border-color: #0d6efd;
        color: white;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
    }

    .wizard-step.active .wizard-step-label {
        color: #0d6efd;
        font-weight: 600;
    }

    .wizard-step.completed .wizard-step-icon {
        background: #28a745;
        border-color: #28a745;
        color: white;
    }

    .wizard-step.completed .wizard-step-label {
        color: #28a745;
    }

    .wizard-content {
        display: none;
    }

    .wizard-content.active {
        display: block;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let currentStep = 1;
const totalSteps = 6;
let propertyId = document.getElementById('propertyId').value || null;
const isEdit = document.getElementById('isEdit').value === '1';

// Navigation entre les étapes
document.getElementById('nextBtn').addEventListener('click', () => {
    if (validateStep(currentStep)) {
        saveStepData(currentStep, () => {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });
    }
});

document.getElementById('prevBtn').addEventListener('click', () => {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
});

// Clic direct sur les étapes
document.querySelectorAll('.wizard-step').forEach(step => {
    step.addEventListener('click', function() {
        const targetStep = parseInt(this.dataset.step);
        if (targetStep < currentStep) {
            // Permet de revenir en arrière sans validation
            currentStep = targetStep;
            showStep(currentStep);
        } else if (targetStep > currentStep && validateStep(currentStep)) {
            // Pour aller en avant, valider et sauvegarder d'abord
            saveStepData(currentStep, () => {
                currentStep = targetStep;
                showStep(currentStep);
            });
        }
    });
});

// Sauvegarder les données d'une étape via AJAX
function saveStepData(step, callback) {
    console.log('saveStepData called for step:', step);
    
    const formData = new FormData(document.getElementById('propertyForm'));
    const saveBtn = step === totalSteps ? document.getElementById('submitBtn') : document.getElementById('nextBtn');
    
    // Déboguer les fichiers photos
    const photoInput = document.getElementById('photo-input');
    if (photoInput && photoInput.files) {
        console.log('Photos in input:', photoInput.files.length);
        for (let i = 0; i < photoInput.files.length; i++) {
            console.log('Photo', i, ':', photoInput.files[i].name, photoInput.files[i].size);
        }
    } else {
        console.log('Photo input not found or no files');
    }
    
    // Vérifier ce que FormData contient
    console.log('FormData entries:');
    for (let pair of formData.entries()) {
        if (pair[1] instanceof File) {
            console.log(pair[0], ':', pair[1].name, pair[1].size);
        } else {
            console.log(pair[0], ':', pair[1]);
        }
    }
    
    // Afficher un loader
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
    
    // Déterminer l'URL en fonction de l'étape
    let url = '<?= base_url("admin/properties/save-step") ?>';
    formData.append('step', step);
    formData.append('property_id', propertyId || '');
    
    console.log('Sending request to:', url);
    console.log('Property ID:', propertyId);
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        
        if (data.success) {
            // Stocker l'ID de la propriété si c'est une nouvelle création
            if (data.property_id && !propertyId) {
                propertyId = data.property_id;
                document.getElementById('propertyId').value = propertyId;
                // Mettre à jour l'URL du formulaire
                document.getElementById('propertyForm').action = 
                    '<?= base_url("admin/properties/update") ?>/' + propertyId;
            }
            
            // Marquer l'étape comme complétée
            document.querySelector(`.wizard-step[data-step="${step}"]`).classList.add('completed');
            
            // Afficher un message de succès
            showToast('Étape ' + step + ' enregistrée avec succès', 'success');
            
            // Si c'est la dernière étape, rediriger vers la page de détails
            if (step === totalSteps) {
                console.log('Last step completed, redirecting to details...');
                setTimeout(() => {
                    window.location.href = '<?= base_url("admin/properties/view") ?>/' + propertyId;
                }, 500);
                return; // Ne pas appeler le callback
            }
            
            // Appeler le callback (passer à l'étape suivante) seulement si ce n'est pas la dernière étape
            if (callback) callback();
        } else {
            showToast('Erreur: ' + (data.message || 'Une erreur est survenue'), 'error');
            console.error('Validation errors:', data.errors);
        }
    })
    .catch(error => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        console.error('Error:', error);
        showToast('Erreur de connexion', 'error');
    });
}

// Afficher un toast message
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function showStep(step) {
    // Cacher tous les contenus
    document.querySelectorAll('.wizard-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Afficher le contenu actif
    document.querySelector(`.wizard-content[data-step="${step}"]`).classList.add('active');
    
    // Mettre à jour la progression
    document.querySelectorAll('.wizard-step').forEach(wizardStep => {
        const stepNum = parseInt(wizardStep.dataset.step);
        wizardStep.classList.remove('active');
        
        if (stepNum === step) {
            wizardStep.classList.add('active');
        }
    });
    
    // Gérer les boutons
    document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
    document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-block';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    const content = document.querySelector(`.wizard-content[data-step="${step}"]`);
    const requiredFields = content.querySelectorAll('[required]');
    let hasError = false;
    let firstErrorField = null;
    let errorFields = [];
    
    for (let field of requiredFields) {
        // Vérifier si le champ est visible (pas dans un élément caché)
        if (!isFieldVisible(field)) {
            console.log('Skipping hidden field:', field.name);
            continue;
        }
        
        // Vérifier si le champ a une valeur
        let isEmpty = false;
        if (field.type === 'checkbox' || field.type === 'radio') {
            // Pour les checkboxes/radios, vérifier si au moins un est coché dans le groupe
            const groupName = field.name;
            const checkedInGroup = content.querySelector(`[name="${groupName}"]:checked`);
            isEmpty = !checkedInGroup;
        } else {
            isEmpty = !field.value || !field.value.trim();
        }
        
        if (isEmpty) {
            field.classList.add('is-invalid');
            errorFields.push(field.name || field.id);
            if (!firstErrorField) {
                firstErrorField = field;
            }
            hasError = true;
        } else {
            field.classList.remove('is-invalid');
        }
    }
    
    if (hasError && firstErrorField) {
        console.error('Validation errors in step ' + step + ':', errorFields);
        firstErrorField.focus();
        showToast('Veuillez remplir tous les champs obligatoires: ' + errorFields.join(', '), 'error');
        return false;
    }
    
    return true;
}

function isFieldVisible(field) {
    // Vérifier si le champ ou l'un de ses parents est caché
    let element = field;
    while (element && element !== document.body) {
        const style = window.getComputedStyle(element);
        if (style.display === 'none' || style.visibility === 'hidden' || element.hidden) {
            return false;
        }
        element = element.parentElement;
    }
    return true;
}

// Empêcher la soumission classique du formulaire
document.getElementById('propertyForm').addEventListener('submit', (e) => {
    e.preventDefault();
    return false;
});

// Gestion du bouton Submit final
document.getElementById('submitBtn').addEventListener('click', (e) => {
    e.preventDefault();
    if (validateStep(currentStep)) {
        saveStepData(currentStep);
    }
});

// Initialiser
showStep(1);
</script>
<?= $this->endSection() ?>
