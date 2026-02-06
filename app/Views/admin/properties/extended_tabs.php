<!-- app/Views/admin/properties/extended_tabs.php -->
<!-- Tabs pour données étendues - À inclure dans edit/view property -->

<?php
$extended = model(\App\Models\PropertyExtendedModel::class);
$config = isset($config) && $config !== null ? $config : service(\App\Services\PropertyConfigService::class);
$propertyType = $property['type'] ?? 'apartment';
$propertyId = $property['id'] ?? 0;

// Récupérer données existantes
$rooms = $extended->getRooms($propertyId);
$options = $extended->getOptions($propertyId);
$locationScoring = $extended->getLocationScoring($propertyId);
$financialData = $extended->getFinancialData($propertyId);
$costs = $extended->getEstimatedCosts($propertyId);
$orientation = $extended->getOrientation($propertyId);
$mediaFiles = $extended->getMediaExtension($propertyId);

// Vérifier features activées
$enabledFeatures = $config->getVisibleSections($propertyType);
?>

<ul class="nav nav-tabs" id="extendedDataTabs" role="tablist">
    <?php if (in_array('rooms', $enabledFeatures)): ?>
    <li class="nav-item">
        <a class="nav-link active" id="rooms-tab" data-toggle="tab" href="#rooms" role="tab">
            <i class="fas fa-door-open"></i> Pièces & Dimensions
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (in_array('options', $enabledFeatures)): ?>
    <li class="nav-item">
        <a class="nav-link" id="options-tab" data-toggle="tab" href="#options" role="tab">
            <i class="fas fa-list-check"></i> Équipements
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (in_array('location_scoring', $enabledFeatures)): ?>
    <li class="nav-item">
        <a class="nav-link" id="location-tab" data-toggle="tab" href="#location" role="tab">
            <i class="fas fa-map-marker-alt"></i> Localisation
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (in_array('financial_data', $enabledFeatures)): ?>
    <li class="nav-item">
        <a class="nav-link" id="financial-tab" data-toggle="tab" href="#financial" role="tab">
            <i class="fas fa-chart-line"></i> Finances
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (in_array('estimated_costs', $enabledFeatures)): ?>
    <li class="nav-item">
        <a class="nav-link" id="costs-tab" data-toggle="tab" href="#costs" role="tab">
            <i class="fas fa-coins"></i> Charges
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (in_array('orientation', $enabledFeatures)): ?>
    <li class="nav-item">
        <a class="nav-link" id="orientation-tab" data-toggle="tab" href="#orientation" role="tab">
            <i class="fas fa-compass"></i> Orientation
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (in_array('media_extension', $enabledFeatures)): ?>
    <li class="nav-item">
        <a class="nav-link" id="media-tab" data-toggle="tab" href="#media" role="tab">
            <i class="fas fa-photo-video"></i> Médias
        </a>
    </li>
    <?php endif; ?>
</ul>

