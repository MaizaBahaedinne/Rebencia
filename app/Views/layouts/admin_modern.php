<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'REBENCIA Admin' ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/datatable-custom.css') ?>">
    
    <?= get_template_styles() ?>
    
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --sidebar-bg: #1a1d29;
            --sidebar-hover: #252936;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f6fa;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1050;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
        }

        body.sidebar-collapsed .admin-sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed .admin-header,
        body.sidebar-collapsed .admin-main {
            margin-left: 0;
            left: 0;
        }

        .sidebar-toggle-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
            border: none;
            border-radius: 10px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s;
            margin-right: 1rem;
        }

        .sidebar-toggle-btn:hover {
            background: #e5e7eb;
            color: var(--primary-color);
        }

        .sidebar-header {
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .sidebar-logo i {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-color);
            border-radius: 12px;
            font-size: 1.3rem;
        }

        /* Menu Search */
        #menuSearch {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            font-size: 0.875rem;
            border-radius: 8px;
        }

        #menuSearch::placeholder {
            color: rgba(255, 255, 255, 0.4) !important;
        }

        #menuSearch:focus {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
        }

        .sidebar-menu {
            padding: 1.5rem 1rem;
        }

        .menu-section-title {
            color: #8b92a7;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.5rem 1rem;
            margin-top: 1rem;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.875rem 1rem;
            margin-bottom: 0.25rem;
            color: #9ca3af;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-item:hover {
            background: var(--sidebar-hover);
            color: white;
            transform: translateX(5px);
        }

        .menu-item.active {
            background: linear-gradient(90deg, rgba(13, 110, 253, 0.2), rgba(13, 110, 253, 0.05));
            color: white;
            border-left: 3px solid var(--primary-color);
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary-color);
            box-shadow: 0 0 10px var(--primary-color);
        }

        .menu-item i {
            width: 20px;
            font-size: 1.1rem;
        }

        .menu-badge {
            margin-left: auto;
            padding: 0.2rem 0.6rem;
            background: var(--danger-color);
            color: white;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* ========== SUBMENU ========== */
        .menu-item.has-submenu {
            cursor: pointer;
            position: relative;
        }

        .menu-item.has-submenu .submenu-toggle {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .menu-item.has-submenu.open .submenu-toggle {
            transform: rotate(180deg);
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding-left: 1rem;
        }

        .submenu.open {
            max-height: 1000px;
        }

        .submenu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.15rem;
            color: #9ca3af;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .submenu-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            padding-left: 1.25rem;
        }

        .submenu-item.active {
            background: rgba(13, 110, 253, 0.15);
            color: white;
            border-left: 2px solid var(--primary-color);
        }

        .submenu-item i {
            width: 16px;
            font-size: 0.9rem;
        }

        /* ========== HEADER ========== */
        .admin-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e5e7eb;
            z-index: 1040;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .header-search {
            flex: 1;
            max-width: 500px;
        }

        .header-search input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 3rem;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #f9fafb;
            transition: all 0.3s;
        }

        .header-search input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        .header-search i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        .header-icon {
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
            border-radius: 10px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s;
        }

        .header-icon:hover {
            background: #e5e7eb;
            color: var(--primary-color);
        }

        .header-icon .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 18px;
            height: 18px;
            background: var(--danger-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            color: white;
            font-weight: 600;
        }

        /* ========== NOTIFICATIONS DROPDOWN ========== */
        .notifications-dropdown {
            position: absolute;
            top: 60px;
            right: 0;
            width: 380px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 1000;
        }

        .notifications-dropdown.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notifications-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notifications-header h6 {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }

        .mark-all-read {
            color: var(--primary-color);
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
        }

        .mark-all-read:hover {
            text-decoration: underline;
        }

        .notifications-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            gap: 12px;
        }

        .notification-item:hover {
            background: #f9fafb;
        }

        .notification-item.unread {
            background: #eff6ff;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon.success {
            background: #d1fae5;
            color: #065f46;
        }

        .notification-icon.info {
            background: #dbeafe;
            color: #1e40af;
        }

        .notification-icon.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .notification-icon.danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .notification-message {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .notifications-footer {
            padding: 0.75rem 1.25rem;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .notifications-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .notifications-footer a:hover {
            text-decoration: underline;
        }

        .empty-notifications {
            padding: 3rem 1.25rem;
            text-align: center;
            color: #9ca3af;
        }

        .empty-notifications i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.5rem 1rem;
            background: #f9fafb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-menu:hover {
            background: #e5e7eb;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .user-role {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* ========== MAIN CONTENT ========== */
        .admin-main {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            min-height: calc(100vh - var(--header-height) - 80px);
        }

        /* ========== FOOTER ========== */
        .admin-footer {
            margin-left: var(--sidebar-width);
            padding: 1.5rem 2rem;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-text {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-links a {
            color: #6b7280;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        /* ========== BREADCRUMB ========== */
        .page-breadcrumb {
            margin-bottom: 1.5rem;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: #6b7280;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }

        /* ========== PAGE HEADER ========== */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            color: var(--primary-color);
        }

        .page-header-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        /* ========== CARDS ========== */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
            border-radius: 16px 16px 0 0;
            font-weight: 600;
        }

        /* ========== ALERTS ========== */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert i {
            margin-right: 0.5rem;
        }

        /* ========== BUTTONS ========== */
        .btn {
            padding: 0.625rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn i {
            margin-right: 0.5rem;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-header,
            .admin-main,
            .admin-footer {
                margin-left: 0;
                left: 0;
            }

            .header-search {
                display: none;
            }
        }

        /* ========== SCROLLBAR ========== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Impersonation Banner -->
    <?php if (session()->has('is_impersonating')): ?>
    <div class="alert alert-warning mb-0 rounded-0 border-0 d-flex align-items-center justify-content-between" style="position: fixed; top: 0; left: 0; right: 0; z-index: 9999;">
        <div>
            <i class="fas fa-user-secret me-2"></i>
            <strong>Mode Impersonation:</strong> Vous êtes connecté en tant que <strong><?= esc(session()->get('user_name')) ?></strong>
            <small class="ms-2 text-muted">(Admin: <?= esc(session()->get('original_user_name')) ?>)</small>
        </div>
        <a href="<?= base_url('admin/stop-impersonation') ?>" class="btn btn-sm btn-dark">
            <i class="fas fa-sign-out-alt me-1"></i> Retourner au compte Admin
        </a>
    </div>
    <?php endif; ?>
    
    <!-- Sidebar -->
    <aside class="admin-sidebar" style="<?= session()->has('is_impersonating') ? 'top: 58px; height: calc(100vh - 58px);' : '' ?>">
        <div class="sidebar-header">
            <a href="<?= base_url('admin') ?>" class="sidebar-logo">
                <i class="fas fa-building"></i>
                <span>REBENCIA</span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="px-3 mb-3">
            <div class="position-relative">
                <input type="text" id="menuSearch" class="form-control form-control-sm bg-dark text-white border-secondary" 
                       placeholder="Rechercher..." autocomplete="off"
                       style="padding-left: 35px;">
                <i class="fas fa-search position-absolute text-white-50" 
                   style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.875rem;"></i>
            </div>
        </div>

        <nav class="sidebar-menu" id="sidebarMenu">
            <div class="menu-section-title">PRINCIPAL</div>
            
            <a href="<?= base_url('admin') ?>" class="menu-item <?= url_is('admin') && !url_is('admin/*') ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <div class="menu-section-title">MENU</div>
            
            <!-- GESTION -->
            <?php if (canRead('properties') || canRead('clients') || canRead('transactions') || canAccessCommissions()): ?>
            <div class="menu-item has-submenu <?= url_is('admin/properties*') || url_is('admin/clients*') || url_is('admin/transactions*') || url_is('admin/commission-settings*') || url_is('admin/property-requests*') ? 'open' : '' ?>">
                <i class="fas fa-briefcase"></i>
                <span>Gestion</span>
                <i class="fas fa-chevron-down submenu-toggle"></i>
            </div>
            <div class="submenu <?= url_is('admin/properties*') || url_is('admin/clients*') || url_is('admin/transactions*') || url_is('admin/commission-settings*') || url_is('admin/property-requests*') ? 'open' : '' ?>">
                <?php if (canRead('properties')): ?>
                <a href="<?= base_url('admin/properties') ?>" class="submenu-item <?= url_is('admin/properties*') && !url_is('admin/properties/assignments*') ? 'active' : '' ?>">
                    <i class="fas fa-building"></i>
                    <span>Biens Immobiliers</span>
                </a>
                <?php endif; ?>
                
                <?php if (canUpdate('properties') || isAdmin()): ?>
                <a href="<?= base_url('admin/properties/assignments') ?>" class="submenu-item <?= url_is('admin/properties/assignments*') ? 'active' : '' ?>">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Affectation des Biens</span>
                </a>
                <?php endif; ?>
                
                <?php if (canRead('properties')): ?>
                <a href="<?= base_url('admin/property-requests') ?>" class="submenu-item <?= url_is('admin/property-requests*') && !isset($_GET['type']) ? 'active' : '' ?>">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>Demandes Clients</span>
                    <?php 
                    $requestModel = model('PropertyRequestModel');
                    $pendingCount = $requestModel->where('status', 'pending')->countAllResults();
                    if ($pendingCount > 0):
                    ?>
                        <span class="badge bg-danger rounded-pill ms-auto"><?= $pendingCount ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="<?= base_url('admin/property-requests?type=estimation') ?>" class="submenu-item <?= url_is('admin/property-requests*') && isset($_GET['type']) && $_GET['type'] === 'estimation' ? 'active' : '' ?>">
                    <i class="fas fa-calculator"></i>
                    <span>Demandes d'Estimation</span>
                    <?php 
                    $estimationCount = $requestModel->where('request_type', 'estimation')->where('status', 'pending')->countAllResults();
                    if ($estimationCount > 0):
                    ?>
                        <span class="badge bg-warning rounded-pill ms-auto"><?= $estimationCount ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <?php if (canRead('clients')): ?>
                <a href="<?= base_url('admin/clients') ?>" class="submenu-item <?= url_is('admin/clients*') ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Clients</span>
                </a>
                <?php endif; ?>
                
                <?php if (canRead('transactions')): ?>
                <a href="<?= base_url('admin/transactions') ?>" class="submenu-item <?= url_is('admin/transactions*') ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Transactions</span>
                </a>
                <?php endif; ?>
                
                <?php if (canAccessCommissions()): ?>
                <a href="<?= base_url('admin/commission-settings') ?>" class="submenu-item <?= url_is('admin/commission-settings*') ? 'active' : '' ?>">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Commissions</span>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- ORGANISATION -->
            <?php if (canRead('agencies') || canRead('users')): ?>
            <div class="menu-item has-submenu <?= url_is('admin/agencies*') || url_is('admin/users*') || url_is('admin/hierarchy*') ? 'open' : '' ?>">
                <i class="fas fa-sitemap"></i>
                <span>Organisation</span>
                <i class="fas fa-chevron-down submenu-toggle"></i>
            </div>
            <div class="submenu <?= url_is('admin/agencies*') || url_is('admin/users*') || url_is('admin/hierarchy*') ? 'open' : '' ?>">
                <?php if (canRead('agencies')): ?>
                <a href="<?= base_url('admin/agencies') ?>" class="submenu-item <?= url_is('admin/agencies*') ? 'active' : '' ?>">
                    <i class="fas fa-store"></i>
                    <span>Agences</span>
                </a>
                <?php endif; ?>
                
                <?php if (canRead('users')): ?>
                <a href="<?= base_url('admin/users') ?>" class="submenu-item <?= url_is('admin/users*') ? 'active' : '' ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Utilisateurs</span>
                </a>
                
                <a href="<?= base_url('admin/hierarchy') ?>" class="submenu-item <?= url_is('admin/hierarchy*') ? 'active' : '' ?>">
                    <i class="fas fa-network-wired"></i>
                    <span>Hiérarchie</span>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- SÉCURITÉ -->
            <?php if (canRead('roles') || isSuperAdmin()): ?>
            <div class="menu-item has-submenu <?= url_is('admin/roles*') || url_is('admin/menus*') ? 'open' : '' ?>">
                <i class="fas fa-shield-alt"></i>
                <span>Sécurité</span>
                <i class="fas fa-chevron-down submenu-toggle"></i>
            </div>
            <div class="submenu <?= url_is('admin/roles*') || url_is('admin/menus*') ? 'open' : '' ?>">
                <a href="<?= base_url('admin/roles') ?>" class="submenu-item <?= url_is('admin/roles') && !url_is('admin/roles/matrix') ? 'active' : '' ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Rôles</span>
                </a>
                
                <a href="<?= base_url('admin/roles/matrix') ?>" class="submenu-item <?= url_is('admin/roles/matrix') ? 'active' : '' ?>">
                    <i class="fas fa-table"></i>
                    <span>Matrice Permissions</span>
                </a>
                
                <a href="<?= base_url('admin/menus/role-menus') ?>" class="submenu-item <?= url_is('admin/menus*') ? 'active' : '' ?>">
                    <i class="fas fa-bars"></i>
                    <span>Gestion des Menus</span>
                </a>
            </div>
            <?php endif; ?>

            <!-- OUTILS -->
            <div class="menu-item has-submenu <?= url_is('admin/workflows*') || url_is('admin/zones*') || url_is('admin/estimations*') || url_is('admin/reports*') || url_is('admin/analytics*') || url_is('admin/appointments*') || url_is('admin/tasks*') || url_is('admin/chat*') || url_is('admin/objectives*') || url_is('admin/price-per-m2*') || url_is('admin/search-alerts*') ? 'open' : '' ?>">
                <i class="fas fa-tools"></i>
                <span>Outils</span>
                <i class="fas fa-chevron-down submenu-toggle"></i>
            </div>
            <div class="submenu <?= url_is('admin/workflows*') || url_is('admin/zones*') || url_is('admin/estimations*') || url_is('admin/reports*') || url_is('admin/analytics*') || url_is('admin/appointments*') || url_is('admin/tasks*') || url_is('admin/chat*') || url_is('admin/objectives*') || url_is('admin/price-per-m2*') || url_is('admin/search-alerts*') ? 'open' : '' ?>">
                <?php if (canRead('properties')): ?>
                <a href="<?= base_url('admin/workflows/pipeline/property') ?>" class="submenu-item <?= url_is('admin/workflows*') ? 'active' : '' ?>">
                    <i class="fas fa-project-diagram"></i>
                    <span>Pipeline Ventes</span>
                </a>
                <?php endif; ?>
                
                <?php if (canRead('zones') || isAdmin()): ?>
                <a href="<?= base_url('admin/zones') ?>" class="submenu-item <?= url_is('admin/zones*') ? 'active' : '' ?>">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Zones</span>
                </a>
                <?php endif; ?>
                
                <a href="<?= base_url('admin/price-per-m2') ?>" class="submenu-item <?= url_is('admin/price-per-m2*') ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Prix au m²</span>
                </a>
                
                <a href="<?= base_url('admin/search-alerts') ?>" class="submenu-item <?= url_is('admin/search-alerts*') ? 'active' : '' ?>">
                    <i class="fas fa-bell"></i>
                    <span>Alertes de recherche</span>
                </a>
                
                <?php if (canRead('properties')): ?>
                <a href="<?= base_url('admin/estimations') ?>" class="submenu-item <?= url_is('admin/estimations*') ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Estimation IA</span>
                </a>
                <?php endif; ?>
                
                <?php if (canRead('transactions') || canRead('properties')): ?>
                <a href="<?= base_url('admin/reports') ?>" class="submenu-item <?= url_is('admin/reports*') ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Rapports & Export</span>
                </a>
                <?php endif; ?>
                
                <a href="<?= base_url('admin/analytics') ?>" class="submenu-item <?= url_is('admin/analytics*') ? 'active' : '' ?>">
                    <i class="fas fa-analytics"></i>
                    <span>Analytics</span>
                </a>
                
                <a href="<?= base_url('admin/appointments') ?>" class="submenu-item <?= url_is('admin/appointments*') ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Agenda</span>
                </a>
                
                <a href="<?= base_url('admin/tasks') ?>" class="submenu-item <?= url_is('admin/tasks*') ? 'active' : '' ?>">
                    <i class="fas fa-tasks"></i>
                    <span>Tâches</span>
                </a>
                
                <a href="<?= base_url('admin/chat') ?>" class="submenu-item <?= url_is('admin/chat*') ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i>
                    <span>Chat</span>
                </a>
                
                <?php if (isAdmin()): ?>
                <a href="<?= base_url('admin/objectives') ?>" class="submenu-item <?= url_is('admin/objectives*') ? 'active' : '' ?>">
                    <i class="fas fa-bullseye"></i>
                    <span>Objectifs</span>
                </a>
                <?php endif; ?>
            </div>

            <!-- SITE WEB -->
            <?php if (isAdmin()): ?>
            <div class="menu-item has-submenu <?= url_is('admin/sliders*') || url_is('admin/theme*') || url_is('admin/settings/footer') || url_is('admin/pages*') ? 'open' : '' ?>">
                <i class="fas fa-globe"></i>
                <span>Site Web</span>
                <i class="fas fa-chevron-down submenu-toggle"></i>
            </div>
            <div class="submenu <?= url_is('admin/sliders*') || url_is('admin/theme*') || url_is('admin/settings/footer') || url_is('admin/pages*') ? 'open' : '' ?>">
                <a href="<?= base_url('admin/sliders') ?>" class="submenu-item <?= url_is('admin/sliders*') ? 'active' : '' ?>">
                    <i class="fas fa-images"></i>
                    <span>Sliders</span>
                </a>
                
                <a href="<?= base_url('admin/theme') ?>" class="submenu-item <?= url_is('admin/theme*') ? 'active' : '' ?>">
                    <i class="fas fa-palette"></i>
                    <span>Thème</span>
                </a>
                
                <a href="<?= base_url('admin/settings/footer') ?>" class="submenu-item <?= url_is('admin/settings/footer') ? 'active' : '' ?>">
                    <i class="fas fa-columns"></i>
                    <span>Footer</span>
                </a>
                
                <a href="<?= base_url('admin/pages') ?>" class="submenu-item <?= url_is('admin/pages*') ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Pages CMS</span>
                </a>
            </div>

            <!-- SYSTÈME -->
            <div class="menu-item has-submenu <?= url_is('admin/system*') || url_is('admin/settings*') ? 'open' : '' ?>">
                <i class="fas fa-cog"></i>
                <span>Système</span>
                <i class="fas fa-chevron-down submenu-toggle"></i>
            </div>
            <div class="submenu <?= url_is('admin/system*') || url_is('admin/settings*') ? 'open' : '' ?>">
                <a href="<?= base_url('admin/system') ?>" class="submenu-item <?= url_is('admin/system*') ? 'active' : '' ?>">
                    <i class="fas fa-server"></i>
                    <span>Système & Backup</span>
                </a>
                
                <a href="<?= base_url('admin/settings/general') ?>" class="submenu-item <?= url_is('admin/settings/general') ? 'active' : '' ?>">
                    <i class="fas fa-sliders-h"></i>
                    <span>Paramètres Généraux</span>
                </a>
                
                <a href="<?= base_url('admin/settings/email') ?>" class="submenu-item <?= url_is('admin/settings/email') ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Config. Email</span>
                </a>
                
                <a href="<?= base_url('admin/settings/sms') ?>" class="submenu-item <?= url_is('admin/settings/sms') ? 'active' : '' ?>">
                    <i class="fas fa-sms"></i>
                    <span>Config. SMS</span>
                </a>
                
                <a href="<?= base_url('admin/settings/payment') ?>" class="submenu-item <?= url_is('admin/settings/payment') ? 'active' : '' ?>">
                    <i class="fas fa-credit-card"></i>
                    <span>Paiement</span>
                </a>
                
                <a href="<?= base_url('admin/settings/notifications') ?>" class="submenu-item <?= url_is('admin/settings/notifications') ? 'active' : '' ?>">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </div>
            <?php endif; ?>
            
            <a href="<?= base_url('admin/logout') ?>" class="menu-item" style="margin-top: 2rem; color: #ef4444;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </nav>
    </aside>

    <!-- Header -->
    <header class="admin-header" style="<?= session()->has('is_impersonating') ? 'top: 58px;' : '' ?>">
        <button class="sidebar-toggle-btn" id="sidebarToggle" title="Masquer/Afficher le menu">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="header-search position-relative">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher un bien, client, transaction...">
        </div>

        <div class="header-actions">
            <!-- Notifications Icon -->
            <div class="header-icon position-relative" id="notificationBell">
                <i class="fas fa-bell"></i>
                <span class="badge" id="notificationCount" style="display: none;">0</span>
                
                <!-- Notifications Dropdown -->
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="notifications-header">
                        <h6>Notifications</h6>
                        <a href="#" class="mark-all-read" id="markAllRead">Tout marquer comme lu</a>
                    </div>
                    <div class="notifications-list" id="notificationsList">
                        <div class="empty-notifications">
                            <i class="fas fa-bell-slash"></i>
                            <p>Aucune notification</p>
                        </div>
                    </div>
                    <div class="notifications-footer">
                        <a href="<?= base_url('admin/notifications') ?>">Voir toutes les notifications</a>
                    </div>
                </div>
            </div>
            
            <div class="header-icon">
                <i class="fas fa-envelope"></i>
                <span class="badge">12</span>
            </div>

            <div class="dropdown">
                <div class="user-menu" data-bs-toggle="dropdown">
                    <?php 
                    $userAvatar = session()->get('user_avatar');
                    if (!empty($userAvatar) && file_exists(FCPATH . 'uploads/avatars/' . $userAvatar)): 
                    ?>
                        <img src="<?= base_url('uploads/avatars/' . $userAvatar) ?>" 
                             alt="Avatar" class="user-avatar" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('user_name') ?? 'Admin') ?>&background=0d6efd&color=fff&size=40" 
                             alt="Avatar" class="user-avatar">
                    <?php endif; ?>
                    <div class="user-info d-none d-md-block">
                        <div class="user-name"><?= esc(session()->get('user_name') ?? 'Admin') ?></div>
                        <div class="user-role">
                            <?= esc(session()->get('role_display_name') ?? 'Administrateur') ?>
                            <?php if (session()->get('agency_name')): ?>
                                <span class="text-muted" style="font-size: 0.75rem;"> • <?= esc(session()->get('agency_name')) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 0.8rem;"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px; padding: 0.5rem;">
                    <li><a class="dropdown-item" href="<?= base_url('admin/profile') ?>">
                        <i class="fas fa-user me-2"></i> Mon Profil
                    </a></li>
                    
                    <?php 
                    // Get user roles if multiple
                    $userId = session()->get('user_id');
                    if ($userId) {
                        $userModel = model('UserModel');
                        $userWithRoles = $userModel->getUserWithRoles($userId);
                        if (!empty($userWithRoles['roles']) && count($userWithRoles['roles']) > 1):
                    ?>
                    <li><hr class="dropdown-divider"></li>
                    <li class="px-3 py-2">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                                <i class="fas fa-user-shield"></i> Changer de Rôle
                            </small>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php foreach ($userWithRoles['roles'] as $role): ?>
                            <form action="<?= base_url('admin/switch-role') ?>" method="post" class="m-0">
                                <?= csrf_field() ?>
                                <input type="hidden" name="role_id" value="<?= $role['role_id'] ?>">
                                <button type="submit" 
                                        class="list-group-item list-group-item-action border-0 py-2 px-3 <?= $role['is_active'] == 1 ? 'active bg-primary text-white' : '' ?>" 
                                        <?= $role['is_active'] == 1 ? 'disabled' : '' ?>
                                        style="border-radius: 8px; margin-bottom: 4px; <?= $role['is_active'] == 1 ? '' : 'background: #f8f9fa;' ?>">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-<?= $role['is_active'] == 1 ? 'check-circle' : 'circle' ?> me-2"></i>
                                            <div>
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    <?= esc($role['display_name']) ?>
                                                </div>
                                                <small class="<?= $role['is_active'] == 1 ? 'text-white-50' : 'text-muted' ?>" style="font-size: 0.75rem;">
                                                    <?= esc($role['name']) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <?php if ($role['is_active'] == 1): ?>
                                            <span class="badge bg-light text-primary" style="font-size: 0.7rem;">Actif</span>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </form>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <?php 
                        endif;
                    }
                    ?>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/settings/general') ?>">
                        <i class="fas fa-cog me-2"></i> Paramètres
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= base_url('admin/logout') ?>">
                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                    </a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main" style="<?= session()->has('is_impersonating') ? 'margin-top: 58px;' : '' ?>">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="admin-footer">
        <div class="footer-text">
            © <?= date('Y') ?> <strong>REBENCIA</strong> - Plateforme immobilière multi-agences
        </div>
        <div class="footer-links">
            <a href="#">Documentation</a>
            <a href="#">Support</a>
            <a href="#">Politique de confidentialité</a>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
    // ========== NOTIFICATIONS SYSTEM ==========
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationsDropdown = document.getElementById('notificationsDropdown');
        const notificationCount = document.getElementById('notificationCount');
        const notificationsList = document.getElementById('notificationsList');
        const markAllRead = document.getElementById('markAllRead');

        // Toggle notifications dropdown
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationsDropdown.classList.toggle('show');
            if (notificationsDropdown.classList.contains('show')) {
                loadNotifications();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target)) {
                notificationsDropdown.classList.remove('show');
            }
        });

        // Load notifications
        function loadNotifications() {
            fetch('<?= base_url('admin/notifications') ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderNotifications(data.notifications);
                    updateBadge(data.unread_count);
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
        }

        // Render notifications
        function renderNotifications(notifications) {
            if (notifications.length === 0) {
                notificationsList.innerHTML = `
                    <div class="empty-notifications">
                        <i class="fas fa-bell-slash"></i>
                        <p>Aucune notification</p>
                    </div>
                `;
                return;
            }

            notificationsList.innerHTML = notifications.map(notif => `
                <div class="notification-item ${notif.is_read == 0 ? 'unread' : ''}" 
                     data-id="${notif.id}" 
                     data-link="${notif.link || '#'}">
                    <div class="notification-icon ${notif.type}">
                        <i class="fas ${notif.icon || 'fa-bell'}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notif.title}</div>
                        <div class="notification-message">${notif.message}</div>
                        <div class="notification-time">${formatTime(notif.created_at)}</div>
                    </div>
                </div>
            `).join('');

            // Add click handlers
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function() {
                    const notifId = this.dataset.id;
                    const link = this.dataset.link;
                    markAsRead(notifId, link);
                });
            });
        }

        // Update badge count
        function updateBadge(count) {
            if (count > 0) {
                notificationCount.textContent = count > 99 ? '99+' : count;
                notificationCount.style.display = 'flex';
            } else {
                notificationCount.style.display = 'none';
            }
        }

        // Mark as read
        function markAsRead(notifId, link) {
            fetch(`<?= base_url('admin/notifications/mark-as-read') ?>/${notifId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                    if (link && link !== '#') {
                        window.location.href = link;
                    }
                }
            })
            .catch(error => console.error('Error marking as read:', error));
        }

        // Mark all as read
        markAllRead.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            fetch('<?= base_url('admin/notifications/mark-all-as-read') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        });

        // Format time
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // seconds

            if (diff < 60) return 'À l\'instant';
            if (diff < 3600) return Math.floor(diff / 60) + ' min';
            if (diff < 86400) return Math.floor(diff / 3600) + ' h';
            if (diff < 604800) return Math.floor(diff / 86400) + ' j';
            
            return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
        }

        // Initial load
        loadNotifications();
        
        // Auto-refresh every 30 seconds
        setInterval(loadNotifications, 30000);
    });

    // ========== SIDEBAR TOGGLE ==========
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const body = document.body;
        
        // Charger l'état sauvegardé
        const sidebarState = localStorage.getItem('sidebarCollapsed');
        if (sidebarState === 'true') {
            body.classList.add('sidebar-collapsed');
        }
        
        sidebarToggle.addEventListener('click', function() {
            body.classList.toggle('sidebar-collapsed');
            
            // Sauvegarder l'état
            const isCollapsed = body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
    });

    // ========== SUBMENU TOGGLE ==========
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.menu-item.has-submenu');
        
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Trouver le submenu associé
                const submenu = this.nextElementSibling;
                
                // Toggle l'état ouvert
                this.classList.toggle('open');
                submenu.classList.toggle('open');
                
                // Sauvegarder l'état dans localStorage
                const menuText = this.querySelector('span').textContent;
                const isOpen = this.classList.contains('open');
                localStorage.setItem('submenu_' + menuText, isOpen);
            });
            
            // Restaurer l'état sauvegardé
            const menuText = item.querySelector('span').textContent;
            const savedState = localStorage.getItem('submenu_' + menuText);
            
            // Si un sous-menu est actif, l'ouvrir automatiquement
            const submenu = item.nextElementSibling;
            const hasActiveItem = submenu && submenu.querySelector('.submenu-item.active');
            
            if (hasActiveItem || savedState === 'true') {
                item.classList.add('open');
                submenu.classList.add('open');
            }
        });
    });

    // ========== MENU SEARCH ==========
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('menuSearch');
        const sidebarMenu = document.getElementById('sidebarMenu');
        
        if (!searchInput || !sidebarMenu) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Get all menu items
            const menuItems = sidebarMenu.querySelectorAll('.menu-item, .submenu-item');
            const sections = sidebarMenu.querySelectorAll('.menu-section-title');
            const submenus = sidebarMenu.querySelectorAll('.submenu');
            
            if (searchTerm === '') {
                // Show all items
                menuItems.forEach(item => item.style.display = '');
                sections.forEach(section => section.style.display = '');
                submenus.forEach(submenu => {
                    submenu.style.display = '';
                    // Restore original open state
                    const parentItem = submenu.previousElementSibling;
                    if (parentItem && !submenu.querySelector('.submenu-item.active')) {
                        submenu.classList.remove('open');
                        parentItem.classList.remove('open');
                    }
                });
                return;
            }
            
            // Hide all sections by default when searching
            sections.forEach(section => section.style.display = 'none');
            
            // Filter menu items
            menuItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                const matches = text.includes(searchTerm);
                
                if (matches) {
                    item.style.display = '';
                    
                    // If it's a submenu item, show and expand its parent
                    if (item.classList.contains('submenu-item')) {
                        const submenu = item.closest('.submenu');
                        if (submenu) {
                            submenu.style.display = '';
                            submenu.classList.add('open');
                            const parentItem = submenu.previousElementSibling;
                            if (parentItem) {
                                parentItem.style.display = '';
                                parentItem.classList.add('open');
                            }
                        }
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Hide empty submenus
            submenus.forEach(submenu => {
                const visibleItems = Array.from(submenu.querySelectorAll('.submenu-item'))
                    .filter(item => item.style.display !== 'none');
                
                if (visibleItems.length === 0) {
                    submenu.style.display = 'none';
                    const parentItem = submenu.previousElementSibling;
                    if (parentItem) {
                        parentItem.style.display = 'none';
                    }
                } else {
                    submenu.style.display = '';
                    submenu.classList.add('open');
                }
            });
        });
        
        // Clear search on Escape
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.dispatchEvent(new Event('input'));
                this.blur();
            }
        });
        
        // Focus search with Ctrl+K or Cmd+K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
