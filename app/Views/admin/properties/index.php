<!-- LISTE BIENS IMMOBILIERS -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-building me-2 text-primary"></i>Biens Immobiliers</h4>
        <p class="text-muted mb-0"><?= $result['total'] ?> bien(s) – Page <?= $result['page'] ?>/<?= $result['pages'] ?: 1 ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- Toggle vue -->
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary active" id="btn-view-list" title="Liste seule" onclick="setView('list')">
                <i class="bi bi-list-ul"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btn-view-split" title="Liste + Carte" onclick="setView('split')">
                <i class="bi bi-layout-split"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btn-view-map" title="Carte seule" onclick="setView('map')">
                <i class="bi bi-map"></i>
            </button>
        </div>
        <?php if (in_array('properties.create', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/properties/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Nouveau bien
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filtres -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous types</option>
                    <?php foreach ($propertyTypes ?? [] as $pt): ?>
                    <option value="<?= esc($pt['slug']) ?>"
                        <?= ($filters['type'] ?? '') === $pt['slug'] ? 'selected' : '' ?>>
                        <?= esc($pt['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous statuts</option>
                    <option value="available" <?= $filters['status'] === 'available' ? 'selected' : '' ?>>Disponible</option>
                    <option value="reserved"  <?= $filters['status'] === 'reserved'  ? 'selected' : '' ?>>Réservé</option>
                    <option value="sold"      <?= $filters['status'] === 'sold'      ? 'selected' : '' ?>>Vendu</option>
                    <option value="inactive"  <?= $filters['status'] === 'inactive'  ? 'selected' : '' ?>>Inactif</option>
                </select>
            </div>
            <?php if (! $auth->hasRole('expert')) : ?>
            <div class="col-md-2">
                <select name="agent_id" class="form-select form-select-sm">
                    <option value="">Tous agents</option>
                    <?php foreach ($agents as $agent) : ?>
                    <option value="<?= $agent['id'] ?>" <?= $filters['agent_id'] == $agent['id'] ? 'selected' : '' ?>>
                        <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-2">
                <input type="text" name="city" class="form-control form-control-sm"
                       value="<?= esc($filters['city'] ?? '') ?>" placeholder="Ville...">
            </div>
            <div class="col">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="<?= esc($filters['search'] ?? '') ?>" placeholder="Recherche...">
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
                <a href="<?= base_url('admin/properties') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Contenu principal : liste + carte -->
<div id="view-container" class="d-flex gap-3 align-items-start">

<!-- Liste -->
<div id="panel-list" class="flex-grow-1" style="min-width:0;">
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">Photo</th>
                        <th>Titre</th>
                        <th>Réf.</th>
                        <th>Type</th>
                        <th>Ville</th>
                        <th>Prix</th>
                        <th>Agent</th>
                        <th>Agence</th>
                        <th>Statut</th>
                        <th>Publié</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($result['data'])) : ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">Aucun bien trouvé.</td></tr>
                    <?php else : ?>
                    <?php foreach ($result['data'] as $p) :
                        $sMap = ['available'=>'success','reserved'=>'warning','sold'=>'danger','rented'=>'info','inactive'=>'secondary'];
                        $sLbl = ['available'=>'Disponible','reserved'=>'Réservé','sold'=>'Vendu','rented'=>'Loué','inactive'=>'Inactif'];
                        $tLbl = [];
                        foreach ($propertyTypes ?? [] as $_pt) { $tLbl[$_pt['slug']] = $_pt['name']; }
                    ?>
                    <tr>
                        <td>
                            <?php if ($p['primary_image']) : ?>
                            <img src="<?= base_url($p['primary_image']) ?>" alt=""
                                 class="rounded" style="width:48px;height:40px;object-fit:cover;">
                            <?php else : ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                                 style="width:48px;height:40px;">
                                <i class="bi bi-image"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/properties/' . $p['id']) ?>" class="fw-semibold text-decoration-none text-dark">
                                <?= esc($p['title']) ?>
                            </a>
                        </td>
                        <td><code class="text-muted small"><?= esc($p['reference']) ?></code></td>
                        <td class="text-muted small"><?= $tLbl[$p['type']] ?? $p['type'] ?></td>
                        <td class="text-muted small"><?= esc($p['city']) ?></td>
                        <td class="fw-semibold small"><?= number_format($p['price'], 0, ',', ' ') ?> TND</td>
                        <td class="text-muted small"><?= esc($p['first_name'] . ' ' . $p['last_name']) ?></td>
                        <td class="text-muted small"><?= esc($p['agency_name'] ?? '—') ?></td>
                        <td><span class="badge bg-<?= $sMap[$p['status']] ?? 'secondary' ?>"><?= $sLbl[$p['status']] ?? $p['status'] ?></span></td>
                        <td>
                            <?php if (in_array('properties.publish', session()->get('permissions') ?? [])) : ?>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input publish-toggle" type="checkbox"
                                       data-id="<?= $p['id'] ?>"
                                       <?= $p['is_published'] ? 'checked' : '' ?>>
                            </div>
                            <?php else : ?>
                            <i class="bi bi-<?= $p['is_published'] ? 'check-circle-fill text-success' : 'x-circle text-muted' ?>"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <?php if (in_array('properties.edit', session()->get('permissions') ?? [])) : ?>
                                <a href="<?= base_url('admin/properties/' . $p['id'] . '/edit') ?>"
                                   class="btn btn-outline-secondary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                <?php endif; ?>
                                <?php if (in_array('properties.delete', session()->get('permissions') ?? [])) : ?>
                                <button class="btn btn-outline-danger" title="Supprimer"
                                        onclick="confirmDelete(<?= $p['id'] ?>, '<?= esc($p['title']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
    <?php if ($result['pages'] > 1) : ?>
    <div class="card-footer bg-white">
        <nav>
            <ul class="pagination pagination-sm mb-0 justify-content-center">
                <?php for ($i = 1; $i <= $result['pages']; $i++) : ?>
                <li class="page-item <?= $i == $result['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<form id="deleteForm" method="POST" class="d-none"><?= csrf_field() ?></form>

</div><!-- /panel-list -->

<!-- Panneau carte -->
<div id="panel-map" style="display:none; width:48%; flex-shrink:0; position:sticky; top:80px;">
    <div class="card shadow-sm">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <span class="fw-semibold small"><i class="bi bi-map me-1 text-primary"></i>Carte des biens</span>
            <span class="badge bg-primary rounded-pill" id="map-count">0</span>
        </div>
        <div class="card-body p-0">
            <div id="properties-map" style="height:600px; border-radius:0 0 .375rem .375rem;"></div>
        </div>
    </div>
</div>

</div><!-- /view-container -->

<?php
// Préparer les données JSON pour la carte
$mapData = [];
foreach ($result['data'] as $p) {
    $lat = (float)($p['latitude']  ?? 0);
    $lng = (float)($p['longitude'] ?? 0);
    $mapData[] = [
        'id'    => (int)$p['id'],
        'title' => $p['title'],
        'city'  => $p['city'],
        'price' => number_format((float)$p['price'], 0, ',', ' ') . ' TND',
        'type'  => $p['type'],
        'status'=> $p['status'],
        'ref'   => $p['reference'],
        'img'   => $p['primary_image'] ? base_url($p['primary_image']) : null,
        'url'   => base_url('admin/properties/' . $p['id']),
        'lat'   => $lat ?: null,
        'lng'   => $lng ?: null,
    ];
}
?>

<!-- Leaflet CSS/JS chargés statiquement via extra_css/extra_js -->
<script>
// Données biens
const propertiesData = <?= json_encode($mapData) ?>;

// Couleurs par statut
const statusColors = {
    available: '#198754',
    reserved:  '#ffc107',
    sold:      '#dc3545',
    rented:    '#0dcaf0',
    inactive:  '#6c757d'
};
const statusLabels = {
    available: 'Disponible', reserved: 'Réservé',
    sold: 'Vendu', rented: 'Loué', inactive: 'Inactif'
};

let map = null;
let markers = [];
let currentView = localStorage.getItem('propView') || 'list';

function loadLeaflet(callback) {
    if (typeof L !== 'undefined') {
        callback();
        return;
    }
    // Fallback si le script statique n'a pas pu charger (CDN lent/bloqué)
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js';
    script.onload = callback;
    if (!document.querySelector('link[href*="leaflet"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css';
        document.head.appendChild(link);
    }
    document.head.appendChild(script);
}

function initMap() {
    if (map) return;
    loadLeaflet(() => {
        map = L.map('properties-map').setView([36.8, 10.18], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 18
        }).addTo(map);
        loadMarkers();
    });
}

function makeIcon(status) {
    const color = statusColors[status] || '#6c757d';
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 36" width="28" height="42">
      <path fill="${color}" stroke="#fff" stroke-width="1.5"
        d="M12 0C7.58 0 4 3.58 4 8c0 5.5 8 20 8 20s8-14.5 8-20c0-4.42-3.58-8-8-8z"/>
      <circle fill="#fff" cx="12" cy="8" r="3.5"/>
    </svg>`;
    return L.divIcon({
        html: svg,
        className: '',
        iconSize: [28, 42],
        iconAnchor: [14, 42],
        popupAnchor: [0, -44]
    });
}

function loadMarkers() {
    markers.forEach(m => map.removeLayer(m));
    markers = [];
    const bounds = [];
    let count = 0;

    propertiesData.forEach(p => {
        let lat = p.lat, lng = p.lng;

        // Fallback géocodage approximatif par ville (Tunisie)
        if (!lat || !lng) {
            const cityCoords = {
                'tunis': [36.8190, 10.1658], 'sfax': [34.7398, 10.7600],
                'sousse': [35.8245, 10.6346], 'nabeul': [36.4574, 10.7357],
                'hammamet': [36.4011, 10.6169], 'monastir': [35.7643, 10.8113],
                'kairouan': [35.6781, 10.0963], 'ariana': [36.8665, 10.1647],
                'la marsa': [36.8773, 10.3248], 'mégrine': [36.7667, 10.2167],
                'megrine': [36.7667, 10.2167], 'menzah': [36.8500, 10.1833],
                'ennasr': [36.8667, 10.2000], 'lac': [36.8361, 10.2231],
                'bizerte': [37.2744, 9.8739], 'djerba': [33.8667, 10.8333],
                'gromball': [36.5833, 10.5000], 'grombalia': [36.5833, 10.5000],
                'mahdia': [35.5047, 11.0622], 'ben arous': [36.7537, 10.2231],
                'beja': [36.7333, 9.1833], 'jendouba': [36.5011, 8.7803],
                'kef': [36.1675, 8.7047], 'siliana': [36.0847, 9.3708],
                'zaghouan': [36.4028, 10.1433], 'gabes': [33.8839, 10.0982],
                'medenine': [33.3500, 10.5000], 'tataouine': [32.9292, 10.4508],
                'tozeur': [33.9197, 8.1339], 'kebili': [33.7042, 8.9692],
                'sidi bouzid': [35.0361, 9.4842], 'kasserine': [35.1667, 8.8333],
                'gafsa': [34.4250, 8.7842]
            };
            const key = (p.city || '').toLowerCase().trim();
            const found = Object.keys(cityCoords).find(k => key.includes(k));
            if (found) {
                // Légère variation aléatoire pour éviter superposition
                lat = cityCoords[found][0] + (Math.random() - 0.5) * 0.03;
                lng = cityCoords[found][1] + (Math.random() - 0.5) * 0.03;
            }
        }

        if (!lat || !lng) return;
        count++;
        bounds.push([lat, lng]);

        const imgHtml = p.img
            ? `<img src="${p.img}" style="width:100%;height:80px;object-fit:cover;border-radius:4px;margin-bottom:6px;">`
            : '';

        const popup = `
            <div style="min-width:200px;">
                ${imgHtml}
                <div class="fw-semibold" style="font-size:.85rem;">${p.title}</div>
                <div style="font-size:.75rem;color:#666;">${p.ref} · ${p.city}</div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span style="font-weight:600;color:#0d6efd;">${p.price}</span>
                    <span class="badge" style="background:${statusColors[p.status] || '#6c757d'};font-size:.65rem;">${statusLabels[p.status] || p.status}</span>
                </div>
                <a href="${p.url}" class="btn btn-sm btn-primary mt-2 w-100" style="font-size:.75rem;">Voir le bien</a>
            </div>`;

        const marker = L.marker([lat, lng], { icon: makeIcon(p.status) })
            .bindPopup(popup, { maxWidth: 240 })
            .addTo(map);
        markers.push(marker);
    });

    document.getElementById('map-count').textContent = count;
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [30, 30], maxZoom: 13 });
    }
}

function setView(mode) {
    currentView = mode;
    localStorage.setItem('propView', mode);
    const panelList = document.getElementById('panel-list');
    const panelMap  = document.getElementById('panel-map');
    const container = document.getElementById('view-container');
    const btnList   = document.getElementById('btn-view-list');
    const btnSplit  = document.getElementById('btn-view-split');
    const btnMap    = document.getElementById('btn-view-map');

    [btnList, btnSplit, btnMap].forEach(b => b.classList.remove('active'));

    if (mode === 'list') {
        panelList.style.display = '';
        panelMap.style.display  = 'none';
        container.style.flexWrap = '';
        btnList.classList.add('active');
    } else if (mode === 'split') {
        panelList.style.display = '';
        panelMap.style.display  = '';
        panelList.style.width   = '';
        container.style.flexWrap = 'nowrap';
        btnSplit.classList.add('active');
        loadLeaflet(() => {
            initMap();
            setTimeout(() => map && map.invalidateSize(), 300);
            setTimeout(() => map && map.invalidateSize(), 800);
        });
    } else if (mode === 'map') {
        panelList.style.display = 'none';
        panelMap.style.display  = '';
        panelMap.style.width    = '100%';
        btnMap.classList.add('active');
        loadLeaflet(() => {
            initMap();
            setTimeout(() => map && map.invalidateSize(), 300);
            setTimeout(() => map && map.invalidateSize(), 800);
        });
    }
}

// Restaurer la vue sauvegardée
document.addEventListener('DOMContentLoaded', () => {
    if (currentView !== 'list') setView(currentView);
});

function confirmDelete(id, title) {
    if (confirm('Supprimer le bien "' + title + '" ?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/properties/' + id + '/delete';
        form.classList.remove('d-none');
        form.submit();
    }
}

// Toggle publication AJAX
document.querySelectorAll('.publish-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const id = this.dataset.id;
        fetch('/admin/properties/' + id + '/publish', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ '<?= csrf_token() ?>': '<?= csrf_hash() ?>' })
        }).then(r => r.json()).then(data => {
            if (! data.success) this.checked = ! this.checked;
        });
    });
});
</script>
