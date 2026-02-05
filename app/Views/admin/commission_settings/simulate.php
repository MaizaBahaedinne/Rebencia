<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-calculator"></i> Simulateur de Commission
    </h1>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Simulateur :</strong> 
            Calculez les commissions avant application réelle. Le système appliquera automatiquement la règle appropriée 
            en tenant compte de la hiérarchie des exceptions (Utilisateur > Rôle > Agence > Système).
        </div>
    </div>
</div>

<div class="row">
    <!-- Formulaire de simulation -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-sliders-h"></i> Paramètres de Simulation</h5>
            </div>
            <div class="card-body">
                <form id="simulationForm">
                    <?= csrf_field() ?>
                    
                    <!-- Type de transaction -->
                    <div class="mb-3">
                        <label class="form-label">Type de Transaction <span class="text-danger">*</span></label>
                        <select name="transaction_type" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="sale">Vente</option>
                            <option value="rent">Location</option>
                        </select>
                    </div>

                    <!-- Type de bien -->
                    <div class="mb-3">
                        <label class="form-label">Type de Bien <span class="text-danger">*</span></label>
                        <select name="property_type" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="apartment">Appartement</option>
                            <option value="villa">Villa</option>
                            <option value="house">Maison</option>
                            <option value="land">Terrain</option>
                            <option value="commercial">Commercial</option>
                            <option value="office">Bureau</option>
                            <option value="business">Fonds de Commerce</option>
                        </select>
                    </div>

                    <!-- Montant -->
                    <div class="mb-3">
                        <label class="form-label">Montant de la Transaction (TND) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required placeholder="Ex: 250000">
                        <small class="text-muted">Pour les locations, indiquez le loyer mensuel HT</small>
                    </div>

                    <!-- Utilisateur (agent) -->
                    <div class="mb-3">
                        <label class="form-label">Agent Responsable</label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Utiliser ma session --</option>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>">
                                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> 
                                        (<?= esc($user['role_name'] ?? 'N/A') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Laissez vide pour simuler avec votre profil actuel</small>
                    </div>

                    <!-- Boutons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calculator"></i> Calculer la Commission
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Réinitialiser
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Résultats -->
    <div class="col-md-7">
        <div id="resultsContainer" style="display: none;">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Résultat du Calcul</h5>
                </div>
                <div class="card-body">
                    <!-- Règle appliquée -->
                    <div class="alert alert-light border mb-3">
                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Règle Appliquée</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Niveau :</strong> <span id="ruleLevel" class="badge bg-secondary">-</span></p>
                                <p class="mb-1"><strong>Base Acheteur :</strong> <span id="buyerBase" class="text-primary">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Type :</strong> <span id="ruleType" class="badge bg-info">-</span></p>
                                <p class="mb-1"><strong>Base Vendeur :</strong> <span id="sellerBase" class="text-success">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Détails du calcul avec pourcentages -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-user"></i> Commission Acheteur/Locataire
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td>Base de calcul :</td>
                                            <td class="text-end">
                                                <strong id="buyerCalcBase" class="text-primary">-</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Montant HT :</td>
                                            <td class="text-end">
                                                <strong id="buyerHT">-</strong>
                                                <small class="text-muted d-block" id="buyerHTPercent"></small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>TVA (19%) :</td>
                                            <td class="text-end"><strong id="buyerVAT">-</strong></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Total TTC :</strong></td>
                                            <td class="text-end">
                                                <strong class="text-primary fs-5" id="buyerTTC">-</strong>
                                                <small class="text-muted d-block" id="buyerTTCPercent"></small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="text-success mb-3">
                                        <i class="fas fa-home"></i> Commission Vendeur/Propriétaire
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td>Base de calcul :</td>
                                            <td class="text-end">
                                                <strong id="sellerCalcBase" class="text-success">-</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Montant HT :</td>
                                            <td class="text-end">
                                                <strong id="sellerHT">-</strong>
                                                <small class="text-muted d-block" id="sellerHTPercent"></small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>TVA (19%) :</td>
                                            <td class="text-end"><strong id="sellerVAT">-</strong></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Total TTC :</strong></td>
                                            <td class="text-end">
                                                <strong class="text-success fs-5" id="sellerTTC">-</strong>
                                                <small class="text-muted d-block" id="sellerTTCPercent"></small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total général -->
                    <div class="card bg-primary text-white mb-3">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fas fa-coins"></i> Commission Totale</h5>
                            <div class="row">
                                <div class="col-4 text-center">
                                    <div class="mb-1 small">Hors Taxes</div>
                                    <h4 id="totalHT" class="mb-0">-</h4>
                                    <small id="totalHTPercent" class="opacity-75">-</small>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="mb-1 small">TVA (19%)</div>
                                    <h4 id="totalVAT" class="mb-0">-</h4>
                                    <small class="opacity-75">du montant HT</small>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="mb-1 small">Toutes Taxes Comprises</div>
                                    <h3 id="totalTTC" class="mb-0 fw-bold">-</h3>
                                    <small id="totalTTCPercent" class="opacity-75">-</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Répartition Agent/Agence -->
                    <div class="card" id="splitCard">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="fas fa-percentage"></i> Répartition Agent / Agence</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <div class="border-end">
                                        <div class="mb-2">
                                            <i class="fas fa-user-tie fa-2x text-primary"></i>
                                        </div>
                                        <h6 class="text-muted mb-1">Part Agent</h6>
                                        <h4 class="text-primary mb-1" id="agentAmount">-</h4>
                                        <div class="progress mx-auto" style="width: 80%; height: 25px;">
                                            <div id="agentProgressBar" class="progress-bar bg-primary" style="width: 50%;">
                                                <strong id="agentPercent">50%</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">
                                        <i class="fas fa-building fa-2x text-success"></i>
                                    </div>
                                    <h6 class="text-muted mb-1">Part Agence</h6>
                                    <h4 class="text-success mb-1" id="agencyAmount">-</h4>
                                    <div class="progress mx-auto" style="width: 80%; height: 25px;">
                                        <div id="agencyProgressBar" class="progress-bar bg-success" style="width: 50%;">
                                            <strong id="agencyPercent">50%</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Détails répartition -->
                            <div class="alert alert-info mb-0">
                                <small>
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Répartition par défaut :</strong> 50% agent / 50% agence sur la commission TTC totale.
                                    Cette répartition peut être personnalisée par transaction.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message d'attente -->
        <div id="waitingMessage" class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Remplissez le formulaire pour calculer</h5>
                <p class="text-muted small mb-0">Les résultats s'afficheront ici</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('simulationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    
    // Afficher un loader
    document.getElementById('waitingMessage').innerHTML = `
        <div class="card-body text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Calcul en cours...</span>
            </div>
            <h5 class="text-muted">Calcul en cours...</h5>
        </div>
    `;
    
    fetch('<?= base_url('admin/commission-settings/process-simulation') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success && result.commission) {
            displayResults(result.commission);
        } else {
            alert('Erreur : ' + (result.error || 'Calcul impossible'));
            document.getElementById('waitingMessage').innerHTML = `
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5 class="text-danger">Erreur de calcul</h5>
                    <p class="text-muted">${result.error || 'Une erreur est survenue'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur de communication avec le serveur');
    });
});

function displayResults(commission) {
    // Récupérer le montant de la transaction du formulaire
    const transactionAmount = parseFloat(document.querySelector('input[name="amount"]').value) || 0;
    
    // Afficher le conteneur de résultats
    document.getElementById('resultsContainer').style.display = 'block';
    document.getElementById('waitingMessage').style.display = 'none';
    
    // Règle appliquée - Informations détaillées
    const levelLabels = {
        'user': 'Utilisateur (Priorité maximale)',
        'role': 'Rôle',
        'agency': 'Agence',
        'system': 'Système (Par défaut)'
    };
    
    const typeLabels = {
        'percentage': 'Pourcentage',
        'fixed': 'Montant Fixe',
        'months': 'Mois de loyer'
    };
    
    document.getElementById('ruleLevel').textContent = levelLabels[commission.override_level] || 'Système';
    document.getElementById('ruleLevel').className = 'badge bg-' + 
        (commission.override_level === 'user' ? 'danger' : 
         commission.override_level === 'role' ? 'warning' : 
         commission.override_level === 'agency' ? 'info' : 'secondary');
    
    document.getElementById('ruleType').textContent = typeLabels[commission.buyer_commission_type] || 'N/A';
    
    // Bases de calcul
    const buyerUnit = commission.buyer_commission_type === 'percentage' ? '%' : 
                      commission.buyer_commission_type === 'months' ? ' mois' : ' TND';
    const sellerUnit = commission.seller_commission_type === 'percentage' ? '%' : 
                       commission.seller_commission_type === 'months' ? ' mois' : ' TND';
    
    document.getElementById('buyerBase').textContent = commission.buyer_commission_value + buyerUnit;
    document.getElementById('sellerBase').textContent = commission.seller_commission_value + sellerUnit;
    
    document.getElementById('buyerCalcBase').textContent = commission.buyer_commission_value + buyerUnit;
    document.getElementById('sellerCalcBase').textContent = commission.seller_commission_value + sellerUnit;
    
    // Commission acheteur avec pourcentages
    const buyerHTPercent = transactionAmount > 0 ? ((commission.buyer_commission_ht / transactionAmount) * 100).toFixed(2) : 0;
    const buyerTTCPercent = transactionAmount > 0 ? ((commission.buyer_commission_ttc / transactionAmount) * 100).toFixed(2) : 0;
    
    document.getElementById('buyerHT').textContent = formatMoney(commission.buyer_commission_ht);
    document.getElementById('buyerHTPercent').textContent = `(${buyerHTPercent}% du montant transaction)`;
    document.getElementById('buyerVAT').textContent = formatMoney(commission.buyer_commission_vat);
    document.getElementById('buyerTTC').textContent = formatMoney(commission.buyer_commission_ttc);
    document.getElementById('buyerTTCPercent').textContent = `(${buyerTTCPercent}% du montant transaction)`;
    
    // Commission vendeur avec pourcentages
    const sellerHTPercent = transactionAmount > 0 ? ((commission.seller_commission_ht / transactionAmount) * 100).toFixed(2) : 0;
    const sellerTTCPercent = transactionAmount > 0 ? ((commission.seller_commission_ttc / transactionAmount) * 100).toFixed(2) : 0;
    
    document.getElementById('sellerHT').textContent = formatMoney(commission.seller_commission_ht);
    document.getElementById('sellerHTPercent').textContent = `(${sellerHTPercent}% du montant transaction)`;
    document.getElementById('sellerVAT').textContent = formatMoney(commission.seller_commission_vat);
    document.getElementById('sellerTTC').textContent = formatMoney(commission.seller_commission_ttc);
    document.getElementById('sellerTTCPercent').textContent = `(${sellerTTCPercent}% du montant transaction)`;
    
    // Totaux avec pourcentages
    const totalHTPercent = transactionAmount > 0 ? ((commission.total_commission_ht / transactionAmount) * 100).toFixed(2) : 0;
    const totalTTCPercent = transactionAmount > 0 ? ((commission.total_commission_ttc / transactionAmount) * 100).toFixed(2) : 0;
    
    document.getElementById('totalHT').textContent = formatMoney(commission.total_commission_ht);
    document.getElementById('totalHTPercent').textContent = `${totalHTPercent}% du montant`;
    document.getElementById('totalVAT').textContent = formatMoney(commission.total_commission_vat);
    document.getElementById('totalTTC').textContent = formatMoney(commission.total_commission_ttc);
    document.getElementById('totalTTCPercent').textContent = `${totalTTCPercent}% du montant`;
    
    // Répartition Agent/Agence
    const agentPercentage = commission.agent_commission_percentage || 50;
    const agencyPercentage = 100 - agentPercentage;
    
    // Calculer les montants si non fournis
    const agentAmount = commission.agent_commission_amount || (commission.total_commission_ttc * agentPercentage / 100);
    const agencyAmount = commission.agency_commission_amount || (commission.total_commission_ttc * agencyPercentage / 100);
    
    document.getElementById('agentAmount').textContent = formatMoney(agentAmount);
    document.getElementById('agencyAmount').textContent = formatMoney(agencyAmount);
    document.getElementById('agentPercent').textContent = agentPercentage + '%';
    document.getElementById('agencyPercent').textContent = agencyPercentage + '%';
    
    // Mettre à jour les barres de progression
    document.getElementById('agentProgressBar').style.width = agentPercentage + '%';
    document.getElementById('agencyProgressBar').style.width = agencyPercentage + '%';
    
    // Scroll vers les résultats
    document.getElementById('resultsContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-TN', {
        style: 'currency',
        currency: 'TND',
        minimumFractionDigits: 2
    }).format(amount || 0);
}

function formatPercent(value) {
    return parseFloat(value || 0).toFixed(2) + '%';
}

// Reset form
document.querySelector('button[type="reset"]').addEventListener('click', function() {
    document.getElementById('resultsContainer').style.display = 'none';
    document.getElementById('waitingMessage').style.display = 'block';
    document.getElementById('waitingMessage').innerHTML = `
        <div class="card-body text-center py-5">
            <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Remplissez le formulaire pour calculer</h5>
            <p class="text-muted small mb-0">Les résultats s'afficheront ici</p>
        </div>
    `;
});
</script>
<?= $this->endSection() ?>
