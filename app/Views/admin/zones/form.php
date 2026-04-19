<?php
/**
 * form.php — Wizard de création / modification d'une zone géographique
 *
 * Variables injectées :
 *   $zone         array   — données de la zone ([] si création)
 *   $zoneType     string  — 'pays' | 'region' | 'ville' | 'quartier'
 *   $pays_list    array   — pays actifs
 *   $preselect    array   — ['pays_id', 'region_id', 'ville_id'] (mode édition)
 *   $regions_list array   — régions pré-chargées (mode édition)
 *   $villes_list  array   — villes pré-chargées (mode édition)
 */

$isEdit = ! empty($zone['id']);
$type   = $zoneType;

// Normaliser les anciens types (avant migration NormalizeZoneTypes)
$typeAliases = [
    'governorate' => 'region',
    'city'        => 'ville',
    'district'    => 'quartier',
];
if (isset($typeAliases[$type])) {
    $type = $typeAliases[$type];
}
if (! in_array($type, ['pays', 'region', 'ville', 'quartier'])) {
    $type = 'quartier'; // fallback sécurisé
}

// Métadonnées du type
$allMeta = [
    'pays'     => ['label' => 'Pays',          'icon' => 'bi-globe2',       'color' => 'primary',
                   'codeLabel' => 'Code ISO',    'codePH' => 'Ex : MA, FR, DZ',  'codeHint' => 'Code ISO du pays (2 lettres)'],
    'region'   => ['label' => 'Région / État',  'icon' => 'bi-map',          'color' => 'success',
                   'codeLabel' => 'Code',         'codePH' => '—',                 'codeHint' => 'Code région (optionnel)'],
    'ville'    => ['label' => 'Ville',           'icon' => 'bi-buildings',    'color' => 'info',
                   'codeLabel' => 'Code postal',  'codePH' => 'Ex : 20000',        'codeHint' => 'Code postal de la ville'],
    'quartier' => ['label' => 'Quartier',        'icon' => 'bi-geo-alt-fill', 'color' => 'warning',
                   'codeLabel' => 'Code',         'codePH' => '—',                 'codeHint' => 'Code interne (optionnel)'],
];
$meta = $allMeta[$type];

// Étapes du wizard selon le type
$steps = [];
if ($type !== 'pays')                               $steps[] = ['id' => 'pays',     'label' => 'Pays',        'optional' => false];
if (in_array($type, ['ville', 'quartier']))         $steps[] = ['id' => 'region',   'label' => 'Région',      'optional' => ($type === 'ville')];
if ($type === 'quartier')                           $steps[] = ['id' => 'ville',    'label' => 'Ville',       'optional' => false];
$steps[] = ['id' => 'info', 'label' => $meta['label'], 'optional' => false];

$totalSteps  = count($steps);
$initialStep = ($isEdit && $totalSteps > 1) ? $totalSteps : 1;

// Valeurs pré-sélectionnées (mode édition ou old() après erreur)
$oldPaysId   = old('pays_id',   $preselect['pays_id']   ?? '');
$oldRegionId = old('region_id', $preselect['region_id'] ?? '');
$oldVilleId  = old('ville_id',  $preselect['ville_id']  ?? '');
?>

<!-- ── EN-TÊTE ─────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/zones?tab=' . $type) ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi <?= $meta['icon'] ?> me-2 text-<?= $meta['color'] ?>"></i>
            <?= $isEdit ? 'Modifier' : 'Ajouter' ?> <?= esc($meta['label']) ?>
        </h4>
        <?php if ($isEdit): ?>
        <p class="text-muted mb-0 small"><?= esc($zone['name']) ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-7">

<!-- ── INDICATEUR DE PROGRESSION ──────────────────────────────────── -->
<?php if ($totalSteps > 1): ?>
<div class="d-flex align-items-center mb-4" id="wizardProgress">
    <?php foreach ($steps as $i => $step): ?>
    <div class="d-flex flex-column align-items-center text-center"
         data-step-node="<?= $i + 1 ?>"
         style="flex:1">
        <div class="wizard-circle d-flex align-items-center justify-content-center
                    rounded-circle border-2 fw-semibold"
             style="width:38px;height:38px;font-size:.85rem;transition:all .25s;border-style:solid">
            <?= $i + 1 ?>
        </div>
        <div class="mt-1" style="font-size:.7rem;line-height:1.2;max-width:64px">
            <?= esc($step['label']) ?>
            <?php if ($step['optional']): ?>
            <br><em class="text-muted">(opt.)</em>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($i < $totalSteps - 1): ?>
    <div class="wizard-line" data-line="<?= $i + 1 ?>"
         style="height:2px;flex:2;background:#dee2e6;margin-bottom:22px;transition:background .25s"></div>
    <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ── FORMULAIRE WIZARD ───────────────────────────────────────────── -->
