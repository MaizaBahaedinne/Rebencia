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
    <?php
    // Charger les helpers
    helper('theme');
    ?>
    <link href="<?= get_theme_fonts() ?>" rel="stylesheet">
    
    <!-- Thème personnalisé (généré depuis la base de données) -->
    <style>
    <?= load_theme_css() ?>
    </style>
    
    <!-- Custom CSS -->
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        /* Modern Header */
        .modern-header {
            background: #fff;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .modern-header.scrolled {
            box-shadow: 0 4px 30px rgba(0,0,0,0.12);
        }
        
        .header-top {
            background: var(--primary-gradient);
            color: white;
            padding: 8px 0;
            font-size: 0.85rem;
        }
        
        .header-top a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            transition: opacity 0.3s;
        }
        
        .header-top a:hover {
            opacity: 0.8;
        }
        
        .navbar-modern {
            padding: 15px 0;
        }
        
        .navbar-brand-modern {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand-modern i {
            -webkit-text-fill-color: var(--primary-color);
        }
        
        .nav-link-modern {
            color: var(--text-dark);
            font-weight: 500;
            padding: 8px 20px;
            margin: 0 5px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link-modern:hover {
            color: var(--primary-color);
            background: rgba(102, 126, 234, 0.1);
        }
        
        .nav-link-modern.active {
            color: var(--primary-color);
        }
        
        .nav-link-modern.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: var(--primary-gradient);
            border-radius: 3px 3px 0 0;
        }
        
        .btn-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        /* Modern Footer */
        .modern-footer {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #cbd5e1;
            padding: 60px 0 0;
            margin-top: 80px;
            position: relative;
        }
        
        .modern-footer::before {
            content: '';
            position: absolute;
            top: -50px;
            left: 0;
            right: 0;
            height: 50px;
            background: linear-gradient(to bottom, transparent, rgba(30, 41, 59, 0.1));
        }
        
        .footer-section {
            margin-bottom: 40px;
        }
        
        .footer-title {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-gradient);
            border-radius: 3px;
        }
        
        .footer-link {
            color: #cbd5e1;
            text-decoration: none;
            display: block;
            padding: 8px 0;
            transition: all 0.3s ease;
        }
        
        .footer-link:hover {
            color: white;
            padding-left: 10px;
        }
        
        .footer-link i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: var(--primary-gradient);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .footer-bottom {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px 0;
            margin-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .contact-info-item {
            display: flex;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .contact-info-item i {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(102, 126, 234, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .property-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 15px;
            overflow: hidden;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .footer {
            background: var(--primary-color);
            color: #fff;
            padding: 3rem 0 1rem;
            margin-top: 5rem;
        }
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        /* Smooth animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand-modern {
                font-size: 1.5rem;
            }
            
            .modern-footer {
                padding: 40px 0 0;
            }
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Modern Header -->
    <header class="modern-header">
        <!-- Top Bar -->
        <div class="header-top">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-none d-md-block">
                        <a href="tel:<?= esc($settings['contact_phone_1'] ?? '+21612345678') ?>"><i class="fas fa-phone"></i> <?= esc($settings['contact_phone_1'] ?? '+216 12 345 678') ?></a>
                        <a href="mailto:<?= esc($settings['contact_email'] ?? 'contact@rebencia.tn') ?>"><i class="fas fa-envelope"></i> <?= esc($settings['contact_email'] ?? 'contact@rebencia.tn') ?></a>
                    </div>
                    <div class="social-links-top d-flex gap-3">
                        <?php if (!empty($settings['social_facebook'])): ?>
                        <a href="<?= esc($settings['social_facebook']) ?>" target="_blank" class="text-white"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_instagram'])): ?>
                        <a href="<?= esc($settings['social_instagram']) ?>" target="_blank" class="text-white"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_linkedin'])): ?>
                        <a href="<?= esc($settings['social_linkedin']) ?>" target="_blank" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_youtube'])): ?>
                        <a href="<?= esc($settings['social_youtube']) ?>" target="_blank" class="text-white"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Navigation -->
        <nav class="navbar navbar-expand-lg navbar-modern">
            <div class="container">
                <a class="navbar-brand-modern" href="<?= base_url() ?>">
                    <i class="fas fa-building"></i>
                    <span><?= esc($settings['site_name'] ?? 'REBENCIA') ?></span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarModern">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarModern">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link-modern active" href="<?= base_url() ?>">
                                <i class="fas fa-home me-1"></i> Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-modern" href="<?= base_url('properties') ?>">
                                <i class="fas fa-th-large me-1"></i> Propriétés
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-modern" href="<?= base_url('search') ?>">
                                <i class="fas fa-search me-1"></i> Recherche
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-modern" href="<?= base_url('about') ?>">
                                <i class="fas fa-info-circle me-1"></i> À propos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-modern" href="<?= base_url('contact') ?>">
                                <i class="fas fa-envelope me-1"></i> Contact
                            </a>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="btn btn-gradient" href="<?= base_url('admin/login') ?>">
                                <i class="fas fa-user me-2"></i> Espace Pro
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
    <?= $this->renderSection('content') ?>
    </main>

    <!-- Modern Footer -->
    <footer class="modern-footer">
        <div class="container">
            <div class="row">
                <!-- About Section -->
                <div class="col-lg-4 col-md-6 footer-section">
                    <h4 class="footer-title">
                        <i class="fas fa-building me-2"></i> <?= esc($settings['site_name'] ?? 'REBENCIA') ?>
                    </h4>
                    <p class="mb-3">
                        <?= esc($settings['footer_about'] ?? 'Votre partenaire de confiance pour tous vos projets immobiliers en Tunisie.') ?>
                    </p>
                    <div class="social-links">
                        <?php if (!empty($settings['social_facebook'])): ?>
                        <a href="<?= esc($settings['social_facebook']) ?>" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_instagram'])): ?>
                        <a href="<?= esc($settings['social_instagram']) ?>" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_linkedin'])): ?>
                        <a href="<?= esc($settings['social_linkedin']) ?>" target="_blank" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_youtube'])): ?>
                        <a href="<?= esc($settings['social_youtube']) ?>" target="_blank" class="social-link"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_whatsapp'])): ?>
                        <a href="https://wa.me/<?= esc(str_replace(['+', ' '], '', $settings['social_whatsapp'])) ?>" target="_blank" class="social-link"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 footer-section">
                    <h4 class="footer-title">Liens rapides</h4>
                    <a href="<?= base_url() ?>" class="footer-link">
                        <i class="fas fa-angle-right"></i> Accueil
                    </a>
                    <a href="<?= base_url('properties') ?>" class="footer-link">
                        <i class="fas fa-angle-right"></i> Propriétés
                    </a>
                    <a href="<?= base_url('search') ?>" class="footer-link">
                        <i class="fas fa-angle-right"></i> Recherche
                    </a>
                    <a href="<?= base_url('about') ?>" class="footer-link">
                        <i class="fas fa-angle-right"></i> À propos
                    </a>
                    <a href="<?= base_url('contact') ?>" class="footer-link">
                        <i class="fas fa-angle-right"></i> Contact
                    </a>
                </div>
                
                <!-- Services -->
                <div class="col-lg-3 col-md-6 footer-section">
                    <h4 class="footer-title">Nos services</h4>
                    <a href="#" class="footer-link">
                        <i class="fas fa-angle-right"></i> Achat de propriété
                    </a>
                    <a href="#" class="footer-link">
                        <i class="fas fa-angle-right"></i> Vente de bien
                    </a>
                    <a href="#" class="footer-link">
                        <i class="fas fa-angle-right"></i> Location immobilière
                    </a>
                    <a href="#" class="footer-link">
                        <i class="fas fa-angle-right"></i> Estimation gratuite
                    </a>
                    <a href="#" class="footer-link">
                        <i class="fas fa-angle-right"></i> Gestion locative
                    </a>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6 footer-section">
                    <h4 class="footer-title">Contactez-nous</h4>
                    <div class="contact-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Adresse</strong><br>
                            <?= esc($settings['contact_address'] ?? 'Avenue Habib Bourguiba, Tunis, Tunisie') ?>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Téléphone</strong><br>
                            <?= esc($settings['contact_phone_1'] ?? '+216 12 345 678') ?><br>
                            <?php if (!empty($settings['contact_phone_2'])): ?>
                            <?= esc($settings['contact_phone_2']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong><br>
                            <?= esc($settings['contact_email'] ?? 'contact@rebencia.tn') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0">&copy; <?= date('Y') ?> <?= esc($settings['site_name'] ?? 'REBENCIA') ?>. Tous droits réservés.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="#" class="text-light text-decoration-none me-3">Mentions légales</a>
                        <a href="#" class="text-light text-decoration-none me-3">Politique de confidentialité</a>
                        <a href="#" class="text-light text-decoration-none">CGU</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Header Scroll Effect -->
    <script>
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.modern-header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Active link detection
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link-modern').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
