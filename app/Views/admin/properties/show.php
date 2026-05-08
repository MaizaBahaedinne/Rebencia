<?php
// ── Helpers ────────────────────────────────────────────────────────────────
$statusMap = [
    'available' => ['label' => 'Disponible', 'bg' => 'success',   'icon' => 'bi-check-circle'],
    'reserved'  => ['label' => 'Réservé',    'bg' => 'warning',   'icon' => 'bi-clock'],
    'sold'      => ['label' => 'Vendu',      'bg' => 'danger',    'icon' => 'bi-bag-check'],
    'rented'    => ['label' => 'Loué',       'bg' => 'info',      'icon' => 'bi-key'],
    'inactive'  => ['label' => 'Inactif',    'bg' => 'secondary', 'icon' => 'bi-dash-circle'],
];
$typeLabels = [
    'apartment'  => 'Appartement',
    'house'      => 'Maison',
    'villa'      => 'Villa',
    'commercial' => 'Local commercial',
    'land'       => 'Terrain',
    'office'     => 'Bureau',
];
$s         = $statusMap[$property['status']] ?? ['label' => $property['status'], 'bg' => 'dark', 'icon' => 'bi-question'];
$typeLabel = $typeLabels[$property['type']] ?? $property['type'];
$txLabel   = $property['transaction_type'] === 'sale' ? 'Vente' : 'Location';
$txIcon    = $property['transaction_type'] === 'sale' ? 'bi-tag' : 'bi-arrow-repeat';
$features  = !empty($property['features']) ? (json_decode($property['features'], true) ?? []) : [];
$hasCoords = !empty($property['latitude']) && !empty($property['longitude']);
?>

<!-- ══ EN-TÊTE ══════════════════════════════════════════════════════════════ -->
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size:.8rem">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/properties') ?>">Biens</a></li>
                <li class="breadcrumb-item active"><?= esc($property['reference']) ?></li>
            </ol>
        </nav>
        <h4 class="mb-1 fw-bold"><?= esc($property['title']) ?></h4>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <code class="text-muted" style="font-size:.8rem"><?= esc($property['reference']) ?></code>
            <span class="badge bg-<?= $s['bg'] ?>"><i class="bi <?= $s['icon'] ?> me-1"></i><?= $s['label'] ?></span>
            <?php if ($property['is_published']): ?>
            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle"><i class="bi bi-eye me-1"></i>Publié</span>
            <?php else: ?>
            <span class="badge bg-secondary bg-opacity-10 text-secondary border"><i class="bi bi-eye-slash me-1"></i>Non publié</span>
            <?php endif; ?>
            <?php if ($property['featured']): ?>
            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>En vedette</span>
            <?php endif; ?>
        </div>
    </div>
<?php $canEdit = $canEdit ?? $auth->hasPermission('properties.edit'); ?>
    <div class="d-flex gap-2 flex-wrap">
        <?php if ($canEdit): ?>
        <a href="<?= site_url('admin/properties/' . $property['id'] . '/edit') ?>" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <?php endif; ?>
        <?php if ($auth->hasPermission('properties.publish')): ?>
        <?php if ($property['is_published']): ?>
        <form method="post" action="<?= site_url('admin/properties/' . $property['id'] . '/publish') ?>" class="d-inline">
            <?= csrf_field() ?>
            <button class="btn btn-outline-danger" onclick="return confirm('Dépublier ce bien ?')">
                <i class="bi bi-eye-slash me-1"></i> Dépublier
            </button>
        </form>
        <?php else: ?>
        <form method="post" action="<?= site_url('admin/properties/' . $property['id'] . '/publish') ?>" class="d-inline">
            <?= csrf_field() ?>
            <button class="btn btn-success">
                <i class="bi bi-eye me-1"></i> Publier
            </button>
        </form>
        <?php endif; ?>
        <?php endif; ?>
        <a href="<?= site_url('admin/properties') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>
</div>

