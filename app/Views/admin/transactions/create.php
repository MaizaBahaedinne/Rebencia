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
.property-card {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}
.property-card:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.property-card.selected {
    border-color: #28a745;
    background-color: #f0f9f4;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-contract"></i> Nouvelle Transaction
    </h1>
    <a href="<?= base_url('admin/transactions') ?>" class="btn btn-secondary">
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

<!-- DEBUG: <?= base_url('admin/transactions/store') ?> -->
<form action="<?= base_url('admin/transactions/store') ?>" method="POST" id="transactionForm" autocomplete="off">
    <?= csrf_field() ?>
    
    <div class="row">
        <!-- Zone Principale - Wizard -->
        <div class="col-lg-8">
            <!-- Steps -->
            <div class="wizard-steps">
                <div class="wizard-step active" data-step="1">
                    <div class="wizard-step-circle">1</div>
                    <div class="wizard-step-title">Bien</div>
                </div>
                <div class="wizard-step" data-step="2">
                    <div class="wizard-step-circle">2</div>
                    <div class="wizard-step-title">Parties</div>
                </div>
                <div class="wizard-step" data-step="3">
                    <div class="wizard-step-circle">3</div>
                    <div class="wizard-step-title">Détails</div>
                </div>
                <div class="wizard-step" data-step="4">
                    <div class="wizard-step-circle">4</div>
                    <div class="wizard-step-title">Commission</div>
                </div>
            </div>

            <!-- Step 1: Sélection du Bien -->
            <div class="wizard-content active" data-step="1">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-building"></i> Étape 1 : Sélection du Bien</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtre de recherche -->
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="searchProperty" placeholder="🔍 Rechercher par référence ou titre...">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filterType">
                                        <option value="">Tous les types</option>
                                        <option value="apartment">Appartement</option>
                                        <option value="villa">Villa</option>
                                        <option value="house">Maison</option>
                                        <option value="land">Terrain</option>
                                        <option value="commercial">Commercial</option>
                                        <option value="office">Bureau</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="filterTransaction">
                                        <option value="">Toutes transactions</option>
                                        <option value="sale">À vendre</option>
                                        <option value="rent">À louer</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Liste des biens -->
                        <div class="row g-3" id="propertiesList">
                            <?php foreach ($properties as $property): ?>
                                <div class="col-md-6 property-item" 
                                     data-reference="<?= strtolower($property['reference']) ?>"
                                     data-title="<?= strtolower($property['title']) ?>"
                                     data-type="<?= $property['type'] ?>"
                                     data-transaction="<?= $property['transaction_type'] ?>"
                                     data-property-id="<?= $property['id'] ?>"
                                     data-price="<?= $property['price'] ?>"
                                     data-rental="<?= $property['rental_price'] ?? 0 ?>"
                                     data-owner-id="<?= $property['owner_id'] ?? '' ?>"
                                     data-owner-name="<?= esc($property['owner_name'] ?? '') ?>"
                                     data-agent-id="<?= $property['agent_id'] ?? '' ?>"
                                     data-agent-name="<?= esc($property['agent_name'] ?? '') ?>"
                                     data-agency-id="<?= $property['agent_agency_id'] ?? '' ?>"
                                     data-agency-name="<?= esc($property['agency_name'] ?? '') ?>">
                                    <div class="property-card card h-100" onclick="selectProperty(<?= $property['id'] ?>, this)">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0"><?= esc($property['reference']) ?></h6>
                                                <span class="badge bg-info"><?= ucfirst($property['type']) ?></span>
                                            </div>
                                            <p class="text-muted small mb-2"><?= esc($property['title']) ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-primary fw-bold">
                                                    <?= number_format($property['price'], 0, ',', ' ') ?> TND
                                                </span>
                                                <?php if ($property['rental_price']): ?>
                                                    <span class="text-success small">
                                                        <?= number_format($property['rental_price'], 0, ',', ' ') ?> TND/mois
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <input type="hidden" name="property_id" id="property_id" required>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary float-end" onclick="nextStep()">
                            Suivant <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Parties -->
            <div class="wizard-content" data-step="2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Étape 2 : Les Parties</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Acheteur/Locataire <span class="text-danger">*</span></label>
                                <select class="form-select" name="buyer_id" id="buyer_id" required>
                                    <option value="">-- Sélectionner --</option>
                                    <?php if (!empty($buyers)): ?>
                                        <?php foreach ($buyers as $buyer): ?>
                                            <option value="<?= $buyer['id'] ?>">
                                                <?= esc($buyer['first_name'] . ' ' . $buyer['last_name']) ?> - <?= esc($buyer['phone']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Aucun acheteur disponible</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vendeur/Propriétaire</label>
                                <select class="form-select" name="seller_id" id="seller_id">
                                    <option value="">-- Sélectionner --</option>
                                    <?php if (!empty($sellers)): ?>
                                        <?php foreach ($sellers as $seller): ?>
                                            <option value="<?= $seller['id'] ?>">
                                                <?= esc($seller['first_name'] . ' ' . $seller['last_name']) ?> - <?= esc($seller['phone']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Aucun vendeur disponible</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Agent Responsable <span class="text-danger">*</span></label>
                                <select class="form-select" name="agent_id" id="agent_id" required onchange="updateCommission()">
                                    <option value="">-- Sélectionner --</option>
                                    <?php if (!empty($agents)): ?>
                                        <?php foreach ($agents as $agent): ?>
                                            <option value="<?= $agent['id'] ?>" 
                                                    data-role="<?= $agent['role_id'] ?? '' ?>"
                                                    data-agency="<?= $agent['agency_id'] ?? '' ?>"
                                                    <?= $agent['id'] == session()->get('user_id') ? 'selected' : '' ?>>
                                                <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Aucun agent disponible</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Agence</label>
                                <select class="form-select" name="agency_id" id="agency_id">
                                    <option value="">-- Sélectionner --</option>
                                    <?php if (!empty($agencies)): ?>
                                        <?php foreach ($agencies as $agency): ?>
                                            <option value="<?= $agency['id'] ?>">
                                                <?= esc($agency['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Aucune agence disponible</option>
                                    <?php endif; ?>
                                </select>
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

            <!-- Step 3: Détails -->
            <div class="wizard-content" data-step="3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Étape 3 : Détails de la Transaction</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Type de Transaction <span class="text-danger">*</span></label>
                                <select class="form-select" name="type" id="type" required onchange="updateAmount()">
                                    <option value="sale">Vente</option>
                                    <option value="rent">Location</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Montant (TND) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" id="amount" step="0.01" required onchange="updateCommission()">
                                <small class="text-muted">Pour location : loyer mensuel HT</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date Transaction <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="transaction_date" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Statut</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="pending">En attente</option>
                                    <option value="signed">Signé</option>
                                    <option value="completed">Complété</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Numéro de Contrat</label>
                                <input type="text" class="form-control" name="contract_number" id="contract_number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notaire</label>
                                <input type="text" class="form-control" name="notary" id="notary">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
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

            <!-- Step 4: Commission -->
            <div class="wizard-content" data-step="4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Étape 4 : Calcul de la Commission</h5>
                    </div>
                    <div class="card-body" id="commissionPreview">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-3 text-muted">Calcul de la commission en cours...</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <button type="submit" class="btn btn-success float-end">
                            <i class="fas fa-save"></i> Créer la Transaction
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
                        <!-- Bien -->
                        <div class="mb-3" id="summaryProperty">
                            <h6 class="text-muted mb-2"><i class="fas fa-building"></i> Bien</h6>
                            <p class="text-muted small mb-0">Non sélectionné</p>
                        </div>
                        <hr>
                        
                        <!-- Parties -->
                        <div class="mb-3" id="summaryParties">
                            <h6 class="text-muted mb-2"><i class="fas fa-users"></i> Parties</h6>
                            <p class="text-muted small mb-1"><strong>Acheteur:</strong> <span id="summaryBuyer">-</span></p>
                            <p class="text-muted small mb-1"><strong>Vendeur:</strong> <span id="summarySeller">-</span></p>
                            <p class="text-muted small mb-0"><strong>Agent:</strong> <span id="summaryAgent">-</span></p>
                        </div>
                        <hr>
                        
                        <!-- Transaction -->
                        <div class="mb-3" id="summaryTransaction">
                            <h6 class="text-muted mb-2"><i class="fas fa-file-contract"></i> Transaction</h6>
                            <p class="text-muted small mb-1"><strong>Type:</strong> <span id="summaryType">-</span></p>
                            <p class="text-muted small mb-1"><strong>Montant:</strong> <span id="summaryAmount">-</span></p>
                            <p class="text-muted small mb-0"><strong>Date:</strong> <span id="summaryDate">-</span></p>
                        </div>
                        <hr>
                        
                        <!-- Commission -->
                        <div id="summaryCommission">
                            <h6 class="text-success mb-2"><i class="fas fa-dollar-sign"></i> Commission</h6>
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">Total HT:</span>
                                    <strong id="summaryCommHT">-</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">TVA (19%):</span>
                                    <strong id="summaryCommVAT">-</strong>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2">
                                    <span><strong>Total TTC:</strong></span>
                                    <strong class="text-success" id="summaryCommTTC">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Pass PHP variables to JavaScript
const BASE_URL = '<?= base_url() ?>';
const COMMISSION_SIMULATION_URL = '<?= base_url("admin/commission-settings/process-simulation") ?>';
</script>
<script src="<?= base_url('assets/js/transactions-wizard.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
