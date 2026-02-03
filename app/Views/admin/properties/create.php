<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Bien - REBENCIA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar (même que index.php) -->
        <?= view('admin/partials/sidebar') ?>

        <!-- Main Content -->
        <div class="flex-grow-1" style="margin-left: 250px;">
            <!-- Navbar -->
            <?= view('admin/partials/navbar') ?>

            <!-- Page Content -->
            <div class="container-fluid p-4">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/properties') ?>">Biens</a></li>
                        <li class="breadcrumb-item active">Créer un bien</li>
                    </ol>
                </nav>

                <!-- Page Title -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-plus-circle text-primary"></i> Créer un Nouveau Bien</h2>
                    <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
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
                <form action="<?= base_url('admin/properties/store') ?>" method="post" enctype="multipart/form-data">
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
                                                   value="<?= old('title') ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="reference" class="form-label">Référence</label>
                                            <input type="text" class="form-control" id="reference" name="reference" 
                                                   value="<?= old('reference') ?>" placeholder="Auto-généré">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" name="description" rows="5" required><?= old('description') ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                            <select class="form-select" id="type" name="type" required>
                                                <option value="">-- Sélectionner --</option>
                                                <option value="apartment" <?= old('type') == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                                <option value="villa" <?= old('type') == 'villa' ? 'selected' : '' ?>>Villa</option>
                                                <option value="house" <?= old('type') == 'house' ? 'selected' : '' ?>>Maison</option>
                                                <option value="land" <?= old('type') == 'land' ? 'selected' : '' ?>>Terrain</option>
                                                <option value="commercial" <?= old('type') == 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                                <option value="office" <?= old('type') == 'office' ? 'selected' : '' ?>>Bureau</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="transaction_type" class="form-label">Transaction <span class="text-danger">*</span></label>
                                            <select class="form-select" id="transaction_type" name="transaction_type" required>
                                                <option value="">-- Sélectionner --</option>
                                                <option value="sale" <?= old('transaction_type') == 'sale' ? 'selected' : '' ?>>Vente</option>
                                                <option value="rent" <?= old('transaction_type') == 'rent' ? 'selected' : '' ?>>Location</option>
                                                <option value="both" <?= old('transaction_type') == 'both' ? 'selected' : '' ?>>Vente ou Location</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="available" selected>Disponible</option>
                                                <option value="reserved">Réservé</option>
                                                <option value="sold">Vendu</option>
                                                <option value="rented">Loué</option>
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
                                                   value="<?= old('price') ?>" min="0" step="0.01">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rent_price" class="form-label">Prix de Location (TND/mois)</label>
                                            <input type="number" class="form-control" id="rent_price" name="rent_price" 
                                                   value="<?= old('rent_price') ?>" min="0" step="0.01">
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
                                                   value="<?= old('bedrooms', 0) ?>" min="0">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="bathrooms" class="form-label">Salles de bain</label>
                                            <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                                   value="<?= old('bathrooms', 0) ?>" min="0">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="area" class="form-label">Surface (m²) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="area" name="area" 
                                                   value="<?= old('area') ?>" min="0" step="0.01" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="floor" class="form-label">Étage</label>
                                            <input type="number" class="form-control" id="floor" name="floor" 
                                                   value="<?= old('floor') ?>" min="0">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="year_built" class="form-label">Année de construction</label>
                                            <input type="number" class="form-control" id="year_built" name="year_built" 
                                                   value="<?= old('year_built') ?>" min="1900" max="<?= date('Y') ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="parking" class="form-label">Parking</label>
                                            <input type="number" class="form-control" id="parking" name="parking" 
                                                   value="<?= old('parking', 0) ?>" min="0">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="furnished" class="form-label">Meublé</label>
                                            <select class="form-select" id="furnished" name="furnished">
                                                <option value="0">Non meublé</option>
                                                <option value="1">Meublé</option>
                                            </select>
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
                                                    <option value="<?= $zone['id'] ?>" <?= old('zone_id') == $zone['id'] ? 'selected' : '' ?>>
                                                        <?= esc($zone['name']) ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="address" name="address" 
                                                   value="<?= old('address') ?>" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="latitude" class="form-label">Latitude</label>
                                            <input type="number" class="form-control" id="latitude" name="latitude" 
                                                   value="<?= old('latitude') ?>" step="0.000001">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="longitude" class="form-label">Longitude</label>
                                            <input type="number" class="form-control" id="longitude" name="longitude" 
                                                   value="<?= old('longitude') ?>" step="0.000001">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne Droite -->
                        <div class="col-lg-4">
                            <!-- Images -->
                            <div class="card mb-4">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="fas fa-images"></i> Images</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="images" class="form-label">Photos du bien</label>
                                        <input type="file" class="form-control" id="images" name="images[]" 
                                               accept="image/*" multiple>
                                        <small class="text-muted">Formats acceptés: JPG, PNG, WebP (Max 5MB par image)</small>
                                    </div>
                                    <div id="image-preview" class="row g-2"></div>
                                </div>
                            </div>

                            <!-- Agence & Agent -->
                            <div class="card mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-building"></i> Agence & Agent</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="agency_id" class="form-label">Agence</label>
                                        <select class="form-select" id="agency_id" name="agency_id">
                                            <option value="">-- Sélectionner --</option>
                                            <?php foreach ($agencies as $agency): ?>
                                                <option value="<?= $agency['id'] ?>" <?= old('agency_id') == $agency['id'] ? 'selected' : '' ?>>
                                                    <?= esc($agency['name']) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="agent_id" class="form-label">Agent responsable</label>
                                        <select class="form-select" id="agent_id" name="agent_id">
                                            <option value="">-- Sélectionner --</option>
                                            <?php foreach ($agents as $agent): ?>
                                                <option value="<?= $agent['id'] ?>" <?= old('agent_id') == $agent['id'] ? 'selected' : '' ?>>
                                                    <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Visibilité -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-eye"></i> Visibilité</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= old('is_featured') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_featured">
                                            <i class="fas fa-star text-warning"></i> Mettre en avant
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" checked>
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
                                <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <div>
                                    <button type="submit" name="action" value="draft" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-save"></i> Enregistrer comme brouillon
                                    </button>
                                    <button type="submit" name="action" value="publish" class="btn btn-primary">
                                        <i class="fas fa-check"></i> Créer le bien
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview des images sélectionnées
        document.getElementById('images').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
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
                                ${index === 0 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Principal</span>' : ''}
                            </div>
                        `;
                        preview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>
