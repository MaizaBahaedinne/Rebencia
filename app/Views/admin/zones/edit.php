<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marked-alt me-2"></i>Modifier Zone
        </h1>
        <a href="<?= base_url('admin/zones') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <h6><i class="fas fa-exclamation-circle me-2"></i>Erreurs de validation:</h6>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <form action="<?= base_url('admin/zones/update/' . $zone['id']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informations de la Zone</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= old('name', $zone['name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="governorate" <?= old('type', $zone['type']) === 'governorate' ? 'selected' : '' ?>>Gouvernorat</option>
                                    <option value="city" <?= old('type', $zone['type']) === 'city' ? 'selected' : '' ?>>Ville</option>
                                    <option value="district" <?= old('type', $zone['type']) === 'district' ? 'selected' : '' ?>>Délégation</option>
                                    <option value="area" <?= old('type', $zone['type']) === 'area' ? 'selected' : '' ?>>Quartier</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="name_ar" class="form-label">Nom Arabe</label>
                                <input type="text" class="form-control" id="name_ar" name="name_ar" 
                                       value="<?= old('name_ar', $zone['name_ar'] ?? '') ?>" dir="rtl">
                            </div>

                            <div class="col-md-6">
                                <label for="name_en" class="form-label">Nom Anglais</label>
                                <input type="text" class="form-control" id="name_en" name="name_en" 
                                       value="<?= old('name_en', $zone['name_en'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="parent_id" class="form-label">Zone Parente</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">-- Aucune (Niveau supérieur) --</option>
                                    <?php foreach ($parentZones as $parent): ?>
                                        <option value="<?= $parent['id'] ?>" <?= old('parent_id', $zone['parent_id']) == $parent['id'] ? 'selected' : '' ?>>
                                            <?= esc($parent['name']) ?> (<?= $parent['type'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <small class="text-muted">Ex: Pour une ville, sélectionnez le gouvernorat</small>
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label">Pays</label>
                                <input type="text" class="form-control" id="country" name="country" 
                                       value="<?= old('country', $zone['country'] ?? 'Tunisia') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Localisation GPS</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" 
                                       value="<?= old('latitude', $zone['latitude'] ?? '') ?>" placeholder="36.8065">
                            </div>

                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" 
                                       value="<?= old('longitude', $zone['longitude'] ?? '') ?>" placeholder="10.1815">
                            </div>

                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Utilisez Google Maps pour obtenir les coordonnées GPS
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Options</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="popularity_score" class="form-label">Score de Popularité</label>
                            <input type="number" class="form-control" id="popularity_score" name="popularity_score" 
                                   value="<?= old('popularity_score', $zone['popularity_score'] ?? 0) ?>" 
                                   min="0" max="100">
                            <small class="text-muted">De 0 à 100 (utilisé pour le tri)</small>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                        </button>
                        <a href="<?= base_url('admin/zones') ?>" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Supprimer la Zone
                        </button>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informations</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Date de Création</label>
                            <p class="mb-0"><i class="fas fa-calendar me-1"></i><?= date('d/m/Y H:i', strtotime($zone['created_at'])) ?></p>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <label class="text-muted small">Dernière Modification</label>
                            <p class="mb-0"><i class="fas fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($zone['updated_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette zone ?')) {
        window.location.href = '<?= base_url('admin/zones/delete/' . $zone['id']) ?>';
    }
}
</script>

<?= $this->endSection() ?>
