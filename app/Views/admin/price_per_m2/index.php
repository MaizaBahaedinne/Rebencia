<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-chart-line text-primary"></i> Prix au m²
            </h1>
            <p class="text-muted">Gérez les prix de l'immobilier par zone et type de bien</p>
        </div>
        <div>
            <a href="<?= base_url('admin/price-per-m2/calculate') ?>" class="btn btn-info me-2">
                <i class="fas fa-calculator"></i> Calculer depuis les biens
            </a>
            <a href="<?= base_url('admin/price-per-m2/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un prix
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Gouvernorat</label>
                    <input type="text" name="governorate" class="form-control" value="<?= esc($filters['governorate']) ?>" placeholder="Ex: Tunis">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Ville</label>
                    <input type="text" name="city" class="form-control" value="<?= esc($filters['city']) ?>" placeholder="Ex: La Marsa">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Type de bien</label>
                    <select name="property_type" class="form-select">
                        <option value="">Tous</option>
                        <option value="apartment" <?= $filters['property_type'] === 'apartment' ? 'selected' : '' ?>>Appartement</option>
                        <option value="villa" <?= $filters['property_type'] === 'villa' ? 'selected' : '' ?>>Villa</option>
                        <option value="studio" <?= $filters['property_type'] === 'studio' ? 'selected' : '' ?>>Studio</option>
                        <option value="office" <?= $filters['property_type'] === 'office' ? 'selected' : '' ?>>Bureau</option>
                        <option value="shop" <?= $filters['property_type'] === 'shop' ? 'selected' : '' ?>>Commerce</option>
                        <option value="land" <?= $filters['property_type'] === 'land' ? 'selected' : '' ?>>Terrain</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Transaction</label>
                    <select name="transaction_type" class="form-select">
                        <option value="">Tous</option>
                        <option value="sale" <?= $filters['transaction_type'] === 'sale' ? 'selected' : '' ?>>Vente</option>
                        <option value="rent" <?= $filters['transaction_type'] === 'rent' ? 'selected' : '' ?>>Location</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="pricesTable">
                    <thead>
                        <tr>
                            <th>Zone / Ville</th>
                            <th>Gouvernorat</th>
                            <th>Type de bien</th>
                            <th>Transaction</th>
                            <th>Prix min</th>
                            <th>Prix moyen</th>
                            <th>Prix max</th>
                            <th>Surface moy.</th>
                            <th>Nb biens</th>
                            <th>Évolution</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prices as $price): ?>
                        <tr>
                            <td>
                                <strong><?= $price['zone_name'] ?: $price['city'] ?: '-' ?></strong>
                            </td>
                            <td><?= esc($price['governorate']) ?: '-' ?></td>
                            <td>
                                <?php
                                $types = [
                                    'apartment' => 'Appartement',
                                    'villa' => 'Villa',
                                    'studio' => 'Studio',
                                    'office' => 'Bureau',
                                    'shop' => 'Commerce',
                                    'warehouse' => 'Entrepôt',
                                    'land' => 'Terrain'
                                ];
                                echo $types[$price['property_type']] ?? $price['property_type'];
                                ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $price['transaction_type'] === 'sale' ? 'primary' : 'success' ?>">
                                    <?= $price['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?>
                                </span>
                            </td>
                            <td><?= $price['price_min'] ? number_format($price['price_min'], 0, ',', ' ') . ' DT' : '-' ?></td>
                            <td><strong><?= number_format($price['price_average'], 0, ',', ' ') ?> DT</strong></td>
                            <td><?= $price['price_max'] ? number_format($price['price_max'], 0, ',', ' ') . ' DT' : '-' ?></td>
                            <td><?= $price['surface_average'] ? round($price['surface_average']) . ' m²' : '-' ?></td>
                            <td><?= $price['properties_count'] ?></td>
                            <td>
                                <?php if ($price['evolution']): ?>
                                    <span class="badge bg-<?= $price['evolution'] > 0 ? 'success' : 'danger' ?>">
                                        <?= $price['evolution'] > 0 ? '+' : '' ?><?= $price['evolution'] ?>%
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= esc($price['period']) ?: '-' ?></td>
                            <td>
                                <span class="badge bg-<?= $price['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $price['is_active'] ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/price-per-m2/edit/' . $price['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= base_url('admin/price-per-m2/delete/' . $price['id']) ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce prix ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#pricesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        order: [[10, 'desc']] // Trier par période
    });
});
</script>
<?= $this->endSection() ?>
