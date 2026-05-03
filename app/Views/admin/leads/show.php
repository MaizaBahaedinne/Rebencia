
<?php
// ── Définitions pipeline ────────────────────────────────────────────────────
$pipelineSteps = [
    'new'         => ['Nouveau',       '#3b82f6', 'bi-circle-fill'],
    'contacted'   => ['Contacté',      '#06b6d4', 'bi-telephone-check-fill'],
    'interested'  => ['Intéressé',     '#f59e0b', 'bi-star-fill'],
    'visit_done'  => ['Visite faite',  '#8b5cf6', 'bi-house-check-fill'],
    'negotiating' => ['Négociation',   '#f97316', 'bi-chat-dots-fill'],
    'won'         => ['Conclu',        '#10b981', 'bi-trophy-fill'],
    'lost'        => ['Perdu',         '#ef4444', 'bi-x-circle-fill'],
];
$mainPipeline  = array_filter($pipelineSteps, fn($k) => $k !== 'lost', ARRAY_FILTER_USE_KEY);
$currentStatus = $lead['status'] ?? 'new';
$statusKeys    = array_keys($pipelineSteps);
$mainKeys      = array_keys($mainPipeline);
$currentIdx    = array_search($currentStatus, $statusKeys);
$isTerminal    = in_array($currentStatus, ['won', 'lost'], true);
$canEdit       = in_array('leads.edit', session()->get('permissions') ?? []);

// ── Dates de passage par statut (depuis l'historique) ──────────────────────
$stepDates = [];
// La date "new" = date de création du lead
$stepDates['new'] = date('d/m/Y', strtotime($lead['created_at']));
foreach ($statusHistory ?? [] as $h) {
    $key = $h['new_status'] ?? '';
    if ($key && ! isset($stepDates[$key])) {
        $stepDates[$key] = date('d/m/Y', strtotime($h['created_at']));
    }
}

// ── Mapping bouton → modal ou POST direct ──────────────────────────────────
$stepActions = [
    'contacted'   => ['modal',  '#modalContacte',  '#06b6d4'],
    'interested'  => ['modal',  '#modalInteresse', '#f59e0b'],
    'visit_done'  => ['direct', '',                '#8b5cf6'],
    'negotiating' => ['direct', '',                '#f97316'],
    'won'         => ['modal',  '#modalConclu',    '#10b981'],
];
?>

<?php
// ── Helpers traduction ───────────────────────────────────────────────────────
$txMap  = ['sale'=>'Vente','rent'=>'Location','sale_rent'=>'Vente & Location'];
$priMap = ['low'=>['Faible','#6b7280'],'medium'=>['Normale','#f59e0b'],'high'=>['Haute','#ef4444']];
$srcMap = ['website'=>'Site web','referral'=>'Recommandation','phone'=>'Téléphone','email'=>'Email','social'=>'Réseaux sociaux','agency'=>'Agence','other'=>'Autre'];
[$priLabel, $priHex] = $priMap[$lead['priority'] ?? 'medium'];
$initials = strtoupper(substr($lead['first_name'] ?? '?', 0, 1) . substr($lead['last_name'] ?? '?', 0, 1));
$aColors  = ['#6c63ff','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316'];
$avatarBg = $aColors[abs(crc32($lead['first_name'] . $lead['last_name'])) % count($aColors)];
$budgetMin = (float)($lead['budget_min'] ?? 0);
$budgetMax = (float)($lead['budget_max'] ?? 0);
if ($budgetMin > 0 && $budgetMax > 0)     $budgetStr = number_format($budgetMin,0,',',' ').' – '.number_format($budgetMax,0,',',' ').' TND';
elseif ($budgetMax > 0)                    $budgetStr = '≤ '.number_format($budgetMax,0,',',' ').' TND';
elseif ($budgetMin > 0)                    $budgetStr = '≥ '.number_format($budgetMin,0,',',' ').' TND';
else                                       $budgetStr = '—';
?>

