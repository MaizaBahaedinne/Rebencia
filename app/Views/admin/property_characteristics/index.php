<!-- CATALOGUE DES CARACTÉRISTIQUES -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-tags me-2 text-primary"></i>Caractéristiques des biens
        </h4>
        <p class="text-muted mb-0"><?= count($rows) ?> caractéristique(s) dans le catalogue</p>
    </div>
    <?php if (in_array('characteristics.create', session()->get('permissions') ?? [])) : ?>
    <a href="<?= base_url('admin/property-characteristics/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouvelle caractéristique
    </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')) : ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0" id="charTable">
            <thead class="table-light">
                <tr>
                    <th style="width:36px" class="text-center">#</th>
                    <th style="width:36px" class="text-center">
                        <i class="bi bi-arrows-move text-muted" title="Glisser pour réordonner"></i>
                    </th>
                    <th>Icône</th>
                    <th>Label / Clé</th>
                    <th>Type</th>
                    <th>Unité</th>
                    <th>Applicable à</th>
                    <th>Ordre</th>
                    <th class="text-center">Actif</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody id="sortableBody">
            <?php if (empty($rows)) : ?>
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                        Aucune caractéristique définie.
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($rows as $i => $row) : ?>
                <?php
                    $typeLabels = [
                        'boolean' => ['badge-secondary', 'Oui/Non'],
                        'number'  => ['badge-info',      'Nombre'],
                        'text'    => ['badge-light text-dark border', 'Texte'],
                        'select'  => ['badge-warning text-dark',      'Liste'],
                    ];
                    [$badgeClass, $typeLabel] = $typeLabels[$row['type']] ?? ['badge-secondary', $row['type']];

                    $appliesToRaw = $row['applies_to'] ? json_decode($row['applies_to'], true) : null;
                    $typesDisplay = $appliesToRaw ? implode(', ', $appliesToRaw) : '<span class="text-muted">Tous</span>';
                ?>
                <tr data-id="<?= (int) $row['id'] ?>">
                    <td class="text-center text-muted small"><?= $i + 1 ?></td>
                    <td class="text-center drag-handle" style="cursor:grab">
                        <i class="bi bi-grip-vertical text-muted"></i>
                    </td>
                    <td>
                        <i class="bi <?= esc($row['icon']) ?> fs-5 text-primary"
                           title="<?= esc($row['icon']) ?>"></i>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= esc($row['label']) ?></div>
                        <code class="small text-muted"><?= esc($row['key']) ?></code>
                    </td>
                    <td>
                        <span class="badge <?= $badgeClass ?>"><?= $typeLabel ?></span>
                    </td>
                    <td>
                        <?= $row['unit'] ? '<code>' . esc($row['unit']) . '</code>' : '<span class="text-muted">—</span>' ?>
                    </td>
                    <td class="small"><?= $typesDisplay ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border"><?= (int) $row['sort_order'] ?></span>
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-inline-block m-0">
                            <input class="form-check-input toggle-active" type="checkbox"
                                   id="active_<?= $row['id'] ?>"
                                   data-id="<?= $row['id'] ?>"
                                   <?= $row['is_active'] ? 'checked' : '' ?>>
                        </div>
                    </td>
                    <td class="text-end pe-3">
                        <?php if (in_array('characteristics.edit', session()->get('permissions') ?? [])) : ?>
                        <a href="<?= base_url('admin/property-characteristics/' . $row['id'] . '/edit') ?>"
                           class="btn btn-sm btn-outline-secondary" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (in_array('characteristics.delete', session()->get('permissions') ?? [])) : ?>
                        <button type="button"
                                class="btn btn-sm btn-outline-danger btn-delete"
                                data-id="<?= $row['id'] ?>"
                                data-label="<?= esc($row['label']) ?>"
                                title="Supprimer">
                            <i class="bi bi-trash"></i>
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

<!-- Modal confirmation suppression -->
<form id="deleteForm" method="post" action="" style="display:none">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i>Confirmer la suppression
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                Supprimer la caractéristique <strong id="deleteLabel"></strong> ?<br>
                <span class="text-danger">Les valeurs enregistrées sur les biens ne seront pas effacées.</span>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-sm btn-danger" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Suppression ──────────────────────────────────────────────
    let deleteId = null;
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            deleteId = this.dataset.id;
            document.getElementById('deleteLabel').textContent = this.dataset.label;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
    document.getElementById('confirmDelete')?.addEventListener('click', function () {
        if (!deleteId) return;
        const form = document.getElementById('deleteForm');
        form.action = `<?= base_url('admin/property-characteristics/') ?>${deleteId}/delete`;
        form.submit();
    });

    // ── Toggle actif ─────────────────────────────────────────────
    document.querySelectorAll('.toggle-active').forEach(chk => {
        chk.addEventListener('change', function () {
            fetch(`<?= base_url('admin/property-characteristics/') ?>${this.dataset.id}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({}),
            })
            .then(r => r.json())
            .then(data => {
                this.checked = !!data.is_active;
            })
            .catch(() => { this.checked = !this.checked; });
        });
    });

    // ── Réordonnement drag-and-drop (SortableJS via CDN) ─────────
    if (typeof Sortable !== 'undefined') {
        Sortable.create(document.getElementById('sortableBody'), {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function () {
                const ids = Array.from(
                    document.querySelectorAll('#sortableBody tr[data-id]')
                ).map(tr => tr.dataset.id);

                fetch('<?= base_url('admin/property-characteristics/reorder') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ ids }),
                });
            },
        });
    }
});
</script>
<!-- SortableJS léger (aucune dépendance) -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
