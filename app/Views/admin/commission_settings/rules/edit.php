<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit"></i> Modifier la Règle de Commission
    </h1>
    <a href="<?= base_url('admin/commission-settings/rules') ?>" class="btn btn-secondary">
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

<form action="<?= base_url('admin/commission-settings/update-rule/' . $rule['id']) ?>" method="post">
    <?= csrf_field() ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label">Nom de la Règle <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name', $rule['name']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Type de Transaction</label>
                            <input type="text" class="form-control" 
                                   value="<?= $rule['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?>" disabled>
                            <small class="text-muted">Le type de transaction ne peut pas être modifié</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Type de Bien</label>
                            <input type="text" class="form-control" 
                                   value="<?= ucfirst(str_replace('_', ' ', $rule['property_type'])) ?>" disabled>
                            <small class="text-muted">Le type de bien ne peut pas être modifié</small>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $rule['description']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Commission Acheteur/Locataire</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="buyer_commission_type" class="form-label">Type de Commission <span class="text-danger">*</span></label>
                            <select class="form-select" id="buyer_commission_type" name="buyer_commission_type" required onchange="updateBuyerLabel()">
                                <option value="percentage" <?= old('buyer_commission_type', $rule['buyer_commission_type']) === 'percentage' ? 'selected' : '' ?>>Pourcentage (%)</option>
                                <option value="fixed" <?= old('buyer_commission_type', $rule['buyer_commission_type']) === 'fixed' ? 'selected' : '' ?>>Montant Fixe (TND)</option>
                                <option value="months" <?= old('buyer_commission_type', $rule['buyer_commission_type']) === 'months' ? 'selected' : '' ?>>Mois de Loyer</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="buyer_commission_value" class="form-label">
                                <span id="buyer_value_label">Valeur</span> <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="buyer_commission_value" 
                                   name="buyer_commission_value" step="0.01" 
                                   value="<?= old('buyer_commission_value', $rule['buyer_commission_value']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="buyer_commission_vat" class="form-label">TVA (%)</label>
                            <input type="number" class="form-control" id="buyer_commission_vat" 
                                   name="buyer_commission_vat" step="0.01" 
                                   value="<?= old('buyer_commission_vat', $rule['buyer_commission_vat']) ?>">
                            <small class="text-muted">Par défaut : 19%</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie"></i> Commission Vendeur/Propriétaire</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="seller_commission_type" class="form-label">Type de Commission <span class="text-danger">*</span></label>
                            <select class="form-select" id="seller_commission_type" name="seller_commission_type" required onchange="updateSellerLabel()">
                                <option value="percentage" <?= old('seller_commission_type', $rule['seller_commission_type']) === 'percentage' ? 'selected' : '' ?>>Pourcentage (%)</option>
                                <option value="fixed" <?= old('seller_commission_type', $rule['seller_commission_type']) === 'fixed' ? 'selected' : '' ?>>Montant Fixe (TND)</option>
                                <option value="months" <?= old('seller_commission_type', $rule['seller_commission_type']) === 'months' ? 'selected' : '' ?>>Mois de Loyer</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="seller_commission_value" class="form-label">
                                <span id="seller_value_label">Valeur</span> <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="seller_commission_value" 
                                   name="seller_commission_value" step="0.01" 
                                   value="<?= old('seller_commission_value', $rule['seller_commission_value']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="seller_commission_vat" class="form-label">TVA (%)</label>
                            <input type="number" class="form-control" id="seller_commission_vat" 
                                   name="seller_commission_vat" step="0.01" 
                                   value="<?= old('seller_commission_vat', $rule['seller_commission_vat']) ?>">
                            <small class="text-muted">Par défaut : 19%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   <?= old('is_active', $rule['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                Règle Active
                            </label>
                        </div>
                        <small class="text-muted">Si désactivée, cette règle ne sera pas utilisée pour les calculs</small>
                    </div>

                    <hr>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Informations :</strong><br>
                        Créée le : <?= date('d/m/Y H:i', strtotime($rule['created_at'])) ?><br>
                        <?php if ($rule['updated_at']): ?>
                            Modifiée le : <?= date('d/m/Y H:i', strtotime($rule['updated_at'])) ?>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les Modifications
                        </button>
                        <a href="<?= base_url('admin/commission-settings/rules') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Aperçu Commission Totale</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-light text-center">
                        <h3 id="totalCommission" class="mb-0 text-primary">-</h3>
                        <small class="text-muted">Commission totale (Acheteur + Vendeur)</small>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <strong id="buyerPreview">-</strong><br>
                                <small class="text-muted">Acheteur</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <strong id="sellerPreview">-</strong><br>
                                <small class="text-muted">Vendeur</small>
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
function updateBuyerLabel() {
    const type = document.getElementById('buyer_commission_type').value;
    const label = document.getElementById('buyer_value_label');
    
    if (type === 'percentage') {
        label.textContent = 'Pourcentage (%)';
    } else if (type === 'fixed') {
        label.textContent = 'Montant (TND)';
    } else if (type === 'months') {
        label.textContent = 'Nombre de Mois';
    }
    
    updatePreview();
}

function updateSellerLabel() {
    const type = document.getElementById('seller_commission_type').value;
    const label = document.getElementById('seller_value_label');
    
    if (type === 'percentage') {
        label.textContent = 'Pourcentage (%)';
    } else if (type === 'fixed') {
        label.textContent = 'Montant (TND)';
    } else if (type === 'months') {
        label.textContent = 'Nombre de Mois';
    }
    
    updatePreview();
}

function updatePreview() {
    const buyerType = document.getElementById('buyer_commission_type').value;
    const buyerValue = parseFloat(document.getElementById('buyer_commission_value').value) || 0;
    const sellerType = document.getElementById('seller_commission_type').value;
    const sellerValue = parseFloat(document.getElementById('seller_commission_value').value) || 0;
    
    let buyerText = '';
    let sellerText = '';
    let totalText = '';
    
    if (buyerType === 'percentage') {
        buyerText = buyerValue + '%';
    } else if (buyerType === 'fixed') {
        buyerText = buyerValue.toFixed(2) + ' TND';
    } else {
        buyerText = buyerValue + ' mois';
    }
    
    if (sellerType === 'percentage') {
        sellerText = sellerValue + '%';
    } else if (sellerType === 'fixed') {
        sellerText = sellerValue.toFixed(2) + ' TND';
    } else {
        sellerText = sellerValue + ' mois';
    }
    
    document.getElementById('buyerPreview').textContent = buyerText;
    document.getElementById('sellerPreview').textContent = sellerText;
    
    // Total (only if same type)
    if (buyerType === sellerType) {
        const total = buyerValue + sellerValue;
        if (buyerType === 'percentage') {
            totalText = total + '%';
        } else if (buyerType === 'fixed') {
            totalText = total.toFixed(2) + ' TND';
        } else {
            totalText = total + ' mois';
        }
        document.getElementById('totalCommission').textContent = totalText;
    } else {
        document.getElementById('totalCommission').textContent = 'Types différents';
    }
}

// Initialize labels and preview on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBuyerLabel();
    updateSellerLabel();
    updatePreview();
    
    // Add event listeners for real-time preview
    document.getElementById('buyer_commission_value').addEventListener('input', updatePreview);
    document.getElementById('seller_commission_value').addEventListener('input', updatePreview);
});
</script>
<?= $this->endSection() ?>