<!-- ═══ Hero Card ══════════════════════════════════════════════════════════ -->
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#4f46e5 100%);padding:1.75rem 1.75rem 1.25rem;">
        <div class="d-flex align-items-start gap-4 flex-wrap">
            <!-- Avatar -->
            <div style="width:72px;height:72px;border-radius:50%;background:<?= $avatarBg ?>;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:white;flex-shrink:0;box-shadow:0 0 0 4px rgba(255,255,255,.2);">
                <?= $initials ?>
            </div>
            <!-- Info principale -->
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h3 class="mb-0 fw-bold text-white"><?= esc($lead['first_name'] . ' ' . $lead['last_name']) ?></h3>
                    <span class="rounded-pill px-2 py-1 fw-semibold" style="background:<?= $priHex ?>33;border:1.5px solid <?= $priHex ?>88;color:white;font-size:.72rem;"><?= $priLabel ?></span>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap" style="color:rgba(255,255,255,.7);font-size:.85rem;">
                    <span><i class="bi bi-hash me-1"></i>Lead #<?= $lead['id'] ?></span>
                    <span><i class="bi bi-calendar3 me-1"></i>Créé le <?= date('d/m/Y', strtotime($lead['created_at'])) ?></span>
                    <?php if (!empty($lead['phone'])): ?><span><i class="bi bi-telephone me-1"></i><?= esc($lead['phone']) ?></span><?php endif; ?>
                    <?php if (!empty($lead['email'])): ?><span><i class="bi bi-envelope me-1"></i><?= esc($lead['email']) ?></span><?php endif; ?>
                </div>
                <!-- KPIs -->
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <?php [$sLabel,$sHex,$sIcon] = $pipelineSteps[$currentStatus]; ?>
                    <span class="d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1 fw-semibold" style="background:<?= $sHex ?>;color:white;font-size:.8rem;"><i class="bi <?= $sIcon ?>"></i><?= $sLabel ?></span>
                    <?php if (!empty($lead['transaction_type'])): ?>
                    <span class="d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1" style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.9);font-size:.8rem;"><i class="bi bi-arrow-left-right me-1"></i><?= $txMap[$lead['transaction_type']] ?? esc($lead['transaction_type']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($lead['source'])): ?>
                    <span class="d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1" style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.9);font-size:.8rem;"><i class="bi bi-funnel me-1"></i><?= $srcMap[$lead['source']] ?? esc($lead['source']) ?></span>
                    <?php endif; ?>
                    <?php if ($budgetStr !== '—'): ?>
                    <span class="d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1" style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.9);font-size:.8rem;"><i class="bi bi-wallet2 me-1"></i><?= $budgetStr ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Actions -->
            <div class="d-flex flex-column gap-2 align-items-end">
                <?php if ($canEdit && ! $isTerminal): ?>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalPerdu"><i class="bi bi-x-circle me-1"></i>Perdu</button>
                <?php endif; ?>
                <?php if ($canEdit): ?>
                <a href="<?= site_url('admin/leads/' . $lead['id'] . '/edit') ?>" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:white;border:1px solid rgba(255,255,255,.3);"><i class="bi bi-pencil me-1"></i>Modifier</a>
                <?php endif; ?>
                <a href="<?= site_url('admin/leads') ?>" class="btn btn-sm" style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);border:1px solid rgba(255,255,255,.2);"><i class="bi bi-arrow-left me-1"></i>Retour</a>
            </div>
        </div>
    </div>
</div>

<?php foreach (['success','error','warning','info'] as $fType): ?>
<?php if (session()->has($fType)): ?>
<div class="alert alert-<?= $fType === 'error' ? 'danger' : $fType ?> alert-dismissible fade show">
    <i class="bi bi-<?= $fType === 'success' ? 'check-circle' : ($fType === 'error' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
    <?= session($fType) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php endforeach; ?>

