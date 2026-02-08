<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.bootstrap5.min.css">
<style>
    .datatable-filter {
        position: relative;
        z-index: 1;
    }
    .datatable-filter select,
    .datatable-filter input {
        font-size: 11px;
        padding: 2px 5px;
        height: 28px;
    }
    .split-view-wrapper {
        position: relative;
        height: calc(100vh - 160px);
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin: 0;
    }
    .split-view-container {
        display: flex;
        height: 100%;
        position: relative;
    }
    .list-panel, .map-panel {
        height: 100%;
        transition: all 0.3s ease;
    }
    .list-panel {
        width: 50%;
        overflow-y: auto;
        background: white;
        position: relative;
        z-index: 1;
        padding: 0;
    }
    .map-panel {
        width: 50%;
        position: relative;
    }
    .list-panel.fullscreen {
        width: 100%;
    }
    .map-panel.fullscreen {
        width: 100%;
    }
    .list-panel.hidden, .map-panel.hidden {
        width: 0;
        overflow: hidden;
    }
    #propertiesMap {
        width: 100%;
        height: 100%;
    }
    .resize-handle {
        width: 4px;
        cursor: ew-resize;
        background: linear-gradient(90deg, #e5e7eb 0%, #cbd5e1 50%, #e5e7eb 100%);
        transition: all 0.3s;
        position: relative;
        z-index: 10;
        flex-shrink: 0;
    }
    .resize-handle:hover {
        background: #3b82f6;
        width: 6px;
    }
    .resize-handle.hidden {
        display: none;
    }
    .view-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        display: flex;
        gap: 5px;
    }
    .view-toggle-btn {
        background: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
    }
    .view-toggle-btn:hover {
        background: #f3f4f6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .view-toggle-btn.active {
        background: #3b82f6;
        color: white;
    }
    .property-row {
        cursor: pointer;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .property-row:hover {
        border-left-color: #3b82f6;
        background: #f9fafb;
    }
    .property-row.active {
        border-left-color: #10b981;
        background: #f0fdf4;
    }
    .table-responsive {
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #propertiesTable {
        margin: 0 !important;
    }
    #propertiesTable thead th {
        padding: 12px 8px;
        font-size: 13px;
        background-color: white !important;
    }
    #propertiesTable tbody td {
        padding: 10px 8px;
        font-size: 13px;
    }
    .table-light {
        background-color: white !important;
    }
    #propertiesTable thead tr {
        background-color: white !important;
    }
    #propertiesTable tfoot th {
        background-color: white !important;
        border-top: 2px solid #dee2e6 !important;
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
    .dt-button {
        margin: 0 2px !important;
    }
    #resetFiltersBtn {
        margin-left: 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-building"></i> Gestion des Biens Immobiliers
    </h1>
    <div class="d-flex gap-2">
        <!-- Debug: Role Level = <?= $currentRoleLevel ?? 'non défini' ?> -->
        <?php if (isset($currentRoleLevel) && $currentRoleLevel == 100): ?>
            <a href="<?= base_url('admin/properties/bulk-manage') ?>" class="btn btn-outline-primary">
                <i class="fas fa-tasks"></i> Gestion en masse
            </a>
        <?php endif; ?>
        <?php if (canCreate('properties')): ?>
        <a href="<?= base_url('admin/properties/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Bien
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="split-view-wrapper">
    <div class="view-controls">
        <button class="view-toggle-btn" onclick="toggleView('split')" title="Vue partagée">
            <i class="fas fa-columns"></i>
        </button>
        <button class="view-toggle-btn" onclick="toggleView('table')" title="Tableau plein écran">
            <i class="fas fa-table"></i>
        </button>
        <button class="view-toggle-btn" onclick="toggleView('map')" title="Carte plein écran">
            <i class="fas fa-map"></i>
        </button>
    </div>

    <div class="split-view-container">
        <div class="list-panel">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-sm" id="propertiesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Transaction</th>
                                <th>Prix Vente</th>
                                <th>Prix Location</th>
                                <th>Surface (m²)</th>
                                <th>Chambres</th>
                                <th>Salles de bain</th>
                                <th>Zone</th>
                                <th>Ville</th>
                                <th>Adresse</th>
                                <th>Agence</th>
                                <th>Agent</th>
                                <th>Statut</th>
                                <th class="no-filter">En vedette</th>
                                <th class="no-filter">Vues</th>
                                <th class="no-filter">Date création</th>
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
                                            <?php
                                            $types = [
                                                'apartment' => 'Appartement',
                                                'villa' => 'Villa',
                                                'house' => 'Maison',
                                                'land' => 'Terrain',
                                                'commercial' => 'Commercial',
                                                'office' => 'Bureau'
                                            ];
                                            ?>
                                            <span class="badge bg-info"><?= $types[$property['type']] ?? $property['type'] ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $transactions = [
                                                'sale' => 'Vente',
                                                'rent' => 'Location',
                                                'both' => 'Vente/Location'
                                            ];
                                            ?>
                                            <?= $transactions[$property['transaction_type']] ?? $property['transaction_type'] ?>
                                        </td>
                                        <td><?= $property['price'] ? number_format($property['price'], 0, ',', ' ') . ' TND' : '-' ?></td>
                                        <td><?= $property['rental_price'] ? number_format($property['rental_price'], 0, ',', ' ') . ' TND' : '-' ?></td>
                                        <td><?= $property['area_total'] ?? '-' ?></td>
                                        <td><?= $property['bedrooms'] ?? '-' ?></td>
                                        <td><?= $property['bathrooms'] ?? '-' ?></td>
                                        <td><?= esc($property['zone_name'] ?? '-') ?></td>
                                        <td><?= esc($property['city'] ?? '-') ?></td>
                                        <td><?= esc($property['address'] ?? '-') ?></td>
                                        <td>
                                            <?php if (!empty($property['agency_name'])): ?>
                                                <span class="badge bg-primary"><?= esc($property['agency_name']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($property['agent_name'])): ?>
                                                <?= esc($property['agent_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadges = [
                                                'draft' => 'secondary',
                                                'published' => 'success',
                                                'reserved' => 'warning',
                                                'sold' => 'danger',
                                                'rented' => 'info'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusBadges[$property['status']] ?? 'secondary' ?>">
                                                <?= ucfirst($property['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($property['featured']): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= number_format($property['views_count'] ?? 0) ?></td>
                                        <td><?= date('d/m/Y', strtotime($property['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" class="btn btn-sm btn-info" onclick="event.stopPropagation()">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($currentRoleLevel == 100 || in_array($property['agent_id'], $editableUserIds)): ?>
                                                <a href="<?= base_url('admin/properties/edit/' . $property['id']) ?>" class="btn btn-sm btn-warning" onclick="event.stopPropagation()">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="19" class="text-center py-4 text-muted">
                                        Aucune propriété trouvée
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
            </div>
        </div>
        
        <div class="resize-handle" id="resizeHandle"></div>
        
        <div class="map-panel">
            <div id="propertiesMap"></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?= base_url('assets/js/datatable-filters.js') ?>"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
let map;
let markers = {};
let dataTable;
const propertiesData = <?= json_encode($properties) ?>;
const currentRoleLevel = <?= $currentRoleLevel ?>;
const editableUserIds = <?= json_encode($editableUserIds) ?>;

// Initialisation
$(document).ready(function() {
    // Initialiser DataTables avec filtres
    dataTable = initDataTableWithFilters('propertiesTable', {
        dom: '<"row mb-3"<"col-sm-12 col-md-auto"B><"col-sm-12 col-md-auto"<"btn btn-secondary btn-sm" id="resetFiltersBtn">><"col-sm-12 col-md"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Colonnes',
                className: 'btn btn-sm btn-outline-secondary me-1'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-outline-primary me-1',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-outline-success me-1',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimer',
                className: 'btn btn-sm btn-outline-info',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
        colReorder: true,
        stateSave: true,
        stateSaveCallback: function(settings, data) {
            localStorage.setItem('DataTables_properties_' + settings.sInstance, JSON.stringify(data));
        },
        stateLoadCallback: function(settings) {
            return JSON.parse(localStorage.getItem('DataTables_properties_' + settings.sInstance));
        },
        columnDefs: [
            {
                targets: [15, 16, 17, 18],
                orderable: true,
                searchable: false
            }
        ]
    });
    
    // Bouton Reset Filtres
    $('#resetFiltersBtn').html('<i class="fas fa-redo"></i> Reset Filtres').on('click', function() {
        // Effacer le cache localStorage
        localStorage.removeItem('DataTables_properties_propertiesTable');
        
        // Réinitialiser tous les filtres de colonnes
        dataTable.columns().every(function() {
            const column = this;
            $('input', column.footer()).val('');
            $('select', column.footer()).val('');
        });
        // Effacer la recherche globale
        dataTable.search('').columns().search('').draw();
        // Message de confirmation
        const btn = $(this);
        btn.html('<i class="fas fa-check"></i> Réinitialisé!');
        setTimeout(() => {
            btn.html('<i class="fas fa-redo"></i> Reset Filtres');
        }, 1500);
    });
    
    initMap();
    
    // Définir la vue table comme vue par défaut
    setTimeout(() => {
        toggleView('table');
        // Activer le bouton table
        document.querySelectorAll('.view-toggle-btn')[1].classList.add('active');
    }, 100);
});

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
                        ${(currentRoleLevel == 100 || editableUserIds.includes(property.agent_id)) ? 
                            `<a href="/admin/properties/edit/${property.id}" class="btn btn-sm btn-warning w-100">
                                <i class="fas fa-edit"></i> Modifier
                            </a>` : ''}
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
</script>
<?= $this->endSection() ?>
