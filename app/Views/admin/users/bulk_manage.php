<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Utilisateurs</a></li>
        <li class="breadcrumb-item active">Gestion en masse</li>
    </ol>
</nav>

<!-- Page Title -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-users-cog text-primary"></i> Gestion en masse des utilisateurs
    </h1>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<!-- Actions en masse -->
<div class="card mb-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-1">
                    <i class="fas fa-check-square text-primary"></i> 
                    <span id="selectedCount">0</span> utilisateur(s) sélectionné(s)
                </h5>
                <small class="text-muted">Choisissez une action à appliquer</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-sm btn-primary" onclick="showBulkModal('agency')">
                    <i class="fas fa-building"></i> Changer d'agence
                </button>
                <button class="btn btn-sm btn-info" onclick="showBulkModal('manager')">
                    <i class="fas fa-user-tie"></i> Changer de manager
                </button>
                <button class="btn btn-sm btn-secondary" onclick="showBulkModal('role')">
                    <i class="fas fa-user-tag"></i> Changer de rôle
                </button>
                <button class="btn btn-sm btn-success" onclick="bulkUpdateStatus('active')">
                    <i class="fas fa-check-circle"></i> Activer
                </button>
                <button class="btn btn-sm btn-warning" onclick="bulkUpdateStatus('inactive')">
                    <i class="fas fa-ban"></i> Désactiver
                </button>
                <button class="btn btn-sm btn-danger" onclick="showBulkModal('delete')">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Agence</label>
                <select class="form-select" id="filterAgency" onchange="applyFilters()">
                    <option value="">Toutes les agences</option>
                    <?php foreach ($agencies as $agency): ?>
                        <option value="<?= $agency['id'] ?>"><?= esc($agency['name']) ?></option>
                    <?php endforeach ?>
                    <option value="null">Sans agence</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Rôle</label>
                <select class="form-select" id="filterRole" onchange="applyFilters()">
                    <option value="">Tous les rôles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Manager</label>
                <select class="form-select" id="filterManager" onchange="applyFilters()">
                    <option value="">Tous les managers</option>
                    <?php foreach ($managers as $manager): ?>
                        <option value="<?= $manager['id'] ?>">
                            <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?>
                        </option>
                    <?php endforeach ?>
                    <option value="null">Sans manager</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select class="form-select" id="filterStatus" onchange="applyFilters()">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                <i class="fas fa-redo"></i> Réinitialiser
            </button>
        </div>
    </div>
</div>

