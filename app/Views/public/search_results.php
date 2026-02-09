<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Search Results Header -->
<section class="py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div class="container">
        <h1 class="h3 mb-0">Résultats de recherche</h1>
        <p class="mb-0"><?= $total ?> propriété(s) trouvée(s)</p>
    </div>
</section>

<!-- Search Results -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('search') ?>" method="get">
                            <!-- Transaction Type -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Type de transaction</label>
                                <select name="transaction_type" class="form-select form-select-sm">
                                    <option value="">Tous</option>
                                    <option value="sale" <?= $filters['transaction_type'] == 'sale' ? 'selected' : '' ?>>Vente</option>
                                    <option value="rent" <?= $filters['transaction_type'] == 'rent' ? 'selected' : '' ?>>Location</option>
                                </select>
                            </div>
                            
                            <!-- Property Type -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Type de bien</label>
                                <select name="type" class="form-select form-select-sm">
                                    <option value="">Tous</option>
                                    <option value="apartment" <?= $filters['type'] == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                                    <option value="villa" <?= $filters['type'] == 'villa' ? 'selected' : '' ?>>Villa</option>
                                    <option value="house" <?= $filters['type'] == 'house' ? 'selected' : '' ?>>Maison</option>
                                    <option value="land" <?= $filters['type'] == 'land' ? 'selected' : '' ?>>Terrain</option>
                                    <option value="office" <?= $filters['type'] == 'office' ? 'selected' : '' ?>>Bureau</option>
                                    <option value="commercial" <?= $filters['type'] == 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                </select>
                            </div>
                            
                            <!-- City -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ville</label>
                                <input type="text" name="city" class="form-control form-control-sm" placeholder="Ville" value="<?= esc($filters['city'] ?? '') ?>">
                            </div>
                            
                            <!-- Governorate -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gouvernorat</label>
                                <select name="governorate" class="form-select form-select-sm">
                                    <option value="">Tous</option>
                                    <option value="Tunis" <?= $filters['governorate'] == 'Tunis' ? 'selected' : '' ?>>Tunis</option>
                                    <option value="Ariana" <?= $filters['governorate'] == 'Ariana' ? 'selected' : '' ?>>Ariana</option>
                                    <option value="Ben Arous" <?= $filters['governorate'] == 'Ben Arous' ? 'selected' : '' ?>>Ben Arous</option>
                                    <option value="Manouba" <?= $filters['governorate'] == 'Manouba' ? 'selected' : '' ?>>Manouba</option>
                                    <option value="Nabeul" <?= $filters['governorate'] == 'Nabeul' ? 'selected' : '' ?>>Nabeul</option>
                                    <option value="Sousse" <?= $filters['governorate'] == 'Sousse' ? 'selected' : '' ?>>Sousse</option>
                                    <option value="Monastir" <?= $filters['governorate'] == 'Monastir' ? 'selected' : '' ?>>Monastir</option>
                                    <option value="Mahdia" <?= $filters['governorate'] == 'Mahdia' ? 'selected' : '' ?>>Mahdia</option>
                                    <option value="Sfax" <?= $filters['governorate'] == 'Sfax' ? 'selected' : '' ?>>Sfax</option>
                                    <option value="Bizerte" <?= $filters['governorate'] == 'Bizerte' ? 'selected' : '' ?>>Bizerte</option>
                                </select>
                            </div>
                            
                            <hr>
                            
                            <!-- Price Range -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Prix (TND)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" name="price_min" class="form-control form-control-sm" placeholder="Min" value="<?= esc($filters['price_min'] ?? '') ?>">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="price_max" class="form-control form-control-sm" placeholder="Max" value="<?= esc($filters['price_max'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Area Range -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Surface (m²)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" name="area_min" class="form-control form-control-sm" placeholder="Min" value="<?= esc($filters['area_min'] ?? '') ?>">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="area_max" class="form-control form-control-sm" placeholder="Max" value="<?= esc($filters['area_max'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bedrooms -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Chambres min</label>
                                <select name="bedrooms_min" class="form-select form-select-sm">
                                    <option value="">Toutes</option>
                                    <option value="1" <?= $filters['bedrooms_min'] == '1' ? 'selected' : '' ?>>1+</option>
                                    <option value="2" <?= $filters['bedrooms_min'] == '2' ? 'selected' : '' ?>>2+</option>
                                    <option value="3" <?= $filters['bedrooms_min'] == '3' ? 'selected' : '' ?>>3+</option>
                                    <option value="4" <?= $filters['bedrooms_min'] == '4' ? 'selected' : '' ?>>4+</option>
                                    <option value="5" <?= $filters['bedrooms_min'] == '5' ? 'selected' : '' ?>>5+</option>
                                </select>
                            </div>
                            
                            <!-- Bathrooms -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Salles de bain min</label>
                                <select name="bathrooms_min" class="form-select form-select-sm">
                                    <option value="">Toutes</option>
                                    <option value="1" <?= $filters['bathrooms_min'] == '1' ? 'selected' : '' ?>>1+</option>
                                    <option value="2" <?= $filters['bathrooms_min'] == '2' ? 'selected' : '' ?>>2+</option>
                                    <option value="3" <?= $filters['bathrooms_min'] == '3' ? 'selected' : '' ?>>3+</option>
                                </select>
                            </div>
                            
                            <!-- Reference -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Référence</label>
                                <input type="text" name="reference" class="form-control form-control-sm" placeholder="REF-XXX" value="<?= esc($filters['reference'] ?? '') ?>">
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                            <a href="<?= base_url('search') ?>" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-redo"></i> Réinitialiser
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Results -->
            <div class="col-lg-9">
                <?php if (!empty($properties)): ?>
                    <div class="row g-4">
                        <?php foreach ($properties as $property): ?>
                            <div class="col-md-4">
                                <div class="card h-100 property-card">
                                    <?php 
                                    $imageSrc = !empty($property['main_image']) && !empty($property['main_image']['file_path']) 
                                        ? base_url('uploads/properties/' . $property['main_image']['file_path'])
                                        : base_url('uploads/properties/placeholder.jpg');
                                    ?>
                                    <img src="<?= $imageSrc ?>" class="card-img-top" alt="<?= esc($property['title']) ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-primary"><?= ucfirst($property['type']) ?></span>
                                            <span class="badge bg-success"><?= ucfirst($property['transaction_type']) ?></span>
                                        </div>
                                        <h6 class="card-title"><?= esc($property['title']) ?></h6>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-map-marker-alt"></i> <?= esc($property['city']) ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-primary fw-bold"><?= number_format($property['price'], 0, ',', ' ') ?> TND</span>
                                        </div>
                                        <div class="d-flex gap-3 text-muted small mb-2">
                                            <?php if (isset($property['surface']) && $property['surface']): ?>
                                                <span><i class="fas fa-ruler-combined"></i> <?= $property['surface'] ?> m²</span>
                                            <?php endif; ?>
                                            <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
                                                <span><i class="fas fa-bed"></i> <?= $property['bedrooms'] ?></span>
                                            <?php endif; ?>
                                            <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
                                                <span><i class="fas fa-bath"></i> <?= $property['bathrooms'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?= base_url('properties/' . $property['reference']) ?>" class="btn btn-outline-primary btn-sm w-100">
                                            Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Aucune propriété ne correspond à vos critères de recherche.
                        <br>Essayez de modifier vos filtres ou <a href="<?= base_url('search') ?>">réinitialisez la recherche</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.property-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
</style>

<?= $this->endSection() ?>