<form method="POST" id="wizardForm"
      action="<?= $isEdit
          ? base_url('admin/zones/' . $zone['id'] . '/update')
          : base_url('admin/zones/store') ?>">
    <?= csrf_field() ?>

    <!-- Champs cachés soumis avec le formulaire -->
    <input type="hidden" name="type"      value="<?= esc($type) ?>">
    <input type="hidden" name="pays_id"   id="hPaysId"   value="<?= esc((string) $oldPaysId) ?>">
    <input type="hidden" name="region_id" id="hRegionId" value="<?= esc((string) $oldRegionId) ?>">
    <input type="hidden" name="ville_id"  id="hVilleId"  value="<?= esc((string) $oldVilleId) ?>">

    <?php foreach ($steps as $i => $step): ?>
    <?php $stepNum = $i + 1; ?>

    <div class="wizard-step" data-step="<?= $stepNum ?>"
         style="<?= $stepNum !== $initialStep ? 'display:none' : '' ?>">
        <div class="card shadow-sm">

            <!-- En-tête de l'étape -->
            <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
                <?php if ($totalSteps > 1): ?>
                <span class="badge rounded-pill text-bg-<?= $meta['color'] ?> me-1">
                    <?= $stepNum ?>/<?= $totalSteps ?>
                </span>
                <?php endif; ?>
                <?php echo match ($step['id']) {
                    'pays'   => '<i class="bi bi-globe2 me-1 text-primary"></i> Sélectionner le pays',
                    'region' => '<i class="bi bi-map me-1 text-success"></i> Sélectionner la région / état'
                                . ($step['optional'] ? ' <small class="text-muted fw-normal">(optionnel)</small>' : ''),
                    'ville'  => '<i class="bi bi-buildings me-1 text-info"></i> Sélectionner la ville',
                    'info'   => '<i class="bi ' . $meta['icon'] . ' me-1 text-' . $meta['color'] . '"></i> Informations '
                                . esc($meta['label']),
                }; ?>
            </div>

            <!-- Corps de l'étape -->
            <div class="card-body">

                <?php if ($step['id'] === 'pays'): ?>
                <!-- ──────────────── ÉTAPE : PAYS ──────────────────── -->
                <label class="form-label fw-semibold" for="selectPays">
                    Pays <span class="text-danger">*</span>
                </label>
                <select id="selectPays" class="form-select form-select-lg">
                    <option value="">— Sélectionner un pays —</option>
                    <?php foreach ($pays_list as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= (string) $oldPaysId === (string) $p['id'] ? 'selected' : '' ?>>
                        <?= esc($p['name']) ?><?= $p['code'] ? ' (' . esc($p['code']) . ')' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text text-danger d-none mt-1" id="errPays">
                    Veuillez sélectionner un pays.
                </div>

                <?php elseif ($step['id'] === 'region'): ?>
                <!-- ──────────────── ÉTAPE : RÉGION ────────────────── -->
                <div class="alert alert-light border py-2 mb-3 small d-flex align-items-center gap-2">
                    <i class="bi bi-globe2 text-primary"></i>
                    Pays : <strong id="paysLabel">—</strong>
                </div>
                <label class="form-label fw-semibold" for="selectRegion">
                    Région / État
                    <?php if (! $step['optional']): ?>
                    <span class="text-danger">*</span>
                    <?php else: ?>
                    <span class="text-muted small">(laissez vide si pas de région)</span>
                    <?php endif; ?>
                </label>
                <div class="position-relative">
                    <select id="selectRegion" class="form-select"
                        <?= $oldPaysId ? '' : 'disabled' ?>>
                        <option value="">— Aucune / Passer cette étape —</option>
                        <?php foreach ($regions_list as $r): ?>
                        <option value="<?= $r['id'] ?>"
                            <?= (string) $oldRegionId === (string) $r['id'] ? 'selected' : '' ?>>
                            <?= esc($r['name']) ?><?= $r['code'] ? ' (' . esc($r['code']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="position-absolute top-50 end-0 translate-middle-y pe-3 d-none"
                         id="spinnerRegion">
                        <span class="spinner-border spinner-border-sm text-primary"></span>
                    </div>
                </div>

                <?php elseif ($step['id'] === 'ville'): ?>
                <!-- ──────────────── ÉTAPE : VILLE ─────────────────── -->
                <div class="alert alert-light border py-2 mb-3 small d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3 text-muted"></i>
                    <span id="villeCtxLabel">—</span>
                </div>
                <label class="form-label fw-semibold" for="selectVille">
                    Ville <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <select id="selectVille" class="form-select"
                        <?= ($oldRegionId || $oldPaysId) ? '' : 'disabled' ?>>
                        <option value="">— Sélectionner une ville —</option>
                        <?php foreach ($villes_list as $v): ?>
                        <option value="<?= $v['id'] ?>"
                            <?= (string) $oldVilleId === (string) $v['id'] ? 'selected' : '' ?>>
                            <?= esc($v['name']) ?><?= $v['code'] ? ' (' . esc($v['code']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="position-absolute top-50 end-0 translate-middle-y pe-3 d-none"
                         id="spinnerVille">
                        <span class="spinner-border spinner-border-sm text-primary"></span>
                    </div>
                </div>
                <div class="form-text text-danger d-none mt-1" id="errVille">
                    Veuillez sélectionner une ville.
                </div>

                <?php elseif ($step['id'] === 'info'): ?>
                <!-- ──────────────── ÉTAPE : INFORMATIONS ──────────── -->

                <?php if ($totalSteps > 1): ?>
                <!-- Fil d'Ariane contextuel -->
                <div class="d-flex align-items-center gap-2 mb-4 p-2 bg-light rounded small">
                    <i class="bi bi-diagram-3 text-muted"></i>
                    <span id="breadcrumbCtx" class="text-muted">—</span>
                </div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label fw-semibold" for="inputName">
                            Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="inputName" name="name"
                               class="form-control <?= session()->getFlashdata('errors') ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('name', $zone['name'] ?? '')) ?>"
                               maxlength="150" required autofocus
                               placeholder="<?= match ($type) {
                                   'pays'     => 'Ex : Maroc, France, Espagne…',
                                   'region'   => 'Ex : Casablanca-Settat, Île-de-France…',
                                   'ville'    => 'Ex : Casablanca, Paris, Madrid…',
                                   'quartier' => 'Ex : Maarif, Bourgogne, Montparnasse…',
                               } ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold" for="inputCode">
                            <?= esc($meta['codeLabel']) ?>
                        </label>
                        <input type="text" id="inputCode" name="code"
                               class="form-control"
                               value="<?= esc(old('code', $zone['code'] ?? '')) ?>"
                               maxlength="20"
                               placeholder="<?= esc($meta['codePH']) ?>">
                        <div class="form-text"><?= esc($meta['codeHint']) ?></div>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="isActive" name="is_active" value="1"
                                <?= old('is_active', $zone['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Entrée active</label>
                        </div>
                    </div>
                </div>

                <?php endif; ?>

            </div><!-- /.card-body -->

            <!-- Pied de carte : navigation -->
            <div class="card-footer bg-transparent d-flex justify-content-between align-items-center gap-2">

                <!-- Gauche : Annuler (step 1) ou Précédent (autres steps) -->
                <?php if ($stepNum === 1): ?>
                <a href="<?= base_url('admin/zones?tab=' . $type) ?>"
                   class="btn btn-outline-secondary">Annuler</a>
                <?php else: ?>
                <button type="button" class="btn btn-outline-secondary btn-prev"
                        data-prev="<?= $stepNum - 1 ?>">
                    <i class="bi bi-arrow-left me-1"></i>Précédent
                </button>
                <?php endif; ?>

                <!-- Droite : Suivant ou Créer/Enregistrer -->
                <?php if ($stepNum < $totalSteps): ?>
                <button type="button" class="btn btn-primary btn-next"
                        data-next="<?= $stepNum + 1 ?>"
                        data-step-id="<?= $step['id'] ?>"
                        data-optional="<?= $step['optional'] ? '1' : '0' ?>">
                    Suivant <i class="bi bi-arrow-right ms-1"></i>
                </button>
                <?php else: ?>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i>
                    <?= $isEdit ? 'Enregistrer les modifications' : 'Créer ' . esc($meta['label']) ?>
                </button>
                <?php endif; ?>

            </div><!-- /.card-footer -->
        </div><!-- /.card -->
    </div><!-- /.wizard-step -->

    <?php endforeach; ?>
</form>

</div><!-- /.col -->
</div><!-- /.row -->

<script>
(function () {
    'use strict';

    const BASE        = '<?= base_url('admin/zones') ?>';
    const TOTAL_STEPS = <?= $totalSteps ?>;
    const TYPE        = '<?= $type ?>';

    // ── Références DOM ────────────────────────────────────────
    const hPaysId    = document.getElementById('hPaysId');
    const hRegionId  = document.getElementById('hRegionId');
    const hVilleId   = document.getElementById('hVilleId');
    const selectPays   = document.getElementById('selectPays');
    const selectRegion = document.getElementById('selectRegion');
    const selectVille  = document.getElementById('selectVille');
    const spinnerReg   = document.getElementById('spinnerRegion');
    const spinnerVil   = document.getElementById('spinnerVille');

    let currentStep = <?= $initialStep ?>;

    // ── Afficher une étape ────────────────────────────────────
    function showStep(n) {
        document.querySelectorAll('.wizard-step').forEach(el => {
            el.style.display = parseInt(el.dataset.step) === n ? '' : 'none';
        });
        currentStep = n;
        updateProgress(n);
        updateContextLabels(n);
    }

    // ── Mettre à jour la progression visuelle ─────────────────
    function updateProgress(n) {
        document.querySelectorAll('[data-step-node]').forEach(node => {
            const sn   = parseInt(node.dataset.stepNode);
            const circ = node.querySelector('.wizard-circle');
            if (sn < n) {
                // Complété
                circ.style.background   = '#0d6efd';
                circ.style.color        = '#fff';
                circ.style.borderColor  = '#0d6efd';
                circ.innerHTML          = '<i class="bi bi-check-lg" style="font-size:.8rem"></i>';
            } else if (sn === n) {
                // Actif
                circ.style.background   = '#fff';
                circ.style.color        = '#0d6efd';
                circ.style.borderColor  = '#0d6efd';
                circ.style.fontWeight   = '700';
                circ.textContent        = sn;
            } else {
                // À venir
                circ.style.background   = '#fff';
                circ.style.color        = '#adb5bd';
                circ.style.borderColor  = '#dee2e6';
                circ.style.fontWeight   = '400';
                circ.textContent        = sn;
            }
        });
        // Lignes de connexion
        document.querySelectorAll('[data-line]').forEach(line => {
            const idx = parseInt(line.dataset.line);
            line.style.background = idx < n ? '#0d6efd' : '#dee2e6';
        });
    }

    // ── Mettre à jour les labels contextuels ──────────────────
    function updateContextLabels(n) {
        const paysText   = selectPays   && selectPays.value
            ? selectPays.options[selectPays.selectedIndex].text : '—';
        const regionText = selectRegion && selectRegion.value
            ? selectRegion.options[selectRegion.selectedIndex].text : null;
        const villeText  = selectVille  && selectVille.value
            ? selectVille.options[selectVille.selectedIndex].text : null;

        const paysEl = document.getElementById('paysLabel');
        if (paysEl) paysEl.textContent = paysText;

        const villeCtxEl = document.getElementById('villeCtxLabel');
        if (villeCtxEl) {
            const parts = [paysText];
            if (regionText) parts.push(regionText);
            villeCtxEl.textContent = parts.join(' › ');
        }

        const breadEl = document.getElementById('breadcrumbCtx');
        if (breadEl) {
            const parts = [];
            if (selectPays   && selectPays.value)   parts.push(paysText);
            if (selectRegion && selectRegion.value)  parts.push(regionText);
            if (selectVille  && selectVille.value)   parts.push(villeText);
            breadEl.textContent = parts.length ? parts.join(' › ') : '—';
        }
    }

    // ── Valider l'étape courante avant d'avancer ──────────────
    function validateStep(stepId, isOptional) {
        if (stepId === 'pays') {
            const ok  = selectPays && selectPays.value !== '';
            const err = document.getElementById('errPays');
            if (err) err.classList.toggle('d-none', ok);
            return ok;
        }
        if (stepId === 'ville') {
            const ok  = selectVille && selectVille.value !== '';
            const err = document.getElementById('errVille');
            if (err) err.classList.toggle('d-none', ok);
            return ok;
        }
        // region est optionnelle (valeur = '' autorisée)
        return true;
    }

    // ── Charger les enfants d'une zone via AJAX ───────────────
    function loadChildren(parentId, targetSelect, spinner, emptyMsg) {
        if (! targetSelect) return;
        targetSelect.innerHTML = '<option value="">Chargement…</option>';
        targetSelect.disabled  = true;
        if (spinner) spinner.classList.remove('d-none');

        fetch(`${BASE}/${parentId}/children`)
            .then(r => r.json())
            .then(data => {
                if (data.length) {
                    targetSelect.innerHTML = '<option value="">— Sélectionner —</option>';
                    data.forEach(item => {
                        const o = document.createElement('option');
                        o.value       = item.id;
                        o.textContent = item.name + (item.code ? ` (${item.code})` : '');
                        targetSelect.appendChild(o);
                    });
                    targetSelect.disabled = false;
                } else {
                    targetSelect.innerHTML = `<option value="">${emptyMsg}</option>`;
                }
            })
            .catch(() => {
                targetSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            })
            .finally(() => {
                if (spinner) spinner.classList.add('d-none');
                syncHiddens();
            });
    }

    // ── Synchroniser les champs cachés ────────────────────────
    function syncHiddens() {
        if (hPaysId)   hPaysId.value   = (selectPays   && selectPays.value)   || '';
        if (hRegionId) hRegionId.value = (selectRegion && selectRegion.value) || '';
        if (hVilleId)  hVilleId.value  = (selectVille  && selectVille.value)  || '';
    }

    // ── Événements : boutons Suivant / Précédent ──────────────
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', function () {
            const stepId   = this.dataset.stepId;
            const isOpt    = this.dataset.optional === '1';
            const nextStep = parseInt(this.dataset.next);

            if (! validateStep(stepId, isOpt)) return;

            // Si on quitte l'étape région sans sélection et que la ville est
            // attendue au step suivant, charger les villes depuis le pays.
            if (stepId === 'region' && selectVille) {
                const parentId = (selectRegion && selectRegion.value)
                    ? selectRegion.value
                    : (hPaysId ? hPaysId.value : '');
                if (parentId) {
                    loadChildren(parentId, selectVille, spinnerVil, 'Aucune ville disponible');
                }
            }

            syncHiddens();
            showStep(nextStep);
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', function () {
            showStep(parseInt(this.dataset.prev));
        });
    });

    // ── Événements : changement Pays ─────────────────────────
    if (selectPays) {
        selectPays.addEventListener('change', function () {
            // Réinitialiser région et ville
            if (selectRegion) {
                selectRegion.innerHTML = '<option value="">— Aucune / Passer —</option>';
                selectRegion.disabled  = true;
                if (hRegionId) hRegionId.value = '';
            }
            if (selectVille) {
                selectVille.innerHTML = '<option value="">— Sélectionner une ville —</option>';
                selectVille.disabled  = true;
                if (hVilleId) hVilleId.value = '';
            }

            if (this.value && selectRegion) {
                loadChildren(this.value, selectRegion, spinnerReg, 'Aucune région disponible');
            }
            syncHiddens();
        });
    }

    // ── Événements : changement Région ───────────────────────
    if (selectRegion) {
        selectRegion.addEventListener('change', function () {
            if (selectVille) {
                selectVille.innerHTML = '<option value="">— Sélectionner —</option>';
                selectVille.disabled  = true;
                if (hVilleId) hVilleId.value = '';
            }

            if (selectVille) {
                const parentId = this.value || (hPaysId ? hPaysId.value : '');
                if (parentId) {
                    loadChildren(parentId, selectVille, spinnerVil, 'Aucune ville disponible');
                }
            }
            syncHiddens();
        });
    }

    // ── Événements : changement Ville ────────────────────────
    if (selectVille) {
        selectVille.addEventListener('change', syncHiddens);
    }

    // ── Initialisation ────────────────────────────────────────
    (function init () {
        const preP = '<?= (int) ($preselect['pays_id']   ?? 0) ?>';
        const preR = '<?= (int) ($preselect['region_id'] ?? 0) ?>';
        const preV = '<?= (int) ($preselect['ville_id']  ?? 0) ?>';

        // Mode édition : activer les selects si options déjà chargées côté serveur
        if (preP && selectPays) {
            if (selectRegion && selectRegion.options.length > 1) {
                selectRegion.disabled = false;
                if (preR) selectRegion.value = preR;
            }
            if (selectVille && selectVille.options.length > 1) {
                selectVille.disabled = false;
                if (preV) selectVille.value = preV;
            }
        }

        syncHiddens();
        showStep(currentStep);
    })();

})();
</script>
