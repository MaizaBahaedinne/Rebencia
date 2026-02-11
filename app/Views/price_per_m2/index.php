<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<style>
.price-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
}

.price-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s;
    height: 100%;
}

.price-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.price-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
}

.price-value {
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
}

.evolution-badge {
    font-size: 0.9rem;
    padding: 5px 12px;
    border-radius: 20px;
}

.evolution-up {
    background-color: #d4edda;
    color: #155724;
}

.evolution-down {
    background-color: #f8d7da;
    color: #721c24;
}

.filter-section {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    margin-top: -40px;
    position: relative;
    z-index: 10;
}

.zone-group {
    margin-bottom: 40px;
}

.zone-group-title {
    background: #f8f9fa;
    padding: 15px 20px;
    border-left: 4px solid #667eea;
    margin-bottom: 20px;
    font-size: 1.3rem;
    font-weight: 600;
}

.info-section {
    background: #f8f9fa;
    padding: 40px 0;
    margin-top: 60px;
}
</style>

<!-- Hero Section -->
<div class="price-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Prix de l'immobilier au m²</h1>
                <p class="lead">Découvrez les prix de l'immobilier en Tunisie. Consultez les tendances du marché par zone, ville et type de bien.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-chart-line" style="font-size: 100px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="get" action="<?= base_url('prix-m2') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Gouvernorat</label>
                    <select name="governorate" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les gouvernorats</option>
                        <?php foreach ($governorates as $gov): ?>
                            <option value="<?= esc($gov['governorate']) ?>" <?= $filters['governorate'] === $gov['governorate'] ? 'selected' : '' ?>>
                                <?= esc($gov['governorate']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!empty($cities)): ?>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Ville</label>
                    <select name="city" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les villes</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= esc($city['city']) ?>" <?= $filters['city'] === $city['city'] ? 'selected' : '' ?>>
                                <?= esc($city['city']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Type de bien</label>
                    <select name="property_type" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les types</option>
                        <option value="apartment" <?= $filters['property_type'] === 'apartment' ? 'selected' : '' ?>>Appartement</option>
                        <option value="villa" <?= $filters['property_type'] === 'villa' ? 'selected' : '' ?>>Villa</option>
                        <option value="studio" <?= $filters['property_type'] === 'studio' ? 'selected' : '' ?>>Studio</option>
                        <option value="office" <?= $filters['property_type'] === 'office' ? 'selected' : '' ?>>Bureau</option>
                        <option value="shop" <?= $filters['property_type'] === 'shop' ? 'selected' : '' ?>>Commerce</option>
                        <option value="land" <?= $filters['property_type'] === 'land' ? 'selected' : '' ?>>Terrain</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Transaction</label>
                    <select name="transaction_type" class="form-select" onchange="this.form.submit()">
                        <option value="sale" <?= $filters['transaction_type'] === 'sale' ? 'selected' : '' ?>>Vente</option>
                        <option value="rent" <?= $filters['transaction_type'] === 'rent' ? 'selected' : '' ?>>Location</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="py-5">
        <?php if (empty($groupedPrices)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun prix disponible pour ces critères. Essayez de modifier vos filtres.</p>
            </div>
        <?php else: ?>
            <?php foreach ($groupedPrices as $governorate => $cities): ?>
                <div class="zone-group">
                    <h2 class="zone-group-title">
                        <i class="fas fa-map-marker-alt me-2"></i> <?= esc($governorate) ?>
                    </h2>

                    <?php foreach ($cities as $city => $cityPrices): ?>
                        <h3 class="h5 text-muted mb-3 ms-4">
                            <i class="fas fa-location-dot me-2"></i> <?= esc($city) ?>
                        </h3>

                        <div class="row g-4 mb-4">
                            <?php foreach ($cityPrices as $price): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card price-card shadow-sm">
                                        <div class="price-card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
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
                                                    $typeIcon = [
                                                        'apartment' => 'building',
                                                        'villa' => 'home',
                                                        'studio' => 'door-open',
                                                        'office' => 'briefcase',
                                                        'shop' => 'store',
                                                        'warehouse' => 'warehouse',
                                                        'land' => 'map'
                                                    ];
                                                    ?>
                                                    <i class="fas fa-<?= $typeIcon[$price['property_type']] ?? 'home' ?> me-2"></i>
                                                    <span class="fw-bold"><?= $types[$price['property_type']] ?? $price['property_type'] ?></span>
                                                </div>
                                                <span class="badge bg-light text-dark">
                                                    <?= $price['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <div class="price-value"><?= number_format($price['price_average'], 0, ',', ' ') ?> DT</div>
                                                <small class="text-muted">Prix moyen / m²</small>
                                            </div>

                                            <?php if ($price['evolution']): ?>
                                                <div class="text-center mb-3">
                                                    <span class="evolution-badge <?= $price['evolution'] > 0 ? 'evolution-up' : 'evolution-down' ?>">
                                                        <i class="fas fa-arrow-<?= $price['evolution'] > 0 ? 'up' : 'down' ?>"></i>
                                                        <?= abs($price['evolution']) ?>% 
                                                        <?= $price['evolution'] > 0 ? 'en hausse' : 'en baisse' ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>

                                            <hr>

                                            <div class="row text-center g-2">
                                                <?php if ($price['price_min']): ?>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Min</small>
                                                    <strong><?= number_format($price['price_min'], 0, ',', ' ') ?></strong>
                                                </div>
                                                <?php endif; ?>

                                                <?php if ($price['price_max']): ?>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Max</small>
                                                    <strong><?= number_format($price['price_max'], 0, ',', ' ') ?></strong>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($price['surface_average'] || $price['properties_count']): ?>
                                                <hr>
                                                <div class="d-flex justify-content-around text-muted small">
                                                    <?php if ($price['surface_average']): ?>
                                                        <div>
                                                            <i class="fas fa-ruler-combined me-1"></i>
                                                            <?= round($price['surface_average']) ?> m² moy.
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($price['properties_count']): ?>
                                                        <div>
                                                            <i class="fas fa-home me-1"></i>
                                                            <?= $price['properties_count'] ?> biens
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Info Section -->
<div class="info-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="text-center mb-4">Comprendre les prix de l'immobilier</h2>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5><i class="fas fa-question-circle text-primary me-2"></i> Comment sont calculés ces prix ?</h5>
                        <p class="text-muted mb-0">Les prix affichés sont des moyennes calculées à partir des biens actuellement sur le marché. Ils reflètent les tendances réelles du marché immobilier dans chaque zone.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5><i class="fas fa-chart-line text-success me-2"></i> Évolution des prix</h5>
                        <p class="text-muted mb-0">Les pourcentages d'évolution indiquent la variation des prix par rapport à la période précédente, vous permettant de suivre les tendances du marché.</p>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <h4 class="mb-3">Besoin d'estimer votre bien ?</h4>
                    <a href="<?= base_url('estimer-mon-bien') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-calculator me-2"></i> Estimer mon bien gratuitement
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
