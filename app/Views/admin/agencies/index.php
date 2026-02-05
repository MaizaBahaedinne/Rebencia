<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building me-2"></i><?= $page_title ?? 'Gestion des Agences' ?>
        </h1>
        <a href="<?= base_url('admin/agencies/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Agence
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
            <h6 class="m-0 font-weight-bold text-primary">Liste des Agences</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="agenciesTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Ville</th>
                            <th>Gouvernorat</th>
                            <th>Contact</th>
                            <th>Utilisateurs</th>
                            <th>Propriétés</th>
                            <th>Statut</th>
                            <th class="text-center no-filter">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agencies as $agency): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($agency['code']) ?></strong>
                                </td>
                                <td>
                                    <?php if ($agency['logo']): ?>
                                        <img src="<?= base_url('uploads/agencies/' . $agency['logo']) ?>" 
                                             alt="Logo" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                    <?php endif ?>
                                    <strong><?= esc($agency['name']) ?></strong>
                                </td>
                                <td>
                                    <?php if ($agency['type'] === 'siege'): ?>
                                        <span class="badge bg-primary">Siège</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Agence</span>
                                    <?php endif ?>
                                </td>
                                <td><?= esc($agency['city']) ?></td>
                                <td><?= esc($agency['governorate']) ?></td>
                                <td>
                                    <?php if (!empty($agency['phone'])): ?>
                                        <div><i class="fas fa-phone me-1"></i><?= esc($agency['phone']) ?></div>
                                    <?php endif ?>
                                    <?php if (!empty($agency['email'])): ?>
                                        <div><i class="fas fa-envelope me-1"></i><?= esc($agency['email']) ?></div>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $agency['users_count'] ?? 0 ?> utilisateurs</span>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?= $agency['properties_count'] ?? 0 ?> biens</span>
                                </td>
                                <td>
                                    <?php if ($agency['status'] === 'active'): ?>
                                        <span class="badge bg-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactif</span>
                                    <?php endif ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/agencies/view/' . $agency['id']) ?>" 
                                           class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/agencies/edit/' . $agency['id']) ?>" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?= $agency['id'] ?>)" title="Supprimer">
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

<script src="<?= base_url('assets/js/datatable-filters.js') ?>"></script>
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette agence ?')) {
        window.location.href = '<?= base_url('admin/agencies/delete/') ?>' + id;
    }
}

$(document).ready(function() {
    initDataTableWithFilters('agenciesTable', {
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 9 }
        ]
    });
});
</script>

<?= $this->endSection() ?>
