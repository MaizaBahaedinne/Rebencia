<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building me-2"></i>Nouvelle Agence
        </h1>
        <a href="<?= base_url('admin/agencies') ?>" class="btn btn-secondary">
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

    <form action="<?= base_url('admin/agencies/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informations Générales</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom de l'Agence <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= old('name') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="siege" <?= old('type') === 'siege' ? 'selected' : '' ?>>Siège</option>
                                    <option value="agence" <?= old('type') === 'agence' ? 'selected' : '' ?>>Agence</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label for="parent_id" class="form-label">Agence Parente</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">-- Aucune (Indépendante) --</option>
                                    <?php foreach ($agencies as $agency): ?>
                                        <option value="<?= $agency['id'] ?>" <?= old('parent_id') == $agency['id'] ? 'selected' : '' ?>>
                                            <?= esc($agency['name']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <small class="text-muted">Sélectionnez le siège si cette agence en dépend</small>
                            </div>

                            <div class="col-md-12">
                                <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?= old('address') ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label">Ville <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= old('city') ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="governorate" class="form-label">Gouvernorat <span class="text-danger">*</span></label>
                                <select class="form-select" id="governorate" name="governorate" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Tunis" <?= old('governorate') === 'Tunis' ? 'selected' : '' ?>>Tunis</option>
                                    <option value="Ariana" <?= old('governorate') === 'Ariana' ? 'selected' : '' ?>>Ariana</option>
                                    <option value="Ben Arous" <?= old('governorate') === 'Ben Arous' ? 'selected' : '' ?>>Ben Arous</option>
                                    <option value="Manouba" <?= old('governorate') === 'Manouba' ? 'selected' : '' ?>>Manouba</option>
                                    <option value="Nabeul" <?= old('governorate') === 'Nabeul' ? 'selected' : '' ?>>Nabeul</option>
                                    <option value="Zaghouan" <?= old('governorate') === 'Zaghouan' ? 'selected' : '' ?>>Zaghouan</option>
                                    <option value="Bizerte" <?= old('governorate') === 'Bizerte' ? 'selected' : '' ?>>Bizerte</option>
                                    <option value="Béja" <?= old('governorate') === 'Béja' ? 'selected' : '' ?>>Béja</option>
                                    <option value="Jendouba" <?= old('governorate') === 'Jendouba' ? 'selected' : '' ?>>Jendouba</option>
                                    <option value="Kef" <?= old('governorate') === 'Kef' ? 'selected' : '' ?>>Kef</option>
                                    <option value="Siliana" <?= old('governorate') === 'Siliana' ? 'selected' : '' ?>>Siliana</option>
                                    <option value="Sousse" <?= old('governorate') === 'Sousse' ? 'selected' : '' ?>>Sousse</option>
                                    <option value="Monastir" <?= old('governorate') === 'Monastir' ? 'selected' : '' ?>>Monastir</option>
                                    <option value="Mahdia" <?= old('governorate') === 'Mahdia' ? 'selected' : '' ?>>Mahdia</option>
                                    <option value="Sfax" <?= old('governorate') === 'Sfax' ? 'selected' : '' ?>>Sfax</option>
                                    <option value="Kairouan" <?= old('governorate') === 'Kairouan' ? 'selected' : '' ?>>Kairouan</option>
                                    <option value="Kasserine" <?= old('governorate') === 'Kasserine' ? 'selected' : '' ?>>Kasserine</option>
                                    <option value="Sidi Bouzid" <?= old('governorate') === 'Sidi Bouzid' ? 'selected' : '' ?>>Sidi Bouzid</option>
                                    <option value="Gabès" <?= old('governorate') === 'Gabès' ? 'selected' : '' ?>>Gabès</option>
                                    <option value="Médenine" <?= old('governorate') === 'Médenine' ? 'selected' : '' ?>>Médenine</option>
                                    <option value="Tataouine" <?= old('governorate') === 'Tataouine' ? 'selected' : '' ?>>Tataouine</option>
                                    <option value="Gafsa" <?= old('governorate') === 'Gafsa' ? 'selected' : '' ?>>Gafsa</option>
                                    <option value="Tozeur" <?= old('governorate') === 'Tozeur' ? 'selected' : '' ?>>Tozeur</option>
                                    <option value="Kébili" <?= old('governorate') === 'Kébili' ? 'selected' : '' ?>>Kébili</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Code Postal</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                       value="<?= old('postal_code') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Contact</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?= old('phone') ?>" placeholder="+216 XX XXX XXX">
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email') ?>" placeholder="contact@agence.com">
                            </div>

                            <div class="col-md-12">
                                <label for="website" class="form-label">Site Web</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?= old('website') ?>" placeholder="https://www.agence.com">
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
                                       value="<?= old('latitude') ?>" placeholder="36.8065">
                            </div>

                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" 
                                       value="<?= old('longitude') ?>" placeholder="10.1815">
                            </div>

                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Utilisez Google Maps pour obtenir les coordonnées GPS exactes
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Logo</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img id="logo_preview" src="<?= base_url('assets/images/no-image.png') ?>" 
                                 alt="Logo Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Créer l'Agence
                        </button>
                        <a href="<?= base_url('admin/agencies') ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Preview logo
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logo_preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?= $this->endSection() ?>