<!-- ═══ Pipeline stepper ══════════════════════════════════════════════════════ -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body px-4 py-4">

        <!-- Ligne de progression en haut -->
        <?php
        // Calcule le % de progression pour la barre sous-jacente
        $totalSteps = count($mainKeys);
        $doneSteps  = 0;
        foreach ($mainKeys as $k) {
            $kidx = array_search($k, $statusKeys);
            if ($currentStatus !== 'lost' && $kidx <= $currentIdx) $doneSteps++;
        }
        $progressPct = $totalSteps > 1 ? round(($doneSteps - 1) / ($totalSteps - 1) * 100) : 0;
        if ($currentStatus === 'lost') $progressPct = 0;
        ?>

        <div class="position-relative" style="padding-bottom:0">
            <!-- Barre de fond grise -->
            <div class="position-absolute" style="top:20px;left:calc(50px / 2);right:calc(50px / 2);height:4px;background:#e5e7eb;z-index:0;border-radius:4px;"></div>
            <!-- Barre colorée remplie -->
            <div class="position-absolute" style="top:20px;left:calc(50px / 2);width:<?= $progressPct ?>%;height:4px;background:linear-gradient(90deg,#3b82f6,#10b981);z-index:1;border-radius:4px;transition:width .4s;"></div>

            <!-- Étapes -->
            <div class="d-flex justify-content-between position-relative" style="z-index:2">
                <?php foreach ($mainPipeline as $key => [$label, $hexColor, $icon]):
                    $kidx = array_search($key, $statusKeys);
                    if ($key === $currentStatus)                                $state = 'active';
                    elseif ($currentStatus !== 'lost' && $kidx < $currentIdx)  $state = 'done';
                    else                                                         $state = 'pending';

                    [$actionType, $modalTarget, $actionColor] = $stepActions[$key] ?? ['none', '', $hexColor];
                    $date = $stepDates[$key] ?? null;
                ?>
                <div class="d-flex flex-column align-items-center" style="width:50px;flex-shrink:0">

                    <?php if ($state === 'done'): ?>
                        <!-- Passé : cercle vert avec check -->
                        <div style="width:40px;height:40px;border-radius:50%;background:#10b981;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 3px #d1fae5;">
                            <i class="bi bi-check-lg text-white" style="font-size:1.1rem;"></i>
                        </div>

                    <?php elseif ($state === 'active'): ?>
                        <!-- Actuel : cercle coloré + anneau -->
                        <div style="width:40px;height:40px;border-radius:50%;background:<?= $hexColor ?>;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 4px <?= $hexColor ?>33;">
                            <i class="bi <?= $icon ?> text-white" style="font-size:1rem;"></i>
                        </div>

                    <?php elseif ($state === 'pending' && $canEdit && ! $isTerminal && $actionType !== 'none'): ?>
                        <!-- Suivant : bouton cliquable -->
                        <?php if ($actionType === 'modal'): ?>
                        <button type="button" data-bs-toggle="modal" data-bs-target="<?= $modalTarget ?>"
                            title="Passer à : <?= $label ?>"
                            style="width:40px;height:40px;border-radius:50%;border:2.5px dashed <?= $hexColor ?>;background:white;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .18s;"
                            onmouseover="this.style.background='<?= $hexColor ?>22'"
                            onmouseout="this.style.background='white'">
                            <i class="bi <?= $icon ?>" style="color:<?= $hexColor ?>;font-size:1rem;"></i>
                        </button>
                        <?php else: ?>
                        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>" class="d-inline m-0 p-0">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="<?= $key ?>">
                            <button type="submit" title="Passer à : <?= $label ?>"
                                style="width:40px;height:40px;border-radius:50%;border:2.5px dashed <?= $hexColor ?>;background:white;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .18s;"
                                onmouseover="this.style.background='<?= $hexColor ?>22'"
                                onmouseout="this.style.background='white'">
                                <i class="bi <?= $icon ?>" style="color:<?= $hexColor ?>;font-size:1rem;"></i>
                            </button>
                        </form>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Futur : cercle gris -->
                        <div style="width:40px;height:40px;border-radius:50%;border:2px solid #d1d5db;background:#f9fafb;display:flex;align-items:center;justify-content:center;">
                            <i class="bi <?= $icon ?>" style="color:#9ca3af;font-size:1rem;"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Label -->
                    <span class="mt-2 text-center lh-1" style="font-size:.68rem;font-weight:<?= $state === 'active' ? '700' : '500' ?>;color:<?= $state === 'active' ? $hexColor : ($state === 'done' ? '#10b981' : '#9ca3af') ?>;white-space:nowrap;">
                        <?= $label ?>
                    </span>

                    <!-- Date de passage -->
                    <?php if ($date): ?>
                    <span class="text-center mt-1" style="font-size:.62rem;color:#6b7280;white-space:nowrap;"><?= $date ?></span>
                    <?php else: ?>
                    <span style="font-size:.62rem;color:transparent;user-select:none;">–</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <?php if ($currentStatus === 'lost'): ?>
                <!-- Perdu : icône rouge à droite -->
                <div class="d-flex flex-column align-items-center" style="width:50px;flex-shrink:0">
                    <div style="width:40px;height:40px;border-radius:50%;background:#ef4444;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 4px #fee2e2;">
                        <i class="bi bi-x-lg text-white" style="font-size:1.1rem;"></i>
                    </div>
                    <span class="mt-2 text-center lh-1" style="font-size:.68rem;font-weight:700;color:#ef4444;white-space:nowrap;">Perdu</span>
                    <?php if (isset($stepDates['lost'])): ?>
                    <span class="text-center mt-1" style="font-size:.62rem;color:#6b7280;"><?= $stepDates['lost'] ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Légende statut actuel -->
        <div class="mt-4 pt-2 border-top d-flex align-items-center gap-3 flex-wrap">
            <?php
            [$curLabel, $curHex, $curIcon] = $pipelineSteps[$currentStatus];
            ?>
            <span style="font-size:.8rem;color:#6b7280;">Statut actuel :</span>
            <span class="d-flex align-items-center gap-1 fw-semibold" style="color:<?= $curHex ?>;font-size:.9rem;">
                <i class="bi <?= $curIcon ?>"></i><?= $curHex === '#f59e0b' ? '<span style="color:#92400e">' . $curLabel . '</span>' : $curLabel ?>
            </span>
            <?php if (! $isTerminal && $canEdit): ?>
            <span class="text-muted" style="font-size:.78rem;">
                <i class="bi bi-arrow-right-circle me-1"></i>
                Cliquez sur le prochain cercle pointillé pour avancer
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ═══ Main row ═══════════════════════════════════════════════════════════ -->
<div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;">
    <!-- Colonne gauche -->
    <div>

        <!-- ── Contact + Projet (côte-à-côte) ── -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;" class="mb-3">
            <!-- Contact -->
            <div>
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#ede9fe,#f5f3ff);border-bottom:1px solid #ddd6fe;">
                        <span style="width:32px;height:32px;border-radius:8px;background:#7c3aed;display:flex;align-items:center;justify-content:center;"><i class="bi bi-person-fill text-white" style="font-size:.85rem;"></i></span>
                        <strong style="color:#5b21b6;">Contact</strong>
                    </div>
                    <div class="card-body p-0">
                        <?php
                        $cRows = [
                            ['bi-person',    'Prénom & Nom', esc($lead['first_name']).' '.esc($lead['last_name'])],
                            ['bi-telephone', 'Téléphone',   !empty($lead['phone']) ? '<a href="tel:'.esc($lead['phone']).'" class="text-decoration-none fw-semibold">'.esc($lead['phone']).'</a>' : '<span class="text-muted">—</span>'],
                            ['bi-envelope',  'Email',       !empty($lead['email']) ? '<a href="mailto:'.esc($lead['email']).'" class="text-decoration-none">'.esc($lead['email']).'</a>' : '<span class="text-muted">—</span>'],
                            ['bi-funnel',    'Source',      $srcMap[$lead['source'] ?? ''] ?? esc($lead['source'] ?? '—')],
                        ];
                        ?>
                        <?php foreach ($cRows as $i => [$ico,$lbl,$val]): ?>
                        <div class="d-flex align-items-center px-3 py-2 <?= $i > 0 ? 'border-top' : '' ?>">
                            <span style="width:30px;height:30px;border-radius:8px;background:#f3f0ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi <?= $ico ?>" style="color:#7c3aed;font-size:.8rem;"></i></span>
                            <div class="ms-3"><div style="font-size:.7rem;color:#9ca3af;font-weight:500;"><?= $lbl ?></div><div style="font-size:.88rem;color:#1f2937;font-weight:600;"><?= $val ?></div></div>
                        </div>
                        <?php endforeach; ?>
                        <div class="d-flex align-items-center px-3 py-2 border-top">
                            <span style="width:30px;height:30px;border-radius:8px;background:#f3f0ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-lightning-fill" style="color:#7c3aed;font-size:.8rem;"></i></span>
                            <div class="ms-3"><div style="font-size:.7rem;color:#9ca3af;font-weight:500;">Priorité</div>
                            <span class="rounded-pill px-2 py-0" style="background:<?= $priHex ?>18;color:<?= $priHex ?>;border:1px solid <?= $priHex ?>55;font-size:.8rem;font-weight:600;"><?= $priLabel ?></span></div>
                        </div>
                        <?php if (!empty($lead['next_follow_up'])): ?>
                        <div class="d-flex align-items-center px-3 py-2 border-top">
                            <span style="width:30px;height:30px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-alarm-fill" style="color:#d97706;font-size:.8rem;"></i></span>
                            <div class="ms-3"><div style="font-size:.7rem;color:#9ca3af;font-weight:500;">Prochain suivi</div><div style="font-size:.88rem;color:#d97706;font-weight:600;"><?= date('d/m/Y', strtotime($lead['next_follow_up'])) ?></div></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Projet immobilier -->
            <div>
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#dbeafe,#eff6ff);border-bottom:1px solid #bfdbfe;">
                        <span style="width:32px;height:32px;border-radius:8px;background:#2563eb;display:flex;align-items:center;justify-content:center;"><i class="bi bi-house-fill text-white" style="font-size:.85rem;"></i></span>
                        <strong style="color:#1d4ed8;">Projet immobilier</strong>
                    </div>
                    <div class="card-body p-0">
                        <?php
                        $pRows = [
                            ['bi-arrow-left-right', 'Transaction', $txMap[$lead['transaction_type'] ?? ''] ?? ($lead['transaction_type'] ?? '—')],
                            ['bi-building',         'Type de bien', !empty($lead['property_type']) ? esc($lead['property_type']) : '—'],
                            ['bi-wallet2',          'Budget',       $budgetStr],
                            ['bi-rulers',           'Surface',      !empty($lead['desired_surface']) ? $lead['desired_surface'].' m²' : '—'],
                            ['bi-geo-alt',          'Localisation', !empty($lead['desired_location']) ? esc($lead['desired_location']) : '—'],
                        ];
                        ?>
                        <?php foreach ($pRows as $i => [$ico,$lbl,$val]): ?>
                        <div class="d-flex align-items-center px-3 py-2 <?= $i > 0 ? 'border-top' : '' ?>">
                            <span style="width:30px;height:30px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi <?= $ico ?>" style="color:#2563eb;font-size:.8rem;"></i></span>
                            <div class="ms-3"><div style="font-size:.7rem;color:#9ca3af;font-weight:500;"><?= $lbl ?></div><div style="font-size:.88rem;color:#1f2937;font-weight:600;"><?= $val ?></div></div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (!empty($lead['property_id'])): ?>
                        <div class="d-flex align-items-center px-3 py-2 border-top">
                            <span style="width:30px;height:30px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-link-45deg" style="color:#2563eb;font-size:.9rem;"></i></span>
                            <div class="ms-3 overflow-hidden"><div style="font-size:.7rem;color:#9ca3af;font-weight:500;">Bien lié</div>
                            <a href="<?= site_url('admin/properties/'.$lead['property_id']) ?>" class="text-decoration-none fw-semibold" style="font-size:.82rem;color:#2563eb;"><?= esc($lead['property_title'] ?? 'Voir le bien') ?></a></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Bien lié (fiche complète) ── -->
        <?php if (!empty($linkedProperty)): ?>
        <?php
        $coverImg = null;
        foreach ($linkedProperty['images'] as $img) { if ($img['is_primary']) { $coverImg = $img['path']; break; } }
        if (!$coverImg && !empty($linkedProperty['images'])) { $coverImg = $linkedProperty['images'][0]['path']; }
        $sMap2 = ['available'=>['Disponible','#10b981'],'sold'=>['Vendu','#ef4444'],'reserved'=>['Réservé','#f59e0b'],'rented'=>['Loué','#06b6d4']];
        [$sLbl2,$sClr2] = $sMap2[$linkedProperty['status']] ?? [ucfirst($linkedProperty['status']),'#6b7280'];
        ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#ecfdf5,#f0fdf4);border-bottom:1px solid #bbf7d0;">
                <span style="width:32px;height:32px;border-radius:8px;background:#059669;display:flex;align-items:center;justify-content:center;"><i class="bi bi-building-check text-white" style="font-size:.85rem;"></i></span>
                <strong style="color:#065f46;">Bien demandé</strong>
                <span class="ms-auto rounded-pill px-2 py-0" style="background:<?= $sClr2 ?>18;color:<?= $sClr2 ?>;border:1px solid <?= $sClr2 ?>44;font-size:.75rem;font-weight:600;"><?= $sLbl2 ?></span>
            </div>
            <div class="card-body p-3">
                <div class="d-flex gap-3 flex-wrap">
                    <?php if ($coverImg): ?>
                    <img src="<?= base_url(esc($coverImg)) ?>" class="rounded-3" style="width:140px;height:100px;object-fit:cover;flex-shrink:0;" alt="">
                    <?php else: ?>
                    <div class="rounded-3 bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:140px;height:100px;"><i class="bi bi-building" style="font-size:2rem;color:#9ca3af;"></i></div>
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="badge" style="background:#6b728020;color:#374151;font-size:.72rem;"><?= esc($linkedProperty['reference']) ?></span>
                            <a href="<?= site_url('admin/properties/'.$linkedProperty['id']) ?>" class="btn btn-sm btn-outline-primary" style="font-size:.75rem;padding:.2rem .6rem;"><i class="bi bi-eye me-1"></i>Voir</a>
                        </div>
                        <h6 class="mb-1 fw-semibold" style="font-size:.9rem;"><?= esc($linkedProperty['title']) ?></h6>
                        <p class="text-muted mb-2" style="font-size:.8rem;"><i class="bi bi-geo-alt me-1"></i><?= esc($linkedProperty['city']) ?><?= !empty($linkedProperty['zone']) ? ', '.esc($linkedProperty['zone']) : '' ?></p>
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="rounded-2 text-center px-3 py-1" style="background:#eff6ff;"><div style="font-size:.65rem;color:#9ca3af;">Prix</div><strong style="font-size:.85rem;color:#2563eb;"><?= number_format((float)$linkedProperty['price'],0,',',' ') ?> TND</strong></div>
                            <?php if (!empty($linkedProperty['surface'])): ?><div class="rounded-2 text-center px-3 py-1" style="background:#f9fafb;"><div style="font-size:.65rem;color:#9ca3af;">Surface</div><strong style="font-size:.85rem;"><?= esc($linkedProperty['surface']) ?> m²</strong></div><?php endif; ?>
                            <?php if (!empty($linkedProperty['rooms'])): ?><div class="rounded-2 text-center px-3 py-1" style="background:#f9fafb;"><div style="font-size:.65rem;color:#9ca3af;">Pièces</div><strong style="font-size:.85rem;"><?= esc($linkedProperty['rooms']) ?></strong></div><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Notes ── -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#fefce8,#fefce8);border-bottom:1px solid #fde68a;">
                <span style="width:32px;height:32px;border-radius:8px;background:#d97706;display:flex;align-items:center;justify-content:center;"><i class="bi bi-chat-text-fill text-white" style="font-size:.85rem;"></i></span>
                <strong style="color:#92400e;">Notes</strong>
                <span class="ms-auto rounded-pill px-2" style="background:#d97706;color:white;font-size:.72rem;font-weight:700;"><?= count($notes ?? []) ?></span>
            </div>
            <div class="card-body p-0" style="max-height:320px;overflow-y:auto;">
                <?php if (empty($notes)): ?>
                <div class="text-center py-4 text-muted"><i class="bi bi-chat-square-dots" style="font-size:2rem;opacity:.3;"></i><p class="mt-2 mb-0 small">Aucune note pour le moment</p></div>
                <?php else: ?>
                <?php foreach (array_reverse($notes) as $note): ?>
                <div class="px-3 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span style="width:26px;height:26px;border-radius:50%;background:#6c63ff;display:flex;align-items:center;justify-content:center;color:white;font-size:.65rem;font-weight:700;flex-shrink:0;"><?= strtoupper(substr($note['author_first_name'] ?? '?',0,1).substr($note['author_last_name'] ?? '',0,1)) ?></span>
                        <strong style="font-size:.82rem;color:#374151;"><?= esc(($note['author_first_name'] ?? '').' '.($note['author_last_name'] ?? '')) ?></strong>
                        <small class="ms-auto text-muted"><?= date('d/m/Y H:i', strtotime($note['created_at'])) ?></small>
                    </div>
                    <p class="mb-0 ps-4" style="font-size:.87rem;color:#4b5563;"><?= nl2br(esc($note['note'])) ?></p>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer p-3" style="background:#fefce8;">
                <form method="post" action="<?= site_url('admin/leads/'.$lead['id'].'/note') ?>">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <textarea name="note" class="form-control" rows="2" placeholder="Ajouter une note…" required style="font-size:.87rem;resize:none;"></textarea>
                        <button type="submit" class="btn" style="background:#d97706;color:white;border:none;"><i class="bi bi-send-fill"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ═══ Sidebar ═══════════════════════════════════════════════════════════ -->
    <div>
        <!-- Agent assigné -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <p class="text-muted small mb-2 fw-semibold" style="text-transform:uppercase;letter-spacing:.05em;font-size:.7rem;">Agent responsable</p>
                <?php if (!empty($lead['agent_first_name'])): ?>
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#6c63ff,#a78bfa);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1rem;flex-shrink:0;"><?= strtoupper(substr($lead['agent_first_name'],0,1).substr($lead['agent_last_name'] ?? '',0,1)) ?></div>
                    <div>
                        <div class="fw-semibold" style="font-size:.92rem;"><?= esc($lead['agent_first_name'].' '.$lead['agent_last_name']) ?></div>
                        <?php if (!empty($lead['agent_name'])): ?><div class="text-muted small"><?= esc($lead['agent_name']) ?></div><?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-person-dash" style="color:#9ca3af;font-size:1.2rem;"></i></div>
                    <span class="text-muted small">Non assigné</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historique pipeline — timeline -->
        <?php if (!empty($statusHistory)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center gap-2" style="background:white;border-bottom:1px solid #f3f4f6;">
                <i class="bi bi-clock-history text-primary"></i>
                <strong style="font-size:.88rem;">Historique du pipeline</strong>
            </div>
            <div class="card-body p-3" style="max-height:400px;overflow-y:auto;">
                <div class="position-relative" style="padding-left:20px;">
                    <div style="position:absolute;left:6px;top:0;bottom:0;width:2px;background:linear-gradient(to bottom,#e0e7ff,#f3f4f6);"></div>
                    <?php foreach ($statusHistory as $h): ?>
                    <?php
                    $hStatus = $h['new_status'] ?? '';
                    [$hLabel, $hHex, $hIcon] = $pipelineSteps[$hStatus] ?? [$hStatus, '#6b7280', 'bi-circle'];
                    ?>
                    <div class="position-relative mb-3">
                        <div style="position:absolute;left:-18px;top:2px;width:14px;height:14px;border-radius:50%;background:<?= $hHex ?>;box-shadow:0 0 0 3px <?= $hHex ?>22;"></div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="d-inline-flex align-items-center gap-1 rounded-pill px-2 py-0" style="background:<?= $hHex ?>15;color:<?= $hHex ?>;border:1px solid <?= $hHex ?>44;font-size:.75rem;font-weight:600;"><i class="bi <?= $hIcon ?>" style="font-size:.65rem;"></i><?= $hLabel ?></span>
                            <small class="text-muted" style="font-size:.7rem;"><?= date('d/m H:i', strtotime($h['created_at'])) ?></small>
                        </div>
                        <?php if (!empty($h['user_first_name'])): ?><div style="font-size:.72rem;color:#9ca3af;">Par <?= esc($h['user_first_name'].' '.$h['user_last_name']) ?></div><?php endif; ?>
                        <?php if (!empty($h['notes'])): ?><div class="mt-1 p-2 rounded-2 fst-italic" style="background:#f9fafb;font-size:.75rem;color:#6b7280;border-left:2px solid <?= $hHex ?>;"><?= esc($h['notes']) ?></div><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══ Propositions similaires ═══════════════════════════════════════════ -->
<?php if (!empty($similarProperties)): ?>
<div class="card border-0 shadow-sm mt-2">
    <div class="card-header d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#fffbeb,#fefce8);border-bottom:1px solid #fde68a;">
        <span style="width:32px;height:32px;border-radius:8px;background:#f59e0b;display:flex;align-items:center;justify-content:center;"><i class="bi bi-stars text-white" style="font-size:.85rem;"></i></span>
        <strong style="color:#92400e;">Biens similaires à la demande</strong>
        <span class="ms-auto rounded-pill px-2" style="background:#f59e0b;color:white;font-size:.72rem;font-weight:700;"><?= count($similarProperties) ?></span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($similarProperties as $prop): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <?php if (!empty($prop['primary_image'])): ?>
                    <img src="<?= base_url(esc($prop['primary_image'])) ?>" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                    <?php else: ?>
                    <div style="height:160px;background:linear-gradient(135deg,#f3f4f6,#e5e7eb);display:flex;align-items:center;justify-content:center;"><i class="bi bi-building" style="font-size:2.5rem;color:#9ca3af;"></i></div>
                    <?php endif; ?>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span style="font-size:.72rem;background:#f3f4f6;color:#374151;border-radius:4px;padding:1px 6px;"><?= esc($prop['type']) ?></span>
                            <?php if ($prop['featured']): ?><i class="bi bi-star-fill text-warning" style="font-size:.8rem;"></i><?php endif; ?>
                        </div>
                        <h6 class="fw-semibold mb-1" style="font-size:.85rem;line-height:1.3;"><?= esc($prop['title']) ?></h6>
                        <p class="text-muted mb-2" style="font-size:.78rem;"><i class="bi bi-geo-alt me-1"></i><?= esc($prop['city']) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong style="color:#2563eb;font-size:.9rem;"><?= number_format((float)$prop['price'],0,',',' ') ?> TND</strong>
                            <?php if (!empty($prop['surface'])): ?><small class="text-muted"><?= esc($prop['surface']) ?> m²</small><?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer p-2" style="background:#f9fafb;border-top:1px solid #f3f4f6;">
                        <a href="<?= site_url('admin/properties/'.$prop['id']) ?>" class="btn btn-sm btn-outline-primary w-100" style="font-size:.78rem;"><i class="bi bi-eye me-1"></i>Voir la fiche</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODALS PIPELINE                                                  -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<!-- ── Modal 1 : Contacté (note obligatoire) ── -->
<div class="modal fade" id="modalContacte" tabindex="-1" aria-labelledby="modalContacteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="contacted">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalContacteLabel">
                        <span class="badge bg-info text-white me-2"><i class="bi bi-telephone-check"></i></span>
                        Passer en &laquo;&nbsp;Contacté&nbsp;&raquo;
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Une note résumant l&rsquo;échange est obligatoire.
                        L&rsquo;agence responsable du bien sera notifiée.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Note de contact <span class="text-danger">*</span></label>
                        <textarea name="contact_note" class="form-control" rows="4" required
                            placeholder="Ex : Appelé le <?= date('d/m/Y') ?>, intéressé par l'appartement T3, rappel prévu sous 48h…"></textarea>
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="bi bi-bell me-1"></i>
                        Une notification sera envoyée à tous les membres de l&rsquo;agence responsable du bien.
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-check-lg me-1"></i>Confirmer le contact
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ── Modal 2 : Intéressé (RDV de visite) ── -->
<div class="modal fade" id="modalInteresse" tabindex="-1" aria-labelledby="modalInteresseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="interested">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalInteresseLabel">
                        <span class="badge bg-warning text-dark me-2"><i class="bi bi-star"></i></span>
                        Planifier un RDV de visite
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>Un rendez-vous de visite sera créé dans le module Visites.
                    </p>
                    <?php if (!empty($lead['property_title'])): ?>
                    <div class="alert alert-light border small mb-3 py-2">
                        <i class="bi bi-building me-1 text-primary"></i>
                        Bien&nbsp;: <strong><?= esc($lead['property_title']) ?></strong>
                    </div>
                    <?php endif; ?>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Date de visite <span class="text-danger">*</span></label>
                            <input type="date" name="visit_date" class="form-control" required
                                   min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Heure</label>
                            <input type="time" name="visit_time" class="form-control" value="10:00">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes pour la visite</label>
                            <textarea name="visit_notes" class="form-control" rows="2"
                                placeholder="Instructions, points à vérifier…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="bi bi-calendar-plus me-1"></i>Planifier la visite
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ── Modal 3 : Conclu (placeholder transaction) ── -->
<div class="modal fade" id="modalConclu" tabindex="-1" aria-labelledby="modalConcluLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="won">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalConcluLabel">
                        <span class="badge bg-success me-2"><i class="bi bi-trophy"></i></span>
                        Marquer comme Conclu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Félicitations&nbsp;!</strong> Ce lead est sur le point d&rsquo;être conclu.
                    </div>
                    <div class="alert alert-warning py-2 small">
                        <i class="bi bi-tools me-1"></i>
                        <strong>Module Transaction</strong> en cours de développement.<br>
                        La fiche de transaction complète (compromis, commission, suivi) sera disponible prochainement.
                    </div>
                    <p class="text-muted small mb-0">En cliquant sur Confirmer, le lead passera au statut <strong>Conclu</strong>.</p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-trophy me-1"></i>Confirmer le closing
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ── Modal 4 : Perdu (raison obligatoire) ── -->
<div class="modal fade" id="modalPerdu" tabindex="-1" aria-labelledby="modalPerduLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="lost">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalPerduLabel">
                        <span class="badge bg-danger me-2"><i class="bi bi-x-circle"></i></span>
                        Marquer comme Perdu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Indiquez la raison de la perte pour améliorer le suivi CRM.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Raison <span class="text-danger">*</span></label>
                        <select name="lost_reason" id="lostReasonSelect" class="form-select" required
                                onchange="document.getElementById('lostDetailWrap').style.display = this.value === 'Autre' ? 'block' : 'none'">
                            <option value="">-- Sélectionner une raison --</option>
                            <option>Budget insuffisant</option>
                            <option>Pas de réponse du client</option>
                            <option>Bien vendu à un autre acheteur</option>
                            <option>Délai de décision trop long</option>
                            <option>Concurrent choisi</option>
                            <option>Projet immobilier abandonné</option>
                            <option>Prix trop élevé</option>
                            <option>Bien ne correspond pas aux critères</option>
                            <option>Client non sérieux</option>
                            <option>Autre</option>
                        </select>
                    </div>
                    <div id="lostDetailWrap" class="mb-3" style="display:none">
                        <label class="form-label fw-semibold">Précisions</label>
                        <textarea name="lost_detail" class="form-control" rows="3"
                            placeholder="Décrire la situation en détail…"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Confirmer la perte
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
