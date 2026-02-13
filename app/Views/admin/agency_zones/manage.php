<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('admin/agency-zones') ?>" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-map-marked-alt me-2"></i>Gestion des Zones
            </h1>
            <p class="text-muted mb-0">
                <strong><?= esc($agency['name']) ?></strong> (<?= esc($agency['code']) ?>)
            </p>
        </div>
        <button type="button" class="btn btn-success" id="saveZones">
            <i class="fas fa-save me-2"></i>Enregistrer
        </button>
    </div>

    <div class="row">
        <!-- Carte interactive -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-map me-2"></i>Carte Interactive</h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-light" id="drawPolygon" title="Dessiner une zone">
                                <i class="fas fa-draw-polygon"></i> Dessiner
                            </button>
                            <button type="button" class="btn btn-light" id="clearDrawing" title="Effacer le dessin">
                                <i class="fas fa-eraser"></i> Effacer
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 600px; width: 100%;"></div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Cliquez sur "Dessiner" puis dessinez un polygone sur la carte pour définir la zone de couverture
                    </small>
                </div>
            </div>
        </div>

        <!-- Liste des zones -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Zones Disponibles</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchZone" placeholder="Rechercher une zone...">
                    </div>
                    
                    <div id="zonesList">
                        <?php 
                        $assignedZoneIds = array_column($assignedZones, 'zone_id');
                        $zonesByType = [];
                        foreach ($allZones as $zone) {
                            $type = $zone['type'];
                            if (!isset($zonesByType[$type])) {
                                $zonesByType[$type] = [];
                            }
                            $zonesByType[$type][] = $zone;
                        }
                        
                        $typeLabels = [
                            'governorate' => 'Gouvernorats',
                            'city' => 'Villes',
                            'district' => 'Quartiers',
                            'area' => 'Zones'
                        ];
                        ?>
                        
                        <?php foreach ($typeLabels as $type => $label): ?>
                            <?php if (isset($zonesByType[$type])): ?>
                                <div class="zone-type-section mb-3">
                                    <h6 class="text-uppercase text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i><?= $label ?>
                                    </h6>
                                    <?php foreach ($zonesByType[$type] as $zone): ?>
                                        <?php 
                                        $isAssigned = in_array($zone['id'], $assignedZoneIds);
                                        $assignedData = null;
                                        if ($isAssigned) {
                                            foreach ($assignedZones as $az) {
                                                if ($az['zone_id'] == $zone['id']) {
                                                    $assignedData = $az;
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="form-check mb-2 zone-item" data-zone-name="<?= esc(strtolower($zone['name'])) ?>">
                                            <input class="form-check-input zone-checkbox" 
                                                   type="checkbox" 
                                                   value="<?= $zone['id'] ?>"
                                                   id="zone_<?= $zone['id'] ?>"
                                                   data-zone-id="<?= $zone['id'] ?>"
                                                   data-zone-name="<?= esc($zone['name']) ?>"
                                                   data-latitude="<?= $zone['latitude'] ?>"
                                                   data-longitude="<?= $zone['longitude'] ?>"
                                                   data-coordinates='<?= $assignedData['boundary_coordinates'] ?? '' ?>'
                                                   <?= $isAssigned ? 'checked' : '' ?>>
                                            <label class="form-check-label d-flex justify-content-between align-items-center w-100" 
                                                   for="zone_<?= $zone['id'] ?>">
                                                <span><?= esc($zone['name']) ?></span>
                                                <?php if ($isAssigned && $assignedData && $assignedData['is_primary']): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Zones sélectionnées -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Zones Sélectionnées (<span id="selectedCount">0</span>)</h6>
                </div>
                <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                    <div id="selectedZones" class="small">
                        <p class="text-muted mb-0">Aucune zone sélectionnée</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<style>
    .zone-item {
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    .zone-item:hover {
        background-color: #f8f9fa;
    }
    .zone-checkbox:checked + label {
        font-weight: 600;
        color: #0d6efd;
    }
    .leaflet-draw-toolbar {
        display: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
const agencyId = <?= $agency['id'] ?>;
let map;
let drawnItems;
let drawControl;
let currentPolygon = null;
let zonePolygons = {};

// Initialize map
function initMap() {
    // Center on Tunisia
    map = L.map('map').setView([36.8065, 10.1815], 7);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
    // FeatureGroup for drawn items
    drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);
    
    // Draw control
    drawControl = new L.Control.Draw({
        draw: {
            polygon: {
                allowIntersection: false,
                shapeOptions: {
                    color: '#3388ff',
                    weight: 2,
                    fillOpacity: 0.3
                }
            },
            polyline: false,
            circle: false,
            rectangle: false,
            marker: false,
            circlemarker: false
        },
        edit: {
            featureGroup: drawnItems,
            remove: true
        }
    });
    
    // Load existing zones
    loadExistingZones();
    
    // Map events
    map.on(L.Draw.Event.CREATED, function (event) {
        const layer = event.layer;
        drawnItems.addLayer(layer);
        currentPolygon = layer;
        
        // Prompt for zone name
        promptZoneSelection(layer);
    });
    
    map.on(L.Draw.Event.EDITED, function (event) {
        event.layers.eachLayer(function (layer) {
            updateZoneCoordinates(layer);
        });
    });
    
    map.on(L.Draw.Event.DELETED, function (event) {
        event.layers.eachLayer(function (layer) {
            const zoneId = layer.options.zoneId;
            if (zoneId) {
                delete zonePolygons[zoneId];
                $(`#zone_${zoneId}`).prop('checked', false);
            }
        });
        updateSelectedZones();
    });
}

// Load existing assigned zones
function loadExistingZones() {
    <?php foreach ($assignedZones as $zone): ?>
        <?php if (!empty($zone['boundary_coordinates'])): ?>
            const coords<?= $zone['zone_id'] ?> = <?= $zone['boundary_coordinates'] ?>;
            if (coords<?= $zone['zone_id'] ?> && coords<?= $zone['zone_id'] ?>.length > 0) {
                const polygon = L.polygon(coords<?= $zone['zone_id'] ?>, {
                    color: '<?= $zone['is_primary'] ? '#28a745' : '#3388ff' ?>',
                    weight: 2,
                    fillOpacity: 0.3,
                    zoneId: <?= $zone['zone_id'] ?>
                }).addTo(drawnItems);
                
                polygon.bindPopup('<strong><?= esc($zone['name']) ?></strong>');
                zonePolygons[<?= $zone['zone_id'] ?>] = polygon;
            }
        <?php endif; ?>
    <?php endforeach; ?>
}

// Enable drawing
$('#drawPolygon').click(function() {
    new L.Draw.Polygon(map, drawControl.options.draw.polygon).enable();
});

// Clear all drawings
$('#clearDrawing').click(function() {
    if (confirm('Voulez-vous vraiment effacer tous les dessins ?')) {
        drawnItems.clearLayers();
        zonePolygons = {};
        $('.zone-checkbox').prop('checked', false);
        updateSelectedZones();
    }
});

// Prompt zone selection for drawn polygon
function promptZoneSelection(layer) {
    const zoneName = prompt('Entrez le nom de la zone ou sélectionnez dans la liste de droite:');
    if (!zoneName) {
        drawnItems.removeLayer(layer);
        return;
    }
    
    layer.bindPopup(zoneName);
}

// Zone checkbox change
$('.zone-checkbox').change(function() {
    const zoneId = $(this).data('zone-id');
    const zoneName = $(this).data('zone-name');
    const lat = parseFloat($(this).data('latitude'));
    const lon = parseFloat($(this).data('longitude'));
    const coords = $(this).data('coordinates');
    
    if ($(this).is(':checked')) {
        // Add marker or zoom to zone
        if (lat && lon) {
            map.setView([lat, lon], 12);
        }
        
        // If has coordinates, draw polygon
        if (coords && coords.length > 0) {
            try {
                const polygon = L.polygon(coords, {
                    color: '#3388ff',
                    weight: 2,
                    fillOpacity: 0.3,
                    zoneId: zoneId
                }).addTo(drawnItems);
                
                polygon.bindPopup(`<strong>${zoneName}</strong>`);
                zonePolygons[zoneId] = polygon;
            } catch (e) {
                console.error('Error drawing polygon:', e);
            }
        }
    } else {
        // Remove from map
        if (zonePolygons[zoneId]) {
            drawnItems.removeLayer(zonePolygons[zoneId]);
            delete zonePolygons[zoneId];
        }
    }
    
    updateSelectedZones();
});

// Update selected zones display
function updateSelectedZones() {
    const checked = $('.zone-checkbox:checked');
    const count = checked.length;
    $('#selectedCount').text(count);
    
    if (count === 0) {
        $('#selectedZones').html('<p class="text-muted mb-0">Aucune zone sélectionnée</p>');
    } else {
        let html = '';
        checked.each(function() {
            const name = $(this).data('zone-name');
            const id = $(this).data('zone-id');
            html += `<span class="badge bg-primary me-1 mb-1">${name}</span>`;
        });
        $('#selectedZones').html(html);
    }
}

// Search zones
$('#searchZone').on('keyup', function() {
    const searchText = $(this).val().toLowerCase();
    $('.zone-item').each(function() {
        const zoneName = $(this).data('zone-name');
        if (zoneName.includes(searchText)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

// Save zones
$('#saveZones').click(function() {
    const zones = [];
    
    $('.zone-checkbox:checked').each(function() {
        const zoneId = $(this).data('zone-id');
        let coordinates = null;
        
        // Get polygon coordinates if drawn
        if (zonePolygons[zoneId]) {
            const latLngs = zonePolygons[zoneId].getLatLngs()[0];
            coordinates = latLngs.map(ll => [ll.lat, ll.lng]);
        }
        
        zones.push({
            zone_id: zoneId,
            coordinates: coordinates ? JSON.stringify(coordinates) : null,
            is_primary: 0 // Could add UI for this
        });
    });
    
    if (zones.length === 0) {
        alert('Veuillez sélectionner au moins une zone');
        return;
    }
    
    // Save via AJAX
    $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...');
    
    $.ajax({
        url: '<?= base_url('admin/agency-zones/save/') ?>' + agencyId,
        method: 'POST',
        data: { zones: zones },
        success: function(response) {
            if (response.success) {
                alert('Zones enregistrées avec succès');
                window.location.href = '<?= base_url('admin/agency-zones') ?>';
            } else {
                alert('Erreur: ' + response.message);
                $('#saveZones').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Enregistrer');
            }
        },
        error: function() {
            alert('Erreur de connexion');
            $('#saveZones').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Enregistrer');
        }
    });
});

// Initialize on load
$(document).ready(function() {
    initMap();
    updateSelectedZones();
});
</script>
<?= $this->endSection() ?>
