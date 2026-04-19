<!-- MATRICE DES RÔLES -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-shield-check text-primary me-2"></i>Matrice des rôles & permissions</h4>
        <p class="text-muted mb-0">Gestion dynamique des permissions en temps réel</p>
    </div>
</div>

<!-- Stats adoption -->
<div class="row g-3 mb-4">
    <?php foreach ($adoption as $a) : ?>
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-semibold small text-muted"><?= esc($a['label']) ?></span>
                    <span class="badge text-white px-2" style="background:<?= esc($a['color']) ?>;"><?= $a['user_count'] ?> users</span>
                </div>
                <div class="progress" style="height:6px;">
                    <div class="progress-bar" style="width:<?= $a['adoption_pct'] ?>%;background:<?= esc($a['color']) ?>;"></div>
                </div>
                <div class="text-end mt-1 text-muted" style="font-size:.75rem;"><?= $a['adoption_pct'] ?>%</div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Matrice permissions -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex align-items-center justify-content-between">
            <span class="fw-semibold"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Tableau des permissions</span>
            <span class="text-muted small">Les modifications sont appliquées immédiatement</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0" id="matrixTable">
                <thead class="table-dark">
                    <tr>
                        <th style="width:240px;">Permission</th>
                        <?php foreach ($roles as $role) : ?>
                        <th class="text-center" style="min-width:120px;">
                            <div style="color:<?= esc($role['color']) ?>">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= esc($role['label']) ?>
                            </div>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($permissions as $module => $perms) : ?>
                    <tr class="table-light">
                        <td colspan="<?= count($roles) + 1 ?>" class="fw-bold text-uppercase text-muted py-2"
                            style="font-size:.75rem;letter-spacing:.1em;">
                            <i class="bi bi-folder2 me-1"></i><?= esc($module) ?>
                        </td>
                    </tr>
                    <?php foreach ($perms as $perm) : ?>
                    <tr>
                        <td>
                            <div class="fw-semibold small"><?= esc($perm['label']) ?></div>
                            <code class="text-muted" style="font-size:.7rem;"><?= esc($perm['name']) ?></code>
                        </td>
                        <?php foreach ($roles as $role) :
                            $hasPermission = in_array($perm['id'], array_column($role['permissions'], 'id'));
                        ?>
                        <td class="text-center align-middle">
                            <div class="form-check d-flex justify-content-center">
                                <input type="checkbox"
                                       class="form-check-input perm-checkbox"
                                       data-role="<?= $role['id'] ?>"
                                       data-perm="<?= $perm['id'] ?>"
                                       <?= $hasPermission ? 'checked' : '' ?>
                                       <?= $role['name'] === 'director' ? 'disabled checked' : '' ?>
                                       title="<?= esc($role['label']) ?> – <?= esc($perm['label']) ?>">
                            </div>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light text-muted small">
        <i class="bi bi-info-circle me-1"></i>
        Le rôle <strong>Directeur</strong> possède toujours toutes les permissions (non modifiable).
        Les modifications sont enregistrées automatiquement.
    </div>
</div>

<script>
// Enregistrement automatique à chaque changement de checkbox
document.querySelectorAll('.perm-checkbox:not([disabled])').forEach(function(cb) {
    cb.addEventListener('change', function() {
        const roleId = this.dataset.role;

        // Collecter toutes les permissions cochées pour ce rôle
        const checked = Array.from(
            document.querySelectorAll('.perm-checkbox[data-role="' + roleId + '"]:checked')
        ).map(el => el.dataset.perm);

        fetch('/admin/roles/' + roleId + '/permissions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ permissions: checked })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Permissions mises à jour', 'success');
            } else {
                showToast('Erreur : ' + (data.error || 'inconnue'), 'danger');
            }
        })
        .catch(() => showToast('Erreur réseau', 'danger'));
    });
});

function showToast(msg, type) {
    const toast = document.createElement('div');
    toast.className = 'position-fixed bottom-0 end-0 m-3 alert alert-' + type + ' shadow';
    toast.style.zIndex = '9999';
    toast.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}
</script>
