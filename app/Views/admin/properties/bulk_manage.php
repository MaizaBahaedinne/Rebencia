<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.bootstrap5.min.css">
<style>
    .dt-button {
        margin: 0 2px !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
    }
    .filters-card {
        border: 2px solid #0d6efd;
        box-shadow: 0 4px 6px rgba(13, 110, 253, 0.1);
    }
    .filters-card .card-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        font-weight: 600;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tasks text-primary"></i> Gestion en Masse des Biens
    </h1>
    <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>



<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-check-square me-2"></i>
                    Sélection : <span id="selectedCount">0</span> bien(s)
                </h5>
            </div>
            <div>
                <button class="btn btn-sm btn-light me-2" onclick="selectAll()">
                    <i class="fas fa-check-double"></i> Tout sélectionner
                </button>
                <button class="btn btn-sm btn-light" onclick="deselectAll()">
                    <i class="fas fa-times"></i> Tout désélectionner
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Actions en masse -->
        <div class="row g-3 mb-4 p-3 bg-light rounded">
            <div class="col-md-3">
                <label class="form-label">Action</label>
                <select class="form-select" id="bulkAction">
                    <option value="">-- Choisir une action --</option>
                    <option value="change_status">Changer le statut</option>
                    <option value="change_agency">Changer l'agence</option>
                    <option value="change_agent">Changer l'agent</option>
                    <option value="set_featured">Mettre en vedette</option>
                    <option value="unset_featured">Retirer de la vedette</option>
                    <option value="delete">Supprimer</option>
                </select>
            </div>
            <div class="col-md-3" id="statusField" style="display: none;">
                <label class="form-label">Nouveau statut</label>
                <select class="form-select" id="newStatus">
                    <option value="draft">Brouillon</option>
                    <option value="published">Publié</option>
                    <option value="reserved">Réservé</option>
                    <option value="sold">Vendu</option>
                    <option value="rented">Loué</option>
                </select>
            </div>
            <div class="col-md-3" id="agencyField" style="display: none;">
                <label class="form-label">Nouvelle agence</label>
                <select class="form-select" id="newAgency">
                    <option value="">-- Choisir --</option>
                    <?php foreach ($agencies as $agency): ?>
                        <option value="<?= $agency['id'] ?>"><?= esc($agency['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3" id="agentField" style="display: none;">
                <label class="form-label">Nouvel agent</label>
                <select class="form-select" id="newAgent">
                    <option value="">-- Choisir --</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?= $agent['id'] ?>"><?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-warning w-100" onclick="executeBulkAction()" id="executeBtn" disabled>
                    <i class="fas fa-bolt"></i> Exécuter
                </button>
            </div>
        </div>

        <!-- Liste des biens -->
        <div class="table-responsive">
            <table class="table table-hover table-sm" id="propertiesTable">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                        </th>
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
                        <th class="no-filter">Date publication</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): ?>
                        <tr data-property-id="<?= $property['id'] ?>" 
                            data-agency="<?= $property['agency_id'] ?? '' ?>" 
                            data-agent="<?= $property['agent_id'] ?? '' ?>"
                            data-status="<?= $property['status'] ?>"
                            data-type="<?= $property['type'] ?>">
                            <td>
                                <input type="checkbox" class="form-check-input property-checkbox" 
                                       value="<?= $property['id'] ?>" onchange="updateSelection()">
                            </td>
                            <td><strong><?= esc($property['reference']) ?></strong></td>
                            <td>
                                <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" target="_blank">
                                    <?= esc($property['title']) ?>
                                </a>
                            </td>
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
                            <td><?= $property['published_at'] ? date('d/m/Y', strtotime($property['published_at'])) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/datatable-filters.js') ?>"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
let dataTable;
let selectedPropertyIds = [];

// Initialiser DataTables avec filtres
document.addEventListener('DOMContentLoaded', function() {
    dataTable = initDataTableWithFilters('propertiesTable', {
        dom: '<"row mb-3"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Colonnes',
                className: 'btn btn-sm btn-outline-secondary me-1',
                columns: ':not(:first-child)'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-outline-primary me-1',
                exportOptions: {
                    columns: ':visible:not(:first-child)'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-outline-success me-1',
                exportOptions: {
                    columns: ':visible:not(:first-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimer',
                className: 'btn btn-sm btn-outline-info',
                exportOptions: {
                    columns: ':visible:not(:first-child)'
                }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
        colReorder: {
            fixedColumnsLeft: 1
        },
        stateSave: true,
        stateSaveCallback: function(settings, data) {
            localStorage.setItem('DataTables_bulkManage_' + settings.sInstance, JSON.stringify(data));
        },
        stateLoadCallback: function(settings) {
            return JSON.parse(localStorage.getItem('DataTables_bulkManage_' + settings.sInstance));
        },
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            },
            {
                targets: [15, 16, 17, 18],
                orderable: true,
                searchable: false
            }
        ],
        drawCallback: function() {
            selectedPropertyIds.forEach(id => {
                $(`.property-checkbox[value="${id}"]`).prop('checked', true);
            });
            updateSelection();
        }
    });
    
    $('#propertiesTable').on('change', '.property-checkbox', function() {
        const id = parseInt($(this).val());
        if ($(this).is(':checked')) {
            if (!selectedPropertyIds.includes(id)) {
                selectedPropertyIds.push(id);
            }
        } else {
            selectedPropertyIds = selectedPropertyIds.filter(i => i !== id);
        }
        updateSelection();
    });
});

// Gestion de la sélection
function updateSelection() {
    document.getElementById('selectedCount').textContent = selectedPropertyIds.length;
    document.getElementById('executeBtn').disabled = selectedPropertyIds.length === 0;
}

function selectAll() {
    dataTable.rows({page: 'current'}).every(function() {
        const checkbox = $(this.node()).find('.property-checkbox');
        if (checkbox.length) {
            const id = parseInt(checkbox.val());
            checkbox.prop('checked', true);
            if (!selectedPropertyIds.includes(id)) {
                selectedPropertyIds.push(id);
            }
        }
    });
    document.getElementById('selectAllCheckbox').checked = true;
    updateSelection();
}

function deselectAll() {
    dataTable.rows({page: 'current'}).every(function() {
        const checkbox = $(this.node()).find('.property-checkbox');
        if (checkbox.length) {
            const id = parseInt(checkbox.val());
            checkbox.prop('checked', false);
            selectedPropertyIds = selectedPropertyIds.filter(i => i !== id);
        }
    });
    document.getElementById('selectAllCheckbox').checked = false;
    updateSelection();
}

document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    if (this.checked) {
        selectAll();
    } else {
        deselectAll();
    }
});

