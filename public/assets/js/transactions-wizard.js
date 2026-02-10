// Global Variables
let currentStep = 1;
let selectedPropertyData = null;
let commissionData = null;

// Navigation Steps
function nextStep() {
    if (!validateStep(currentStep)) {
        return;
    }
    
    if (currentStep < 4) {
        // Hide current step
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('completed');
        
        currentStep++;
        
        // Show next step
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.add('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');
        
        // Si on arrive à l'étape 4, calculer la commission
        if (currentStep === 4) {
            updateCommission();
        }
        
        // Update summary
        updateSummary();
    }
}

function prevStep() {
    if (currentStep > 1) {
        // Hide current step
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('active');
        
        currentStep--;
        
        // Show previous step
        document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.add('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');
        document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.remove('completed');
    }
}

// Validation des étapes
function validateStep(step) {
    if (step === 1) {
        const propertyId = document.getElementById('property_id').value;
        if (!propertyId) {
            alert('⚠️ Veuillez sélectionner un bien immobilier');
            return false;
        }
    }
    
    if (step === 2) {
        const buyerId = document.getElementById('buyer_id').value;
        const agentId = document.getElementById('agent_id').value;
        
        if (!buyerId) {
            alert('⚠️ Veuillez sélectionner un acheteur/locataire');
            return false;
        }
        
        if (!agentId) {
            alert('⚠️ Veuillez sélectionner un agent responsable');
            return false;
        }
    }
    
    if (step === 3) {
        const amount = document.getElementById('amount').value;
        const date = document.getElementById('transaction_date').value;
        
        if (!amount || amount <= 0) {
            alert('⚠️ Veuillez saisir un montant valide');
            return false;
        }
        
        if (!date) {
            alert('⚠️ Veuillez sélectionner une date de transaction');
            return false;
        }
    }
    
    return true;
}

// Sélection du bien
function selectProperty(propertyId, element) {
    // Remove previous selection
    document.querySelectorAll('.property-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Mark as selected
    element.classList.add('selected');
    
    // Get property data from data attributes
    const propertyItem = element.closest('.property-item');
    const reference = propertyItem.getAttribute('data-reference');
    const title = propertyItem.getAttribute('data-title');
    const type = propertyItem.getAttribute('data-type');
    const transaction = propertyItem.getAttribute('data-transaction');
    const price = parseFloat(propertyItem.getAttribute('data-price'));
    const rental = parseFloat(propertyItem.getAttribute('data-rental'));
    
    // Store selected property data
    selectedPropertyData = {
        id: propertyId,
        reference: reference,
        title: title,
        type: type,
        transaction_type: transaction,
        price: price,
        rental_price: rental
    };
    
    // Set hidden input
    document.getElementById('property_id').value = propertyId;
    
    // Auto-fill transaction type
    const typeSelect = document.getElementById('type');
    if (transaction === 'sale') {
        typeSelect.value = 'sale';
        document.getElementById('amount').value = price;
    } else if (transaction === 'rent') {
        typeSelect.value = 'rent';
        document.getElementById('amount').value = rental;
    }
    
    // Update summary
    updateSummary();
}

// Filtre des biens
function filterProperties() {
    const searchText = document.getElementById('searchProperty').value.toLowerCase();
    const filterType = document.getElementById('filterType').value;
    const filterTransaction = document.getElementById('filterTransaction').value;
    
    document.querySelectorAll('.property-item').forEach(item => {
        const reference = item.getAttribute('data-reference');
        const title = item.getAttribute('data-title');
        const type = item.getAttribute('data-type');
        const transaction = item.getAttribute('data-transaction');
        
        let showItem = true;
        
        // Text search
        if (searchText && !reference.includes(searchText) && !title.includes(searchText)) {
            showItem = false;
        }
        
        // Type filter
        if (filterType && type !== filterType) {
            showItem = false;
        }
        
        // Transaction filter
        if (filterTransaction && transaction !== filterTransaction) {
            showItem = false;
        }
        
        item.style.display = showItem ? 'block' : 'none';
    });
}

// Mettre à jour le montant selon le type de transaction
function updateAmount() {
    if (!selectedPropertyData) return;
    
    const type = document.getElementById('type').value;
    const amountInput = document.getElementById('amount');
    
    if (type === 'sale') {
        amountInput.value = selectedPropertyData.price;
    } else if (type === 'rent') {
        amountInput.value = selectedPropertyData.rental_price;
    }
    
    updateSummary();
}

// Calculer et afficher la commission
async function updateCommission() {
    const propertyId = document.getElementById('property_id').value;
    const agentId = document.getElementById('agent_id').value;
    const type = document.getElementById('type').value;
    const amount = document.getElementById('amount').value;
    
    if (!propertyId || !agentId || !amount) {
        return;
    }
    
    // Show loading
    const previewDiv = document.getElementById('commissionPreview');
    previewDiv.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-3 text-muted">Calcul de la commission en cours...</p>
        </div>
    `;
    
    try {
        const response = await fetch(COMMISSION_SIMULATION_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                property_id: propertyId,
                user_id: agentId,
                transaction_type: type,
                amount: amount
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            commissionData = result.data;
            displayCommissionPreview(result.data);
            updateSummary();
        } else {
            previewDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${result.message}
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        previewDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Erreur lors du calcul de la commission
            </div>
        `;
    }
}