<!-- Liste des utilisateurs -->
<div class="card">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                <label class="form-check-label fw-bold" for="selectAll">
                    Tout sélectionner
                </label>
            </div>
            <span class="badge bg-primary"><?= count($users) ?> utilisateur(s)</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;"></th>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Agence</th>
                        <th>Manager</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="user-row" 
                            data-user-id="<?= $user['id'] ?>"
                            data-agency-id="<?= $user['agency_id'] ?? 'null' ?>"
                            data-role-id="<?= $user['role_id'] ?>"
                            data-manager-id="<?= $user['manager_id'] ?? 'null' ?>"
                            data-status="<?= $user['status'] ?? 'active' ?>">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input user-checkbox" 
                                           type="checkbox" 
                                           value="<?= $user['id'] ?>"
                                           onchange="updateBulkActions()">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="avatar-initial rounded-circle bg-primary text-white">
                                            <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                        </div>
                                        <small class="text-muted">ID: <?= $user['id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?= esc($user['role_name'] ?? 'Non défini') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['agency_name']): ?>
                                    <i class="fas fa-building text-primary me-1"></i>
                                    <?= esc($user['agency_name']) ?>
                                <?php else: ?>
                                    <span class="text-muted">
                                        <i class="fas fa-minus-circle me-1"></i>
                                        Non affecté
                                    </span>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($user['manager_name']): ?>
                                    <i class="fas fa-user-tie text-success me-1"></i>
                                    <?= esc($user['manager_name']) ?>
                                <?php else: ?>
                                    <span class="text-muted">
                                        <i class="fas fa-minus-circle me-1"></i>
                                        Aucun
                                    </span>
                                <?php endif ?>
                            </td>
                            <td>
                                <small><?= esc($user['email']) ?></small>
                            </td>
                            <td>
                                <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Actif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-ban"></i> Inactif
                                    </span>
                                <?php endif ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Changer d'agence -->
<div class="modal fade" id="bulkAgencyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-building"></i> Changer d'agence
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Affecter <strong><span id="bulkAgencyCount">0</span> utilisateur(s)</strong> à une agence :</p>
                <select class="form-select" id="bulkAgencySelect">
                    <option value="">-- Sélectionner une agence --</option>
                    <?php foreach ($agencies as $agency): ?>
                        <option value="<?= $agency['id'] ?>"><?= esc($agency['name']) ?></option>
                    <?php endforeach ?>
                    <option value="null">Retirer l'agence</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="executeBulkAction('agency')">
                    <i class="fas fa-check"></i> Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Changer de manager -->
<div class="modal fade" id="bulkManagerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-tie"></i> Changer de manager
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Affecter <strong><span id="bulkManagerCount">0</span> utilisateur(s)</strong> à un manager :</p>
                <select class="form-select" id="bulkManagerSelect">
                    <option value="">-- Sélectionner un manager --</option>
                    <?php foreach ($managers as $manager): ?>
                        <option value="<?= $manager['id'] ?>">
                            <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?>
                            (<?= esc($manager['role_name']) ?>)
                        </option>
                    <?php endforeach ?>
                    <option value="null">Retirer le manager</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-info" onclick="executeBulkAction('manager')">
                    <i class="fas fa-check"></i> Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Changer de rôle -->
<div class="modal fade" id="bulkRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-tag"></i> Changer de rôle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Affecter <strong><span id="bulkRoleCount">0</span> utilisateur(s)</strong> à un rôle :</p>
                <select class="form-select" id="bulkRoleSelect">
                    <option value="">-- Sélectionner un rôle --</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= esc($role['name']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-secondary" onclick="executeBulkAction('role')">
                    <i class="fas fa-check"></i> Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Supprimer -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Attention !</strong> Cette action est irréversible.
                </div>
                <p>Êtes-vous sûr de vouloir supprimer <strong><span id="bulkDeleteCount">0</span> utilisateur(s)</strong> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="executeBulkAction('delete')">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    
    .avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }
    
    .user-row:hover {
        background-color: #f8f9fa;
    }
    
    .user-row.selected {
        background-color: #e7f3ff;
    }
    
    #bulkActionsCard {
        position: sticky;
        top: 20px;
        z-index: 100;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: 2px solid #0d6efd;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let selectedUsers = [];

// Toggle select all
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox:not([disabled])');
    const visibleCheckboxes = Array.from(checkboxes).filter(cb => {
        return cb.closest('tr').style.display !== 'none';
    });
    
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

// Update bulk actions
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    selectedUsers = Array.from(checkboxes).map(cb => cb.value);
    
    const selectedCount = document.getElementById('selectedCount');
    const bulkActionsCard = document.getElementById('bulkActionsCard');
    const selectAll = document.getElementById('selectAll');
    
    selectedCount.textContent = selectedUsers.length;
    
    if (selectedUsers.length > 0) {
        bulkActionsCard.style.display = 'block';
        document.querySelectorAll('.user-checkbox:checked').forEach(cb => {
            cb.closest('tr').classList.add('selected');
        });
    } else {
        bulkActionsCard.style.display = 'none';
        selectAll.checked = false;
    }
    
    document.querySelectorAll('.user-checkbox:not(:checked)').forEach(cb => {
        cb.closest('tr').classList.remove('selected');
    });
    
    // Update select all checkbox
    const allVisibleCheckboxes = Array.from(document.querySelectorAll('.user-checkbox')).filter(cb => {
        return cb.closest('tr').style.display !== 'none';
    });
    const allVisibleChecked = allVisibleCheckboxes.every(cb => cb.checked);
    selectAll.checked = allVisibleCheckboxes.length > 0 && allVisibleChecked;
}

