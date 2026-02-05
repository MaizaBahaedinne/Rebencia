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
    #propertiesMap {
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
    .property-row {
        cursor: pointer;
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }
    .property-row:hover {
        border-left-color: #3b82f6;
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .property-row.active {
        border-left-color: #10b981;
        background: #f0fdf4;
    }
    .leaflet-popup-content {
        min-width: 250px;
    }
    .property-popup img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-building"></i> Gestion des Biens Immobiliers
    </h1>
    <?php if (canCreate('properties')): ?>
    <a href="<?= base_url('admin/properties/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouveau Bien
    </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="split-view-container">
    <div class="list-panel">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="propertiesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Réf</th>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Ville</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th class="no-filter">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($properties)): ?>
                                <?php foreach ($properties as $property): ?>
                                    <tr class="property-row" 
                                        data-property-id="<?= $property['id'] ?>"
                                        data-lat="<?= $property['latitude'] ?? '' ?>"
                                        data-lng="<?= $property['longitude'] ?? '' ?>"
                                        onclick="highlightProperty(<?= $property['id'] ?>, <?= $property['latitude'] ?? 'null' ?>, <?= $property['longitude'] ?? 'null' ?>)">
                                        <td><strong><?= esc($property['reference']) ?></strong></td>
                                        <td><?= esc($property['title']) ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= ucfirst($property['type']) ?></span>
                                        </td>
                                        <td><?= esc($property['city'] ?? '-') ?></td>
                                        <td><strong><?= number_format($property['price'], 0, ',', ' ') ?> TND</strong></td>
                                        <td>
                                            <?php
                                            $badgeClass = match($property['status']) {
                                                'published' => 'success',
                                                'reserved' => 'warning',
                                                'sold' => 'danger',
                                                default => 'secondary'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($property['status']) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" class="btn btn-sm btn-info" onclick="event.stopPropagation()">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/properties/edit/' . $property['id']) ?>" class="btn btn-sm btn-warning" onclick="event.stopPropagation()">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Aucune propriété trouvée
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="resize-handle" id="resizeHandle"></div>
    
    <div class="map-panel">
        <div id="propertiesMap"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?= base_url('assets/js/datatable-filters.js') ?>"></script>
<script>
let map;
let markers = {};
const propertiesData = <?= json_encode($properties) ?>;

// Initialize map
function initMap() {
    map = L.map('propertiesMap').setView([36.8065, 10.1815], 7); // Centre sur la Tunisie
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
    // Add markers for all properties with coordinates
    propertiesData.forEach(property => {
        if (property.latitude && property.longitude) {
            const iconColor = getMarkerColor(property.status);
            
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background:${iconColor};width:30px;height:30px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-home" style="color:white;font-size:14px;"></i>
                </div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            
            const marker = L.marker([property.latitude, property.longitude], { icon }).addTo(map);
            
            const price = new Intl.NumberFormat('fr-TN').format(property.price);
            marker.bindPopup(`
                <div class="property-popup">
                    <h6 class="mb-2">${property.title}</h6>
                    <div class="mb-2">
                        <span class="badge bg-info">${property.type}</span>
                        <span class="badge bg-${getStatusBadge(property.status)}">${property.status}</span>
                    </div>
                    <p class="mb-2"><strong>${price} TND</strong></p>
                    <small class="text-muted"><i class="fas fa-map-marker-alt"></i> ${property.city || '-'}</small>
                    <div class="mt-3">
                        <a href="/admin/properties/view/${property.id}" class="btn btn-sm btn-primary w-100 mb-1">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        <a href="/admin/properties/edit/${property.id}" class="btn btn-sm btn-warning w-100">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
            `);
            
            markers[property.id] = marker;
        }
    });
    
    // Fit bounds to show all markers
    if (Object.keys(markers).length > 0) {
        const group = L.featureGroup(Object.values(markers));
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

function getMarkerColor(status) {
    const colors = {
        'published': '#198754',
        'reserved': '#ffc107',
        'sold': '#dc3545',
        'draft': '#6c757d'
    };
    return colors[status] || '#0d6efd';
}

function getStatusBadge(status) {
    const badges = {
        'published': 'success',
        'reserved': 'warning',
        'sold': 'danger',
        'draft': 'secondary'
    };
    return badges[status] || 'primary';
}

function highlightProperty(propertyId, lat, lng) {
    // Remove previous highlights
    document.querySelectorAll('.property-row').forEach(row => row.classList.remove('active'));
    
    // Highlight current row
    document.querySelector(`[data-property-id="${propertyId}"]`)?.classList.add('active');
    
    // Center map on property if has coordinates
    if (lat && lng && markers[propertyId]) {
        map.setView([lat, lng], 15);
        markers[propertyId].openPopup();
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
    initDataTableWithFilters('propertiesTable', {
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 6 }
        ],
        pageLength: 25
    });
    
    initMap();
    
    // Fix map size after DataTable initialization
    setTimeout(() => map.invalidateSize(), 300);
});
</script>
<?= $this->endSection() ?>
