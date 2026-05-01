<?php
$perms        = session()->get('permissions') ?? [];
$tMeta        = $typeLabels[$client['client_type']]         ?? ['label' => $client['client_type'], 'color' => 'secondary', 'icon' => 'bi-person'];
$sMeta        = $statusLabels[$client['status']]            ?? ['label' => $client['status'],       'color' => 'secondary'];
$srcLbl       = $sourceLabels[$client['source']]            ?? $client['source'];
$orientations = json_decode($client['orientations'] ?? '[]', true) ?? [];

$featureLabels = [];
foreach ($featuresCatalog as $catData) {
    foreach ($catData['items'] as $fKey => $fLabel) {
        $featureLabels[$fKey] = ['label' => $fLabel, 'cat' => $catData['label'], 'icon' => $catData['icon']];
    }
}

$floorLabels = [
    'rdc'     => 'Rez-de-chaussee',
    'bas'     => 'Etages bas (1-3)',
    'moyen'   => 'Etages moyens (4-6)',
    'haut'    => 'Etages hauts (7+)',
    'dernier' => 'Dernier etage',
];

$critCount = count($pivotPropTypes)
    + count($pivotFeatures)
    + count($orientations)
    + (int)!! ($client['demand_type'] ?? null)
    + (int)!! (($client['budget_max'] ?? null) || ($client['budget_min'] ?? null))
    + (int)!! (($client['surface_min'] ?? null) || ($client['surface_max'] ?? null))
    + (int)!! (($client['rooms_min'] ?? null) || ($client['bedrooms_min'] ?? null))
    + (int)!! ($client['bathrooms_min'] ?? null)
    + (int)!! ($client['parking_min']   ?? null)
    + (int)!! ($client['construction_state'] ?? null)
    + (int)!! ($client['furnished']          ?? null);
