<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<style>
    .tree-container {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 20px;
    }
    
    .tree-governorate {
        margin-bottom: 30px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .tree-governorate-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
    }
    
    .tree-governorate-header:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
    
    .tree-governorate-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .tree-governorate-header .toggle-icon {
        transition: transform 0.3s;
    }
    
    .tree-governorate-header.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }
    
    .tree-governorate-info {
        display: flex;
        gap: 15px;
        align-items: center;
        font-size: 0.9rem;
    }
    
    .tree-governorate-actions {
        display: flex;
        gap: 5px;
    }
    
    .tree-governorate-actions .btn {
        padding: 5px 10px;
        font-size: 0.85rem;
    }
    
    .tree-cities {
        background: #f9fafb;
    }
    
    .tree-city {
        padding: 12px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }
    
    .tree-city:last-child {
        border-bottom: none;
    }
    
    .tree-city:hover {
        background: white;
        padding-left: 30px;
    }
    
    .tree-city-content {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }
    
    .tree-city-icon {
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .tree-city-name {
        font-weight: 500;
        color: #1f2937;
    }
    
    .tree-city-meta {
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 0.85rem;
        color: #6b7280;
    }
    
    .popularity-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .popularity-5 {
        background: #fef3c7;
        color: #92400e;
    }
    
    .popularity-4 {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .popularity-3 {
        background: #d1fae5;
        color: #065f46;
    }
    
    .popularity-2 {
        background: #e5e7eb;
        color: #374151;
    }
    
    .tree-city-actions {
        display: flex;
        gap: 5px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .tree-city:hover .tree-city-actions {
        opacity: 1;
    }
    
    .tree-city-actions .btn {
        padding: 4px 8px;
        font-size: 0.8rem;
    }
    
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }
    
    .stat-item {
        text-align: center;
        padding: 15px;
        background: #f9fafb;
        border-radius: 8px;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        display: block;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 5px;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sitemap me-2"></i><?= $page_title ?? 'Gestion des Zones' ?>
        </h1>
        <a href="<?= base_url('admin/zones/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Zone
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <!-- Statistics -->
    <div class="stats-card">
        <div class="stats-row">
            <div class="stat-item">
                <span class="stat-number"><?= count($governorates) ?></span>
                <span class="stat-label">Gouvernorats</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= array_sum(array_map('count', $citiesByParent)) ?></span>
                <span class="stat-label">Villes</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= count($zones) ?></span>
                <span class="stat-label">Total Zones</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">
                    <?php 
                    $popular = array_filter($zones, fn($z) => ($z['popularity_score'] ?? 0) >= 80);
                    echo count($popular);
                    ?>
                </span>
                <span class="stat-label">Zones Populaires</span>
            </div>
        </div>
    </div>

    <!-- Tree View -->
    <div class="tree-container">
        <?php if (empty($governorates)): ?>
            <div class="empty-state">
                <i class="fas fa-map-marker-alt"></i>
                <h5>Aucune zone disponible</h5>
                <p>Commencez par créer un gouvernorat</p>
                <a href="<?= base_url('admin/zones/create') ?>" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Créer une zone
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($governorates as $gov): ?>
                <div class="tree-governorate">
                    <div class="tree-governorate-header" onclick="toggleGovernorate(<?= $gov['id'] ?>)">
                        <div style="display: flex; align-items: center; gap: 15px; flex: 1;">
                            <i class="fas fa-chevron-down toggle-icon"></i>
                            <h5>
                                <i class="fas fa-map-marked-alt"></i>
                                <?= esc($gov['name']) ?>
                                <?php if ($gov['name_ar']): ?>
                                    <small style="opacity: 0.8;">(<?= esc($gov['name_ar']) ?>)</small>
                                <?php endif ?>
                            </h5>
                            <span class="badge bg-light text-dark">
                                <?= count($citiesByParent[$gov['id']] ?? []) ?> villes
                            </span>
                        </div>
                        <div class="tree-governorate-actions" onclick="event.stopPropagation();">
                            <a href="<?= base_url('admin/zones/edit/' . $gov['id']) ?>" 
                               class="btn btn-sm btn-light" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-light" 
                                    onclick="confirmDelete(<?= $gov['id'] ?>)" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="tree-cities" id="cities-<?= $gov['id'] ?>">
                        <?php 
                        $cities = $citiesByParent[$gov['id']] ?? [];
                        if (empty($cities)): 
                        ?>
                            <div class="empty-state" style="padding: 20px;">
                                <i class="fas fa-city" style="font-size: 2rem;"></i>
                                <p class="mb-0">Aucune ville dans ce gouvernorat</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($cities as $city): ?>
                                <div class="tree-city">
                                    <div class="tree-city-content">
                                        <span class="tree-city-icon">├─</span>
                                        <span class="tree-city-name"><?= esc($city['name']) ?></span>
                                        <?php if ($city['name_ar']): ?>
                                            <small class="text-muted"><?= esc($city['name_ar']) ?></small>
                                        <?php endif ?>
                                    </div>
                                    
                                    <div class="tree-city-meta">
                                        <?php 
                                        $score = $city['popularity_score'] ?? 0;
                                        $badgeClass = 'popularity-2';
                                        $stars = '⭐';
                                        if ($score >= 100) { $badgeClass = 'popularity-5'; $stars = '⭐⭐⭐⭐⭐'; }
                                        elseif ($score >= 90) { $badgeClass = 'popularity-4'; $stars = '⭐⭐⭐⭐'; }
                                        elseif ($score >= 80) { $badgeClass = 'popularity-3'; $stars = '⭐⭐⭐'; }
                                        elseif ($score >= 70) { $badgeClass = 'popularity-2'; $stars = '⭐⭐'; }
                                        ?>
                                        <span class="popularity-badge <?= $badgeClass ?>">
                                            <?= $stars ?> <?= $score ?>
                                        </span>
                                        
                                        <span style="color: #9ca3af;">ID: <?= $city['id'] ?></span>
                                    </div>
                                    
                                    <div class="tree-city-actions">
                                        <a href="<?= base_url('admin/zones/edit/' . $city['id']) ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?= $city['id'] ?>)" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleGovernorate(govId) {
    const citiesDiv = document.getElementById('cities-' + govId);
    const header = citiesDiv.previousElementSibling;
    
    if (citiesDiv.style.display === 'none') {
        citiesDiv.style.display = 'block';
        header.classList.remove('collapsed');
    } else {
        citiesDiv.style.display = 'none';
        header.classList.add('collapsed');
    }
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette zone ? Cette action est irréversible.')) {
        window.location.href = '<?= base_url('admin/zones/delete/') ?>' + id;
    }
}

// Collapse all by default except first
document.addEventListener('DOMContentLoaded', function() {
    const allCities = document.querySelectorAll('.tree-cities');
    allCities.forEach((cities, index) => {
        if (index !== 0) {
            cities.style.display = 'none';
            cities.previousElementSibling.classList.add('collapsed');
        }
    });
});
</script>

<?= $this->endSection() ?>
                    <table class="table table-hover mb-0" id="zonesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Parent</th>
                                    <th>Popularité</th>
                                    <th class="text-center no-filter">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php 
                        $typeLabels = [
                            'governorate' => 'Gouvernorat',
                            'city' => 'Ville',
                            'district' => 'Délégation',
                            'area' => 'Quartier'
                        ];
                        $typeBadges = [
                            'governorate' => 'primary',
                            'city' => 'success',
                            'district' => 'info',
                            'area' => 'secondary'
                        ];
                        
                        // Group zones by parent for better display
                        $zonesByType = [];
                        foreach ($zones as $zone) {
                            $zonesByType[$zone['type']][] = $zone;
                        }
                        ?>
                        
                        <?php foreach ($zones as $zone): ?>
                            <tr class="zone-row" data-zone-id="<?= $zone['id'] ?>" 
                                data-lat="<?= $zone['latitude'] ?? '' ?>" 
                                data-lng="<?= $zone['longitude'] ?? '' ?>"
                                onclick="highlightZone(<?= $zone['id'] ?>, <?= $zone['latitude'] ?? 'null' ?>, <?= $zone['longitude'] ?? 'null' ?>)">
                                <td><strong><?= $zone['id'] ?></strong></td>
                                <td><?= esc($zone['name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $typeBadges[$zone['type']] ?? 'secondary' ?>">
                                        <?= $typeLabels[$zone['type']] ?? $zone['type'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($zone['parent_id']): ?>
                                        <?php
                                        $parent = array_filter($zones, fn($z) => $z['id'] == $zone['parent_id']);
                                        $parent = reset($parent);
                                        ?>
                                        <small class="text-muted"><?= $parent ? esc($parent['name']) : 'N/A' ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?= min($zone['popularity_score'] ?? 0, 100) ?>%">
                                            <?= $zone['popularity_score'] ?? 0 ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <?php if (!empty($zone['latitude']) && !empty($zone['longitude'])): ?>
                                            <a href="https://www.google.com/maps?q=<?= $zone['latitude'] ?>,<?= $zone['longitude'] ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-secondary" title="Voir sur Google Maps">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </a>
                                        <?php endif ?>
                                        <a href="<?= base_url('admin/zones/edit/' . $zone['id']) ?>" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="event.stopPropagation(); confirmDelete(<?= $zone['id'] ?>)" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        
        <div class="resize-handle" id="resizeHandle"></div>
        
        <div class="map-panel">
            <div id="zonesMap"></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?= base_url('assets/js/datatable-filters.js') ?>"></script>
<script>
let map;
let markers = {};
const zonesData = <?= json_encode($zones) ?>;

// Initialize map
function initMap() {
    map = L.map('zonesMap').setView([36.8065, 10.1815], 7); // Centre sur la Tunisie
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
    // Add markers for all zones with coordinates
    zonesData.forEach(zone => {
        if (zone.latitude && zone.longitude) {
            const markerColor = getMarkerColor(zone.type);
            const marker = L.circleMarker([zone.latitude, zone.longitude], {
                radius: 8,
                fillColor: markerColor,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);
            
            marker.bindPopup(`
                <div class="p-2">
                    <h6 class="mb-1">${zone.name}</h6>
                    <span class="badge bg-${getTypeBadge(zone.type)}">${getTypeLabel(zone.type)}</span>
                    <div class="mt-2">
                        <a href="/admin/zones/edit/${zone.id}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
            `);
            
            markers[zone.id] = marker;
        }
    });
}

function getMarkerColor(type) {
    const colors = {
        'governorate': '#0d6efd',
        'city': '#198754',
        'district': '#0dcaf0',
        'area': '#6c757d'
    };
    return colors[type] || '#6c757d';
}

function getTypeBadge(type) {
    const badges = {
        'governorate': 'primary',
        'city': 'success',
        'district': 'info',
        'area': 'secondary'
    };
    return badges[type] || 'secondary';
}

function getTypeLabel(type) {
    const labels = {
        'governorate': 'Gouvernorat',
        'city': 'Ville',
        'district': 'Délégation',
        'area': 'Quartier'
    };
    return labels[type] || type;
}

function highlightZone(zoneId, lat, lng) {
    // Remove previous highlights
    document.querySelectorAll('.zone-row').forEach(row => row.classList.remove('active'));
    
    // Highlight current row
    document.querySelector(`[data-zone-id="${zoneId}"]`)?.classList.add('active');
    
    // Center map on zone if has coordinates
    if (lat && lng && markers[zoneId]) {
        map.setView([lat, lng], 13);
        markers[zoneId].openPopup();
    }
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette zone ?')) {
        window.location.href = '<?= base_url('admin/zones/delete/') ?>' + id;
    }
}

// Resize handle
const listPanel = document.querySelector('.list-panel');
const mapPanel = document.querySelector('.map-panel');
const resizeHandle = document.getElementById('resizeHandle');
let isResizing = false;
let currentView = 'split';

// Toggle view modes
function toggleView(mode) {
    currentView = mode;
    
    // Update button states
    document.querySelectorAll('.view-toggle-btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.view-toggle-btn').classList.add('active');
    
    listPanel.classList.remove('fullscreen', 'hidden');
    mapPanel.classList.remove('fullscreen', 'hidden');
    resizeHandle.classList.remove('hidden');
    
    if (mode === 'split') {
        listPanel.style.width = '50%';
        mapPanel.style.width = '50%';
    } else if (mode === 'table') {
        listPanel.classList.add('fullscreen');
        mapPanel.classList.add('hidden');
        resizeHandle.classList.add('hidden');
    } else if (mode === 'map') {
        listPanel.classList.add('hidden');
        mapPanel.classList.add('fullscreen');
        resizeHandle.classList.add('hidden');
    }
    
    setTimeout(() => map.invalidateSize(), 100);
}

resizeHandle.addEventListener('mousedown', () => isResizing = true);

document.addEventListener('mousemove', (e) => {
    if (!isResizing || currentView !== 'split') return;
    
    const containerWidth = document.querySelector('.split-view-container').offsetWidth;
    const listWidth = (e.clientX / containerWidth) * 100;
    
    if (listWidth > 30 && listWidth < 70) {
        listPanel.style.width = listWidth + '%';
        mapPanel.style.width = (100 - listWidth) + '%';
        map.invalidateSize();
    }
});

document.addEventListener('mouseup', () => {
    if (isResizing) {
        isResizing = false;
    }
});

$(document).ready(function() {
    initDataTableWithFilters('zonesTable', {
        order: [[3, 'asc'], [1, 'asc']],
        columnDefs: [
            { orderable: false, targets: 5 }
        ],
        pageLength: 25
    });
    
    initMap();
    
    // Fix map size after DataTable initialization
    setTimeout(() => map.invalidateSize(), 300);
});
</script>

<?= $this->endSection() ?>
