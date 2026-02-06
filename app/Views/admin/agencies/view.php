<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building me-2"></i><?= esc($agency['name']) ?>
        </h1>
        <div>
            <a href="<?= base_url('admin/agencies/edit/' . $agency['id']) ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="<?= base_url('admin/agencies') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-6 mb-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Sous-agences</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($subAgencies ?? []) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Utilisateurs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($users) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Biens</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($properties) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Clients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($clients) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Transactions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($transactions) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="agencyTabs" role="tablist">
        <?php if (!empty($subAgencies)): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="subagencies-tab" data-bs-toggle="tab" data-bs-target="#subagencies" type="button" role="tab">
                <i class="fas fa-sitemap me-2"></i>Sous-agences (<?= count($subAgencies) ?>)
            </button>
        </li>
        <?php endif; ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= empty($subAgencies) ? 'active' : '' ?>" id="properties-tab" data-bs-toggle="tab" data-bs-target="#properties" type="button" role="tab">
                <i class="fas fa-building me-2"></i>Biens (<?= count($properties) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients" type="button" role="tab">
                <i class="fas fa-user-friends me-2"></i>Clients (<?= count($clients) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                <i class="fas fa-handshake me-2"></i>Transactions (<?= count($transactions) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                <i class="fas fa-users me-2"></i>Utilisateurs (<?= count($users) ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="agencyTabsContent">
        <!-- Sous-agences Tab -->
        <?php if (!empty($subAgencies)): ?>
        <div class="tab-pane fade show active" id="subagencies" role="tabpanel">
            <div class="card shadow mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>Agences Dépendantes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($subAgencies as $subAgency): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-left-secondary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <?php if (!empty($subAgency['logo'])): ?>
                                            <img src="<?= base_url('uploads/agencies/' . $subAgency['logo']) ?>" 
                                                 alt="Logo" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        <?php else: ?>
                                            <div class="me-3" style="width: 50px; height: 50px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-building fa-2x text-secondary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1"><?= esc($subAgency['name']) ?></h6>
                                            <small class="text-muted">Code: <?= esc($subAgency['code']) ?></small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-<?= $subAgency['type'] === 'siege' ? 'primary' : 'success' ?>">
                                            <?= $subAgency['type'] === 'siege' ? 'Siège' : 'Agence' ?>
                                        </span>
                                        <span class="badge bg-<?= $subAgency['status'] === 'active' ? 'success' : 'secondary' ?> ms-1">
                                            <?= $subAgency['status'] === 'active' ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </div>
                                    
                                    <div class="small text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?= esc($subAgency['city'] ?? 'Non renseigné') ?>
                                    </div>
                                    
                                    <?php if (!empty($subAgency['phone'])): ?>
                                    <div class="small text-muted mb-2">
                                        <i class="fas fa-phone me-1"></i>
                                        <?= esc($subAgency['phone']) ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="<?= base_url('admin/agencies/view/' . $subAgency['id']) ?>" 
                                           class="btn btn-sm btn-info flex-fill">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </a>
                                        <a href="<?= base_url('admin/agencies/edit/' . $subAgency['id']) ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Biens Tab -->
        <div class="tab-pane fade <?= empty($subAgencies) ? 'show active' : '' ?>" id="properties" role="tabpanel">
            <div class="card shadow mt-3">
                <div class="card-body">
                    <?php if (!empty($properties)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="propertiesTable">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Titre</th>
                                        <th>Type</th>
                                        <th>Zone</th>
                                        <th>Prix</th>
                                        <th>Agent</th>
                                        <th>Statut</th>
                                        <th class="no-filter">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($properties as $property): ?>
                                        <tr>
                                            <td><strong><?= esc($property['reference']) ?></strong></td>
                                            <td><?= esc($property['title']) ?></td>
                                            <td><span class="badge bg-info"><?= esc($property['type']) ?></span></td>
                                            <td><?= esc($property['zone_name'] ?? '-') ?></td>
                                            <td><strong><?= number_format($property['price'], 0, ',', ' ') ?> TND</strong></td>
                                            <td><?= esc($property['agent_name'] . ' ' . ($property['agent_lastname'] ?? '')) ?></td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'published' => 'success',
                                                    'reserved' => 'warning',
                                                    'sold' => 'danger'
                                                ];
                                                $color = $statusColors[$property['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= ucfirst($property['status']) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/properties/edit/' . $property['id']) ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun bien pour cette agence</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Clients Tab -->
        <div class="tab-pane fade" id="clients" role="tabpanel">
            <div class="card shadow mt-3">
                <div class="card-body">
                    <?php if (!empty($clients)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="clientsTable">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Type</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Agent</th>
                                        <th>Date création</th>
                                        <th class="no-filter">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clients as $client): ?>
                                        <tr>
                                            <td><strong><?= esc($client['first_name'] . ' ' . $client['last_name']) ?></strong></td>
                                            <td>
                                                <?php if ($client['type'] === 'individual'): ?>
                                                    <span class="badge bg-primary">Particulier</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Entreprise</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($client['email'] ?? '-') ?></td>
                                            <td><?= esc($client['phone'] ?? '-') ?></td>
                                            <td><?= esc($client['agent_name'] . ' ' . ($client['agent_lastname'] ?? '')) ?></td>
                                            <td><?= date('d/m/Y', strtotime($client['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('admin/clients/view/' . $client['id']) ?>" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/clients/edit/' . $client['id']) ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun client pour cette agence</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Transactions Tab -->
        <div class="tab-pane fade" id="transactions" role="tabpanel">
            <div class="card shadow mt-3">
                <div class="card-body">
                    <?php if (!empty($transactions)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="transactionsTable">
                                <thead>
                                    <tr>
                                        <th>Bien</th>
                                        <th>Client</th>
                                        <th>Agent</th>
                                        <th>Montant</th>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th class="no-filter">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($transaction['property_ref']) ?></strong><br>
                                                <small class="text-muted"><?= esc($transaction['property_title']) ?></small>
                                            </td>
                                            <td><?= esc($transaction['client_name'] ?? '-') ?></td>
                                            <td><?= esc($transaction['agent_name'] ?? '-') ?></td>
                                            <td><strong><?= number_format($transaction['amount'], 0, ',', ' ') ?> TND</strong></td>
                                            <td><span class="badge bg-primary"><?= ucfirst($transaction['type']) ?></span></td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'pending' => 'warning',
                                                    'signed' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $color = $statusColors[$transaction['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= ucfirst($transaction['status']) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/transactions/view/' . $transaction['id']) ?>" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('admin/transactions/edit/' . $transaction['id']) ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune transaction pour cette agence</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Users Tab -->
        <div class="tab-pane fade" id="users" role="tabpanel">
            <div class="card shadow mt-3">
                <div class="card-body">
                    <?php if (!empty($users)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                        <th class="no-filter">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong></td>
                                            <td><?= esc($user['email']) ?></td>
                                            <td><?= esc($user['phone'] ?? '-') ?></td>
                                            <td><span class="badge bg-secondary"><?= esc($user['role_name'] ?? 'N/A') ?></span></td>
                                            <td>
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun utilisateur pour cette agence</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/datatable-filters.js') ?>"></script>
<script>
$(document).ready(function() {
    // Initialize DataTables for all tabs
    if ($('#propertiesTable').length && $('#propertiesTable tbody tr').length > 0) {
        initDataTableWithFilters('propertiesTable', {
            order: [[0, 'desc']],
            pageLength: 25
        });
    }
    
    if ($('#clientsTable').length && $('#clientsTable tbody tr').length > 0) {
        initDataTableWithFilters('clientsTable', {
            order: [[5, 'desc']],
            pageLength: 25
        });
    }
    
    if ($('#transactionsTable').length && $('#transactionsTable tbody tr').length > 0) {
        initDataTableWithFilters('transactionsTable', {
            order: [[0, 'desc']],
            pageLength: 25
        });
    }
    
    if ($('#usersTable').length && $('#usersTable tbody tr').length > 0) {
        initDataTableWithFilters('usersTable', {
            order: [[0, 'asc']],
            pageLength: 25
        });
    }
});
</script>
<?= $this->endSection() ?>
