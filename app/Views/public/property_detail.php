<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Image Gallery Section -->
<section class="position-relative" style="background: #000;">
    <?php if (!empty($images)): ?>
        <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($images as $index => $image): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= base_url('uploads/properties/' . $image['file_path']) ?>" 
                             class="d-block w-100" 
                             alt="<?= esc($image['title']) ?>"
                             style="height: 600px; object-fit: contain;">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($images) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
                <div class="position-absolute bottom-0 end-0 m-3 bg-dark bg-opacity-75 text-white px-3 py-2 rounded">
                    <i class="fas fa-images"></i> <span id="currentSlide">1</span> / <?= count($images) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <img src="<?= base_url('uploads/properties/placeholder.jpg') ?>" 
             class="d-block w-100" 
             alt="Pas d'image"
             style="height: 600px; object-fit: cover;">
    <?php endif; ?>
</section>

<!-- Main Content -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <!-- Left Column - Property Details -->
            <div class="col-lg-8">
                <!-- Property Header -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <span class="badge bg-primary"><?= ucfirst($property['type']) ?></span>
                            <span class="badge bg-success"><?= ucfirst($property['transaction_type']) ?></span>
                            <?php if (isset($property['available']) && $property['available']): ?>
                                <span class="badge bg-info">Disponible</span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="h3 mb-3"><?= esc($property['title']) ?></h1>
                        
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt"></i> 
                            <?= esc($property['address'] ?? '') ?>, <?= esc($property['city'] ?? '') ?>
                            <?php if (isset($property['governorate']) && $property['governorate']): ?>
                                , <?= esc($property['governorate']) ?>
                            <?php endif; ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="h2 text-primary mb-0"><?= number_format($property['price'], 0, ',', ' ') ?> TND</h2>
                                <?php if ($property['transaction_type'] === 'rent'): ?>
                                    <small class="text-muted">par mois</small>
                                <?php endif; ?>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Réf: <?= esc($property['reference']) ?></small>
                                <?php if (isset($property['year_built']) && $property['year_built']): ?>
                                    <small class="text-muted d-block">Année: <?= $property['year_built'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Features -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Caractéristiques principales</h5>
                        <div class="row g-4">
                            <?php if (isset($property['surface']) && $property['surface']): ?>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-ruler-combined fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0"><?= $property['surface'] ?> m²</h6>
                                        <small class="text-muted">Surface</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-bed fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0"><?= $property['bedrooms'] ?></h6>
                                        <small class="text-muted">Chambres</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-bath fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0"><?= $property['bathrooms'] ?></h6>
                                        <small class="text-muted">Salles de bain</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['floor']) && $property['floor']): ?>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-building fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0"><?= $property['floor'] ?></h6>
                                        <small class="text-muted">Étage</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Description</h5>
                        <div class="property-description">
                            <?= nl2br(esc($property['description'] ?? '')) ?>
                        </div>
                    </div>
                </div>

                <!-- Prix & Charges -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-money-bill-wave text-primary"></i> Prix & Charges mensuelles
                        </h5>
                        
                        <!-- Prix -->
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-tag text-success"></i> Prix
                        </h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <?php if ($property['transaction_type'] === 'sale' && isset($property['price'])): ?>
                                        <tr>
                                            <td class="text-muted">Prix de vente</td>
                                            <td class="fw-bold text-end text-primary h5">
                                                <?= number_format($property['price'], 2, ',', ' ') ?> TND
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if ($property['transaction_type'] === 'rent' && isset($property['rental_price']) && $property['rental_price']): ?>
                                        <tr>
                                            <td class="text-muted">Prix de location (mensuel)</td>
                                            <td class="fw-bold text-end text-primary h5">
                                                <?= number_format($property['rental_price'], 2, ',', ' ') ?> TND/mois
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Promotion -->
                        <?php if (isset($property['promo_price']) && $property['promo_price'] > 0): ?>
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-percent text-danger"></i> Promotion
                        </h6>
                        <div class="alert alert-danger mb-4">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <strong>Prix promotionnel:</strong>
                                    <span class="h5 text-danger ms-2"><?= number_format($property['promo_price'], 2, ',', ' ') ?> TND</span>
                                    <span class="badge bg-danger ms-2">
                                        -<?= round((($property['price'] - $property['promo_price']) / $property['price']) * 100) ?>%
                                    </span>
                                </div>
                                <?php if (isset($property['promo_start_date']) && $property['promo_start_date']): ?>
                                <div class="col-md-6">
                                    <small><i class="fas fa-calendar-alt"></i> Début: <?= date('d/m/Y', strtotime($property['promo_start_date'])) ?></small>
                                </div>
                                <?php endif; ?>
                                <?php if (isset($property['promo_end_date']) && $property['promo_end_date']): ?>
                                <div class="col-md-6">
                                    <small><i class="fas fa-calendar-times"></i> Fin: <?= date('d/m/Y', strtotime($property['promo_end_date'])) ?></small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Charges mensuelles -->
                        <?php 
                        $hasCharges = (isset($property['charge_syndic']) && $property['charge_syndic'] > 0) ||
                                     (isset($property['charge_water']) && $property['charge_water'] > 0) ||
                                     (isset($property['charge_gas']) && $property['charge_gas'] > 0) ||
                                     (isset($property['charge_electricity']) && $property['charge_electricity'] > 0) ||
                                     (isset($property['charge_other']) && $property['charge_other'] > 0);
                        
                        if ($hasCharges):
                            $totalCharges = ($property['charge_syndic'] ?? 0) + 
                                          ($property['charge_water'] ?? 0) + 
                                          ($property['charge_gas'] ?? 0) + 
                                          ($property['charge_electricity'] ?? 0) + 
                                          ($property['charge_other'] ?? 0);
                        ?>
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-file-invoice-dollar text-warning"></i> Charges mensuelles
                        </h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <?php if (isset($property['charge_syndic']) && $property['charge_syndic'] > 0): ?>
                                        <tr>
                                            <td class="text-muted">Charges de syndic</td>
                                            <td class="fw-bold text-end"><?= number_format($property['charge_syndic'], 2, ',', ' ') ?> TND/mois</td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($property['charge_water']) && $property['charge_water'] > 0): ?>
                                        <tr>
                                            <td class="text-muted">Charges d'eau</td>
                                            <td class="fw-bold text-end"><?= number_format($property['charge_water'], 2, ',', ' ') ?> TND/mois</td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($property['charge_gas']) && $property['charge_gas'] > 0): ?>
                                        <tr>
                                            <td class="text-muted">Charges de gaz</td>
                                            <td class="fw-bold text-end"><?= number_format($property['charge_gas'], 2, ',', ' ') ?> TND/mois</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <?php if (isset($property['charge_electricity']) && $property['charge_electricity'] > 0): ?>
                                        <tr>
                                            <td class="text-muted">Charges d'électricité</td>
                                            <td class="fw-bold text-end"><?= number_format($property['charge_electricity'], 2, ',', ' ') ?> TND/mois</td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($property['charge_other']) && $property['charge_other'] > 0): ?>
                                        <tr>
                                            <td class="text-muted">Autres charges</td>
                                            <td class="fw-bold text-end"><?= number_format($property['charge_other'], 2, ',', ' ') ?> TND/mois</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-0">
                            <strong>Total des charges mensuelles:</strong>
                            <span class="h5 text-primary ms-2"><?= number_format($totalCharges, 2, ',', ' ') ?> TND</span>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-light mb-0">
                            <i class="fas fa-info-circle"></i> Aucune charge mensuelle renseignée pour ce bien
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Detailed Characteristics -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-info-circle text-primary"></i> Caractéristiques détaillées
                        </h5>
                        
                        <!-- Surfaces -->
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-ruler-horizontal text-primary"></i> Surfaces
                        </h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <?php if (isset($property['area_total']) && $property['area_total']): ?>
                                        <tr>
                                            <td class="text-muted">Surface totale (m²)</td>
                                            <td class="fw-bold text-end"><?= number_format($property['area_total'], 2, ',', ' ') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['area_living']) && $property['area_living']): ?>
                                        <tr>
                                            <td class="text-muted">Surface habitable (m²)</td>
                                            <td class="fw-bold text-end"><?= number_format($property['area_living'], 2, ',', ' ') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['area_land']) && $property['area_land']): ?>
                                        <tr>
                                            <td class="text-muted">Surface terrain (m²)</td>
                                            <td class="fw-bold text-end"><?= number_format($property['area_land'], 2, ',', ' ') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Composition -->
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-th-large text-primary"></i> Composition
                        </h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <?php if (isset($property['rooms']) && $property['rooms']): ?>
                                        <tr>
                                            <td class="text-muted">Nombre de pièces</td>
                                            <td class="fw-bold text-end"><?= $property['rooms'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
                                        <tr>
                                            <td class="text-muted">Chambres</td>
                                            <td class="fw-bold text-end"><?= $property['bedrooms'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
                                        <tr>
                                            <td class="text-muted">Salles de bain</td>
                                            <td class="fw-bold text-end"><?= $property['bathrooms'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['parking_spaces'])): ?>
                                        <tr>
                                            <td class="text-muted">Places parking</td>
                                            <td class="fw-bold text-end"><?= $property['parking_spaces'] ?? 0 ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <?php if (isset($property['floor']) && $property['floor']): ?>
                                        <tr>
                                            <td class="text-muted">Étage</td>
                                            <td class="fw-bold text-end"><?= $property['floor'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['total_floors']) && $property['total_floors']): ?>
                                        <tr>
                                            <td class="text-muted">Nombre total d'étages</td>
                                            <td class="fw-bold text-end"><?= $property['total_floors'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['construction_year']) && $property['construction_year']): ?>
                                        <tr>
                                            <td class="text-muted">Année de construction</td>
                                            <td class="fw-bold text-end"><?= $property['construction_year'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Caractéristiques -->
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-star text-primary"></i> Caractéristiques
                        </h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <?php if (isset($property['orientation']) && $property['orientation']): ?>
                                        <tr>
                                            <td class="text-muted">Orientation</td>
                                            <td class="fw-bold text-end"><?= ucfirst($property['orientation']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['floor_type']) && $property['floor_type']): ?>
                                        <tr>
                                            <td class="text-muted">Type de sol</td>
                                            <td class="fw-bold text-end"><?= ucfirst($property['floor_type']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['gas_type']) && $property['gas_type']): ?>
                                        <tr>
                                            <td class="text-muted">Type de gaz</td>
                                            <td class="fw-bold text-end"><?= ucfirst($property['gas_type']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        <?php if (isset($property['standing']) && $property['standing']): ?>
                                        <tr>
                                            <td class="text-muted">Standing</td>
                                            <td class="fw-bold text-end">
                                                <?php
                                                $standing = ucfirst($property['standing']);
                                                $standingLabels = [
                                                    'Economique' => 'Économique',
                                                    'Standard' => 'Standard',
                                                    'Standing' => 'Standing',
                                                    'Premium' => 'Premium',
                                                    'Luxe' => 'Luxe'
                                                ];
                                                echo $standingLabels[$standing] ?? $standing;
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['condition_state']) && $property['condition_state']): ?>
                                        <tr>
                                            <td class="text-muted">État du bien</td>
                                            <td class="fw-bold text-end"><?= ucfirst($property['condition_state']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['legal_status']) && $property['legal_status']): ?>
                                        <tr>
                                            <td class="text-muted">Statut légal</td>
                                            <td class="fw-bold text-end"><?= ucfirst($property['legal_status']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Équipements -->
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-tools text-primary"></i> Équipements
                        </h6>
                        <div class="row">
                            <?php
                            $equipments = [];
                            if (isset($property['has_elevator']) && $property['has_elevator']) $equipments[] = ['icon' => 'fa-elevator', 'label' => 'Ascenseur'];
                            if (isset($property['has_parking']) && $property['has_parking']) $equipments[] = ['icon' => 'fa-parking', 'label' => 'Parking'];
                            if (isset($property['has_garden']) && $property['has_garden']) $equipments[] = ['icon' => 'fa-tree', 'label' => 'Jardin'];
                            if (isset($property['has_pool']) && $property['has_pool']) $equipments[] = ['icon' => 'fa-swimming-pool', 'label' => 'Piscine'];
                            
                            if (!empty($equipments)):
                                foreach ($equipments as $equipment):
                            ?>
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="d-flex align-items-center p-2 bg-light rounded">
                                        <i class="fas <?= $equipment['icon'] ?> text-success me-2"></i>
                                        <span class="small"><?= $equipment['label'] ?></span>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            else:
                            ?>
                                <div class="col-12">
                                    <p class="text-muted small mb-0">Aucun équipement spécifique renseigné</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Rooms -->
                <?php if (!empty($rooms)): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Composition du bien</h5>
                        <div class="row g-3">
                            <?php foreach ($rooms as $room): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start p-3 border rounded">
                                        <i class="fas fa-door-open text-primary me-3 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($room['name'] ?? $room['room_type'] ?? 'Pièce') ?></h6>
                                            <?php if (isset($room['surface']) && $room['surface']): ?>
                                                <p class="mb-0 text-muted small"><?= $room['surface'] ?> m²</p>
                                            <?php endif; ?>
                                            <?php if (isset($room['description']) && $room['description']): ?>
                                                <p class="mb-0 text-muted small"><?= esc($room['description']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Proximities -->
                <?php if (!empty($proximities)): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-location-arrow text-primary"></i> Proximités
                        </h5>
                        <div class="row g-2">
                            <?php foreach ($proximities as $proximity): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span><?= esc($proximity['name'] ?? $proximity['proximity_type'] ?? 'Proximité') ?></span>
                                        <?php if (isset($proximity['distance']) && $proximity['distance']): ?>
                                            <span class="ms-auto text-muted small"><?= $proximity['distance'] ?> m</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Energy Performance -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-leaf text-success"></i> Performance énergétique
                        </h5>
                        
                        <?php if (isset($property['energy_class']) && $property['energy_class']): ?>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <h6 class="small text-muted mb-2">Classe énergétique</h6>
                                <div class="alert alert-info mb-2">
                                    <strong>Classe <?= strtoupper($property['energy_class']) ?></strong>
                                    <?php
                                    $energyLabels = [
                                        'A' => '(Très économe)',
                                        'B' => '(Économe)',
                                        'C' => '(Assez économe)',
                                        'D' => '(Moyennement économe)',
                                        'E' => '(Peu économe)',
                                        'F' => '(Énergivore)',
                                        'G' => '(Très énergivore)'
                                    ];
                                    echo ' ' . ($energyLabels[strtoupper($property['energy_class'])] ?? '');
                                    ?>
                                </div>
                                <?php if (isset($property['energy_consumption_kwh']) && $property['energy_consumption_kwh']): ?>
                                <p class="small mb-0">
                                    <strong>Consommation:</strong> <?= number_format($property['energy_consumption_kwh'], 2, ',', ' ') ?> kWh/m²/an
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="small text-muted mb-2">Consommation énergétique</h6>
                                <div class="energy-scale">
                                    <?php
                                    $energy_classes = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                                    $energy_class = strtoupper($property['energy_class'] ?? 'N/A');
                                    foreach ($energy_classes as $class):
                                        $active = ($class === $energy_class) ? 'active' : '';
                                    ?>
                                        <div class="energy-bar energy-<?= strtolower($class) ?> <?= $active ?>">
                                            <span><?= $class ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (isset($property['co2_emission']) && $property['co2_emission']): ?>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="small text-muted mb-2">Émissions de gaz à effet de serre</h6>
                                <p class="mb-0">
                                    <i class="fas fa-cloud text-warning"></i> 
                                    <strong><?= number_format($property['co2_emission'], 2, ',', ' ') ?> kg CO₂/m²/an</strong>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Informations de performance énergétique non disponibles pour ce bien
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Contact Forms -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 20px;">
                    <!-- Contact Forms Card -->
                    <div class="card mb-3 shadow">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-paper-plane text-primary"></i> Contactez-nous
                            </h5>
                            
                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs mb-3" id="contactTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="visit-tab" data-bs-toggle="tab" data-bs-target="#visit-form" type="button" role="tab">
                                        <i class="fas fa-calendar-check"></i> Visite
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-form" type="button" role="tab">
                                        <i class="fas fa-envelope"></i> Information
                                    </button>
                                </li>
                            </ul>

                            <!-- Tabs Content -->
                            <div class="tab-content" id="contactTabsContent">
                                <!-- Visit Request Tab -->
                                <div class="tab-pane fade show active" id="visit-form" role="tabpanel">
                                    <form id="visitForm">
                                        <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                        <input type="hidden" name="property_reference" value="<?= $property['reference'] ?>">
                                        <input type="hidden" name="request_type" value="visit">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" required placeholder="Votre nom">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" name="phone" required placeholder="+216 12 345 678">
                                            <small class="text-muted">Format: +216 XX XXX XXX</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" placeholder="votre@email.com">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Date souhaitée</label>
                                            <input type="date" class="form-control" name="visit_date" min="<?= date('Y-m-d') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Heure préférée</label>
                                            <select class="form-select" name="visit_time">
                                                <option value="">Choisir...</option>
                                                <option value="morning">Matin (9h-12h)</option>
                                                <option value="afternoon">Après-midi (14h-17h)</option>
                                                <option value="evening">Soir (17h-19h)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Message (optionnel)</label>
                                            <textarea class="form-control" name="message" rows="3" placeholder="Questions ou remarques..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-paper-plane"></i> Demander une visite
                                        </button>
                                    </form>
                                    <div id="visitAlert" class="mt-3"></div>
                                </div>

                                <!-- Information Request Tab -->
                                <div class="tab-pane fade" id="info-form" role="tabpanel">
                                    <form id="infoForm">
                                        <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                        <input type="hidden" name="property_reference" value="<?= $property['reference'] ?>">
                                        <input type="hidden" name="request_type" value="information">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nom complet <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" required placeholder="Votre nom">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" name="phone" required placeholder="+216 12 345 678">
                                            <small class="text-muted">Format: +216 XX XXX XXX</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" placeholder="votre@email.com">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Votre message <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="message" rows="4" required placeholder="Décrivez votre demande..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-info-circle"></i> Demander des informations
                                        </button>
                                    </form>
                                    <div id="infoAlert" class="mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="card shadow">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Partager ce bien</h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-grow-1" onclick="shareProperty('facebook')">
                                    <i class="fab fa-facebook"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info flex-grow-1" onclick="shareProperty('twitter')">
                                    <i class="fab fa-twitter"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success flex-grow-1" onclick="shareProperty('whatsapp')">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary flex-grow-1" onclick="copyLink()">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Similar Properties -->
<?php if (!empty($similar_properties)): ?>
<section class="py-5 bg-white">
    <div class="container">
        <h3 class="mb-4">
            <i class="fas fa-home text-primary"></i> Publications similaires
        </h3>
        <div class="row g-4">
            <?php foreach ($similar_properties as $simProperty): ?>
                <div class="col-md-4">
                    <div class="card h-100 property-card">
                        <?php 
                        $imageSrc = !empty($simProperty['main_image']) && !empty($simProperty['main_image']['file_path']) 
                            ? base_url('uploads/properties/' . $simProperty['main_image']['file_path'])
                            : base_url('uploads/properties/placeholder.jpg');
                        ?>
                        <img src="<?= $imageSrc ?>" class="card-img-top" alt="<?= esc($simProperty['title']) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge bg-primary"><?= ucfirst($simProperty['type']) ?></span>
                                <span class="badge bg-success"><?= ucfirst($simProperty['transaction_type']) ?></span>
                            </div>
                            <h6 class="card-title"><?= esc($simProperty['title']) ?></h6>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt"></i> <?= esc($simProperty['city']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-primary fw-bold"><?= number_format($simProperty['price'], 0, ',', ' ') ?> TND</span>
                            </div>
                            <div class="d-flex gap-3 text-muted small mb-3">
                                <?php if (isset($simProperty['surface']) && $simProperty['surface']): ?>
                                    <span><i class="fas fa-ruler-combined"></i> <?= $simProperty['surface'] ?> m²</span>
                                <?php endif; ?>
                                <?php if (isset($simProperty['bedrooms']) && $simProperty['bedrooms']): ?>
                                    <span><i class="fas fa-bed"></i> <?= $simProperty['bedrooms'] ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url('properties/' . $simProperty['reference']) ?>" class="btn btn-outline-primary btn-sm w-100">
                                Voir ce bien
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
.property-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.property-description {
    line-height: 1.8;
}

.energy-scale {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.energy-bar {
    display: flex;
    align-items: center;
    padding: 8px 15px;
    border-radius: 4px;
    font-weight: bold;
    color: white;
    position: relative;
    opacity: 0.3;
}

.energy-bar.active {
    opacity: 1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.energy-bar span {
    font-size: 14px;
}

.energy-a { background: #319834; width: 40%; }
.energy-b { background: #4db34d; width: 50%; }
.energy-c { background: #b8d234; width: 60%; }
.energy-d { background: #f9e547; width: 70%; }
.energy-e { background: #f5c933; width: 80%; }
.energy-f { background: #f39333; width: 90%; }
.energy-g { background: #e8212e; width: 100%; }

.sticky-top {
    position: sticky;
    top: 20px;
}

@media (max-width: 991px) {
    .sticky-top {
        position: relative;
        top: 0;
    }
}
</style>

<script>
// Carousel slide counter
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('propertyCarousel');
    if (carousel) {
        carousel.addEventListener('slid.bs.carousel', function (e) {
            const currentSlide = document.getElementById('currentSlide');
            if (currentSlide) {
                currentSlide.textContent = e.to + 1;
            }
        });
    }
    
    // Visit Form Submission
    const visitForm = document.getElementById('visitForm');
    if (visitForm) {
        visitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitRequest(new FormData(this), 'visitAlert', visitForm);
        });
    }
    
    // Info Form Submission
    const infoForm = document.getElementById('infoForm');
    if (infoForm) {
        infoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitRequest(new FormData(this), 'infoAlert', infoForm);
        });
    }
});

// Submit request function
function submitRequest(formData, alertId, form) {
    const alertDiv = document.getElementById(alertId);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
    
    // Add property characteristics for client preferences
    formData.append('property_type', '<?= $property['type'] ?>');
    formData.append('property_transaction_type', '<?= $property['transaction_type'] ?>');
    formData.append('property_price', '<?= $property['price'] ?>');
    formData.append('property_city', '<?= $property['city'] ?? '' ?>');
    formData.append('property_governorate', '<?= $property['governorate'] ?? '' ?>');
    <?php if (isset($property['area_total']) && $property['area_total']): ?>
    formData.append('property_area', '<?= $property['area_total'] ?>');
    <?php endif; ?>
    <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
    formData.append('property_bedrooms', '<?= $property['bedrooms'] ?>');
    <?php endif; ?>
    <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
    formData.append('property_bathrooms', '<?= $property['bathrooms'] ?>');
    <?php endif; ?>
    
    fetch('<?= base_url('properties/submit-request') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alertDiv.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            form.reset();
        } else {
            alertDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    })
    .catch(error => {
        alertDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Une erreur est survenue. Veuillez réessayer.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

function shareProperty(platform) {
    const url = window.location.href;
    const title = '<?= addslashes($property['title']) ?>';
    
    let shareUrl;
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Lien copié dans le presse-papier!');
    });
}
</script>

<?= $this->endSection() ?>
