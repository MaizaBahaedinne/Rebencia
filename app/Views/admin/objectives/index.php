<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-bullseye me-2"></i><?= $title ?></h4>
    <div>
        <a href="<?= base_url('admin/objectives/refresh-all') ?>" class="btn btn-info">
            <i class="fas fa-sync-alt me-1"></i>Actualiser Tout
        </a>
        <a href="<?= base_url('admin/objectives/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nouvel Objectif
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="personal" <?= $filters['type'] === 'personal' ? 'selected' : '' ?>>Personnel</option>
                    <option value="agency" <?= $filters['type'] === 'agency' ? 'selected' : '' ?>>Agence</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="user_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les utilisateurs</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= $filters['user_id'] == $user['id'] ? 'selected' : '' ?>>
                            <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="agency_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes les agences</option>
                    <?php foreach ($agencies as $agency): ?>
                        <option value="<?= $agency['id'] ?>" <?= $filters['agency_id'] == $agency['id'] ? 'selected' : '' ?>>
                            <?= esc($agency['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="month" name="period" class="form-control" value="<?= esc($filters['period']) ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Actif</option>
                    <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Terminé</option>
                    <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Objectives List -->
<?php if (empty($objectives)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
            <p class="text-muted">Aucun objectif trouvé</p>
            <a href="<?= base_url('admin/objectives/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Créer un objectif
            </a>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($objectives as $objective): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h6 class="mb-1">
                            <?php if ($objective['type'] === 'personal'): ?>
                                <span class="badge bg-primary me-2">Personnel</span>
                                <?= esc($objective['user_first_name'] . ' ' . $objective['user_last_name']) ?>
                            <?php else: ?>
                                <span class="badge bg-info me-2">Agence</span>
                                <?= esc($objective['agency_name']) ?>
                            <?php endif; ?>
                        </h6>
                        <p class="text-muted mb-0 small">
                            <i class="far fa-calendar me-1"></i>
                            <?php
                            list($year, $month) = explode('-', $objective['period']);
                            $monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                         'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                            echo $monthNames[(int)$month] . ' ' . $year;
                            ?>
                        </p>
                        <?php
                        $statusClasses = ['active' => 'success', 'completed' => 'primary', 'cancelled' => 'danger'];
                        $statusLabels = ['active' => 'Actif', 'completed' => 'Terminé', 'cancelled' => 'Annulé'];
                        ?>
                        <span class="badge bg-<?= $statusClasses[$objective['status']] ?> mt-2">
                            <?= $statusLabels[$objective['status']] ?>
                        </span>
                    </div>
                    
                    <div class="col-md-7">
                        <!-- Overall Progress -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="fw-bold">Progression Globale</small>
                                <small class="fw-bold text-primary"><?= $objective['progress']['overall'] ?>%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: <?= min($objective['progress']['overall'], 100) ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="row g-2">
                            <!-- Revenue -->
                            <?php if ($objective['revenue_target'] > 0): ?>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Chiffre d'affaires</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small"><?= number_format($objective['revenue_achieved'], 0) ?> DT</span>
                                    <span class="badge bg-<?= $objective['progress']['revenue'] >= 100 ? 'success' : 'warning' ?>">
                                        <?= $objective['progress']['revenue'] ?>%
                                    </span>
                                </div>
                                <small class="text-muted">sur <?= number_format($objective['revenue_target'], 0) ?> DT</small>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Contacts -->
                            <?php if ($objective['new_contacts_target'] > 0): ?>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Nouveaux contacts</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small"><?= $objective['new_contacts_achieved'] ?></span>
                                    <span class="badge bg-<?= $objective['progress']['contacts'] >= 100 ? 'success' : 'warning' ?>">
                                        <?= $objective['progress']['contacts'] ?>%
                                    </span>
                                </div>
                                <small class="text-muted">sur <?= $objective['new_contacts_target'] ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Rent Properties -->
                            <?php if ($objective['properties_rent_target'] > 0): ?>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Biens Location</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small"><?= $objective['properties_rent_achieved'] ?></span>
                                    <span class="badge bg-<?= $objective['progress']['rent'] >= 100 ? 'success' : 'warning' ?>">
                                        <?= $objective['progress']['rent'] ?>%
                                    </span>
                                </div>
                                <small class="text-muted">sur <?= $objective['properties_rent_target'] ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Sale Properties -->
                            <?php if ($objective['properties_sale_target'] > 0): ?>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Biens Vente</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small"><?= $objective['properties_sale_achieved'] ?></span>
                                    <span class="badge bg-<?= $objective['progress']['sale'] >= 100 ? 'success' : 'warning' ?>">
                                        <?= $objective['progress']['sale'] ?>%
                                    </span>
                                </div>
                                <small class="text-muted">sur <?= $objective['properties_sale_target'] ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Transactions -->
                            <?php if ($objective['transactions_target'] > 0): ?>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Transactions</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small"><?= $objective['transactions_achieved'] ?></span>
                                    <span class="badge bg-<?= $objective['progress']['transactions'] >= 100 ? 'success' : 'warning' ?>">
                                        <?= $objective['progress']['transactions'] ?>%
                                    </span>
                                </div>
                                <small class="text-muted">sur <?= $objective['transactions_target'] ?></small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-2 text-end">
                        <a href="<?= base_url('admin/objectives/refresh/' . $objective['id']) ?>" 
                           class="btn btn-sm btn-info mb-2" title="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                        <a href="<?= base_url('admin/objectives/edit/' . $objective['id']) ?>" 
                           class="btn btn-sm btn-primary mb-2" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= base_url('admin/objectives/delete/' . $objective['id']) ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Supprimer cet objectif ?')" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                
                <?php if ($objective['notes']): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <small class="text-muted">
                            <i class="fas fa-sticky-note me-1"></i>
                            <?= nl2br(esc($objective['notes'])) ?>
                        </small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
