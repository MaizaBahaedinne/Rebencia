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
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --rb-sidebar-w:   260px;
            --rb-bg:          #f0f2f8;
            --rb-sidebar-bg:  #1e1b4b;
            --rb-surface:     #ffffff;
            --rb-surface-2:   #f5f6fb;
            --rb-border:      #e5e7f0;
            --rb-primary:     #6c63ff;
            --rb-primary-rgb: 108,99,255;
            --rb-accent:      #f7c948;
            --rb-green:       #10b981;
            --rb-red:         #ef4444;
            --rb-text:        #1e1b4b;
            --rb-text-muted:  #6b7280;
            --rb-radius:      .875rem;
            --rb-transition:  .22s cubic-bezier(.4,0,.2,1);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            background: var(--rb-bg);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: var(--rb-text);
            font-size: .9rem;
            line-height: 1.6;
        }

        /* ════════════════════════════════
           SCROLLBAR
        ════════════════════════════════ */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f0f2f8; }
        ::-webkit-scrollbar-thumb { background: rgba(108,99,255,.3); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(108,99,255,.55); }

        /* ════════════════════════════════
           SIDEBAR
        ════════════════════════════════ */
        #sidebar {
            width: var(--rb-sidebar-w);
            height: 100vh;
            background: var(--rb-sidebar-bg);
            border-right: 1px solid var(--rb-border);
            position: fixed; top: 0; left: 0; z-index: 1040;
            display: flex; flex-direction: column;
            transition: transform var(--rb-transition);
        }

        /* Logo */
        .sidebar-logo {
            padding: 1.4rem 1.3rem 1.1rem;
            border-bottom: 1px solid var(--rb-border);
            display: flex; align-items: center; gap: .75rem;
        }
        .sidebar-logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--rb-primary), #9b5de5);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: #fff; flex-shrink: 0;
        }
        .sidebar-logo-text { font-size: 1.05rem; font-weight: 700; color: #fff; letter-spacing: -.02em; }
        .sidebar-logo-sub  { font-size: .68rem; color: var(--rb-text-muted); letter-spacing: .03em; }

        /* Nav section labels */
        .nav-section {
            color: var(--rb-text-muted);
            font-size: .63rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            font-weight: 600;
            padding: 1.1rem 1.3rem .3rem;
        }

        /* Nav links */
        #sidebar .nav-link {
            display: flex; align-items: center; gap: .6rem;
            color: var(--rb-text-muted);
            padding: .52rem 1rem;
            margin: .1rem .75rem;
            border-radius: .6rem;
            font-size: .845rem;
            font-weight: 500;
            transition: background var(--rb-transition), color var(--rb-transition), transform .15s;
            position: relative;
            white-space: nowrap;
            overflow: hidden;
        }
        #sidebar .nav-link .bi {
            font-size: 1rem;
            width: 20px; text-align: center; flex-shrink: 0;
            transition: transform var(--rb-transition);
        }
        #sidebar .nav-link:hover {
            background: rgba(108,99,255,.1);
            color: var(--rb-text);
            transform: translateX(3px);
        }
        #sidebar .nav-link:hover .bi { transform: scale(1.15); }
        #sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(108,99,255,.22), rgba(108,99,255,.08));
            color: #fff;
            box-shadow: inset 3px 0 0 var(--rb-primary);
        }
        #sidebar .nav-link.active .bi { color: var(--rb-primary); }

        /* Sidebar scrollable nav */
        #sidebar .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: .5rem;
        }
        #sidebar .sidebar-nav::-webkit-scrollbar { width: 3px; }
        #sidebar .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 99px; }

        /* Sidebar footer — fixé en bas */
        .sidebar-footer {
            flex-shrink: 0;
            padding: .9rem 1.1rem;
            border-top: 1px solid rgba(255,255,255,.1);
            background: var(--rb-sidebar-bg);
        }
        .sidebar-footer .sf-avatar {
            width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, var(--rb-primary), #9b5de5);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; color: #fff; font-weight: 700;
        }
        .sidebar-footer .sf-name  { font-size: .8rem; color: #fff; font-weight: 600; line-height: 1.2; }
        .sidebar-footer .sf-role  { font-size: .68rem; color: var(--rb-text-muted); }
        .sidebar-footer .sf-logout {
            color: var(--rb-text-muted); font-size: 1rem;
            transition: color var(--rb-transition), transform var(--rb-transition);
            padding: .25rem;
        }
        .sidebar-footer .sf-logout:hover { color: var(--rb-red); transform: scale(1.2); }

        /* ════════════════════════════════
           MAIN CONTENT
        ════════════════════════════════ */
        #content {
            margin-left: var(--rb-sidebar-w);
            min-height: 100vh;
            padding-bottom: 52px;
            transition: margin var(--rb-transition);
        }

        /* ════════════════════════════════
           TOPBAR
        ════════════════════════════════ */
        .topbar {
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--rb-border);
            padding: .65rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 900;
            gap: 1rem;
        }
        .topbar .breadcrumb { margin: 0; }
        .topbar .breadcrumb-item a { color: var(--rb-text-muted); text-decoration: none; font-size: .83rem; }
        .topbar .breadcrumb-item a:hover { color: var(--rb-primary); }
        .topbar .breadcrumb-item.active { color: var(--rb-text); font-size: .83rem; font-weight: 500; }
        .topbar .breadcrumb-item + .breadcrumb-item::before { color: var(--rb-text-muted); }

        /* Topbar icon buttons */
        .topbar .btn-icon {
            width: 36px; height: 36px;
            background: var(--rb-surface);
            border: 1px solid var(--rb-border);
            border-radius: .55rem;
            display: flex; align-items: center; justify-content: center;
            color: var(--rb-text-muted);
            transition: background var(--rb-transition), color var(--rb-transition), border-color var(--rb-transition);
            font-size: 1rem;
        }
        .topbar .btn-icon:hover {
            background: rgba(108,99,255,.12);
            border-color: rgba(108,99,255,.3);
            color: var(--rb-primary);
        }

        /* User pill */
        .user-pill {
            display: flex; align-items: center; gap: .6rem;
            background: var(--rb-surface);
            border: 1px solid var(--rb-border);
            border-radius: 2rem;
            padding: .3rem .75rem .3rem .35rem;
            cursor: pointer;
            transition: background var(--rb-transition), border-color var(--rb-transition);
        }
        .user-pill:hover { background: var(--rb-surface-2); border-color: rgba(108,99,255,.3); }
        .user-pill .up-avatar {
            width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, var(--rb-primary), #9b5de5);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; color: #fff; font-weight: 700;
            overflow: hidden;
        }
        .user-pill .up-name  { font-size: .8rem; font-weight: 600; color: var(--rb-text); max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-pill .up-role  { font-size: .67rem; color: var(--rb-text-muted); }
        .user-pill .bi-chevron-down { font-size: .6rem; color: var(--rb-text-muted); }

        /* ════════════════════════════════
           DROPDOWN MENUS
        ════════════════════════════════ */
        .dropdown-menu {
            background: var(--rb-surface) !important;
            border: 1px solid var(--rb-border) !important;
            border-radius: var(--rb-radius) !important;
            box-shadow: 0 20px 60px rgba(108,99,255,.12), 0 4px 16px rgba(0,0,0,.08) !important;
            padding: .4rem !important;
        }
        .dropdown-item {
            color: var(--rb-text) !important;
            border-radius: .5rem;
            font-size: .845rem;
            padding: .5rem .75rem !important;
            transition: background var(--rb-transition);
        }
        .dropdown-item:hover, .dropdown-item:focus {
            background: rgba(108,99,255,.12) !important;
            color: #fff !important;
        }
        .dropdown-item.active { background: rgba(108,99,255,.2) !important; }
        .dropdown-item.text-danger:hover { background: rgba(255,92,124,.12) !important; color: var(--rb-red) !important; }
        .dropdown-divider { border-color: var(--rb-border) !important; }

        /* ════════════════════════════════
           PAGE CONTENT
        ════════════════════════════════ */
        .page-content { padding: 1.6rem; width: 100%; }

        /* ════════════════════════════════
           CARDS
        ════════════════════════════════ */
        .card {
            background: var(--rb-surface) !important;
            border: 1px solid var(--rb-border) !important;
            border-radius: var(--rb-radius) !important;
            color: var(--rb-text) !important;
        }
        .card-header, .card-footer {
            background: transparent !important;
            border-color: var(--rb-border) !important;
        }

        .stat-card {
            border: 1px solid var(--rb-border) !important;
            border-radius: var(--rb-radius) !important;
            background: var(--rb-surface) !important;
            transition: transform var(--rb-transition), box-shadow var(--rb-transition), border-color var(--rb-transition);
            overflow: hidden;
            position: relative;
        }
        .stat-card::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(108,99,255,.06) 0%, transparent 60%);
            pointer-events: none;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,.35);
            border-color: rgba(108,99,255,.3) !important;
        }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: .85rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }

        /* ════════════════════════════════
           TABLES
        ════════════════════════════════ */
        .table {
            color: var(--rb-text) !important;
            border-color: var(--rb-border) !important;
        }
        .table thead th {
            background: var(--rb-surface-2) !important;
            color: var(--rb-text-muted) !important;
            font-size: .72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .07em;
            border-color: var(--rb-border) !important;
            padding: .75rem 1rem !important;
        }
        .table tbody td {
            border-color: var(--rb-border) !important;
            padding: .8rem 1rem !important;
            vertical-align: middle;
        }
        .table tbody tr { transition: background var(--rb-transition); }
        .table tbody tr:hover { background: rgba(108,99,255,.06) !important; }
        .table-striped tbody tr:nth-of-type(odd) { background: rgba(255,255,255,.018) !important; }

        /* ════════════════════════════════
           FORMS
        ════════════════════════════════ */
        .form-control, .form-select {
            background: var(--rb-surface-2) !important;
            border: 1px solid var(--rb-border) !important;
            color: var(--rb-text) !important;
            border-radius: .6rem !important;
            font-size: .875rem;
            transition: border-color var(--rb-transition), box-shadow var(--rb-transition);
        }
        .form-control::placeholder { color: var(--rb-text-muted) !important; }
        .form-control:focus, .form-select:focus {
            border-color: var(--rb-primary) !important;
            box-shadow: 0 0 0 3px rgba(108,99,255,.18) !important;
            background: var(--rb-surface) !important;
        }
        .form-label { color: var(--rb-text-muted); font-size: .8rem; font-weight: 500; margin-bottom: .35rem; }
        .form-check-input { background-color: var(--rb-surface-2) !important; border-color: var(--rb-border) !important; }
        .form-check-input:checked { background-color: var(--rb-primary) !important; border-color: var(--rb-primary) !important; }
        .form-select option { background: var(--rb-surface-2); color: var(--rb-text); }
        .input-group-text {
            background: var(--rb-surface-2) !important;
            border-color: var(--rb-border) !important;
            color: var(--rb-text-muted) !important;
        }

        /* ════════════════════════════════
           BUTTONS
        ════════════════════════════════ */
        .btn {
            border-radius: .6rem !important;
            font-weight: 500;
            font-size: .845rem;
            transition: all var(--rb-transition) !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--rb-primary), #9b5de5) !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(108,99,255,.35);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(108,99,255,.5) !important;
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-outline-secondary {
            border-color: var(--rb-border) !important;
            color: var(--rb-text-muted) !important;
            background: transparent !important;
        }
        .btn-outline-secondary:hover {
            background: var(--rb-surface-2) !important;
            color: var(--rb-text) !important;
            border-color: rgba(108,99,255,.3) !important;
        }
        .btn-light, .btn-secondary {
            background: var(--rb-surface-2) !important;
            border-color: var(--rb-border) !important;
            color: var(--rb-text) !important;
        }
        .btn-light:hover, .btn-secondary:hover {
            background: rgba(108,99,255,.12) !important;
            border-color: rgba(108,99,255,.3) !important;
            color: #fff !important;
        }
        .btn-danger { background: linear-gradient(135deg, #ff5c7c, #ff2d55) !important; border: none !important; }
        .btn-success { background: linear-gradient(135deg, #22d3a5, #0fa97e) !important; border: none !important; }
        .btn-warning { background: linear-gradient(135deg, #f7c948, #f0a500) !important; border: none !important; color: #1a1d27 !important; }
        .btn-info    { background: linear-gradient(135deg, #38bdf8, #0ea5e9) !important; border: none !important; color: #fff !important; }
        .btn-sm { font-size: .78rem !important; padding: .3rem .7rem !important; }

        /* ════════════════════════════════
           BADGES
        ════════════════════════════════ */
        .badge { border-radius: .4rem !important; font-weight: 500; letter-spacing: .02em; }
        .badge-super_admin  { background: linear-gradient(135deg,#6610f2,#9b5de5) !important; }
        .badge-admin        { background: linear-gradient(135deg,#22d3a5,#0fa97e) !important; }
        .badge-director     { background: linear-gradient(135deg,#ff5c7c,#ff2d55) !important; }
        .badge-expert       { background: linear-gradient(135deg,#6c63ff,#3b82f6) !important; }
        .badge-coordinator  { background: linear-gradient(135deg,#22d3a5,#059669) !important; }
        .badge-collaborator { background: linear-gradient(135deg,#f7c948,#f0a500) !important; color:#1a1d27 !important; }

        /* ════════════════════════════════
           ALERTS
        ════════════════════════════════ */
        .alert {
            border-radius: var(--rb-radius) !important;
            border: 1px solid var(--rb-border) !important;
            font-size: .875rem;
        }
        .alert-success { background: #ecfdf5 !important; color: #065f46 !important; border-color: #a7f3d0 !important; }
        .alert-danger  { background: #fef2f2 !important; color: #991b1b !important; border-color: #fecaca !important; }
        .alert-warning { background: #fffbeb !important; color: #92400e !important; border-color: #fde68a !important; }
        .alert-info    { background: #eff6ff !important; color: #1e40af !important; border-color: #bfdbfe !important; }
        .btn-close { filter: none; opacity: .5; }
        .btn-close:hover { filter: none; opacity: 1; }

        /* ════════════════════════════════
           PAGINATION
        ════════════════════════════════ */
        .page-link {
            background: var(--rb-surface) !important;
            border-color: var(--rb-border) !important;
            color: var(--rb-text-muted) !important;
            border-radius: .5rem !important;
            margin: 0 2px;
        }
        .page-link:hover { background: rgba(108,99,255,.12) !important; color: #fff !important; }
        .page-item.active .page-link { background: var(--rb-primary) !important; border-color: var(--rb-primary) !important; color: #fff !important; }

        /* ════════════════════════════════
           PIPELINE KANBAN
        ════════════════════════════════ */
        .pipeline-col { min-width: 230px; flex: 1; }
        .pipeline-card {
            background: var(--rb-surface-2);
            border-radius: .75rem; padding: .85rem;
            margin-bottom: .5rem; font-size: .84rem;
            border-left: 3px solid var(--rb-primary);
            border-top: 1px solid var(--rb-border);
            border-right: 1px solid var(--rb-border);
            border-bottom: 1px solid var(--rb-border);
            transition: transform var(--rb-transition), box-shadow var(--rb-transition);
        }
        .pipeline-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.3); }

        /* ════════════════════════════════
           NOTIFICATIONS DROPDOWN
        ════════════════════════════════ */
        #rb-notif-menu {
            background: var(--rb-surface) !important;
            border: 1px solid var(--rb-border) !important;
        }
        #rb-notif-menu .sticky-top { background: #f8f9fc !important; }
        .notif-item { transition: background var(--rb-transition); }
        .notif-item:hover { background: rgba(108,99,255,.08) !important; }

        /* ════════════════════════════════
           PAGE ENTRY ANIMATION
        ════════════════════════════════ */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .page-content > * { animation: fadeSlideUp .35s ease both; }
        .page-content > *:nth-child(2) { animation-delay: .04s; }
        .page-content > *:nth-child(3) { animation-delay: .08s; }
        .page-content > *:nth-child(4) { animation-delay: .12s; }
        .page-content > *:nth-child(5) { animation-delay: .16s; }

        /* ════════════════════════════════
           MISC UTILITIES
        ════════════════════════════════ */
        .text-muted { color: var(--rb-text-muted) !important; }
        hr { border-color: var(--rb-border) !important; }
        .border   { border-color: var(--rb-border) !important; }
        .bg-white { background: var(--rb-surface) !important; }
        .bg-light { background: var(--rb-surface-2) !important; }
        .bg-body  { background: var(--rb-bg) !important; }
        .list-group-item {
            background: var(--rb-surface) !important;
            border-color: var(--rb-border) !important;
            color: var(--rb-text) !important;
        }
        .nav-tabs .nav-link { color: var(--rb-text-muted) !important; border-color: transparent !important; border-radius: .5rem .5rem 0 0 !important; }
        .nav-tabs .nav-link:hover { color: var(--rb-text) !important; background: rgba(108,99,255,.08) !important; }
        .nav-tabs .nav-link.active { background: var(--rb-surface) !important; color: var(--rb-primary) !important; border-color: var(--rb-border) var(--rb-border) transparent !important; }
        .nav-tabs { border-color: var(--rb-border) !important; }
        .tab-content { background: var(--rb-surface); border: 1px solid var(--rb-border); border-top: none; border-radius: 0 0 var(--rb-radius) var(--rb-radius); }
        .modal-content { background: var(--rb-surface) !important; border: 1px solid var(--rb-border) !important; color: var(--rb-text) !important; }
        .modal-header, .modal-footer { border-color: var(--rb-border) !important; }
        .tooltip-inner { background: var(--rb-surface-2); border: 1px solid var(--rb-border); }
        code { background: rgba(108,99,255,.08); color: #6c63ff; border-radius: .3rem; padding: .1em .35em; font-size: .85em; }
        pre  { background: var(--rb-surface-2) !important; border: 1px solid var(--rb-border); border-radius: .6rem; color: var(--rb-text); }
        .opacity-65 { opacity: .65; }

        /* ════════════════════════════════
           FOOTER FIXE
        ════════════════════════════════ */
        #app-footer {
            position: fixed;
            bottom: 0;
            left: var(--rb-sidebar-w);
            right: 0;
            height: 44px;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid var(--rb-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 800;
            font-size: .75rem;
            color: var(--rb-text-muted);
        }
        #app-footer a { color: var(--rb-primary); text-decoration: none; }
        #app-footer a:hover { text-decoration: underline; }

        /* ════════════════════════════════
           RESPONSIVE
        ════════════════════════════════ */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); box-shadow: none; }
            #sidebar.show { transform: translateX(0); box-shadow: 8px 0 40px rgba(0,0,0,.6); }
            #content { margin-left: 0; }
            .page-content { padding: 1rem; }
            #app-footer { left: 0; }
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
        <div class="sidebar-logo-icon"><i class="bi bi-buildings-fill"></i></div>
        <div>
            <div class="sidebar-logo-text">Rebencia</div>
            <div class="sidebar-logo-sub">Gestion Immobilière</div>
        </div>
    </div>

    <div class="sidebar-nav" id="sidebar-nav">
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

        <?php if (in_array('leads.view', session()->get('permissions') ?? []) || in_array('clients.view', session()->get('permissions') ?? []) || in_array('visits.view', session()->get('permissions') ?? [])) : ?>
        <div class="nav-section">CRM</div>
        <?php endif; ?>
        <?php if (in_array('leads.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/leads') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/leads') ? 'active' : '' ?>">
            <i class="bi bi-person-lines-fill"></i> Leads
        </a>
        <?php endif; ?>
        <?php if (in_array('clients.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/clients') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/clients') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Clients
        </a>
        <?php endif; ?>
        <?php if (in_array('visits.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/visits') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/visits') ? 'active' : '' ?>">
            <i class="bi bi-calendar2-event"></i> Visites
        </a>
        <?php endif; ?>

        <?php if (in_array('users.view', session()->get('permissions') ?? []) || in_array('agencies.view', session()->get('permissions') ?? [])) : ?>
        <div class="nav-section">Équipe</div>
        <?php if (in_array('users.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/teams') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/teams') ? 'active' : '' ?>">
            <i class="bi bi-diagram-3-fill"></i> Équipes
        </a>
        <?php endif; ?>
        <?php if (in_array('agencies.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/agencies') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/agencies') ? 'active' : '' ?>">
            <i class="bi bi-buildings"></i> Agences
        </a>
        <?php endif; ?>
        <?php if (in_array('users.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/users') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/users') ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Utilisateurs
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (in_array('roles.view', session()->get('permissions') ?? [])) : ?>
        <a href="<?= base_url('admin/roles') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/roles') ? 'active' : '' ?>">
            <i class="bi bi-shield-check"></i> Rôles & Permissions
        </a>
        <?php endif; ?>

        <?php
        $_devPerms = session()->get('permissions') ?? [];
        $_hasTasks = in_array('tasks.view', $_devPerms);
        ?>
        <?php if ($_hasTasks): ?>
        <div class="nav-section">Développement</div>
        <a href="<?= base_url('admin/tasks') ?>" class="nav-link <?= str_starts_with(uri_string(), 'admin/tasks') ? 'active' : '' ?>">
            <i class="bi bi-kanban"></i> Suivi des tâches
        </a>
        <?php endif; ?>
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
    </div><!-- /.sidebar-nav -->

    <!-- Sidebar footer : profil -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <?php if (!empty(session()->get('user_avatar'))): ?>
            <img src="<?= base_url(esc(session()->get('user_avatar'))) ?>"
                 class="sf-avatar" style="object-fit:cover;" alt="">
            <?php else: ?>
            <div class="sf-avatar"><?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?></div>
            <?php endif; ?>
            <div style="flex:1;min-width:0;">
                <div class="sf-name text-truncate"><?= esc(session()->get('user_name')) ?></div>
                <div class="sf-role text-truncate"><?= esc(session()->get('user_role_label')) ?>
                    <?php if (session()->get('agency_name')): ?>
                    · <?= esc(session()->get('agency_name')) ?>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" class="sf-logout" title="Déconnexion">
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
        <button class="btn-icon d-lg-none" id="sidebarToggle" style="border:none;cursor:pointer;">
            <i class="bi bi-list fs-5"></i>
        </button>

        <nav aria-label="breadcrumb" class="d-none d-lg-block flex-grow-1">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-house-fill me-1"></i>Accueil</a></li>
                <li class="breadcrumb-item active"><?= esc($page_title ?? '') ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">

            <!-- ── Cloche de notifications ── -->
            <div class="dropdown" id="rb-notif-wrap">
                <button class="btn-icon position-relative" id="rb-notif-btn"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        data-fetch-url="<?= base_url('admin/notifications/unread') ?>"
                        title="Notifications" style="border:none;cursor:pointer;">
                    <i class="bi bi-bell"></i>
                    <span id="rb-notif-badge"
                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="display:none;font-size:.6rem;min-width:16px;"></span>
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

            <!-- ── Menu utilisateur ── -->
            <div class="dropdown">
                <div class="user-pill" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if (!empty(session()->get('user_avatar'))): ?>
                    <div class="up-avatar">
                        <img src="<?= base_url(esc(session()->get('user_avatar'))) ?>"
                             style="width:100%;height:100%;object-fit:cover;" alt="">
                    </div>
                    <?php else: ?>
                    <div class="up-avatar"><?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?></div>
                    <?php endif; ?>
                    <div class="d-none d-md-block">
                        <div class="up-name"><?= esc(session()->get('user_name')) ?></div>
                        <div class="up-role"><?= esc(session()->get('user_role_label')) ?></div>
                    </div>
                    <i class="bi bi-chevron-down"></i>
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1"
                    style="min-width:240px;border-radius:.75rem;" aria-labelledby="userMenuBtn">
                    <!-- En-tête du menu -->
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-semibold" style="font-size:.875rem;"><?= esc(session()->get('user_name')) ?></div>
                        <div class="text-muted" style="font-size:.75rem;"><?= esc(session()->get('user_email') ?? '') ?></div>
                        <span class="badge bg-primary mt-1" style="font-size:.7rem;"><?= esc(session()->get('user_role_label')) ?></span>
                    </li>

                    <li>
                        <a class="dropdown-item py-2" href="<?= base_url('admin/profile') ?>">
                            <i class="bi bi-person me-2 text-muted"></i>Profil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?= base_url('admin/settings') ?>">
                            <i class="bi bi-gear me-2 text-muted"></i>Paramètres
                        </a>
                    </li>

                    <!-- Switcher de rôles -->
                    <?php
                    $sessionUserId = session()->get('user_id');
                    $sessionRoleId = (int) session()->get('user_role_id');
                    $availableRoles = $sessionUserId ? (new \App\Models\UserModel())->getUserRoles((int)$sessionUserId) : [];
                    ?>
                    <?php if (count($availableRoles) > 0): ?>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li class="px-3 pt-1 pb-0">
                        <small class="text-muted text-uppercase fw-semibold" style="font-size:.65rem;letter-spacing:.06em;">
                            <i class="bi bi-arrow-left-right me-1"></i>Mes rôles
                        </small>
                    </li>
                    <?php foreach ($availableRoles as $r):
                        $isActive = ((int)$r['role_id'] === $sessionRoleId);
                    ?>
                    <li>
                        <?php if ($isActive): ?>
                        <span class="dropdown-item py-2 d-flex align-items-center justify-content-between active pe-none">
                            <span>
                                <span class="rounded-circle d-inline-block me-2"
                                      style="width:8px;height:8px;background:<?= esc($r['color']) ?>;"></span>
                                <?= esc($r['label']) ?>
                            </span>
                            <i class="bi bi-check2 fw-bold"></i>
                        </span>
                        <?php else: ?>
                        <form method="post" action="<?= base_url('admin/role/switch') ?>" class="d-block">
                            <?= csrf_field() ?>
                            <input type="hidden" name="role_id" value="<?= (int)$r['role_id'] ?>">
                            <input type="hidden" name="redirect" value="<?= esc(current_url()) ?>">
                            <button type="submit" class="dropdown-item py-2 d-flex align-items-center">
                                <span class="rounded-circle d-inline-block me-2"
                                      style="width:8px;height:8px;background:<?= esc($r['color']) ?>;"></span>
                                <?= esc($r['label']) ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <li>
                        <a class="dropdown-item py-2" href="<?= base_url('admin/agencies') ?>">
                            <i class="bi bi-building me-2 text-muted"></i>Mon agence
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-1"></li>

                    <li>
                        <a class="dropdown-item py-2 text-danger" href="<?= base_url('logout') ?>">
                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
            <!-- ── Fin menu utilisateur ── -->
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

<!-- Footer fixe -->
<footer id="app-footer">
    <span>© <?= date('Y') ?> <strong>Rebencia</strong> — Gestion Immobilière</span>
    <span class="d-none d-md-inline">
        <i class="bi bi-circle-fill me-1" style="font-size:.45rem;color:var(--rb-green);vertical-align:middle;"></i>
        Système opérationnel
    </span>
</footer>

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
