<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nouvelle Transaction</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/transactions') ?>">Transactions</a></li>
                    <li class="breadcrumb-item active">Nouvelle</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('admin/transactions') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreurs de validation:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <form action="<?= base_url('admin/transactions/store') ?>" method="post" id="transactionForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Section 1: Informations Transaction -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Informations Transaction</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="property_id" class="form-label">Bien <span class="text-danger">*</span></label>
                                <select class="form-select" id="property_id" name="property_id" required onchange="updatePropertyDetails()">
                                    <option value="">-- Sélectionner un bien --</option>
                                    <?php foreach ($properties as $property): ?>
                                        <option value="<?= $property['id'] ?>" 
                                                data-price="<?= $property['price'] ?>"
                                                data-rent="<?= $property['rental_price'] ?? 0 ?>"
                                                data-type="<?= $property['transaction_type'] ?>"
                                                <?= old('property_id') == $property['id'] ? 'selected' : '' ?>>
                                            <?= esc($property['reference']) ?> - <?= esc($property['title']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label">Type de Transaction <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required onchange="updatePriceField()">
                                    <option value="sale" <?= old('type') == 'sale' ? 'selected' : '' ?>>Vente</option>
                                    <option value="rent" <?= old('type') == 'rent' ? 'selected' : '' ?>>Location</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="buyer_id" class="form-label">Acheteur/Locataire <span class="text-danger">*</span></label>
                                <select class="form-select" id="buyer_id" name="buyer_id" required>
                                    <option value="">-- Sélectionner un client --</option>
                                    <?php foreach ($buyers as $buyer): ?>
                                        <option value="<?= $buyer['id'] ?>" <?= old('buyer_id') == $buyer['id'] ? 'selected' : '' ?>>
                                            <?= esc($buyer['first_name'] . ' ' . $buyer['last_name']) ?> - <?= esc($buyer['phone']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="seller_id" class="form-label">Vendeur/Bailleur</label>
                                <select class="form-select" id="seller_id" name="seller_id">
                                    <option value="">-- Sélectionner un client --</option>
                                    <?php foreach ($sellers as $seller): ?>
                                        <option value="<?= $seller['id'] ?>" <?= old('seller_id') == $seller['id'] ? 'selected' : '' ?>>
                                            <?= esc($seller['first_name'] . ' ' . $seller['last_name']) ?> - <?= esc($seller['phone']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="transaction_date" class="form-label">Date de Transaction <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="transaction_date" name="transaction_date" 
                                       value="<?= old('transaction_date', date('Y-m-d')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Montant (TND) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       value="<?= old('amount') ?>" step="0.01" required onchange="calculateCommission()">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Commission -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-percentage me-2"></i>Calcul de Commission</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="commission_percentage" class="form-label">Taux de Commission (%)</label>
                                <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" 
                                       value="<?= old('commission_percentage', '3') ?>" step="0.1" onchange="calculateCommission()">
                            </div>
                            <div class="col-md-4">
                                <label for="commission_amount" class="form-label">Montant Commission (TND)</label>
                                <input type="number" class="form-control" id="commission_amount" name="commission_amount" 
                                       value="<?= old('commission_amount') ?>" step="0.01" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="commission_paid" class="form-label">Statut Paiement</label>
                                <select class="form-select" id="commission_paid" name="commission_paid">
                                    <option value="0" <?= old('commission_paid', '0') == '0' ? 'selected' : '' ?>>Non Payée</option>
                                    <option value="1" <?= old('commission_paid') == '1' ? 'selected' : '' ?>>Payée</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Répartition de commission:</strong> Le montant sera automatiquement réparti entre l'agent, le superviseur et l'agence selon les règles définies.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Documents et Notes -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Documents et Notes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contract_number" class="form-label">Numéro de Contrat</label>
                                <input type="text" class="form-control" id="contract_number" name="contract_number" 
                                       value="<?= old('contract_number') ?>" placeholder="CONT-XXXXXX">
                            </div>
                            <div class="col-md-6">
                                <label for="notary" class="form-label">Notaire</label>
                                <input type="text" class="form-control" id="notary" name="notary" 
                                       value="<?= old('notary') ?>" placeholder="Nom du notaire">
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes et Commentaires</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Statut et Attribution -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" <?= old('status', 'pending') == 'pending' ? 'selected' : '' ?>>En Attente</option>
                                <option value="completed" <?= old('status') == 'completed' ? 'selected' : '' ?>>Complétée</option>
                                <option value="cancelled" <?= old('status') == 'cancelled' ? 'selected' : '' ?>>Annulée</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="agent_id" class="form-label">Agent Responsable <span class="text-danger">*</span></label>
                            <select class="form-select" id="agent_id" name="agent_id" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?= $agent['id'] ?>" <?= old('agent_id', session()->get('user_id')) == $agent['id'] ? 'selected' : '' ?>>
                                        <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="agency_id" class="form-label">Agence</label>
                            <select class="form-select" id="agency_id" name="agency_id">
                                <option value="">-- Agence par défaut --</option>
                                <?php foreach ($agencies as $agency): ?>
                                    <option value="<?= $agency['id'] ?>" <?= old('agency_id') == $agency['id'] ? 'selected' : '' ?>>
                                        <?= esc($agency['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer la Transaction
                            </button>
                            <a href="<?= base_url('admin/transactions') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif -->
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Récapitulatif</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Montant Transaction:</th>
                                <td id="summary_amount" class="text-end">0 TND</td>
                            </tr>
                            <tr>
                                <th>Commission (%):</th>
                                <td id="summary_percentage" class="text-end">3%</td>
                            </tr>
                            <tr class="table-success">
                                <th>Total Commission:</th>
                                <td id="summary_commission" class="text-end fw-bold">0 TND</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function updatePropertyDetails() {
    const select = document.getElementById('property_id');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        const type = option.dataset.type;
        const price = parseFloat(option.dataset.price || 0);
        const rent = parseFloat(option.dataset.rent || 0);
        
        // Auto-sélectionner le type de transaction
        document.getElementById('type').value = type === 'both' ? 'sale' : type;
        
        // Pré-remplir le montant
        if (type === 'rent') {
            document.getElementById('amount').value = rent;
        } else {
            document.getElementById('amount').value = price;
        }
        
        calculateCommission();
    }
}

function updatePriceField() {
    const select = document.getElementById('property_id');
    const option = select.options[select.selectedIndex];
    const type = document.getElementById('type').value;
    
    if (option.value) {
        const price = parseFloat(option.dataset.price || 0);
        const rent = parseFloat(option.dataset.rent || 0);
        
        if (type === 'rent') {
            document.getElementById('amount').value = rent;
        } else {
            document.getElementById('amount').value = price;
        }
        
        calculateCommission();
    }
}

function calculateCommission() {
    const amount = parseFloat(document.getElementById('amount').value || 0);
    const percentage = parseFloat(document.getElementById('commission_percentage').value || 3);
    
    const commission = (amount * percentage) / 100;
    
    document.getElementById('commission_amount').value = commission.toFixed(2);
    
    // Mise à jour du récapitulatif
    document.getElementById('summary_amount').textContent = amount.toLocaleString('fr-TN') + ' TND';
    document.getElementById('summary_percentage').textContent = percentage + '%';
    document.getElementById('summary_commission').textContent = commission.toLocaleString('fr-TN', {minimumFractionDigits: 2}) + ' TND';
}

// Calculer au chargement si des valeurs existent
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('amount').value) {
        calculateCommission();
    }
});
</script>

<?= $this->endSection() ?>
