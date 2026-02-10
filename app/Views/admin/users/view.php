<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<style>
    .user-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .user-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        object-fit: cover;
    }
    .user-avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid white;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #667eea;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .stat-card-compact {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    .stat-card-compact:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }
    .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        color: white;
    }
    .info-row i {
        width: 20px;
        opacity: 0.9;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Utilisateurs</a></li>
            <li class="breadcrumb-item active"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></li>
        </ol>
    </nav>

    <!-- User Header -->
    <div class="user-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= base_url('uploads/avatars/' . $user['avatar']) ?>" alt="Avatar" class="user-avatar-large">
                <?php else: ?>
                    <div class="user-avatar-placeholder">
                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col">
                <h2 class="mb-2"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <i class="fas fa-user-tag"></i>
                            <strong><?= esc($user['role_name']) ?></strong>
                            <span class="badge bg-light text-dark ms-2">Niveau <?= $user['role_level'] ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-building"></i>
                            <span><?= esc($user['agency_name'] ?? 'Aucune agence') ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-envelope"></i>
                            <span><?= esc($user['email']) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <i class="fas fa-phone"></i>
                            <span><?= esc($user['phone'] ?? 'Non renseigné') ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-calendar"></i>
                            <span>Inscrit le <?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-circle"></i>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge bg-success">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactif</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="stat-card-compact">
                <i class="fas fa-home fa-2x text-primary"></i>
                <div class="stat-number text-primary"><?= count($properties) ?></div>
                <div class="stat-label">Biens</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-compact">
                <i class="fas fa-users fa-2x text-info"></i>
                <div class="stat-number text-info"><?= count($clients) ?></div>
                <div class="stat-label">Clients</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-compact">
                <i class="fas fa-exchange-alt fa-2x text-success"></i>
                <div class="stat-number text-success"><?= count($transactions) ?></div>
                <div class="stat-label">Transactions</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-compact">
                <i class="fas fa-calendar-check fa-2x text-warning"></i>
                <div class="stat-number text-warning"><?= count($appointments) ?></div>
                <div class="stat-label">Rendez-vous</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-compact">
                <i class="fas fa-coins fa-2x text-danger"></i>
                <div class="stat-number text-danger"><?= count($commissions) ?></div>
                <div class="stat-label">Commissions</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-compact">
                <i class="fas fa-euro-sign fa-2x text-secondary"></i>
                <div class="stat-number text-secondary">
                    <?php 
                    $totalCommissions = 0;
                    foreach ($commissions as $comm) {
                        $totalCommissions += $comm['agent_commission_amount'] ?? 0;
                    }
                    echo number_format($totalCommissions, 0, ',', ' ');
                    ?>
                </div>
                <div class="stat-label">Total TND</div>
            </div>
        </div>
    </div>

    <!-- Team Members Section (Manager/Admin only) -->
    <?php if (!empty($team_members)): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-users-cog text-primary"></i>
                Mon Équipe (<?= count($team_members) ?> membres actifs)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            <th>Agence</th>
                            <th class="text-center">Biens</th>
                            <th class="text-center">Clients</th>
                            <th class="text-center">Transactions</th>
                            <th class="text-end">CA Total</th>
                            <th class="text-end">Commissions</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($team_members as $member): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($member['avatar'])): ?>
                                        <img src="<?= base_url('uploads/avatars/' . $member['avatar']) ?>" 
                                             alt="Avatar" 
                                             class="rounded-circle me-2" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px; font-size: 0.875rem; font-weight: 600;">
                                            <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-semibold"><?= esc($member['first_name'] . ' ' . $member['last_name']) ?></div>
                                        <small class="text-muted"><?= esc($member['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-building"></i>
                                    <?= esc($member['agency_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?= $member['stats']['properties'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= $member['stats']['clients'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= $member['stats']['transactions'] ?></span>
                            </td>
                            <td class="text-end">
                                <strong class="text-success">
                                    <?= number_format($member['stats']['total_sales'], 0, ',', ' ') ?> TND
                                </strong>
                            </td>
                            <td class="text-end">
                                <strong class="text-warning">
                                    <?= number_format($member['stats']['total_commission'], 0, ',', ' ') ?> TND
                                </strong>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('admin/users/view/' . $member['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Voir le profil">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2" class="text-end">Total Équipe:</th>
                            <th class="text-center">
                                <?= array_sum(array_column(array_column($team_members, 'stats'), 'properties')) ?>
                            </th>
                            <th class="text-center">
                                <?= array_sum(array_column(array_column($team_members, 'stats'), 'clients')) ?>
                            </th>
                            <th class="text-center">
                                <?= array_sum(array_column(array_column($team_members, 'stats'), 'transactions')) ?>
                            </th>
                            <th class="text-end">
                                <strong class="text-success">
                                    <?= number_format(array_sum(array_column(array_column($team_members, 'stats'), 'total_sales')), 0, ',', ' ') ?> TND
                                </strong>
                            </th>
                            <th class="text-end">
                                <strong class="text-warning">
                                    <?= number_format(array_sum(array_column(array_column($team_members, 'stats'), 'total_commission')), 0, ',', ' ') ?> TND
                                </strong>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3" id="userTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="properties-tab" data-bs-toggle="tab" data-bs-target="#properties" type="button">
                <i class="fas fa-home me-2"></i>Biens (<?= count($properties) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="clients-tab" data-bs-toggle="tab" data-bs-target="#clients" type="button">
                <i class="fas fa-users me-2"></i>Clients (<?= count($clients) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button">
                <i class="fas fa-exchange-alt me-2"></i>Transactions (<?= count($transactions) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button">
                <i class="fas fa-calendar-check me-2"></i>Rendez-vous (<?= count($appointments) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="commissions-tab" data-bs-toggle="tab" data-bs-target="#commissions" type="button">
                <i class="fas fa-coins me-2"></i>Commissions (<?= count($commissions) ?>)
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="userTabsContent">
        <!-- Properties Tab -->
        <div class="tab-pane fade show active" id="properties" role="tabpanel">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>Biens Immobiliers</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($properties)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-home fa-3x mb-3"></i>
                            <p>Aucun bien assigné à cet utilisateur</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-hover" id="propertiesTable">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Zone</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($properties as $property): ?>
                                <tr>
                                    <td><strong><?= esc($property['reference']) ?></strong></td>
                                    <td><?= esc($property['title']) ?></td>
                                    <td>
                                        <?php
                                        $typeLabels = [
                                            'apartment' => 'Appartement',
                                            'villa' => 'Villa',
                                            'house' => 'Maison',
                                            'land' => 'Terrain',
                                            'office' => 'Bureau',
                                            'commercial' => 'Commercial',
                                            'warehouse' => 'Entrepôt',
                                            'other' => 'Autre'
                                        ];
                                        echo $typeLabels[$property['type']] ?? ucfirst($property['type']);
                                        ?>
                                    </td>
                                    <td><?= esc($property['zone_name'] ?? 'N/A') ?></td>
                                    <td><strong><?= number_format($property['price'], 0, ',', ' ') ?> TND</strong></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'available' => 'success',
                                            'reserved' => 'warning',
                                            'sold' => 'danger',
                                            'rented' => 'info'
                                        ];
                                        $statusLabels = [
                                            'available' => 'Disponible',
                                            'reserved' => 'Réservé',
                                            'sold' => 'Vendu',
                                            'rented' => 'Loué'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusColors[$property['status']] ?? 'secondary' ?>">
                                            <?= $statusLabels[$property['status']] ?? $property['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($property['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/properties/edit/' . $property['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Clients Tab -->
        <div class="tab-pane fade" id="clients" role="tabpanel">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Clients</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($clients)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>Aucun client assigné à cet utilisateur</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-hover" id="clientsTable">
                            <thead>
                                <tr>
                                    <th>Nom Complet</th>
                                    <th>Type</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Source</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td><strong><?= esc($client['full_name']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?= $client['type'] === 'buyer' ? 'success' : ($client['type'] === 'seller' ? 'primary' : 'warning') ?>">
                                            <?= $client['type'] === 'buyer' ? 'Acheteur' : ($client['type'] === 'seller' ? 'Vendeur' : 'Les deux') ?>
                                        </span>
                                    </td>
                                    <td><?= esc($client['email'] ?? 'N/A') ?></td>
                                    <td><?= esc($client['phone'] ?? 'N/A') ?></td>
                                    <td><?= esc($client['source'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($client['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/clients/view/' . $client['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/clients/edit/' . $client['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Transactions Tab -->
        <div class="tab-pane fade" id="transactions" role="tabpanel">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Transactions</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($transactions)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                            <p>Aucune transaction pour cet utilisateur</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-hover" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Bien</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><strong><?= esc($transaction['reference'] ?? 'N/A') ?></strong></td>
                                    <td><?= esc($transaction['property_reference'] ?? 'N/A') ?></td>
                                    <td><?= esc($transaction['client_first_name'] . ' ' . $transaction['client_last_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $transaction['type'] === 'sale' ? 'primary' : 'info' ?>">
                                            <?= $transaction['type'] === 'sale' ? 'Vente' : 'Location' ?>
                                        </span>
                                    </td>
                                    <td><strong><?= number_format($transaction['amount'], 0, ',', ' ') ?> TND</strong></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'pending' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusColors[$transaction['status']] ?? 'secondary' ?>">
                                            <?= esc($transaction['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($transaction['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/transactions/view/' . $transaction['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Appointments Tab -->
        <div class="tab-pane fade" id="appointments" role="tabpanel">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Rendez-vous</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($appointments)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-check fa-3x mb-3"></i>
                            <p>Aucun rendez-vous pour cet utilisateur</p>
                        </div>
                    <?php else: ?>
                        <table class="table table-hover" id="appointmentsTable">
                            <thead>
                                <tr>
                                    <th>Date & Heure</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th>Bien</th>
                                    <th>Lieu</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><strong><?= date('d/m/Y H:i', strtotime($appointment['scheduled_at'])) ?></strong></td>
                                    <td>
                                        <?php
                                        $typeIcons = [
                                            'visite' => 'home',
                                            'meeting' => 'handshake',
                                            'appel' => 'phone',
                                            'signature' => 'pen',
                                            'autre' => 'calendar'
                                        ];
                                        $typeLabels = [
                                            'visite' => 'Visite',
                                            'meeting' => 'Réunion',
                                            'appel' => 'Appel',
                                            'signature' => 'Signature',
                                            'autre' => 'Autre'
                                        ];
                                        $type = $appointment['appointment_type'] ?? 'autre';
                                        ?>
                                        <i class="fas fa-<?= $typeIcons[$type] ?? 'calendar' ?> me-1"></i>
                                        <?= $typeLabels[$type] ?? ucfirst($type) ?>
                                    </td>
                                    <td><?= esc($appointment['client_first_name'] . ' ' . $appointment['client_last_name']) ?></td>
                                    <td><?= esc($appointment['property_reference'] ?? 'N/A') ?></td>
                                    <td><?= esc($appointment['location'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'scheduled' => 'info',
                                            'confirmed' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            'no_show' => 'warning'
                                        ];
                                        $statusLabels = [
                                            'scheduled' => 'Planifié',
                                            'confirmed' => 'Confirmé',
                                            'completed' => 'Terminé',
                                            'cancelled' => 'Annulé',
                                            'no_show' => 'Absent'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusColors[$appointment['status']] ?? 'secondary' ?>">
                                            <?= $statusLabels[$appointment['status']] ?? $appointment['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/appointments/view/' . $appointment['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Commissions Tab -->
        <div class="tab-pane fade" id="commissions" role="tabpanel">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-coins me-2"></i>Commissions</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($commissions)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-coins fa-3x mb-3"></i>
                            <p>Aucune commission pour cet utilisateur</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-3">
                            <strong>Total des commissions :</strong> 
                            <?= number_format($totalCommissions, 2, ',', ' ') ?> TND
                        </div>
                        <table class="table table-hover" id="commissionsTable">
                            <thead>
                                <tr>
                                    <th>Transaction</th>
                                    <th>Bien</th>
                                    <th>Type</th>
                                    <th>Commission Agent</th>
                                    <th>Pourcentage</th>
                                    <th>Statut Paiement</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commissions as $commission): ?>
                                <tr>
                                    <td><strong><?= esc($commission['transaction_reference'] ?? 'N/A') ?></strong></td>
                                    <td><?= esc($commission['property_reference'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $commission['transaction_type'] === 'sale' ? 'primary' : 'info' ?>">
                                            <?= $commission['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?>
                                        </span>
                                    </td>
                                    <td><strong><?= number_format($commission['agent_commission_amount'], 2, ',', ' ') ?> TND</strong></td>
                                    <td><?= number_format($commission['agent_commission_percentage'], 1) ?>%</td>
                                    <td>
                                        <?php
                                        $paymentColors = [
                                            'pending' => 'warning',
                                            'partial' => 'info',
                                            'paid' => 'success'
                                        ];
                                        $paymentLabels = [
                                            'pending' => 'En attente',
                                            'partial' => 'Partiel',
                                            'paid' => 'Payé'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $paymentColors[$commission['payment_status']] ?? 'secondary' ?>">
                                            <?= $paymentLabels[$commission['payment_status']] ?? $commission['payment_status'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($commission['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize DataTables for each table
    $('#propertiesTable, #clientsTable, #transactionsTable, #appointmentsTable, #commissionsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        pageLength: 10,
        order: [[6, 'desc']] // Sort by date column by default
    });
});
</script>
<?= $this->endSection() ?>