?>
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <a href="<?= base_url('admin/clients') ?>" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i></a>
    <div class="flex-grow-1">
        <h4 class="mb-0 fw-bold">
            <i class="bi <?= $tMeta['icon'] ?> me-2 text-<?= $tMeta['color'] ?>"></i>
            <?= esc($client['first_name'] . ' ' . $client['last_name']) ?>
        </h4>
        <div class="d-flex gap-1 mt-1 flex-wrap">
            <span class="badge text-bg-<?= $tMeta['color'] ?>"><?= $tMeta['label'] ?></span>
            <span class="badge bg-<?= $sMeta['color'] ?>-subtle text-<?= $sMeta['color'] ?> border border-<?= $sMeta['color'] ?>-subtle"><?= $sMeta['label'] ?></span>
            <?php if (! empty($client['demand_type']) && isset($demandTypeLabels[$client['demand_type']])): ?>
            <?php $dm = $demandTypeLabels[$client['demand_type']]; ?>
            <span class="badge text-bg-<?= $dm['color'] ?>"><i class="bi <?= $dm['icon'] ?> me-1"></i><?= $dm['label'] ?></span>
            <?php endif; ?>
            <?php if (! empty($client['urgency']) && isset($urgencyLabels[$client['urgency']])): ?>
            <?php $um = $urgencyLabels[$client['urgency']]; ?>
            <span class="badge bg-<?= $um['color'] ?>-subtle text-<?= $um['color'] ?> border border-<?= $um['color'] ?>-subtle">
                <i class="bi bi-clock me-1"></i><?= $um['label'] ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php if (in_array('clients.edit', $perms)): ?>
        <a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <?php endif; ?>
        <?php if (in_array('clients.delete', $perms)): ?>
        <form method="POST" action="<?= base_url('admin/clients/' . $client['id'] . '/delete') ?>"
              onsubmit="return confirm('Supprimer ce client ?')">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Supprimer</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->has('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= session('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<ul class="nav nav-tabs mb-0" id="clientTabs" role="tablist" style="border-bottom:2px solid #dee2e6;">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-identite" type="button" role="tab">
            <i class="bi bi-person-vcard me-1"></i>Identite
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-recherche" type="button" role="tab">
            <i class="bi bi-search me-1"></i>Criteres<?php if ($critCount > 0): ?> <span class="badge text-bg-primary ms-1" style="font-size:.65rem;"><?= $critCount ?></span><?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tab-localisation-btn" data-bs-toggle="tab" data-bs-target="#tab-localisation" type="button" role="tab">
            <i class="bi bi-geo-alt me-1"></i>Localisation<?php if (! empty($pivotZones)): ?> <span class="badge text-bg-info ms-1" style="font-size:.65rem;"><?= count($pivotZones) ?></span><?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-caracteristiques" type="button" role="tab">
            <i class="bi bi-star me-1"></i>Caracteristiques<?php if (! empty($pivotFeatures)): ?> <span class="badge text-bg-warning ms-1" style="font-size:.65rem;"><?= count($pivotFeatures) ?></span><?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-suivi" type="button" role="tab">
            <i class="bi bi-bar-chart me-1"></i>Suivi CRM
        </button>
    </li>
</ul>

<div class="tab-content pt-4" id="clientTabContent">

<!-- TAB 1 : Identite -->
<div class="tab-pane fade show active" id="tab-identite" role="tabpanel">
<div class="row g-4">
<div class="col-lg-6">
<div class="card shadow-sm h-100">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-telephone me-1 text-primary"></i> Coordonnees</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <div class="text-muted small mb-1">Telephone</div>
                <a href="tel:<?= esc($client['phone']) ?>" class="fw-semibold text-decoration-none fs-5">
                    <i class="bi bi-telephone-fill me-1 text-primary"></i><?= esc($client['phone']) ?>
                </a>
            </div>
            <?php if ($client['email']): ?>
            <div class="col-12">
                <div class="text-muted small mb-1">Email</div>
                <a href="mailto:<?= esc($client['email']) ?>" class="text-decoration-none">
                    <i class="bi bi-envelope me-1 text-info"></i><?= esc($client['email']) ?>
                </a>
            </div>
            <?php endif; ?>
            <?php if ($client['profession'] || $client['company']): ?><div class="col-12"><hr class="my-1"></div><?php endif; ?>
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
</div>
<div class="col-lg-6">
<?php $hasAddress = $client['address'] || $client['pays_name'] || $client['region_name'] || $client['ville_name'] || $client['postal_code']; ?>
<?php if ($hasAddress): ?>
<div class="card shadow-sm h-100">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-geo-alt me-1 text-info"></i> Adresse</div>
    <div class="card-body">
        <div class="row g-2">
            <?php if ($client['address']): ?><div class="col-12"><span class="text-muted small">Rue — </span><?= esc($client['address']) ?></div><?php endif; ?>
            <?php foreach (['pays_name'=>['Pays','bi-flag'],'region_name'=>['Gouvernorat','bi-map'],'ville_name'=>['Ville','bi-building-fill'],'postal_code'=>['Code postal','bi-mailbox']] as $key=>$meta): ?>
            <?php if ($client[$key]): ?>
            <div class="col-sm-6">
                <div class="text-muted small"><i class="bi <?= $meta[1] ?> me-1"></i><?= $meta[0] ?></div>
                <strong><?= esc($client[$key]) ?></strong>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card shadow-sm h-100">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-geo-alt fs-1 opacity-25 d-block mb-2"></i>Aucune adresse renseignee
    </div>
</div>
<?php endif; ?>
</div>
<?php if ($client['notes']): ?>
<div class="col-12">
    <div class="card shadow-sm">
        <div class="card-header fw-semibold bg-white"><i class="bi bi-chat-text me-1 text-muted"></i> Notes</div>
        <div class="card-body"><p class="mb-0" style="white-space:pre-wrap;"><?= esc($client['notes']) ?></p></div>
    </div>
</div>
<?php endif; ?>
</div>
</div>

<!-- TAB 2 : Criteres -->
<div class="tab-pane fade" id="tab-recherche" role="tabpanel">
<div class="row g-4">

<?php $hasDemand = $client['demand_type'] || $client['budget_min'] || $client['budget_max']
    || $client['urgency'] || $client['budget_flexibility']
    || $client['desired_zone'] || $client['owner_location'] || $client['desired_price']; ?>
<?php if ($hasDemand): ?>
<div class="col-12">
<div class="card shadow-sm">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-currency-dollar me-1 text-warning"></i> Profil de demande</div>
    <div class="card-body"><div class="row g-3">
        <?php if (! empty($client['demand_type']) && isset($demandTypeLabels[$client['demand_type']])): ?>
        <?php $dm = $demandTypeLabels[$client['demand_type']]; ?>
        <div class="col-sm-6 col-lg-3">
            <div class="text-muted small mb-1">Transaction</div>
            <span class="badge text-bg-<?= $dm['color'] ?> fs-6 px-3 py-2"><i class="bi <?= $dm['icon'] ?> me-1"></i><?= $dm['label'] ?></span>
        </div>
        <?php endif; ?>
        <?php if ($client['budget_min'] || $client['budget_max']): ?>
        <div class="col-sm-6 col-lg-3">
            <div class="text-muted small mb-1">Budget</div>
            <div class="fw-semibold">
                <?php if ($client['budget_min'] && $client['budget_max']): ?>
                    <?= number_format($client['budget_min'],0,',',' ') ?> &ndash; <?= number_format($client['budget_max'],0,',',' ') ?> TND
                <?php elseif ($client['budget_min']): ?>
                    min <?= number_format($client['budget_min'],0,',',' ') ?> TND
                <?php else: ?>
                    max <?= number_format($client['budget_max'],0,',',' ') ?> TND
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if (! empty($client['urgency']) && isset($urgencyLabels[$client['urgency']])): ?>
        <?php $um = $urgencyLabels[$client['urgency']]; ?>
        <div class="col-sm-6 col-lg-3">
            <div class="text-muted small mb-1">Urgence</div>
            <span class="badge bg-<?= $um['color'] ?>-subtle text-<?= $um['color'] ?> border border-<?= $um['color'] ?>-subtle fs-6 px-3 py-2">
                <i class="bi bi-clock me-1"></i><?= $um['label'] ?>
            </span>
        </div>
        <?php endif; ?>
        <?php if (! empty($client['budget_flexibility']) && isset($budgetFlexLabels[$client['budget_flexibility']])): ?>
        <?php $bm = $budgetFlexLabels[$client['budget_flexibility']]; ?>
        <div class="col-sm-6 col-lg-3">
            <div class="text-muted small mb-1">Flexibilite budget</div>
            <span class="badge bg-<?= $bm['color'] ?>-subtle text-<?= $bm['color'] ?> border border-<?= $bm['color'] ?>-subtle fs-6 px-3 py-2"><?= $bm['label'] ?></span>
        </div>
        <?php endif; ?>
        <?php if ($client['desired_zone']): ?>
        <div class="col-sm-6 col-lg-4"><div class="text-muted small mb-1">Zone souhaitee</div><span><i class="bi bi-geo me-1 text-info"></i><?= esc($client['desired_zone']) ?></span></div>
        <?php endif; ?>
        <?php if ($client['owner_location']): ?>
        <div class="col-sm-6 col-lg-4"><div class="text-muted small mb-1">Localisation du bien</div><span><i class="bi bi-pin-map me-1 text-info"></i><?= esc($client['owner_location']) ?></span></div>
        <?php endif; ?>
        <?php if ($client['desired_price']): ?>
        <div class="col-sm-6 col-lg-4"><div class="text-muted small mb-1">Prix souhaite</div><strong><?= number_format($client['desired_price'],0,',',' ') ?> TND</strong></div>
        <?php endif; ?>
    </div></div>
</div>
</div>
<?php endif; ?>

<?php $hasTech = $client['surface_min'] || $client['surface_max'] || $client['rooms_min']
    || $client['bedrooms_min'] || ($client['bathrooms_min'] ?? null) || ($client['parking_min'] ?? null)
    || $client['floor_preferred'] || $client['has_elevator']
    || ($client['construction_state'] ?? null) || ($client['furnished'] ?? null); ?>

<?php if (! empty($pivotPropTypes)): ?>
<div class="col-lg-<?= $hasTech ? '6' : '12' ?>">
<div class="card shadow-sm h-100">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-building me-1 text-primary"></i> Types de biens recherches</div>
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
</div>
<?php endif; ?>

<?php if ($hasTech): ?>
<div class="col-lg-<?= ! empty($pivotPropTypes) ? '6' : '12' ?>">
<div class="card shadow-sm h-100">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-rulers me-1 text-primary"></i> Criteres techniques</div>
    <div class="card-body"><div class="row g-2">
        <?php if ($client['surface_min'] || $client['surface_max']): ?>
        <div class="col-6">
            <div class="text-muted small">Surface</div>
            <strong>
                <?php if ($client['surface_min'] && $client['surface_max']): ?><?= (int)$client['surface_min'] ?>&ndash;<?= (int)$client['surface_max'] ?> m&sup2;
                <?php elseif ($client['surface_min']): ?>min <?= (int)$client['surface_min'] ?> m&sup2;
                <?php else: ?>max <?= (int)$client['surface_max'] ?> m&sup2;<?php endif; ?>
            </strong>
        </div>
        <?php endif; ?>
        <?php if ($client['rooms_min']): ?><div class="col-6"><div class="text-muted small">Pieces min</div><strong><?= (int)$client['rooms_min'] ?></strong></div><?php endif; ?>
        <?php if ($client['bedrooms_min']): ?><div class="col-6"><div class="text-muted small">Chambres min</div><strong><?= (int)$client['bedrooms_min'] ?></strong></div><?php endif; ?>
        <?php if ($client['bathrooms_min'] ?? null): ?><div class="col-6"><div class="text-muted small">Salles de bain min</div><strong><?= (int)$client['bathrooms_min'] ?></strong></div><?php endif; ?>
        <?php if ($client['parking_min'] ?? null): ?><div class="col-6"><div class="text-muted small">Parkings min</div><strong><?= (int)$client['parking_min'] ?></strong></div><?php endif; ?>
        <?php if (! empty($client['floor_preferred']) && isset($floorLabels[$client['floor_preferred']])): ?>
        <div class="col-6"><div class="text-muted small">Etage prefere</div><span><i class="bi bi-layers me-1 text-secondary"></i><?= $floorLabels[$client['floor_preferred']] ?></span></div>
        <?php endif; ?>
        <?php if (! empty($client['construction_state']) && isset($constructionStateLabels[$client['construction_state']])): ?>
        <?php $csm = $constructionStateLabels[$client['construction_state']]; $csc = $csm['color'] === 'light' ? 'secondary' : $csm['color']; ?>
        <div class="col-6"><div class="text-muted small">Etat du bien</div>
            <span class="badge bg-<?= $csc ?>-subtle text-<?= $csc ?> border px-2 py-1"><i class="bi <?= $csm['icon'] ?> me-1"></i><?= $csm['label'] ?></span>
        </div>
        <?php endif; ?>
        <?php if (! empty($client['furnished']) && isset($furnishedLabels[$client['furnished']])): ?>
        <?php $fm = $furnishedLabels[$client['furnished']]; $fc = $fm['color'] === 'light' ? 'secondary' : $fm['color']; ?>
        <div class="col-6"><div class="text-muted small">Meublement</div>
            <span class="badge bg-<?= $fc ?>-subtle text-<?= $fc ?> border px-2 py-1"><i class="bi <?= $fm['icon'] ?> me-1"></i><?= $fm['label'] ?></span>
        </div>
        <?php endif; ?>
        <div class="col-6">
            <div class="text-muted small">Ascenseur</div>
            <?php if ($client['has_elevator']): ?>
            <span class="badge text-bg-success"><i class="bi bi-check2 me-1"></i>Requis</span>
            <?php else: ?>
            <span class="text-muted small fst-italic">Non requis</span>
            <?php endif; ?>
        </div>
    </div></div>
</div>
</div>
<?php endif; ?>

<?php if (! $hasDemand && empty($pivotPropTypes) && ! $hasTech): ?>
<div class="col-12 text-center text-muted py-5">
    <i class="bi bi-search fs-1 opacity-25 d-block mb-2"></i>Aucun critere renseigne
    <?php if (in_array('clients.edit', $perms)): ?>
    <div class="mt-3"><a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Completer le profil</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>
</div>

<!-- TAB 3 : Localisation -->
<div class="tab-pane fade" id="tab-localisation" role="tabpanel">
<?php if (! empty($pivotZones)): ?>
<?php $zoneTypeLbl = ['pays'=>'Pays','region'=>'Gouvernorat','ville'=>'Ville','quartier'=>'Quartier']; ?>
<div class="card shadow-sm">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-geo-alt-fill me-1 text-info"></i> Zones de recherche</div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php foreach ($pivotZones as $pz): ?>
            <span class="badge text-bg-info px-3 py-2 fs-6">
                <i class="bi bi-geo-alt me-1"></i><?= esc($pz['name']) ?>
                <span class="opacity-75 ms-1 small">(<?= $zoneTypeLbl[$pz['type']] ?? $pz['type'] ?>)</span>
            </span>
            <?php endforeach; ?>
        </div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <div id="zoneMap" style="height:400px;border-radius:.5rem;border:1px solid #dee2e6;"></div>
        <p class="text-muted small mt-2 mb-0"><i class="bi bi-geo-alt-fill me-1 text-info"></i>Zones de recherche du client</p>
    </div>
</div>
<?php else: ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-geo-alt fs-1 opacity-25 d-block mb-2"></i>Aucune zone geolocalise
    <?php if (in_array('clients.edit', $perms)): ?>
    <div class="mt-3"><a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-map me-1"></i>Ajouter des zones</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>

<!-- TAB 4 : Caracteristiques -->
<div class="tab-pane fade" id="tab-caracteristiques" role="tabpanel">
<div class="row g-4">
<?php if (! empty($orientations)): ?>
<div class="col-lg-4">
<div class="card shadow-sm h-100">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-compass me-1 text-secondary"></i> Orientations preferees</div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($orientations as $oKey): ?>
            <?php if (isset($orientationLabels[$oKey])): ?>
            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 fs-6">
                <i class="bi bi-compass me-1"></i><?= esc($orientationLabels[$oKey]) ?>
            </span>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>
<?php endif; ?>

<?php if (! empty($pivotFeatures)): ?>
<div class="col-lg-<?= empty($orientations) ? '12' : '8' ?>">
<div class="card shadow-sm">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-star me-1 text-warning"></i> Caracteristiques souhaitees</div>
    <div class="card-body">
        <?php
        $byCategory = [];
        foreach ($pivotFeatures as $fKey => $reqType) {
            if (! isset($featureLabels[$fKey])) continue;
            $cat = $featureLabels[$fKey]['cat'];
            $byCategory[$cat][] = ['label' => $featureLabels[$fKey]['label'], 'req' => $reqType, 'catIcon' => $featureLabels[$fKey]['icon']];
        }
        ?>
        <?php foreach ($byCategory as $catLabel => $items): ?>
        <div class="mb-3">
            <h6 class="fw-semibold text-muted small mb-2"><i class="bi <?= esc($items[0]['catIcon']) ?> me-1"></i><?= esc($catLabel) ?></h6>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($items as $feat): ?>
                <?php $isReq = $feat['req'] === 'obligatoire'; ?>
                <span class="badge <?= $isReq ? 'text-bg-danger' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' ?> px-3 py-2 fs-6">
                    <?= esc($feat['label']) ?><span class="opacity-75 ms-1 small"><?= $isReq ? '· obligatoire' : '· optionnel' ?></span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</div>
<?php endif; ?>

<?php if (empty($orientations) && empty($pivotFeatures)): ?>
<div class="col-12 text-center text-muted py-5">
    <i class="bi bi-star fs-1 opacity-25 d-block mb-2"></i>Aucune caracteristique renseignee
    <?php if (in_array('clients.edit', $perms)): ?>
    <div class="mt-3"><a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Completer</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>
</div>

<!-- TAB 5 : Suivi CRM -->
<div class="tab-pane fade" id="tab-suivi" role="tabpanel">
<div class="row g-4">

<div class="col-lg-4">
<div class="card shadow-sm">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-diagram-3 me-1 text-success"></i> CRM</div>
    <div class="card-body">
        <dl class="row mb-0 g-2">
            <dt class="col-5 text-muted small fw-normal">Statut</dt>
            <dd class="col-7"><span class="badge bg-<?= $sMeta['color'] ?>-subtle text-<?= $sMeta['color'] ?> border border-<?= $sMeta['color'] ?>-subtle"><?= $sMeta['label'] ?></span></dd>
            <dt class="col-5 text-muted small fw-normal">Agent</dt>
            <dd class="col-7"><?= $client['agent_first'] ? esc($client['agent_first'] . ' ' . $client['agent_last']) : '<span class="text-muted fst-italic">Non assigne</span>' ?></dd>
            <dt class="col-5 text-muted small fw-normal">Source</dt>
            <dd class="col-7"><?= esc($srcLbl) ?></dd>
            <dt class="col-5 text-muted small fw-normal">Cree le</dt>
            <dd class="col-7 small"><?= date('d/m/Y', strtotime($client['created_at'])) ?></dd>
            <?php if ($client['updated_at'] && $client['updated_at'] !== $client['created_at']): ?>
            <dt class="col-5 text-muted small fw-normal">Modifie</dt>
            <dd class="col-7 small"><?= date('d/m/Y', strtotime($client['updated_at'])) ?></dd>
            <?php endif; ?>
        </dl>
    </div>
</div>
</div>

<div class="col-lg-4">
<?php
$scoreMax = 8; $score = 0; $missing = [];
$checks = ['demand_type'=>'Type de transaction','budget_max'=>'Budget max','surface_max'=>'Surface max',
           'rooms_min'=>'Pieces min','construction_state'=>'Etat du bien','furnished'=>'Meublement'];
foreach ($checks as $field => $label) {
    if (! empty($client[$field])) { $score++; } else { $missing[] = $label; }
}
if (! empty($pivotPropTypes)) { $score++; } else { $missing[] = 'Types de biens'; }
if (! empty($pivotZones))     { $score++; } else { $missing[] = 'Zones recherchees'; }
$scoreColor = $score >= 7 ? 'success' : ($score >= 4 ? 'warning' : 'danger');
?>
<div class="card shadow-sm">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-bar-chart me-1 text-primary"></i> Completude du profil</div>
    <div class="card-body text-center py-3">
        <div class="display-6 fw-bold text-<?= $scoreColor ?>"><?= $score ?>/<?= $scoreMax ?></div>
        <div class="progress mt-2 mb-3" style="height:8px;">
            <div class="progress-bar bg-<?= $scoreColor ?>" style="width:<?= round($score / $scoreMax * 100) ?>%"></div>
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
</div>

<?php if (in_array('clients.edit', $perms)): ?>
<div class="col-lg-4">
<div class="card shadow-sm">
    <div class="card-header fw-semibold bg-white"><i class="bi bi-lightning me-1 text-warning"></i> Actions rapides</div>
    <div class="card-body d-grid gap-2">
        <a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifier ce client
        </a>
        <a href="<?= base_url('admin/leads/create?client_id=' . $client['id']) ?>" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-funnel me-1"></i>Creer un lead
        </a>
        <a href="<?= base_url('admin/visits/create?client_id=' . $client['id']) ?>" class="btn btn-outline-info btn-sm">
            <i class="bi bi-calendar-check me-1"></i>Planifier une visite
        </a>
    </div>
</div>
</div>
<?php endif; ?>

</div>
</div>

</div><!-- /.tab-content -->

<?php if (! empty($pivotZones)): ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    'use strict';
    var mapInited = false;
    document.getElementById('tab-localisation-btn').addEventListener('shown.bs.tab', function () {
        if (mapInited) return;
        mapInited = true;
        var zonesData = <?= json_encode(array_map(function($z) {
            return ['name'=>$z['name'],'lat'=>$z['latitude']!==null?(float)$z['latitude']:null,'lng'=>$z['longitude']!==null?(float)$z['longitude']:null];
        }, $pivotZones)) ?>;
        var map = L.map('zoneMap').setView([33.8869, 9.5375], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>', maxZoom: 18
        }).addTo(map);
        var bounds = [];
        function placeMarker(lat, lng, name) {
            bounds.push([lat, lng]);
            L.circleMarker([lat, lng], {radius:11,color:'#0dcaf0',fillColor:'#0dcaf0',fillOpacity:.4,weight:2})
             .addTo(map).bindTooltip(name, {permanent:true,direction:'top',className:'fw-semibold'});
        }
        Promise.all(zonesData.map(function (z) {
            if (z.lat && z.lng) { placeMarker(z.lat, z.lng, z.name); return Promise.resolve(); }
            return fetch('https://nominatim.openstreetmap.org/search?q='+encodeURIComponent(z.name+', Tunisie')+'&format=json&limit=1',
                {headers:{'Accept-Language':'fr'}})
                .then(function(r){return r.json();})
                .then(function(res){if(res&&res[0])placeMarker(parseFloat(res[0].lat),parseFloat(res[0].lon),z.name);})
                .catch(function(){});
        })).then(function(){if(bounds.length)map.fitBounds(bounds,{padding:[40,40],maxZoom:10});});
    });
})();
</script>
<?php endif; ?>
