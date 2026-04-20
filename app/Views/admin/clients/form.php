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

            <!-- ── Section 4 : Besoin immobilier ──────────────────── -->
            <div class="card shadow-sm" id="needCard">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-house-heart me-2 text-warning"></i>Besoin immobilier
                </div>
                <div class="card-body row g-3">

                    <!-- Type de bien (commun) -->
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label">Type de bien</label>
                        <select name="property_type_id" class="form-select">
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($propertyTypes as $pt): ?>
                            <option value="<?= $pt['id'] ?>"
                                <?= old('property_type_id', $client['property_type_id'] ?? '') == $pt['id'] ? 'selected' : '' ?>>
                                <?= esc($pt['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Acheteur / Locataire / Investisseur -->
                    <div id="needBuyer" class="col-12 row g-3">
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
                        <div class="col-12 col-lg-4">
                            <label class="form-label">Zone recherchée</label>
                            <input type="text" name="desired_zone" class="form-control"
                                   value="<?= esc(old('desired_zone', $client['desired_zone'] ?? '')) ?>"
                                   placeholder="Ex : Centre-ville Tunis">
                        </div>
                    </div>

                    <!-- Propriétaire -->
                    <div id="needOwner" class="col-12 row g-3" style="display:none;">
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
            </div>

            <!-- ── Section 6 : Notes ───────────────────────────────── -->
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

            <!-- ── Section 5 : CRM ────────────────────────────────── -->
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
</form>

<!-- ── JS : type sélecteur + besoin dynamique + cascade zones ─────── -->
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

    // Init
    applyTypeSelection('<?= esc($currentType, 'js') ?>');

    typeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            const radio = card.querySelector('.type-radio');
            radio.checked = true;
            applyTypeSelection(radio.value);
        });
    });

    // ── Cascade zones (AJAX) ─────────────────────────────────────────
    const selPays   = document.getElementById('selPays');
    const selRegion = document.getElementById('selRegion');
    const selVille  = document.getElementById('selVille');

    const CSRF_NAME = '<?= csrf_token() ?>';
    const CSRF_HASH = '<?= csrf_hash() ?>';

    // Valeurs pré-sélectionnées (édition)
    const preRegion = '<?= (int) ($client['zone_region_id'] ?? 0) ?>';
    const preVille  = '<?= (int) ($client['zone_ville_id'] ?? 0) ?>';

    function loadSelect(sel, url, preselect) {
        sel.innerHTML = '<option value="">Chargement…</option>';
        sel.disabled  = true;

        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
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

    // Pré-charger si édition
    <?php if (! empty($client['zone_pays_id'])): ?>
    loadSelect(selRegion, '<?= base_url('admin/clients/regions/') ?>' + selPays.value, preRegion);
    <?php endif; ?>
    <?php if (! empty($client['zone_region_id'])): ?>
    // Attendre que selRegion soit chargé avant de charger les villes
    selRegion.addEventListener('change', function onFirstChange() {
        if (selRegion.value === preRegion) {
            loadSelect(selVille, '<?= base_url('admin/clients/villes/') ?>' + preRegion, preVille);
            selRegion.removeEventListener('change', onFirstChange);
        }
    });
    <?php endif; ?>

})();
</script>
