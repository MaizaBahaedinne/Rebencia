<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-chart-bar text-primary"></i>
            Rapports & Exports
        </h1>
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Rapports</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4">
    <!-- Export Propriétés -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">Rapport Propriétés</h5>
                        <p class="text-muted mb-0 small">Exporter la liste des propriétés</p>
                    </div>
                </div>
                
                <form action="<?= base_url('admin/reports/export-properties') ?>" method="GET" class="export-form">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small">Statut</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <option value="draft">Brouillon</option>
                                <option value="published">Publié</option>
                                <option value="reserved">Réservé</option>
                                <option value="sold">Vendu</option>
                                <option value="rented">Loué</option>
                                <option value="archived">Archivé</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <option value="apartment">Appartement</option>
                                <option value="villa">Villa</option>
                                <option value="house">Maison</option>
                                <option value="land">Terrain</option>
                                <option value="office">Bureau</option>
                                <option value="commercial">Commercial</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Date début</label>
                            <input type="date" name="date_from" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Date fin</label>
                            <input type="date" name="date_to" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-download me-2"></i>Exporter en Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Clients -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">Rapport Clients</h5>
                        <p class="text-muted mb-0 small">Exporter la liste des clients</p>
                    </div>
                </div>
                
                <form action="<?= base_url('admin/reports/export-clients') ?>" method="GET" class="export-form">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <option value="individual">Particulier</option>
                                <option value="company">Entreprise</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Statut</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <option value="lead">Lead</option>
                                <option value="prospect">Prospect</option>
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="archived">Archivé</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Date début</label>
                            <input type="date" name="date_from" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Date fin</label>
                            <input type="date" name="date_to" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-download me-2"></i>Exporter en Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Transactions -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                        <i class="fas fa-exchange-alt fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">Rapport Transactions</h5>
                        <p class="text-muted mb-0 small">Exporter la liste des transactions</p>
                    </div>
                </div>
                
                <form action="<?= base_url('admin/reports/export-transactions') ?>" method="GET" class="export-form">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <option value="sale">Vente</option>
                                <option value="rental">Location</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Statut</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <option value="pending">En attente</option>
                                <option value="in_progress">En cours</option>
                                <option value="completed">Complété</option>
                                <option value="cancelled">Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Date début</label>
                            <input type="date" name="date_from" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Date fin</label>
                            <input type="date" name="date_to" class="form-control form-control-sm">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm w-100">
                        <i class="fas fa-download me-2"></i>Exporter en Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Commissions -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">Rapport Commissions</h5>
                        <p class="text-muted mb-0 small">Exporter les commissions par période</p>
                    </div>
                </div>
                
                <form action="<?= base_url('admin/reports/export-commissions') ?>" method="GET" class="export-form">
                    <div class="row g-2 mb-3">
                        <div class="col-md-12">
                            <label class="form-label small">Mois</label>
                            <input type="month" name="month" class="form-control form-control-sm" value="<?= date('Y-m') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small">Agent (optionnel)</label>
                            <select name="agent_id" class="form-select form-select-sm">
                                <option value="">Tous les agents</option>
                                <?php 
                                $userModel = model('UserModel');
                                $agents = $userModel->where('status', 'active')->findAll();
                                foreach($agents as $agent): 
                                ?>
                                    <option value="<?= $agent['id'] ?>"><?= esc(($agent['first_name'] ?? '') . ' ' . ($agent['last_name'] ?? '')) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-download me-2"></i>Exporter en Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.export-form {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}
</style>

<?= $this->endSection() ?>
