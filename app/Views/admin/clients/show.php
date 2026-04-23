<?php
$perms       = session()->get('permissions') ?? [];
$tMeta       = $typeLabels[$client['client_type']] ?? ['label' => $client['client_type'], 'color' => 'secondary', 'icon' => 'bi-person'];
$sMeta       = $statusLabels[$client['status']]    ?? ['label' => $client['status'], 'color' => 'secondary'];
$srcLbl      = $sourceLabels[$client['source']]    ?? $client['source'];
$orientations= json_decode($client['orientations'] ?? '[]', true) ?? [];

$featureLabels = [];
foreach ($featuresCatalog as $catKey => $catData) {
    foreach ($catData['items'] as $fKey => $fLabel) {
        $featureLabels[$fKey] = ['label' => $fLabel, 'cat' => $catData['label'], 'icon' => $catData['icon']];
    }
}

$floorLabels = [
    'rdc'     => 'Rez-de-chaussée',
    'bas'     => 'Étages bas (1-3)',
    'moyen'   => 'Étages moyens (4-6)',
    'haut'    => 'Étages hauts (7+)',
    'dernier' => 'Dernier étage',
];
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <a href="<?= base_url('admin/clients') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi <?= $tMeta['icon'] ?> me-2 text-<?= $tMeta['color'] ?>"></i>
            <?= esc($client['first_name'] . ' ' . $client['last_name']) ?>
        </h4>
        <div class="d-flex gap-1 mt-1 flex-wrap">
            <span class="badge text-bg-<?= $tMeta['color'] ?>"><?= $tMeta['label'] ?></span>
            <span class="badge bg-<?= $sMeta['color'] ?>-subtle text-<?= $sMeta['color'] ?> border border-<?= $sMeta['color'] ?>-subtle">
                <?= $sMeta['label'] ?>
            </span>
            <?php if (! empty($client['demand_type']) && isset($demandTypeLabels[$client['demand_type']])): ?>
            <?php $dm = $demandTypeLabels[$client['demand_type']]; ?>
            <span class="badge text-bg-<?= $dm['color'] ?>">
                <i class="bi <?= $dm['icon'] ?> me-1"></i><?= $dm['label'] ?>
            </span>
            <?php endif; ?>
            <?php if (! empty($client['urgency']) && isset($urgencyLabels[$client['urgency']])): ?>
            <?php $um = $urgencyLabels[$client['urgency']]; ?>
            <span class="badge bg-<?= $um['color'] ?>-subtle text-<?= $um['color'] ?> border border-<?= $um['color'] ?>-subtle">
                <i class="bi bi-clock me-1"></i><?= $um['label'] ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="ms-auto d-flex gap-2 flex-wrap">
        <?php if (in_array('clients.edit', $perms)): ?>
        <a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>"
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <?php endif; ?>
        <?php if (in_array('clients.delete', $perms)): ?>
        <form method="POST"
              action="<?= base_url('admin/clients/' . $client['id'] . '/delete') ?>"
              onsubmit="return confirm('Supprimer ce client ?')">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Supprimer
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">

    <!-- ── Colonne principale ─────────────────────────────── -->
    <div class="col-lg-8 d-flex flex-column gap-4">

        <!-- Coordonnées -->
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-person-vcard me-1 text-primary"></i> Coordonnées
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Téléphone</div>
                        <a href="tel:<?= esc($client['phone']) ?>" class="fw-semibold text-decoration-none">
                            <i class="bi bi-telephone me-1 text-primary"></i><?= esc($client['phone']) ?>
                        </a>
                    </div>
                    <?php if ($client['email']): ?>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Email</div>
                        <a href="mailto:<?= esc($client['email']) ?>" class="text-decoration-none">
                            <i class="bi bi-envelope me-1 text-info"></i><?= esc($client['email']) ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if ($client['profession']): ?>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Profession</div>
                        <span><i class="bi bi-briefcase me-1 text-secondary"></i><?= esc($client['profession']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($client['company']): ?>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Entreprise</div>
                        <span><i class="bi bi-building me-1 text-secondary"></i><?= esc($client['company']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Adresse -->
        <?php $hasAddress = $client['address'] || $client['pays_name'] || $client['region_name'] || $client['ville_name'] || $client['postal_code']; ?>
        <?php if ($hasAddress): ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-geo-alt me-1 text-info"></i> Adresse
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php if ($client['address']): ?>
                    <div class="col-12">
                        <span class="text-muted small">Rue — </span><?= esc($client['address']) ?>
                    </div>
                    <?php endif; ?>
                    <?php foreach ([
                        'pays_name'   => 'Pays',
                        'region_name' => 'Gouvernorat',
                        'ville_name'  => 'Ville',
                        'postal_code' => 'Code postal',
                    ] as $key => $lbl): ?>
                    <?php if ($client[$key]): ?>
                    <div class="col-sm-4">
                        <div class="text-muted small"><?= $lbl ?></div>
                        <strong><?= esc($client[$key]) ?></strong>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Profil de demande -->
        <?php
        $hasDemand = $client['demand_type'] || $client['budget_min'] || $client['budget_max']
                  || $client['urgency'] || $client['budget_flexibility']
                  || $client['desired_zone'] || $client['owner_location'] || $client['desired_price'];
        ?>
        <?php if ($hasDemand): ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-currency-dollar me-1 text-warning"></i> Profil de demande
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php if (! empty($client['demand_type']) && isset($demandTypeLabels[$client['demand_type']])): ?>
                    <?php $dm = $demandTypeLabels[$client['demand_type']]; ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Transaction</div>
                        <span class="badge text-bg-<?= $dm['color'] ?> fs-6 fw-semibold px-3 py-2">
                            <i class="bi <?= $dm['icon'] ?> me-1"></i><?= $dm['label'] ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <?php if ($client['budget_min'] || $client['budget_max']): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Budget</div>
                        <div class="fw-semibold">
                            <?php if ($client['budget_min'] && $client['budget_max']): ?>
                                <?= number_format($client['budget_min'], 0, ',', ' ') ?> – <?= number_format($client['budget_max'], 0, ',', ' ') ?> TND
                            <?php elseif ($client['budget_min']): ?>
                                min <?= number_format($client['budget_min'], 0, ',', ' ') ?> TND
                            <?php else: ?>
                                max <?= number_format($client['budget_max'], 0, ',', ' ') ?> TND
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (! empty($client['urgency']) && isset($urgencyLabels[$client['urgency']])): ?>
                    <?php $um = $urgencyLabels[$client['urgency']]; ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Urgence</div>
                        <span class="badge bg-<?= $um['color'] ?>-subtle text-<?= $um['color'] ?> border border-<?= $um['color'] ?>-subtle fs-6 px-3 py-2">
                            <i class="bi bi-clock me-1"></i><?= $um['label'] ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <?php if (! empty($client['budget_flexibility']) && isset($budgetFlexLabels[$client['budget_flexibility']])): ?>
                    <?php $bm = $budgetFlexLabels[$client['budget_flexibility']]; ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Flexibilité budget</div>
                        <span class="badge bg-<?= $bm['color'] ?>-subtle text-<?= $bm['color'] ?> border border-<?= $bm['color'] ?>-subtle fs-6 px-3 py-2">
                            <?= $bm['label'] ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <?php if ($client['desired_zone']): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Zone souhaitée</div>
                        <span><i class="bi bi-geo me-1 text-info"></i><?= esc($client['desired_zone']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if ($client['owner_location']): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Localisation du bien</div>
                        <span><i class="bi bi-pin-map me-1 text-info"></i><?= esc($client['owner_location']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if ($client['desired_price']): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Prix souhaité</div>
                        <strong><?= number_format($client['desired_price'], 0, ',', ' ') ?> TND</strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Types de biens -->
        <?php if (! empty($pivotPropTypes)): ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-building me-1 text-primary"></i> Types de biens recherchés
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($pivotPropTypes as $pt): ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6">
                        <?php if (! empty($pt['icon'])): ?><i class="bi <?= esc($pt['icon']) ?> me-1"></i><?php endif; ?>
                        <?= esc($pt['name']) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Zones recherchées + carte -->
        <?php if (! empty($pivotZones)): ?>
        <?php
        $zoneTypeLbl = [
            'pays'     => 'Pays',
            'region'   => 'Gouvernorat',
            'ville'    => 'Ville',
            'quartier' => 'Quartier',
        ];
        ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-geo-alt-fill me-1 text-info"></i> Zones de recherche
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php foreach ($pivotZones as $pz): ?>
                    <span class="badge text-bg-info px-3 py-2 fs-6">
                        <i class="bi bi-geo-alt me-1"></i>
                        <?= esc($pz['name']) ?>
                        <span class="opacity-75 ms-1 small">(<?= $zoneTypeLbl[$pz['type']] ?? $pz['type'] ?>)</span>
                    </span>
                    <?php endforeach; ?>
                </div>
                <div id="zoneMap" style="height:280px; border-radius:.5rem; border:1px solid #dee2e6;"></div>
                <p class="text-muted small mt-1 mb-0">
                    <i class="bi bi-info-circle me-1"></i>Visualisation via OpenStreetMap (Nominatim)
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Orientations -->
        <?php if (! empty($orientations)): ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-compass me-1 text-secondary"></i> Orientations préférées
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($orientations as $oKey): ?>
                    <?php if (isset($orientationLabels[$oKey])): ?>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 fs-6">
                        <?= esc($orientationLabels[$oKey]) ?>
                    </span>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Caractéristiques souhaitées -->
        <?php if (! empty($pivotFeatures)): ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-star me-1 text-warning"></i> Caractéristiques souhaitées
            </div>
            <div class="card-body">
                <?php
                $byCategory = [];
                foreach ($pivotFeatures as $fKey => $reqType) {
                    if (! isset($featureLabels[$fKey])) continue;
                    $cat = $featureLabels[$fKey]['cat'];
                    $byCategory[$cat][] = [
                        'label'   => $featureLabels[$fKey]['label'],
                        'req'     => $reqType,
                        'catIcon' => $featureLabels[$fKey]['icon'],
                    ];
                }
                ?>
                <?php foreach ($byCategory as $catLabel => $items): ?>
                <div class="mb-3">
                    <h6 class="fw-semibold text-muted small mb-2">
                        <i class="bi <?= esc($items[0]['catIcon']) ?> me-1"></i><?= esc($catLabel) ?>
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($items as $feat): ?>
                        <?php $isReq = $feat['req'] === 'obligatoire'; ?>
                        <span class="badge <?= $isReq ? 'text-bg-danger' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' ?> px-3 py-2 fs-6">
                            <?= esc($feat['label']) ?>
                            <span class="opacity-75 ms-1 small"><?= $isReq ? '· obligatoire' : '· optionnel' ?></span>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Critères techniques -->
        <?php
        $hasTech = $client['surface_min'] || $client['surface_max'] || $client['rooms_min']
                || $client['bedrooms_min'] || $client['floor_preferred'] || $client['has_elevator'];
        ?>
        <?php if ($hasTech): ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-rulers me-1 text-primary"></i> Critères techniques
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php if ($client['surface_min'] || $client['surface_max']): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Surface</div>
                        <strong>
                            <?php if ($client['surface_min'] && $client['surface_max']): ?>
                                <?= (int)$client['surface_min'] ?>–<?= (int)$client['surface_max'] ?> m²
                            <?php elseif ($client['surface_min']): ?>
                                min <?= (int)$client['surface_min'] ?> m²
                            <?php else: ?>
                                max <?= (int)$client['surface_max'] ?> m²
                            <?php endif; ?>
                        </strong>
                    </div>
                    <?php endif; ?>

                    <?php if ($client['rooms_min']): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Pièces (min)</div>
                        <strong><?= (int)$client['rooms_min'] ?></strong>
                    </div>
                    <?php endif; ?>

                    <?php if ($client['bedrooms_min']): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Chambres (min)</div>
                        <strong><?= (int)$client['bedrooms_min'] ?></strong>
                    </div>
                    <?php endif; ?>

                    <?php if (! empty($client['floor_preferred']) && isset($floorLabels[$client['floor_preferred']])): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Étage préféré</div>
                        <span><i class="bi bi-layers me-1 text-secondary"></i><?= $floorLabels[$client['floor_preferred']] ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="col-sm-6 col-lg-4">
                        <div class="text-muted small mb-1">Ascenseur</div>
                        <?php if ($client['has_elevator']): ?>
                        <span class="badge text-bg-success"><i class="bi bi-check2 me-1"></i>Requis</span>
                        <?php else: ?>
                        <span class="text-muted small fst-italic">Non requis</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Notes -->
        <?php if ($client['notes']): ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-chat-text me-1 text-muted"></i> Notes
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space:pre-wrap;"><?= esc($client['notes']) ?></p>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- ── Colonne latérale ───────────────────────────────── -->
    <div class="col-lg-4 d-flex flex-column gap-4">

        <!-- CRM -->
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-diagram-3 me-1 text-success"></i> CRM
            </div>
            <div class="card-body">
                <dl class="row mb-0 g-2">
                    <dt class="col-5 text-muted small fw-normal">Statut</dt>
                    <dd class="col-7">
                        <span class="badge bg-<?= $sMeta['color'] ?>-subtle text-<?= $sMeta['color'] ?> border border-<?= $sMeta['color'] ?>-subtle">
                            <?= $sMeta['label'] ?>
                        </span>
                    </dd>

                    <dt class="col-5 text-muted small fw-normal">Agent</dt>
                    <dd class="col-7">
                        <?= $client['agent_first']
                            ? esc($client['agent_first'] . ' ' . $client['agent_last'])
                            : '<span class="text-muted fst-italic">Non assigné</span>' ?>
                    </dd>

                    <dt class="col-5 text-muted small fw-normal">Source</dt>
                    <dd class="col-7"><?= esc($srcLbl) ?></dd>

                    <dt class="col-5 text-muted small fw-normal">Créé le</dt>
                    <dd class="col-7 small"><?= date('d/m/Y', strtotime($client['created_at'])) ?></dd>

                    <?php if ($client['updated_at'] && $client['updated_at'] !== $client['created_at']): ?>
                    <dt class="col-5 text-muted small fw-normal">Modifié</dt>
                    <dd class="col-7 small"><?= date('d/m/Y', strtotime($client['updated_at'])) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <!-- Complétude du profil -->
        <?php
        $score = 0;
        if ($client['demand_type'])   $score++;
        if ($client['budget_max'])     $score++;
        if (! empty($pivotPropTypes))  $score++;
        if (! empty($pivotZones))      $score++;
        if (! empty($pivotFeatures))   $score++;
        if ($client['surface_max'])    $score++;
        $scoreMax   = 6;
        $scoreColor = $score >= 5 ? 'success' : ($score >= 3 ? 'warning' : 'danger');
        $missing = [];
        if (! $client['demand_type'])  $missing[] = 'Type de transaction';
        if (! $client['budget_max'])   $missing[] = 'Budget max';
        if (empty($pivotPropTypes))    $missing[] = 'Types de biens';
        if (empty($pivotZones))        $missing[] = 'Zones recherchées';
        if (empty($pivotFeatures))     $missing[] = 'Caractéristiques';
        if (! $client['surface_max'])  $missing[] = 'Surface max';
        ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-bar-chart me-1 text-primary"></i> Complétude du profil
            </div>
            <div class="card-body text-center py-3">
                <div class="display-6 fw-bold text-<?= $scoreColor ?>"><?= $score ?>/<?= $scoreMax ?></div>
                <div class="progress mt-2 mb-3" style="height:8px;">
                    <div class="progress-bar bg-<?= $scoreColor ?>"
                         style="width:<?= round($score / $scoreMax * 100) ?>%"></div>
                </div>
                <?php if (! empty($missing)): ?>
                <div class="text-start">
                    <div class="text-muted small fw-semibold mb-1">Champs manquants :</div>
                    <?php foreach ($missing as $m): ?>
                    <div class="small text-muted"><i class="bi bi-x-circle text-danger me-1"></i><?= $m ?></div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <span class="badge text-bg-success"><i class="bi bi-check-all me-1"></i>Profil complet</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions rapides -->
        <?php if (in_array('clients.edit', $perms)): ?>
        <div class="card shadow-sm">
            <div class="card-body d-grid gap-2">
                <a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>"
                   class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i>Modifier ce client
                </a>
                <a href="<?= base_url('admin/leads/create?client_id=' . $client['id']) ?>"
                   class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-funnel me-1"></i>Créer un lead
                </a>
                <a href="<?= base_url('admin/visits/create?client_id=' . $client['id']) ?>"
                   class="btn btn-outline-info btn-sm">
                    <i class="bi bi-calendar-check me-1"></i>Planifier une visite
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php if (! empty($pivotZones)): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLfI=" crossorigin=""></script>
<script>
(function () {
    'use strict';
    var zoneNames = <?= json_encode(array_column($pivotZones, 'name')) ?>;
    var map = L.map('zoneMap').setView([33.8869, 9.5375], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
        maxZoom: 18,
    }).addTo(map);
    // Forcer le recalcul de taille (tuiles grises sinon dans un layout Bootstrap)
    setTimeout(function () { map.invalidateSize(); }, 200);
    var bounds = [];
    Promise.all(zoneNames.map(function (name) {
        return fetch(
            'https://nominatim.openstreetmap.org/search?q=' +
            encodeURIComponent(name + ', Tunisie') + '&format=json&limit=1',
            { headers: { 'Accept-Language': 'fr' } }
        )
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res && res[0]) {
                var lat = parseFloat(res[0].lat);
                var lng = parseFloat(res[0].lon);
                bounds.push([lat, lng]);
                L.circleMarker([lat, lng], {
                    radius: 11, color: '#0dcaf0', fillColor: '#0dcaf0', fillOpacity: 0.4, weight: 2,
                }).addTo(map).bindTooltip(name, { permanent: true, direction: 'top', className: 'fw-semibold' });
            }
        })
        .catch(function () {});
    })).then(function () {
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 10 });
            map.invalidateSize();
        }
    });
})();
</script>
<?php endif; ?>

