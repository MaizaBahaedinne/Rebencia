<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-server text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
    </div>
    <div>
        <a href="<?= base_url('admin/system/createBackup') ?>" class="btn btn-primary">
            <i class="fas fa-download"></i> Créer Backup
        </a>
    </div>
</div>

<!-- System Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Backups Disponibles</h6>
                <h2><?= count($backups) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Actions Auditées (7j)</h6>
                <h2><?= count(array_filter($recentLogs, function($log) {
                    return strtotime($log['created_at']) > strtotime('-7 days');
                })) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#backups">Backups</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#logs">Audit Logs</a>
    </li>
</ul>

<div class="tab-content">
    <!-- Backups Tab -->
    <div class="tab-pane fade show active" id="backups">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Backups Base de Données</h5>
            </div>
            <div class="card-body">
                <?php if (empty($backups)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-database fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun backup disponible</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fichier</th>
                                    <th>Date</th>
                                    <th>Taille</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backups as $backup): ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-file-archive text-primary"></i>
                                            <?= esc($backup['filename']) ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', $backup['date']) ?></td>
                                        <td><?= number_format($backup['size'] / 1024 / 1024, 2) ?> MB</td>
                                        <td class="text-end">
                                            <a href="<?= base_url('admin/system/downloadBackup/' . $backup['filename']) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteBackup('<?= $backup['filename'] ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Audit Logs Tab -->
    <div class="tab-pane fade" id="logs">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Journal d'Audit (100 dernières actions)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Module</th>
                                <th>Action</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                                    <td><?= esc($log['user_name'] ?? 'Système') ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc($log['module']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $log['action'] === 'create' ? 'success' : 
                                            ($log['action'] === 'delete' ? 'danger' : 'info') 
                                        ?>">
                                            <?= esc($log['action']) ?>
                                        </span>
                                    </td>
                                    <td><small><?= esc($log['ip_address']) ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteBackup(filename) {
    if (confirm('Supprimer ce backup ?')) {
        fetch('<?= base_url('admin/system/deleteBackup/') ?>' + filename, {
            method: 'DELETE'
        }).then(() => location.reload());
    }
}
</script>

<?= $this->endSection() ?>