<!-- ══ STATS RAPIDES ════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 text-center py-3">
            <div class="text-primary fs-5 fw-bold"><?= number_format((float)$property['price'], 0, ',', ' ') ?> TND</div>
            <div class="text-muted small mt-1"><i class="bi <?= $txIcon ?> me-1"></i><?= $txLabel ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 text-center py-3">
            <div class="fs-5 fw-bold"><?= !empty($property['surface']) ? number_format((float)$property['surface'], 0) . ' m²' : '—' ?></div>
            <div class="text-muted small mt-1"><i class="bi bi-rulers me-1"></i>Surface</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 text-center py-3">
            <div class="fs-5 fw-bold"><?= !empty($property['rooms']) ? (int)$property['rooms'] : '—' ?></div>
            <div class="text-muted small mt-1"><i class="bi bi-grid me-1"></i>Pièces</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 text-center py-3">
            <div class="fs-5 fw-bold"><?= esc($typeLabel) ?></div>
            <div class="text-muted small mt-1"><i class="bi bi-house me-1"></i>Type de bien</div>
        </div>
    </div>
</div>

<!-- ══ CORPS PRINCIPAL ══════════════════════════════════════════════════════ -->
<div class="row g-4">

    <!-- ── COLONNE GAUCHE ─────────────────────────────────────────────────── -->
    <div class="col-lg-8">

        <!-- GALERIE PHOTOS -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-images text-warning me-2"></i>Photos
                    <span class="badge bg-secondary ms-1"><?= count($images) ?></span>
                </span>
                <?php if ($canEdit): ?>
                <label class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer">
                    <i class="bi bi-plus-lg me-1"></i>Ajouter
                    <input type="file" id="inp-add-photo" accept="image/jpeg,image/png,image/webp" multiple style="display:none">
                </label>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($images)): ?>
                <!-- Carousel principal -->
                <div id="galleryCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        <?php foreach ($images as $i => $img): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <img src="<?= base_url(esc($img['path'])) ?>"
                                 class="d-block w-100"
                                 style="height:380px;object-fit:cover;cursor:zoom-in"
                                 data-bs-toggle="modal" data-bs-target="#modalPhoto"
                                 data-src="<?= base_url(esc($img['path'])) ?>"
                                 alt="<?= esc($img['alt_text'] ?? $property['title']) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($images) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    <?php endif; ?>
                </div>
                <!-- Vignettes -->
                <?php if (count($images) > 1): ?>
                <div class="d-flex gap-2 p-3 overflow-auto flex-nowrap" id="gallery-thumbs">
                    <?php foreach ($images as $i => $img): ?>
                    <div class="position-relative flex-shrink-0" style="width:88px">
                        <img src="<?= base_url(esc($img['path'])) ?>"
                             class="rounded thumb-img <?= $i === 0 ? 'border border-primary border-2' : 'opacity-75' ?>"
                             style="width:88px;height:64px;object-fit:cover;cursor:pointer"
                             data-index="<?= $i ?>"
                             alt="">
                        <?php if ($img['is_primary']): ?>
                        <span class="position-absolute top-0 start-0 badge bg-primary" style="font-size:.5rem">★</span>
                        <?php endif; ?>
                        <?php if ($canEdit): ?>
                        <button class="btn-delete-img position-absolute top-0 end-0 d-none
                                       bg-danger text-white border-0 rounded-circle d-flex align-items-center justify-content-center"
                                data-img-id="<?= $img['id'] ?>"
                                style="width:20px;height:20px;font-size:.6rem;padding:0">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:220px">
                    <div class="text-center text-muted">
                        <i class="bi bi-image" style="font-size:3rem;opacity:.4"></i>
                        <p class="mt-2 mb-0 small">Aucune photo ajoutée</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- DESCRIPTION -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-file-text text-primary me-2"></i>Description
            </div>
            <div class="card-body" style="line-height:1.8">
                <?php if (!empty($property['description'])): ?>
                    <?= nl2br(esc($property['description'])) ?>
                <?php else: ?>
                    <span class="text-muted fst-italic">Aucune description renseignée.</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- ÉQUIPEMENTS & CARACTÉRISTIQUES DYNAMIQUES -->
        <?php if (!empty($features)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-tags text-info me-2"></i>Équipements & caractéristiques
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php foreach ($features as $key => $val):
                        if ($val === '' || $val === null) continue;
                        $label = ucwords(str_replace('_', ' ', $key));
                    ?>
                    <div class="col-6 col-md-4">
                        <?php if ($val === '1' || $val === 1): ?>
                        <div class="d-flex align-items-center gap-2 p-2 rounded bg-success bg-opacity-10 border border-success-subtle">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span class="small"><?= esc($label) ?></span>
                        </div>
                        <?php else: ?>
                        <div class="d-flex align-items-center gap-2 p-2 rounded bg-light border">
                            <i class="bi bi-circle text-muted"></i>
                            <span class="small text-muted"><?= esc($label) ?> : <strong class="text-dark"><?= esc($val) ?></strong></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- LOCALISATION -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-geo-alt text-danger me-2"></i>Localisation
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <?php if (!empty($property['address'])): ?>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Adresse</div>
                        <div class="fw-semibold"><?= esc($property['address']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($property['city'])): ?>
                    <div class="col-md-3">
                        <div class="text-muted small mb-1">Ville</div>
                        <div class="fw-semibold"><?= esc($property['city']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($property['zone'])): ?>
                    <div class="col-md-3">
                        <div class="text-muted small mb-1">Quartier / Zone</div>
                        <div class="fw-semibold"><?= esc($property['zone']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($hasCoords): ?>
                    <div class="col-12">
                        <div class="text-muted small mb-1">Coordonnées GPS</div>
                        <code class="small"><?= esc($property['latitude']) ?>, <?= esc($property['longitude']) ?></code>
                    </div>
                    <?php endif; ?>
                    <?php if (!$property['address'] && !$property['city'] && !$property['zone'] && !$hasCoords): ?>
                    <div class="col-12 text-muted fst-italic small">Aucune localisation renseignée.</div>
                    <?php endif; ?>
                </div>
                <?php if ($hasCoords): ?>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
                <div id="show-map" style="height:260px;border-radius:.5rem;border:1px solid #dee2e6;z-index:0"></div>
                <script>
                (function(){
                    const lat = <?= (float)$property['latitude'] ?>;
                    const lng = <?= (float)$property['longitude'] ?>;
                    const map = L.map('show-map',{zoomControl:true,scrollWheelZoom:false}).setView([lat,lng],14);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
                        attribution:'© OpenStreetMap',maxZoom:19
                    }).addTo(map);
                    L.marker([lat,lng]).addTo(map)
                     .bindPopup('<strong><?= esc(addslashes($property['title'])) ?></strong><br><?= esc(addslashes($property['address'] ?? '')) ?>')
                     .openPopup();
                    setTimeout(()=>map.invalidateSize(),300);
                })();
                </script>
                <?php endif; ?>
            </div>
        </div>

        <!-- HISTORIQUE DES MODIFICATIONS -->
        <?php if (!empty($history)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-clock-history text-secondary me-2"></i>Historique des modifications</span>
                <span class="badge bg-secondary"><?= count($history) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Champ</th>
                            <th>Ancienne valeur</th>
                            <th>Nouvelle valeur</th>
                            <th>Par</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                        <tr>
                            <td class="text-muted small text-nowrap"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= esc($h['field_changed'] ?? $h['action'] ?? '—') ?></span></td>
                            <td class="text-muted small"><?= esc($h['old_value'] ?? '—') ?></td>
                            <td class="small"><strong><?= esc($h['new_value'] ?? '—') ?></strong></td>
                            <td class="small text-nowrap"><?= esc(trim(($h['user_first_name'] ?? '') . ' ' . ($h['user_last_name'] ?? ''))) ?: '—' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /col-lg-8 -->

    <!-- ── COLONNE DROITE (sidebar) ───────────────────────────────────────── -->
    <div class="col-lg-4">

        <!-- AGENT RESPONSABLE -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-badge text-primary me-2"></i>Agent responsable
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:50px;height:50px">
                        <span class="fw-bold text-primary fs-5">
                            <?= strtoupper(mb_substr($property['first_name'] ?? '?', 0, 1))
                              . strtoupper(mb_substr($property['last_name']  ?? '',  0, 1)) ?>
                        </span>
                    </div>
                    <div>
                        <div class="fw-semibold"><?= esc(($property['first_name'] ?? '') . ' ' . ($property['last_name'] ?? '')) ?></div>
                        <?php if (!empty($property['agent_email'])): ?>
                        <a href="mailto:<?= esc($property['agent_email']) ?>" class="text-muted small text-decoration-none d-block">
                            <i class="bi bi-envelope me-1"></i><?= esc($property['agent_email']) ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARACTÉRISTIQUES DÉTAILLÉES -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-list-check text-success me-2"></i>Caractéristiques
            </div>
            <ul class="list-group list-group-flush">
                <?php
                $chars = [
                    ['icon'=>'bi-house',         'label'=>'Type',           'raw'=> esc($typeLabel)],
                    ['icon'=>$txIcon,             'label'=>'Transaction',    'raw'=> esc($txLabel)],
                    ['icon'=>'bi-rulers',         'label'=>'Surface',        'raw'=> !empty($property['surface']) ? number_format((float)$property['surface'],0).' m²' : null],
                    ['icon'=>'bi-grid',           'label'=>'Pièces',         'raw'=> !empty($property['rooms']) ? (int)$property['rooms'] : null],
                    ['icon'=>'bi-door-open',      'label'=>'Chambres',       'raw'=> !empty($property['bedrooms']) ? (int)$property['bedrooms'] : null],
                    ['icon'=>'bi-droplet',        'label'=>'Salles de bain', 'raw'=> !empty($property['bathrooms']) ? (int)$property['bathrooms'] : null],
                    ['icon'=>'bi-building',       'label'=>'Étage',
                        'raw'=> (isset($property['floor']) && $property['floor'] !== null && $property['floor'] !== '') ? (int)$property['floor'] : null],
                    ['icon'=>'bi-buildings',      'label'=>'Nbre d\'étages', 'raw'=> !empty($property['total_floors']) ? (int)$property['total_floors'] : null],
                    ['icon'=>'bi-p-square',       'label'=>'Parking',
                        'raw'=> $property['parking'] ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-light text-muted border">Non</span>'],
                    ['icon'=>'bi-lamp',           'label'=>'Meublé',
                        'raw'=> $property['furnished'] ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-light text-muted border">Non</span>'],
                ];
                foreach ($chars as $c):
                    if ($c['raw'] === null) continue;
                ?>
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small"><i class="bi <?= $c['icon'] ?> me-2"></i><?= $c['label'] ?></span>
                    <span class="fw-semibold small"><?= $c['raw'] ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- PUBLICATION & STATISTIQUES -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-bar-chart text-info me-2"></i>Publication & stats
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small"><i class="bi bi-eye me-2"></i>Vues</span>
                    <strong><?= (int)($property['views_count'] ?? 0) ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small"><i class="bi bi-calendar-plus me-2"></i>Créé le</span>
                    <span class="small"><?= date('d/m/Y', strtotime($property['created_at'])) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small"><i class="bi bi-pencil-square me-2"></i>Modifié le</span>
                    <span class="small"><?= !empty($property['updated_at']) ? date('d/m/Y H:i', strtotime($property['updated_at'])) : '—' ?></span>
                </li>
                <?php if ($property['is_published'] && !empty($property['published_at'])): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small"><i class="bi bi-send me-2"></i>Publié le</span>
                    <span class="small text-success fw-semibold"><?= date('d/m/Y', strtotime($property['published_at'])) ?></span>
                </li>
                <?php endif; ?>
                <?php if ($property['featured']): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small"><i class="bi bi-star me-2"></i>En vedette</span>
                    <span class="badge bg-warning text-dark">Actif</span>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- ZONE DE DANGER -->
        <?php if ($auth->hasPermission('properties.delete')): ?>
        <div class="card border-0 border-danger-subtle shadow-sm">
            <div class="card-header bg-danger bg-opacity-10 text-danger fw-semibold" style="font-size:.875rem">
                <i class="bi bi-exclamation-triangle me-2"></i>Zone de danger
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/properties/' . $property['id'] . '/delete') ?>"
                      onsubmit="return confirm('Supprimer définitivement ce bien et toutes ses données ?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-1"></i> Supprimer ce bien
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /col-lg-4 -->
</div><!-- /row -->

<!-- ══ MODAL ZOOM PHOTO ═════════════════════════════════════════════════════ -->
<?php if (!empty($images)): ?>
<div class="modal fade" id="modalPhoto" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 py-2">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="modal-photo-img" src="" class="img-fluid" style="max-height:85vh;object-fit:contain" alt="">
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ══ SCRIPTS ═══════════════════════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    // ── Zoom photo → modal ──────────────────────────────────────────
    document.querySelectorAll('[data-bs-target="#modalPhoto"]').forEach(img => {
        img.addEventListener('click', () => {
            document.getElementById('modal-photo-img').src = img.dataset.src;
        });
    });

    // ── Vignettes → carousel ────────────────────────────────────────
    const carousel = document.getElementById('galleryCarousel');
    const thumbs   = document.querySelectorAll('.thumb-img');

    if (carousel && thumbs.length) {
        const bsCarousel = bootstrap.Carousel.getOrCreateInstance(carousel, { ride: false });

        thumbs.forEach(th => {
            th.addEventListener('click', () => bsCarousel.to(parseInt(th.dataset.index)));
        });

        carousel.addEventListener('slid.bs.carousel', e => {
            thumbs.forEach(th => {
                const active = parseInt(th.dataset.index) === e.to;
                th.classList.toggle('opacity-75', !active);
                th.classList.toggle('border',        active);
                th.classList.toggle('border-primary', active);
                th.classList.toggle('border-2',       active);
            });
        });

        // Hover → afficher / cacher bouton supprimer
        document.querySelectorAll('#gallery-thumbs .position-relative').forEach(wrap => {
            const btn = wrap.querySelector('.btn-delete-img');
            if (!btn) return;
            wrap.addEventListener('mouseenter', () => btn.classList.remove('d-none'));
            wrap.addEventListener('mouseleave', () => btn.classList.add('d-none'));
        });
    }

    // ── Suppression image AJAX ──────────────────────────────────────
    document.querySelectorAll('.btn-delete-img').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            if (!confirm('Supprimer cette photo ?')) return;
            const fd = new FormData();
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            fetch('<?= site_url('admin/properties/images/') ?>' + btn.dataset.imgId + '/delete', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(r => r.json())
            .then(d => { if (d.success) btn.closest('.position-relative')?.remove(); })
            .catch(() => alert('Erreur lors de la suppression.'));
        });
    });

    // ── Upload photo AJAX ───────────────────────────────────────────
    const inpPhoto = document.getElementById('inp-add-photo');
    if (inpPhoto) {
        inpPhoto.addEventListener('change', () => {
            if (!inpPhoto.files.length) return;
            const fd = new FormData();
            Array.from(inpPhoto.files).forEach(f => fd.append('images[]', f));
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            fetch('<?= site_url('admin/properties/' . $property['id'] . '/update') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(() => location.reload())
            .catch(() => alert('Erreur upload.'));
        });
    }
})();
</script>

