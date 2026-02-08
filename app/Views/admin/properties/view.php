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
            <?php if ($canEdit): ?>
                <a href="<?= base_url('admin/properties/edit/' . $property['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
            <?php endif; ?>
            <a href="<?= base_url('admin/properties') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <?php if ($canEdit && empty($property['owner_name']) && empty($property['owner_phone']) && empty($property['owner_email'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Informations du propriétaire manquantes</h5>
            <p class="mb-3">Les informations du propriétaire de ce bien ne sont pas renseignées. Vous pouvez les ajouter rapidement.</p>
            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#ownerModal">
                <i class="fas fa-user-plus me-1"></i>Ajouter un propriétaire
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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
                                    <div><strong><?= $property['area_total'] ? number_format($property['area_total']) . ' m²' : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-home text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Surface habitable</small>
                                    <div><strong><?= $property['area_living'] ? number_format($property['area_living']) . ' m²' : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Surface terrain</small>
                                    <div><strong><?= $property['area_land'] ? number_format($property['area_land']) . ' m²' : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-door-open text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Nombre de pièces</small>
                                    <div><strong><?= $property['rooms'] ?? '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bed text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Chambres</small>
                                    <div><strong><?= $property['bedrooms'] ?? '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bath text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Salles de bain</small>
                                    <div><strong><?= $property['bathrooms'] ?? '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Étage</small>
                                    <div><strong><?= $property['floor'] ?? '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-layer-group text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Nombre d'étages</small>
                                    <div><strong><?= $property['total_floors'] ?? '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-car text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Parking</small>
                                    <div><strong><?= $property['parking_spaces'] ? $property['parking_spaces'] . ' place(s)' : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Année construction</small>
                                    <div><strong><?= $property['construction_year'] ?? '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-star text-warning me-2"></i>
                                <div>
                                    <small class="text-muted">Standing</small>
                                    <div><strong><?= $property['standing'] ? ucfirst($property['standing']) : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tools text-info me-2"></i>
                                <div>
                                    <small class="text-muted">État</small>
                                    <div><strong><?= $property['condition_state'] ? ucfirst($property['condition_state']) : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-gavel text-secondary me-2"></i>
                                <div>
                                    <small class="text-muted">Statut légal</small>
                                    <div><strong><?= $property['legal_status'] ? ucfirst($property['legal_status']) : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Code postal</small>
                                    <div><strong><?= $property['postal_code'] ?? '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-money-bill text-success me-2"></i>
                                <div>
                                    <small class="text-muted">Estimation interne</small>
                                    <div><strong><?= $property['internal_estimation'] ? number_format($property['internal_estimation'], 0, ',', ' ') . ' TND' : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Équipements</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($property['has_elevator']): ?>
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Ascenseur</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Ascenseur</span>
                        <?php endif ?>
                        <?php if ($property['has_garden']): ?>
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Jardin</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Jardin</span>
                        <?php endif ?>
                        <?php if ($property['has_pool']): ?>
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Piscine</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Piscine</span>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            
            <!-- Informations Propriétaire -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Informations du Propriétaire</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Nom</small>
                                    <div><strong><?= $property['owner_name'] ? esc($property['owner_name']) : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Téléphone</small>
                                    <div><strong><?= $property['owner_phone'] ? esc($property['owner_phone']) : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Email</small>
                                    <div><strong><?= $property['owner_email'] ? esc($property['owner_email']) : '-' ?></strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        
                        <!-- Carte Leaflet -->
                        <div id="propertyMap" style="height: 300px; border-radius: 0.5rem; margin-top: 1rem;"></div>
                    <?php endif ?>
                    <?php if (!empty($property['legal_status'])): ?>
                        <p class="mb-0">
                            <i class="fas fa-gavel text-primary me-2"></i>
                            Statut légal: <strong><?= ucfirst(esc($property['legal_status'])) ?></strong>
                        </p>
                    <?php endif ?>
                </div>
            </div>
            
            <!-- Documents -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Documents</h5>
                </div>
                <div class="card-body">
                    <?php 
                    $documentTypes = [
                        'contrat' => ['icon' => 'file-contract', 'label' => 'Contrat', 'color' => 'primary'],
                        'titre_foncier' => ['icon' => 'certificate', 'label' => 'Titre foncier', 'color' => 'success'],
                        'plan' => ['icon' => 'drafting-compass', 'label' => 'Plan', 'color' => 'info'],
                        'diagnostic_performance_energetique' => ['icon' => 'leaf', 'label' => 'Diagnostic Performance Énergétique (DPE)', 'color' => 'success'],
                        'diagnostic_technique' => ['icon' => 'clipboard-check', 'label' => 'Diagnostic technique', 'color' => 'warning'],
                        'certificat_conformite' => ['icon' => 'check-circle', 'label' => 'Certificat de conformité', 'color' => 'success'],
                        'autorisation_construction' => ['icon' => 'hard-hat', 'label' => 'Autorisation de construction', 'color' => 'danger'],
                        'photo' => ['icon' => 'image', 'label' => 'Photos supplémentaires', 'color' => 'info'],
                        'autre' => ['icon' => 'file', 'label' => 'Autres documents', 'color' => 'secondary']
                    ];
                    
                    // Indexer les documents par type
                    $documentsByType = [];
                    foreach ($documents as $doc) {
                        if (!isset($documentsByType[$doc['document_type']])) {
                            $documentsByType[$doc['document_type']] = [];
                        }
                        $documentsByType[$doc['document_type']][] = $doc;
                    }
                    ?>
                    
                    <div class="row g-3">
                        <?php foreach ($documentTypes as $type => $info): ?>
                            <?php 
                            $hasDocument = isset($documentsByType[$type]);
                            $docs = $documentsByType[$type] ?? [];
                            ?>
                            <div class="col-md-6">
                                <div class="card h-100 <?= $hasDocument ? 'border-' . $info['color'] : 'border-secondary' ?>" style="border-width: 2px;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <i class="fas fa-<?= $info['icon'] ?> text-<?= $hasDocument ? $info['color'] : 'muted' ?> me-2"></i>
                                                <strong><?= $info['label'] ?></strong>
                                            </div>
                                            <?php if ($hasDocument): ?>
                                                <span class="badge bg-<?= $info['color'] ?>">
                                                    <i class="fas fa-check me-1"></i>Disponible
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>Non disponible
                                                </span>
                                            <?php endif ?>
                                        </div>
                                        
                                        <?php if ($hasDocument): ?>
                                            <div class="mt-2">
                                                <?php foreach ($docs as $doc): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= base_url('uploads/documents/' . $doc['file_path']) ?>" 
                                                           target="_blank" 
                                                           class="text-decoration-none">
                                                            <i class="fas fa-download me-1"></i>
                                                            <?= esc($doc['file_name']) ?>
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?= $doc['file_size'] ? number_format($doc['file_size'] / 1024, 0) . ' KB' : '-' ?> · 
                                                            <?= date('d/m/Y', strtotime($doc['created_at'])) ?>
                                                        </small>
                                                        <?php if (!empty($doc['description'])): ?>
                                                            <br><small class="text-muted"><?= esc($doc['description']) ?></small>
                                                        <?php endif ?>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                        <?php else: ?>
                                            <small class="text-muted">Aucun document de ce type n'est disponible</small>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    
                    <?php if (empty($documents)): ?>
                        <div class="alert alert-info mb-0 mt-3">
                            <i class="fas fa-info-circle me-2"></i>Aucun document n'a été ajouté pour ce bien.
                        </div>
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

