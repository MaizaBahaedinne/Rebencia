<?php
$isEdit  = ! empty($visit['id']);
$perms   = session()->get('permissions') ?? [];
$errors  = session()->getFlashdata('errors') ?? [];
?>

<!-- ── EN-TÊTE ───────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/visits' . ($isEdit ? '/' . $visit['id'] : '')) ?>"
       class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">
        <i class="bi bi-calendar-plus me-2 text-primary"></i>
        <?= esc($page_title) ?>
    </h4>
</div>

<?php if (! empty($errors)): ?>
<div class="alert alert-danger py-2 small">
    <ul class="mb-0 ps-3">
        <?php foreach ($errors as $e): ?>
        <li><?= esc($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST"
      action="<?= base_url($isEdit ? 'admin/visits/' . $visit['id'] . '/update' : 'admin/visits/store') ?>"
      novalidate>
    <?= csrf_field() ?>
    <input type="hidden" id="visitId" value="<?= $isEdit ? (int) $visit['id'] : '' ?>">
    <input type="hidden" name="client_signature" id="clientSignatureData"
           value="<?= esc($visit['client_signature'] ?? '') ?>">

<div class="row g-4">

    <!-- ── COLONNE GAUCHE ─────────────────────────────────────────── -->
    <div class="col-lg-8">

        <!-- Section 1 : Informations -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-people me-1 text-primary"></i> Section 1 — Informations
            </div>
            <div class="card-body">

                <!-- Client -->
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="client_id">
                        Client <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="searchClient" class="form-control form-control-sm mb-1"
                           placeholder="Filtrer par nom ou téléphone…"
                           oninput="filterSelect(this, 'client_id')">
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">— Sélectionner un client —</option>
                        <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= (string) old('client_id', $visit['client_id'] ?? '') === (string) $c['id'] ? 'selected' : '' ?>>
                            <?= esc($c['last_name'] . ' ' . $c['first_name']) ?> — <?= esc($c['phone']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Bien -->
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="property_id">
                        Bien immobilier <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="searchProperty" class="form-control form-control-sm mb-1"
                           placeholder="Filtrer par titre, référence ou ville…"
                           oninput="filterSelect(this, 'property_id')">
                    <select name="property_id" id="property_id" class="form-select" required>
                        <option value="">— Sélectionner un bien —</option>
                        <?php foreach ($properties as $pr): ?>
                        <option value="<?= $pr['id'] ?>"
                            <?= (string) old('property_id', $visit['property_id'] ?? '') === (string) $pr['id'] ? 'selected' : '' ?>>
                            <?= esc($pr['reference'] ? '[' . $pr['reference'] . '] ' : '') ?><?= esc($pr['title']) ?><?= $pr['city'] ? ' — ' . esc($pr['city']) : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Agent -->
                <div class="mb-0">
                    <label class="form-label fw-semibold" for="agent_id">
                        Agent responsable <span class="text-danger">*</span>
                    </label>
                    <select name="agent_id" id="agent_id" class="form-select" required
                            onchange="triggerAvailabilityCheck()">
                        <option value="">— Sélectionner un agent —</option>
                        <?php foreach ($agents as $ag): ?>
                        <option value="<?= $ag['id'] ?>"
                            <?= (string) old('agent_id', $visit['agent_id'] ?? '') === (string) $ag['id'] ? 'selected' : '' ?>>
                            <?= esc($ag['first_name'] . ' ' . $ag['last_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
        </div>

        <!-- Section 2 : Planification -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-clock me-1 text-warning"></i> Section 2 — Planification
            </div>
            <div class="card-body">

                <div class="row g-3">
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold" for="visit_date">
                            Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="visit_date" id="visit_date"
                               class="form-control" required
                               value="<?= esc(old('visit_date', $visit['visit_date'] ?? date('Y-m-d'))) ?>"
                               onchange="triggerAvailabilityCheck()">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold" for="visit_time">
                            Heure <span class="text-danger">*</span>
                        </label>
                        <input type="time" name="visit_time" id="visit_time"
                               class="form-control" required
                               value="<?= esc(old('visit_time', $visit['visit_time'] ?? '10:00')) ?>"
                               onchange="triggerAvailabilityCheck()">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold">Durée</label>
                        <div class="d-flex gap-2 flex-wrap pt-1">
                            <?php foreach ([30 => '30 min', 60 => '1h', 90 => '1h30', 120 => '2h'] as $dVal => $dLabel): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="duration"
                                       id="dur<?= $dVal ?>" value="<?= $dVal ?>"
                                       <?= (string) old('duration', $visit['duration'] ?? 60) === (string) $dVal ? 'checked' : '' ?>
                                       onchange="triggerAvailabilityCheck()">
                                <label class="form-check-label" for="dur<?= $dVal ?>"><?= $dLabel ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Résultat de la vérification de disponibilité -->
                <div id="availabilityStatus" class="mt-2 small"></div>

            </div>
        </div>

        <!-- Section 4 : Notes -->
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-chat-left-text me-1 text-info"></i> Section 4 — Notes internes
            </div>
            <div class="card-body">
                <textarea name="notes" class="form-control" rows="4"
                          placeholder="Notes internes sur cette visite…"><?= esc(old('notes', $visit['notes'] ?? '')) ?></textarea>
            </div>
        </div>

    </div><!-- /col-lg-8 -->

    <!-- ── COLONNE DROITE ─────────────────────────────────────────── -->
    <div class="col-lg-4">

        <!-- Section 3 : Statut -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-flag me-1 text-success"></i> Section 3 — Statut
            </div>
            <div class="card-body">
                <?php
                $currentStatus = old('status', $visit['status'] ?? 'planifiee');
                foreach ($statusLabels as $sKey => $sMeta):
                ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="status"
                           id="status_<?= $sKey ?>" value="<?= $sKey ?>"
                           <?= $currentStatus === $sKey ? 'checked' : '' ?>>
                    <label class="form-check-label d-flex align-items-center gap-2"
                           for="status_<?= $sKey ?>">
                        <span class="badge text-bg-<?= $sMeta['color'] ?>">
                            <i class="bi <?= $sMeta['icon'] ?> me-1"></i><?= $sMeta['label'] ?>
                        </span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="card shadow-sm">
            <div class="card-body d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>
                    <?= $isEdit ? 'Enregistrer les modifications' : 'Planifier la visite' ?>
                </button>
                <a href="<?= base_url('admin/visits' . ($isEdit ? '/' . $visit['id'] : '')) ?>"
                   class="btn btn-light">
                    Annuler
                </a>
            </div>
        </div>

    </div><!-- /col-lg-4 -->

</div><!-- /row -->
</form>

<!-- ── MODAL SIGNATURE CLIENT ────────────────────────────────────────────── -->
<div class="modal fade" id="signatureModal" data-bs-backdrop="static" tabindex="-1"
     aria-labelledby="signatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signatureModalLabel">
                    <i class="bi bi-pen me-2 text-primary"></i>Signature du client
                </h5>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted small mb-3">
                    Faites signer le client sur l'écran tactile pour confirmer la visite.
                </p>
                <div class="border rounded bg-white position-relative"
                     style="touch-action:none; cursor:crosshair;">
                    <canvas id="sigCanvas" style="width:100%; display:block; height:200px;"></canvas>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="sigClearBtn">
                        <i class="bi bi-eraser me-1"></i>Effacer
                    </button>
                    <span class="text-muted small align-self-center" id="sigEmptyMsg" style="display:none;">
                        Veuillez signer avant de valider.
                    </span>
                </div>
            </div>
            <div class="modal-footer flex-column gap-2">
                <button type="button" class="btn btn-success w-100" id="sigValidateBtn">
                    <i class="bi bi-check2-circle me-1"></i>Valider la signature et enregistrer
                </button>
                <button type="button" class="btn btn-link text-muted small w-100" id="sigSkipBtn">
                    Enregistrer sans signature
                </button>
            </div>
        </div>
    </div>
</div>

<!-- signature_pad.js -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4/dist/signature_pad.umd.min.js"></script>

<script>
(function () {
    'use strict';

    const BASE_URL  = '<?= base_url('/') ?>';
    const VISIT_ID  = '<?= $isEdit ? (int) $visit['id'] : '' ?>';

    // ── Filtre select ────────────────────────────────────────────────
    window.filterSelect = function (input, selectId) {
        const filter = input.value.toLowerCase();
        const select = document.getElementById(selectId);
        Array.from(select.options).forEach(function (opt) {
            if (opt.value === '') return;
            opt.hidden = ! opt.text.toLowerCase().includes(filter);
        });
    };

    // ── Vérification disponibilité ───────────────────────────────────
    let availTimer = null;

    window.triggerAvailabilityCheck = function () {
        clearTimeout(availTimer);
        availTimer = setTimeout(checkAvailability, 600);
    };

    function checkAvailability() {
        const agentId  = document.getElementById('agent_id').value;
        const date     = document.getElementById('visit_date').value;
        const time     = document.getElementById('visit_time').value;
        const durEl    = document.querySelector('input[name="duration"]:checked');
        const duration = durEl ? durEl.value : '60';
        const el       = document.getElementById('availabilityStatus');

        if (! agentId || ! date || ! time) {
            el.innerHTML = '';
            return;
        }

        el.innerHTML = '<span class="text-muted"><i class="bi bi-hourglass-split me-1"></i>Vérification…</span>';

        let url = BASE_URL + 'admin/visits/check-availability'
                + '?agent_id=' + encodeURIComponent(agentId)
                + '&date='     + encodeURIComponent(date)
                + '&time='     + encodeURIComponent(time)
                + '&duration=' + encodeURIComponent(duration);

        if (VISIT_ID) {
            url += '&exclude_id=' + encodeURIComponent(VISIT_ID);
        }

        fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.available) {
                    el.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Créneau disponible</span>';
                } else {
                    el.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Conflit détecté — cet agent a déjà une visite à ce créneau</span>';
                }
            })
            .catch(function () {
                el.innerHTML = '';
            });
    }

    // Vérifier au chargement si on est en mode édition
    <?php if ($isEdit && ! empty($visit['agent_id'])): ?>
    checkAvailability();
    <?php endif; ?>

    // ── Signature pad ─────────────────────────────────────────────────
    var signaturePad = null;
    var pendingSubmit = false;

    // Initialise le canvas au ratio DPR pour une écriture nette sur mobile
    function initSignaturePad() {
        var canvas = document.getElementById('sigCanvas');
        var ratio  = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width  = canvas.offsetWidth  * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        if (signaturePad) {
            signaturePad.clear();
        } else {
            signaturePad = new SignaturePad(canvas, {
                minWidth: 1,
                maxWidth: 3,
                penColor: '#000000',
            });
        }
    }

    // Intercept form submit
    var form = document.querySelector('form[action*="store"], form[action*="update"]');
    form.addEventListener('submit', function (e) {
        var statusEl = document.querySelector('input[name="status"]:checked');
        if (statusEl && statusEl.value === 'effectuee') {
            e.preventDefault();
            pendingSubmit = true;
            var modal = new bootstrap.Modal(document.getElementById('signatureModal'));
            modal.show();
            document.getElementById('signatureModal').addEventListener('shown.bs.modal', function () {
                initSignaturePad();
            }, { once: true });
        }
    });

    // Effacer
    document.getElementById('sigClearBtn').addEventListener('click', function () {
        if (signaturePad) signaturePad.clear();
        document.getElementById('sigEmptyMsg').style.display = 'none';
    });

    // Valider avec signature
    document.getElementById('sigValidateBtn').addEventListener('click', function () {
        if (! signaturePad || signaturePad.isEmpty()) {
            document.getElementById('sigEmptyMsg').style.display = 'inline';
            return;
        }
        document.getElementById('sigEmptyMsg').style.display = 'none';
        document.getElementById('clientSignatureData').value = signaturePad.toDataURL('image/png');
        bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
        pendingSubmit = false;
        form.submit();
    });

    // Passer sans signature
    document.getElementById('sigSkipBtn').addEventListener('click', function () {
        document.getElementById('clientSignatureData').value = '';
        bootstrap.Modal.getInstance(document.getElementById('signatureModal')).hide();
        pendingSubmit = false;
        form.submit();
    });

})();
</script>


