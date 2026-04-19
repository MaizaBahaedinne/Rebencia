<?php
$perms = session()->get('permissions') ?? [];
$typeMeta = \App\Models\ZoneModel::TYPE_META;
?>

<!-- EN-TÊTE -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-upload me-2 text-primary"></i>Import / Purge des zones
        </h4>
        <p class="text-muted mb-0">Importer un fichier JSON ou purger les données existantes</p>
    </div>
    <a href="<?= base_url('admin/zones') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left me-1"></i>Retour aux zones
    </a>
</div>

<!-- COMPTEURS ACTUELS -->
<div class="row g-3 mb-4">
    <?php foreach ($typeMeta as $typeKey => $m): ?>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 p-2 bg-<?= $m['color'] ?> bg-opacity-10 flex-shrink-0">
                    <i class="bi <?= $m['icon'] ?> fs-4 text-<?= $m['color'] ?>"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1"><?= $counts[$typeKey] ?? 0 ?></div>
                    <div class="text-muted small"><?= esc($m['label']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">

    <!-- ── IMPORT ────────────────────────────────────────────── -->
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary bg-opacity-10 border-bottom">
                <h6 class="mb-0 fw-semibold text-primary">
                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Importer un fichier JSON
                </h6>
            </div>
            <div class="card-body">

                <form method="POST"
                      action="<?= base_url('admin/zones/import') ?>"
                      enctype="multipart/form-data"
                      id="importForm">
                    <?= csrf_field() ?>

                    <!-- Nom du pays -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="pays_name_import">
                            Nom du pays <span class="text-muted fw-normal">(par défaut : Tunisie)</span>
                        </label>
                        <input type="text" class="form-control" id="pays_name_import"
                               name="pays_name" value="Tunisie" maxlength="100">
                    </div>

                    <!-- Zone de dépôt + sélection fichier -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fichier JSON</label>
                        <div id="dropZone"
                             class="border border-2 border-dashed rounded-3 p-4 text-center position-relative"
                             style="cursor:pointer; min-height:130px; border-color:#6c757d !important; transition:.2s">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary d-block mb-2"></i>
                            <p class="mb-1 fw-semibold">Glissez-déposez votre fichier ici</p>
                            <p class="text-muted small mb-2">ou cliquez pour sélectionner</p>
                            <p class="text-muted small mb-0" id="fileLabel">Aucun fichier sélectionné</p>
                            <input type="file" name="json_file" id="json_file"
                                   accept=".json,application/json"
                                   class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                   style="cursor:pointer" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                        <i class="bi bi-upload me-2"></i>Lancer l'import
                    </button>
                </form>

                <!-- Formats supportés -->
                <div class="mt-4">
                    <p class="small fw-semibold text-muted mb-2">
                        <i class="bi bi-info-circle me-1"></i>Formats JSON acceptés
                    </p>
                    <ul class="nav nav-tabs nav-tabs-sm" id="formatTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active py-1 px-2 small" data-bs-toggle="tab" data-bs-target="#fmtA">Format A</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-1 px-2 small" data-bs-toggle="tab" data-bs-target="#fmtB">Format B</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-1 px-2 small" data-bs-toggle="tab" data-bs-target="#fmtC">Format C</button>
                        </li>
                    </ul>
                    <div class="tab-content border border-top-0 rounded-bottom p-3 bg-light">
                        <div class="tab-pane fade show active" id="fmtA">
                            <small class="text-muted d-block mb-1">Objet indexé par gouvernorat — tableau de <code>[délégation, localité, cp, cp_ville]</code></small>
                            <pre class="mb-0 small"><code>{
  "Ariana": [
    ["Ariana Ville", "Cite El Intissar 1", "2091", "2058"],
    ["Sidi Thabet",  "Borj El Khoukha",    "2032", "2032"]
  ]
}</code></pre>
                        </div>
                        <div class="tab-pane fade" id="fmtB">
                            <small class="text-muted d-block mb-1">Tableau plat d'objets avec clés explicites. Champ <code>cp</code> = code postal.</small>
                            <pre class="mb-0 small"><code>[
  { "gouvernorat": "Ariana",
    "delegation":  "Ariana Ville",
    "localite":    "Cite El Intissar 1",
    "cp":          "2091" }
]</code></pre>
                        </div>
                        <div class="tab-pane fade" id="fmtC">
                            <small class="text-muted d-block mb-1">Objet hiérarchique avec clé <code>gouvernorats</code></small>
                            <pre class="mb-0 small"><code>{
  "pays": "Tunisie",
  "gouvernorats": [
    { "nom": "Ariana",
      "delegations": [
        { "nom": "Ariana Ville", "cp": "2058",
          "localites": [
            { "nom": "Cite El Intissar 1", "cp": "2091" }
          ]}
      ]}
  ]
}</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ── PURGE ──────────────────────────────────────────────── -->
    <?php if (in_array('zones.delete', $perms)): ?>
    <div class="col-lg-5">
        <div class="card shadow-sm border-danger-subtle h-100">
            <div class="card-header bg-danger bg-opacity-10 border-bottom border-danger-subtle">
                <h6 class="mb-0 fw-semibold text-danger">
                    <i class="bi bi-trash3 me-2"></i>Purger les zones d'un pays
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Supprime ou archive toutes les zones rattachées au pays sélectionné
                    (régions, villes et quartiers inclus).
                </p>

                <form method="POST"
                      action="<?= base_url('admin/zones/purge') ?>"
                      id="purgeForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="pays_name_purge">Nom du pays</label>
                        <input type="text" class="form-control" id="pays_name_purge"
                               name="pays_name" value="Tunisie" maxlength="100" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mode de suppression</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode"
                                       id="modeSoft" value="soft" checked>
                                <label class="form-check-label" for="modeSoft">
                                    <span class="fw-semibold">Archiver</span>
                                    <small class="text-muted d-block">Soft-delete — les données restent en base, masquées</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode"
                                       id="modeHard" value="hard">
                                <label class="form-check-label" for="modeHard">
                                    <span class="fw-semibold text-danger">Supprimer définitivement</span>
                                    <small class="text-muted d-block">Suppression physique — irréversible</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-danger w-100" onclick="confirmPurge()">
                        <i class="bi bi-trash3-fill me-2"></i>Purger
                    </button>
                </form>

                <!-- Avertissement hard delete -->
                <div class="alert alert-warning mt-3 mb-0 py-2 small d-flex gap-2">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                    <span>La suppression définitive efface toutes les zones du pays sans possibilité de récupération.</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /row -->

<script>
// ── Drag & Drop + feedback fichier ──────────────────────────────
(function () {
    const drop   = document.getElementById('dropZone');
    const input  = document.getElementById('json_file');
    const label  = document.getElementById('fileLabel');
    const submit = document.getElementById('submitBtn');

    function setFile(name) {
        label.textContent = name;
        drop.classList.add('border-primary');
        drop.style.backgroundColor = '#f0f7ff';
    }

    input.addEventListener('change', () => {
        if (input.files[0]) setFile(input.files[0].name);
    });

    drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('border-primary'); });
    drop.addEventListener('dragleave', () => drop.classList.remove('border-primary'));
    drop.addEventListener('drop', e => {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            setFile(file.name);
        }
    });

    // Spinner sur submit
    document.getElementById('importForm').addEventListener('submit', function () {
        submit.disabled = true;
        submit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Import en cours…';
    });
})();

// ── Confirmation purge ───────────────────────────────────────────
function confirmPurge() {
    const pays = document.getElementById('pays_name_purge').value.trim();
    const hard = document.getElementById('modeHard').checked;
    const msg  = hard
        ? `⚠️ Suppression DÉFINITIVE de toutes les zones de « ${pays} ».\n\nCette opération est IRRÉVERSIBLE.\n\nConfirmer ?`
        : `Archiver (soft-delete) toutes les zones de « ${pays} » ?\n\nElles seront masquées mais resteront en base.`;

    if (confirm(msg)) {
        document.getElementById('purgeForm').submit();
    }
}
</script>
