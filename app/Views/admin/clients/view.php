<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= esc($client['first_name'] . ' ' . $client['last_name']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/clients') ?>">Clients</a></li>
                    <li class="breadcrumb-item active">Détails</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/clients/edit/' . $client['id']) ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="<?= base_url('admin/clients') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations Principales -->
        <div class="col-lg-8">
            <!-- Informations Personnelles -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations Personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted">Type</small>
                            <div>
                                <span class="badge bg-<?= 
                                    $client['type'] === 'buyer' ? 'success' : 
                                    ($client['type'] === 'seller' ? 'primary' : 
                                    ($client['type'] === 'tenant' ? 'info' : 'warning')) 
                                ?>">
                                    <?= ucfirst($client['type']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Email</small>
                            <div>
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <a href="mailto:<?= esc($client['email']) ?>"><?= esc($client['email']) ?></a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Téléphone</small>
                            <div>
                                <i class="fas fa-phone text-primary me-2"></i>
                                <a href="tel:<?= esc($client['phone']) ?>"><?= esc($client['phone']) ?></a>
                            </div>
                        </div>
                        <?php if ($client['phone_secondary']): ?>
                            <div class="col-md-6">
                                <small class="text-muted">Téléphone Secondaire</small>
                                <div>
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <a href="tel:<?= esc($client['phone_secondary']) ?>"><?= esc($client['phone_secondary']) ?></a>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if ($client['cin']): ?>
                            <div class="col-md-6">
                                <small class="text-muted">CIN</small>
                                <div><strong><?= esc($client['cin']) ?></strong></div>
                            </div>
                        <?php endif ?>
                        <?php if ($client['address']): ?>
                            <div class="col-12">
                                <small class="text-muted">Adresse</small>
                                <div>
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <?= nl2br(esc($client['address'])) ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <!-- Préférences de Recherche -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Préférences de Recherche</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if ($client['property_type_preference']): ?>
                            <div class="col-md-6">
                                <small class="text-muted">Type de Bien</small>
                                <div><strong><?= ucfirst($client['property_type_preference']) ?></strong></div>
                            </div>
                        <?php endif ?>
                        <?php if ($client['transaction_type_preference']): ?>
                            <div class="col-md-6">
                                <small class="text-muted">Type de Transaction</small>
                                <div><strong><?= ucfirst($client['transaction_type_preference']) ?></strong></div>
                            </div>
                        <?php endif ?>
                        <?php if ($client['budget_min'] || $client['budget_max']): ?>
                            <div class="col-md-6">
                                <small class="text-muted">Budget</small>
                                <div>
                                    <strong>
                                        <?php if ($client['budget_min'] && $client['budget_max']): ?>
                                            <?= number_format($client['budget_min']) ?> - <?= number_format($client['budget_max']) ?> TND
                                        <?php elseif ($client['budget_min']): ?>
                                            À partir de <?= number_format($client['budget_min']) ?> TND
                                        <?php else: ?>
                                            Jusqu'à <?= number_format($client['budget_max']) ?> TND
                                        <?php endif ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if ($client['area_preference']): ?>
                            <div class="col-md-6">
                                <small class="text-muted">Surface Souhaitée</small>
                                <div><strong><?= number_format($client['area_preference']) ?> m²</strong></div>
                            </div>
                        <?php endif ?>
                        <?php if ($client['preferred_zones']): ?>
                            <div class="col-12">
                                <small class="text-muted">Zones Préférées</small>
                                <div class="mt-2">
                                    <?php 
                                    $zones = json_decode($client['preferred_zones'], true);
                                    if ($zones):
                                        foreach ($zones as $zoneId):
                                    ?>
                                        <span class="badge bg-secondary me-1"><?= esc($zoneId) ?></span>
                                    <?php 
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                    
                    <?php if ($client['notes']): ?>
                        <hr class="my-3">
                        <div>
                            <small class="text-muted">Notes</small>
                            <p class="mb-0 mt-2"><?= nl2br(esc($client['notes'])) ?></p>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statut & Informations -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Statut</small>
                        <div>
                            <span class="badge bg-<?= 
                                $client['status'] === 'active' ? 'success' : 
                                ($client['status'] === 'inactive' ? 'secondary' : 'warning') 
                            ?> fs-6">
                                <?= ucfirst($client['status']) ?>
                            </span>
                        </div>
                    </div>
                    <?php if ($client['source']): ?>
                        <div class="mb-3">
                            <small class="text-muted">Source</small>
                            <div><strong><?= ucfirst($client['source']) ?></strong></div>
                        </div>
                    <?php endif ?>
                    <?php if ($client['agent_name']): ?>
                        <div class="mb-3">
                            <small class="text-muted">Agent Assigné</small>
                            <div>
                                <i class="fas fa-user-tie text-primary me-2"></i>
                                <strong><?= esc($client['agent_name']) ?></strong>
                            </div>
                        </div>
                    <?php endif ?>
                    <?php if ($client['agency_name']): ?>
                        <div class="mb-3">
                            <small class="text-muted">Agence</small>
                            <div>
                                <i class="fas fa-building text-primary me-2"></i>
                                <strong><?= esc($client['agency_name']) ?></strong>
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="mb-3">
                        <small class="text-muted">Date d'inscription</small>
                        <div><strong><?= date('d/m/Y', strtotime($client['created_at'])) ?></strong></div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Dernière modification</small>
                        <div><strong><?= date('d/m/Y H:i', strtotime($client['updated_at'])) ?></strong></div>
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions Rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:<?= esc($client['email']) ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-2"></i>Envoyer un Email
                        </a>
                        <a href="tel:<?= esc($client['phone']) ?>" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>Appeler
                        </a>
                        <a href="<?= base_url('admin/properties?client_id=' . $client['id']) ?>" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-home me-2"></i>Voir Propriétés Associées
                        </a>
                        <a href="<?= base_url('admin/transactions?client_id=' . $client['id']) ?>" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-file-contract me-2"></i>Voir Transactions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-0">0</h3>
                                <small class="text-muted">Visites</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-0">0</h3>
                                <small class="text-muted">Transactions</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-0">0 TND</h4>
                                <small class="text-muted">Valeur Totale</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
