<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-home"></i> Propriétés</h2>
    <a href="<?= base_url('admin/properties/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle Propriété
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Rechercher...">
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">Tous les types</option>
                    <option>Appartement</option>
                    <option>Villa</option>
                    <option>Maison</option>
                    <option>Terrain</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select">
                    <option value="">Tous les statuts</option>
                    <option>Brouillon</option>
                    <option>Publié</option>
                    <option>Réservé</option>
                    <option>Vendu</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Réf</th>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Zone</th>
                        <th>Prix</th>
                        <th>Agent</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($properties)): ?>
                        <?php foreach ($properties as $property): ?>
                            <tr>
                                <td><strong><?= esc($property['reference']) ?></strong></td>
                                <td><?= esc($property['title']) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst($property['type']) ?></span>
                                </td>
                                <td><?= esc($property['zone_name'] ?? '-') ?></td>
                                <td><strong><?= number_format($property['price'], 0, ',', ' ') ?> TND</strong></td>
                                <td><?= esc($property['agent_name'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match($property['status']) {
                                        'published' => 'success',
                                        'reserved' => 'warning',
                                        'sold' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($property['status']) ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('admin/properties/edit/' . $property['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $property['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Aucune propriété trouvée
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette propriété ?')) {
        fetch(`<?= base_url('admin/properties/delete/') ?>${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(() => location.reload());
    }
}
</script>
<?= $this->endSection() ?>
