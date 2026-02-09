<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'REBENCIA - Immobilier de prestige en Tunisie') ?></title>
    
    <?php 
    // Load site settings
    $settingsModel = model('SiteSettingModel');
    $settings = $settingsModel->getAllSettings();
    ?>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Google Fonts -->
    <?php helper('theme'); ?>
    <link href="<?= get_theme_fonts() ?>" rel="stylesheet">
    
    <!-- Thème personnalisé -->
    <style>
    <?= load_theme_css() ?>
    
    /* Largeur maximale des conteneurs */
    .container {
        max-width: var(--page-max-width) !important;
    }
    
    /* Header Style ORPI */
    .orpi-header {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    
    .orpi-top-bar {
        background: var(--text-dark);
        color: white;
        padding: 8px 0;
        font-size: 0.85rem;
    }
    
    .orpi-top-bar a {
        color: white;
        text-decoration: none;
        margin: 0 15px;
        transition: opacity 0.3s;
    }
    
    .orpi-top-bar a:hover {
        opacity: 0.8;
    }
    
    .orpi-nav {
        padding: 10px 0;
    }
    
    .orpi-logo {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary-color);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -1px;
    }
    
    .orpi-logo i {
        font-size: 2.2rem;
    }
    
    .orpi-nav-menu {
        display: flex;
        align-items: center;
        gap: 5px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .orpi-nav-item {
        position: relative;
    }
    
    .orpi-nav-link {
        color: var(--text-dark);
        font-weight: 500;
        font-size: 0.9rem;
        padding: 8px 12px;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .orpi-nav-link:hover {
        color: var(--primary-color);
        background: rgba(102, 126, 234, 0.08);
    }
    
    .orpi-nav-link.active {
        color: white;
        background: var(--primary-color);
    }
    
    /* Dropdown Menu */
    .orpi-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        min-width: 220px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border-radius: 8px;
        padding: 10px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }
    
    .orpi-nav-item:hover .orpi-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .orpi-dropdown-item {
        padding: 10px 20px;
        color: var(--text-dark);
        text-decoration: none;
        display: block;
        transition: all 0.3s;
    }
    
    .orpi-dropdown-item:hover {
        background: var(--bg-light);
        color: var(--primary-color);
        padding-left: 25px;
    }
    
    .orpi-cta-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .orpi-phone {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1.1rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        border: 2px solid var(--primary-color);
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .orpi-phone:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-orpi-primary {
        background: var(--button-bg-color);
        color: var(--button-text-color);
        padding: 6px 16px;
        border: none;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.3s;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .btn-orpi-primary:hover {
        background: var(--button-hover-bg-color);
        color: var(--button-hover-text-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .btn-orpi-secondary {
        background: var(--button-secondary-bg-color);
        color: var(--button-secondary-text-color);
        padding: var(--button-padding);
        border: none;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .btn-orpi-secondary:hover {
        background: var(--button-secondary-hover-bg-color);
        color: var(--button-secondary-hover-text-color);
    }
    
    /* Hero Section */
    .orpi-hero {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        padding: 120px 0 80px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .orpi-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><path fill="%23ffffff" fill-opacity="0.05" d="M0,200 Q300,100 600,200 T1200,200 L1200,600 L0,600 Z"/></svg>');
        background-size: cover;
        opacity: 0.5;
    }
    
    .orpi-hero-content {
        position: relative;
        z-index: 1;
    }
    
    .orpi-search-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        max-width: 1100px;
        margin: 30px auto 0;
    }
    
    .orpi-search-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .orpi-search-tab {
        padding: 12px 30px;
        background: none;
        border: none;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        border-bottom: 3px solid transparent;
    }
    
    .orpi-search-tab.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
    }
    
    /* Service Cards */
    .orpi-service-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s;
        border: 2px solid #e2e8f0;
        height: 100%;
    }
    
    .orpi-service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
    }
    
    .orpi-service-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
        color: white;
    }
    
    /* Property Card */
    .orpi-property-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        height: 100%;
    }
    
    .orpi-property-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    
    .orpi-property-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }
    
    .orpi-property-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .orpi-property-card:hover .orpi-property-image img {
        transform: scale(1.1);
    }
    
    .orpi-property-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--primary-color);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .orpi-property-price {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1.1rem;
    }
    
    /* Footer */
    .orpi-footer {
        background: #1a202c;
        color: #cbd5e1;
        padding: 60px 0 20px;
        margin-top: 80px;
    }
    
    .orpi-footer-title {
        color: white;
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .orpi-footer-link {
        color: #cbd5e1;
        text-decoration: none;
        display: block;
        padding: 8px 0;
        transition: all 0.3s;
    }
    
    .orpi-footer-link:hover {
        color: var(--primary-color);
        padding-left: 10px;
    }
    
    .orpi-social-links {
        display: flex;
        gap: 15px;
    }
    
    .orpi-social-link {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .orpi-social-link:hover {
        background: var(--primary-color);
        transform: translateY(-3px);
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .orpi-nav-menu {
            flex-direction: column;
            width: 100%;
        }
        
        .orpi-cta-buttons {
            flex-direction: column;
            width: 100%;
        }
        
        .orpi-search-tabs {
            flex-wrap: wrap;
        }
    }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="orpi-header">
        <!-- Top Bar -->
        <div class="orpi-top-bar d-none d-md-block">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="tel:<?= esc($settings['phone'] ?? '+216 70 123 456') ?>">
                            <i class="fas fa-phone"></i> <?= esc($settings['phone'] ?? '+216 70 123 456') ?>
                        </a>
                        <a href="mailto:<?= esc($settings['email'] ?? 'contact@rebencia.com') ?>">
                            <i class="fas fa-envelope"></i> <?= esc($settings['email'] ?? 'contact@rebencia.com') ?>
                        </a>
                    </div>
                    <div>
                        <a href="<?= base_url('admin/login') ?>">
                            <i class="fas fa-user"></i> Espace Pro
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Navigation -->
        <nav class="orpi-nav">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Logo -->
                    <a href="<?= base_url() ?>" class="orpi-logo">
                        <i class="fas fa-home"></i>
                        REBENCIA
                    </a>
                    
                    <!-- Navigation Menu -->
                    <ul class="orpi-nav-menu d-none d-lg-flex">
                        <li class="orpi-nav-item">
                            <a href="<?= base_url('search?transaction_type=sale') ?>" class="orpi-nav-link">
                                <i class="fas fa-shopping-cart"></i> Acheter
                            </a>
                            <div class="orpi-dropdown">
                                <a href="<?= base_url('search?transaction_type=sale&type=apartment') ?>" class="orpi-dropdown-item">Appartements</a>
                                <a href="<?= base_url('search?transaction_type=sale&type=villa') ?>" class="orpi-dropdown-item">Villas</a>
                                <a href="<?= base_url('search?transaction_type=sale&type=house') ?>" class="orpi-dropdown-item">Maisons</a>
                                <a href="<?= base_url('search?transaction_type=sale&type=land') ?>" class="orpi-dropdown-item">Terrains</a>
                            </div>
                        </li>
                        <li class="orpi-nav-item">
                            <a href="<?= base_url('search?transaction_type=rent') ?>" class="orpi-nav-link">
                                <i class="fas fa-key"></i> Louer
                            </a>
                            <div class="orpi-dropdown">
                                <a href="<?= base_url('search?transaction_type=rent&type=apartment') ?>" class="orpi-dropdown-item">Appartements</a>
                                <a href="<?= base_url('search?transaction_type=rent&type=villa') ?>" class="orpi-dropdown-item">Villas</a>
                                <a href="<?= base_url('search?transaction_type=rent&type=house') ?>" class="orpi-dropdown-item">Maisons</a>
                            </div>
                        </li>
                        <li class="orpi-nav-item">
                            <a href="<?= base_url('properties') ?>" class="orpi-nav-link">
                                <i class="fas fa-building"></i> Nos Biens
                            </a>
                        </li>
                        <li class="orpi-nav-item">
                            <a href="<?= base_url('about') ?>" class="orpi-nav-link">
                                <i class="fas fa-info-circle"></i> Qui sommes-nous
                            </a>
                        </li>
                        <li class="orpi-nav-item">
                            <a href="<?= base_url('contact') ?>" class="orpi-nav-link">
                                <i class="fas fa-envelope"></i> Contact
                            </a>
                        </li>
                    </ul>
                    
                    <!-- CTA Buttons -->
                    <div class="orpi-cta-buttons d-none d-lg-flex">
                        <a href="tel:<?= esc($settings['phone'] ?? '+216 70 123 456') ?>" class="orpi-phone">
                            <i class="fas fa-phone-alt"></i>
                            <?= esc($settings['phone'] ?? '+216 70 123 456') ?>
                        </a>
                        <a href="<?= base_url('contact') ?>" class="btn-orpi-primary">
                            <i class="fas fa-paper-plane"></i> Estimer mon bien
                        </a>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button class="btn btn-link d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                        <i class="fas fa-bars fa-2x"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-unstyled">
                <li class="mb-2"><a href="<?= base_url('search?transaction_type=sale') ?>" class="orpi-nav-link">Acheter</a></li>
                <li class="mb-2"><a href="<?= base_url('search?transaction_type=rent') ?>" class="orpi-nav-link">Louer</a></li>
                <li class="mb-2"><a href="<?= base_url('properties') ?>" class="orpi-nav-link">Nos Biens</a></li>
                <li class="mb-2"><a href="<?= base_url('about') ?>" class="orpi-nav-link">Qui sommes-nous</a></li>
                <li class="mb-2"><a href="<?= base_url('contact') ?>" class="orpi-nav-link">Contact</a></li>
                <li class="mb-2"><a href="<?= base_url('admin/login') ?>" class="orpi-nav-link">Espace Pro</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <?= $this->renderSection('content') ?>

    <!-- Footer -->
    <footer class="orpi-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h3 class="orpi-footer-title">REBENCIA</h3>
                    <p>Votre partenaire immobilier de confiance en Tunisie. Nous vous accompagnons dans tous vos projets immobiliers.</p>
                    <div class="orpi-social-links mt-3">
                        <a href="#" class="orpi-social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="orpi-social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="orpi-social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="orpi-social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h4 class="orpi-footer-title">Acheter</h4>
                    <a href="<?= base_url('search?transaction_type=sale&type=apartment') ?>" class="orpi-footer-link">Appartements</a>
                    <a href="<?= base_url('search?transaction_type=sale&type=villa') ?>" class="orpi-footer-link">Villas</a>
                    <a href="<?= base_url('search?transaction_type=sale&type=house') ?>" class="orpi-footer-link">Maisons</a>
                    <a href="<?= base_url('search?transaction_type=sale&type=land') ?>" class="orpi-footer-link">Terrains</a>
                </div>
                <div class="col-md-2 mb-4">
                    <h4 class="orpi-footer-title">Louer</h4>
                    <a href="<?= base_url('search?transaction_type=rent&type=apartment') ?>" class="orpi-footer-link">Appartements</a>
                    <a href="<?= base_url('search?transaction_type=rent&type=villa') ?>" class="orpi-footer-link">Villas</a>
                    <a href="<?= base_url('search?transaction_type=rent&type=house') ?>" class="orpi-footer-link">Maisons</a>
                </div>
                <div class="col-md-2 mb-4">
                    <h4 class="orpi-footer-title">Services</h4>
                    <a href="<?= base_url('contact') ?>" class="orpi-footer-link">Estimation</a>
                    <a href="<?= base_url('contact') ?>" class="orpi-footer-link">Vendre</a>
                    <a href="<?= base_url('properties') ?>" class="orpi-footer-link">Louer mon bien</a>
                </div>
                <div class="col-md-2 mb-4">
                    <h4 class="orpi-footer-title">À propos</h4>
                    <a href="<?= base_url('about') ?>" class="orpi-footer-link">Qui sommes-nous</a>
                    <a href="<?= base_url('contact') ?>" class="orpi-footer-link">Contact</a>
                    <a href="#" class="orpi-footer-link">Mentions légales</a>
                    <a href="#" class="orpi-footer-link">Confidentialité</a>
                </div>
            </div>
            <hr class="mt-4 mb-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> REBENCIA. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
