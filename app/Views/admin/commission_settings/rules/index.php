<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-cogs"></i> Règles de Commission
    </h1>
    <?php if (canCreate('commissions')): ?>
    <a href="<?= base_url('admin/commission-settings/create-rule') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle Règle
    </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>À propos des règles de commission :</strong> 
            Les règles définissent les taux de commission par défaut pour chaque type de transaction et de bien. 
            Elles peuvent être surchargées au niveau de l'agence, du rôle ou de l'utilisateur.
        </div>
    </div>
</div>

<?php if (!empty($rules)): ?>
    <?php foreach ($rules as $transactionType => $propertyTypes): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-<?= $transactionType === 'sale' ? 'hand-holding-usd' : 'home' ?>"></i>
                    <?= $transactionType === 'sale' ? 'Ventes' : 'Locations' ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 250px;">Type de Bien</th>
                                <th>Commission Acheteur/Locataire</th>
                                <th>Commission Vendeur/Propriétaire</th>
                                <th class="text-center">Commission Totale</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($propertyTypes as $rule): ?>
                                <tr>
                                    <td>
                                        <strong><?= ucfirst(str_replace('_', ' ', $rule['property_type'])) ?></strong>
                                        <?php if ($rule['is_default']): ?>
                                            <span class="badge bg-success ms-2">Par défaut</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($rule['buyer_commission_type'] === 'percentage'): ?>
                                            <span class="badge bg-info"><?= $rule['buyer_commission_value'] ?>%</span>
                                        <?php elseif ($rule['buyer_commission_type'] === 'months'): ?>
                                            <span class="badge bg-warning"><?= $rule['buyer_commission_value'] ?> mois</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= number_format($rule['buyer_commission_value'], 0, ',', ' ') ?> TND</span>
                                        <?php endif; ?>
                                        <small class="text-muted">(TVA <?= $rule['buyer_commission_vat'] ?>%)</small>
                                    </td>
                                    <td>
                                        <?php if ($rule['seller_commission_type'] === 'percentage'): ?>
                                            <span class="badge bg-info"><?= $rule['seller_commission_value'] ?>%</span>
                                        <?php elseif ($rule['seller_commission_type'] === 'months'): ?>
                                            <span class="badge bg-warning"><?= $rule['seller_commission_value'] ?> mois</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= number_format($rule['seller_commission_value'], 0, ',', ' ') ?> TND</span>
                                        <?php endif; ?>
                                        <small class="text-muted">(TVA <?= $rule['seller_commission_vat'] ?>%)</small>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $total = $rule['buyer_commission_value'] + $rule['seller_commission_value'];
                                        $unit = $rule['buyer_commission_type'] === 'percentage' ? '%' : ($rule['buyer_commission_type'] === 'months' ? ' mois' : ' TND');
                                        ?>
                                        <strong class="text-primary"><?= $total ?><?= $unit ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($rule['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <?php if (canUpdate('commissions')): ?>
                                                <a href="<?= base_url('admin/commission-settings/edit-rule/' . $rule['id']) ?>" 
                                                   class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (canDelete('commissions') && !$rule['is_default']): ?>
                                                <button onclick="confirmDelete(<?= $rule['id'] ?>)" 
                                                        class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if (canUpdate('commissions') && !$rule['is_default']): ?>
                                                <form method="POST" action="<?= base_url('admin/commission-settings/set-default-rule/' . $rule['id']) ?>" 
                                                      style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-success" title="Définir par défaut">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucune règle de commission trouvée.</p>
            <?php if (canCreate('commissions')): ?>
                <a href="<?= base_url('admin/commission-settings/create-rule') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer la première règle
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(ruleId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette règle de commission ?')) {
        fetch(`<?= base_url('admin/commission-settings/delete-rule/') ?>${ruleId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    }
}
</script>
<?= $this->endSection() ?>
