<!-- Sidebar -->
<div class="sidebar bg-dark text-white position-fixed vh-100" style="width: 250px; z-index: 1000; overflow-y: auto;">
    <div class="p-3">
        <h4 class="mb-4">
            <i class="fas fa-building"></i> REBENCIA
        </h4>

        <!-- Search Bar -->
        <div class="mb-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-dark border-secondary text-white">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="menuSearch" class="form-control bg-dark border-secondary text-white" 
                       placeholder="Rechercher un menu..." autocomplete="off">
            </div>
        </div>
        
        <nav class="nav flex-column" id="menuNav">
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
            
            <a href="<?= base_url('admin/property-requests') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'PropertyRequests') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-clipboard-list"></i> Demandes clients
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

            <a href="<?= base_url('admin/price-per-m2') ?>" class="nav-link text-white <?= strpos(service('router')->controllerName(), 'PricePerM2') !== false ? 'active bg-primary' : '' ?>">
                <i class="fas fa-chart-line"></i> Prix au m²
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

#menuSearch::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

#menuSearch:focus {
    background-color: #2d3748;
    border-color: #4299e1;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
}

.menu-item-hidden {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('menuSearch');
    const menuNav = document.getElementById('menuNav');
    const menuItems = menuNav.querySelectorAll('a.nav-link, hr, div.px-3');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all items
            menuItems.forEach(item => {
                item.classList.remove('menu-item-hidden');
            });
            return;
        }
        
        // Filter menu items
        menuItems.forEach(item => {
            if (item.tagName === 'A') {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.classList.remove('menu-item-hidden');
                } else {
                    item.classList.add('menu-item-hidden');
                }
            } else {
                // Hide separators and labels when filtering
                item.classList.add('menu-item-hidden');
            }
        });
        
        // Show separators between visible groups
        let lastVisible = null;
        menuItems.forEach(item => {
            if (!item.classList.contains('menu-item-hidden')) {
                lastVisible = item;
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
