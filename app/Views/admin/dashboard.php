<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </h1>
    <div class="text-muted">
        <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i') ?>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Propriétés</h6>
                    <h3 class="mb-0"><?= $stats['total_properties'] ?? 0 ?></h3>
                </div>
                <div class="text-primary" style="font-size: 3rem; opacity: 0.3;">
                    <i class="fas fa-home"></i>
                </div>
            </div>
            <div class="mt-2">
                <small class="text-success">
                    <i class="fas fa-check-circle"></i> 
                    <?= $stats['properties_published'] ?? 0 ?> publiées
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Clients</h6>
                    <h3 class="mb-0"><?= $stats['total_clients'] ?? 0 ?></h3>
                </div>
                <div class="text-info" style="font-size: 3rem; opacity: 0.3;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-2">
                <small class="text-warning">
                    <i class="fas fa-star"></i> 
                    <?= $stats['leads_count'] ?? 0 ?> leads
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Transactions</h6>
                    <h3 class="mb-0"><?= $stats['total_transactions'] ?? 0 ?></h3>
                </div>
                <div class="text-success" style="font-size: 3rem; opacity: 0.3;">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
            <div class="mt-2">
                <small class="text-warning">
                    <i class="fas fa-clock"></i> 
                    <?= $stats['transactions_pending'] ?? 0 ?> en attente
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Utilisateurs</h6>
                    <h3 class="mb-0"><?= $stats['total_users'] ?? 0 ?></h3>
                </div>
                <div class="text-danger" style="font-size: 3rem; opacity: 0.3;">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="mt-2">
                <small class="text-success">
                    <i class="fas fa-check"></i> Actifs
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="<?= base_url('admin/properties/create') ?>" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle"></i> Nouvelle Propriété
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= base_url('admin/clients/create') ?>" class="btn btn-info w-100">
                            <i class="fas fa-user-plus"></i> Nouveau Client
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= base_url('admin/transactions/create') ?>" class="btn btn-success w-100">
                            <i class="fas fa-handshake"></i> Nouvelle Transaction
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Profil</h5>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                <h5><?= esc($user['first_name'] ?? 'Admin') ?> <?= esc($user['last_name'] ?? '') ?></h5>
                <p class="text-muted mb-0"><?= esc($user['role_display_name'] ?? 'Administrateur') ?></p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
