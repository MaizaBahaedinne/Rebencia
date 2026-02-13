<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marked-alt me-2"></i>Nouvelle Zone
        </h1>
        <a href="<?= base_url('admin/zones') ?>" class="btn btn-secondary">
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

    <form action="<?= base_url('admin/zones/store') ?>" method="post">
        <?= csrf_field() ?>
        
        <input type="hidden" id="boundary_coordinates" name="boundary_coordinates" value="">

        <div class="row">
            <!-- Carte Interactive -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow" style="height: 700px;">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-map me-2"></i>Carte - Cliquez pour positionner / Dessinez la zone</h6>
                    </div>
                    <div class="card-body p-0">
                        <div id="zoneMap" style="height: 100%; width: 100%;"></div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Instructions:</strong> Cliquez sur la carte pour définir le centre (lat/lng). 
                            Utilisez les outils de dessin pour tracer un polygone représentant la zone.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="col-lg-6"
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informations de la Zone</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= old('name') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="governorate" <?= old('type') === 'governorate' ? 'selected' : '' ?>>Gouvernorat</option>
                                    <option value="city" <?= old('type') === 'city' ? 'selected' : '' ?>>Ville</option>
                                    <option value="district" <?= old('type') === 'district' ? 'selected' : '' ?>>Délégation</option>
                                    <option value="area" <?= old('type') === 'area' ? 'selected' : '' ?>>Quartier</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="name_ar" class="form-label">Nom Arabe</label>
                                <input type="text" class="form-control" id="name_ar" name="name_ar" 
                                       value="<?= old('name_ar') ?>" dir="rtl">
                            </div>

                            <div class="col-md-6">
                                <label for="name_en" class="form-label">Nom Anglais</label>
                                <input type="text" class="form-control" id="name_en" name="name_en" 
                                       value="<?= old('name_en') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="parent_id" class="form-label">Zone Parente</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">-- Aucune (Niveau supérieur) --</option>
                                    <?php foreach ($parentZones as $parent): ?>
                                        <option value="<?= $parent['id'] ?>" <?= old('parent_id') == $parent['id'] ? 'selected' : '' ?>>
                                            <?= esc($parent['name']) ?> (<?= $parent['type'] ?>)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <small class="text-muted">Ex: Pour une ville, sélectionnez le gouvernorat</small>
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label">Pays</label>
                                <input type="text" class="form-control" id="country" name="country" 
                                       value="<?= old('country', 'Tunisia') ?>">
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
                                <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="latitude" name="latitude" 
                                       value="<?= old('latitude') ?>" placeholder="36.8065" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="longitude" name="longitude" 
                                       value="<?= old('longitude') ?>" placeholder="10.1815" readonly>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Cliquez sur la carte</strong> pour définir les coordonnées GPS du centre de la zone
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Options</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="popularity_score" class="form-label">Score de Popularité</label>
                            <input type="number" class="form-control" id="popularity_score" name="popularity_score" 
                                   value="<?= old('popularity_score', 0) ?>" min="0" max="100">
                            <small class="text-muted">De 0 à 100 (utilisé pour le tri)</small>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Créer la Zone
                        </button>
                        <a href="<?= base_url('admin/zones') ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Zone Tracée</h6>
                    </div>
                    <div class="card-body">
                        <div id="drawnZoneInfo">
                            <p class="text-muted text-center">
                                <i class="fas fa-draw-polygon" style="font-size: 48px;"></i><br>
                                Aucune zone tracée
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script>
let zoneMap;
let drawnItems;
let currentPolygon = null;
let centerMarker = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Tunisia
    zoneMap = L.map('zoneMap').setView([36.8065, 10.1815], 7);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(zoneMap);
    
    // Initialize FeatureGroup for drawn items
    drawnItems = new L.FeatureGroup();
    zoneMap.addLayer(drawnItems);
    
    // Initialize draw controls
    const drawControl = new L.Control.Draw({
        position: 'topright',
        draw: {
            polygon: {
                allowIntersection: false,
                shapeOptions: {
                    color: '#0d6efd',
                    fillOpacity: 0.3
                }
            },
            polyline: false,
            circle: false,
            circlemarker: false,
            rectangle: {
                shapeOptions: {
                    color: '#0d6efd',
                    fillOpacity: 0.3
                }
            },
            marker: false
        },
        edit: {
            featureGroup: drawnItems,
            remove: true
        }
    });
    zoneMap.addControl(drawControl);
    
    // Handle drawing created
    zoneMap.on(L.Draw.Event.CREATED, function(event) {
        const layer = event.layer;
        
        // Remove previous polygon if exists
        if (currentPolygon) {
            drawnItems.removeLayer(currentPolygon);
        }
        
        currentPolygon = layer;
        drawnItems.addLayer(layer);
        
        // Get coordinates
        const coordinates = layer.getLatLngs()[0].map(latlng => [latlng.lat, latlng.lng]);
        
        // Save to hidden field as JSON
        document.getElementById('boundary_coordinates').value = JSON.stringify(coordinates);
        
        // Update info display
        updateZoneInfo(coordinates);
    });
    
    // Handle drawing edited
    zoneMap.on(L.Draw.Event.EDITED, function(event) {
        const layers = event.layers;
        layers.eachLayer(function(layer) {
            const coordinates = layer.getLatLngs()[0].map(latlng => [latlng.lat, latlng.lng]);
            document.getElementById('boundary_coordinates').value = JSON.stringify(coordinates);
            updateZoneInfo(coordinates);
        });
    });
    
    // Handle drawing deleted
    zoneMap.on(L.Draw.Event.DELETED, function() {
        currentPolygon = null;
        document.getElementById('boundary_coordinates').value = '';
        updateZoneInfo(null);
    });
    
    // Click on map to set center point (latitude/longitude)
    zoneMap.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);
        
        // Update input fields
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        
        // Remove previous marker if exists
        if (centerMarker) {
            zoneMap.removeLayer(centerMarker);
        }
        
        // Add new marker
        centerMarker = L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'center-marker',
                html: '<i class="fas fa-map-pin" style="color: red; font-size: 24px;"></i>',
                iconSize: [24, 24],
                iconAnchor: [12, 24]
            })
        }).addTo(zoneMap);
        
        centerMarker.bindPopup(`<strong>Centre de la zone</strong><br>Lat: ${lat}<br>Lng: ${lng}`).openPopup();
    });
});

