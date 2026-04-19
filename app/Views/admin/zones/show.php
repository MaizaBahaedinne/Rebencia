<?php
$perms    = session()->get('permissions') ?? [];
$meta     = $typeMeta[$zone['type']] ?? ['label' => $zone['type'], 'icon' => 'bi-geo-alt', 'color' => 'secondary'];

// Construire le fil d'Ariane (pays › région › ville ›  actuel)
$breadcrumb = array_filter([
    $chain['pays']   ?? null,
    $chain['region'] ?? null,
    $chain['ville']  ?? null,
]);
?>

<!-- ── EN-TÊTE ─────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/zones?tab=' . $zone['type']) ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi <?= $meta['icon'] ?> me-2 text-<?= $meta['color'] ?>"></i>
            <?= esc($zone['name']) ?>
        </h4>
        <p class="text-muted mb-0 small"><?= esc($meta['label']) ?></p>
    </div>
    <!-- Actions rapides -->
    <div class="ms-auto d-flex gap-2">
        <?php if (in_array('zones.edit', $perms)): ?>
        <a href="<?= base_url('admin/zones/' . $zone['id'] . '/edit') ?>"
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <?php endif; ?>
        <?php if (in_array('zones.create', $perms)): ?>
        <a href="<?= base_url('admin/zones/create/' . $zone['type']) ?>"
           class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nouveau(elle) <?= esc($meta['label']) ?>
        </a>
        <?php endif; ?>
    </div>
</div>


