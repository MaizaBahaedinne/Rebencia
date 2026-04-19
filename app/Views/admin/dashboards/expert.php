<!-- DASHBOARD EXPERT IMMOBILIER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Bonjour, <?= esc(session()->get('user_name')) ?></h4>
        <p class="text-muted mb-0">Vos biens et performances – <?= date('d/m/Y') ?></p>
    </div>
    <span class="badge badge-expert text-white px-3 py-2">
        <i class="bi bi-award me-1"></i>Expert Immobilier
    </span>
</div>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center py-3">
                <div class="fs-2 fw-bold text-primary"><?= $property_stats['total'] ?></div>
                <div class="text-muted small">Mes biens</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center py-3">
                <div class="fs-2 fw-bold text-success"><?= $property_stats['available'] ?></div>
                <div class="text-muted small">Disponibles</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center py-3">
                <div class="fs-2 fw-bold text-warning"><?= $lead_stats['total'] ?></div>
                <div class="text-muted small">Leads actifs</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center py-3">
                <div class="fs-2 fw-bold text-danger"><?= $property_stats['sold'] ?></div>
                <div class="text-muted small">Ventes</div>
            </div>
        </div>
    </div>
</div>

<!-- Mes biens récents -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
        <i class="bi bi-building text-primary"></i> Mes biens récents
        <a href="<?= base_url('admin/properties/create') ?>" class="ms-auto btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouveau bien
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($my_properties)) : ?>
        <p class="text-muted text-center py-4">Aucun bien assigné pour le moment.</p>
        <?php else : ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Réf.</th><th>Titre</th><th>Ville</th><th>Prix</th><th>Statut</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_properties as $p) : ?>
                    <tr>
                        <td class="text-muted small"><?= esc($p['reference']) ?></td>
                        <td><?= esc($p['title']) ?></td>
                        <td><?= esc($p['city']) ?></td>
                        <td class="fw-semibold"><?= number_format($p['price'], 0, ',', ' ') ?> TND</td>
                        <td>
                            <?php
                            $sMap = ['available'=>'success','reserved'=>'warning','sold'=>'danger','inactive'=>'secondary'];
                            $sLbl = ['available'=>'Disponible','reserved'=>'Réservé','sold'=>'Vendu','inactive'=>'Inactif'];
                            ?>
                            <span class="badge bg-<?= $sMap[$p['status']] ?? 'secondary' ?>">
                                <?= $sLbl[$p['status']] ?? $p['status'] ?>
                            </span>
                        </td>
                        <td><a href="<?= base_url('admin/properties/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Voir</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
