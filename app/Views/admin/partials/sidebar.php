<!-- Sidebar -->
<div class="sidebar bg-dark text-white position-fixed vh-100" style="width: 250px; z-index: 1000;">
    <div class="p-3">
        <h4 class="mb-4">
            <i class="fas fa-building"></i> REBENCIA
        </h4>
        
        <nav class="nav flex-column">
            <a href="<?= base_url('admin') ?>" class="nav-link text-white <?= service('router')->methodName() == 'index' && service('router')->controllerName() == '\App\Controllers\Admin\Dashboard' ? 'active bg-primary' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <a href="<?= base_url('admin/properties') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'Properties') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-home"></i> Biens
            </a>
            
            <a href="<?= base_url('admin/clients') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'Clients') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-users"></i> Clients
            </a>
            
            <a href="<?= base_url('admin/transactions') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'Transactions') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-file-invoice-dollar"></i> Transactions
            </a>
            
            <a href="<?= base_url('admin/property-requests') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'PropertyEstimations') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-calculator"></i> Demandes d'estimation
            </a>
            
            <a href="<?= base_url('admin/search-alerts') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'SearchAlerts') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-bell"></i> Alertes de recherche
            </a>
            
            <a href="<?= base_url('admin/users') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'Users') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-user-tie"></i> Utilisateurs
            </a>
            
            <hr class="border-secondary">
            
            <a href="<?= base_url('admin/agencies') ?>" class="nav-link text-white">
                <i class="fas fa-building"></i> Agences
            </a>
            
            <a href="<?= base_url('admin/zones') ?>" class="nav-link text-white">
                <i class="fas fa-map-marker-alt"></i> Zones
            </a>
            
            <a href="<?= base_url('admin/reports') ?>" class="nav-link text-white">
                <i class="fas fa-chart-bar"></i> Rapports
            </a>
            
            <hr class="border-secondary">
            
            <div class="px-3 py-2 text-muted small">
                <i class="fas fa-paint-brush"></i> Site Web
            </div>
            
            <a href="<?= base_url('admin/sliders') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'Sliders') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-images"></i> Sliders
            </a>
            
            <a href="<?= base_url('admin/theme') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'Theme') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-palette"></i> Thème
            </a>
            
            <a href="<?= base_url('admin/settings/footer') ?>" class="nav-link text-white">
                <i class="fas fa-columns"></i> Footer
            </a>
            
            <hr class="border-secondary">
            
            <a href="<?= base_url('admin/settings') ?>" class="nav-link text-white">
                <i class="fas fa-cog"></i> Paramètres
            </a>
            
            <hr class="border-secondary">
            
            <a href="<?= base_url('admin/logout') ?>" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </nav>
    </div>
</div>

<style>
.sidebar .nav-link {
    padding: 0.75rem 1rem;
    border-radius: 5px;
    margin-bottom: 0.25rem;
    transition: all 0.3s;
}

.sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.sidebar .nav-link.active {
    background-color: #0d6efd !important;
}

.sidebar .nav-link i {
    width: 20px;
    margin-right: 10px;
}
</style>