<!-- Modal Ajout Propriétaire -->
<?php if ($canEdit): ?>
<div class="modal fade" id="ownerModal" tabindex="-1" aria-labelledby="ownerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ownerModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Ajouter un propriétaire
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="ownerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="search-tab" data-bs-toggle="tab" data-bs-target="#search" type="button">
                            <i class="fas fa-search me-1"></i>Rechercher
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="add-tab" data-bs-toggle="tab" data-bs-target="#add" type="button">
                            <i class="fas fa-plus me-1"></i>Nouveau propriétaire
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="ownerTabsContent">
                    <!-- Onglet Recherche -->
                    <div class="tab-pane fade show active" id="search" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Rechercher un propriétaire existant</label>
                            <input type="text" class="form-control" id="searchOwnerInput" placeholder="Nom, téléphone ou email...">
                            <small class="text-muted">Tapez au moins 3 caractères pour rechercher</small>
                        </div>
                        <div id="searchResults" class="mt-3"></div>
                    </div>

                    <!-- Onglet Ajout -->
                    <div class="tab-pane fade" id="add" role="tabpanel">
                        <form id="ownerForm">
                            <div class="mb-3">
                                <label class="form-label">Type de client <span class="text-danger">*</span></label>
                                <select class="form-select" id="client_type" name="client_type">
                                    <option value="individual">Particulier</option>
                                    <option value="company">Entreprise</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="owner_name" class="form-label">
                                        <span class="individual-label">Nom complet</span>
                                        <span class="company-label" style="display:none;">Nom de l'entreprise</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="owner_name" name="owner_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="owner_phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="owner_phone" name="owner_phone" required placeholder="+216 12 345 678">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="owner_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="owner_email" name="owner_email" placeholder="email@exemple.com">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveOwnerBtn">
                    <i class="fas fa-save me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if ($canEdit): ?>