// Gestion des champs d'action
document.getElementById('bulkAction').addEventListener('change', function() {
    document.getElementById('statusField').style.display = 'none';
    document.getElementById('agencyField').style.display = 'none';
    document.getElementById('agentField').style.display = 'none';
    
    if (this.value === 'change_status') {
        document.getElementById('statusField').style.display = 'block';
    } else if (this.value === 'change_agency') {
        document.getElementById('agencyField').style.display = 'block';
    } else if (this.value === 'change_agent') {
        document.getElementById('agentField').style.display = 'block';
    }
});

// Exécution de l'action
function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    if (!action) {
        alert('Veuillez sélectionner une action');
        return;
    }
    
    if (selectedPropertyIds.length === 0) {
        alert('Veuillez sélectionner au moins un bien');
        return;
    }
    
    let value = null;
    let confirmMsg = `Voulez-vous vraiment appliquer cette action à ${selectedPropertyIds.length} bien(s) ?`;
    
    if (action === 'change_status') {
        value = document.getElementById('newStatus').value;
    } else if (action === 'change_agency') {
        value = document.getElementById('newAgency').value;
        if (!value) {
            alert('Veuillez sélectionner une agence');
            return;
        }
    } else if (action === 'change_agent') {
        value = document.getElementById('newAgent').value;
        if (!value) {
            alert('Veuillez sélectionner un agent');
            return;
        }
    } else if (action === 'delete') {
        confirmMsg = `⚠️ ATTENTION : Voulez-vous vraiment SUPPRIMER ${selectedPropertyIds.length} bien(s) ?\nCette action est IRRÉVERSIBLE !`;
    }
    
    if (!confirm(confirmMsg)) {
        return;
    }
    
    // Envoi de la requête
    fetch('<?= base_url('admin/properties/bulk-action') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            property_ids: selectedPropertyIds,
            action: action,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
}
</script>
<?= $this->endSection() ?>
