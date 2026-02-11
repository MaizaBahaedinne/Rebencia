<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Ajouter un prix au m²</h1>
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
            <form method="post" action="<?= base_url('admin/price-per-m2/store') ?>">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Zone</label>
                        <select name="zone_id" class="form-select">
                            <option value="">-- Sélectionner une zone --</option>
                            <?php foreach ($zones as $zone): ?>
                                <option value="<?= $zone['id'] ?>"><?= esc($zone['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Ou remplissez ville et gouvernorat ci-dessous</small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ville</label>
                        <input type="text" name="city" class="form-control" value="<?= old('city') ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Gouvernorat</label>
                        <input type="text" name="governorate" class="form-control" value="<?= old('governorate') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de bien <span class="text-danger">*</span></label>
                        <select name="property_type" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="apartment">Appartement</option>
                            <option value="villa">Villa</option>
                            <option value="studio">Studio</option>
                            <option value="office">Bureau</option>
                            <option value="shop">Commerce</option>
                            <option value="warehouse">Entrepôt</option>
                            <option value="land">Terrain</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de transaction <span class="text-danger">*</span></label>
                        <select name="transaction_type" class="form-select" required>
                            <option value="sale">Vente</option>
                            <option value="rent">Location</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prix minimum (DT/m²)</label>
                        <input type="number" step="0.01" name="price_min" class="form-control" value="<?= old('price_min') ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prix moyen (DT/m²) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price_average" class="form-control" value="<?= old('price_average') ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prix maximum (DT/m²)</label>
                        <input type="number" step="0.01" name="price_max" class="form-control" value="<?= old('price_max') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Surface moyenne (m²)</label>
                        <input type="number" step="0.01" name="surface_average" class="form-control" value="<?= old('surface_average') ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nombre de biens</label>
                        <input type="number" name="properties_count" class="form-control" value="<?= old('properties_count') ?: 0 ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Évolution (%)</label>
                        <input type="number" step="0.01" name="evolution" class="form-control" value="<?= old('evolution') ?>" placeholder="Ex: 5.5 ou -2.3">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Période</label>
                        <input type="text" name="period" class="form-control" value="<?= old('period') ?: date('Y-m') ?>" placeholder="Ex: 2026-02 ou 2026-Q1">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
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
