
<?php
$isEdit = !empty($lead['id']);
$selTypes   = $lead['property_types_arr']  ?? (array) json_decode($lead['property_types'] ?? '[]', true);
$selZones   = $lead['desired_zone_ids_arr'] ?? (array) json_decode($lead['desired_zone_ids'] ?? '[]', true);
$selZones   = array_map('intval', $selZones);

// Build zone hierarchy for display
$parentZones = [];
$childZones  = [];
foreach (($zones ?? []) as $z) {
    if (empty($z['parent_id'])) {
        $parentZones[$z['id']] = $z['name'];
    } else {
        $childZones[(int)$z['parent_id']][] = $z;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><?= $isEdit ? 'Modifier le lead' : 'Nouveau lead' ?></h2>
        <small class="text-muted"><?= $isEdit ? esc($lead['first_name'] . ' ' . $lead['last_name']) : 'Créer un prospect' ?></small>
    </div>
    <a href="<?= site_url('admin/leads') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Erreur de validation :</strong>
        <ul class="mb-0 mt-1">
            <?php foreach ((array)session('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="post" action="<?= $isEdit ? site_url('admin/leads/' . $lead['id'] . '/update') : site_url('admin/leads/store') ?>">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- ============ COLONNE PRINCIPALE ============ -->
        <div class="col-lg-8">

            <!-- Contact -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong><i class="bi bi-person me-2 text-primary"></i>Informations du contact</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required
                                   value="<?= esc(old('first_name', $lead['first_name'] ?? '')) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required
                                   value="<?= esc(old('last_name', $lead['last_name'] ?? '')) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= esc(old('email', $lead['email'] ?? '')) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required
                                   value="<?= esc(old('phone', $lead['phone'] ?? '')) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projet immo -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong><i class="bi bi-house me-2 text-primary"></i>Projet immobilier</strong></div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Transaction -->
                        <div class="col-sm-6">
                            <label class="form-label">Type de transaction</label>
                            <select name="transaction_type" class="form-select">
                                <option value="">— Sélectionner —</option>
                                <option value="buy"  <?= old('transaction_type', $lead['transaction_type'] ?? '') === 'buy'  ? 'selected' : '' ?>>Achat</option>
                                <option value="rent" <?= old('transaction_type', $lead['transaction_type'] ?? '') === 'rent' ? 'selected' : '' ?>>Location</option>
                            </select>
                        </div>

                        <!-- Date limite -->
                        <div class="col-sm-6">
                            <label class="form-label">Date limite d'acquisition</label>
                            <input type="date" name="target_date" class="form-control"
                                   value="<?= esc(old('target_date', $lead['target_date'] ?? '')) ?>">
                        </div>

                        <!-- Types de biens (checkboxes multiples) -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Types de biens souhaités</label>
                            <?php
                            $propTypes = [
                                'apartment' => ['label' => 'Appartement', 'icon' => 'bi-building'],
                                'house'     => ['label' => 'Maison',      'icon' => 'bi-house-door'],
                                'villa'     => ['label' => 'Villa',        'icon' => 'bi-house-heart'],
                                'duplex'    => ['label' => 'Duplex',       'icon' => 'bi-layers'],
                                'studio'    => ['label' => 'Studio',       'icon' => 'bi-door-open'],
                                'land'      => ['label' => 'Terrain',      'icon' => 'bi-map'],
                                'commercial'=> ['label' => 'Commercial',   'icon' => 'bi-shop'],
                                'office'    => ['label' => 'Bureau',       'icon' => 'bi-briefcase'],
                                'warehouse' => ['label' => 'Entrepôt',     'icon' => 'bi-box-seam'],
                                'garage'    => ['label' => 'Garage',       'icon' => 'bi-car-front'],
                            ];
                            ?>
                            <div class="row g-2 mt-1">
                                <?php foreach ($propTypes as $val => $info): ?>
                                <div class="col-6 col-md-4 col-xl-3">
                                    <input type="checkbox" class="btn-check" name="property_types[]"
                                           id="pt_<?= $val ?>" value="<?= $val ?>"
                                           <?= in_array($val, $selTypes) ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary w-100 text-start py-2 px-3" for="pt_<?= $val ?>">
                                        <i class="bi <?= $info['icon'] ?> me-2"></i><?= $info['label'] ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Budget -->
                        <div class="col-sm-6">
                            <label class="form-label">Budget min (TND)</label>
                            <div class="input-group">
                                <input type="number" name="budget_min" class="form-control" min="0" step="1000"
                                       value="<?= old('budget_min', $lead['budget_min'] ?? '') ?>">
                                <span class="input-group-text">TND</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Budget max (TND)</label>
                            <div class="input-group">
                                <input type="number" name="budget_max" class="form-control" min="0" step="1000"
                                       value="<?= old('budget_max', $lead['budget_max'] ?? '') ?>">
                                <span class="input-group-text">TND</span>
                            </div>
                        </div>

                        <!-- Surface -->
                        <div class="col-sm-6">
                            <label class="form-label">Surface min (m²)</label>
                            <div class="input-group">
                                <input type="number" name="surface_min" class="form-control" min="0"
                                       value="<?= old('surface_min', $lead['surface_min'] ?? $lead['desired_surface'] ?? '') ?>">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Surface max (m²)</label>
                            <div class="input-group">
                                <input type="number" name="surface_max" class="form-control" min="0"
                                       value="<?= old('surface_max', $lead['surface_max'] ?? '') ?>">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Caractéristiques souhaitées -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <strong><i class="bi bi-sliders me-2 text-primary"></i>Caractéristiques souhaitées</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-sm-6 col-md-4">
                            <label class="form-label">Pièces min</label>
                            <select name="rooms_min" class="form-select">
                                <option value="">Indifférent</option>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>" <?= old('rooms_min', $lead['rooms_min'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?>+</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <label class="form-label">Chambres min</label>
                            <select name="bedrooms_min" class="form-select">
                                <option value="">Indifférent</option>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?= $i ?>" <?= old('bedrooms_min', $lead['bedrooms_min'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?>+</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <label class="form-label">Salle de bains min</label>
                            <select name="bathrooms_min" class="form-select">
                                <option value="">Indifférent</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?= $i ?>" <?= old('bathrooms_min', $lead['bathrooms_min'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?>+</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Étage min</label>
                            <select name="floor_min" class="form-select">
                                <option value="">Indifférent</option>
                                <option value="0" <?= old('floor_min', $lead['floor_min'] ?? '') === '0' ? 'selected' : '' ?>>RDC</option>
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                    <option value="<?= $i ?>" <?= old('floor_min', $lead['floor_min'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?>e étage</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Étage max</label>
                            <select name="floor_max" class="form-select">
                                <option value="">Indifférent</option>
                                <option value="0" <?= old('floor_max', $lead['floor_max'] ?? '') === '0' ? 'selected' : '' ?>>RDC</option>
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                    <option value="<?= $i ?>" <?= old('floor_max', $lead['floor_max'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?>e étage</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">État du bien</label>
                            <select name="construction_state" class="form-select">
                                <option value="any"         <?= old('construction_state', $lead['construction_state'] ?? 'any') === 'any'         ? 'selected' : '' ?>>Indifférent</option>
                                <option value="new"         <?= old('construction_state', $lead['construction_state'] ?? '') === 'new'         ? 'selected' : '' ?>>Neuf</option>
                                <option value="good"        <?= old('construction_state', $lead['construction_state'] ?? '') === 'good'        ? 'selected' : '' ?>>Bon état</option>
                                <option value="to_refresh"  <?= old('construction_state', $lead['construction_state'] ?? '') === 'to_refresh'  ? 'selected' : '' ?>>À rafraîchir</option>
                                <option value="to_renovate" <?= old('construction_state', $lead['construction_state'] ?? '') === 'to_renovate' ? 'selected' : '' ?>>À rénover</option>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Meublé</label>
                            <select name="furnished" class="form-select">
                                <option value="any"         <?= old('furnished', $lead['furnished'] ?? 'any') === 'any'         ? 'selected' : '' ?>>Indifférent</option>
                                <option value="furnished"   <?= old('furnished', $lead['furnished'] ?? '') === 'furnished'   ? 'selected' : '' ?>>Meublé</option>
                                <option value="unfurnished" <?= old('furnished', $lead['furnished'] ?? '') === 'unfurnished' ? 'selected' : '' ?>>Non meublé</option>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Orientation souhaitée</label>
                            <select name="orientation" class="form-select">
                                <option value="">Indifférent</option>
                                <?php foreach (['nord'=>'Nord','sud'=>'Sud','est'=>'Est','ouest'=>'Ouest','nord-est'=>'Nord-Est','nord-ouest'=>'Nord-Ouest','sud-est'=>'Sud-Est','sud-ouest'=>'Sud-Ouest'] as $val=>$lbl): ?>
                                    <option value="<?= $val ?>" <?= old('orientation', $lead['orientation'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Options souhaitées -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Options souhaitées</label>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                <?php foreach ([
                                    'wants_parking'  => ['label' => 'Parking',   'icon' => 'bi-car-front-fill'],
                                    'wants_elevator' => ['label' => 'Ascenseur', 'icon' => 'bi-arrow-up-square'],
                                    'wants_garden'   => ['label' => 'Jardin',    'icon' => 'bi-tree'],
                                    'wants_pool'     => ['label' => 'Piscine',   'icon' => 'bi-droplet-fill'],
                                    'wants_terrace'  => ['label' => 'Terrasse',  'icon' => 'bi-sun'],
                                ] as $field => $opt): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="<?= $field ?>" id="<?= $field ?>" value="1"
                                           <?= old($field, $lead[$field] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="<?= $field ?>">
                                        <i class="bi <?= $opt['icon'] ?> me-1"></i><?= $opt['label'] ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Zones souhaitées -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <strong><i class="bi bi-geo-alt me-2 text-primary"></i>Zones souhaitées</strong>
                    <?php if (!empty($parentZones)): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearZones">
                        <i class="bi bi-x-lg me-1"></i>Effacer
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($parentZones)): ?>
                    <div class="mb-2">
                        <input type="text" id="zoneSearch" class="form-control form-control-sm" placeholder="Rechercher une zone…">
                    </div>
                    <div class="zone-selector" style="max-height:280px;overflow-y:auto;border:1px solid #dee2e6;border-radius:.5rem;padding:.75rem;">
                        <?php foreach ($parentZones as $pid => $pname): ?>
                        <div class="zone-group mb-2">
                            <div class="fw-semibold text-muted small mb-1">
                                <i class="bi bi-building me-1"></i><?= esc($pname) ?>
                            </div>
                            <div class="ps-2">
                                <?php if (!empty($childZones[$pid])): ?>
                                    <?php foreach ($childZones[$pid] as $zone): ?>
                                    <div class="zone-item">
                                        <input type="checkbox" class="form-check-input me-1" name="desired_zone_ids[]"
                                               id="zone_<?= $zone['id'] ?>" value="<?= $zone['id'] ?>"
                                               <?= in_array((int)$zone['id'], $selZones) ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="zone_<?= $zone['id'] ?>">
                                            <?= esc($zone['name']) ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="zone-item">
                                        <input type="checkbox" class="form-check-input me-1" name="desired_zone_ids[]"
                                               id="zone_p<?= $pid ?>" value="<?= $pid ?>"
                                               <?= in_array($pid, $selZones) ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="zone_p<?= $pid ?>">(Toute la ville)</label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="desired_location" class="form-control form-control-sm"
                               placeholder="Précision libre (ex: proche école, zone calme…)"
                               value="<?= esc(old('desired_location', $lead['desired_location'] ?? '')) ?>">
                    </div>
                    <?php else: ?>
                    <input type="text" name="desired_location" class="form-control"
                           placeholder="Ex: Tunis, La Marsa, Sousse…"
                           value="<?= esc(old('desired_location', $lead['desired_location'] ?? '')) ?>">
                    <small class="text-muted">Les zones n'ont pas encore été configurées dans le système.</small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Propriété liée -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong><i class="bi bi-link-45deg me-2 text-primary"></i>Propriété liée</strong></div>
                <div class="card-body">
                    <select name="property_id" class="form-select">
                        <option value="">— Aucune propriété liée —</option>
                        <?php foreach ($properties as $prop): ?>
                            <option value="<?= $prop['id'] ?>" <?= old('property_id', $lead['property_id'] ?? '') == $prop['id'] ? 'selected' : '' ?>>
                                [<?= esc($prop['reference']) ?>] <?= esc($prop['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent"><strong><i class="bi bi-chat-text me-2 text-primary"></i>Notes</strong></div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="4" placeholder="Remarques, commentaires, besoins spécifiques…"><?= esc(old('notes', $lead['notes'] ?? '')) ?></textarea>
                </div>
            </div>

        </div><!-- /col-lg-8 -->

        <!-- ============ SIDEBAR ============ -->
        <div class="col-lg-4">

            <!-- Pipeline -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong><i class="bi bi-funnel me-2 text-primary"></i>Pipeline</strong></div>
                <div class="card-body">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select mb-3">
                        <?php foreach ([
                            'new'         => '🔵 Nouveau',
                            'contacted'   => '🟡 Contacté',
                            'interested'  => '🟠 Intéressé',
                            'visit_done'  => '🟣 Visite faite',
                            'negotiating' => '🔴 En négociation',
                            'won'         => '🟢 Conclu',
                            'lost'        => '⚫ Perdu',
                        ] as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= old('status', $lead['status'] ?? 'new') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label">Source</label>
                    <select name="source" class="form-select">
                        <option value="">— Source —</option>
                        <?php foreach ([
                            'website'  => 'Site web',
                            'referral' => 'Recommandation',
                            'phone'    => 'Téléphone',
                            'email'    => 'Email',
                            'walk_in'  => 'Passage en agence',
                            'social'   => 'Réseaux sociaux',
                            'portals'  => 'Portails immo',
                            'other'    => 'Autre',
                        ] as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= old('source', $lead['source'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Assignation & suivi -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong><i class="bi bi-person-check me-2 text-primary"></i>Assignation & suivi</strong></div>
                <div class="card-body">
                    <label class="form-label">Assigné à</label>
                    <select name="assigned_to" class="form-select mb-3">
                        <option value="">— Non assigné —</option>
                        <?php foreach ($agents as $agent): ?>
                            <option value="<?= $agent['id'] ?>" <?= old('assigned_to', $lead['assigned_to'] ?? '') == $agent['id'] ? 'selected' : '' ?>>
                                <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label">Prochain suivi</label>
                    <input type="date" name="next_follow_up" class="form-control"
                           value="<?= esc(old('next_follow_up', $lead['next_follow_up'] ?? '')) ?>">
                </div>
            </div>

            <!-- Priorité -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong><i class="bi bi-flag me-2 text-primary"></i>Priorité</strong></div>
                <div class="card-body">
                    <div class="btn-group w-100" role="group">
                        <?php foreach (['low' => 'Faible', 'medium' => 'Normale', 'high' => 'Haute'] as $val => $lbl): ?>
                            <input type="radio" class="btn-check" name="priority" id="priority_<?= $val ?>" value="<?= $val ?>"
                                   <?= old('priority', $lead['priority'] ?? 'medium') === $val ? 'checked' : '' ?>>
                            <label class="btn btn-outline-secondary" for="priority_<?= $val ?>"><?= $lbl ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Récap types sélectionnés -->
            <div class="card border-0 shadow-sm mb-3" id="typeSummaryCard" style="display:none">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1"><i class="bi bi-check2-circle me-1"></i>Types sélectionnés :</div>
                    <div id="typeSummary" class="d-flex flex-wrap gap-1"></div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-1"></i>
                    <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le lead' ?>
                </button>
                <?php if ($isEdit): ?>
                <a href="<?= site_url('admin/leads/' . $lead['id']) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-eye me-1"></i> Voir le lead
                </a>
                <?php endif; ?>
            </div>

        </div><!-- /col-lg-4 -->
    </div>
</form>

<script>
// Zone search filter
document.getElementById('zoneSearch')?.addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('.zone-item').forEach(function (item) {
        var text = item.querySelector('label')?.textContent?.toLowerCase() ?? '';
        item.style.display = text.includes(q) ? '' : 'none';
    });
    document.querySelectorAll('.zone-group').forEach(function (group) {
        var visible = [...group.querySelectorAll('.zone-item')].some(function (i) { return i.style.display !== 'none'; });
        group.style.display = visible ? '' : 'none';
    });
});

// Clear zones
document.getElementById('clearZones')?.addEventListener('click', function () {
    document.querySelectorAll('input[name="desired_zone_ids[]"]').forEach(function (cb) { cb.checked = false; });
});

// Types summary badge in sidebar
(function () {
    var checkboxes = document.querySelectorAll('input[name="property_types[]"]');
    var card = document.getElementById('typeSummaryCard');
    var summary = document.getElementById('typeSummary');
    if (!checkboxes.length || !card || !summary) return;

    function updateSummary() {
        var selected = [...checkboxes].filter(function (c) { return c.checked; });
        summary.innerHTML = selected.map(function (c) {
            var lbl = document.querySelector('label[for="' + c.id + '"]')?.textContent?.trim() ?? c.value;
            return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">' + lbl + '</span>';
        }).join('');
        card.style.display = selected.length ? 'block' : 'none';
    }
    checkboxes.forEach(function (c) { c.addEventListener('change', updateSummary); });
    updateSummary();
}());
</script>