// Afficher l'aperçu de la commission
function displayCommissionPreview(data) {
    const previewDiv = document.getElementById('commissionPreview');
    
    const buyerPercentage = data.commission_buyer > 0 
        ? ((data.commission_buyer_ht / parseFloat(document.getElementById('amount').value)) * 100).toFixed(2) 
        : 0;
    const sellerPercentage = data.commission_seller > 0 
        ? ((data.commission_seller_ht / parseFloat(document.getElementById('amount').value)) * 100).toFixed(2) 
        : 0;
    
    let html = `
        <div class="mb-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Règle appliquée:</strong> ${data.rule.name || 'Règle par défaut'}
                <span class="badge bg-primary ms-2">${data.rule.buyer_commission || 0}% Acheteur + ${data.rule.seller_commission || 0}% Vendeur</span>
            </div>
        </div>
    `;
    
    // Buyer Commission
    if (data.commission_buyer > 0) {
        html += `
            <div class="card mb-3 border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user"></i> Commission Acheteur</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">Base de calcul</small>
                            <div><strong>${formatMoney(document.getElementById('amount').value)} TND</strong></div>
                            <small class="text-primary">${buyerPercentage}%</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">Montant HT</small>
                            <div><strong>${formatMoney(data.commission_buyer_ht)} TND</strong></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">TVA (19%)</small>
                            <div><strong>${formatMoney(data.commission_buyer_vat)} TND</strong></div>
                        </div>
                    </div>
                    <div class="border-top pt-2 mt-2">
                        <div class="d-flex justify-content-between">
                            <strong>Total TTC:</strong>
                            <strong class="text-primary">${formatMoney(data.commission_buyer)} TND</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Seller Commission
    if (data.commission_seller > 0) {
        html += `
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-user-tie"></i> Commission Vendeur</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">Base de calcul</small>
                            <div><strong>${formatMoney(document.getElementById('amount').value)} TND</strong></div>
                            <small class="text-success">${sellerPercentage}%</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">Montant HT</small>
                            <div><strong>${formatMoney(data.commission_seller_ht)} TND</strong></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">TVA (19%)</small>
                            <div><strong>${formatMoney(data.commission_seller_vat)} TND</strong></div>
                        </div>
                    </div>
                    <div class="border-top pt-2 mt-2">
                        <div class="d-flex justify-content-between">
                            <strong>Total TTC:</strong>
                            <strong class="text-success">${formatMoney(data.commission_seller)} TND</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Total Commission
    html += `
        <div class="card bg-light border-dark mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <small class="text-muted">Total HT</small>
                        <div class="h5 mb-0">${formatMoney(data.total_commission_ht)} TND</div>
                    </div>
                    <div class="col-4">
                        <small class="text-muted">TVA (19%)</small>
                        <div class="h5 mb-0">${formatMoney(data.total_vat)} TND</div>
                    </div>
                    <div class="col-4">
                        <small class="text-muted">Total TTC</small>
                        <div class="h5 mb-0 text-success">${formatMoney(data.total_commission)} TND</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agent/Agency Split
    const agentAmount = data.total_commission / 2;
    const agencyAmount = data.total_commission / 2;
    
    html += `
        <div class="card border-warning">
            <div class="card-header bg-warning">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Répartition Agent/Agence</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-user"></i> Part Agent (50%)</span>
                        <strong>${formatMoney(agentAmount)} TND</strong>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                            50%
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-building"></i> Part Agence (50%)</span>
                        <strong>${formatMoney(agencyAmount)} TND</strong>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                            50%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    previewDiv.innerHTML = html;
}

// Mettre à jour le récapitulatif
function updateSummary() {
    // Property
    if (selectedPropertyData) {
        document.getElementById('summaryProperty').innerHTML = `
            <h6 class="text-muted mb-2"><i class="fas fa-building"></i> Bien</h6>
            <p class="mb-1"><strong>${selectedPropertyData.reference.toUpperCase()}</strong></p>
            <p class="text-muted small mb-1">${selectedPropertyData.title}</p>
            <span class="badge bg-info">${selectedPropertyData.type}</span>
        `;
    }
    
    // Parties
    const buyerSelect = document.getElementById('buyer_id');
    const sellerSelect = document.getElementById('seller_id');
    const agentSelect = document.getElementById('agent_id');
    
    document.getElementById('summaryBuyer').textContent = 
        buyerSelect.selectedOptions[0]?.text || '-';
    document.getElementById('summarySeller').textContent = 
        sellerSelect.selectedOptions[0]?.text || '-';
    document.getElementById('summaryAgent').textContent = 
        agentSelect.selectedOptions[0]?.text || '-';
    
    // Transaction
    const typeSelect = document.getElementById('type');
    const amount = document.getElementById('amount').value;
    const date = document.getElementById('transaction_date').value;
    
    document.getElementById('summaryType').textContent = 
        typeSelect.selectedOptions[0]?.text || '-';
    document.getElementById('summaryAmount').textContent = 
        amount ? formatMoney(amount) + ' TND' : '-';
    document.getElementById('summaryDate').textContent = 
        date ? new Date(date).toLocaleDateString('fr-FR') : '-';
    
    // Commission
    if (commissionData) {
        document.getElementById('summaryCommHT').textContent = 
            formatMoney(commissionData.total_commission_ht) + ' TND';
        document.getElementById('summaryCommVAT').textContent = 
            formatMoney(commissionData.total_vat) + ' TND';
        document.getElementById('summaryCommTTC').textContent = 
            formatMoney(commissionData.total_commission) + ' TND';
    }
}

// Format money
function formatMoney(value) {
    return parseFloat(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

// Auto-select agency when agent is selected
document.getElementById('agent_id')?.addEventListener('change', function() {
    const selectedOption = this.selectedOptions[0];
    const agencyId = selectedOption?.getAttribute('data-agency');
    
    if (agencyId) {
        document.getElementById('agency_id').value = agencyId;
    }
    
    updateSummary();
});

// Event listeners for filters
document.getElementById('searchProperty')?.addEventListener('input', filterProperties);
document.getElementById('filterType')?.addEventListener('change', filterProperties);
document.getElementById('filterTransaction')?.addEventListener('change', filterProperties);

// Event listeners for summary updates
document.getElementById('buyer_id')?.addEventListener('change', updateSummary);
document.getElementById('seller_id')?.addEventListener('change', updateSummary);
document.getElementById('type')?.addEventListener('change', updateSummary);
document.getElementById('amount')?.addEventListener('input', updateSummary);
document.getElementById('transaction_date')?.addEventListener('change', updateSummary);