<div class="row g-4">

    <!-- ── INFORMATIONS ────────────────────────────────────────────── -->
    <div class="col-md-5">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-info-circle me-1"></i> Informations
            </div>
            <div class="card-body">

                <!-- Fil d'Ariane hiérarchique -->
                <?php if (! empty($breadcrumb)): ?>
                <div class="mb-3 p-2 bg-light rounded small">
                    <i class="bi bi-diagram-3 me-1 text-muted"></i>
                    <?php
                    $parts = [];
                    foreach ($breadcrumb as $ancestor) {
                        $aMeta  = $typeMeta[$ancestor['type']] ?? ['color' => 'secondary', 'icon' => 'bi-geo'];
                        $parts[] = '<span class="text-' . $aMeta['color'] . '">'
                                 . '<i class="bi ' . $aMeta['icon'] . ' me-1"></i>'
                                 . esc($ancestor['name']) . '</span>';
                    }
                    echo implode(' <i class="bi bi-chevron-right text-muted mx-1 small"></i> ', $parts);
                    ?>
                    <i class="bi bi-chevron-right text-muted mx-1 small"></i>
                    <strong class="text-<?= $meta['color'] ?>"><?= esc($zone['name']) ?></strong>
                </div>
                <?php endif; ?>

                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal small">Nom</dt>
                    <dd class="col-7 fw-semibold"><?= esc($zone['name']) ?></dd>

                    <dt class="col-5 text-muted fw-normal small">Type</dt>
                    <dd class="col-7">
                        <span class="badge text-bg-<?= $meta['color'] ?>">
                            <i class="bi <?= $meta['icon'] ?> me-1"></i><?= esc($meta['label']) ?>
                        </span>
                    </dd>

                    <?php if ($zone['code']): ?>
                    <dt class="col-5 text-muted fw-normal small">
                        <?= $zone['type'] === 'pays' ? 'Code ISO' : ($zone['type'] === 'ville' ? 'Code postal' : 'Code') ?>
                    </dt>
                    <dd class="col-7"><code><?= esc($zone['code']) ?></code></dd>
                    <?php endif; ?>

                    <dt class="col-5 text-muted fw-normal small">Statut</dt>
                    <dd class="col-7">
                        <?php if ($zone['is_active']): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border">Inactif</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-5 text-muted fw-normal small">Créé le</dt>
                    <dd class="col-7 small"><?= date('d/m/Y', strtotime($zone['created_at'])) ?></dd>
                </dl>
            </div>

            <!-- Actions -->
            <?php if (in_array('zones.edit', $perms) || in_array('zones.delete', $perms)): ?>
            <div class="card-footer bg-transparent d-flex gap-2 flex-wrap">
                <?php if (in_array('zones.edit', $perms)): ?>
                <form method="POST"
                      action="<?= base_url('admin/zones/' . $zone['id'] . '/toggle-status') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-<?= $zone['is_active'] ? 'secondary' : 'success' ?>">
                        <i class="bi <?= $zone['is_active'] ? 'bi-toggle-off' : 'bi-toggle-on' ?> me-1"></i>
                        <?= $zone['is_active'] ? 'Désactiver' : 'Activer' ?>
                    </button>
                </form>
                <?php endif; ?>
                <?php if (in_array('zones.delete', $perms) && empty($children)): ?>
                <form method="POST"
                      action="<?= base_url('admin/zones/' . $zone['id'] . '/delete') ?>"
                      onsubmit="return confirm('Supprimer « <?= esc($zone['name'], 'js') ?> » définitivement ?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Supprimer
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── SOUS-ZONES ──────────────────────────────────────────────── -->
    <div class="col-md-7">
        <?php
        $childTypes   = ['pays' => 'region', 'region' => 'ville', 'ville' => 'quartier', 'quartier' => null];
        $childType    = $childTypes[$zone['type']] ?? null;
        $childMeta    = $childType ? ($typeMeta[$childType] ?? null) : null;
        ?>
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-list-nested me-1"></i>
                    Sous-zones
                    <span class="badge bg-secondary ms-1"><?= count($children) ?></span>
                </span>
                <?php if ($childMeta && in_array('zones.create', $perms)): ?>
                <a href="<?= base_url('admin/zones/create/' . $childType) ?>"
                   class="btn btn-sm btn-outline-<?= $childMeta['color'] ?>">
                    <i class="bi bi-plus-lg me-1"></i>Ajouter <?= esc($childMeta['label']) ?>
                </a>
                <?php endif; ?>
            </div>

            <?php if (empty($children)): ?>
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                Aucune sous-zone pour le moment.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase text-muted" style="font-size:.7rem">
                        <tr>
                            <th class="ps-3">Nom</th>
                            <th>Type</th>
                            <?php if ($childType === 'ville'): ?><th>Code postal</th><?php endif; ?>
                            <th>Statut</th>
                            <th class="pe-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($children as $child): ?>
                    <?php $cMeta = $typeMeta[$child['type']] ?? ['label' => $child['type'], 'icon' => 'bi-geo', 'color' => 'secondary']; ?>
                    <tr>
                        <td class="ps-3">
                            <a href="<?= base_url('admin/zones/' . $child['id']) ?>"
                               class="fw-semibold text-dark text-decoration-none">
                                <i class="bi <?= $cMeta['icon'] ?> text-<?= $cMeta['color'] ?> me-1"></i>
                                <?= esc($child['name']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge text-bg-<?= $cMeta['color'] ?> bg-opacity-75">
                                <?= esc($cMeta['label']) ?>
                            </span>
                        </td>
                        <?php if ($childType === 'ville'): ?>
                        <td><code><?= $child['code'] ? esc($child['code']) : '—' ?></code></td>
                        <?php endif; ?>
                        <td>
                            <?php if ($child['is_active']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                            <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-3 text-end">
                            <a href="<?= base_url('admin/zones/' . $child['id']) ?>"
                               class="btn btn-sm btn-light">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.row -->

<?php if ($zone['type'] === 'city'): ?>
<!-- ── CARTE GÉOMÉTRIQUE ─────────────────────────────────────────────── -->
<div class="card shadow-sm mt-4" id="mapCard">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            <i class="bi bi-map me-1 text-warning"></i>
            Zone géométrique — <?= esc($zone['name']) ?>
        </span>
        <div class="d-flex gap-2 align-items-center">
            <span id="mapStatus" class="small text-muted"></span>
            <?php if (in_array('zones.edit', $perms)): ?>
            <button class="btn btn-sm btn-warning" id="btnSaveGeo" disabled>
                <i class="bi bi-floppy me-1"></i>Enregistrer la zone
            </button>
            <button class="btn btn-sm btn-outline-danger" id="btnClearGeo" title="Effacer le dessin">
                <i class="bi bi-trash"></i>
            </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="zoneMap" style="height:500px; width:100%; border-radius:0 0 .5rem .5rem;"></div>
    </div>
</div>

<!-- Leaflet CSS + JS (CDN) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

<script>
(function () {
    'use strict';

    const SAVE_URL   = '<?= base_url('admin/zones/' . $zone['id'] . '/geometry') ?>';
    const CSRF_NAME  = '<?= csrf_token() ?>';
    const CSRF_HASH  = '<?= csrf_hash() ?>';
    const ZONE_ID    = <?= (int) $zone['id'] ?>;
    const CAN_EDIT   = <?= in_array('zones.edit', $perms) ? 'true' : 'false' ?>;
    const SAVED_GEO  = <?= ! empty($zone['geometry']) ? $zone['geometry'] : 'null' ?>;

    // ── Initialisation de la carte ───────────────────────────────────
    const map = L.map('zoneMap', { zoomControl: true });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    // Vue par défaut : Tunisie
    map.setView([33.8869, 9.5375], 7);

    // ── Couche des formes dessinées ──────────────────────────────────
    const drawnItems = new L.FeatureGroup().addTo(map);

    // Charger géométrie existante
    if (SAVED_GEO) {
        try {
            const geoLayer = L.geoJSON(SAVED_GEO, {
                style: { color: '#f59e0b', weight: 2, fillOpacity: 0.15 }
            }).addTo(drawnItems);
            map.fitBounds(geoLayer.getBounds(), { padding: [40, 40] });
            setStatus('Géométrie enregistrée', 'success');
        } catch (e) {
            console.warn('GeoJSON existant invalide :', e);
        }
    }

    // ── Contrôle Leaflet.draw ────────────────────────────────────────
    if (CAN_EDIT) {
        const drawControl = new L.Control.Draw({
            edit: { featureGroup: drawnItems, edit: true, remove: true },
            draw: {
                polygon:   { shapeOptions: { color: '#f59e0b', weight: 2, fillOpacity: 0.15 } },
                rectangle: { shapeOptions: { color: '#f59e0b', weight: 2, fillOpacity: 0.15 } },
                circle:    false,
                circlemarker: false,
                marker:    false,
                polyline:  false,
            },
        });
        map.addControl(drawControl);

        // Après avoir dessiné une forme → remplace tout
        map.on(L.Draw.Event.CREATED, function (e) {
            drawnItems.clearLayers();
            drawnItems.addLayer(e.layer);
            enableSave();
        });

        map.on(L.Draw.Event.EDITED, enableSave);
        map.on(L.Draw.Event.DELETED, function () {
            if (drawnItems.getLayers().length === 0) {
                document.getElementById('btnSaveGeo').disabled = true;
                setStatus('Forme supprimée — sauvegardez pour effacer en base', 'warning');
            }
        });

        // Bouton Enregistrer
        document.getElementById('btnSaveGeo').addEventListener('click', saveGeometry);

        // Bouton Effacer
        document.getElementById('btnClearGeo').addEventListener('click', function () {
            if (! confirm('Effacer le dessin actuel sans sauvegarder ?')) return;
            drawnItems.clearLayers();
            document.getElementById('btnSaveGeo').disabled = true;
            setStatus('', '');
        });
    }

    // ── Fonctions ────────────────────────────────────────────────────
    function enableSave() {
        document.getElementById('btnSaveGeo').disabled = false;
        setStatus('Modifications non sauvegardées', 'warning');
    }

    function setStatus(msg, type) {
        const el = document.getElementById('mapStatus');
        el.className = 'small';
        if (type === 'success') el.classList.add('text-success');
        else if (type === 'warning') el.classList.add('text-warning');
        else if (type === 'danger') el.classList.add('text-danger');
        else el.classList.add('text-muted');
        el.textContent = msg;
    }

    function saveGeometry() {
        const btn = document.getElementById('btnSaveGeo');
        btn.disabled = true;
        setStatus('Enregistrement…', '');

        // Construire le GeoJSON
        const geojsonData = drawnItems.toGeoJSON(); // FeatureCollection
        // Si vide → envoie null pour effacer
        const geometryPayload = geojsonData.features.length > 0 ? geojsonData : null;

        fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                [CSRF_NAME]: CSRF_HASH,
            },
            body: JSON.stringify({ geometry: geometryPayload }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                setStatus('Géométrie enregistrée ✓', 'success');
            } else {
                setStatus('Erreur : ' + (data.error ?? 'inconnue'), 'danger');
                btn.disabled = false;
            }
        })
        .catch(() => {
            setStatus('Erreur réseau', 'danger');
            btn.disabled = false;
        });
    }

})();
</script>
<?php endif; ?>
