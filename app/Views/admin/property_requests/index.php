<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-envelope-open-text text-primary"></i> Demandes Clients
            </h1>
            <p class="text-muted">Gérez les demandes d'information et de visite</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i class="fas fa-envelope fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $stats['total'] ?></h3>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded p-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $stats['pending'] ?></h3>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <i class="fas fa-calendar-check fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $stats['visits'] ?></h3>
                            <small class="text-muted">Visites</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-purple bg-opacity-10 rounded p-3">
                                <i class="fas fa-info-circle fa-2x text-purple"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $stats['information'] ?></h3>
                            <small class="text-muted">Infos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-secondary bg-opacity-10 rounded p-3">
                                <i class="fas fa-calculator fa-2x text-secondary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $stats['estimation'] ?></h3>
                            <small class="text-muted">Estimations</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $stats['completed'] ?></h3>
                            <small class="text-muted">Complétées</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                        <option value="contacted" <?= $filters['status'] === 'contacted' ? 'selected' : '' ?>>Contacté</option>
                        <option value="scheduled" <?= $filters['status'] === 'scheduled' ? 'selected' : '' ?>>Planifié</option>
                        <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Complété</option>
                        <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="visit" <?= $filters['type'] === 'visit' ? 'selected' : '' ?>>Visite</option>
                        <option value="information" <?= $filters['type'] === 'information' ? 'selected' : '' ?>>Information</option>
                        <option value="estimation" <?= $filters['type'] === 'estimation' ? 'selected' : '' ?>>Estimation</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" name="date_from" class="form-control" value="<?= $filters['date_from'] ?>">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" name="date_to" class="form-control" value="<?= $filters['date_to'] ?>">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="requestsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Client</th>
                            <th>Téléphone</th>
                            <th>Propriété</th>
                            <th>Statut</th>
                            <th>Agent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune demande trouvée
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td data-order="<?= strtotime($request['created_at']) ?>">
                                        <?= date('d/m/Y', strtotime($request['created_at'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($request['request_type'] === 'visit'): ?>
                                            <span class="badge bg-info"><i class="fas fa-calendar-check me-1"></i>Visite</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary"><i class="fas fa-info-circle me-1"></i>Info</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($request['first_name'] . ' ' . $request['last_name']) ?></td>
                                    <td><?= esc($request['phone']) ?></td>
                                    <td><?= esc($request['title']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= ['pending'=>'warning','contacted'=>'info','scheduled'=>'primary','completed'=>'success','cancelled'=>'danger'][$request['status']] ?>">
                                            <?= ['pending'=>'En attente','contacted'=>'Contacté','scheduled'=>'Planifié','completed'=>'Complété','cancelled'=>'Annulé'][$request['status']] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($request['assigned_to']): ?>
                                            <?= esc($request['agent_first_name'] . ' ' . $request['agent_last_name']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="<?= base_url('admin/property-requests/view/' . $request['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-success" onclick="assignAgent(<?= $request['id'] ?>)">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $request['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Agent Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner un agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="requestId">
                <div class="mb-3">
                    <label class="form-label">Sélectionner un agent</label>
                    <select id="agentSelect" class="form-select">
                        <option value="">Choisir un agent...</option>
                        <?php foreach ($agents as $agent): ?>
                            <option value="<?= $agent['id'] ?>">
                                <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitAssign()">Assigner</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// DataTable with proper column configuration
$(document).ready(function() {
    // Only initialize DataTables if there are rows
    const tableBody = $('#requestsTable tbody tr');
    const hasData = tableBody.length > 0 && !tableBody.first().find('td[colspan]').length;
    
    if ($.fn.DataTable && hasData) {
        $('#requestsTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25,
            columnDefs: [
                { orderable: false, targets: [7] } // Actions column not sortable
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            }
        });
    }
});

// Assign Agent
function assignAgent(requestId) {
    document.getElementById('requestId').value = requestId;
    new bootstrap.Modal(document.getElementById('assignModal')).show();
}

function submitAssign() {
    const requestId = document.getElementById('requestId').value;
    const agentId = document.getElementById('agentSelect').value;
    
    if (!agentId) {
        alert('Veuillez sélectionner un agent');
        return;
    }
    
    fetch('<?= base_url('admin/property-requests/assign') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            id: requestId,
            agent_id: agentId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

// Delete Confirmation
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')) {
        window.location.href = '<?= base_url('admin/property-requests/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
