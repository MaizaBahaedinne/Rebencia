<!-- Étape 2: Localisation & Carte -->
<div class="row g-4">
    <!-- Zone -->
    <div class="col-md-4">
        <label for="zone_id" class="form-label">
            Zone / Délégation <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="zone_id" name="zone_id" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($zones as $zone): ?>
                <option value="<?= $zone['id'] ?>" <?= old('zone_id', $property['zone_id'] ?? '') == $zone['id'] ? 'selected' : '' ?>>
                    <?= esc($zone['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Gouvernorat -->
    <div class="col-md-4">
        <label for="governorate" class="form-label">
            Gouvernorat <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="governorate" 
               name="governorate" 
               value="<?= old('governorate', $property['governorate'] ?? '') ?>"
               placeholder="Ex: Tunis"
               required>
    </div>

    <!-- Ville -->
    <div class="col-md-4">
        <label for="city" class="form-label">
            Ville <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="city" 
               name="city" 
               value="<?= old('city', $property['city'] ?? '') ?>"
               placeholder="Ex: Ariana"
               required>
    </div>

    <!-- Quartier -->
    <div class="col-md-4">
        <label for="neighborhood" class="form-label">Quartier</label>
        <input type="text" 
               class="form-control" 
               id="neighborhood" 
               name="neighborhood" 
               value="<?= old('neighborhood', $property['neighborhood'] ?? '') ?>"
               placeholder="Ex: Centre ville">
    </div>

    <!-- Code postal -->
    <div class="col-md-4">
        <label for="postal_code" class="form-label">Code postal</label>
        <input type="text" 
               class="form-control" 
               id="postal_code" 
               name="postal_code" 
               value="<?= old('postal_code', $property['postal_code'] ?? '') ?>"
               placeholder="Ex: 2080">
    </div>

    <!-- Masquer l'adresse -->
    <div class="col-md-4">
        <label class="form-label d-block">&nbsp;</label>
        <div class="form-check form-switch">
            <input class="form-check-input" 
                   type="checkbox" 
                   id="hide_address" 
                   name="hide_address" 
                   value="1"
                   <?= old('hide_address', $property['hide_address'] ?? 0) ? 'checked' : '' ?>>
            <label class="form-check-label" for="hide_address">
                <i class="fas fa-eye-slash"></i> Masquer l'adresse exacte
            </label>
        </div>
    </div>

    <!-- Adresse complète -->
    <div class="col-md-12">
        <label for="address" class="form-label">
            Adresse complète <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" 
                  id="address" 
                  name="address" 
                  rows="2"
                  placeholder="Ex: 15 Avenue Habib Bourguiba"
                  required><?= old('address', $property['address'] ?? '') ?></textarea>
    </div>

    <!-- Carte interactive -->
    <div class="col-md-12">
        <label class="form-label">
            <i class="fas fa-map-marked-alt"></i> Géolocalisation
            <small class="text-muted">(Cliquez sur la carte pour positionner le marqueur)</small>
        </label>
        <div id="map"></div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="text" 
                       class="form-control" 
                       id="latitude" 
                       name="latitude" 
                       value="<?= old('latitude', $property['latitude'] ?? '') ?>"
                       readonly>
            </div>
            <div class="col-md-6">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="text" 
                       class="form-control" 
                       id="longitude" 
                       name="longitude" 
                       value="<?= old('longitude', $property['longitude'] ?? '') ?>"
                       readonly>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="geolocateMe()">
            <i class="fas fa-crosshairs"></i> Me géolocaliser
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="geocodeAddress()">
            <i class="fas fa-search-location"></i> Rechercher l'adresse
        </button>
    </div>
</div>

<script>
let map, marker;

function initMap() {
    const lat = parseFloat(document.getElementById('latitude').value) || 36.8065;
    const lng = parseFloat(document.getElementById('longitude').value) || 10.1815;
    
    map = L.map('map').setView([lat, lng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
    
    if (document.getElementById('latitude').value) {
        marker = L.marker([lat, lng], {draggable: true}).addTo(map);
        marker.on('dragend', function(e) {
            updateCoordinates(e.target.getLatLng());
        });
    }
    
    map.on('click', function(e) {
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, {draggable: true}).addTo(map);
            marker.on('dragend', function(e) {
                updateCoordinates(e.target.getLatLng());
            });
        }
        updateCoordinates(e.latlng);
    });
}

function updateCoordinates(latlng) {
    document.getElementById('latitude').value = latlng.lat.toFixed(8);
    document.getElementById('longitude').value = latlng.lng.toFixed(8);
}

function geolocateMe() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const latlng = L.latLng(position.coords.latitude, position.coords.longitude);
            map.setView(latlng, 15);
            if (marker) {
                marker.setLatLng(latlng);
            } else {
                marker = L.marker(latlng, {draggable: true}).addTo(map);
                marker.on('dragend', function(e) {
                    updateCoordinates(e.target.getLatLng());
                });
            }
            updateCoordinates(latlng);
        });
    }
}

function geocodeAddress() {
    const address = document.getElementById('address').value;
    const city = document.getElementById('city').value;
    const query = encodeURIComponent(`${address}, ${city}, Tunisia`);
    
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const latlng = L.latLng(data[0].lat, data[0].lon);
                map.setView(latlng, 16);
                if (marker) {
                    marker.setLatLng(latlng);
                } else {
                    marker = L.marker(latlng, {draggable: true}).addTo(map);
                    marker.on('dragend', function(e) {
                        updateCoordinates(e.target.getLatLng());
                    });
                }
                updateCoordinates(latlng);
            }
        });
}

// Initialiser la carte au chargement
setTimeout(initMap, 100);
</script>
