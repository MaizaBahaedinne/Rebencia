<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marked-alt me-2"></i>Affectation des Zones aux Agences
        </h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <div class="row">
        <!-- Liste des agences -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-store me-2"></i>Agences et leurs Zones</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Agence</th>
                                    <th>Code</th>
                                    <th>Zones Affectées</th>
                                    <th>Zone Principale</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $agencyAssignments = [];
                                foreach ($assignments as $assignment) {
                                    $agencyId = $assignment['agency_id'];
                                    if (!isset($agencyAssignments[$agencyId])) {
                                        $agencyAssignments[$agencyId] = [
                                            'name' => $assignment['agency_name'],
                                            'code' => $assignment['agency_code'],
                                            'zones' => [],
                                            'primary' => null
                                        ];
                                    }
                                    $agencyAssignments[$agencyId]['zones'][] = $assignment['zone_name'];
                                    if ($assignment['is_primary']) {
                                        $agencyAssignments[$agencyId]['primary'] = $assignment['zone_name'];
                                    }
                                }
                                ?>
                                
                                <?php foreach ($agencies as $agency): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($agency['name']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= esc($agency['code']) ?></span>
                                        </td>
                                        <td>
                                            <?php if (isset($agencyAssignments[$agency['id']])): ?>
                                                <?php foreach ($agencyAssignments[$agency['id']]['zones'] as $zoneName): ?>
                                                    <span class="badge bg-info me-1"><?= esc($zoneName) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Aucune zone</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($agencyAssignments[$agency['id']]) && $agencyAssignments[$agency['id']]['primary']): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-star me-1"></i>
                                                    <?= esc($agencyAssignments[$agency['id']]['primary']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/agency-zones/manage/' . $agency['id']) ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-map-marked-alt me-1"></i>Gérer
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
