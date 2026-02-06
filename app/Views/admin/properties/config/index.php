<!-- app/Views/admin/properties/config/index.php -->
<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Configuration des Features par Type de Propriété
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Configurez quelles features sont activées et requises pour chaque type de propriété.
                    </p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Type de Propriété</th>
                                    <th>Pièces</th>
                                    <th>Localisation</th>
                                    <th>Finances</th>
                                    <th>Charges</th>
                                    <th>Orientation</th>
                                    <th>Médias</th>
                                    <th>Options</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($configs as $type => $data): ?>
                                <tr>
                                    <td><strong><?= $data['label'] ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?= $data['config']['enable_rooms'] ? 'success' : 'secondary' ?>">
                                            <?= $data['config']['enable_rooms'] ? 'Activé' : 'Désactivé' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $data['config']['enable_location_scoring'] ? 'success' : 'secondary' ?>">
                                            <?= $data['config']['enable_location_scoring'] ? 'Activé' : 'Désactivé' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $data['config']['enable_financial_data'] ? 'success' : 'secondary' ?>">
                                            <?= $data['config']['enable_financial_data'] ? 'Activé' : 'Désactivé' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $data['config']['enable_estimated_costs'] ? 'success' : 'secondary' ?>">
                                            <?= $data['config']['enable_estimated_costs'] ? 'Activé' : 'Désactivé' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $data['config']['enable_orientation'] ? 'success' : 'secondary' ?>">
                                            <?= $data['config']['enable_orientation'] ? 'Activé' : 'Désactivé' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $data['config']['enable_media_extension'] ? 'success' : 'secondary' ?>">
                                            <?= $data['config']['enable_media_extension'] ? 'Activé' : 'Désactivé' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $data['config']['enable_options'] ? 'success' : 'secondary' ?>">
                                            <?= $data['config']['enable_options'] ? 'Activé' : 'Désactivé' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/properties/config/<?= $type ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Configurer
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
