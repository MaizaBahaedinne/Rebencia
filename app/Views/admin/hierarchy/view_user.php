<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('title') ?>Détails Utilisateur - Hiérarchie<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-user-circle text-primary"></i>
                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
            </h1>
            <p class="text-muted mb-0">Gestion hiérarchique et équipe</p>
        </div>
        <div>
            <a href="<?= base_url('admin/hierarchy') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour à l'organigramme
            </a>
            <a href="<?= base_url('admin/hierarchy/assign-manager/' . $user['id']) ?>" class="btn btn-primary">
                <i class="fas fa-user-tie"></i> Assigner Manager
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Carte principale utilisateur -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="user-avatar-large mb-3">
                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                    </div>
                    
                    <h4 class="mb-1"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                    <p class="text-muted mb-3">
                        <i class="fas fa-id-badge"></i> ID: <?= $user['id'] ?>
                    </p>

                    <?php if ($user['role_id'] == 1): ?>
                        <span class="badge bg-danger mb-3">
                            <i class="fas fa-crown"></i> Administrateur
                        </span>
                    <?php else: ?>
                        <span class="badge bg-primary mb-3">
                            <i class="fas fa-user"></i> Utilisateur
                        </span>
                    <?php endif; ?>

                    <hr>

                    <div class="info-group mb-3">
                        <label class="text-muted small mb-1">Email</label>
                        <div><?= esc($user['email']) ?></div>
                    </div>

                    <?php if (isset($user['phone']) && $user['phone']): ?>
                    <div class="info-group mb-3">
                        <label class="text-muted small mb-1">Téléphone</label>
                        <div><?= esc($user['phone']) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($user['agency_id']) && $user['agency_id']): ?>
                    <div class="info-group">
                        <label class="text-muted small mb-1">Agence</label>
                        <div><?= esc($user['agency_id']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-bar text-primary"></i> Statistiques
                    </h5>
                    
                    <div class="stat-item">
                        <div class="stat-icon bg-primary-subtle">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value"><?= count($subordinates) ?></div>
                            <div class="stat-label">Subordonnés directs</div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon bg-success-subtle">
                            <i class="fas fa-users text-success"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value"><?= $totalSubordinatesCount ?></div>
                            <div class="stat-label">Équipe totale (récursif)</div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon bg-info-subtle">
                            <i class="fas fa-home text-info"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value"><?= count($properties) ?></div>
                            <div class="stat-label">Biens gérés</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manager et équipe -->
        <div class="col-lg-8">
            <!-- Manager actuel -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie text-primary"></i> Manager
                    </h5>
                    <?php if ($manager): ?>
                        <a href="<?= base_url('admin/hierarchy/assign-manager/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Changer
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if ($manager): ?>
                        <div class="manager-card">
                            <div class="user-avatar-medium">
                                <?= strtoupper(substr($manager['first_name'], 0, 1) . substr($manager['last_name'], 0, 1)) ?>
                            </div>
                            <div class="manager-info">
                                <h6 class="mb-1"><?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?></h6>
                                <p class="text-muted mb-0 small"><?= esc($manager['email']) ?></p>
                            </div>
                            <a href="<?= base_url('admin/hierarchy/view-user/' . $manager['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            Aucun manager assigné. Cet utilisateur est au sommet de la hiérarchie.
                            <a href="<?= base_url('admin/hierarchy/assign-manager/' . $user['id']) ?>" class="alert-link">
                                Assigner un manager
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Subordonnés directs -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-users text-primary"></i> 
                        Équipe directe 
                        <span class="badge bg-primary"><?= count($subordinates) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($subordinates)): ?>
                        <div class="subordinates-grid">
                            <?php foreach ($subordinates as $subordinate): ?>
                                <div class="subordinate-card">
                                    <div class="user-avatar-small">
                                        <?= strtoupper(substr($subordinate['first_name'], 0, 1) . substr($subordinate['last_name'], 0, 1)) ?>
                                    </div>
                                    <div class="subordinate-info">
                                        <h6 class="mb-0"><?= esc($subordinate['first_name'] . ' ' . $subordinate['last_name']) ?></h6>
                                        <p class="text-muted mb-0 small"><?= esc($subordinate['email']) ?></p>
                                    </div>
                                    <a href="<?= base_url('admin/hierarchy/view-user/' . $subordinate['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Aucun subordonné direct</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Biens gérés -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-home text-primary"></i> 
                        Biens gérés 
                        <span class="badge bg-primary"><?= count($properties) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($properties)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Type</th>
                                        <th>Adresse</th>
                                        <th>Prix</th>
                                        <th>Statut</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($properties as $property): ?>
                                        <tr>
                                            <td><strong><?= esc($property['reference']) ?></strong></td>
                                            <td><?= esc($property['type']) ?></td>
                                            <td><?= esc($property['address']) ?></td>
                                            <td>
                                                <?php if ($property['transaction_type'] === 'sale'): ?>
                                                    <?= number_format($property['price'], 0, ',', ' ') ?> DT
                                                <?php else: ?>
                                                    <?= number_format($property['rental_price'] ?? $property['price'], 0, ',', ' ') ?> DT/mois
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($property['status'] === 'available'): ?>
                                                    <span class="badge bg-success">Disponible</span>
                                                <?php elseif ($property['status'] === 'sold'): ?>
                                                    <span class="badge bg-danger">Vendu</span>
                                                <?php elseif ($property['status'] === 'rented'): ?>
                                                    <span class="badge bg-warning">Loué</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= ucfirst($property['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/properties/edit/' . $property['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-home fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Aucun bien géré</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .user-avatar-large {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: 600;
        margin: 0 auto;
    }

    .user-avatar-medium {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .user-avatar-small {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .info-group {
        text-align: left;
    }

    .stat-item {
        display: flex;
        align-items: center;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 20px;
    }

    .stat-details {
        flex: 1;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #212529;
    }

    .stat-label {
        font-size: 13px;
        color: #6c757d;
    }

    .manager-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .manager-info {
        flex: 1;
    }

    .subordinates-grid {
        display: grid;
        gap: 12px;
    }

    .subordinate-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .subordinate-card:hover {
        background: #e9ecef;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .subordinate-info {
        flex: 1;
    }

    .card {
        border-radius: 12px;
    }

    .card-header {
        padding: 1.25rem;
    }

    .table th {
        font-weight: 600;
        color: #6c757d;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
<?= $this->endSection() ?>
