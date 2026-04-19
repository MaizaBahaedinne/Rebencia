<!-- FORMULAIRE BIEN IMMOBILIER -->
<?php
$isEdit      = ! empty($property['id']);
// Décoder les valeurs features existantes si en édition
$featValues  = [];
if ($isEdit && ! empty($property['features'])) {
    $featValues = json_decode($property['features'], true) ?? [];
}
$characteristics = $characteristics ?? [];
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/properties') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold"><?= $isEdit ? 'Modifier le bien' : 'Nouveau bien immobilier' ?></h4>
        <?php if ($isEdit) : ?>
        <p class="text-muted mb-0"><?= esc($property['reference']) ?></p>
        <?php endif; ?>
    </div>
</div>

<form method="POST"
      action="<?= $isEdit ? base_url('admin/properties/' . $property['id'] . '/update') : base_url('admin/properties/store') ?>"
      enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Colonne principale -->
        <div class="col-12 col-lg-8">

            <!-- Informations générales -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-info-circle text-primary me-2"></i>Informations générales
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Titre de l'annonce <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               value="<?= esc(old('title', $property['title'] ?? '')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= esc(old('description', $property['description'] ?? '')) ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type de bien <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <?php foreach (['apartment'=>'Appartement','house'=>'Maison','villa'=>'Villa','commercial'=>'Commercial','land'=>'Terrain','office'=>'Bureau'] as $v => $l) : ?>
                                <option value="<?= $v ?>" <?= old('type', $property['type'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Transaction <span class="text-danger">*</span></label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="sale" <?= old('transaction_type', $property['transaction_type'] ?? 'sale') === 'sale' ? 'selected' : '' ?>>Vente</option>
                                <option value="rent" <?= old('transaction_type', $property['transaction_type'] ?? '') === 'rent' ? 'selected' : '' ?>>Location</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="status" class="form-select">
                                <?php foreach (['available'=>'Disponible','reserved'=>'Réservé','sold'=>'Vendu','rented'=>'Loué','inactive'=>'Inactif'] as $v => $l) : ?>
                                <option value="<?= $v ?>" <?= old('status', $property['status'] ?? 'available') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Caractéristiques -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-rulers text-success me-2"></i>Caractéristiques
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prix (TND) <span class="text-danger">*</span></label>
                            <input type="number" name="price" step="100" class="form-control"
                                   value="<?= old('price', $property['price'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Surface (m²)</label>
                            <input type="number" name="surface" step="0.5" class="form-control"
                                   value="<?= old('surface', $property['surface'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pièces</label>
                            <input type="number" name="rooms" min="0" class="form-control"
                                   value="<?= old('rooms', $property['rooms'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Chambres</label>
                            <input type="number" name="bedrooms" min="0" class="form-control"
                                   value="<?= old('bedrooms', $property['bedrooms'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">SDB</label>
                            <input type="number" name="bathrooms" min="0" class="form-control"
                                   value="<?= old('bathrooms', $property['bathrooms'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Étage</label>
                            <input type="number" name="floor" class="form-control"
                                   value="<?= old('floor', $property['floor'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Total étages</label>
                            <input type="number" name="total_floors" min="0" class="form-control"
                                   value="<?= old('total_floors', $property['total_floors'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input type="checkbox" name="parking" id="parking" class="form-check-input"
                                       value="1" <?= old('parking', $property['parking'] ?? 0) ? 'checked' : '' ?>>
                                <label for="parking" class="form-check-label">Parking</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input type="checkbox" name="furnished" id="furnished" class="form-check-input"
                                       value="1" <?= old('furnished', $property['furnished'] ?? 0) ? 'checked' : '' ?>>
                                <label for="furnished" class="form-check-label">Meublé</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Équipements & caractéristiques dynamiques -->
            <?php if (! empty($characteristics)) : ?>
            <div class="card shadow-sm mb-3" id="card-features">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-tags text-info me-2"></i>Équipements &amp; caractéristiques
                    <small class="text-muted ms-2 fw-normal">— chargés depuis le catalogue</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                    <?php
                    foreach ($characteristics as $ch) :
                        $k          = $ch['key'];
                        $lbl        = esc($ch['label']);
                        $icon       = esc($ch['icon']);
                        $unit       = esc($ch['unit'] ?? '');
                        $currentVal = $featValues[$k] ?? null;
                        $idAttr     = 'feat_' . $k;
                        $nameAttr   = 'feat[' . $k . ']';
                        $isRequired = false;
                        if ($ch['required_for']) {
                            $reqTypes = json_decode($ch['required_for'], true) ?? [];
                            $isRequired = in_array($property['type'] ?? '', $reqTypes, true);
                        }
                        $reqAttr = $isRequired ? 'required' : '';
                    ?>
                    <?php if ($ch['type'] === 'boolean') : ?>
                        <div class="col-6 col-md-4 col-xl-3 d-flex align-items-center gap-2 pt-1">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox"
                                       id="<?= $idAttr ?>"
                                       name="<?= $nameAttr ?>"
                                       value="1"
                                       <?= $currentVal == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="<?= $idAttr ?>">
                                    <i class="bi <?= $icon ?> me-1 text-muted small"></i><?= $lbl ?>
                                    <?php if ($isRequired) : ?><span class="text-danger">*</span><?php endif; ?>
                                </label>
                            </div>
                        </div>

                    <?php elseif ($ch['type'] === 'number') : ?>
                        <div class="col-6 col-md-4 col-xl-3">
                            <label class="form-label fw-semibold small" for="<?= $idAttr ?>">
                                <i class="bi <?= $icon ?> me-1 text-muted"></i><?= $lbl ?>
                                <?php if ($isRequired) : ?><span class="text-danger">*</span><?php endif; ?>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control form-control-sm"
                                       id="<?= $idAttr ?>" name="<?= $nameAttr ?>"
                                       value="<?= esc($currentVal ?? '') ?>"
                                       min="0" step="1" <?= $reqAttr ?>>
                                <?php if ($unit) : ?><span class="input-group-text"><?= $unit ?></span><?php endif; ?>
                            </div>
                        </div>

                    <?php elseif ($ch['type'] === 'text') : ?>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold small" for="<?= $idAttr ?>">
                                <i class="bi <?= $icon ?> me-1 text-muted"></i><?= $lbl ?>
                                <?php if ($isRequired) : ?><span class="text-danger">*</span><?php endif; ?>
                            </label>
                            <input type="text" class="form-control form-control-sm"
                                   id="<?= $idAttr ?>" name="<?= $nameAttr ?>"
                                   value="<?= esc($currentVal ?? '') ?>"
                                   <?= $reqAttr ?>>
                        </div>

                    <?php elseif ($ch['type'] === 'select') : ?>
                        <?php $opts = $ch['options'] ? json_decode($ch['options'], true) : []; ?>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold small" for="<?= $idAttr ?>">
                                <i class="bi <?= $icon ?> me-1 text-muted"></i><?= $lbl ?>
                                <?php if ($isRequired) : ?><span class="text-danger">*</span><?php endif; ?>
                            </label>
                            <select class="form-select form-select-sm"
                                    id="<?= $idAttr ?>" name="<?= $nameAttr ?>" <?= $reqAttr ?>>
                                <option value="">— Choisir —</option>
                                <?php foreach ($opts as $opt) : ?>
                                <option value="<?= esc($opt) ?>" <?= $currentVal === $opt ? 'selected' : '' ?>>
                                    <?= esc($opt) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    </div><!-- /row -->
                </div>
            </div>
            <?php endif; ?>

            <!-- Localisation -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-geo-alt text-danger me-2"></i>Localisation
                </div>
                <div class="card-body">

                    <?php
                    // Pour l'édition : préparer les données de pré-sélection
                    $vp        = $ville_preselect ?? null;  // zone ville trouvée
                    $vpId      = $vp ? $vp['id'] : null;
                    $vpName    = $vp ? $vp['name'] : null;
                    // On ne peut pas facilement déterminer région/pays depuis la ville en PHP ici,
                    // le JS les charge dynamiquement via AJAX après le rendu.
                    ?>

                    <!-- Sélecteurs en cascade -->
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small">Pays</label>
                            <select id="sel_pays" class="form-select form-select-sm">
                                <option value="">— Pays —</option>
                                <?php foreach ($pays_list ?? [] as $p) : ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small">Région</label>
                            <select id="sel_region" class="form-select form-select-sm" disabled>
                                <option value="">— Région —</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small">Ville <span class="text-danger">*</span></label>
                            <select id="sel_ville" class="form-select form-select-sm" disabled>
                                <option value="">— Ville —</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small">Quartier</label>
                            <select id="sel_quartier" class="form-select form-select-sm" disabled>
                                <option value="">— Quartier —</option>
                            </select>
                        </div>
                    </div>

                    <!-- Adresse + code postal (champs visibles) -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold small">Adresse précise</label>
                            <input type="text" name="address" id="inp_address" class="form-control form-control-sm"
                                   placeholder="Ex : 12 Avenue Habib Bourguiba"
                                   value="<?= esc(old('address', $property['address'] ?? '')) ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Code postal</label>
                            <input type="text" id="inp_postal" class="form-control form-control-sm bg-light"
                                   readonly placeholder="Auto depuis le quartier"
                                   value="">
                        </div>
                    </div>

                    <!-- Champs cachés soumis avec le formulaire -->
                    <input type="hidden" name="city" id="inp_city"
                           value="<?= esc(old('city', $property['city'] ?? '')) ?>">
                    <input type="hidden" name="zone" id="inp_zone"
                           value="<?= esc(old('zone', $property['zone'] ?? '')) ?>">

                    <!-- Carte + coordonnées GPS -->
                    <div class="row g-3">
                        <div class="col-12 col-md-5 d-flex flex-column gap-2">
                            <label class="form-label fw-semibold small mb-0">Coordonnées GPS</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Lat</span>
                                        <input type="text" name="latitude" id="inp_lat" class="form-control"
                                               value="<?= esc(old('latitude', $property['latitude'] ?? '')) ?>"
                                               placeholder="36.8189">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Lng</span>
                                        <input type="text" name="longitude" id="inp_lng" class="form-control"
                                               value="<?= esc(old('longitude', $property['longitude'] ?? '')) ?>"
                                               placeholder="10.1658">
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted" style="font-size:.78rem;">
                                <i class="bi bi-info-circle me-1"></i>Cliquer sur la carte ou glisser le marqueur.
                            </div>
                            <button type="button" id="btn_geocode" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-search me-1"></i>Géolocaliser depuis l'adresse/zone
                            </button>
                            <button type="button" id="btn_reset_map" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-crosshair me-1"></i>Centrer sur la Tunisie
                            </button>
                        </div>
                        <div class="col-12 col-md-7">
                            <div id="property-map" style="height:300px;border-radius:.5rem;border:1px solid #dee2e6;z-index:0;"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Données de pré-sélection pour le JS (édition) -->
            <script>
            window.PROP_LOC = {
                villeId:   <?= json_encode($vpId) ?>,
                villeName: <?= json_encode($vpName) ?>,
                city:      <?= json_encode(old('city',  $property['city']  ?? '')) ?>,
                zone:      <?= json_encode(old('zone',  $property['zone']  ?? '')) ?>,
                lat:       <?= json_encode(old('latitude',  $property['latitude']  ?? '')) ?>,
                lng:       <?= json_encode(old('longitude', $property['longitude'] ?? '')) ?>,
                childrenUrl: <?= json_encode(base_url('admin/zones/')) ?>
            };
            </script>

            <!-- Images -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-images text-warning me-2"></i>Photos
                </div>
                <div class="card-body">
                    <?php if (! empty($property['images'])) : ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php foreach ($property['images'] as $img) : ?>
                        <div class="position-relative">
                            <img src="<?= base_url($img['path']) ?>" class="rounded"
                                 style="width:80px;height:70px;object-fit:cover;">
                            <?php if ($img['is_primary']) : ?>
                            <span class="position-absolute top-0 start-0 badge bg-primary" style="font-size:.6rem;">Principale</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPEG, PNG, WebP. Première image = image principale.</div>
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-person-badge text-primary me-2"></i>Agent responsable
                </div>
                <div class="card-body">
                    <select name="agent_id" class="form-select" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($agents as $agent) : ?>
                        <option value="<?= $agent['id'] ?>"
                            <?= old('agent_id', $property['agent_id'] ?? '') == $agent['id'] ? 'selected' : '' ?>>
                            <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-lg me-1"></i>
                        <?= $isEdit ? 'Enregistrer' : 'Créer le bien' ?>
                    </button>
                    <a href="<?= base_url('admin/properties') ?>" class="btn btn-outline-secondary w-100">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- ── LEAFLET ──────────────────────────────────────────────────────── -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
(function () {
    'use strict';

    const LOC      = window.PROP_LOC;
    const BASE_URL = LOC.childrenUrl;   // ex: https://…/admin/zones/

    // ── Sélecteurs DOM ──────────────────────────────────────────────
    const selPays     = document.getElementById('sel_pays');
    const selRegion   = document.getElementById('sel_region');
    const selVille    = document.getElementById('sel_ville');
    const selQuartier = document.getElementById('sel_quartier');
    const inpCity     = document.getElementById('inp_city');    // hidden
    const inpZone     = document.getElementById('inp_zone');    // hidden
    const inpPostal   = document.getElementById('inp_postal');  // lecture seule visible
    const inpAddress  = document.getElementById('inp_address');
    const inpLat      = document.getElementById('inp_lat');
    const inpLng      = document.getElementById('inp_lng');
    const btnGeocode  = document.getElementById('btn_geocode');
    const btnReset    = document.getElementById('btn_reset_map');

    // ── Carte Leaflet ────────────────────────────────────────────────
    const DEFAULT = [33.886917, 9.537499];   // centre Tunisie
    const ZOOM_COUNTRY = 6;
    const ZOOM_CITY    = 13;

    let initLat = parseFloat(LOC.lat) || null;
    let initLng = parseFloat(LOC.lng) || null;

    const map = L.map('property-map').setView(
        (initLat && initLng) ? [initLat, initLng] : DEFAULT,
        (initLat && initLng) ? ZOOM_CITY : ZOOM_COUNTRY
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    // Marqueur draggable
    const markerIcon = L.icon({
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        iconAnchor: [12, 41], popupAnchor: [1, -34]
    });

    let marker = null;

    function placeMarker(lat, lng, panTo = true) {
        lat = parseFloat(lat); lng = parseFloat(lng);
        if (isNaN(lat) || isNaN(lng)) return;
        inpLat.value = lat.toFixed(6);
        inpLng.value = lng.toFixed(6);
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true, icon: markerIcon })
                .addTo(map)
                .bindPopup('<strong>Position du bien</strong><br>Glisser pour ajuster.')
                .openPopup();
            marker.on('dragend', () => {
                const p = marker.getLatLng();
                inpLat.value = p.lat.toFixed(6);
                inpLng.value = p.lng.toFixed(6);
            });
        }
        if (panTo) map.setView([lat, lng], Math.max(map.getZoom(), ZOOM_CITY));
    }

    // Clic sur la carte → placer le marqueur
    map.on('click', (e) => placeMarker(e.latlng.lat, e.latlng.lng));

    // Si coordonnées existantes (mode édition)
    if (initLat && initLng) placeMarker(initLat, initLng, false);

    // ── Géocodage Nominatim ─────────────────────────────────────────
    function geocode(query) {
        if (! query.trim()) return;
        const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query);
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data && data.length > 0) {
                    placeMarker(data[0].lat, data[0].lon);
                } else {
                    alert('Aucun résultat pour : ' + query);
                }
            })
            .catch(() => alert('Erreur de géocodage.'));
    }

    btnGeocode.addEventListener('click', () => {
        const city    = inpCity.value  || selVille.options[selVille.selectedIndex]?.text.split(' (')[0] || '';
        const quartier = inpZone.value || selQuartier.options[selQuartier.selectedIndex]?.text.split(' (')[0] || '';
        const parts   = [inpAddress.value, quartier, city].filter(Boolean).join(', ');
        geocode(parts || 'Tunisie');
    });

    btnReset.addEventListener('click', () => {
        map.setView(DEFAULT, ZOOM_COUNTRY);
    });

    // Recalibrer la carte quand elle devient visible (layout responsive)
    setTimeout(() => map.invalidateSize(), 300);

    // ── Chargement enfants via AJAX ─────────────────────────────────
    function loadChildren(parentId, targetSelect, placeholder) {
        targetSelect.innerHTML = '<option value="">Chargement…</option>';
        targetSelect.disabled = true;

        fetch(BASE_URL + parentId + '/children', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            targetSelect.innerHTML = '<option value="">' + placeholder + '</option>';
            data.forEach(z => {
                const opt = document.createElement('option');
                opt.value = z.id;
                opt.textContent = z.name + (z.code ? ' (' + z.code + ')' : '');
                targetSelect.appendChild(opt);
            });
            targetSelect.disabled = data.length === 0;
        })
        .catch(() => {
            targetSelect.innerHTML = '<option value="">Erreur de chargement</option>';
        });
    }

    // ── Cascade Pays → Région ───────────────────────────────────────
    selPays.addEventListener('change', () => {
        const id = selPays.value;
        // Reset en cascade
        selRegion.innerHTML   = '<option value="">— Région —</option>';
        selRegion.disabled    = true;
        selVille.innerHTML    = '<option value="">— Ville —</option>';
        selVille.disabled     = true;
        selQuartier.innerHTML = '<option value="">— Quartier —</option>';
        selQuartier.disabled  = true;
        inpCity.value = '';
        inpZone.value = '';

        if (id) loadChildren(id, selRegion, '— Région —');
    });

    // ── Cascade Région → Ville ──────────────────────────────────────
    selRegion.addEventListener('change', () => {
        const id = selRegion.value;
        selVille.innerHTML    = '<option value="">— Ville —</option>';
        selVille.disabled     = true;
        selQuartier.innerHTML = '<option value="">— Quartier —</option>';
        selQuartier.disabled  = true;
        inpCity.value = '';
        inpZone.value = '';

        if (id) loadChildren(id, selVille, '— Ville —');
    });

    // ── Cascade Ville → Quartier + geocode ─────────────────────────
    selVille.addEventListener('change', () => {
        const id   = selVille.value;
        const name = selVille.options[selVille.selectedIndex]?.text.split(' (')[0] || '';
        selQuartier.innerHTML = '<option value="">— Quartier —</option>';
        selQuartier.disabled  = true;
        inpZone.value = '';

        if (id) {
            inpCity.value = name;
            loadChildren(id, selQuartier, '— Quartier —');
            // Géocoder la ville automatiquement
            geocode(name + ', Tunisie');
        } else {
            inpCity.value = '';
        }
    });

    // ── Sélection quartier ──────────────────────────────────────────
    selQuartier.addEventListener('change', () => {
        const sel  = selQuartier.options[selQuartier.selectedIndex];
        if (! selQuartier.value) {
            inpZone.value   = '';
            inpPostal.value = '';
            return;
        }
        // Le texte est "Nom (CODE)" — on sépare
        const fullText = sel.textContent.trim();
        const match    = fullText.match(/^(.+?)\s*\(([^)]+)\)\s*$/);
        const name     = match ? match[1].trim() : fullText;
        const code     = match ? match[2].trim() : '';
        inpZone.value   = name;
        inpPostal.value = code;
        if (inpCity.value) {
            geocode(name + ' ' + inpCity.value + ', Tunisie');
        }
    });

    // ── Pré-sélection en mode édition ───────────────────────────────
    // Les champs city et zone sont déjà dans les inputs hidden via PHP.
    // On pré-remplit le champ code postal si on a une zone avec code.
    if (LOC.zone && LOC.zone.includes('(')) {
        const m = LOC.zone.match(/^(.+?)\s*\(([^)]+)\)\s*$/);
        if (m) inpPostal.value = m[2];
    }

    // ── Saisie manuelle des coords ──────────────────────────────────
    [inpLat, inpLng].forEach(inp => {
        inp.addEventListener('change', () => {
            const lat = inpLat.value;
            const lng = inpLng.value;
            if (lat && lng) placeMarker(lat, lng);
        });
    });

})();
</script>
