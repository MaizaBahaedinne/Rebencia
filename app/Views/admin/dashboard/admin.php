<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-tachometer-alt me-2"></i><?= esc($title) ?></h1>
        <div class="text-muted">
            <i class="far fa-calendar"></i> <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <!-- System Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Users -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Utilisateurs actifs</p>
                            <h3 class="mb-0"><?= number_format($stats['active_users']) ?></h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> <?= number_format($stats['new_users_month']) ?> ce mois
                            </small>
                        </div>
                        <div class="text-primary" style="font-size: 2.5rem; opacity: 0.3;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agencies -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Agences actives</p>
                            <h3 class="mb-0"><?= number_format($stats['total_agencies']) ?></h3>
                            <small class="text-muted">
                                <?= number_format($stats['total_clients']) ?> clients
                            </small>
                        </div>
                        <div class="text-info" style="font-size: 2.5rem; opacity: 0.3;">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Properties -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Biens immobiliers</p>
                            <h3 class="mb-0"><?= number_format($stats['total_properties']) ?></h3>
                            <small class="text-muted">
                                <?= number_format($stats['total_transactions']) ?> transactions
                            </small>
                        </div>
                        <div class="text-success" style="font-size: 2.5rem; opacity: 0.3;">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Support en attente</p>
                            <h3 class="mb-0"><?= number_format($stats['support_pending']) ?></h3>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-circle"></i> Nécessite attention
                            </small>
                        </div>
                        <div class="text-warning" style="font-size: 2.5rem; opacity: 0.3;">
                            <i class="fas fa-headset"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Monitoring -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-server me-2"></i>État du serveur</h5>
                </div>
                <div class="card-body">
                    <!-- Server Load -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Charge CPU</small>
                            <small class="fw-bold"><?= number_format($stats['server_load'], 2) ?></small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <?php 
                            $loadPercent = min($stats['server_load'] * 100, 100);
                            $loadClass = $loadPercent < 50 ? 'bg-success' : ($loadPercent < 75 ? 'bg-warning' : 'bg-danger');
                            ?>
                            <div class="progress-bar <?= $loadClass ?>" style="width: <?= $loadPercent ?>%"></div>
                        </div>
                    </div>

                    <!-- Memory Usage -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Utilisation mémoire</small>
                            <small class="fw-bold"><?= number_format($stats['memory_usage'], 0) ?> MB</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 45%"></div>
                        </div>
                    </div>

                    <!-- Disk Space -->
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Espace disque libre</small>
                            <small class="fw-bold"><?= number_format($stats['disk_free'], 1) ?> GB / <?= number_format($stats['disk_total'], 1) ?> GB</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <?php 
                            $diskPercent = ($stats['disk_free'] / $stats['disk_total']) * 100;
                            $diskClass = $diskPercent > 20 ? 'bg-success' : 'bg-danger';
                            ?>
                            <div class="progress-bar <?= $diskClass ?>" style="width: <?= $diskPercent ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Activité système</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="text-primary" style="font-size: 2rem;">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h4 class="mb-0"><?= number_format($stats['property_views_month']) ?></h4>
                            <small class="text-muted">Vues de biens (30j)</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-success" style="font-size: 2rem;">
                                <i class="fas fa-list-alt"></i>
                            </div>
                            <h4 class="mb-0"><?= number_format($stats['audit_logs_today']) ?></h4>
                            <small class="text-muted">Actions aujourd'hui</small>
                        </div>
                        <div class="col-6">
                            <div class="text-info" style="font-size: 2rem;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h4 class="mb-0"><?= number_format($stats['emails_week']) ?></h4>
                            <small class="text-muted">Emails (7j)</small>
                        </div>
                        <div class="col-6">
                            <div class="text-warning" style="font-size: 2rem;">
                                <i class="fas fa-sms"></i>
                            </div>
                            <h4 class="mb-0"><?= number_format($stats['sms_week']) ?></h4>
                            <small class="text-muted">SMS (7j)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Logs -->
    <?php if ($stats['error_logs_today'] > 0): ?>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong><?= $stats['error_logs_today'] ?> erreur(s)</strong> détectée(s) aujourd'hui. 
                <a href="/admin/audit-logs?level=error" class="alert-link">Voir les logs</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Activities -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Activités récentes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date/Heure</th>
                                    <th>Utilisateur</th>
                                    <th>Action</th>
                                    <th>IP</th>
                                    <th>Niveau</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stats['recent_activities'])): ?>
                                    <?php foreach ($stats['recent_activities'] as $activity): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($activity['first_name']): ?>
                                                <?= esc($activity['first_name'] . ' ' . $activity['last_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Système</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($activity['action']) ?></td>
                                        <td><code><?= esc($activity['ip_address'] ?? 'N/A') ?></code></td>
                                        <td>
                                            <?php
                                            $levelClass = 'secondary';
                                            if ($activity['level'] == 'error') $levelClass = 'danger';
                                            elseif ($activity['level'] == 'warning') $levelClass = 'warning';
                                            elseif ($activity['level'] == 'info') $levelClass = 'info';
                                            ?>
                                            <span class="badge bg-<?= $levelClass ?>"><?= esc($activity['level']) ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Aucune activité récente
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
