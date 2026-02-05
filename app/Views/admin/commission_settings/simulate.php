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
                    <div class="alert alert-light border">
                        <h6 class="mb-2"><i class="fas fa-info-circle"></i> Règle Appliquée</h6>
                        <div id="ruleInfo"></div>
                    </div>

                    <!-- Détails du calcul -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-user"></i> Commission Acheteur/Locataire
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td>Montant HT :</td>
                                            <td class="text-end"><strong id="buyerHT">-</strong></td>
                                        </tr>
                                        <tr>
                                            <td>TVA (19%) :</td>
                                            <td class="text-end"><strong id="buyerVAT">-</strong></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Total TTC :</strong></td>
                                            <td class="text-end"><strong class="text-primary" id="buyerTTC">-</strong></td>
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
                                            <td>Montant HT :</td>
                                            <td class="text-end"><strong id="sellerHT">-</strong></td>
                                        </tr>
                                        <tr>
                                            <td>TVA (19%) :</td>
                                            <td class="text-end"><strong id="sellerVAT">-</strong></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Total TTC :</strong></td>
                                            <td class="text-end"><strong class="text-success" id="sellerTTC">-</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total général -->
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fas fa-coins"></i> Commission Totale</h5>
                            <div class="row">
                                <div class="col-4 text-center">
                                    <div class="mb-1">HT</div>
                                    <h4 id="totalHT" class="mb-0">-</h4>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="mb-1">TVA</div>
                                    <h4 id="totalVAT" class="mb-0">-</h4>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="mb-1">TTC</div>
                                    <h3 id="totalTTC" class="mb-0 fw-bold">-</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Répartition Agent/Agence -->
                    <div class="card mt-3" id="splitCard" style="display: none;">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0"><i class="fas fa-percentage"></i> Répartition Agent / Agence</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 text-center">
                                    <div class="mb-1 text-muted">Part Agent (<span id="agentPercent">50</span>%)</div>
                                    <h5 id="agentAmount" class="text-primary mb-0">-</h5>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="mb-1 text-muted">Part Agence (<span id="agencyPercent">50</span>%)</div>
                                    <h5 id="agencyAmount" class="text-success mb-0">-</h5>
                                </div>
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
    // Afficher le conteneur de résultats
    document.getElementById('resultsContainer').style.display = 'block';
    document.getElementById('waitingMessage').style.display = 'none';
    
    // Règle appliquée
    document.getElementById('ruleInfo').innerHTML = `
        <strong>Niveau :</strong> ${commission.override_level || 'system'}<br>
        <strong>Type :</strong> ${commission.buyer_commission_type || 'N/A'}<br>
        <strong>Valeur acheteur :</strong> ${commission.buyer_commission_value || 0}<br>
        <strong>Valeur vendeur :</strong> ${commission.seller_commission_value || 0}
    `;
    
    // Commission acheteur
    document.getElementById('buyerHT').textContent = formatMoney(commission.buyer_commission_ht);
    document.getElementById('buyerVAT').textContent = formatMoney(commission.buyer_commission_vat);
    document.getElementById('buyerTTC').textContent = formatMoney(commission.buyer_commission_ttc);
    
    // Commission vendeur
    document.getElementById('sellerHT').textContent = formatMoney(commission.seller_commission_ht);
    document.getElementById('sellerVAT').textContent = formatMoney(commission.seller_commission_vat);
    document.getElementById('sellerTTC').textContent = formatMoney(commission.seller_commission_ttc);
    
    // Totaux
    document.getElementById('totalHT').textContent = formatMoney(commission.total_commission_ht);
    document.getElementById('totalVAT').textContent = formatMoney(commission.total_commission_vat);
    document.getElementById('totalTTC').textContent = formatMoney(commission.total_commission_ttc);
    
    // Répartition (si disponible)
    if (commission.agent_commission_amount && commission.agency_commission_amount) {
        document.getElementById('splitCard').style.display = 'block';
        document.getElementById('agentAmount').textContent = formatMoney(commission.agent_commission_amount);
        document.getElementById('agencyAmount').textContent = formatMoney(commission.agency_commission_amount);
        document.getElementById('agentPercent').textContent = commission.agent_commission_percentage || 50;
        document.getElementById('agencyPercent').textContent = (100 - (commission.agent_commission_percentage || 50));
    }
    
    // Scroll vers les résultats
    document.getElementById('resultsContainer').scrollIntoView({ behavior: 'smooth' });
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-TN', {
        style: 'currency',
        currency: 'TND',
        minimumFractionDigits: 2
    }).format(amount || 0);
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
