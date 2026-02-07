<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<style>
.wizard-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
}
.wizard-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e0e0e0;
    z-index: 0;
}
.wizard-step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}
.wizard-step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 0.5rem;
}
.wizard-step.active .wizard-step-circle {
    background: #0d6efd;
    color: white;
}
.wizard-step.completed .wizard-step-circle {
    background: #28a745;
    color: white;
}
.wizard-content {
    display: none;
}
.wizard-content.active {
    display: block;
}
.summary-card {
    position: sticky;
    top: 20px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-edit"></i> Modifier Client: <?= esc($client['first_name'] . ' ' . $client['last_name']) ?>
    </h1>
    <a href="<?= base_url('admin/clients') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Erreurs :</strong>
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif ?>

<form action="<?= base_url('admin/clients/update/' . $client['id']) ?>" method="post" id="clientForm">
    <?= csrf_field() ?>
    
    <div class="row">
        <!-- Zone Principale - Wizard -->
        <div class="col-lg-8">
            <!-- Steps -->
            <div class="wizard-steps">
                <div class="wizard-step active" data-step="1">
                    <div class="wizard-step-circle">1</div>
                    <div class="wizard-step-title">Infos Personnelles</div>
                </div>
                <div class="wizard-step" data-step="2">
                    <div class="wizard-step-circle">2</div>
                    <div class="wizard-step-title">Préférences</div>
                </div>
                <div class="wizard-step" data-step="3">
                    <div class="wizard-step-circle">3</div>
                    <div class="wizard-step-title">Configuration</div>
                </div>
            </div>

            <!-- Step 1: Informations Personnelles -->
            <div class="wizard-content active" data-step="1">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Étape 1 : Informations Personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= old('first_name', $client['first_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= old('last_name', $client['last_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email', $client['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= old('phone', $client['phone']) ?>" placeholder="+216 XX XXX XXX" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone_secondary" class="form-label">Téléphone Secondaire</label>
                                <input type="tel" class="form-control" id="phone_secondary" name="phone_secondary" 
                                       value="<?= old('phone_secondary', $client['phone_secondary']) ?>" placeholder="+216 XX XXX XXX">
                            </div>
                            <div class="col-md-6">
                                <label for="cin" class="form-label">CIN</label>
                                <input type="text" class="form-control" id="cin" name="cin" 
                                       value="<?= old('cin', $client['cin']) ?>" placeholder="12345678">
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Adresse</label>
                                <textarea class="form-control" id="address" name="address" rows="2"><?= old('address', $client['address']) ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary float-end" onclick="nextStep()">
                            Suivant <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Préférences de Recherche -->
            <div class="wizard-content" data-step="2">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-search"></i> Étape 2 : Préférences de Recherche</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="property_type_preference" class="form-label">Type de Bien Souhaité</label>
                                <select class="form-select" id="property_type_preference" name="property_type_preference">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="apartment" <?= old('property_type_preference', $client['property_type_preference'] ?? '') == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                    <option value="villa" <?= old('property_type_preference', $client['property_type_preference'] ?? '') == 'villa' ? 'selected' : '' ?>>Villa</option>
                                    <option value="house" <?= old('property_type_preference', $client['property_type_preference'] ?? '') == 'house' ? 'selected' : '' ?>>Maison</option>
                                    <option value="land" <?= old('property_type_preference', $client['property_type_preference'] ?? '') == 'land' ? 'selected' : '' ?>>Terrain</option>
                                    <option value="commercial" <?= old('property_type_preference', $client['property_type_preference'] ?? '') == 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                    <option value="office" <?= old('property_type_preference', $client['property_type_preference'] ?? '') == 'office' ? 'selected' : '' ?>>Bureau</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="transaction_type_preference" class="form-label">Type de Transaction</label>
                                <select class="form-select" id="transaction_type_preference" name="transaction_type_preference">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="sale" <?= old('transaction_type_preference', $client['transaction_type_preference'] ?? '') == 'sale' ? 'selected' : '' ?>>Achat</option>
                                    <option value="rent" <?= old('transaction_type_preference', $client['transaction_type_preference'] ?? '') == 'rent' ? 'selected' : '' ?>>Location</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="budget_min" class="form-label">Budget Minimum (TND)</label>
                                <input type="number" class="form-control" id="budget_min" name="budget_min" 
                                       value="<?= old('budget_min', $client['budget_min'] ?? '') ?>" step="1000">
                            </div>
                            <div class="col-md-6">
                                <label for="budget_max" class="form-label">Budget Maximum (TND)</label>
                                <input type="number" class="form-control" id="budget_max" name="budget_max" 
                                       value="<?= old('budget_max', $client['budget_max'] ?? '') ?>" step="1000">
                            </div>
                            <div class="col-md-6">
                                <label for="preferred_zones" class="form-label">Zones Préférées</label>
                                <?php 
                                $selectedZones = !empty($client['preferred_zones']) ? json_decode($client['preferred_zones'], true) : [];
                                ?>
                                <select class="form-select" id="preferred_zones" name="preferred_zones[]" multiple size="5">
                                    <?php foreach ($zones as $zone): ?>
                                        <option value="<?= $zone['id'] ?>" <?= in_array($zone['id'], $selectedZones ?? []) ? 'selected' : '' ?>>
                                            <?= esc($zone['name']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <small class="text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs zones</small>
                            </div>
                            <div class="col-md-6">
                                <label for="area_preference" class="form-label">Surface Souhaitée (m²)</label>
                                <input type="number" class="form-control" id="area_preference" name="area_preference" 
                                       value="<?= old('area_preference', $client['area_preference'] ?? '') ?>" step="10">
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes & Commentaires</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $client['notes']) ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <button type="button" class="btn btn-primary float-end" onclick="nextStep()">
                            Suivant <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Configuration -->
            <div class="wizard-content" data-step="3">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Étape 3 : Configuration & Attribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Type de Client <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="buyer" <?= old('type', $client['type']) == 'buyer' ? 'selected' : '' ?>>Acheteur</option>
                                    <option value="seller" <?= old('type', $client['type']) == 'seller' ? 'selected' : '' ?>>Vendeur</option>
                                    <option value="tenant" <?= old('type', $client['type']) == 'tenant' ? 'selected' : '' ?>>Locataire</option>
                                    <option value="landlord" <?= old('type', $client['type']) == 'landlord' ? 'selected' : '' ?>>Bailleur</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="source" class="form-label">Source</label>
                                <select class="form-select" id="source" name="source">
                                    <option value="website" <?= old('source', $client['source']) == 'website' ? 'selected' : '' ?>>Site Web</option>
                                    <option value="referral" <?= old('source', $client['source']) == 'referral' ? 'selected' : '' ?>>Recommandation</option>
                                    <option value="social_media" <?= old('source', $client['source']) == 'social_media' ? 'selected' : '' ?>>Réseaux Sociaux</option>
                                    <option value="walk_in" <?= old('source', $client['source']) == 'walk_in' ? 'selected' : '' ?>>Visite Spontanée</option>
                                    <option value="phone" <?= old('source', $client['source']) == 'phone' ? 'selected' : '' ?>>Téléphone</option>
                                    <option value="other" <?= old('source', $client['source']) == 'other' ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="lead" <?= old('status', $client['status']) == 'lead' ? 'selected' : '' ?>>Prospect</option>
                                    <option value="active" <?= old('status', $client['status']) == 'active' ? 'selected' : '' ?>>Actif</option>
                                    <option value="inactive" <?= old('status', $client['status']) == 'inactive' ? 'selected' : '' ?>>Inactif</option>
                                    <option value="converted" <?= old('status', $client['status']) == 'converted' ? 'selected' : '' ?>>Converti</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_agent_id" class="form-label">Agent Assigné</label>
                                <select class="form-select" id="assigned_agent_id" name="assigned_agent_id">
                                    <option value="">-- Non assigné --</option>
                                    <?php if (!empty($agents)): ?>
                                        <?php foreach ($agents as $agent): ?>
                                            <option value="<?= $agent['id'] ?>" <?= old('assigned_agent_id', $client['assigned_to']) == $agent['id'] ? 'selected' : '' ?>>
                                                <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                            </option>
                                        <?php endforeach ?>
                                    <?php else: ?>
                                        <option value="">Aucun agent disponible</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="agency_id" class="form-label">Agence</label>
                                <select class="form-select" id="agency_id" name="agency_id">
                                    <option value="">-- Non assigné --</option>
                                    <?php foreach ($agencies as $agency): ?>
                                        <option value="<?= $agency['id'] ?>" <?= old('agency_id', $client['agency_id']) == $agency['id'] ? 'selected' : '' ?>>
                                            <?= esc($agency['name']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <button type="submit" class="btn btn-success float-end">
                            <i class="fas fa-save"></i> Enregistrer les Modifications
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zone Récapitulatif -->
        <div class="col-lg-4">
            <div class="summary-card">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Récapitulatif</h5>
                    </div>
                    <div class="card-body">
                        <!-- Informations Personnelles -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-user"></i> Client</h6>
                            <p class="mb-1"><strong id="summaryName"><?= esc($client['first_name'] . ' ' . $client['last_name']) ?></strong></p>
                            <p class="text-muted small mb-1"><i class="fas fa-envelope"></i> <span id="summaryEmail"><?= esc($client['email']) ?></span></p>
                            <p class="text-muted small mb-0"><i class="fas fa-phone"></i> <span id="summaryPhone"><?= esc($client['phone']) ?></span></p>
                        </div>
                        <hr>
                        
                        <!-- Type & Statut -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-tag"></i> Type & Statut</h6>
                            <p class="mb-1"><strong>Type:</strong> <span id="summaryType"><?= ucfirst($client['type']) ?></span></p>
                            <p class="mb-0"><strong>Statut:</strong> <span id="summaryStatus"><?= ucfirst($client['status']) ?></span></p>
                        </div>
                        <hr>
                        
                        <!-- Préférences -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-search"></i> Préférences</h6>
                            <p class="text-muted small mb-1"><strong>Type:</strong> <span id="summaryPropertyType"><?= isset($client['property_type_preference']) && $client['property_type_preference'] ? ucfirst($client['property_type_preference']) : '-' ?></span></p>
                            <p class="text-muted small mb-1"><strong>Transaction:</strong> <span id="summaryTransType"><?= isset($client['transaction_type_preference']) && $client['transaction_type_preference'] ? ucfirst($client['transaction_type_preference']) : '-' ?></span></p>
                            <p class="text-muted small mb-0"><strong>Budget:</strong> <span id="summaryBudget">
                                <?php 
                                if ((isset($client['budget_min']) && $client['budget_min']) || (isset($client['budget_max']) && $client['budget_max'])) {
                                    echo (isset($client['budget_min']) && $client['budget_min'] ? number_format($client['budget_min']) : '0') . ' - ' . (isset($client['budget_max']) && $client['budget_max'] ? number_format($client['budget_max']) : '∞') . ' TND';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </span></p>
                        </div>
                        <hr>
                        
                        <!-- Attribution -->
                        <div>
                            <h6 class="text-muted mb-2"><i class="fas fa-users"></i> Attribution</h6>
                            <p class="text-muted small mb-1"><strong>Agent:</strong> <span id="summaryAgent">
                                <?php 
                                if ($client['assigned_to']) {
                                    foreach ($agents as $agent) {
                                        if ($agent['id'] == $client['assigned_to']) {
                                            echo esc($agent['first_name'] . ' ' . $agent['last_name']);
                                            break;
                                        }
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </span></p>
                            <p class="text-muted small mb-0"><strong>Agence:</strong> <span id="summaryAgency">
                                <?php 
                                if ($client['agency_id']) {
                                    foreach ($agencies as $agency) {
                                        if ($agency['id'] == $client['agency_id']) {
                                            echo esc($agency['name']);
                                            break;
                                        }
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </span></p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Supprimer le Client
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let currentStep = 1;

function nextStep() {
    if (!validateStep(currentStep)) {
        return;
    }
    
    if (currentStep < 3) {
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('completed');
        
        currentStep++;
        
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.add('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');
        
        updateSummary();
    }
}

function prevStep() {
    if (currentStep > 1) {
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('active');
        
        currentStep--;
        
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.add('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('completed');
    }
}

function validateStep(step) {
    if (step === 1) {
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        
        if (!firstName || !lastName) {
            alert('⚠️ Veuillez saisir le prénom et le nom');
            return false;
        }
        
        if (!email || !phone) {
            alert('⚠️ Veuillez saisir l\'email et le téléphone');
            return false;
        }
    }
    
    if (step === 3) {
        const type = document.getElementById('type').value;
        if (!type) {
            alert('⚠️ Veuillez sélectionner le type de client');
            return false;
        }
    }
    
    return true;
}

function updateSummary() {
    // Personal Info
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    
    document.getElementById('summaryName').textContent = firstName + ' ' + lastName;
    document.getElementById('summaryEmail').textContent = email || '-';
    document.getElementById('summaryPhone').textContent = phone || '-';
    
    // Type & Status
    const typeSelect = document.getElementById('type');
    const statusSelect = document.getElementById('status');
    document.getElementById('summaryType').textContent = typeSelect.selectedOptions[0]?.text || '-';
    document.getElementById('summaryStatus').textContent = statusSelect.selectedOptions[0]?.text || '-';
    
    // Preferences
    const propertyType = document.getElementById('property_type_preference');
    const transType = document.getElementById('transaction_type_preference');
    const budgetMin = document.getElementById('budget_min').value;
    const budgetMax = document.getElementById('budget_max').value;
    
    document.getElementById('summaryPropertyType').textContent = propertyType.selectedOptions[0]?.text || '-';
    document.getElementById('summaryTransType').textContent = transType.selectedOptions[0]?.text || '-';
    
    if (budgetMin || budgetMax) {
        document.getElementById('summaryBudget').textContent = 
            (budgetMin ? parseInt(budgetMin).toLocaleString() : '0') + ' - ' + 
            (budgetMax ? parseInt(budgetMax).toLocaleString() : '∞') + ' TND';
    } else {
        document.getElementById('summaryBudget').textContent = '-';
    }
    
    // Attribution
    const agentSelect = document.getElementById('assigned_agent_id');
    const agencySelect = document.getElementById('agency_id');
    
    document.getElementById('summaryAgent').textContent = agentSelect.selectedOptions[0]?.text || '-';
    document.getElementById('summaryAgency').textContent = agencySelect.selectedOptions[0]?.text || '-';
}

function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.')) {
        window.location.href = '<?= base_url('admin/clients/delete/' . $client['id']) ?>';
    }
}

// Event listeners for real-time summary updates
document.getElementById('first_name')?.addEventListener('input', updateSummary);
document.getElementById('last_name')?.addEventListener('input', updateSummary);
document.getElementById('email')?.addEventListener('input', updateSummary);
document.getElementById('phone')?.addEventListener('input', updateSummary);
document.getElementById('type')?.addEventListener('change', updateSummary);
document.getElementById('status')?.addEventListener('change', updateSummary);
document.getElementById('property_type_preference')?.addEventListener('change', updateSummary);
document.getElementById('transaction_type_preference')?.addEventListener('change', updateSummary);
document.getElementById('budget_min')?.addEventListener('input', updateSummary);
document.getElementById('budget_max')?.addEventListener('input', updateSummary);
document.getElementById('assigned_agent_id')?.addEventListener('change', updateSummary);
document.getElementById('agency_id')?.addEventListener('change', updateSummary);
</script>
<?= $this->endSection() ?>
