<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Notifications -->
                <li class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-lg text-muted"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            3
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item small" href="#">
                            <i class="fas fa-user-plus text-success"></i> Nouveau client ajouté
                            <small class="text-muted d-block">Il y a 5 min</small>
                        </a></li>
                        <li><a class="dropdown-item small" href="#">
                            <i class="fas fa-home text-primary"></i> Nouveau bien publié
                            <small class="text-muted d-block">Il y a 1 heure</small>
                        </a></li>
                        <li><a class="dropdown-item small" href="#">
                            <i class="fas fa-file-invoice text-warning"></i> Transaction en attente
                            <small class="text-muted d-block">Il y a 3 heures</small>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center small" href="<?= base_url('admin/notifications') ?>">Voir toutes</a></li>
                    </ul>
                </li>

                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('user_name') ?? 'Admin') ?>&background=0d6efd&color=fff" 
                             class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                        <span><?= esc(session()->get('user_name') ?? 'Admin') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('admin/profile') ?>">
                            <i class="fas fa-user"></i> Mon Profil
                        </a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/settings') ?>">
                            <i class="fas fa-cog"></i> Paramètres
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('admin/logout') ?>">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
