<?= $this->extend('layouts/public_orpi_style') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">Estimer mon bien</h1>
                <p class="lead">Obtenez une estimation gratuite et professionnelle de votre propriété en quelques minutes</p>
            </div>
        </div>
    </div>
</section>

<!-- Estimation Form -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <!-- Wizard Card -->
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        
                        <!-- Wizard Steps -->
                        <div class="wizard-steps mb-5">
                            <div class="d-flex justify-content-between align-items-center position-relative">
                                <div class="wizard-step active" data-step="1">
                                    <div class="wizard-step-circle">1</div>
                                    <div class="wizard-step-label">Vos informations</div>
                                </div>
                                <div class="wizard-line"></div>
                                <div class="wizard-step" data-step="2">
                                    <div class="wizard-step-circle">2</div>
                                    <div class="wizard-step-label">Le bien</div>
                                </div>
                                <div class="wizard-line"></div>
                                <div class="wizard-step" data-step="3">
                                    <div class="wizard-step-circle">3</div>
                                    <div class="wizard-step-label">Détails</div>
                                </div>
                            </div>
                        </div>

                        <form method="post" action="<?= base_url('estimer-mon-bien/submit') ?>" id="estimationForm">
                            <?= csrf_field() ?>

                            <!-- Step 1: Client Information -->
                            <div class="wizard-content active" data-step="1">
                                <h3 class="mb-4">Vos informations de contact</h3>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control form-control-lg" 
                                               value="<?= old('first_name') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control form-control-lg" 
                                               value="<?= old('last_name') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control form-control-lg" 
                                               value="<?= old('email') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Téléphone</label>
                                        <input type="tel" name="phone" class="form-control form-control-lg" 
                                               value="<?= old('phone') ?>" placeholder="Ex: +216 12 345 678">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-orpi-primary btn-lg px-5" onclick="nextStep()">
                                        Suivant <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Property Information -->
                            <div class="wizard-content" data-step="2">
                                <h3 class="mb-4">Informations sur votre bien</h3>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Type de bien <span class="text-danger">*</span></label>
                                        <select name="property_type" class="form-select form-select-lg" required>
                                            <option value="">Sélectionnez un type</option>
                                            <option value="apartment" <?= old('property_type') === 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                            <option value="villa" <?= old('property_type') === 'villa' ? 'selected' : '' ?>>Villa</option>
                                            <option value="studio" <?= old('property_type') === 'studio' ? 'selected' : '' ?>>Studio</option>
                                            <option value="office" <?= old('property_type') === 'office' ? 'selected' : '' ?>>Bureau</option>
                                            <option value="shop" <?= old('property_type') === 'shop' ? 'selected' : '' ?>>Commerce</option>
                                            <option value="warehouse" <?= old('property_type') === 'warehouse' ? 'selected' : '' ?>>Entrepôt</option>
                                            <option value="land" <?= old('property_type') === 'land' ? 'selected' : '' ?>>Terrain</option>
                                            <option value="other" <?= old('property_type') === 'other' ? 'selected' : '' ?>>Autre</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Type de transaction <span class="text-danger">*</span></label>
                                        <select name="transaction_type" class="form-select form-select-lg" required>
                                            <option value="sale" <?= old('transaction_type') === 'sale' ? 'selected' : '' ?>>Vente</option>
                                            <option value="rent" <?= old('transaction_type') === 'rent' ? 'selected' : '' ?>>Location</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label">Adresse complète</label>
                                        <input type="text" name="address" class="form-control form-control-lg" 
                                               value="<?= old('address') ?>" placeholder="Numéro, rue, résidence...">
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">Ville</label>
                                        <input type="text" name="city" class="form-control form-control-lg" 
                                               value="<?= old('city') ?>" placeholder="Ex: Tunis">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Gouvernorat</label>
                                        <input type="text" name="governorate" class="form-control form-control-lg" 
                                               value="<?= old('governorate') ?>" placeholder="Ex: Tunis">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Zone</label>
                                        <select name="zone_id" class="form-select form-select-lg">
                                            <option value="">Sélectionnez une zone</option>
                                            <?php foreach ($zones as $zone): ?>
                                                <option value="<?= $zone['id'] ?>" <?= old('zone_id') == $zone['id'] ? 'selected' : '' ?>>
                                                    <?= esc($zone['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary btn-lg px-5" onclick="prevStep()">
                                        <i class="fas fa-arrow-left me-2"></i> Précédent
                                    </button>
                                    <button type="button" class="btn btn-orpi-primary btn-lg px-5" onclick="nextStep()">
                                        Suivant <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3: Property Details -->
                            <div class="wizard-content" data-step="3">
                                <h3 class="mb-4">Détails du bien</h3>
                                
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Surface (m²)</label>
                                        <input type="number" name="area_total" class="form-control form-control-lg" 
                                               value="<?= old('area_total') ?>" step="0.01" placeholder="Ex: 120">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nombre de pièces</label>
                                        <input type="number" name="rooms" class="form-control form-control-lg" 
                                               value="<?= old('rooms') ?>" placeholder="Ex: 4">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Chambres</label>
                                        <input type="number" name="bedrooms" class="form-control form-control-lg" 
                                               value="<?= old('bedrooms') ?>" placeholder="Ex: 3">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Salles de bain</label>
                                        <input type="number" name="bathrooms" class="form-control form-control-lg" 
                                               value="<?= old('bathrooms') ?>" placeholder="Ex: 2">
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">Étage</label>
                                        <input type="number" name="floor" class="form-control form-control-lg" 
                                               value="<?= old('floor') ?>" placeholder="Ex: 2">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Année de construction</label>
                                        <input type="number" name="construction_year" class="form-control form-control-lg" 
                                               value="<?= old('construction_year') ?>" placeholder="Ex: 2015">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">État du bien</label>
                                        <select name="condition_state" class="form-select form-select-lg">
                                            <option value="">Sélectionnez</option>
                                            <option value="new" <?= old('condition_state') === 'new' ? 'selected' : '' ?>>Neuf</option>
                                            <option value="excellent" <?= old('condition_state') === 'excellent' ? 'selected' : '' ?>>Excellent</option>
                                            <option value="good" <?= old('condition_state') === 'good' ? 'selected' : '' ?>>Bon</option>
                                            <option value="to_renovate" <?= old('condition_state') === 'to_renovate' ? 'selected' : '' ?>>À rénover</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label fw-bold mb-3">Équipements</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="checkbox" name="has_elevator" value="1" 
                                                           id="has_elevator" <?= old('has_elevator') ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="has_elevator">Ascenseur</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="checkbox" name="has_parking" value="1" 
                                                           id="has_parking" <?= old('has_parking') ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="has_parking">Parking</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="checkbox" name="has_garden" value="1" 
                                                           id="has_garden" <?= old('has_garden') ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="has_garden">Jardin</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label">Description ou commentaires</label>
                                        <textarea name="description" class="form-control" rows="4" 
                                                  placeholder="Décrivez votre bien, ses particularités, travaux effectués..."><?= old('description') ?></textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary btn-lg px-5" onclick="prevStep()">
                                        <i class="fas fa-arrow-left me-2"></i> Précédent
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i> Envoyer ma demande
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Confidentialité garantie :</strong> Vos informations restent strictement confidentielles 
                    et seront uniquement utilisées pour vous fournir une estimation de votre bien.
                </div>

            </div>
        </div>
    </div>
</section>

<style>
.wizard-steps {
    position: relative;
}

.wizard-step {
    text-align: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.wizard-step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    margin: 0 auto 10px;
    border: 3px solid #e9ecef;
    transition: all 0.3s;
}

.wizard-step.active .wizard-step-circle {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.wizard-step.completed .wizard-step-circle {
    background: var(--success-color, #28a745);
    color: white;
    border-color: var(--success-color, #28a745);
}

.wizard-step-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.wizard-step.active .wizard-step-label {
    color: var(--primary-color);
    font-weight: 600;
}

.wizard-line {
    position: absolute;
    top: 25px;
    left: 0;
    right: 0;
    height: 3px;
    background: #e9ecef;
    z-index: 1;
}

.wizard-content {
    display: none;
}

.wizard-content.active {
    display: block;
}

.form-check-lg .form-check-input {
    width: 1.5rem;
    height: 1.5rem;
    margin-top: 0.125rem;
}

.form-check-lg .form-check-label {
    padding-left: 0.5rem;
    font-size: 1.1rem;
}
</style>

<script>
let currentStep = 1;

function nextStep() {
    if (validateStep(currentStep)) {
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('completed');
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('active');
        
        currentStep++;
        
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.add('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevStep() {
    document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('active');
    
    currentStep--;
    
    document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.add('active');
    document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');
    document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('completed');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    const currentContent = document.querySelector(`.wizard-content[data-step="${step}"]`);
    const requiredInputs = currentContent.querySelectorAll('[required]');
    
    for (let input of requiredInputs) {
        if (!input.value.trim()) {
            input.focus();
            input.classList.add('is-invalid');
            return false;
        } else {
            input.classList.remove('is-invalid');
        }
    }
    
    return true;
}
</script>

<?= $this->endSection() ?>
