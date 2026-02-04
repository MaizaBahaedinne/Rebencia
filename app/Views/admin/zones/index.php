<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-map-marked-alt me-2"></i><?= $page_title ?? 'Gestion des Zones' ?>
        </h1>
        <a href="<?= base_url('admin/zones/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Zone
        </a>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Zones</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="zonesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Nom Arabe</th>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>Popularité</th>
                            <th>GPS</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $typeLabels = [
                            'governorate' => 'Gouvernorat',
                            'city' => 'Ville',
                            'district' => 'Délégation',
                            'area' => 'Quartier'
                        ];
                        $typeBadges = [
                            'governorate' => 'primary',
                            'city' => 'success',
                            'district' => 'info',
                            'area' => 'secondary'
                        ];
                        
                        // Group zones by parent for better display
                        $zonesByType = [];
                        foreach ($zones as $zone) {
                            $zonesByType[$zone['type']][] = $zone;
                        }
                        ?>
                        
                        <?php foreach ($zones as $zone): ?>
                            <tr>
                                <td><strong><?= $zone['id'] ?></strong></td>
                                <td><?= esc($zone['name']) ?></td>
                                <td><?= esc($zone['name_ar'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= $typeBadges[$zone['type']] ?? 'secondary' ?>">
                                        <?= $typeLabels[$zone['type']] ?? $zone['type'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($zone['parent_id']): ?>
                                        <?php
                                        $parent = array_filter($zones, fn($z) => $z['id'] == $zone['parent_id']);
                                        $parent = reset($parent);
                                        ?>
                                        <small class="text-muted"><?= $parent ? esc($parent['name']) : 'N/A' ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?= min($zone['popularity_score'] ?? 0, 100) ?>%">
                                            <?= $zone['popularity_score'] ?? 0 ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($zone['latitude']) && !empty($zone['longitude'])): ?>
                                        <a href="https://www.google.com/maps?q=<?= $zone['latitude'] ?>,<?= $zone['longitude'] ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-secondary" title="Voir sur Google Maps">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/zones/edit/' . $zone['id']) ?>" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?= $zone['id'] ?>)" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette zone ?')) {
        window.location.href = '<?= base_url('admin/zones/delete/') ?>' + id;
    }
}

$(document).ready(function() {
    $('#zonesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
        },
        order: [[3, 'asc'], [1, 'asc']]
    });
});
</script>

<?= $this->endSection() ?>
