<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total</p>
                        <h3 class="mb-0"><?= $stats['total'] ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="fas fa-calculator text-primary fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">En attente</p>
                        <h3 class="mb-0"><?= $stats['pending'] ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="fas fa-clock text-warning fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">En cours</p>
                        <h3 class="mb-0"><?= $stats['in_progress'] ?></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="fas fa-spinner text-info fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Terminées</p>
                        <h3 class="mb-0"><?= $stats['completed'] ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="fas fa-check-circle text-success fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Annulées</p>
                        <h3 class="mb-0"><?= $stats['cancelled'] ?></h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i><?= $title ?></h5>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-body border-bottom">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                    <option value="in_progress" <?= $filters['status'] === 'in_progress' ? 'selected' : '' ?>>En cours</option>
                    <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Terminées</option>
                    <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Annulées</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="property_type" class="form-select" onchange="this.form.submit()">
                    <option value="">Type de bien</option>
                    <option value="apartment" <?= $filters['property_type'] === 'apartment' ? 'selected' : '' ?>>Appartement</option>
                    <option value="villa" <?= $filters['property_type'] === 'villa' ? 'selected' : '' ?>>Villa</option>
                    <option value="house" <?= $filters['property_type'] === 'house' ? 'selected' : '' ?>>Maison</option>
                    <option value="land" <?= $filters['property_type'] === 'land' ? 'selected' : '' ?>>Terrain</option>
                    <option value="commercial" <?= $filters['property_type'] === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="transaction_type" class="form-select" onchange="this.form.submit()">
                    <option value="">Type transaction</option>
                    <option value="sale" <?= $filters['transaction_type'] === 'sale' ? 'selected' : '' ?>>Vente</option>
                    <option value="rent" <?= $filters['transaction_type'] === 'rent' ? 'selected' : '' ?>>Location</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="city" class="form-control" placeholder="Ville" value="<?= esc($filters['city']) ?>">
            </div>
            <div class="col-md-2">
                <select name="agent_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les agents</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?= $agent['id'] ?>" <?= $filters['agent_id'] == $agent['id'] ? 'selected' : '' ?>>
                            <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card-body">
        <div class="table-responsive">
            <table id="estimationsTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Contact</th>
                        <th>Type Bien</th>
                        <th>Transaction</th>
                        <th>Localisation</th>
                        <th>Surface</th>
                        <th>Prix Estimé</th>
                        <th>Statut</th>
                        <th>Agent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estimations as $estimation): ?>
                        <tr>
                            <td><strong>#<?= $estimation['id'] ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($estimation['created_at'])) ?></td>
                            <td>
                                <strong><?= esc($estimation['first_name'] . ' ' . $estimation['last_name']) ?></strong>
                            </td>
                            <td>
                                <small>
                                    <i class="fas fa-phone text-muted"></i> <?= esc($estimation['phone']) ?><br>
                                    <i class="fas fa-envelope text-muted"></i> <?= esc($estimation['email']) ?>
                                </small>
                            </td>
                            <td>
                                <?php
                                $types = [
                                    'apartment' => 'Appartement',
                                    'villa' => 'Villa',
                                    'house' => 'Maison',
                                    'land' => 'Terrain',
                                    'commercial' => 'Commercial',
                                    'industrial' => 'Industriel',
                                    'office' => 'Bureau'
                                ];
                                ?>
                                <span class="badge bg-secondary"><?= $types[$estimation['property_type']] ?? $estimation['property_type'] ?></span>
                            </td>
                            <td>
                                <?php if ($estimation['transaction_type'] === 'sale'): ?>
                                    <span class="badge bg-success">Vente</span>
                                <?php else: ?>
                                    <span class="badge bg-info">Location</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small>
                                    <?= esc($estimation['city']) ?><br>
                                    <?= esc($estimation['governorate']) ?>
                                    <?php if ($estimation['zone_name']): ?>
                                        <br><span class="text-muted"><?= esc($estimation['zone_name']) ?></span>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($estimation['area_total']): ?>
                                    <?= number_format($estimation['area_total'], 0) ?> m²
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($estimation['estimated_price']): ?>
                                    <strong><?= number_format($estimation['estimated_price'], 0) ?> DT</strong>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClasses = [
                                    'pending' => 'warning',
                                    'in_progress' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $statusLabels = [
                                    'pending' => 'En attente',
                                    'in_progress' => 'En cours',
                                    'completed' => 'Terminée',
                                    'cancelled' => 'Annulée'
                                ];
                                ?>
                                <span class="badge bg-<?= $statusClasses[$estimation['status']] ?>">
                                    <?= $statusLabels[$estimation['status']] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($estimation['agent_first_name']): ?>
                                    <small><?= esc($estimation['agent_first_name'] . ' ' . $estimation['agent_last_name']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/property-estimations/view/' . $estimation['id']) ?>" 
                                   class="btn btn-sm btn-primary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#estimationsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
</script>
<?= $this->endSection() ?>