<div class="tab-content" id="extendedDataTabsContent">
    
    <!-- TAB: PIÈCES -->
    <?php if (in_array('rooms', $enabledFeatures)): ?>
    <div class="tab-pane fade show active" id="rooms" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5>Gestion des Pièces</h5>
                <button type="button" class="btn btn-sm btn-primary mb-3" id="addRoomBtn">
                    <i class="fas fa-plus"></i> Ajouter une pièce
                </button>
                
                <div id="roomsContainer">
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $index => $room): ?>
                            <?= view('admin/properties/partials/room_form', ['room' => $room, 'index' => $index]) ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <button type="button" class="btn btn-success mt-3" id="saveRoomsBtn">
                    <i class="fas fa-save"></i> Enregistrer les pièces
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- TAB: OPTIONS -->
    <?php if (in_array('options', $enabledFeatures)): ?>
    <div class="tab-pane fade" id="options" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5>Équipements & Options</h5>
                <?php
                $db = \Config\Database::connect();
                $allOptions = $db->table('property_options')
                    ->where('is_active', 1)
                    ->orderBy('category', 'ASC')
                    ->orderBy('sort_order', 'ASC')
                    ->get()
                    ->getResultArray();
                
                $categories = ['comfort' => 'Confort', 'outdoor' => 'Extérieur', 'parking' => 'Parking', 
                              'security' => 'Sécurité', 'amenities' => 'Équipements', 'other' => 'Autre'];
                
                $selectedOptions = array_column($options, 'option_id');
                ?>
                
                <?php foreach ($categories as $catKey => $catLabel): ?>
                    <?php $catOptions = array_filter($allOptions, fn($o) => $o['category'] === $catKey); ?>
                    <?php if (!empty($catOptions)): ?>
                        <h6 class="mt-3"><?= $catLabel ?></h6>
                        <div class="row">
                            <?php foreach ($catOptions as $option): ?>
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input option-checkbox" 
                                               id="option_<?= $option['id'] ?>" 
                                               name="options[]" 
                                               value="<?= $option['id'] ?>"
                                               <?= in_array($option['id'], $selectedOptions) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="option_<?= $option['id'] ?>">
                                            <i class="fas <?= $option['icon'] ?? 'fa-check' ?>"></i>
                                            <?= $option['name_fr'] ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <button type="button" class="btn btn-success mt-3" id="saveOptionsBtn">
                    <i class="fas fa-save"></i> Enregistrer les options
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- TAB: LOCALISATION -->
    <?php if (in_array('location_scoring', $enabledFeatures)): ?>
    <div class="tab-pane fade" id="location" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5>Scoring de Localisation</h5>
                <p class="text-muted">Évaluez la proximité et la qualité de l'environnement (0-100)</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Proximité Écoles</label>
                            <input type="range" class="form-control-range location-score" 
                                   id="proximity_to_schools" min="0" max="100" 
                                   value="<?= $locationScoring['proximity_to_schools'] ?? 50 ?>">
                            <span class="score-value"><?= $locationScoring['proximity_to_schools'] ?? 50 ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Proximité Transports</label>
                            <input type="range" class="form-control-range location-score" 
                                   id="proximity_to_transport" min="0" max="100" 
                                   value="<?= $locationScoring['proximity_to_transport'] ?? 50 ?>">
                            <span class="score-value"><?= $locationScoring['proximity_to_transport'] ?? 50 ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Proximité Commerces</label>
                            <input type="range" class="form-control-range location-score" 
                                   id="proximity_to_shopping" min="0" max="100" 
                                   value="<?= $locationScoring['proximity_to_shopping'] ?? 50 ?>">
                            <span class="score-value"><?= $locationScoring['proximity_to_shopping'] ?? 50 ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sécurité du Quartier</label>
                            <input type="range" class="form-control-range location-score" 
                                   id="area_safety_score" min="0" max="100" 
                                   value="<?= $locationScoring['area_safety_score'] ?? 50 ?>">
                            <span class="score-value"><?= $locationScoring['area_safety_score'] ?? 50 ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <strong>Score Global:</strong> <span id="overallLocationScore">--</span>/100
                </div>
                
                <button type="button" class="btn btn-success" id="saveLocationBtn">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- TAB: FINANCES -->
    <?php if (in_array('financial_data', $enabledFeatures)): ?>
    <div class="tab-pane fade" id="financial" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5>Données Financières</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Prix de Marché Estimé</label>
                            <input type="number" class="form-control" id="estimated_market_price" 
                                   value="<?= $financialData['estimated_market_price'] ?? $property['price'] ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Loyer Mensuel Estimé</label>
                            <input type="number" class="form-control" id="estimated_rental_price" 
                                   value="<?= $financialData['estimated_rental_price'] ?? $property['rental_price'] ?>">
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-success" id="financialMetrics">
                    <h6>Métriques Calculées</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Rendement Brut:</strong> <span id="grossYield">--</span>%
                        </div>
                        <div class="col-md-3">
                            <strong>Rendement Net:</strong> <span id="netYield">--</span>%
                        </div>
                        <div class="col-md-3">
                            <strong>Cap Rate:</strong> <span id="capRate">--</span>%
                        </div>
                        <div class="col-md-3">
                            <strong>Prix/m²:</strong> <span id="pricePerSqm">--</span> TND
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success" id="saveFinancialBtn">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- TAB: CHARGES -->
    <?php if (in_array('estimated_costs', $enabledFeatures)): ?>
    <div class="tab-pane fade" id="costs" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5>Charges Estimées</h5>
                
                <h6 class="mt-3">Charges Mensuelles</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Syndic</label>
                            <input type="number" class="form-control cost-input" id="monthly_syndic" 
                                   value="<?= $costs['monthly_syndic'] ?? 0 ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Électricité</label>
                            <input type="number" class="form-control cost-input" id="monthly_electricity" 
                                   value="<?= $costs['monthly_electricity'] ?? 0 ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Eau</label>
                            <input type="number" class="form-control cost-input" id="monthly_water" 
                                   value="<?= $costs['monthly_water'] ?? 0 ?>">
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <strong>Total Mensuel:</strong> <span id="totalMonthlyCosts">0</span> TND
                </div>
                
                <button type="button" class="btn btn-success" id="saveCostsBtn">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- TAB: ORIENTATION -->
    <?php if (in_array('orientation', $enabledFeatures)): ?>
    <div class="tab-pane fade" id="orientation" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5>Orientation & Exposition</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Orientation Principale</label>
                            <select class="form-control" id="primary_orientation">
                                <option value="">-- Sélectionner --</option>
                                <option value="north" <?= ($orientation['primary_orientation'] ?? '') === 'north' ? 'selected' : '' ?>>Nord</option>
                                <option value="south" <?= ($orientation['primary_orientation'] ?? '') === 'south' ? 'selected' : '' ?>>Sud</option>
                                <option value="east" <?= ($orientation['primary_orientation'] ?? '') === 'east' ? 'selected' : '' ?>>Est</option>
                                <option value="west" <?= ($orientation['primary_orientation'] ?? '') === 'west' ? 'selected' : '' ?>>Ouest</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Exposition Soleil</label>
                            <select class="form-control" id="sun_exposure">
                                <option value="">-- Sélectionner --</option>
                                <option value="full_sun" <?= ($orientation['sun_exposure'] ?? '') === 'full_sun' ? 'selected' : '' ?>>Plein soleil</option>
                                <option value="partial" <?= ($orientation['sun_exposure'] ?? '') === 'partial' ? 'selected' : '' ?>>Partiel</option>
                                <option value="shaded" <?= ($orientation['sun_exposure'] ?? '') === 'shaded' ? 'selected' : '' ?>>Ombragé</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success" id="saveOrientationBtn">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- TAB: MÉDIA -->
    <?php if (in_array('media_extension', $enabledFeatures)): ?>
    <div class="tab-pane fade" id="media" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5>Médias Avancés</h5>
                
                <div class="form-group">
                    <label>Type de Fichier</label>
                    <select class="form-control" id="media_file_type">
                        <option value="floor_plan">Plan d'étage</option>
                        <option value="3d_render">Rendu 3D</option>
                        <option value="video_tour">Vidéo</option>
                        <option value="document">Document</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Fichier</label>
                    <input type="file" class="form-control-file" id="media_file" accept="image/*,video/*,application/pdf">
                </div>
                
                <button type="button" class="btn btn-primary" id="uploadMediaBtn">
                    <i class="fas fa-upload"></i> Upload
                </button>
                
                <hr>
                
                <div id="mediaList">
                    <?php if (!empty($mediaFiles)): ?>
                        <?php foreach ($mediaFiles as $media): ?>
                            <div class="media-item" data-id="<?= $media['id'] ?>">
                                <strong><?= ucfirst($media['file_type']) ?>:</strong> <?= $media['file_name'] ?>
                                <button class="btn btn-sm btn-danger delete-media" data-id="<?= $media['id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<script src="<?= base_url('assets/js/property-extended.js') ?>"></script>
