
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Déploiement Git</h2>
        <small class="text-muted">Mise à jour depuis le dépôt distant</small>
    </div>
</div>

<?php if (session()->has('deploy_output')): ?>
<div class="alert alert-<?= session('deploy_success') ? 'success' : 'danger' ?> alert-dismissible fade show">
    <strong><?= session('deploy_success') ? 'Déploiement réussi' : 'Erreur de déploiement' ?></strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <pre class="mt-2 mb-0 small" style="background:rgba(0,0,0,.05);padding:.75rem;border-radius:.5rem;max-height:200px;overflow-y:auto"><?= esc(session('deploy_output')) ?></pre>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Déclencher un déploiement -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <strong><i class="bi bi-cloud-download me-2"></i>Lancer un déploiement</strong>
            </div>
            <div class="card-body">
                <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                    <div>
                        <strong>Attention</strong> — Cette action effectue un <code>git pull</code> sur le serveur.
                        Assurez-vous que la branche de production est à jour.
                    </div>
                </div>

                <form method="post" action="<?= site_url('admin/system/deploy/pull') ?>" id="deployForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Branche</label>
                        <input type="text" name="branch" class="form-control" value="main" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note (optionnel)</label>
                        <input type="text" name="note" class="form-control" placeholder="Motif du déploiement…">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger" id="deployBtn"
                                onclick="return confirm('Confirmer le déploiement ?\n\nCela exécutera :\n1. git pull\n2. php spark migrate')">
                            <i class="bi bi-rocket-takeoff me-1"></i> Déployer (git pull + migrate)
                        </button>
                    </div>
                </form>

                <hr class="my-3">

                <form method="post" action="<?= site_url('admin/system/deploy/migrate') ?>">
                    <?= csrf_field() ?>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-warning"
                                onclick="return confirm('Appliquer les migrations en attente ?')">
                            <i class="bi bi-database-gear me-1"></i> Appliquer les migrations uniquement
                        </button>
                    </div>
                </form>

                <hr class="my-2">

                <form method="post" action="<?= site_url('admin/system/deploy/cache') ?>">
                    <?= csrf_field() ?>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-trash3 me-1"></i> Vider le cache
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Infos dépôt -->
        <?php if (!empty($gitInfo)): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-transparent"><strong><i class="bi bi-git me-2"></i>État du dépôt</strong></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Branche</span>
                    <code><?= esc($gitInfo['branch'] ?? '—') ?></code>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Dernier commit</span>
                    <code class="small"><?= esc(substr($gitInfo['commit'] ?? '', 0, 8)) ?></code>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Message</span>
                    <span class="small text-end" style="max-width:180px"><?= esc($gitInfo['message'] ?? '—') ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Date</span>
                    <span class="small"><?= esc($gitInfo['date'] ?? '—') ?></span>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <!-- Historique déploiements -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-clock-history me-2"></i>Historique des déploiements</strong>
                <span class="badge bg-secondary"><?= count($deployments ?? []) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Branche</th>
                            <th>Commit</th>
                            <th>Déclenché par</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deployments)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i> Aucun déploiement
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($deployments as $dep): ?>
                        <?php
                        $stBadge = [
                            'pending'  => ['En attente','secondary'],
                            'running'  => ['En cours',  'warning'],
                            'success'  => ['Succès',    'success'],
                            'failed'   => ['Échoué',    'danger'],
                        ][$dep['status']] ?? [$dep['status'],'light'];
                        ?>
                        <tr>
                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($dep['created_at'])) ?></td>
                            <td><code><?= esc($dep['branch']) ?></code></td>
                            <td><code class="small"><?= esc(substr($dep['commit_hash'] ?? '—', 0, 8)) ?></code></td>
                            <td class="small"><?= esc(($dep['user_first_name'] ?? '') . ' ' . ($dep['user_last_name'] ?? '')) ?></td>
                            <td><span class="badge bg-<?= $stBadge[1] ?>"><?= $stBadge[0] ?></span></td>
                            <td>
                                <?php if (!empty($dep['output'])): ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0"
                                        onclick="showOutput(<?= htmlspecialchars(json_encode($dep['output']), ENT_QUOTES) ?>)">
                                    <i class="bi bi-terminal"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal output -->
<div class="modal fade" id="outputModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-terminal me-2"></i>Sortie du déploiement</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="deployOutput" class="bg-dark text-light p-3 rounded" style="font-size:.8rem;max-height:400px;overflow-y:auto"></pre>
            </div>
        </div>
    </div>
</div>

<script>
function showOutput(data) {
    document.getElementById('deployOutput').textContent = data;
    new bootstrap.Modal(document.getElementById('outputModal')).show();
}

// Disable button on submit to prevent double-click
document.getElementById('deployForm').addEventListener('submit', function() {
    const btn = document.getElementById('deployBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Déploiement en cours…';
    this.submit();
});
</script>

