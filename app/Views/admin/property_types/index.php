<?php
$perms = session()->get('permissions') ?? [];
?>

<!-- EN-TÊTE -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-house-gear me-2 text-primary"></i>Types de bien
        </h4>
        <p class="text-muted mb-0 small"><?= count($rows) ?> type(s) défini(s)</p>
    </div>
    <?php if (in_array('property_types.create', $perms)): ?>
    <a href="<?= base_url('admin/property-types/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau type
    </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase text-muted" style="font-size:.75rem">
                <tr>
                    <th class="ps-3" style="width:50px">#</th>
                    <th style="width:50px">Icône</th>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th class="text-center" style="width:110px">Statut</th>
                    <th class="text-end pe-3" style="width:130px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                        Aucun type de bien défini.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $i => $row): ?>
                <tr>
                    <td class="ps-3 text-muted small"><?= $i + 1 ?></td>
                    <td>
                        <?php if ($row['icon']): ?>
                        <i class="bi <?= esc($row['icon']) ?> fs-5 text-primary"></i>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= esc($row['name']) ?></td>
                    <td><code class="small"><?= esc($row['slug']) ?></code></td>
                    <td class="text-muted small">
                        <?= $row['description'] ? esc(mb_strimwidth($row['description'], 0, 80, '…')) : '—' ?>
                    </td>
                    <td class="text-center">
                        <?php if (in_array('property_types.edit', $perms)): ?>
                        <div class="form-check form-switch d-inline-block mb-0"
                             title="<?= $row['is_active'] ? 'Actif' : 'Inactif' ?>">
                            <input class="form-check-input toggle-active" type="checkbox"
                                   role="switch"
                                   data-id="<?= (int) $row['id'] ?>"
                                   <?= $row['is_active'] ? 'checked' : '' ?>>
                        </div>
                        <?php else: ?>
                        <?php if ($row['is_active']): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Actif</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border">Inactif</span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex gap-1 justify-content-end">
                            <?php if (in_array('property_types.edit', $perms)): ?>
                            <a href="<?= base_url('admin/property-types/' . $row['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (in_array('property_types.delete', $perms)): ?>
                            <form method="POST"
                                  action="<?= base_url('admin/property-types/' . $row['id'] . '/delete') ?>"
                                  onsubmit="return confirm('Supprimer « <?= esc($row['name'], 'js') ?> » ?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-active').forEach(function (chk) {
    chk.addEventListener('change', function () {
        const id   = this.dataset.id;
        const self = this;
        fetch('<?= base_url('admin/property-types/') ?>' + id + '/toggle', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (typeof data.is_active === 'undefined') {
                self.checked = !self.checked; // rollback
            }
        })
        .catch(() => { self.checked = !self.checked; });
    });
});
</script>
