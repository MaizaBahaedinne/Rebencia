<!-- Étape 4: Tarification et charges -->
<div class="row g-4">
    <!-- Prix -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-tag text-primary"></i> Prix</h6>
    </div>

    <div class="col-md-6">
        <label for="price" class="form-label">
            Prix de vente <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input type="number" 
                   class="form-control" 
                   id="price" 
                   name="price" 
                   value="<?= old('price', $property['price'] ?? '') ?>"
                   step="0.01"
                   required>
            <span class="input-group-text">TND</span>
        </div>
    </div>

    <div class="col-md-6">
        <label for="rental_price" class="form-label">
            Prix de location (mensuel)
        </label>
        <div class="input-group">
            <input type="number" 
                   class="form-control" 
                   id="rental_price" 
                   name="rental_price" 
                   value="<?= old('rental_price', $property['rental_price'] ?? '') ?>"
                   step="0.01">
            <span class="input-group-text">TND/mois</span>
        </div>
    </div>

    <!-- Promotion -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3"><i class="fas fa-percent text-danger"></i> Promotion</h6>
    </div>

    <div class="col-md-4">
        <label for="promo_price" class="form-label">Prix promotionnel</label>
        <div class="input-group">
            <input type="number" 
                   class="form-control" 
                   id="promo_price" 
                   name="promo_price" 
                   value="<?= old('promo_price', $property['promo_price'] ?? '') ?>"
                   step="0.01">
            <span class="input-group-text">TND</span>
        </div>
    </div>

    <div class="col-md-4">
        <label for="promo_start_date" class="form-label">Date début promo</label>
        <input type="date" 
               class="form-control" 
               id="promo_start_date" 
               name="promo_start_date" 
               value="<?= old('promo_start_date', $property['promo_start_date'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label for="promo_end_date" class="form-label">Date fin promo</label>
        <input type="date" 
               class="form-control" 
               id="promo_end_date" 
               name="promo_end_date" 
               value="<?= old('promo_end_date', $property['promo_end_date'] ?? '') ?>">
    </div>

    <!-- Charges -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3"><i class="fas fa-file-invoice-dollar text-success"></i> Charges mensuelles</h6>
        <p class="text-muted small">Renseignez les charges mensuelles associées au bien (si applicable)</p>
    </div>

    <div class="col-md-6">
        <label for="charge_syndic" class="form-label">Charges de syndic</label>
        <div class="input-group">
            <input type="number" 
                   class="form-control" 
                   id="charge_syndic" 
                   name="charge_syndic" 
                   value="<?= old('charge_syndic', $property['charge_syndic'] ?? '') ?>"
                   step="0.01">
            <span class="input-group-text">TND/mois</span>
        </div>
    </div>

    <div class="col-md-6">
        <label for="charge_water" class="form-label">Charges d'eau</label>
        <div class="input-group">
            <input type="number" 
                   class="form-control" 
                   id="charge_water" 
                   name="charge_water" 
                   value="<?= old('charge_water', $property['charge_water'] ?? '') ?>"
                   step="0.01">
            <span class="input-group-text">TND/mois</span>
        </div>
    </div>

    <div class="col-md-6">
        <label for="charge_gas" class="form-label">Charges de gaz</label>
        <div class="input-group">
            <input type="number" 
                   class="form-control" 
                   id="charge_gas" 
                   name="charge_gas" 
                   value="<?= old('charge_gas', $property['charge_gas'] ?? '') ?>"
                   step="0.01">
            <span class="input-group-text">TND/mois</span>
        </div>
    </div>

    <div class="col-md-6">
        <label for="charge_electricity" class="form-label">Charges d'électricité</label>
        <div class="input-group">
            <input type="number" 
                   class="form-control" 
                   id="charge_electricity" 
                   name="charge_electricity" 
                   value="<?= old('charge_electricity', $property['charge_electricity'] ?? '') ?>"
                   step="0.01">
            <span class="input-group-text">TND/mois</span>
        </div>
    </div>

    <div class="col-md-12">
        <label for="charge_other" class="form-label">Autres charges</label>
        <div class="input-group">
            <input type="number" 
                   class="form-control" 
                   id="charge_other" 
                   name="charge_other" 
                   value="<?= old('charge_other', $property['charge_other'] ?? '') ?>"
                   step="0.01">
            <span class="input-group-text">TND/mois</span>
        </div>
        <small class="text-muted">Ex: gardiennage, entretien espaces verts, etc.</small>
    </div>

    <!-- Récapitulatif charges -->
    <div class="col-12 mt-4">
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-calculator me-3 fs-4"></i>
            <div>
                <strong>Total des charges mensuelles: </strong>
                <span id="total_charges" class="fs-5">0.00 TND</span>
            </div>
        </div>
    </div>
</div>

<script>
// Calculer le total des charges
function calculateTotalCharges() {
    const charges = [
        parseFloat(document.getElementById('charge_syndic')?.value || 0),
        parseFloat(document.getElementById('charge_water')?.value || 0),
        parseFloat(document.getElementById('charge_gas')?.value || 0),
        parseFloat(document.getElementById('charge_electricity')?.value || 0),
        parseFloat(document.getElementById('charge_other')?.value || 0)
    ];
    
    const total = charges.reduce((sum, charge) => sum + charge, 0);
    document.getElementById('total_charges').textContent = total.toFixed(2) + ' TND';
}

// Écouter les changements sur tous les champs de charges
document.addEventListener('DOMContentLoaded', function() {
    const chargeInputs = ['charge_syndic', 'charge_water', 'charge_gas', 'charge_electricity', 'charge_other'];
    chargeInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', calculateTotalCharges);
        }
    });
    
    // Calculer au chargement
    calculateTotalCharges();
});
</script>
