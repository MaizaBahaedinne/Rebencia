<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin - REBENCIA') ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
        }
        
        body {
            background: #f8f9fa;
        }
        
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: #2c3e50;
            color: #fff;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .admin-sidebar .logo {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
        }
        
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        
        .admin-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .admin-header {
            background: #fff;
            height: var(--header-height);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            padding: 0 2rem;
        }
        
        .admin-main {
            padding: 2rem;
        }
        
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="logo">
            <h4 class="mb-0"><i class="fas fa-building"></i> REBENCIA</h4>
            <small class="text-muted">Admin Panel</small>
        </div>
        <nav class="nav flex-column mt-3">
            <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a class="nav-link" href="<?= base_url('admin/properties') ?>">
                <i class="fas fa-home me-2"></i> Propriétés
            </a>
            <a class="nav-link" href="<?= base_url('admin/clients') ?>">
                <i class="fas fa-users me-2"></i> Clients
            </a>
            <a class="nav-link" href="<?= base_url('admin/transactions') ?>">
                <i class="fas fa-exchange-alt me-2"></i> Transactions
            </a>
            <a class="nav-link" href="<?= base_url('admin/commissions') ?>">
                <i class="fas fa-dollar-sign me-2"></i> Commissions
            </a>
            <a class="nav-link" href="<?= base_url('admin/agencies') ?>">
                <i class="fas fa-building me-2"></i> Agences
            </a>
            <a class="nav-link" href="<?= base_url('admin/users') ?>">
                <i class="fas fa-user-tie me-2"></i> Utilisateurs
            </a>
            <a class="nav-link" href="<?= base_url('admin/roles') ?>">
                <i class="fas fa-shield-alt me-2"></i> Rôles
            </a>
            <a class="nav-link" href="<?= base_url('admin/workflows') ?>">
                <i class="fas fa-project-diagram me-2"></i> Workflows
            </a>
            <a class="nav-link" href="<?= base_url('admin/pages') ?>">
                <i class="fas fa-file-alt me-2"></i> Pages
            </a>
            <a class="nav-link" href="<?= base_url('admin/settings') ?>">
                <i class="fas fa-cog me-2"></i> Paramètres
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Header -->
        <div class="admin-header">
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3">
                    <i class="fas fa-user-circle"></i> <?= session()->get('username') ?? 'Admin' ?>
                </span>
                <a href="<?= base_url('admin/logout') ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="admin-main">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
