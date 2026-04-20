<?php
$isEdit     = ! empty($client['id']);
$errors     = session()->getFlashdata('errors') ?? [];
$old        = fn(string $k, $def = '') => old($k, $client[$k] ?? $def);

$formAction = $isEdit
    ? base_url('admin/clients/' . $client['id'] . '/update')
    : base_url('admin/clients/store');

// Type courant (après redirect withInput ou en édition)
$currentType = old('client_type', $client['client_type'] ?? 'acheteur');
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= base_url('admin/clients') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-bold">
        <i class="bi bi-person-plus me-2 text-primary"></i><?= esc($page_title) ?>
    </h4>
</div>

<?php if (! empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="<?= $formAction ?>" id="clientForm">
    <?= csrf_field() ?>

    <div class="row g-4">

        <!-- ── Colonne principale ──────────────────────────────────── -->
        <div class="col-lg-8 d-flex flex-column gap-4">

            <!-- ── Section 1 : Infos de base ──────────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-person me-2 text-primary"></i>Informations de base
                </div>
                <div class="card-body row g-3">

                    <!-- Type client -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Type de client <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2" id="typeSelector">
                            <?php foreach ($typeLabels as $key => $meta): ?>
                            <label class="type-card border rounded p-2 px-3 d-flex align-items-center gap-2 cursor-pointer
                                <?= $currentType === $key ? 'border-' . $meta['color'] . ' bg-' . $meta['color'] . '-subtle' : '' ?>"
                                style="cursor:pointer; min-width:120px;"
                                for="type_<?= $key ?>">
                                <input class="d-none type-radio" type="radio"
                                       name="client_type" id="type_<?= $key ?>"
                                       value="<?= $key ?>"
                                       <?= $currentType === $key ? 'checked' : '' ?>>
                                <i class="bi <?= $meta['icon'] ?> text-<?= $meta['color'] ?> fs-5"></i>
                                <span class="fw-semibold small"><?= $meta['label'] ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Nom / Prénom -->
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                               value="<?= esc($old('last_name')) ?>" required>
                        <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= esc($errors['last_name']) ?></div><?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                               value="<?= esc($old('first_name')) ?>" required>
                        <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= esc($errors['first_name']) ?></div><?php endif; ?>
                    </div>

                    <!-- Téléphone / Email -->
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                               value="<?= esc($old('phone')) ?>" required>
                        <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= esc($errors['phone']) ?></div><?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= esc($old('email')) ?>">
                    </div>

                </div>
            </div>

            <!-- ── Section 2 : Infos pro ───────────────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-briefcase me-2 text-secondary"></i>Informations professionnelles <span class="text-muted small fw-normal">(optionnel)</span></span>
                </div>
                <div class="card-body row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Profession</label>
                        <input type="text" name="profession" class="form-control"
                               value="<?= esc($old('profession')) ?>" placeholder="Ex : Médecin, Ingénieur…">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Entreprise</label>
                        <input type="text" name="company" class="form-control"
                               value="<?= esc($old('company')) ?>" placeholder="Nom de la société">
                    </div>
                </div>
            </div>

            <!-- ── Section 3 : Adresse ─────────────────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-geo-alt me-2 text-info"></i>Adresse
                </div>
                <div class="card-body row g-3">

                    <div class="col-12">
                        <label class="form-label">N° et Rue</label>
                        <input type="text" name="address" class="form-control"
                               value="<?= esc($old('address')) ?>" placeholder="Ex : 12, Rue de la République">
                    </div>

                    <!-- Pays -->
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Pays</label>
                        <select name="zone_pays_id" id="selPays" class="form-select">
                            <option value="">— Pays —</option>
                            <?php foreach ($pays_list as $p): ?>
                            <option value="<?= $p['id'] ?>"
                                <?= old('zone_pays_id', $client['zone_pays_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Région (chargée via AJAX) -->
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Région</label>
                        <select name="zone_region_id" id="selRegion" class="form-select" disabled>
                            <option value="">— Région —</option>
                        </select>
                    </div>

                    <!-- Ville (chargée via AJAX) -->
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Ville</label>
                        <select name="zone_ville_id" id="selVille" class="form-select" disabled>
                            <option value="">— Ville —</option>
                        </select>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Code postal</label>
                        <input type="text" name="postal_code" class="form-control"
                               value="<?= esc($old('postal_code')) ?>" placeholder="Ex : 1000">
                    </div>

                </div>
            </div>

            <!-- ── Section 4 : Profil de demande ──────────────────── -->
            <div class="card shadow-sm" id="needCard">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-currency-dollar me-2 text-warning"></i>Profil de demande
                </div>

                <!-- Acheteur / Locataire / Investisseur -->
                <div id="needBuyer" class="card-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Type de transaction</label>
                        <div class="d-flex gap-3">
                            <?php foreach ($demandTypeLabels as $dKey => $dMeta): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="demand_type"
                                       id="demand_<?= $dKey ?>" value="<?= $dKey ?>"
                                       <?= old('demand_type', $client['demand_type'] ?? '') === $dKey ? 'checked' : '' ?>>
                                <label class="form-check-label" for="demand_<?= $dKey ?>">
                                    <i class="bi <?= $dMeta['icon'] ?> text-<?= $dMeta['color'] ?> me-1"></i><?= $dMeta['label'] ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Budget min (TND)</label>
                        <input type="number" name="budget_min" class="form-control" min="0" step="100"
                               value="<?= esc(old('budget_min', $client['budget_min'] ?? '')) ?>">
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Budget max (TND)</label>
                        <input type="number" name="budget_max" class="form-control" min="0" step="100"
                               value="<?= esc(old('budget_max', $client['budget_max'] ?? '')) ?>">
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Urgence</label>
                        <select name="urgency" class="form-select">
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($urgencyLabels as $uKey => $uMeta): ?>
                            <option value="<?= $uKey ?>"
                                <?= old('urgency', $client['urgency'] ?? '') === $uKey ? 'selected' : '' ?>>
                                <?= $uMeta['label'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Flexibilité budget</label>
                        <select name="budget_flexibility" class="form-select">
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($budgetFlexLabels as $bKey => $bMeta): ?>
                            <option value="<?= $bKey ?>"
                                <?= old('budget_flexibility', $client['budget_flexibility'] ?? '') === $bKey ? 'selected' : '' ?>>
                                <?= $bMeta['label'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Propriétaire -->
                <div id="needOwner" class="card-body row g-3" style="display:none;">
                    <div class="col-sm-6">
                        <label class="form-label">Localisation du bien</label>
                        <input type="text" name="owner_location" class="form-control"
                               value="<?= esc(old('owner_location', $client['owner_location'] ?? '')) ?>"
                               placeholder="Ex : Lac 2, Tunis">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Prix souhaité (TND)</label>
                        <input type="number" name="desired_price" class="form-control" min="0" step="1000"
                               value="<?= esc(old('desired_price', $client['desired_price'] ?? '')) ?>">
                    </div>
                </div>
            </div>

            <!-- ── Section 5 : Types de biens ──────────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-building me-2 text-primary"></i>Types de biens recherchés
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach ($propertyTypes as $pt): ?>
                        <div class="col-6 col-sm-4 col-lg-3">
                            <label class="d-flex align-items-center gap-2 p-2 border rounded cursor-pointer
                                <?= in_array($pt['id'], $selectedPropTypes) ? 'border-primary bg-primary-subtle' : '' ?>
                                prop-type-card" style="cursor:pointer;">
                                <input type="checkbox" name="search_prop_types[]"
                                       class="form-check-input prop-type-check"
                                       value="<?= $pt['id'] ?>"
                                       <?= in_array($pt['id'], $selectedPropTypes) ? 'checked' : '' ?>>
                                <span class="small fw-semibold"><?= esc($pt['name']) ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($propertyTypes)): ?>
                    <p class="text-muted small mb-0">Aucun type de bien disponible.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Section 6 : Zones recherchées ───────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-geo-alt-fill me-2 text-info"></i>Zones de recherche
                </div>
                <div class="card-body">
                    <!-- Champ de recherche -->
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="zoneSearchInput" class="form-control"
                               placeholder="Tapez un nom de ville, gouvernorat…" autocomplete="off">
                    </div>
                    <!-- Suggestions -->
                    <div id="zoneSearchResults" class="list-group mb-3" style="display:none; max-height:200px; overflow-y:auto;"></div>

                    <!-- Zones sélectionnées -->
                    <div id="selectedZonesTags" class="d-flex flex-wrap gap-2">
                        <?php foreach ($selectedZones as $sz): ?>
                        <span class="badge text-bg-info d-inline-flex align-items-center gap-1 zone-tag"
                              data-zone-id="<?= $sz['zone_id'] ?>">
                            <i class="bi bi-geo-alt"></i>
                            <?= esc($sz['name']) ?>
                            <button type="button" class="btn-close btn-close-white btn-sm zone-remove"
                                    style="font-size:.6rem;" aria-label="Retirer"></button>
                            <input type="hidden" name="search_zones[]" value="<?= $sz['zone_id'] ?>">
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <p id="noZonesHint" class="text-muted small mb-0 <?= ! empty($selectedZones) ? 'd-none' : '' ?>">
                        Aucune zone sélectionnée.
                    </p>

                    <!-- Carte Leaflet -->
                    <div id="zoneMapWrapper" class="mt-3 <?= empty($selectedZones) ? 'd-none' : '' ?>">
                        <div id="zoneMap" style="height:260px; border-radius:.5rem; border:1px solid #dee2e6;"></div>
                        <p class="text-muted small mt-1 mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Visualisation approximative — centrage sur les zones sélectionnées.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ── Section 7 : Orientations ────────────────────────── -->
            <?php
            $rawOrientations = old('orientations', json_decode($client['orientations'] ?? '[]', true) ?? []);
            ?>
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-compass me-2 text-secondary"></i>Orientations préférées
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($orientationLabels as $oKey => $oLabel): ?>
                        <label class="border rounded px-3 py-2 d-flex align-items-center gap-2 orientation-card
                            <?= in_array($oKey, (array) $rawOrientations) ? 'border-secondary bg-secondary-subtle' : '' ?>"
                            style="cursor:pointer;">
                            <input type="checkbox" class="form-check-input orientation-check"
                                   name="orientations[]" value="<?= $oKey ?>"
                                   <?= in_array($oKey, (array) $rawOrientations) ? 'checked' : '' ?>>
                            <span class="small fw-semibold"><?= esc($oLabel) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ── Section 8 : Caractéristiques ───────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-star me-2 text-warning"></i>Caractéristiques souhaitées
                </div>
                <div class="card-body d-flex flex-column gap-4">
                    <?php foreach ($featuresCatalog as $catKey => $catData): ?>
                    <div>
                        <h6 class="fw-semibold mb-2">
                            <i class="bi <?= $catData['icon'] ?> me-1"></i><?= esc($catData['label']) ?>
                        </h6>
                        <div class="row g-2">
                            <?php foreach ($catData['items'] as $fKey => $fLabel): ?>
                            <?php
                            $isReq = isset($selectedFeatures[$fKey]) && $selectedFeatures[$fKey] === 'obligatoire';
                            $isOpt = isset($selectedFeatures[$fKey]) && $selectedFeatures[$fKey] === 'optionnel';
                            $isChecked = $isReq || $isOpt;
                            ?>
                            <div class="col-12 col-sm-6 col-lg-4 feature-item" id="feat_<?= $fKey ?>">
                                <label class="border rounded p-2 d-flex align-items-start gap-2 feature-card
                                    <?= $isChecked ? 'border-warning bg-warning-subtle' : '' ?>"
                                    style="cursor:pointer;">
                                    <input type="checkbox" class="form-check-input feature-checkbox mt-1"
                                           data-key="<?= $fKey ?>" <?= $isChecked ? 'checked' : '' ?>>
                                    <div class="flex-grow-1">
                                        <div class="small fw-semibold"><?= esc($fLabel) ?></div>
                                        <div class="feature-type-selector mt-1 <?= $isChecked ? '' : 'd-none' ?>">
                                            <div class="d-flex gap-2">
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input" type="radio"
                                                           name="feat_type[<?= $fKey ?>]"
                                                           id="feat_req_<?= $fKey ?>"
                                                           value="obligatoire" <?= $isReq ? 'checked' : '' ?>>
                                                    <label class="form-check-label small text-danger fw-semibold"
                                                           for="feat_req_<?= $fKey ?>">Obligatoire</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input" type="radio"
                                                           name="feat_type[<?= $fKey ?>]"
                                                           id="feat_opt_<?= $fKey ?>"
                                                           value="optionnel" <?= (! $isReq) ? 'checked' : '' ?>>
                                                    <label class="form-check-label small text-secondary"
                                                           for="feat_opt_<?= $fKey ?>">Optionnel</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ── Section 9 : Critères techniques ─────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-rulers me-2 text-primary"></i>Critères techniques
                </div>
                <div class="card-body row g-3">
                    <div class="col-sm-6 col-lg-3">
                        <label class="form-label">Surface min (m²)</label>
                        <input type="number" name="surface_min" class="form-control" min="0" step="5"
                               value="<?= esc(old('surface_min', $client['surface_min'] ?? '')) ?>">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <label class="form-label">Surface max (m²)</label>
                        <input type="number" name="surface_max" class="form-control" min="0" step="5"
                               value="<?= esc(old('surface_max', $client['surface_max'] ?? '')) ?>">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <label class="form-label">Pièces (min)</label>
                        <input type="number" name="rooms_min" class="form-control" min="1" max="20"
                               value="<?= esc(old('rooms_min', $client['rooms_min'] ?? '')) ?>">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <label class="form-label">Chambres (min)</label>
                        <input type="number" name="bedrooms_min" class="form-control" min="0" max="20"
                               value="<?= esc(old('bedrooms_min', $client['bedrooms_min'] ?? '')) ?>">
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Étage préféré</label>
                        <select name="floor_preferred" class="form-select">
                            <option value="">— Indifférent —</option>
                            <?php foreach ([
                                'rdc'     => 'Rez-de-chaussée',
                                'bas'     => 'Étages bas (1-3)',
                                'moyen'   => 'Étages moyens (4-6)',
                                'haut'    => 'Étages hauts (7+)',
                                'dernier' => 'Dernier étage',
                            ] as $fv => $fl): ?>
                            <option value="<?= $fv ?>"
                                <?= old('floor_preferred', $client['floor_preferred'] ?? '') === $fv ? 'selected' : '' ?>>
                                <?= $fl ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-4 d-flex align-items-end">
                        <div class="form-check form-switch ms-1 mb-1">
                            <input class="form-check-input" type="checkbox" name="has_elevator"
                                   id="hasElevator" role="switch"
                                   <?= old('has_elevator', $client['has_elevator'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="hasElevator">Ascenseur requis</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Section 10 : Notes ──────────────────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-chat-text me-2 text-muted"></i>Notes
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="4"
                              placeholder="Remarques, informations complémentaires…"><?= esc(old('notes', $client['notes'] ?? '')) ?></textarea>
                </div>
            </div>

        </div>

        <!-- ── Colonne latérale ────────────────────────────────────── -->
        <div class="col-lg-4 d-flex flex-column gap-4">

            <!-- ── Section CRM ────────────────────────────────────── -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-diagram-3 me-2 text-success"></i>CRM
                </div>
                <div class="card-body d-flex flex-column gap-3">

                    <!-- Statut -->
                    <div>
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="status" class="form-select">
                            <?php foreach ($statusLabels as $k => $m): ?>
                            <option value="<?= $k ?>"
                                <?= old('status', $client['status'] ?? 'nouveau') === $k ? 'selected' : '' ?>>
                                <?= $m['label'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Assigné à -->
                    <div>
                        <label class="form-label fw-semibold">Assigné à</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">— Non assigné —</option>
                            <?php foreach ($agents as $a): ?>
                            <option value="<?= $a['id'] ?>"
                                <?= old('assigned_to', $client['assigned_to'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                                <?= esc($a['first_name'] . ' ' . $a['last_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Source -->
                    <div>
                        <label class="form-label fw-semibold">Source</label>
                        <select name="source" class="form-select">
                            <?php foreach ($sourceLabels as $k => $lbl): ?>
                            <option value="<?= $k ?>"
                                <?= old('source', $client['source'] ?? 'site_web') === $k ? 'selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i>
                        <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le client' ?>
                    </button>
                    <a href="<?= base_url('admin/clients' . ($isEdit ? '/' . $client['id'] : '')) ?>"
                       class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Champs cachés features (construits par JS avant submit) -->
    <div id="featuresHiddenContainer"></div>

</form>

<!-- ── JS ──────────────────────────────────────────────────────────── -->
<script>
(function () {
    'use strict';

    // ── Type de client → cartes visuelles ───────────────────────────
    const typeCards = document.querySelectorAll('.type-card');
    const typeColors = {
        acheteur:     'primary',
        locataire:    'info',
        proprietaire: 'success',
        investisseur: 'warning',
    };

    function applyTypeSelection(value) {
        typeCards.forEach(function (card) {
            const radio = card.querySelector('.type-radio');
            const color = typeColors[radio.value] || 'secondary';
            card.classList.remove(
                'border-primary','bg-primary-subtle',
                'border-info','bg-info-subtle',
                'border-success','bg-success-subtle',
                'border-warning','bg-warning-subtle'
            );
            if (radio.value === value) {
                card.classList.add('border-' + color, 'bg-' + color + '-subtle');
            }
        });

        const needBuyer = document.getElementById('needBuyer');
        const needOwner = document.getElementById('needOwner');
        if (value === 'proprietaire') {
            needBuyer.style.display = 'none';
            needOwner.style.display = '';
        } else {
            needBuyer.style.display = '';
            needOwner.style.display = 'none';
        }
    }

    applyTypeSelection('<?= esc($currentType, 'js') ?>');

    typeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            const radio = card.querySelector('.type-radio');
            radio.checked = true;
            applyTypeSelection(radio.value);
        });
    });

    // ── Types de biens visuels ───────────────────────────────────────
    document.querySelectorAll('.prop-type-card').forEach(function (card) {
        card.addEventListener('click', function () {
            const cb = card.querySelector('.prop-type-check');
            // Le click toggle la checkbox nativement, on update juste le style
            setTimeout(function () {
                if (cb.checked) {
                    card.classList.add('border-primary', 'bg-primary-subtle');
                } else {
                    card.classList.remove('border-primary', 'bg-primary-subtle');
                }
            }, 0);
        });
    });

    // ── Orientations visuelles ───────────────────────────────────────
    document.querySelectorAll('.orientation-card').forEach(function (card) {
        card.addEventListener('click', function () {
            const cb = card.querySelector('.orientation-check');
            setTimeout(function () {
                if (cb.checked) {
                    card.classList.add('border-secondary', 'bg-secondary-subtle');
                } else {
                    card.classList.remove('border-secondary', 'bg-secondary-subtle');
                }
            }, 0);
        });
    });

    // ── Caractéristiques : toggle radio ─────────────────────────────
    document.querySelectorAll('.feature-checkbox').forEach(function (cb) {
        function updateFeatureCard() {
            const card     = cb.closest('.feature-card');
            const selector = cb.closest('.feature-item').querySelector('.feature-type-selector');
            if (cb.checked) {
                card.classList.add('border-warning', 'bg-warning-subtle');
                selector.classList.remove('d-none');
                // Set "optionnel" by default if neither is checked
                const radios = selector.querySelectorAll('input[type=radio]');
                const hasChecked = Array.from(radios).some(r => r.checked);
                if (!hasChecked) {
                    const optRadio = selector.querySelector('input[value=optionnel]');
                    if (optRadio) optRadio.checked = true;
                }
            } else {
                card.classList.remove('border-warning', 'bg-warning-subtle');
                selector.classList.add('d-none');
            }
        }
        updateFeatureCard();
        cb.addEventListener('change', updateFeatureCard);
    });

    // Avant submit : transformer feat_type[] en features_obligatoire[] et features_optionnel[]
    document.getElementById('clientForm').addEventListener('submit', function () {
        const container = document.getElementById('featuresHiddenContainer');
        container.innerHTML = '';

        document.querySelectorAll('.feature-checkbox:checked').forEach(function (cb) {
            const key      = cb.dataset.key;
            const radios   = document.querySelectorAll('input[name="feat_type[' + key + ']"]:checked');
            const typeVal  = radios.length > 0 ? radios[0].value : 'optionnel';
            const fieldName = typeVal === 'obligatoire' ? 'features_obligatoire[]' : 'features_optionnel[]';
            const input     = document.createElement('input');
            input.type  = 'hidden';
            input.name  = fieldName;
            input.value = key;
            container.appendChild(input);
        });
    });

    // ── Zones recherchées (AJAX autocomplete) ───────────────────────
    const zoneInput   = document.getElementById('zoneSearchInput');
    const zoneResults = document.getElementById('zoneSearchResults');
    const zoneTags    = document.getElementById('selectedZonesTags');
    const noZonesHint = document.getElementById('noZonesHint');

    // Ensemble des IDs déjà sélectionnés
    const selectedZoneIds = new Set(
        Array.from(zoneTags.querySelectorAll('.zone-tag')).map(t => String(t.dataset.zoneId))
    );

    function updateNoHint() {
        if (zoneTags.querySelectorAll('.zone-tag').length > 0) {
            noZonesHint.classList.add('d-none');
        } else {
            noZonesHint.classList.remove('d-none');
        }
    }

    function addZoneTag(zone) {
        if (selectedZoneIds.has(String(zone.id))) return;
        selectedZoneIds.add(String(zone.id));

        const span = document.createElement('span');
        span.className = 'badge text-bg-info d-inline-flex align-items-center gap-1 zone-tag';
        span.dataset.zoneId = zone.id;
        span.innerHTML =
            '<i class="bi bi-geo-alt"></i>' +
            document.createTextNode(zone.name).textContent +
            ' <small class="opacity-75">(' + zone.type_label + ')</small>' +
            '<button type="button" class="btn-close btn-close-white btn-sm zone-remove" style="font-size:.6rem;" aria-label="Retirer"></button>' +
            '<input type="hidden" name="search_zones[]" value="' + zone.id + '">';

        // Réécrire proprement pour éviter XSS
        span.innerHTML = '';
        const icon = document.createElement('i');
        icon.className = 'bi bi-geo-alt';
        span.appendChild(icon);

        const nameText = document.createElement('span');
        nameText.textContent = zone.name;
        span.appendChild(nameText);

        const typeBadge = document.createElement('small');
        typeBadge.className = 'opacity-75';
        typeBadge.textContent = '(' + zone.type_label + ')';
        span.appendChild(typeBadge);

        const rmBtn = document.createElement('button');
        rmBtn.type = 'button';
        rmBtn.className = 'btn-close btn-close-white btn-sm zone-remove';
        rmBtn.style.fontSize = '.6rem';
        rmBtn.setAttribute('aria-label', 'Retirer');
        span.appendChild(rmBtn);

        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'search_zones[]';
        hidden.value = zone.id;
        span.appendChild(hidden);

        zoneTags.appendChild(span);
        updateNoHint();
    }

    // Suppression d'un badge
    zoneTags.addEventListener('click', function (e) {
        const rmBtn = e.target.closest('.zone-remove');
        if (!rmBtn) return;
        const tag = rmBtn.closest('.zone-tag');
        selectedZoneIds.delete(String(tag.dataset.zoneId));
        tag.remove();
        updateNoHint();
    });

    // Debounce helper
    let debounceTimer;
    zoneInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            zoneResults.innerHTML = '';
            zoneResults.style.display = 'none';
            return;
        }
        debounceTimer = setTimeout(function () {
            fetch('<?= base_url('admin/clients/zones-search') ?>?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(r => r.json())
            .then(function (data) {
                zoneResults.innerHTML = '';
                if (data.length === 0) {
                    zoneResults.style.display = 'none';
                    return;
                }
                data.forEach(function (z) {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action py-2';
                    if (selectedZoneIds.has(String(z.id))) {
                        item.classList.add('disabled');
                    }

                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'fw-semibold small';
                    nameSpan.textContent = z.name;

                    const typeBadge = document.createElement('span');
                    typeBadge.className = 'badge text-bg-secondary ms-2 small';
                    typeBadge.textContent = z.type_label;

                    item.appendChild(nameSpan);
                    item.appendChild(typeBadge);

                    item.addEventListener('click', function () {
                        addZoneTag(z);
                        zoneInput.value = '';
                        zoneResults.innerHTML = '';
                        zoneResults.style.display = 'none';
                    });
                    zoneResults.appendChild(item);
                });
                zoneResults.style.display = '';
            })
            .catch(function () {
                zoneResults.style.display = 'none';
            });
        }, 300);
    });

    // Fermer les suggestions au clic en dehors
    document.addEventListener('click', function (e) {
        if (!zoneResults.contains(e.target) && e.target !== zoneInput) {
            zoneResults.style.display = 'none';
        }
    });

    // ── Cascade zones adresse (AJAX) ─────────────────────────────────
    const selPays   = document.getElementById('selPays');
    const selRegion = document.getElementById('selRegion');
    const selVille  = document.getElementById('selVille');

    const preRegion = '<?= (int) ($client['zone_region_id'] ?? 0) ?>';
    const preVille  = '<?= (int) ($client['zone_ville_id'] ?? 0) ?>';

    function loadSelect(sel, url, preselect) {
        sel.innerHTML = '<option value="">Chargement…</option>';
        sel.disabled  = true;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(function (data) {
            sel.innerHTML = '<option value="">— Sélectionner —</option>';
            data.forEach(function (z) {
                const opt = document.createElement('option');
                opt.value       = z.id;
                opt.textContent = z.name;
                if (String(z.id) === String(preselect)) opt.selected = true;
                sel.appendChild(opt);
            });
            sel.disabled = data.length === 0;
            // Si présélectionné → déclencher le chargement du niveau suivant
            if (preselect && sel === selRegion && sel.value === String(preselect)) {
                loadSelect(selVille, '<?= base_url('admin/clients/villes/') ?>' + preselect, preVille);
            }
        })
        .catch(function () {
            sel.innerHTML = '<option value="">Erreur chargement</option>';
        });
    }

    selPays.addEventListener('change', function () {
        selRegion.innerHTML = '<option value="">— Région —</option>';
        selRegion.disabled  = true;
        selVille.innerHTML  = '<option value="">— Ville —</option>';
        selVille.disabled   = true;
        if (! this.value) return;
        loadSelect(selRegion, '<?= base_url('admin/clients/regions/') ?>' + this.value, '');
    });

    selRegion.addEventListener('change', function () {
        selVille.innerHTML = '<option value="">— Ville —</option>';
        selVille.disabled  = true;
        if (! this.value) return;
        loadSelect(selVille, '<?= base_url('admin/clients/villes/') ?>' + this.value, '');
    });

    // Pré-charger depuis la BDD en édition
    <?php if (! empty($client['zone_pays_id'])): ?>
    loadSelect(selRegion, '<?= base_url('admin/clients/regions/') ?>' + selPays.value, preRegion);
    <?php endif; ?>

})();
</script>

<!-- Leaflet CSS + JS (CDN) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLfI=" crossorigin=""></script>

<script>
(function () {
    'use strict';

    // ── Initialisation carte Leaflet ─────────────────────────────────
    let map         = null;
    let markersGroup = null;

    function initMap() {
        if (map) return;
        const el = document.getElementById('zoneMap');
        if (!el) return;

        map = L.map('zoneMap', { zoomControl: true }).setView([33.8869, 9.5375], 6); // Tunisie

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
            maxZoom: 18,
        }).addTo(map);

        markersGroup = L.layerGroup().addTo(map);
    }

    function refreshMap() {
        const tags    = document.querySelectorAll('#selectedZonesTags .zone-tag');
        const wrapper = document.getElementById('zoneMapWrapper');

        if (tags.length === 0) {
            if (wrapper) wrapper.classList.add('d-none');
            return;
        }

        if (wrapper) wrapper.classList.remove('d-none');
        initMap();
        if (!map) return;

        // Forcer Leaflet à recalculer la taille (sinon tuiles grises)
        setTimeout(function () { map.invalidateSize(); }, 50);

        markersGroup.clearLayers();

        const zoneIds = Array.from(tags).map(t => t.dataset.zoneId);
        const url = '<?= base_url('admin/clients/zones-search') ?>?q=';

        // Géocoder chaque zone par nom via Nominatim (open, no-key)
        const names = Array.from(tags).map(t => {
            const spans = t.querySelectorAll('span');
            return spans.length > 0 ? spans[0].textContent.trim() : '';
        }).filter(Boolean);

        const bounds = [];

        Promise.all(names.map(function (name) {
            return fetch(
                'https://nominatim.openstreetmap.org/search?q=' +
                encodeURIComponent(name + ', Tunisie') +
                '&format=json&limit=1',
                { headers: { 'Accept-Language': 'fr' } }
            )
            .then(r => r.json())
            .then(function (res) {
                if (res && res[0]) {
                    const lat = parseFloat(res[0].lat);
                    const lng = parseFloat(res[0].lon);
                    bounds.push([lat, lng]);

                    const marker = L.circleMarker([lat, lng], {
                        radius: 10, color: '#0dcaf0', fillColor: '#0dcaf0', fillOpacity: 0.45, weight: 2,
                    }).addTo(markersGroup);
                    marker.bindTooltip(name, { permanent: false, direction: 'top' });
                }
            })
            .catch(function () {});
        })).then(function () {
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [30, 30], maxZoom: 10 });
            }
        });
    }

    // Écouter les ajouts/suppressions de zones pour rafraîchir la carte
    const observer = new MutationObserver(refreshMap);
    observer.observe(document.getElementById('selectedZonesTags'), { childList: true });

    // Init si des zones pré-sélectionnées existent
    if (document.querySelectorAll('#selectedZonesTags .zone-tag').length > 0) {
        // Attendre que Leaflet JS soit prêt
        window.addEventListener('load', refreshMap);
    }

})();
</script>
