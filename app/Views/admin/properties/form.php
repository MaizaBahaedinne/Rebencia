<!-- FORMULAIRE BIEN IMMOBILIER -->
<?php $isEdit = ! empty($property['id']); ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/properties') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold"><?= $isEdit ? 'Modifier le bien' : 'Nouveau bien immobilier' ?></h4>
        <?php if ($isEdit) : ?>
        <p class="text-muted mb-0"><?= esc($property['reference']) ?></p>
        <?php endif; ?>
    </div>
</div>

<form method="POST"
      action="<?= $isEdit ? base_url('admin/properties/' . $property['id'] . '/update') : base_url('admin/properties/store') ?>"
      enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Colonne principale -->
        <div class="col-12 col-lg-8">

            <!-- Informations générales -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-info-circle text-primary me-2"></i>Informations générales
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Titre de l'annonce <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               value="<?= esc(old('title', $property['title'] ?? '')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= esc(old('description', $property['description'] ?? '')) ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type de bien <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <?php foreach (['apartment'=>'Appartement','house'=>'Maison','villa'=>'Villa','commercial'=>'Commercial','land'=>'Terrain','office'=>'Bureau'] as $v => $l) : ?>
                                <option value="<?= $v ?>" <?= old('type', $property['type'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Transaction <span class="text-danger">*</span></label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="sale" <?= old('transaction_type', $property['transaction_type'] ?? 'sale') === 'sale' ? 'selected' : '' ?>>Vente</option>
                                <option value="rent" <?= old('transaction_type', $property['transaction_type'] ?? '') === 'rent' ? 'selected' : '' ?>>Location</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="status" class="form-select">
                                <?php foreach (['available'=>'Disponible','reserved'=>'Réservé','sold'=>'Vendu','rented'=>'Loué','inactive'=>'Inactif'] as $v => $l) : ?>
                                <option value="<?= $v ?>" <?= old('status', $property['status'] ?? 'available') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Caractéristiques -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-rulers text-success me-2"></i>Caractéristiques
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prix (TND) <span class="text-danger">*</span></label>
                            <input type="number" name="price" step="100" class="form-control"
                                   value="<?= old('price', $property['price'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Surface (m²)</label>
                            <input type="number" name="surface" step="0.5" class="form-control"
                                   value="<?= old('surface', $property['surface'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pièces</label>
                            <input type="number" name="rooms" min="0" class="form-control"
                                   value="<?= old('rooms', $property['rooms'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Chambres</label>
                            <input type="number" name="bedrooms" min="0" class="form-control"
                                   value="<?= old('bedrooms', $property['bedrooms'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">SDB</label>
                            <input type="number" name="bathrooms" min="0" class="form-control"
                                   value="<?= old('bathrooms', $property['bathrooms'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Étage</label>
                            <input type="number" name="floor" class="form-control"
                                   value="<?= old('floor', $property['floor'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Total étages</label>
                            <input type="number" name="total_floors" min="0" class="form-control"
                                   value="<?= old('total_floors', $property['total_floors'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input type="checkbox" name="parking" id="parking" class="form-check-input"
                                       value="1" <?= old('parking', $property['parking'] ?? 0) ? 'checked' : '' ?>>
                                <label for="parking" class="form-check-label">Parking</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input type="checkbox" name="furnished" id="furnished" class="form-check-input"
                                       value="1" <?= old('furnished', $property['furnished'] ?? 0) ? 'checked' : '' ?>>
                                <label for="furnished" class="form-check-label">Meublé</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-geo-alt text-danger me-2"></i>Localisation
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" name="address" class="form-control"
                                   value="<?= esc(old('address', $property['address'] ?? '')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ville</label>
                            <input type="text" name="city" class="form-control"
                                   value="<?= esc(old('city', $property['city'] ?? '')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Zone</label>
                            <input type="text" name="zone" class="form-control"
                                   value="<?= esc(old('zone', $property['zone'] ?? '')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Latitude</label>
                            <input type="text" name="latitude" class="form-control"
                                   value="<?= esc(old('latitude', $property['latitude'] ?? '')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Longitude</label>
                            <input type="text" name="longitude" class="form-control"
                                   value="<?= esc(old('longitude', $property['longitude'] ?? '')) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-images text-warning me-2"></i>Photos
                </div>
                <div class="card-body">
                    <?php if (! empty($property['images'])) : ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php foreach ($property['images'] as $img) : ?>
                        <div class="position-relative">
                            <img src="<?= base_url($img['path']) ?>" class="rounded"
                                 style="width:80px;height:70px;object-fit:cover;">
                            <?php if ($img['is_primary']) : ?>
                            <span class="position-absolute top-0 start-0 badge bg-primary" style="font-size:.6rem;">Principale</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPEG, PNG, WebP. Première image = image principale.</div>
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-person-badge text-primary me-2"></i>Agent responsable
                </div>
                <div class="card-body">
                    <select name="agent_id" class="form-select" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($agents as $agent) : ?>
                        <option value="<?= $agent['id'] ?>"
                            <?= old('agent_id', $property['agent_id'] ?? '') == $agent['id'] ? 'selected' : '' ?>>
                            <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-lg me-1"></i>
                        <?= $isEdit ? 'Enregistrer' : 'Créer le bien' ?>
                    </button>
                    <a href="<?= base_url('admin/properties') ?>" class="btn btn-outline-secondary w-100">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
