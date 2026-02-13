<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marked-alt me-2"></i>Gestion des Zones
        </h1>
        <div>
            <button class="btn btn-outline-secondary me-2" onclick="expandAll()">
                <i class="fas fa-chevron-down me-1"></i>Développer tout
            </button>
            <button class="btn btn-outline-secondary me-2" onclick="collapseAll()">
                <i class="fas fa-chevron-right me-1"></i>Réduire tout
            </button>
            <a href="<?= base_url('admin/zones/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Zone
            </a>
        </div>
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

    <div class="row">
        <!-- Carte (moitié gauche) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="height: 650px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map me-2"></i>Carte des Zones</h5>
                </div>
                <div class="card-body p-0">
                    <div id="zonesMap" style="height: 100%; width: 100%;"></div>
                </div>
            </div>
        </div>

        <!-- Liste en mode détails (moitié droite) -->
        <div class="col-lg-6 mb-4">
    <div class="card shadow-sm" style="height: 650px;">
        <div class="card-body p-0" style="overflow-y: auto;">
            <div class="table-responsive">
                <table class="table table-hover mb-0 zone-tree-table">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 35%;">
                                <i class="fas fa-folder me-2"></i>Nom
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-tag me-2"></i>Type
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-globe me-2"></i>Pays
                            </th>
                            <th style="width: 12%;" class="text-center">
                                <i class="fas fa-star me-2"></i>Popularité
                            </th>
                            <th style="width: 10%;" class="text-center">
                                <i class="fas fa-map-pin me-2"></i>Coord.
                            </th>
                            <th style="width: 13%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="zonesTableBody">
                        <!-- Les zones seront rendues ici -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #zonesMap {
        z-index: 1;
    }
    
    .zone-tree-table {
        font-size: 0.9rem;
    }
    
    .zone-tree-table tbody tr {
        transition: background-color 0.2s;
    }
    
    .zone-tree-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .zone-name-cell {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .toggle-icon {
        display: inline-block;
        transition: transform 0.2s;
        width: 20px;
        text-align: center;
    }
    
    .toggle-icon.expanded i {
        transform: rotate(90deg);
    }
    
    .zone-row.collapsed {
        display: none;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Initialize the map
let zonesMap;
const zoneMarkers = {};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Tunisia
    zonesMap = L.map('zonesMap').setView([36.8065, 10.1815], 7);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(zonesMap);
    
    // Add zone markers
    zones.forEach(zone => {
        if (zone.latitude && zone.longitude) {
            const lat = parseFloat(zone.latitude);
            const lon = parseFloat(zone.longitude);
            
            // Determine marker color based on type
            let color = '#6c757d';
            switch(zone.type) {
                case 'governorate': color = '#0d6efd'; break;
                case 'city': color = '#0dcaf0'; break;
                case 'district': color = '#198754'; break;
                case 'area': color = '#6c757d'; break;
            }
            
            const marker = L.circleMarker([lat, lon], {
                radius: zone.type === 'governorate' ? 10 : zone.type === 'city' ? 8 : 6,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(zonesMap);
            
            // Popup
            const popupContent = `
                <div class="text-center">
                    <strong>${zone.name}</strong><br>
                    ${zone.name_ar ? '<small class="text-muted">' + zone.name_ar + '</small><br>' : ''}
                    <span class="badge bg-primary">${zone.type}</span><br>
                    <small>Pop: ${zone.popularity_score || 0}</small>
                </div>
            `;
            marker.bindPopup(popupContent);
            
            zoneMarkers[zone.id] = marker;
            
            // Click on marker to highlight row
            marker.on('click', function() {
                highlightZoneRow(zone.id);
            });
        }
    });
});

function highlightZoneRow(zoneId) {
    // Remove previous highlights
    document.querySelectorAll('.zone-row').forEach(row => {
        row.style.backgroundColor = '';
    });
    
    // Highlight the selected row
    const row = document.querySelector(`tr[data-zone-id="${zoneId}"]`);
    if (row) {
        row.style.backgroundColor = '#fff3cd';
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        setTimeout(() => {
            row.style.backgroundColor = '';
        }, 2000);
    }
}

// Organize zones hierarchically
const zones = <?= json_encode($zones) ?>;
const zonesByParent = {};

zones.forEach(zone => {
    const parentId = zone.parent_id || 0;
    if (!zonesByParent[parentId]) {
        zonesByParent[parentId] = [];
    }
    zonesByParent[parentId].push(zone);
});

// Render zone row
function renderZoneRow(zone, level = 0, parentPath = '') {
    const indent = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(level);
    const hasChildren = zonesByParent[zone.id] && zonesByParent[zone.id].length > 0;
    const zonePath = parentPath + '_' + zone.id;
    
    // Type badge
    let typeBadge = '';
    let typeIcon = '';
    let iconColor = '';
    
    switch(zone.type) {
        case 'governorate':
            typeBadge = '<span class="badge bg-primary">Gouvernorat</span>';
            typeIcon = 'fa-flag';
            iconColor = 'text-primary';
            break;
        case 'city':
            typeBadge = '<span class="badge bg-info">Ville</span>';
            typeIcon = 'fa-city';
            iconColor = 'text-info';
            break;
        case 'district':
            typeBadge = '<span class="badge bg-success">Quartier</span>';
            typeIcon = 'fa-map-signs';
            iconColor = 'text-success';
            break;
        case 'area':
            typeBadge = '<span class="badge bg-secondary">Zone</span>';
            typeIcon = 'fa-location-dot';
            iconColor = 'text-secondary';
            break;
    }
    
    let html = `<tr class="zone-row" data-zone-id="${zone.id}" data-level="${level}" data-path="${zonePath}"`;
    if (level > 0) {
        html += ` data-parent="${zone.parent_id}"`;
    }
    html += '>';
    
    // Nom avec indentation
    html += '<td class="zone-name-cell">';
    html += indent;
    if (hasChildren) {
        html += `<span class="toggle-icon me-2" onclick="toggleZone('${zonePath}')" style="cursor: pointer;">`;
        html += '<i class="fas fa-chevron-right"></i>';
        html += '</span>';
    } else {
        html += '<span class="me-2" style="width: 20px; display: inline-block;"></span>';
    }
    html += `<i class="fas ${typeIcon} ${iconColor} me-2"></i>`;
    html += `<strong>${escapeHtml(zone.name)}</strong>`;
    if (zone.name_ar) {
        html += ` <small class="text-muted">(${escapeHtml(zone.name_ar)})</small>`;
    }
    html += '</td>';
    
    // Type
    html += `<td>${typeBadge}</td>`;
    
    // Pays
    html += '<td>';
    html += `<span class="text-muted">${escapeHtml(zone.country || 'Tunisia')}</span>`;
    html += '</td>';
    
    // Popularité
    html += '<td class="text-center">';
    const popularity = zone.popularity_score || 0;
    const stars = Math.min(5, Math.max(0, Math.round(popularity / 20)));
    for (let i = 0; i < stars; i++) {
        html += '<i class="fas fa-star text-warning"></i>';
    }
    for (let i = stars; i < 5; i++) {
        html += '<i class="far fa-star text-muted"></i>';
    }
    html += ` <small class="text-muted">(${popularity})</small>`;
    html += '</td>';
    
    // Coordonnées
    html += '<td class="text-center">';
    if (zone.latitude && zone.longitude) {
        html += `<span class="badge bg-success" title="Lat: ${zone.latitude}, Lon: ${zone.longitude}">`;
        html += '<i class="fas fa-check"></i>';
        html += '</span>';
    } else {
        html += '<span class="badge bg-secondary">';
        html += '<i class="fas fa-times"></i>';
        html += '</span>';
    }
    html += '</td>';
    
    // Actions
    html += '<td class="text-center">';
    html += '<div class="btn-group btn-group-sm">';
    html += `<a href="<?= base_url('admin/zones/edit/') ?>${zone.id}" class="btn btn-outline-primary" title="Modifier">`;
    html += '<i class="fas fa-edit"></i>';
    html += '</a>';
    html += `<a href="<?= base_url('admin/zones/delete/') ?>${zone.id}" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette zone ?')" title="Supprimer">`;
    html += '<i class="fas fa-trash"></i>';
    html += '</a>';
    html += '</div>';
    html += '</td>';
    
    html += '</tr>';
    
    return html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Render all zones recursively
function renderZones() {
    let html = '';
    const rootZones = zonesByParent[0] || [];
    
    function renderWithChildren(zone, level, parentPath) {
        html += renderZoneRow(zone, level, parentPath);
        const zonePath = parentPath + '_' + zone.id;
        const children = zonesByParent[zone.id] || [];
        children.forEach(child => {
            renderWithChildren(child, level + 1, zonePath);
        });
    }
    
    rootZones.forEach(zone => {
        renderWithChildren(zone, 0, '');
    });
    
    document.getElementById('zonesTableBody').innerHTML = html;
    
    // Collapse all children by default
    collapseAll();
}

function toggleZone(zonePath) {
    const icon = event.currentTarget;
    const isExpanded = icon.classList.contains('expanded');
    
    icon.classList.toggle('expanded');
    
    const rows = document.querySelectorAll('tr.zone-row');
    rows.forEach(row => {
        const rowPath = row.dataset.path;
        if (rowPath && rowPath.startsWith(zonePath + '_')) {
            if (isExpanded) {
                row.classList.add('collapsed');
                const nestedIcon = row.querySelector('.toggle-icon');
                if (nestedIcon) {
                    nestedIcon.classList.remove('expanded');
                }
            } else {
                const pathParts = rowPath.substring(zonePath.length + 1).split('_');
                if (pathParts.length === 1) {
                    row.classList.remove('collapsed');
                }
            }
        }
    });
}

function expandAll() {
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.add('expanded');
    });
    document.querySelectorAll('.zone-row').forEach(row => {
        row.classList.remove('collapsed');
    });
}

function collapseAll() {
    document.querySelectorAll('.toggle-icon').forEach(icon => {
        icon.classList.remove('expanded');
    });
    document.querySelectorAll('.zone-row').forEach((row, index) => {
        if (parseInt(row.dataset.level) > 0) {
            row.classList.add('collapsed');
        }
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    renderZones();
});
</script>
<?= $this->endSection() ?>
