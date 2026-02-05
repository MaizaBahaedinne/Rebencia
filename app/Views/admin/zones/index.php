<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .split-view-container {
        display: flex;
        height: calc(100vh - 140px);
        gap: 0;
        margin: 0 -2rem;
        position: relative;
    }
    .list-panel {
        width: 50%;
        overflow-y: auto;
        padding: 2rem;
        background: #f5f6fa;
    }
    .map-panel {
        width: 50%;
        position: relative;
        background: #e5e7eb;
    }
    #zonesMap {
        width: 100%;
        height: 100%;
    }
    .resize-handle {
        width: 5px;
        cursor: ew-resize;
        background: #cbd5e1;
        transition: background 0.3s;
        position: relative;
        z-index: 10;
    }
    .resize-handle:hover {
        background: #3b82f6;
    }
    .view-toggle {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background: white;
        border-radius: 10px;
        padding: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .zone-card {
        cursor: pointer;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }
    .zone-card:hover {
        border-left-color: #3b82f6;
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .zone-card.active {
        border-left-color: #10b981;
        background: #f0fdf4;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marked-alt me-2"></i><?= $page_title ?? 'Gestion des Zones' ?>
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

    <div class="split-view-container">
        <div class="list-panel">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Liste des Zones</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
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
                                <td>
                                    <?php if (!empty($zone['latitude']) && !empty($zone['longitude'])): ?>
                                        <a href="https://www.google.com/maps?q=<?= $zone['latitude'] ?>,<?= $zone['longitude'] ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-secondary" title="Voir sur Google Maps">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/zones/edit/' . $zone['id']) ?>" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?= $zone['id'] ?>)" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
                </div>
            </div>
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

resizeHandle.addEventListener('mousedown', () => isResizing = true);

document.addEventListener('mousemove', (e) => {
    if (!isResizing) return;
    
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
