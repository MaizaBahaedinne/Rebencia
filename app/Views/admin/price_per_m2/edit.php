<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier le prix au m²</h1>
        <a href="<?= base_url('admin/price-per-m2') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <?php if (session('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/price-per-m2/update/' . $price['id']) ?>">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Zone</label>
                        <select name="zone_id" class="form-select">
                            <option value="">-- Sélectionner une zone --</option>
                            <?php foreach ($zones as $zone): ?>
                                <option value="<?= $zone['id'] ?>" <?= $zone['id'] == $price['zone_id'] ? 'selected' : '' ?>>
                                    <?= esc($zone['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ville</label>
                        <input type="text" name="city" class="form-control" value="<?= esc($price['city']) ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Gouvernorat</label>
                        <input type="text" name="governorate" class="form-control" value="<?= esc($price['governorate']) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de bien <span class="text-danger">*</span></label>
                        <select name="property_type" class="form-select" required>
                            <option value="apartment" <?= $price['property_type'] === 'apartment' ? 'selected' : '' ?>>Appartement</option>
                            <option value="villa" <?= $price['property_type'] === 'villa' ? 'selected' : '' ?>>Villa</option>
                            <option value="studio" <?= $price['property_type'] === 'studio' ? 'selected' : '' ?>>Studio</option>
                            <option value="office" <?= $price['property_type'] === 'office' ? 'selected' : '' ?>>Bureau</option>
                            <option value="shop" <?= $price['property_type'] === 'shop' ? 'selected' : '' ?>>Commerce</option>
                            <option value="warehouse" <?= $price['property_type'] === 'warehouse' ? 'selected' : '' ?>>Entrepôt</option>
                            <option value="land" <?= $price['property_type'] === 'land' ? 'selected' : '' ?>>Terrain</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de transaction <span class="text-danger">*</span></label>
                        <select name="transaction_type" class="form-select" required>
                            <option value="sale" <?= $price['transaction_type'] === 'sale' ? 'selected' : '' ?>>Vente</option>
                            <option value="rent" <?= $price['transaction_type'] === 'rent' ? 'selected' : '' ?>>Location</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prix minimum (DT/m²)</label>
                        <input type="number" step="0.01" name="price_min" class="form-control" value="<?= $price['price_min'] ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prix moyen (DT/m²) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price_average" class="form-control" value="<?= $price['price_average'] ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prix maximum (DT/m²)</label>
                        <input type="number" step="0.01" name="price_max" class="form-control" value="<?= $price['price_max'] ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Surface moyenne (m²)</label>
                        <input type="number" step="0.01" name="surface_average" class="form-control" value="<?= $price['surface_average'] ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nombre de biens</label>
                        <input type="number" name="properties_count" class="form-control" value="<?= $price['properties_count'] ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Évolution (%)</label>
                        <input type="number" step="0.01" name="evolution" class="form-control" value="<?= $price['evolution'] ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Période</label>
                        <input type="text" name="period" class="form-control" value="<?= esc($price['period']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" <?= $price['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Actif</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('admin/price-per-m2') ?>" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