// Show bulk modal
function showBulkModal(type) {
    const count = selectedUsers.length;
    
    if (type === 'agency') {
        document.getElementById('bulkAgencyCount').textContent = count;
        new bootstrap.Modal(document.getElementById('bulkAgencyModal')).show();
    } else if (type === 'manager') {
        document.getElementById('bulkManagerCount').textContent = count;
        new bootstrap.Modal(document.getElementById('bulkManagerModal')).show();
    } else if (type === 'role') {
        document.getElementById('bulkRoleCount').textContent = count;
        new bootstrap.Modal(document.getElementById('bulkRoleModal')).show();
    } else if (type === 'delete') {
        document.getElementById('bulkDeleteCount').textContent = count;
        new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
    }
}

// Execute bulk action
function executeBulkAction(type) {
    let data = {
        user_ids: selectedUsers,
        action: type
    };
    
    if (type === 'agency') {
        const agencyId = document.getElementById('bulkAgencySelect').value;
        if (!agencyId) {
            alert('Veuillez sélectionner une agence');
            return;
        }
        data.agency_id = agencyId === 'null' ? null : agencyId;
    } else if (type === 'manager') {
        const managerId = document.getElementById('bulkManagerSelect').value;
        if (!managerId) {
            alert('Veuillez sélectionner un manager');
            return;
        }
        data.manager_id = managerId === 'null' ? null : managerId;
    } else if (type === 'role') {
        const roleId = document.getElementById('bulkRoleSelect').value;
        if (!roleId) {
            alert('Veuillez sélectionner un rôle');
            return;
        }
        data.role_id = roleId;
    }
    
    // Send request
    fetch('<?= base_url('admin/users/bulk-action') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.querySelector('.modal.show'))?.hide();
            
            // Show success message
            alert(result.message || 'Action effectuée avec succès');
            
            // Reload page
            window.location.reload();
        } else {
            alert(result.message || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de l\'exécution de l\'action');
    });
}

// Bulk update status
function bulkUpdateStatus(status) {
    if (confirm(`Voulez-vous vraiment ${status === 'active' ? 'activer' : 'désactiver'} ${selectedUsers.length} utilisateur(s) ?`)) {
        let data = {
            user_ids: selectedUsers,
            action: 'status',
            status: status
        };
        
        // Send request
        fetch('<?= base_url('admin/users/bulk-action') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message || 'Action effectuée avec succès');
                window.location.reload();
            } else {
                alert(result.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de l\'exécution de l\'action');
        });
    }
}

// Apply filters
function applyFilters() {
    const filterAgency = document.getElementById('filterAgency').value;
    const filterRole = document.getElementById('filterRole').value;
    const filterManager = document.getElementById('filterManager').value;
    const filterStatus = document.getElementById('filterStatus').value;
    
    const rows = document.querySelectorAll('.user-row');
    
    rows.forEach(row => {
        let show = true;
        
        if (filterAgency && row.dataset.agencyId !== filterAgency) {
            show = false;
        }
        
        if (filterRole && row.dataset.roleId !== filterRole) {
            show = false;
        }
        
        if (filterManager && row.dataset.managerId !== filterManager) {
            show = false;
        }
        
        if (filterStatus && row.dataset.status !== filterStatus) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    updateBulkActions();
}

// Reset filters
function resetFilters() {
    document.getElementById('filterAgency').value = '';
    document.getElementById('filterRole').value = '';
    document.getElementById('filterManager').value = '';
    document.getElementById('filterStatus').value = '';
    
    applyFilters();
}
</script>
<?= $this->endSection() ?>