<script>
let searchTimeout;

// Recherche de propriétaires
$('#searchOwnerInput').on('keyup', function() {
    const query = $(this).val().trim();
    
    if (query.length < 3) {
        $('#searchResults').html('');
        return;
    }
    
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        $.ajax({
            url: '<?= base_url('admin/properties/search-owners') ?>',
            method: 'GET',
            data: { q: query },
            success: function(response) {
                if (response.success && response.owners.length > 0) {
                    let html = '<div class="list-group">';
                    response.owners.forEach(owner => {
                        html += `
                            <a href="#" class="list-group-item list-group-item-action owner-item" 
                               data-id="${owner.id}"
                               data-name="${owner.name}" 
                               data-phone="${owner.phone}" 
                               data-email="${owner.email || ''}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        ${owner.name}
                                        ${owner.type === 'company' ? '<span class="badge bg-info ms-2">Entreprise</span>' : '<span class="badge bg-secondary ms-2">Particulier</span>'}
                                    </h6>
                                </div>
                                <small><i class="fas fa-phone me-1"></i>${owner.phone}</small>
                                ${owner.phone_secondary ? `<small class="ms-2"><i class="fas fa-mobile-alt me-1"></i>${owner.phone_secondary}</small>` : ''}
                                ${owner.email ? `<br><small><i class="fas fa-envelope me-1"></i>${owner.email}</small>` : ''}
                            </a>
                        `;
                    });
                    html += '</div>';
                    $('#searchResults').html(html);
                } else {
                    $('#searchResults').html('<div class="alert alert-info">Aucun propriétaire trouvé</div>');
                }
            }
        });
    }, 300);
});

// Sélection d'un propriétaire depuis les résultats
$(document).on('click', '.owner-item', function(e) {
    e.preventDefault();
    const clientId = $(this).data('id');
    const name = $(this).data('name');
    const phone = $(this).data('phone');
    const email = $(this).data('email');
    
    if (confirm(`Associer le client "${name}" à ce bien ?`)) {
        saveOwnerInfo(clientId, name, phone, email);
    }
});

// Toggle label selon le type de client
$('#client_type').on('change', function() {
    if ($(this).val() === 'company') {
        $('.individual-label').hide();
        $('.company-label').show();
    } else {
        $('.individual-label').show();
        $('.company-label').hide();
    }
});

// Enregistrer un nouveau propriétaire
$('#saveOwnerBtn').on('click', function() {
    const activeTab = $('#ownerTabs .nav-link.active').attr('id');
    
    if (activeTab === 'add-tab') {
        // Nouveau propriétaire
        const name = $('#owner_name').val().trim();
        const phone = $('#owner_phone').val().trim();
        const email = $('#owner_email').val().trim();
        const clientType = $('#client_type').val();
        
        if (!name || !phone) {
            alert('Le nom et le téléphone sont obligatoires');
            return;
        }
        
        saveOwnerInfo(null, name, phone, email, clientType);
    } else {
        alert('Veuillez sélectionner un client dans les résultats de recherche');
    }
});

// Fonction pour sauvegarder les infos propriétaire
function saveOwnerInfo(clientId, name, phone, email, clientType) {
    const postData = {
        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
    };
    
    if (clientId) {
        // Client existant
        postData.client_id = clientId;
    } else {
        // Nouveau client
        postData.owner_name = name;
        postData.owner_phone = phone;
        postData.owner_email = email;
        postData.client_type = clientType;
    }
    
    $.ajax({
        url: '<?= base_url('admin/properties/update-owner/' . $property['id']) ?>',
        method: 'POST',
        data: postData,
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Erreur: ' + response.message);
            }
        },
        error: function() {
            alert('Une erreur est survenue');
        }
    });
}
</script>
<?php endif; ?>

<?php if (!empty($property['latitude']) && !empty($property['longitude'])): ?>
<!-- Leaflet Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la carte
    const map = L.map('propertyMap').setView([<?= $property['latitude'] ?>, <?= $property['longitude'] ?>], 15);
    
    // Ajouter le layer de tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Ajouter un marqueur pour la propriété
    const marker = L.marker([<?= $property['latitude'] ?>, <?= $property['longitude'] ?>]).addTo(map);
    marker.bindPopup(`
        <div style="text-align: center;">
            <strong><?= esc($property['title']) ?></strong><br>
            <small><?= esc($property['address']) ?></small>
        </div>
    `).openPopup();
});
</script>
<?php endif; ?>

<?= $this->endSection() ?>
