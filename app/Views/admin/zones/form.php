<?php
$isEdit = ! empty($zone['id']);

// Pré-sélection hiérarchique (injectée par le contrôleur en mode édition)
$preselect    = $preselect    ?? ['pays_id' => null, 'region_id' => null, 'ville_id' => null];
$regions_list = $regions_list ?? [];
$villes_list  = $villes_list  ?? [];
$codes_list   = $codes_list   ?? [];

$oldPaysId   = old('pays_id',   $preselect['pays_id']   ?? '');
$oldRegionId = old('region_id', $preselect['region_id'] ?? '');
$oldVilleId  = old('ville_id',  $preselect['ville_id']  ?? '');
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/zones') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
            <?= $isEdit ? 'Modifier l\'adresse' : 'Ajouter une adresse' ?>
        </h4>
        <?php if ($isEdit) : ?>
        <p class="text-muted mb-0"><?= esc($zone['name']) ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-7">

<form method="POST" id="addressForm"
      action="<?= $isEdit
          ? base_url('admin/zones/' . $zone['id'] . '/update')
          : base_url('admin/zones/store') ?>">
    <?= csrf_field() ?>

    <!-- Champs cachés gérés par JS -->
    <input type="hidden" name="type"      id="hiddenType"     value="<?= esc(old('type', $zone['type'] ?? 'pays')) ?>">
    <input type="hidden" name="parent_id" id="hiddenParentId" value="<?= esc(old('parent_id', $zone['parent_id'] ?? '')) ?>">
    <input type="hidden" name="pays_id"   id="hiddenPaysId"   value="<?= esc($oldPaysId) ?>">
    <input type="hidden" name="region_id" id="hiddenRegionId" value="<?= esc($oldRegionId) ?>">
    <input type="hidden" name="ville_id"  id="hiddenVilleId"  value="<?= esc($oldVilleId) ?>">

    <div class="card shadow-sm">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-map me-1"></i> Adresse géographique
        </div>
        <div class="card-body">
            <div class="row g-4">

                <!-- ── PAYS ────────────────────────────────────── -->
                <div class="col-12">
                    <label class="form-label fw-semibold" for="selectPays">
                        <i class="bi bi-globe2 text-primary me-1"></i>
                        Pays <span class="text-danger">*</span>
                    </label>
                    <select id="selectPays" class="form-select form-select-lg" required>
                        <option value="">— Sélectionner un pays —</option>
                        <?php foreach ($pays_list as $p) : ?>
                        <option value="<?= $p['id'] ?>"
                            <?= (string)$oldPaysId === (string)$p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['name']) ?><?= $p['code'] ? ' (' . esc($p['code']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ── RÉGION ───────────────────────────────────── -->
                <div class="col-md-6" id="wrapRegion">
                    <label class="form-label fw-semibold" for="selectRegion">
                        <i class="bi bi-map text-success me-1"></i>
                        Région / État
                        <span class="badge bg-secondary ms-1 small" id="badgeRegion">optionnel</span>
                    </label>
                    <div class="position-relative">
                        <select id="selectRegion" class="form-select" disabled>
                            <option value="">— Sélectionner d'abord un pays —</option>
                            <?php foreach ($regions_list as $r) : ?>
                            <option value="<?= $r['id'] ?>"
                                <?= (string)$oldRegionId === (string)$r['id'] ? 'selected' : '' ?>>
                                <?= esc($r['name']) ?><?= $r['code'] ? ' (' . esc($r['code']) . ')' : '' ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="position-absolute top-50 end-0 translate-middle-y pe-3 d-none" id="spinnerRegion">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                        </div>
                    </div>
                </div>

                <!-- ── VILLE ────────────────────────────────────── -->
                <div class="col-md-6" id="wrapVille">
                    <label class="form-label fw-semibold" for="selectVille">
                        <i class="bi bi-buildings text-info me-1"></i>
                        Ville
                        <span class="badge bg-secondary ms-1 small" id="badgeVille">optionnel</span>
                    </label>
                    <div class="position-relative">
                        <select id="selectVille" class="form-select" disabled>
                            <option value="">— Sélectionner d'abord une région —</option>
                            <?php foreach ($villes_list as $v) : ?>
                            <option value="<?= $v['id'] ?>"
                                <?= (string)$oldVilleId === (string)$v['id'] ? 'selected' : '' ?>>
                                <?= esc($v['name']) ?><?= $v['code'] ? ' (' . esc($v['code']) . ')' : '' ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="position-absolute top-50 end-0 translate-middle-y pe-3 d-none" id="spinnerVille">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                        </div>
                    </div>
                </div>

                <!-- ── NOM + CODE ───────────────────────────────── -->
                <div class="col-md-7">
                    <label class="form-label fw-semibold" id="labelName" for="inputName">
                        Nom <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="inputName" name="name" class="form-control"
                           value="<?= esc(old('name', $zone['name'] ?? '')) ?>"
                           maxlength="150" required
                           placeholder="Ex : Casablanca, Grand Casablanca…">
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold" id="labelCode" for="inputCode">
                        Code <span id="codeRequired"></span>
                    </label>
                    <input type="text" id="inputCode" name="code" class="form-control"
                           value="<?= esc(old('code', $zone['code'] ?? '')) ?>"
                           maxlength="20" placeholder="—">
                    <div class="form-text" id="codeHint"></div>
                </div>

                <!-- ── STATUT ────────────────────────────────────── -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="isActive" name="is_active" value="1"
                            <?= old('is_active', $zone['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isActive">Entrée active</label>
                    </div>
                </div>

            </div>
        </div>

        <!-- Indicateur de niveau inféré -->
        <div class="card-footer bg-light d-flex align-items-center gap-2">
            <small class="text-muted">Niveau détecté :</small>
            <span id="levelBadge" class="badge bg-secondary">Pays</span>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>
            <?= $isEdit ? 'Enregistrer' : 'Ajouter' ?>
        </button>
        <a href="<?= base_url('admin/zones') ?>" class="btn btn-outline-secondary">Annuler</a>
    </div>

</form>
</div>
</div>

<script>
(function () {
    const BASE = '<?= base_url('admin/zones') ?>';

    // DOM refs
    const selectPays     = document.getElementById('selectPays');
    const selectRegion   = document.getElementById('selectRegion');
    const selectVille    = document.getElementById('selectVille');
    const spinnerRegion  = document.getElementById('spinnerRegion');
    const spinnerVille   = document.getElementById('spinnerVille');
    const hiddenType     = document.getElementById('hiddenType');
    const hiddenParentId = document.getElementById('hiddenParentId');
    const hiddenPaysId   = document.getElementById('hiddenPaysId');
    const hiddenRegionId = document.getElementById('hiddenRegionId');
    const hiddenVilleId  = document.getElementById('hiddenVilleId');
    const labelName      = document.getElementById('labelName');
    const labelCode      = document.getElementById('labelCode');
    const codeRequired   = document.getElementById('codeRequired');
    const codeHint       = document.getElementById('codeHint');
    const inputCode      = document.getElementById('inputCode');
    const inputName      = document.getElementById('inputName');
    const levelBadge     = document.getElementById('levelBadge');
    const badgeRegion    = document.getElementById('badgeRegion');
    const badgeVille     = document.getElementById('badgeVille');

    // Niveau inféré selon ce qui est sélectionné
    const LEVELS = {
        pays:        { label: 'Pays',        color: 'primary',   namePlaceholder: 'Ex : Maroc, France…',          codeHint: 'Code ISO (ex : MA, FR)',  codePH: 'Ex : MA',    codeReq: false },
        region:      { label: 'Région',      color: 'success',   namePlaceholder: 'Ex : Casablanca-Settat…',      codeHint: 'Code région (optionnel)',  codePH: '—',          codeReq: false },
        ville:       { label: 'Ville',       color: 'info',      namePlaceholder: 'Ex : Casablanca…',             codeHint: 'Code ville (optionnel)',   codePH: '—',          codeReq: false },
        code_postal: { label: 'Code postal', color: 'warning',   namePlaceholder: 'Libellé (ex : Casablanca 1)', codeHint: 'Code postal (ex : 20000)', codePH: 'Ex : 20000', codeReq: true  },
    };

    function inferLevel() {
        if (!selectPays.value)   return 'pays';
        if (!selectRegion.value) return 'region';
        if (!selectVille.value)  return 'ville';
        return 'code_postal';
    }

    function updateHidden() {
        const level = inferLevel();
        const cfg   = LEVELS[level];

        hiddenType.value = level;

        // parent_id = dernier select rempli AVANT le niveau courant
        if (level === 'pays')        hiddenParentId.value = '';
        else if (level === 'region') hiddenParentId.value = selectPays.value;
        else if (level === 'ville')  hiddenParentId.value = selectRegion.value;
        else                         hiddenParentId.value = selectVille.value;

        hiddenPaysId.value   = selectPays.value   || '';
        hiddenRegionId.value = selectRegion.value || '';
        hiddenVilleId.value  = selectVille.value  || '';

        // Badge niveau
        levelBadge.className     = `badge bg-${cfg.color}`;
        levelBadge.textContent   = cfg.label;

        // Badges optionnel / requis sur région & ville
        badgeRegion.className    = selectPays.value   ? 'badge bg-primary ms-1 small' : 'badge bg-secondary ms-1 small';
        badgeRegion.textContent  = selectPays.value   ? 'disponible' : 'optionnel';
        badgeVille.className     = selectRegion.value ? 'badge bg-primary ms-1 small' : 'badge bg-secondary ms-1 small';
        badgeVille.textContent   = selectRegion.value ? 'disponible' : 'optionnel';

        // Labels dynamiques
        labelName.innerHTML      = cfg.namePlaceholder ? `<i class="bi bi-tag me-1 opacity-50"></i>Nom <span class="text-danger">*</span>` : 'Nom';
        inputName.placeholder    = cfg.namePlaceholder;
        codeHint.textContent     = cfg.codeHint;
        inputCode.placeholder    = cfg.codePH;
        inputCode.required       = cfg.codeReq;
        if (cfg.codeReq) {
            codeRequired.innerHTML = '<span class="text-danger">*</span>';
        } else {
            codeRequired.innerHTML = '';
        }
    }

    // AJAX : charger les enfants d'un parent
    function loadChildren(parentId, targetSelect, spinner, emptyMsg) {
        targetSelect.innerHTML = `<option value="">Chargement…</option>`;
        targetSelect.disabled  = true;
        spinner.classList.remove('d-none');

        fetch(`${BASE}/${parentId}/children`)
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    targetSelect.innerHTML = `<option value="">${emptyMsg}</option>`;
                } else {
                    targetSelect.innerHTML = '<option value="">— Sélectionner —</option>';
                    data.forEach(item => {
                        const o = document.createElement('option');
                        o.value       = item.id;
                        o.textContent = item.name + (item.code ? ` (${item.code})` : '');
                        targetSelect.appendChild(o);
                    });
                    targetSelect.disabled = false;
                }
            })
            .catch(() => {
                targetSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            })
            .finally(() => {
                spinner.classList.add('d-none');
                updateHidden();
            });
    }

    // Événement : changement Pays
    selectPays.addEventListener('change', function () {
        // Reset région
        selectRegion.innerHTML = '<option value="">— Sélectionner —</option>';
        selectRegion.disabled  = true;
        // Reset ville
        selectVille.innerHTML  = '<option value="">— Sélectionner d\'abord une région —</option>';
        selectVille.disabled   = true;

        if (this.value) {
            loadChildren(this.value, selectRegion, spinnerRegion, 'Aucune région disponible');
        }
        updateHidden();
    });

    // Événement : changement Région
    selectRegion.addEventListener('change', function () {
        // Reset ville
        selectVille.innerHTML = '<option value="">— Sélectionner —</option>';
        selectVille.disabled  = true;

        if (this.value) {
            loadChildren(this.value, selectVille, spinnerVille, 'Aucune ville disponible');
        }
        updateHidden();
    });

    // Événement : changement Ville
    selectVille.addEventListener('change', updateHidden);

    // ── Init (édition ou old() après erreur) ──────────────────
    (function init() {
        const preP = '<?= addslashes((string)$oldPaysId) ?>';
        const preR = '<?= addslashes((string)$oldRegionId) ?>';
        const preV = '<?= addslashes((string)$oldVilleId) ?>';

        if (preP && selectPays.value === preP) {
            // Les listes région/ville/code ont été rendues côté serveur
            if (selectRegion.options.length > 1) {
                selectRegion.disabled = false;
                if (preR) { selectRegion.value = preR; }
            }
            if (selectVille.options.length > 1) {
                selectVille.disabled = false;
                if (preV) { selectVille.value = preV; }
            }
        }
        updateHidden();
    })();
})();
</script>


