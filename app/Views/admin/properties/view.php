<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= esc($property['title']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/properties') ?>">Propriétés</a></li>
                    <li class="breadcrumb-item active">Détails</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/properties/edit/' . $property['id']) ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="<?= base_url('admin/properties') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations Principales -->
        <div class="col-lg-8">
            <!-- Images -->
            <?php if (!empty($property['images'])): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-images me-2"></i>Photos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($property['images'] as $image): ?>
                                <div class="col-md-4">
                                    <img src="<?= base_url('uploads/properties/' . $image['file_path']) ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?= esc($property['title']) ?>">
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <!-- Description -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Description</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($property['title_ar']) || !empty($property['title_en'])): ?>
                        <h6 class="mb-3"><i class="fas fa-language me-2"></i>Titres multilingues</h6>
                        <?php if (!empty($property['title'])): ?>
                            <div class="mb-2">
                                <span class="badge bg-primary">FR</span>
                                <strong><?= esc($property['title']) ?></strong>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['title_ar'])): ?>
                            <div class="mb-2">
                                <span class="badge bg-info">AR</span>
                                <strong><?= esc($property['title_ar']) ?></strong>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['title_en'])): ?>
                            <div class="mb-2">
                                <span class="badge bg-success">EN</span>
                                <strong><?= esc($property['title_en']) ?></strong>
                            </div>
                        <?php endif ?>
                        <hr class="my-3">
                    <?php endif ?>
                    
                    <h6 class="mb-2"><i class="fas fa-file-alt me-2"></i>Description (FR)</h6>
                    <p><?= nl2br(esc($property['description'])) ?></p>
                    
                    <?php if (!empty($property['description_ar'])): ?>
                        <hr class="my-3">
                        <h6 class="mb-2"><i class="fas fa-file-alt me-2"></i>Description (AR)</h6>
                        <p dir="rtl"><?= nl2br(esc($property['description_ar'])) ?></p>
                    <?php endif ?>
                    
                    <?php if (!empty($property['description_en'])): ?>
                        <hr class="my-3">
                        <h6 class="mb-2"><i class="fas fa-file-alt me-2"></i>Description (EN)</h6>
                        <p><?= nl2br(esc($property['description_en'])) ?></p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Caractéristiques -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i>Caractéristiques</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-ruler-combined text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Surface totale</small>
                                    <div><strong><?= number_format($property['area_total'] ?? 0) ?> m²</strong></div>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($property['area_living'])): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-home text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Surface habitable</small>
                                        <div><strong><?= number_format($property['area_living']) ?> m²</strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['area_land'])): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Surface terrain</small>
                                        <div><strong><?= number_format($property['area_land']) ?> m²</strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['rooms'])): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-door-open text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Nombre de pièces</small>
                                        <div><strong><?= $property['rooms'] ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bed text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Chambres</small>
                                    <div><strong><?= $property['bedrooms'] ?? 0 ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bath text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Salles de bain</small>
                                    <div><strong><?= $property['bathrooms'] ?? 0 ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($property['floor'])): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-building text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Étage</small>
                                        <div><strong><?= esc($property['floor']) ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['parking_spaces']) && $property['parking_spaces'] > 0): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-car text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Parking</small>
                                        <div><strong><?= esc($property['parking_spaces']) ?> place(s)</strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['construction_year'])): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Année construction</small>
                                        <div><strong><?= esc($property['construction_year']) ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['total_floors'])): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-layer-group text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Nombre d'étages</small>
                                        <div><strong><?= esc($property['total_floors']) ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['standing'])): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    <div>
                                        <small class="text-muted">Standing</small>
                                        <div><strong><?= ucfirst(esc($property['standing'])) ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if (!empty($property['condition_state'])): ?>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tools text-info me-2"></i>
                                    <div>
                                        <small class="text-muted">État</small>
                                        <div><strong><?= ucfirst(esc($property['condition_state'])) ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>

                    <?php if (!empty($property['has_elevator']) || !empty($property['has_garden']) || !empty($property['has_pool'])): ?>
                        <hr class="my-3">
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (!empty($property['has_elevator'])): ?>
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Ascenseur</span>
                            <?php endif ?>
                            <?php if (!empty($property['has_garden'])): ?>
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Jardin</span>
                            <?php endif ?>
                            <?php if (!empty($property['has_pool'])): ?>
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Piscine</span>
                            <?php endif ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Localisation -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Localisation</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="fas fa-map-pin text-primary me-2"></i>
                        <strong><?= esc($property['address']) ?></strong>
                    </p>
                    <?php if ($property['city']): ?>
                        <p class="mb-2">
                            <i class="fas fa-city text-primary me-2"></i>
                            <?= esc($property['city']) ?><?= $property['governorate'] ? ', ' . esc($property['governorate']) : '' ?>
                            <?php if (!empty($property['postal_code'])): ?>
                                - <?= esc($property['postal_code']) ?>
                            <?php endif ?>
                        </p>
                    <?php endif ?>
                    <?php if ($property['zone_name']): ?>
                        <p class="mb-2">
                            <i class="fas fa-map text-primary me-2"></i>
                            Zone: <?= esc($property['zone_name']) ?>
                        </p>
                    <?php endif ?>
                    <?php if (!empty($property['latitude']) && !empty($property['longitude'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-globe text-primary me-2"></i>
                            Coordonnées GPS: <code><?= number_format($property['latitude'], 6) ?>, <?= number_format($property['longitude'], 6) ?></code>
                        </p>
                    <?php endif ?>
                    <?php if (!empty($property['legal_status'])): ?>
                        <p class="mb-0">
                            <i class="fas fa-gavel text-primary me-2"></i>
                            Statut légal: <strong><?= ucfirst(esc($property['legal_status'])) ?></strong>
                        </p>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Prix & Statut -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <?php if ($property['transaction_type'] === 'sale' || $property['transaction_type'] === 'both'): ?>
                        <div class="mb-3">
                            <small class="text-muted">Prix de vente</small>
                            <h2 class="text-primary mb-0"><?= number_format($property['price']) ?> TND</h2>
                        </div>
                    <?php endif ?>
                    
                    <?php if ($property['transaction_type'] === 'rent' || $property['transaction_type'] === 'both'): ?>
                        <div class="mb-3">
                            <small class="text-muted">Prix de location</small>
                            <h3 class="text-success mb-0"><?= number_format($property['rental_price']) ?> TND/mois</h3>
                        </div>
                    <?php endif ?>

                    <div class="mt-3">
                        <span class="badge bg-<?= $property['status'] === 'published' ? 'success' : 'warning' ?> fs-6">
                            <?= ucfirst($property['status']) ?>
                        </span>
                        <?php if ($property['featured']): ?>
                            <span class="badge bg-primary fs-6"><i class="fas fa-star me-1"></i>En vedette</span>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <!-- Informations -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Référence</small>
                        <div><strong><?= esc($property['reference']) ?></strong></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Type</small>
                        <div><strong><?= ucfirst($property['type']) ?></strong></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Transaction</small>
                        <div><strong><?= ucfirst($property['transaction_type']) ?></strong></div>
                    </div>
                    <?php if ($property['agent_name']): ?>
                        <div class="mb-3">
                            <small class="text-muted">Agent</small>
                            <div><strong><?= esc($property['agent_name']) ?></strong></div>
                        </div>
                    <?php endif ?>
                    <?php if ($property['agency_name']): ?>
                        <div class="mb-3">
                            <small class="text-muted">Agence</small>
                            <div><strong><?= esc($property['agency_name']) ?></strong></div>
                        </div>
                    <?php endif ?>
                    <?php if (!empty($property['internal_estimation'])): ?>
                        <div class="mb-3">
                            <small class="text-muted">Estimation interne</small>
                            <div><strong><?= number_format($property['internal_estimation']) ?> TND</strong></div>
                        </div>
                    <?php endif ?>
                    <div class="mb-3">
                        <small class="text-muted">Vues</small>
                        <div><strong><?= number_format($property['views_count'] ?? 0) ?></strong></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Créé le</small>
                        <div><strong><?= date('d/m/Y', strtotime($property['created_at'])) ?></strong></div>
                    </div>
                    <?php if (!empty($property['published_at'])): ?>
                        <div class="mb-0">
                            <small class="text-muted">Publié le</small>
                            <div><strong><?= date('d/m/Y', strtotime($property['published_at'])) ?></strong></div>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Propriétaire -->
            <?php if ($property['owner_name']): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Propriétaire</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong><?= esc($property['owner_name']) ?></strong></p>
                        <?php if ($property['owner_phone']): ?>
                            <p class="mb-2">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <a href="tel:<?= esc($property['owner_phone']) ?>"><?= esc($property['owner_phone']) ?></a>
                            </p>
                        <?php endif ?>
                        <?php if ($property['owner_email']): ?>
                            <p class="mb-0">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <a href="mailto:<?= esc($property['owner_email']) ?>"><?= esc($property['owner_email']) ?></a>
                            </p>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
