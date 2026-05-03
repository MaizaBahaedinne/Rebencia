<?php $perms = session()->get('permissions') ?? []; ?>

<!-- BREADCRUMB -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/teams') ?>">Équipes</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/teams/' . $team['id']) ?>"><?= esc($team['name']) ?></a></li>
        <li class="breadcrumb-item active">Organigramme</li>
    </ol>
</nav>

<!-- Flash -->
<?php if (session()->has('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= esc(session('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i><?= esc(session('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-diagram-3-fill text-primary me-2"></i>Organigramme — <?= esc($team['name']) ?>
        </h4>
        <p class="text-muted small mb-0"><?= count($members) ?> membre(s) · <?= count($levels) ?> niveau(x)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/teams/' . $team['id']) ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour à l'équipe
        </a>
    </div>
</div>

<!-- Alerte migration manquante -->
<?php if (! $managerColExists): ?>
<div class="alert alert-warning d-flex align-items-center gap-2">
    <i class="bi bi-wrench fs-5"></i>
    <div>
        <strong>Migration requise.</strong> La colonne <code>manager_id</code> n'existe pas encore.
        <a href="<?= base_url('migrate_manager.php?token=reb2026manager') ?>"
           class="btn btn-sm btn-warning ms-2" target="_blank">Exécuter la migration</a>
    </div>
</div>
<?php endif; ?>

<!-- Prompt setup si aucune relation définie -->
<?php if ($managerColExists && ! $hasRelations && count($members) > 1 && $canManage): ?>
<div class="alert alert-info d-flex align-items-start gap-2">
    <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <strong>Aucune relation de management définie.</strong><br>
        Cliquez sur <strong>Définir manager</strong> sous chaque membre pour construire l'arbre hiérarchique.
        Le manager sera placé au-dessus de ses subordonnés.
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     ORGANIGRAMME
═══════════════════════════════════════════ -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span class="fw-semibold">
            <i class="bi bi-diagram-3 me-2 text-primary"></i>Arbre hiérarchique
        </span>
        <!-- Contrôles Zoom -->
        <div class="d-flex align-items-center gap-1">
            <button class="btn btn-sm btn-outline-secondary org-zoom-btn" onclick="orgZoom(-0.15)" title="Réduire">
                <i class="bi bi-dash-lg"></i>
            </button>
            <span class="org-zoom-label" id="orgScaleLabel">100%</span>
            <button class="btn btn-sm btn-outline-secondary org-zoom-btn" onclick="orgZoom(0.15)" title="Agrandir">
                <i class="bi bi-plus-lg"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary ms-1" onclick="orgZoomReset()" title="Réinitialiser le zoom">
                <i class="bi bi-fullscreen-exit"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="org-wrap" id="orgWrap">
            <!-- Canvas scalable (SVG + nœuds) -->
            <div id="orgCanvas">
                <!-- SVG pour les lignes de connexion -->
                <svg id="orgSvg"></svg>

                <!-- Niveaux de l'arbre -->
                <div class="org-levels" id="orgLevels">
                <?php foreach ($levels as $depth => $levelNodes): ?>
                <div class="org-level" id="level-<?= $depth ?>">
                    <?php foreach ($levelNodes as $m):
                        $initials   = strtoupper(substr($m['first_name'], 0, 1) . substr($m['last_name'], 0, 1));
                        $statusMap  = ['active' => ['bg-success', 'Actif'], 'pending' => ['bg-warning text-dark', 'En attente'], 'suspended' => ['bg-danger', 'Suspendu']];
                        [$sBadge, $sLabel] = $statusMap[$m['status']] ?? ['bg-secondary', $m['status']];
                        $isRoot = ($depth === 0);
                    ?>
                    <div class="org-node <?= $isRoot ? 'org-node--root' : '' ?>"
                         data-node-id="<?= $m['id'] ?>">

                        <!-- Avatar -->
                        <div class="org-avatar" style="border-color: <?= esc($m['role_color']) ?>;">
                            <?php if ($m['avatar']): ?>
                            <img src="<?= base_url('uploads/' . esc($m['avatar'])) ?>"
                                 alt="<?= esc($initials) ?>">
                            <?php else: ?>
                            <span style="color:<?= esc($m['role_color']) ?>;"><?= esc($initials) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Nom -->
                        <div class="org-name"><?= esc($m['first_name'] . ' ' . $m['last_name']) ?></div>

                        <!-- Rôle -->
                        <div class="org-role">
                            <span class="badge rounded-pill"
                                  style="background:<?= esc($m['role_color']) ?>22;color:<?= esc($m['role_color']) ?>;border:1px solid <?= esc($m['role_color']) ?>55;font-size:.63rem;">
                                <?= esc($m['role_label']) ?>
                            </span>
                        </div>

                        <!-- Statut -->
                        <div class="mt-1">
                            <span class="badge <?= $sBadge ?>" style="font-size:.6rem;"><?= $sLabel ?></span>
                        </div>

                        <!-- Bouton Définir manager -->
                        <?php if ($canManage && $managerColExists): ?>
                        <button class="org-mgr-btn"
                                onclick="openManagerModal(<?= $m['id'] ?>, '<?= esc(addslashes($m['first_name'] . ' ' . $m['last_name'])) ?>', <?= (int)($m['manager_id'] ?? 0) ?>)">
                            <i class="bi bi-diagram-3 me-1"></i>Définir manager
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
                </div><!-- /org-levels -->
            </div><!-- /orgCanvas -->
        </div><!-- /org-wrap -->
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL DÉFINIR MANAGER
═══════════════════════════════════════════ -->
<?php if ($canManage && $managerColExists): ?>
<div class="modal fade" id="managerModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title fw-semibold">
                    <i class="bi bi-diagram-3 me-2 text-primary"></i>Définir le manager
                </h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="managerForm" action="<?= base_url('admin/teams/' . $team['id'] . '/set-manager') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="managerUserId">
                <div class="modal-body">
                    <p class="text-muted small mb-2">
                        Manager de : <strong id="managerUserName"></strong>
                    </p>
                    <select name="manager_id" id="managerSelect" class="form-select form-select-sm">
                        <option value="0">— Aucun manager (nœud racine) —</option>
                        <?php foreach ($members as $m): ?>
                        <option value="<?= $m['id'] ?>"
                                data-exclude-for="<?= $m['id'] ?>">
                            <?= esc($m['first_name'] . ' ' . $m['last_name']) ?>
                            · <?= esc($m['role_label']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text mt-1">Le membre sera placé sous son manager dans l'arbre.</div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check2 me-1"></i>Appliquer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     STYLES
═══════════════════════════════════════════ -->
<style>
/* Conteneur principal */
.org-wrap {
    overflow: auto;
    padding: 2.5rem 2rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #f0f2f8 100%);
    border-radius: 0 0 .875rem .875rem;
    position: relative;
    min-height: 240px;
}

/* Canvas scalable (contient SVG + nœuds) */
#orgCanvas {
    position: relative;
    display: inline-block;
    min-width: 100%;
    transform-origin: top center;
    transition: transform .22s ease;
}

/* SVG overlay */
#orgSvg {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    pointer-events: none;
    z-index: 0;
    overflow: visible;
}

/* Niveaux */
.org-levels {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 64px;
    min-width: max-content;
    padding-bottom: .5rem;
}

.org-level {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: nowrap;
}

/* Zoom label & boutons */
.org-zoom-label {
    font-size: .8rem;
    font-weight: 600;
    color: #6b7280;
    min-width: 40px;
    text-align: center;
}
.org-zoom-btn { padding: .18rem .45rem; font-size: .8rem; }

/* Nœud */
.org-node {
    width: 148px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    padding: 14px 10px 10px;
    text-align: center;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
    transition: transform .15s, box-shadow .15s;
    border: 2px solid transparent;
}
.org-node:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 24px rgba(108,99,255,.18);
    border-color: #6c63ff44;
}
.org-node--root {
    border-color: #6c63ff55;
    box-shadow: 0 4px 20px rgba(108,99,255,.2);
}

/* Avatar */
.org-avatar {
    width: 52px; height: 52px;
    border-radius: 50%;
    margin: 0 auto 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .88rem; font-weight: 700;
    background: #f0f2f8;
    border: 2px solid currentColor;
    overflow: hidden;
    flex-shrink: 0;
}
.org-avatar img { width: 100%; height: 100%; object-fit: cover; }

/* Textes */
.org-name {
    font-size: .76rem;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 5px;
    color: #1e1b4b;
    word-break: break-word;
}
.org-role { min-height: 20px; }

/* Bouton manager */
.org-mgr-btn {
    display: block;
    width: 100%;
    margin-top: 8px;
    padding: .18rem .4rem;
    font-size: .67rem;
    background: none;
    border: 1px dashed #6c63ff88;
    border-radius: 6px;
    color: #6c63ff;
    cursor: pointer;
    transition: background .12s, color .12s;
}
.org-mgr-btn:hover {
    background: #6c63ff11;
    border-style: solid;
}
</style>

<!-- ══════════════════════════════════════════
     JAVASCRIPT — tracé des lignes SVG
═══════════════════════════════════════════ -->
<script>
const ORG_EDGES = <?= json_encode(array_values($edges)) ?>;
let orgScale = 1;

// ── Zoom ──────────────────────────────────────────
function orgZoom(delta) {
    orgScale = Math.max(0.25, Math.min(2.0, orgScale + delta));
    document.getElementById('orgCanvas').style.transform = `scale(${orgScale})`;
    document.getElementById('orgScaleLabel').textContent = Math.round(orgScale * 100) + '%';
    // Ajuster la hauteur du conteneur pour éviter le chevauchement
    const canvas = document.getElementById('orgCanvas');
    document.getElementById('orgWrap').style.minHeight = (canvas.offsetHeight * orgScale + 80) + 'px';
}

function orgZoomReset() {
    orgScale = 1;
    document.getElementById('orgCanvas').style.transform = 'scale(1)';
    document.getElementById('orgScaleLabel').textContent = '100%';
    document.getElementById('orgWrap').style.minHeight = '';
}

// ── Dessin des lignes SVG ─────────────────────────
function drawOrgChart() {
    const canvas = document.getElementById('orgCanvas');
    const svg    = document.getElementById('orgSvg');
    if (!canvas || !svg || ORG_EDGES.length === 0) return;

    // Taille SVG = taille réelle du canvas (non-scalée)
    const W = canvas.offsetWidth;
    const H = canvas.offsetHeight;
    svg.setAttribute('width',  W);
    svg.setAttribute('height', H);
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.innerHTML = '';

    // Gradient
    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    const grad = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
    grad.setAttribute('id', 'orgLineGrad');
    grad.setAttribute('x1', '0'); grad.setAttribute('y1', '0');
    grad.setAttribute('x2', '0'); grad.setAttribute('y2', '1');
    [['0%','#6c63ff'],['100%','#a78bfa']].forEach(([offset, color]) => {
        const stop = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop.setAttribute('offset', offset);
        stop.setAttribute('stop-color', color);
        grad.appendChild(stop);
    });
    defs.appendChild(grad);
    svg.appendChild(defs);

    const canvasRect = canvas.getBoundingClientRect();

    ORG_EDGES.forEach(([parentId, childId]) => {
        const pEl = document.querySelector(`[data-node-id="${parentId}"]`);
        const cEl = document.querySelector(`[data-node-id="${childId}"]`);
        if (!pEl || !cEl) return;

        const pr = pEl.getBoundingClientRect();
        const cr = cEl.getBoundingClientRect();

        // Coordonnées dans l'espace SVG (non-scalé)
        const x1 = (pr.left + pr.width  / 2 - canvasRect.left) / orgScale;
        const y1 = (pr.bottom               - canvasRect.top)  / orgScale;
        const x2 = (cr.left + cr.width  / 2 - canvasRect.left) / orgScale;
        const y2 = (cr.top                  - canvasRect.top)  / orgScale;
        const midY = (y1 + y2) / 2;

        // Bezier
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', `M ${x1} ${y1} C ${x1} ${midY} ${x2} ${midY} ${x2} ${y2}`);
        path.setAttribute('stroke', 'url(#orgLineGrad)');
        path.setAttribute('stroke-width', '2.5');
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke-linecap', 'round');
        svg.appendChild(path);

        // Dot enfant
        const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        dot.setAttribute('cx', x2); dot.setAttribute('cy', y2);
        dot.setAttribute('r', '4'); dot.setAttribute('fill', '#a78bfa');
        svg.appendChild(dot);

        // Dot parent
        const dotP = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        dotP.setAttribute('cx', x1); dotP.setAttribute('cy', y1);
        dotP.setAttribute('r', '3.5'); dotP.setAttribute('fill', '#6c63ff');
        svg.appendChild(dotP);
    });
}

// Double rAF pour garantir le layout complet avant de dessiner
window.addEventListener('load', () => {
    requestAnimationFrame(() => requestAnimationFrame(drawOrgChart));
});
window.addEventListener('resize', drawOrgChart);

// ── Modal définir manager ──────────────────────────
function openManagerModal(userId, userName, currentMgrId) {
    document.getElementById('managerUserId').value        = userId;
    document.getElementById('managerUserName').textContent = userName;

    const sel = document.getElementById('managerSelect');
    if (!sel) return;

    // Désactiver l'option correspondant à l'utilisateur lui-même
    Array.from(sel.options).forEach(opt => {
        opt.disabled = (parseInt(opt.value) === userId);
    });

    sel.value = currentMgrId || 0;
    new bootstrap.Modal(document.getElementById('managerModal')).show();
}
</script>
