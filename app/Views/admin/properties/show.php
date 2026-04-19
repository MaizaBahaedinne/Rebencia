<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><?= esc($property['title']) ?></h2>
        <small class="text-muted">Réf. <?= esc($property['reference']) ?></small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= site_url('admin/properties/' . $property['id'] . '/edit') ?>" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="<?= site_url('admin/properties') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Galerie + statut -->
    <div class="col-lg-7">
        <!-- Galerie -->
        <div class="card border-0 shadow-sm mb-3">
            <?php if (!empty($images)): ?>
            <div id="propertyCarousel" class="carousel slide">
                <div class="carousel-inner">
                    <?php foreach ($images as $i => $img): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= site_url('uploads/properties/' . $img['filename']) ?>"
                             class="d-block w-100" style="height:320px;object-fit:cover"
                             alt="<?= esc($img['alt_text'] ?? '') ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($images) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="d-flex align-items-center justify-content-center bg-light" style="height:220px">
                <div class="text-center text-muted">
                    <i class="bi bi-image" style="font-size:3rem"></i>
                    <p class="mt-2 mb-0">Aucune photo</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Description -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><strong>Description</strong></div>
            <div class="card-body">
                <?= nl2br(esc($property['description'] ?? '—')) ?>
            </div>
        </div>
    </div>

    <!-- Détails -->
    <div class="col-lg-5">
        <!-- Prix + Statut -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fs-4 fw-bold text-primary">
                        <?= number_format((float)$property['price'], 0, ',', ' ') ?> TND
                    </span>
                    <?php
                    $statusMap = [
                        'draft'     => ['label'=>'Brouillon',  'bg'=>'secondary'],
                        'published' => ['label'=>'Publié',     'bg'=>'success'],
                        'sold'      => ['label'=>'Vendu',      'bg'=>'danger'],
                        'rented'    => ['label'=>'Loué',       'bg'=>'info'],
                        'suspended' => ['label'=>'Suspendu',   'bg'=>'warning'],
                    ];
                    $s = $statusMap[$property['status']] ?? ['label'=>$property['status'],'bg'=>'dark'];
                    ?>
                    <span class="badge bg-<?= $s['bg'] ?> fs-6"><?= $s['label'] ?></span>
                </div>
                <small class="text-muted">
                    <?= $property['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?> •
                    Ajoutée le <?= date('d/m/Y', strtotime($property['created_at'])) ?>
                </small>
            </div>
        </div>

        <!-- Caractéristiques -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent"><strong>Caractéristiques</strong></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-geo-alt me-2 text-muted"></i>Adresse</span>
                    <span class="text-end"><?= esc($property['address'] . ', ' . ($property['city'] ?? '')) ?></span>
                </li>
                <?php if (!empty($property['type'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-house me-2 text-muted"></i>Type</span>
                    <strong><?= esc($property['type']) ?></strong>
                </li>
                <?php endif; ?>
                <?php if (!empty($property['surface'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-rulers me-2 text-muted"></i>Surface</span>
                    <strong><?= number_format((float)$property['surface'], 0) ?> m²</strong>
                </li>
                <?php endif; ?>
                <?php if (!empty($property['rooms'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-grid me-2 text-muted"></i>Pièces</span>
                    <strong><?= (int)$property['rooms'] ?></strong>
                </li>
                <?php endif; ?>
                <?php if (!empty($property['bedrooms'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-door-open me-2 text-muted"></i>Chambres</span>
                    <strong><?= (int)$property['bedrooms'] ?></strong>
                </li>
                <?php endif; ?>
                <?php if (!empty($property['bathrooms'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-water me-2 text-muted"></i>SDB</span>
                    <strong><?= (int)$property['bathrooms'] ?></strong>
                </li>
                <?php endif; ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-person me-2 text-muted"></i>Agent</span>
                    <strong><?= esc(($property['agent_first_name'] ?? '') . ' ' . ($property['agent_last_name'] ?? '—')) ?></strong>
                </li>
            </ul>
        </div>

        <!-- Actions rapides -->
        <div class="d-grid gap-2">
            <?php if ($property['status'] === 'published'): ?>
            <form method="post" action="<?= site_url('admin/properties/' . $property['id'] . '/unpublish') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-outline-warning w-100"><i class="bi bi-eye-slash me-1"></i> Dépublier</button>
            </form>
            <?php elseif ($property['status'] === 'draft'): ?>
            <form method="post" action="<?= site_url('admin/properties/' . $property['id'] . '/publish') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i> Publier</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Historique -->
<?php if (!empty($history)): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-clock-history me-2"></i>Historique des modifications</strong>
        <span class="badge bg-secondary"><?= count($history) ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>Par</th>
                    <th>Champ</th>
                    <th>Ancienne valeur</th>
                    <th>Nouvelle valeur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $h): ?>
                <tr>
                    <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= esc($h['action']) ?></span></td>
                    <td><?= esc(($h['user_first_name'] ?? '') . ' ' . ($h['user_last_name'] ?? '')) ?></td>
                    <td><?= esc($h['field_changed'] ?? '—') ?></td>
                    <td class="text-muted small"><?= esc($h['old_value'] ?? '—') ?></td>
                    <td class="small"><?= esc($h['new_value'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php $this->endSection(); ?>
