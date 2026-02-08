<?= $this->extend('layouts/admin_modern') ?>

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

<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtres</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Agence</label>
                <select class="form-select" id="filterAgency">
                    <option value="">Toutes les agences</option>
                    <?php foreach ($agencies as $agency): ?>
                        <option value="<?= $agency['id'] ?>"><?= esc($agency['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Agent</label>
                <select class="form-select" id="filterAgent">
                    <option value="">Tous les agents</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?= $agent['id'] ?>"><?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select class="form-select" id="filterStatus">
                    <option value="">Tous les statuts</option>
                    <option value="draft">Brouillon</option>
                    <option value="published">Publié</option>
                    <option value="reserved">Réservé</option>
                    <option value="sold">Vendu</option>
                    <option value="rented">Loué</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select class="form-select" id="filterType">
                    <option value="">Tous les types</option>
                    <option value="apartment">Appartement</option>
                    <option value="villa">Villa</option>
                    <option value="house">Maison</option>
                    <option value="land">Terrain</option>
                    <option value="commercial">Commercial</option>
                    <option value="office">Bureau</option>
                </select>
            </div>
        </div>
    </div>
</div>

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
            <table class="table table-hover" id="propertiesTable">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                        </th>
                        <th>Référence</th>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Agence</th>
                        <th>Agent</th>
                        <th>Prix</th>
                        <th>Statut</th>
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
                            <td><strong><?= number_format($property['price'], 0, ',', ' ') ?> TND</strong></td>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Gestion de la sélection
function updateSelection() {
    const checkboxes = document.querySelectorAll('.property-checkbox:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length;
    document.getElementById('executeBtn').disabled = checkboxes.length === 0;
}

function selectAll() {
    document.querySelectorAll('.property-checkbox').forEach(cb => cb.checked = true);
    document.getElementById('selectAllCheckbox').checked = true;
    updateSelection();
}

function deselectAll() {
    document.querySelectorAll('.property-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
    updateSelection();
}

document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    document.querySelectorAll('.property-checkbox').forEach(cb => cb.checked = this.checked);
    updateSelection();
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

// Filtres
['filterAgency', 'filterAgent', 'filterStatus', 'filterType'].forEach(filterId => {
    document.getElementById(filterId).addEventListener('change', applyFilters);
});

function applyFilters() {
    const agencyFilter = document.getElementById('filterAgency').value;
    const agentFilter = document.getElementById('filterAgent').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const typeFilter = document.getElementById('filterType').value;
    
    document.querySelectorAll('#propertiesTable tbody tr').forEach(row => {
        const agency = row.dataset.agency;
        const agent = row.dataset.agent;
        const status = row.dataset.status;
        const type = row.dataset.type;
        
        const showAgency = !agencyFilter || agency === agencyFilter;
        const showAgent = !agentFilter || agent === agentFilter;
        const showStatus = !statusFilter || status === statusFilter;
        const showType = !typeFilter || type === typeFilter;
        
        row.style.display = (showAgency && showAgent && showStatus && showType) ? '' : 'none';
    });
}

// Exécution de l'action
function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    if (!action) {
        alert('Veuillez sélectionner une action');
        return;
    }
    
    const selectedIds = Array.from(document.querySelectorAll('.property-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Veuillez sélectionner au moins un bien');
        return;
    }
    
    let value = null;
    let confirmMsg = `Voulez-vous vraiment appliquer cette action à ${selectedIds.length} bien(s) ?`;
    
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
        confirmMsg = `⚠️ ATTENTION : Voulez-vous vraiment SUPPRIMER ${selectedIds.length} bien(s) ?\nCette action est IRRÉVERSIBLE !`;
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
            property_ids: selectedIds,
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
