<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/properties') ?>">Biens</a></li>
        <li class="breadcrumb-item active">Modifier le bien #<?= $property['reference'] ?></li>
    </ol>
</nav>

<!-- Page Title -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-edit"></i> Modifier le Bien - <?= esc($property['title']) ?>
    </h1>
    <div>
        <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" class="btn btn-info">
            <i class="fas fa-eye"></i> Voir
        </a>
    </div>
</div>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif ?>

<!-- Formulaire -->
<form action="<?= base_url('admin/properties/update/' . $property['id']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row">
        <!-- Colonne Gauche -->
        <div class="col-lg-8">
            <!-- Informations Générales -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Titre du bien <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= esc($property['title']) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" class="form-control" id="reference" name="reference" 
                                   value="<?= esc($property['reference']) ?>" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= esc($property['description']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="apartment" <?= $property['type'] == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                <option value="villa" <?= $property['type'] == 'villa' ? 'selected' : '' ?>>Villa</option>
                                <option value="house" <?= $property['type'] == 'house' ? 'selected' : '' ?>>Maison</option>
                                <option value="land" <?= $property['type'] == 'land' ? 'selected' : '' ?>>Terrain</option>
                                <option value="commercial" <?= $property['type'] == 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                <option value="office" <?= $property['type'] == 'office' ? 'selected' : '' ?>>Bureau</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="transaction_type" class="form-label">Transaction <span class="text-danger">*</span></label>
                            <select class="form-select" id="transaction_type" name="transaction_type" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="sale" <?= $property['transaction_type'] == 'sale' ? 'selected' : '' ?>>Vente</option>
                                <option value="rent" <?= $property['transaction_type'] == 'rent' ? 'selected' : '' ?>>Location</option>
                                <option value="both" <?= $property['transaction_type'] == 'both' ? 'selected' : '' ?>>Vente ou Location</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" <?= $property['status'] == 'available' ? 'selected' : '' ?>>Disponible</option>
                                <option value="reserved" <?= $property['status'] == 'reserved' ? 'selected' : '' ?>>Réservé</option>
                                <option value="sold" <?= $property['status'] == 'sold' ? 'selected' : '' ?>>Vendu</option>
                                <option value="rented" <?= $property['status'] == 'rented' ? 'selected' : '' ?>>Loué</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prix -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Prix</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Prix de Vente (TND)</label>
                            <input type="number" class="form-control" id="price" name="price" 
                                   value="<?= $property['price'] ?>" min="0" step="0.01">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rent_price" class="form-label">Prix de Location (TND/mois)</label>
                            <input type="number" class="form-control" id="rent_price" name="rent_price" 
                                   value="<?= $property['rent_price'] ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Caractéristiques -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-ruler-combined"></i> Caractéristiques</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="bedrooms" class="form-label">Chambres</label>
                            <input type="number" class="form-control" id="bedrooms" name="bedrooms" 
                                   value="<?= $property['bedrooms'] ?>" min="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="bathrooms" class="form-label">Salles de bain</label>
                            <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                   value="<?= $property['bathrooms'] ?>" min="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="area" class="form-label">Surface (m²) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="area" name="area" 
                                   value="<?= $property['area'] ?>" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="floor" class="form-label">Étage</label>
                            <input type="number" class="form-control" id="floor" name="floor" 
                                   value="<?= $property['floor'] ?>" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Localisation</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="zone_id" class="form-label">Zone <span class="text-danger">*</span></label>
                            <select class="form-select" id="zone_id" name="zone_id" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($zones as $zone): ?>
                                    <option value="<?= $zone['id'] ?>" <?= $property['zone_id'] == $zone['id'] ? 'selected' : '' ?>>
                                        <?= esc($zone['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   value="<?= esc($property['address']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite -->
        <div class="col-lg-4">
            <!-- Images Actuelles -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-images"></i> Images Actuelles</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($property_images)): ?>
                        <div class="row g-2">
                            <?php foreach ($property_images as $image): ?>
                                <div class="col-6">
                                    <div class="position-relative">
                                        <img src="<?= base_url($image['file_path']) ?>" class="img-thumbnail" alt="Image">
                                        <?php if ($image['is_primary']): ?>
                                            <span class="badge bg-primary position-absolute top-0 start-0 m-1">Principal</span>
                                        <?php endif ?>
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                                onclick="deleteImage(<?= $image['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Aucune image</p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Ajouter des Images -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Ajouter des Images</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control" id="new_images" name="new_images[]" 
                               accept="image/*" multiple>
                        <small class="text-muted">Formats acceptés: JPG, PNG, WebP (Max 5MB par image)</small>
                    </div>
                    <div id="new-image-preview" class="row g-2"></div>
                </div>
            </div>

            <!-- Visibilité -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Visibilité</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                               <?= $property['is_featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">
                            <i class="fas fa-star text-warning"></i> Mettre en avant
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" 
                               <?= $property['status'] == 'published' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_published">
                            <i class="fas fa-globe text-success"></i> Publier sur le site
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Supprimer ce bien
                </button>
                <div>
                    <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary me-2">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Preview des nouvelles images
    document.getElementById('new_images').addEventListener('change', function(e) {
        const preview = document.getElementById('new-image-preview');
        preview.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" alt="Preview ${index + 1}">
                            <span class="badge bg-success position-absolute top-0 start-0 m-1">Nouveau</span>
                        </div>
                    `;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Confirmation de suppression
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible.')) {
            window.location.href = '<?= base_url('admin/properties/delete/' . $property['id']) ?>';
        }
    }

    // Supprimer une image
    function deleteImage(imageId) {
        if (confirm('Supprimer cette image ?')) {
            fetch('<?= base_url('admin/properties/delete-image') ?>/' + imageId, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression');
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