// Pré-sélection hiérarchique (injectée par le contrôleur)
$preselect    = $preselect    ?? ['pays_id' => null, 'region_id' => null, 'ville_id' => null];
$regions_list = $regions_list ?? [];
$villes_list  = $villes_list  ?? [];

// Récupération old() après échec de validation POST
$oldPaysId   = old('pays_id',   $preselect['pays_id']   ?? '');
$oldRegionId = old('region_id', $preselect['region_id'] ?? '');
$oldVilleId  = old('ville_id',  $preselect['ville_id']  ?? '');
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/zones') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold">
            <?= $isEdit ? 'Modifier la zone' : 'Nouvelle adresse / zone' ?>
        </h4>
        <?php if ($isEdit) : ?>
        <p class="text-muted mb-0"><?= esc($zone['name']) ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-8">

<form method="POST"
      action="<?= $isEdit
          ? base_url('admin/zones/' . $zone['id'] . '/update')
          : base_url('admin/zones/store') ?>">
    <?= csrf_field() ?>

    <!-- ── ÉTAPE 1 : Type de zone ───────────────────────────── -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-layers me-1"></i> Niveau de la zone
        </div>
        <div class="card-body">
            <div class="row g-2">
                <?php foreach ($types as $typeKey => [$typeLabel, $typeIcon, $typeColor]) : ?>
                <div class="col-6 col-md-3">
                    <input type="radio" class="btn-check" name="type"
                           id="type_<?= $typeKey ?>" value="<?= $typeKey ?>"
                           autocomplete="off"
                           <?= $currentType === $typeKey ? 'checked' : '' ?>>
                    <label class="btn btn-outline-<?= $typeColor ?> w-100 py-3"
                           for="type_<?= $typeKey ?>">
                        <i class="bi <?= $typeIcon ?> d-block fs-4 mb-1"></i>
                        <?= $typeLabel ?>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ── ÉTAPE 2 : Hiérarchie (cascade) ──────────────────── -->
    <div class="card shadow-sm mb-4" id="parentCard">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-geo me-1"></i> Localisation parente
        </div>
        <div class="card-body">
            <div class="row g-3">

                <!-- Pays -->
                <div class="col-md-4" id="rowPays">
                    <label class="form-label fw-semibold">
                        Pays <span class="text-danger" id="paysRequired">*</span>
                    </label>
                    <select id="selectPays" class="form-select">
                        <option value="">-- Sélectionner un pays --</option>
                        <?php foreach ($pays_list as $p) : ?>
                        <option value="<?= $p['id'] ?>"
                            <?= (string)$oldPaysId === (string)$p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['name']) ?>
                            <?= $p['code'] ? ' (' . esc($p['code']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Région (AJAX après Pays) -->
                <div class="col-md-4" id="rowRegion" style="display:none;">
                    <label class="form-label fw-semibold">
                        Région <span class="text-danger" id="regionRequired">*</span>
                    </label>
                    <select id="selectRegion" class="form-select" disabled>
                        <option value="">-- Sélectionner d'abord un pays --</option>
                        <?php foreach ($regions_list as $r) : ?>
                        <option value="<?= $r['id'] ?>"
                            <?= (string)$oldRegionId === (string)$r['id'] ? 'selected' : '' ?>>
                            <?= esc($r['name']) ?>
                            <?= $r['code'] ? ' (' . esc($r['code']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text text-primary d-none" id="regionSpinner">
                        <span class="spinner-border spinner-border-sm me-1"></span>Chargement…
                    </div>
                </div>

                <!-- Ville (AJAX après Région) -->
                <div class="col-md-4" id="rowVille" style="display:none;">
                    <label class="form-label fw-semibold">
                        Ville <span class="text-danger" id="villeRequired">*</span>
                    </label>
                    <select id="selectVille" class="form-select" disabled>
                        <option value="">-- Sélectionner d'abord une région --</option>
                        <?php foreach ($villes_list as $v) : ?>
                        <option value="<?= $v['id'] ?>"
                            <?= (string)$oldVilleId === (string)$v['id'] ? 'selected' : '' ?>>
                            <?= esc($v['name']) ?>
                            <?= $v['code'] ? ' (' . esc($v['code']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text text-primary d-none" id="villeSpinner">
                        <span class="spinner-border spinner-border-sm me-1"></span>Chargement…
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Hidden parent_id mis à jour par JS -->
    <input type="hidden" name="parent_id"  id="hiddenParentId"
           value="<?= esc(old('parent_id', $zone['parent_id'] ?? '')) ?>">
    <!-- Hidden selects pour old() recovery -->
    <input type="hidden" name="pays_id"   id="hiddenPaysId"   value="<?= esc($oldPaysId) ?>">
    <input type="hidden" name="region_id" id="hiddenRegionId" value="<?= esc($oldRegionId) ?>">
    <input type="hidden" name="ville_id"  id="hiddenVilleId"  value="<?= esc($oldVilleId) ?>">

    <!-- ── ÉTAPE 3 : Informations de la zone ────────────────── -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-info-circle me-1"></i> Informations de la zone
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold" id="labelName">
                        Nom <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control" id="inputName"
                           value="<?= esc(old('name', $zone['name'] ?? '')) ?>"
                           maxlength="150" required
                           placeholder="Ex : Casablanca-Settat…">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold" id="labelCode">Code</label>
                    <input type="text" name="code" class="form-control" id="inputCode"
                           value="<?= esc(old('code', $zone['code'] ?? '')) ?>"
                           maxlength="20" placeholder="—">
                    <div class="form-text" id="codeHint"></div>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="isActive" name="is_active" value="1"
                            <?= old('is_active', $zone['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isActive">Zone active</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>
            <?= $isEdit ? 'Enregistrer les modifications' : 'Créer la zone' ?>
        </button>
        <a href="<?= base_url('admin/zones') ?>" class="btn btn-outline-secondary">Annuler</a>
    </div>

</form>
</div>
</div>

<script>
(function () {
    // ── Refs DOM ──────────────────────────────────────────────
    const typeRadios      = document.querySelectorAll('input[name="type"]');
    const parentCard      = document.getElementById('parentCard');
    const rowPays         = document.getElementById('rowPays');
    const rowRegion       = document.getElementById('rowRegion');
    const rowVille        = document.getElementById('rowVille');
    const selectPays      = document.getElementById('selectPays');
    const selectRegion    = document.getElementById('selectRegion');
    const selectVille     = document.getElementById('selectVille');
    const regionSpinner   = document.getElementById('regionSpinner');
    const villeSpinner    = document.getElementById('villeSpinner');
    const hiddenParentId  = document.getElementById('hiddenParentId');
    const hiddenPaysId    = document.getElementById('hiddenPaysId');
    const hiddenRegionId  = document.getElementById('hiddenRegionId');
    const hiddenVilleId   = document.getElementById('hiddenVilleId');
    const labelCode       = document.getElementById('labelCode');
    const inputCode       = document.getElementById('inputCode');
    const codeHint        = document.getElementById('codeHint');
    const labelName       = document.getElementById('labelName');

    const BASE = '<?= base_url('admin/zones') ?>';

    // ── Config par type ───────────────────────────────────────
    const config = {
        pays: {
            parentCard: false,
            showPays: false, showRegion: false, showVille: false,
            codeLabel: 'Code ISO pays', codeHint: 'Ex : MA, FR, DZ',
            codePlaceholder: 'Ex : MA', nameLabel: 'Nom du pays',
        },
        region: {
            parentCard: true,
            showPays: true, showRegion: false, showVille: false,
            codeLabel: 'Code région', codeHint: 'Optionnel',
            codePlaceholder: '—', nameLabel: 'Nom de la région',
        },
        ville: {
            parentCard: true,
            showPays: true, showRegion: true, showVille: false,
            codeLabel: 'Code ville', codeHint: 'Optionnel',
            codePlaceholder: '—', nameLabel: 'Nom de la ville',
        },
        code_postal: {
            parentCard: true,
            showPays: true, showRegion: true, showVille: true,
            codeLabel: 'Code postal *', codeHint: 'Ex : 20000, 75001',
            codePlaceholder: 'Ex : 20000', nameLabel: 'Libellé (optionnel)',
        },
    };

    // ── Chargement AJAX ───────────────────────────────────────
    function loadChildren(parentId, targetSelect, spinner) {
        targetSelect.innerHTML = '<option value="">Chargement…</option>';
        targetSelect.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`${BASE}/${parentId}/children`)
            .then(r => r.json())
            .then(data => {
                targetSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.name + (item.code ? ` (${item.code})` : '');
                    targetSelect.appendChild(opt);
                });
                targetSelect.disabled = data.length === 0;
            })
            .catch(() => {
                targetSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            })
            .finally(() => spinner.classList.add('d-none'));
    }

    // ── Appliquer config type ─────────────────────────────────
    function applyType(type) {
        const cfg = config[type] || config.pays;

        parentCard.style.display = cfg.parentCard  ? '' : 'none';
        rowPays.style.display    = cfg.showPays    ? '' : 'none';
        rowRegion.style.display  = cfg.showRegion  ? '' : 'none';
        rowVille.style.display   = cfg.showVille   ? '' : 'none';

        // required attr + label
        if (cfg.showPays)   selectPays.setAttribute('required', 'required');
        else                selectPays.removeAttribute('required');
        if (cfg.showRegion) selectRegion.setAttribute('required', 'required');
        else                selectRegion.removeAttribute('required');
        if (cfg.showVille)  selectVille.setAttribute('required', 'required');
        else                selectVille.removeAttribute('required');

        // Code field UX
        labelCode.innerHTML       = cfg.codeLabel + (type === 'code_postal' ? ' <span class="text-danger">*</span>' : '');
        inputCode.placeholder     = cfg.codePlaceholder;
        inputCode.required        = type === 'code_postal';
        codeHint.textContent      = cfg.codeHint;
        labelName.innerHTML       = cfg.nameLabel + ' <span class="text-danger">*</span>';
        inputCode.required        = type === 'code_postal';

        updateParentId();
    }

    // ── Mise à jour parent_id caché ───────────────────────────
    function updateParentId() {
        const type = getSelectedType();
        if (type === 'pays')        { hiddenParentId.value = ''; }
        else if (type === 'region') { hiddenParentId.value = selectPays.value; }
        else if (type === 'ville')  { hiddenParentId.value = selectRegion.value; }
        else                        { hiddenParentId.value = selectVille.value; }

        hiddenPaysId.value   = selectPays.value;
        hiddenRegionId.value = selectRegion.value || '';
        hiddenVilleId.value  = selectVille.value  || '';
    }

    function getSelectedType() {
        const checked = document.querySelector('input[name="type"]:checked');
        return checked ? checked.value : 'pays';
    }

    // ── Événements ────────────────────────────────────────────
    typeRadios.forEach(r => r.addEventListener('change', () => {
        applyType(r.value);
    }));

    selectPays.addEventListener('change', function () {
        // Reset région + ville
        selectRegion.innerHTML = '<option value="">-- Sélectionner --</option>';
        selectRegion.disabled  = true;
        selectVille.innerHTML  = '<option value="">-- Sélectionner d\'abord une région --</option>';
        selectVille.disabled   = true;

        if (this.value) {
            loadChildren(this.value, selectRegion, regionSpinner);
        }
        updateParentId();
    });

    selectRegion.addEventListener('change', function () {
        // Reset ville
        selectVille.innerHTML = '<option value="">-- Sélectionner --</option>';
        selectVille.disabled  = true;

        if (this.value && getSelectedType() === 'code_postal') {
            loadChildren(this.value, selectVille, villeSpinner);
        }
        updateParentId();
    });

    selectVille.addEventListener('change', updateParentId);

    // ── Init au chargement ────────────────────────────────────
    const initialType = getSelectedType();
    applyType(initialType);

    // Si mode édition ou old() : pré-remplir les AJAX selects
    const preselects = {
        pays:   '<?= $oldPaysId ?>',
        region: '<?= $oldRegionId ?>',
        ville:  '<?= $oldVilleId ?>',
    };

    if (preselects.pays && selectPays.value) {
        // Les options région (et ville) ont été rendues côté serveur —
        // il suffit de les activer si elles ont des enfants
        if (selectRegion.options.length > 1) {
            selectRegion.disabled = false;
            if (preselects.region) {
                selectRegion.value = preselects.region;
            }
        }
        if (selectVille.options.length > 1) {
            selectVille.disabled = false;
            if (preselects.ville) {
                selectVille.value = preselects.ville;
            }
        }
        updateParentId();
    }
})();
</script>