function updateZoneInfo(coordinates) {
    const infoDiv = document.getElementById('drawnZoneInfo');
    
    if (!coordinates || coordinates.length === 0) {
        infoDiv.innerHTML = `
            <p class="text-muted text-center">
                <i class="fas fa-draw-polygon" style="font-size: 48px;"></i><br>
                Aucune zone tracée
            </p>
        `;
        return;
    }
    
    // Calculate area (approximate)
    const area = calculatePolygonArea(coordinates);
    
    infoDiv.innerHTML = `
        <div class="alert alert-success">
            <h6><i class="fas fa-check-circle me-2"></i>Zone tracée avec succès!</h6>
            <p class="mb-0">
                <strong>Points:</strong> ${coordinates.length}<br>
                <strong>Superficie:</strong> ~${area.toFixed(2)} km²
            </p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="clearDrawing()">
            <i class="fas fa-trash me-1"></i>Effacer le tracé
        </button>
    `;
}

function calculatePolygonArea(coordinates) {
    // Simple area calculation (Haversine approximation)
    let area = 0;
    const earthRadius = 6371; // km
    
    for (let i = 0; i < coordinates.length; i++) {
        const j = (i + 1) % coordinates.length;
        const lat1 = coordinates[i][0] * Math.PI / 180;
        const lat2 = coordinates[j][0] * Math.PI / 180;
        const lng1 = coordinates[i][1] * Math.PI / 180;
        const lng2 = coordinates[j][1] * Math.PI / 180;
        
        area += (lng2 - lng1) * (2 + Math.sin(lat1) + Math.sin(lat2));
    }
    
    area = Math.abs(area * earthRadius * earthRadius / 2);
    return area;
}

function clearDrawing() {
    if (currentPolygon) {
        drawnItems.removeLayer(currentPolygon);
        currentPolygon = null;
        document.getElementById('boundary_coordinates').value = '';
        updateZoneInfo(null);
    }
}
</script>
<?= $this->endSection() ?>