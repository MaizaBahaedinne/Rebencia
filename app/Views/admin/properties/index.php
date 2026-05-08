<!-- LISTE BIENS IMMOBILIERS -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-building me-2 text-primary"></i>Biens Immobiliers</h4>
        <p class="text-muted mb-0"><?= $result['total'] ?> bien(s) – Page <?= $result['page'] ?>/<?= $result['pages'] ?: 1 ?></p>
    </div>
    <?php if (in_array('properties.create', session()->get('permissions') ?? [])) : ?>
    <a href="<?= base_url('admin/properties/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau bien
    </a>
    <?php endif; ?>
</div>

<!-- Filtres -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous types</option>
                    <?php foreach ($propertyTypes ?? [] as $pt): ?>
                    <option value="<?= esc($pt['slug']) ?>"
                        <?= ($filters['type'] ?? '') === $pt['slug'] ? 'selected' : '' ?>>
                        <?= esc($pt['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous statuts</option>
                    <option value="available" <?= $filters['status'] === 'available' ? 'selected' : '' ?>>Disponible</option>
                    <option value="reserved"  <?= $filters['status'] === 'reserved'  ? 'selected' : '' ?>>Réservé</option>
                    <option value="sold"      <?= $filters['status'] === 'sold'      ? 'selected' : '' ?>>Vendu</option>
                    <option value="inactive"  <?= $filters['status'] === 'inactive'  ? 'selected' : '' ?>>Inactif</option>
                </select>
            </div>
            <?php if (! $auth->hasRole('expert')) : ?>
            <div class="col-md-2">
                <select name="agent_id" class="form-select form-select-sm">
                    <option value="">Tous agents</option>
                    <?php foreach ($agents as $agent) : ?>
                    <option value="<?= $agent['id'] ?>" <?= $filters['agent_id'] == $agent['id'] ? 'selected' : '' ?>>
                        <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-2">
                <input type="text" name="city" class="form-control form-control-sm"
                       value="<?= esc($filters['city'] ?? '') ?>" placeholder="Ville...">
            </div>
            <div class="col">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="<?= esc($filters['search'] ?? '') ?>" placeholder="Recherche...">
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
                <a href="<?= base_url('admin/properties') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Liste -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">Photo</th>
                        <th>Titre</th>
                        <th>Réf.</th>
                        <th>Type</th>
                        <th>Ville</th>
                        <th>Prix</th>
                        <th>Agent</th>
                        <th>Agence</th>
                        <th>Statut</th>
                        <th>Publié</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($result['data'])) : ?>
                    <tr><td colspan="11" class="text-center text-muted py-4">Aucun bien trouvé.</td></tr>
                    <?php else : ?>
                    <?php foreach ($result['data'] as $p) :
                        $sMap = ['available'=>'success','reserved'=>'warning','sold'=>'danger','rented'=>'info','inactive'=>'secondary'];
                        $sLbl = ['available'=>'Disponible','reserved'=>'Réservé','sold'=>'Vendu','rented'=>'Loué','inactive'=>'Inactif'];
                        $tLbl = [];
                        foreach ($propertyTypes ?? [] as $_pt) { $tLbl[$_pt['slug']] = $_pt['name']; }
                    ?>
                    <tr>
                        <td>
                            <?php if ($p['primary_image']) : ?>
                            <img src="<?= base_url($p['primary_image']) ?>" alt=""
                                 class="rounded" style="width:48px;height:40px;object-fit:cover;">
                            <?php else : ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                                 style="width:48px;height:40px;">
                                <i class="bi bi-image"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/properties/' . $p['id']) ?>" class="fw-semibold text-decoration-none text-dark">
                                <?= esc($p['title']) ?>
                            </a>
                        </td>
                        <td><code class="text-muted small"><?= esc($p['reference']) ?></code></td>
                        <td class="text-muted small"><?= $tLbl[$p['type']] ?? $p['type'] ?></td>
                        <td class="text-muted small"><?= esc($p['city']) ?></td>
                        <td class="fw-semibold small"><?= number_format($p['price'], 0, ',', ' ') ?> TND</td>
                        <td class="text-muted small"><?= esc($p['first_name'] . ' ' . $p['last_name']) ?></td>
                        <td class="text-muted small"><?= esc($p['agency_name'] ?? '—') ?></td>
                        <td><span class="badge bg-<?= $sMap[$p['status']] ?? 'secondary' ?>"><?= $sLbl[$p['status']] ?? $p['status'] ?></span></td>
                        <td>
                            <?php if (in_array('properties.publish', session()->get('permissions') ?? [])) : ?>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input publish-toggle" type="checkbox"
                                       data-id="<?= $p['id'] ?>"
                                       <?= $p['is_published'] ? 'checked' : '' ?>>
                            </div>
                            <?php else : ?>
                            <i class="bi bi-<?= $p['is_published'] ? 'check-circle-fill text-success' : 'x-circle text-muted' ?>"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <?php if (in_array('properties.edit', session()->get('permissions') ?? [])) : ?>
                                <a href="<?= base_url('admin/properties/' . $p['id'] . '/edit') ?>"
                                   class="btn btn-outline-secondary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                <?php endif; ?>
                                <?php if (in_array('properties.delete', session()->get('permissions') ?? [])) : ?>
                                <button class="btn btn-outline-danger" title="Supprimer"
                                        onclick="confirmDelete(<?= $p['id'] ?>, '<?= esc($p['title']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
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
    <!-- Pagination -->
    <?php if ($result['pages'] > 1) : ?>
    <div class="card-footer bg-white">
        <nav>
            <ul class="pagination pagination-sm mb-0 justify-content-center">
                <?php for ($i = 1; $i <= $result['pages']; $i++) : ?>
                <li class="page-item <?= $i == $result['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<form id="deleteForm" method="POST" class="d-none"><?= csrf_field() ?></form>

<script>
function confirmDelete(id, title) {
    if (confirm('Supprimer le bien "' + title + '" ?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/properties/' + id + '/delete';
        form.classList.remove('d-none');
        form.submit();
    }
}

// Toggle publication AJAX
document.querySelectorAll('.publish-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const id = this.dataset.id;
        fetch('/admin/properties/' + id + '/publish', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ '<?= csrf_token() ?>': '<?= csrf_hash() ?>' })
        }).then(r => r.json()).then(data => {
            if (! data.success) this.checked = ! this.checked;
        });
    });
});
</script>
