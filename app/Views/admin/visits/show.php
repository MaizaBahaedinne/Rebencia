<?php
$perms   = session()->get('permissions') ?? [];
$sMeta   = $statusLabels[$visit['status']] ?? ['label' => $visit['status'], 'color' => 'secondary', 'icon' => 'bi-circle'];
$fbMeta  = isset($visit['feedback']) && $visit['feedback'] ? ($feedbackLabels[$visit['feedback']] ?? null) : null;

// WhatsApp
$cleanPhone   = preg_replace('/[^0-9]/', '', $visit['client_phone'] ?? '');
$visitDateFmt = date('d/m/Y', strtotime($visit['visit_date']));
$visitTimeFmt = substr($visit['visit_time'], 0, 5);

$waMsgConfirm  = urlencode('Bonjour ' . ($visit['first_name'] ?? '') . ', nous confirmons votre visite du bien « ' . ($visit['property_title'] ?? '') . ' » le ' . $visitDateFmt . ' à ' . $visitTimeFmt . '. Pour toute question, contactez-nous. Cordialement, Rebencia.');
$waMsgReminder = urlencode('Rappel : votre visite du bien « ' . ($visit['property_title'] ?? '') . ' » est prévue le ' . $visitDateFmt . ' à ' . $visitTimeFmt . '. Cordialement, Rebencia.');
$waConfirmLink  = 'https://wa.me/' . $cleanPhone . '?text=' . $waMsgConfirm;
$waReminderLink = 'https://wa.me/' . $cleanPhone . '?text=' . $waMsgReminder;
?>

