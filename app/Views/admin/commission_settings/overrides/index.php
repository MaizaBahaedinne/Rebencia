<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-layer-group"></i> Exceptions de Commission
    </h1>
    <?php if (canCreate('commissions')): ?>
    <a href="<?= base_url('admin/commission-settings/create-override') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle Exception
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
            <strong>Hiérarchie des exceptions :</strong> 
            Les exceptions permettent de définir des taux personnalisés qui remplacent les règles par défaut. 
            Ordre de priorité : <strong>Utilisateur</strong> > <strong>Rôle</strong> > <strong>Agence</strong> > <strong>Système</strong>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <select class="form-select" id="filterLevel">
                    <option value="">Tous les niveaux</option>
                    <option value="user">Utilisateur</option>
                    <option value="role">Rôle</option>
                    <option value="agency">Agence</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterType">
                    <option value="">Tous les types</option>
                    <option value="sale">Vente</option>
                    <option value="rent">Location</option>
                </select>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($overrides)): ?>
    <!-- Groupe par niveau -->
    <?php 
    $groupedOverrides = [];
    foreach ($overrides as $override) {
        $groupedOverrides[$override['override_level']][] = $override;
    }
    
    $levelLabels = [
        'user' => ['label' => 'Exceptions Utilisateur', 'icon' => 'fa-user', 'color' => 'danger'],
        'role' => ['label' => 'Exceptions Rôle', 'icon' => 'fa-user-shield', 'color' => 'warning'],
        'agency' => ['label' => 'Exceptions Agence', 'icon' => 'fa-store', 'color' => 'info']
    ];
    ?>
    
    <?php foreach ($levelLabels as $level => $config): ?>
        <?php if (isset($groupedOverrides[$level]) && !empty($groupedOverrides[$level])): ?>
            <div class="card mb-4" data-level="<?= $level ?>">
                <div class="card-header bg-<?= $config['color'] ?> text-white">
                    <h5 class="mb-0">
                        <i class="fas <?= $config['icon'] ?>"></i>
                        <?= $config['label'] ?>
                        <span class="badge bg-white text-<?= $config['color'] ?> ms-2">
                            <?= count($groupedOverrides[$level]) ?>
                        </span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cible</th>
                                    <th>Type Transaction</th>
                                    <th>Type Bien</th>
                                    <th>Commission Acheteur/Locataire</th>
                                    <th>Commission Vendeur/Propriétaire</th>
                                    <th>Total</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($groupedOverrides[$level] as $override): ?>
                                    <tr data-type="<?= $override['transaction_type'] ?>">
                                        <td>
                                            <?php if ($override['override_level'] === 'user'): ?>
                                                <i class="fas fa-user text-danger"></i>
                                                <strong><?= esc($override['user_name'] ?? 'N/A') ?></strong>
                                            <?php elseif ($override['override_level'] === 'role'): ?>
                                                <i class="fas fa-user-shield text-warning"></i>
                                                <strong><?= esc($override['role_name'] ?? 'N/A') ?></strong>
                                            <?php else: ?>
                                                <i class="fas fa-store text-info"></i>
                                                <strong><?= esc($override['agency_name'] ?? 'N/A') ?></strong>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $override['transaction_type'] === 'sale' ? 'primary' : 'success' ?>">
                                                <?= $override['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= ucfirst(str_replace('_', ' ', $override['property_type'])) ?>
                                        </td>
                                        <td>
                                            <?php if ($override['buyer_commission_value']): ?>
                                                <?php if ($override['buyer_commission_type'] === 'percentage'): ?>
                                                    <span class="badge bg-info"><?= $override['buyer_commission_value'] ?>%</span>
                                                <?php elseif ($override['buyer_commission_type'] === 'months'): ?>
                                                    <span class="badge bg-warning"><?= $override['buyer_commission_value'] ?> mois</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= number_format($override['buyer_commission_value'], 0) ?> TND</span>
                                                <?php endif; ?>
                                                <small class="text-muted">(TVA <?= $override['buyer_commission_vat'] ?? 19 ?>%)</small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($override['seller_commission_value']): ?>
                                                <?php if ($override['seller_commission_type'] === 'percentage'): ?>
                                                    <span class="badge bg-info"><?= $override['seller_commission_value'] ?>%</span>
                                                <?php elseif ($override['seller_commission_type'] === 'months'): ?>
                                                    <span class="badge bg-warning"><?= $override['seller_commission_value'] ?> mois</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= number_format($override['seller_commission_value'], 0) ?> TND</span>
                                                <?php endif; ?>
                                                <small class="text-muted">(TVA <?= $override['seller_commission_vat'] ?? 19 ?>%)</small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $total = ($override['buyer_commission_value'] ?? 0) + ($override['seller_commission_value'] ?? 0);
                                            $unit = $override['buyer_commission_type'] === 'percentage' ? '%' : 
                                                    ($override['buyer_commission_type'] === 'months' ? ' mois' : ' TND');
                                            ?>
                                            <strong class="text-primary"><?= $total ?><?= $unit ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <?php if (canUpdate('commissions')): ?>
                                                    <a href="<?= base_url('admin/commission-settings/edit-override/' . $override['id']) ?>" 
                                                       class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (canDelete('commissions')): ?>
                                                    <button onclick="confirmDelete(<?= $override['id'] ?>)" 
                                                            class="btn btn-sm btn-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
        <?php endif; ?>
    <?php endforeach; ?>
    
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucune exception de commission trouvée.</p>
            <p class="text-muted small">Les règles système par défaut seront appliquées à toutes les transactions.</p>
            <?php if (canCreate('commissions')): ?>
                <a href="<?= base_url('admin/commission-settings/create-override') ?>" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> Créer une exception
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Filtrage
document.getElementById('filterLevel')?.addEventListener('change', function() {
    const level = this.value;
    document.querySelectorAll('.card[data-level]').forEach(card => {
        card.style.display = (!level || card.dataset.level === level) ? 'block' : 'none';
    });
});

document.getElementById('filterType')?.addEventListener('change', function() {
    const type = this.value;
    document.querySelectorAll('tbody tr[data-type]').forEach(row => {
        row.style.display = (!type || row.dataset.type === type) ? '' : 'none';
    });
});

function confirmDelete(overrideId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette exception de commission ?')) {
        fetch(`<?= base_url('admin/commission-settings/delete-override/') ?>${overrideId}`, {
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
