<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($page_title ?? 'Rebencia') ?> – Rebencia</title>
    <!-- PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="theme-color" content="#1a3c5e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --rb-primary:   #1a3c5e;
            --rb-accent:    #e8a020;
            --rb-sidebar-w: 260px;
        }
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }

        /* ---- Sidebar ---- */
        #sidebar {
            width: var(--rb-sidebar-w);
            min-height: 100vh;
            background: var(--rb-primary);
            position: fixed; top: 0; left: 0; z-index: 1000;
            display: flex; flex-direction: column;
            transition: transform .3s;
        }
        #sidebar .sidebar-logo {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        #sidebar .sidebar-logo span { color: var(--rb-accent); font-size: 1.5rem; font-weight: 700; }
        #sidebar .sidebar-logo small { color: rgba(255,255,255,.5); font-size: .75rem; display:block; }
        #sidebar .nav-link {
            color: rgba(255,255,255,.75); padding: .6rem 1.25rem;
            border-radius: .5rem; margin: .1rem .75rem; font-size: .875rem;
            transition: background .2s, color .2s;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active { background: rgba(255,255,255,.12); color: #fff; }
        #sidebar .nav-link .bi { width: 22px; display:inline-block; text-align:center; margin-right:.5rem; }
        #sidebar .nav-section {
            color: rgba(255,255,255,.35); font-size:.7rem; text-transform:uppercase;
            letter-spacing:.1em; padding:.75rem 1.25rem .25rem; margin-top:.5rem;
        }
        #sidebar .sidebar-footer {
            margin-top: auto; padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.1);
        }

        /* ---- Content ---- */
        #content {
            margin-left: var(--rb-sidebar-w);
            transition: margin .3s;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: .75rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 900;
        }
        .page-content { padding: 1.5rem; }

        /* ---- Cards ---- */
        .stat-card {
            border: none; border-radius: 1rem;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1); }
        .stat-icon {
            width: 56px; height: 56px;
            border-radius: 1rem; display:flex; align-items:center; justify-content:center;
            font-size: 1.4rem;
        }

        /* ---- Badges rôles ---- */
        .badge-super_admin  { background-color: #6610f2 !important; }
        .badge-admin        { background-color: #20c997 !important; }
        .badge-director    { background-color: #dc3545 !important; }
        .badge-expert      { background-color: #0d6efd !important; }
        .badge-coordinator { background-color: #198754 !important; }
        .badge-collaborator{ background-color: #fd7e14 !important; }

        /* ---- Pipeline Kanban ---- */
        .pipeline-col { min-width: 220px; flex: 1; }
        .pipeline-card {
            background: #fff; border-radius:.75rem; padding:.75rem;
            margin-bottom:.5rem; font-size:.85rem;
            border-left: 4px solid var(--rb-accent);
            box-shadow: 0 2px 6px rgba(0,0,0,.06);
        }

        /* ---- Responsive ---- */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #content { margin-left: 0; }
        }
    </style>
    <?= $extra_css ?? '' ?>
</head>
<body>

<!-- ============================================================ -->
<!-- SIDEBAR                                                      -->
<!-- ============================================================ -->
<nav id="sidebar">
    <div class="sidebar-logo">
        <span>Rebencia</span>
        <small>Gestion Immobilière</small>
    </div>

    <div class="py-2 overflow-auto flex-grow-1">
        <!-- Dashboard -->
        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= (uri_string() === 'admin/dashboard' || uri_string() === 'admin') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Tableau de bord
        </a>

        <?php if (in_array('properties.view', session()->get('permissions') ?? []) || in_array('zones.view', session()->get('permissions') ?? [])) : ?>
        <div class="nav-section">Immobilier</div>
        <?php if (in_array('properties.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/properties') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/properties') ? 'active' : '' ?>">
            <i class="bi bi-building"></i> Biens
        </a>
        <?php endif; ?>
        <?php if (in_array('zones.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/zones') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/zones') ? 'active' : '' ?>">
            <i class="bi bi-geo-alt"></i> Zones
        </a>
        <?php endif; ?>
        <?php if (in_array('characteristics.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/property-characteristics') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/property-characteristics') ? 'active' : '' ?>">
            <i class="bi bi-tags"></i> Caractéristiques
        </a>
        <?php endif; ?>
        <?php if (in_array('property_types.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/property-types') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/property-types') ? 'active' : '' ?>">
            <i class="bi bi-house-gear"></i> Types de bien
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (in_array('leads.view', session()->get('permissions') ?? [])) : ?>
        <div class="nav-section">CRM</div>
        <a href="<?= base_url('admin/leads') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/leads') ? 'active' : '' ?>">
            <i class="bi bi-person-lines-fill"></i> Leads
        </a>
        <?php endif; ?>

        <?php if (in_array('users.view', session()->get('permissions') ?? [])) : ?>
        <div class="nav-section">Équipe</div>
        <a href="<?= base_url('admin/users') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/users') ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Utilisateurs
        </a>
        <?php endif; ?>

        <?php if (in_array('roles.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/roles') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/roles') ? 'active' : '' ?>">
            <i class="bi bi-shield-check"></i> Rôles & Permissions
        </a>
        <?php endif; ?>

        <div class="nav-section">Développement</div>
        <a href="<?= base_url('admin/tasks') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/tasks') ? 'active' : '' ?>">
            <i class="bi bi-kanban"></i> Suivi des tâches
        </a>
        <a href="<?= base_url('admin/notifications') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/notifications') ? 'active' : '' ?>"
           id="sb-notif-link" title="Notifications">
            <i class="bi bi-bell"></i> Notifications
            <span id="sb-notif-badge" class="badge bg-danger ms-1" style="display:none;font-size:.65rem;"></span>
        </a>

        <?php if (in_array('system.logs', session()->get('permissions') ?? []) || in_array('system.deploy', session()->get('permissions') ?? [])) : ?>
        <div class="nav-section">Système</div>
        <?php if (in_array('system.logs', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/system/logs') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/system/logs') ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i> Logs
        </a>
        <?php endif; ?>
        <?php if (in_array('system.deploy', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/system/deploy') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/system/deploy') ? 'active' : '' ?>">
            <i class="bi bi-git"></i> Déploiement
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Sidebar footer : profil -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                 style="width:36px;height:36px;font-size:.875rem;flex-shrink:0;">
                <?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="text-white text-truncate" style="font-size:.8rem;"><?= esc(session()->get('user_name')) ?></div>
                <div class="text-white-50" style="font-size:.7rem;"><?= esc(session()->get('user_role_label')) ?></div>
            </div>
            <a href="<?= base_url('logout') ?>" class="text-white-50" title="Déconnexion">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</nav>

<!-- ============================================================ -->
<!-- MAIN CONTENT                                                 -->
<!-- ============================================================ -->
<div id="content">
    <!-- Topbar -->
    <div class="topbar">
        <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>

        <nav aria-label="breadcrumb" class="d-none d-lg-block">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Accueil</a></li>
                <li class="breadcrumb-item active"><?= esc($page_title ?? '') ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-3">

            <!-- ── Cloche de notifications ── -->
            <div class="dropdown" id="rb-notif-wrap">
                <button class="btn btn-sm btn-light position-relative" id="rb-notif-btn"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        data-fetch-url="<?= base_url('admin/notifications/unread') ?>"
                        title="Notifications">
                    <i class="bi bi-bell fs-5"></i>
                    <span id="rb-notif-badge"
                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="display:none;font-size:.6rem;"></span>
                </button>

                <div class="dropdown-menu dropdown-menu-end shadow-sm p-0" id="rb-notif-menu"
                     style="width:360px;max-height:440px;overflow-y:auto;border-radius:.5rem;">
                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light sticky-top">
                        <span class="fw-semibold" style="font-size:.9rem;">
                            <i class="bi bi-bell me-1"></i>Notifications
                        </span>
                        <button id="rb-notif-readall" class="btn btn-link btn-sm p-0 text-secondary" style="font-size:.8rem;">
                            Tout marquer lu
                        </button>
                    </div>
                    <!-- Items injectés par JS -->
                    <div id="rb-notif-list">
                        <div class="text-center text-muted py-4" style="font-size:.85rem;">
                            <i class="bi bi-hourglass-split me-1"></i>Chargement…
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="text-center border-top py-2">
                        <a href="<?= base_url('admin/notifications') ?>" class="small text-decoration-none">
                            Voir toutes les notifications
                        </a>
                    </div>
                </div>
            </div>
            <!-- ── Fin cloche ── -->

            <span class="badge bg-light text-dark border" style="font-size:.75rem;">
                <?= esc(session()->get('user_role_label')) ?>
            </span>
            <a href="<?= base_url('admin/profile') ?>" class="text-decoration-none text-dark" style="font-size:.875rem;">
                <i class="bi bi-person-circle me-1"></i><?= esc(session()->get('user_name')) ?>
            </a>
        </div>
    </div>

    <!-- Flash messages -->
    <div class="page-content pb-0">
        <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ((array) session()->getFlashdata('errors') as $err) : ?>
                <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Page content -->
    <div class="page-content">
        <?= $content ?? '' ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle sidebar mobile
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });

    // Fermer sidebar au clic en dehors (mobile)
    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('sidebar');
        const toggle  = document.getElementById('sidebarToggle');
        if (window.innerWidth < 992 && sidebar.classList.contains('show')
            && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });
</script>

<!-- ── Notifications dropdown JS ── -->
<script>
(function () {
    'use strict';

    const btn       = document.getElementById('rb-notif-btn');
    const badge     = document.getElementById('rb-notif-badge');
    const list      = document.getElementById('rb-notif-list');
    const readAll   = document.getElementById('rb-notif-readall');
    const fetchUrl  = btn ? btn.dataset.fetchUrl : null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    if (!btn || !fetchUrl) return;

    // ── Rendu d'une notification ───────────────────────────────────
    const icons = {
        info:     {icon:'bi-info-circle-fill',    cls:'text-primary'},
        success:  {icon:'bi-check-circle-fill',   cls:'text-success'},
        warning:  {icon:'bi-exclamation-triangle-fill', cls:'text-warning'},
        lead:     {icon:'bi-person-lines-fill',   cls:'text-info'},
        property: {icon:'bi-building-fill',       cls:'text-secondary'},
        task:     {icon:'bi-kanban-fill',         cls:'text-dark'},
        system:   {icon:'bi-gear-fill',           cls:'text-dark'},
    };

    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function renderItem(n) {
        const t   = icons[n.type] || icons.info;
        const url = n.url ? `<?= base_url() ?>` + n.url.replace(/^\//, '') : null;
        return `
        <div class="d-flex gap-2 px-3 py-2 border-bottom notif-item ${n.is_read ? '' : 'fw-semibold'}"
             style="${n.is_read ? '' : 'background:rgba(26,60,94,.04);'}" data-id="${n.id}">
            <i class="bi ${t.icon} ${t.cls} flex-shrink-0 mt-1"></i>
            <div class="flex-grow-1" style="min-width:0;">
                <div class="d-flex justify-content-between">
                    <span class="text-truncate" style="font-size:.85rem;">${esc(n.title)}</span>
                    <small class="text-muted text-nowrap ms-1" style="font-size:.72rem;">
                        ${n.created_at ? n.created_at.substring(5,16).replace('T',' ') : ''}
                    </small>
                </div>
                <p class="mb-1 text-muted fw-normal" style="font-size:.8rem;">${esc(n.message)}</p>
                ${url ? `<a href="${esc(url)}" class="small" style="font-size:.8rem;">Voir &rarr;</a>` : ''}
            </div>
        </div>`;
    }

    // ── Charger les non-lues via JSON ──────────────────────────────
    async function fetchUnread() {
        try {
            const r    = await fetch(fetchUrl, {credentials: 'same-origin'});
            const data = await r.json();
            const count = data.count ?? 0;

            // Badge
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = '';
                // Badge sidebar
                const sbBadge = document.getElementById('sb-notif-badge');
                if (sbBadge) { sbBadge.textContent = count > 99 ? '99+' : count; sbBadge.style.display = ''; }
            } else {
                badge.style.display = 'none';
                const sbBadge = document.getElementById('sb-notif-badge');
                if (sbBadge) sbBadge.style.display = 'none';
            }

            // Liste items
            if (!data.notifications || data.notifications.length === 0) {
                list.innerHTML = `<div class="text-center text-muted py-4" style="font-size:.85rem;">
                    <i class="bi bi-bell-slash mb-2 d-block fs-4 opacity-50"></i>Aucune notification non lue</div>`;
            } else {
                list.innerHTML = data.notifications.map(renderItem).join('');
            }
        } catch (e) {
            // silencieux — ne pas afficher d'erreur pour ne pas gêner l'UX
        }
    }

    // ── Mark-read au clic sur item ─────────────────────────────────
    list.addEventListener('click', async (e) => {
        const item = e.target.closest('.notif-item[data-id]');
        if (!item) return;
        const id = item.dataset.id;
        await fetch(`<?= base_url('admin/notifications/') ?>${id}/read`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'X-CSRF-TOKEN': csrfToken},
        });
        item.classList.remove('fw-semibold');
        item.style.background = '';
        fetchUnread();
    });

    // ── Mark-all-read ──────────────────────────────────────────────
    readAll?.addEventListener('click', async () => {
        await fetch(`<?= base_url('admin/notifications/read-all') ?>`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'X-CSRF-TOKEN': csrfToken},
        });
        fetchUnread();
    });

    // ── Polling toutes les 60 secondes ─────────────────────────────
    fetchUnread();
    setInterval(fetchUnread, 60_000);

    // Refresh aussi à l'ouverture du dropdown
    document.getElementById('rb-notif-wrap')?.addEventListener('show.bs.dropdown', fetchUnread);
})();
</script>

<!-- ── PWA ── -->
<script src="<?= base_url('js/pwa.js') ?>"></script>

<?= $extra_js ?? '' ?>
</body>
</html>