<!-- ── EN-TÊTE ───────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <a href="<?= base_url('admin/visits') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-calendar2-event me-2 text-primary"></i>
            Visite #<?= $visit['id'] ?>
        </h4>
        <p class="text-muted small mb-0">
            <?= $visitDateFmt ?> à <?= $visitTimeFmt ?>
            — <?= $visit['duration'] ?> min
        </p>
    </div>
    <div class="ms-auto d-flex gap-2 flex-wrap">
        <!-- WhatsApp notifications -->
        <?php if ($cleanPhone): ?>
        <a href="<?= $waConfirmLink ?>" target="_blank"
           class="btn btn-sm btn-outline-success" title="Envoyer confirmation WhatsApp">
            <i class="bi bi-whatsapp me-1"></i>Confirmer
        </a>
        <a href="<?= $waReminderLink ?>" target="_blank"
           class="btn btn-sm btn-outline-success" title="Envoyer rappel WhatsApp">
            <i class="bi bi-whatsapp me-1"></i>Rappel
        </a>
        <?php endif; ?>
        <?php if (in_array('visits.edit', $perms)): ?>
        <a href="<?= base_url('admin/visits/' . $visit['id'] . '/edit') ?>"
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">

    <!-- ── Détails visite ─────────────────────────────────────────── -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-calendar-event me-1"></i> Détails de la visite</span>
                <span class="badge text-bg-<?= $sMeta['color'] ?>">
                    <i class="bi <?= $sMeta['icon'] ?> me-1"></i><?= $sMeta['label'] ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Date</p>
                        <p class="fw-semibold mb-0"><?= $visitDateFmt ?></p>
                    </div>
                    <div class="col-sm-3">
                        <p class="text-muted small mb-1">Heure</p>
                        <p class="fw-semibold mb-0"><?= $visitTimeFmt ?></p>
                    </div>
                    <div class="col-sm-3">
                        <p class="text-muted small mb-1">Durée</p>
                        <p class="fw-semibold mb-0"><?= $visit['duration'] ?> min</p>
                    </div>

                    <?php if (! empty($visit['notes'])): ?>
                    <div class="col-12">
                        <p class="text-muted small mb-1">Notes internes</p>
                        <p class="mb-0 bg-light rounded p-2 small"><?= nl2br(esc($visit['notes'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Changer le statut -->
            <?php if (in_array('visits.edit', $perms)): ?>
            <div class="card-footer bg-transparent">
                <p class="small text-muted mb-2 fw-semibold">Changer le statut :</p>
                <div id="statusButtons" class="d-flex gap-2 flex-wrap">
                    <?php foreach ($statusLabels as $sKey => $sm): ?>
                    <?php if ($sKey !== $visit['status']): ?>
                    <button class="btn btn-sm btn-outline-<?= $sm['color'] ?>"
                            onclick="updateStatus(<?= $visit['id'] ?>, '<?= $sKey ?>')">
                        <i class="bi <?= $sm['icon'] ?> me-1"></i><?= $sm['label'] ?>
                    </button>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div id="statusMsg" class="mt-2 small"></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Feedback post-visite -->
        <?php if ($visit['status'] === 'effectuee'): ?>

        <!-- Signature client -->
        <?php if (! empty($visit['client_signature'])): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-pen me-1 text-success"></i> Signature du client
                </span>
                <span class="badge text-bg-success">
                    <i class="bi bi-check2-circle me-1"></i>Signé
                </span>
            </div>
            <div class="card-body text-center">
                <img src="<?= esc($visit['client_signature']) ?>"
                     alt="Signature client"
                     class="img-fluid border rounded"
                     style="max-height:160px; background:#fff;">
                <?php if (! empty($visit['signed_at'])): ?>
                <p class="text-muted small mt-2 mb-0">
                    <i class="bi bi-clock me-1"></i>
                    Signé le <?= date('d/m/Y à H:i', strtotime($visit['signed_at'])) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Feedback post-visite -->
        <div class="card shadow-sm mb-4 <?= ! $fbMeta ? 'border-warning border-2' : '' ?>">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-chat-square-quote me-1 text-warning"></i>
                    Feedback post-visite
                </span>
                <?php if ($fbMeta): ?>
                <span class="badge bg-<?= $fbMeta['color'] ?>-subtle text-<?= $fbMeta['color'] ?> border border-<?= $fbMeta['color'] ?>-subtle">
                    <?= $fbMeta['label'] ?>
                </span>
                <?php else: ?>
                <span class="badge bg-warning text-dark">À remplir</span>
                <?php endif; ?>
            </div>

            <?php if ($fbMeta): ?>
            <!-- Feedback déjà enregistré -->
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-4 text-muted fw-normal">Résultat</dt>
                    <dd class="col-8 fw-semibold"><?= $fbMeta['label'] ?></dd>
                    <?php if (! empty($visit['feedback_notes'])): ?>
                    <dt class="col-4 text-muted fw-normal">Notes</dt>
                    <dd class="col-8"><?= nl2br(esc($visit['feedback_notes'])) ?></dd>
                    <?php endif; ?>
                </dl>
                <?php if (in_array('visits.edit', $perms)): ?>
                <button class="btn btn-sm btn-outline-secondary mt-2" data-bs-toggle="collapse" data-bs-target="#feedbackForm">
                    <i class="bi bi-pencil me-1"></i>Modifier le feedback
                </button>
                <?php endif; ?>
            </div>
            <div id="feedbackForm" class="collapse">
            <?php else: ?>
            <div id="feedbackForm">
            <?php endif; ?>

                <?php if (in_array('visits.edit', $perms)): ?>
                <form method="POST" action="<?= base_url('admin/visits/' . $visit['id'] . '/feedback') ?>">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <label class="form-label small fw-semibold">
                            Résultat de la visite <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-3 mb-3">
                            <?php foreach ($feedbackLabels as $fbKey => $fm): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="feedback"
                                       id="fb_<?= $fbKey ?>" value="<?= $fbKey ?>"
                                       <?= ($visit['feedback'] ?? '') === $fbKey ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="fb_<?= $fbKey ?>">
                                    <span class="badge bg-<?= $fm['color'] ?>-subtle text-<?= $fm['color'] ?> border border-<?= $fm['color'] ?>-subtle">
                                        <?= $fm['label'] ?>
                                    </span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <label class="form-label small fw-semibold">Notes complémentaires</label>
                        <textarea name="feedback_notes" class="form-control form-control-sm" rows="3"
                                  placeholder="Remarques sur la visite…"><?= esc($visit['feedback_notes'] ?? '') ?></textarea>
                    </div>
                    <div class="card-footer bg-transparent">
                        <button class="btn btn-sm btn-warning">
                            <i class="bi bi-floppy me-1"></i>Enregistrer le feedback
                        </button>
                    </div>
                </form>
                <?php endif; ?>

            </div>
        </div>
        <?php endif; ?>
    </div><!-- /col-md-8 -->

    <!-- ── SIDEBAR ────────────────────────────────────────────────── -->
    <div class="col-md-4">

        <!-- Client -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold small">
                <i class="bi bi-person me-1 text-primary"></i> Client
            </div>
            <div class="card-body small">
                <p class="fw-bold fs-6 mb-1">
                    <?= esc($visit['first_name'] . ' ' . $visit['last_name']) ?>
                </p>
                <?php if (! empty($visit['client_phone'])): ?>
                <p class="mb-1">
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $visit['client_phone']) ?>"
                       target="_blank" class="text-success text-decoration-none">
                        <i class="bi bi-whatsapp me-1"></i><?= esc($visit['client_phone']) ?>
                    </a>
                </p>
                <?php endif; ?>
                <?php if (! empty($visit['client_email'])): ?>
                <p class="mb-2">
                    <a href="mailto:<?= esc($visit['client_email']) ?>"
                       class="text-decoration-none text-muted">
                        <i class="bi bi-envelope me-1"></i><?= esc($visit['client_email']) ?>
                    </a>
                </p>
                <?php endif; ?>
                <?php if (! empty($visit['client_status'])): ?>
                <?php $cStatus = \App\Models\ClientModel::STATUS_LABELS[$visit['client_status']] ?? null; ?>
                <?php if ($cStatus): ?>
                <span class="badge bg-<?= $cStatus['color'] ?>-subtle text-<?= $cStatus['color'] ?> border border-<?= $cStatus['color'] ?>-subtle">
                    <?= $cStatus['label'] ?>
                </span>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= base_url('admin/clients/' . $visit['client_id']) ?>"
                   class="btn btn-sm btn-light w-100">
                    <i class="bi bi-arrow-right me-1"></i>Fiche client
                </a>
            </div>
        </div>

        <!-- Bien -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold small">
                <i class="bi bi-house me-1 text-success"></i> Bien immobilier
            </div>
            <div class="card-body small">
                <p class="fw-bold mb-1"><?= esc($visit['property_title']) ?></p>
                <?php if (! empty($visit['property_ref'])): ?>
                <p class="mb-1 text-muted">Réf. <code><?= esc($visit['property_ref']) ?></code></p>
                <?php endif; ?>
                <?php if (! empty($visit['property_city'])): ?>
                <p class="mb-1"><i class="bi bi-geo-alt me-1 text-muted"></i><?= esc($visit['property_city']) ?></p>
                <?php endif; ?>
                <?php if (! empty($visit['property_type'])): ?>
                <p class="mb-0 text-muted"><?= esc($visit['property_type']) ?></p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= base_url('admin/properties/' . $visit['property_id']) ?>"
                   class="btn btn-sm btn-light w-100">
                    <i class="bi bi-arrow-right me-1"></i>Fiche du bien
                </a>
            </div>
        </div>

        <!-- Agent -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold small">
                <i class="bi bi-person-badge me-1 text-warning"></i> Agent
            </div>
            <div class="card-body small">
                <p class="fw-bold mb-1"><?= esc($visit['agent_first'] . ' ' . $visit['agent_last']) ?></p>
                <?php if (! empty($visit['agent_phone'])): ?>
                <p class="mb-1 text-muted">
                    <i class="bi bi-telephone me-1"></i><?= esc($visit['agent_phone']) ?>
                </p>
                <?php endif; ?>
                <?php if (! empty($visit['agent_email'])): ?>
                <p class="mb-0">
                    <a href="mailto:<?= esc($visit['agent_email']) ?>" class="text-muted text-decoration-none">
                        <i class="bi bi-envelope me-1"></i><?= esc($visit['agent_email']) ?>
                    </a>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Danger zone -->
        <?php if (in_array('visits.delete', $perms)): ?>
        <div class="card shadow-sm border-danger-subtle">
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/visits/' . $visit['id'] . '/delete') ?>"
                      onsubmit="return confirm('Supprimer définitivement cette visite ?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i>Supprimer la visite
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /col-md-4 -->

</div><!-- /row -->

<!-- ── MODAL SIGNATURE (changement statut → effectuée) ────────────────── -->
<div class="modal fade" id="signatureStatusModal" data-bs-backdrop="static" tabindex="-1"
     aria-labelledby="signatureStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signatureStatusModalLabel">
                    <i class="bi bi-pen me-2 text-primary"></i>Signature du client
                </h5>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted small mb-3">
                    Faites signer le client sur l'écran tactile pour confirmer la visite effectuée.
                </p>
                <div class="border rounded bg-white" style="touch-action:none; cursor:crosshair;">
                    <canvas id="sigStatusCanvas" style="width:100%; display:block; height:220px;"></canvas>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="sigStatusClearBtn">
                        <i class="bi bi-eraser me-1"></i>Effacer
                    </button>
                    <span class="text-danger small align-self-center" id="sigStatusEmptyMsg" style="display:none;">
                        Veuillez signer avant de valider.
                    </span>
                </div>
            </div>
            <div class="modal-footer flex-column gap-2">
                <button type="button" class="btn btn-success w-100" id="sigStatusValidateBtn">
                    <i class="bi bi-check2-circle me-1"></i>Valider la signature
                </button>
                <button type="button" class="btn btn-link text-muted small w-100" id="sigStatusSkipBtn">
                    Marquer effectuée sans signature
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
    const CSRF_NAME = '<?= csrf_token() ?>';
    let   csrfHash  = '<?= csrf_hash() ?>';

    var signaturePad    = null;
    var pendingVisitId  = null;
    var pendingStatus   = null;

    // ── Init canvas à la résolution DPR ──────────────────────────────
    function initSignaturePad() {
        var canvas = document.getElementById('sigStatusCanvas');
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

    document.getElementById('signatureStatusModal')
        .addEventListener('shown.bs.modal', function () { initSignaturePad(); });

    document.getElementById('sigStatusClearBtn').addEventListener('click', function () {
        if (signaturePad) signaturePad.clear();
        document.getElementById('sigStatusEmptyMsg').style.display = 'none';
    });

    document.getElementById('sigStatusValidateBtn').addEventListener('click', function () {
        if (! signaturePad || signaturePad.isEmpty()) {
            document.getElementById('sigStatusEmptyMsg').style.display = 'inline';
            return;
        }
        document.getElementById('sigStatusEmptyMsg').style.display = 'none';
        var sigData = signaturePad.toDataURL('image/png');
        bootstrap.Modal.getInstance(document.getElementById('signatureStatusModal')).hide();
        doUpdateStatus(pendingVisitId, pendingStatus, sigData);
    });

    document.getElementById('sigStatusSkipBtn').addEventListener('click', function () {
        bootstrap.Modal.getInstance(document.getElementById('signatureStatusModal')).hide();
        doUpdateStatus(pendingVisitId, pendingStatus, '');
    });

    // ── Point d'entrée appelé par les boutons de statut ──────────────
    window.updateStatus = function (visitId, newStatus) {
        if (newStatus === 'effectuee') {
            pendingVisitId = visitId;
            pendingStatus  = newStatus;
            new bootstrap.Modal(document.getElementById('signatureStatusModal')).show();
            return;
        }

        const label = document.querySelector('[onclick*="' + newStatus + '"]')?.textContent.trim() || newStatus;
        if (! confirm('Passer la visite au statut « ' + label + ' » ?')) return;
        doUpdateStatus(visitId, newStatus, '');
    };

    function doUpdateStatus(visitId, newStatus, sigData) {
        var btn = document.querySelector(
            '[onclick*="updateStatus(' + visitId + ', \'' + newStatus + '\')"]'
        );
        if (btn) btn.disabled = true;

        var body = CSRF_NAME + '=' + encodeURIComponent(csrfHash)
                 + '&status=' + encodeURIComponent(newStatus);
        if (sigData) {
            body += '&client_signature=' + encodeURIComponent(sigData);
        }

        fetch(BASE_URL + 'admin/visits/' + visitId + '/status', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    body,
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) {
                document.getElementById('statusMsg').innerHTML =
                    '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Statut mis à jour. Rechargement…</span>';
                setTimeout(function () { window.location.reload(); }, 800);
            } else {
                alert('Erreur : ' + (data.error || 'inconnue'));
                if (btn) btn.disabled = false;
            }
        })
        .catch(function () {
            alert('Erreur réseau');
            if (btn) btn.disabled = false;
        });
    }

})();
</script>

