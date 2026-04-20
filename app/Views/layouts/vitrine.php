<!DOCTYPE html>
<html lang="<?= esc($lang ?? 'fr') ?>" dir="<?= ($lang ?? 'fr') === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? 'Rebencia') ?> — Rebencia Immobilier</title>
    <meta name="description" content="<?= esc($meta_description ?? 'Rebencia — Votre agence immobilière de confiance au Maroc. Achat, vente et location de biens immobiliers.') ?>">

    <!-- Bootstrap 5 -->
    <?php if (($lang ?? 'fr') === 'ar'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <?php else: ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <?php endif; ?>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Vitrine CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/vitrine.css') ?>">
    <?= $this->renderSection('head') ?>
</head>
<body>

<!-- =========================================================
     NAVBAR
========================================================= -->
<nav class="navbar navbar-expand-lg rb-navbar fixed-top" id="rbNavbar">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand rb-logo" href="<?= base_url($currentLang . '/') ?>">
            <span class="rb-logo-primary">Reben</span><span class="rb-logo-accent">cia</span>
            <small>Immobilier</small>
        </a>

        <!-- Mobile toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'home' ? 'active' : '' ?>"
                       href="<?= base_url($currentLang . '/') ?>">
                        <?= lang('Vitrine.nav_home') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'properties' ? 'active' : '' ?>"
                       href="<?= base_url($currentLang . '/properties') ?>">
                        <?= lang('Vitrine.nav_properties') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'about' ? 'active' : '' ?>"
                       href="<?= base_url($currentLang . '/about') ?>">
                        <?= lang('Vitrine.nav_about') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'blog' ? 'active' : '' ?>"
                       href="<?= base_url($currentLang . '/blog') ?>">
                        <?= lang('Vitrine.nav_blog') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'contact' ? 'active' : '' ?>"
                       href="<?= base_url($currentLang . '/contact') ?>">
                        <?= lang('Vitrine.nav_contact') ?>
                    </a>
                </li>
            </ul>

            <!-- Right side -->
            <div class="d-flex align-items-center gap-3">
                <!-- Language switcher -->
                <div class="dropdown rb-lang-switcher">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                        <?= strtoupper($currentLang) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item <?= $currentLang === 'fr' ? 'active' : '' ?>"
                               href="<?= base_url('fr/' . ($langSwitchUri ?? '')) ?>">🇫🇷 Français</a></li>
                        <li><a class="dropdown-item <?= $currentLang === 'en' ? 'active' : '' ?>"
                               href="<?= base_url('en/' . ($langSwitchUri ?? '')) ?>">🇬🇧 English</a></li>
                        <li><a class="dropdown-item <?= $currentLang === 'ar' ? 'active' : '' ?>"
                               href="<?= base_url('ar/' . ($langSwitchUri ?? '')) ?>">🇲🇦 العربية</a></li>
                    </ul>
                </div>

                <!-- Estimate CTA -->
                <a href="<?= base_url($currentLang . '/estimate') ?>" class="btn rb-btn-accent btn-sm">
                    <i class="bi bi-calculator me-1"></i><?= lang('Vitrine.nav_estimate') ?>
                </a>

                <!-- Admin login -->
                <a href="<?= base_url('login') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-person-circle me-1"></i><?= lang('Vitrine.nav_admin') ?>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- =========================================================
     MAIN CONTENT
========================================================= -->
<main>
    <?= $this->renderSection('content') ?>
</main>

<!-- =========================================================
     FOOTER
========================================================= -->
<footer class="rb-footer">
    <div class="container">
        <div class="row g-4">
            <!-- Brand -->
            <div class="col-lg-4 col-md-6">
                <div class="rb-logo mb-3">
                    <span class="rb-logo-primary fs-4">Reben</span><span class="rb-logo-accent fs-4">cia</span>
                </div>
                <p class="text-white-50 small"><?= lang('Vitrine.footer_about') ?></p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="rb-social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="rb-social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="rb-social-icon"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="rb-social-icon"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Quick links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="rb-footer-title"><?= lang('Vitrine.footer_links') ?></h6>
                <ul class="list-unstyled rb-footer-links">
                    <li><a href="<?= base_url($currentLang . '/') ?>"><?= lang('Vitrine.nav_home') ?></a></li>
                    <li><a href="<?= base_url($currentLang . '/properties') ?>"><?= lang('Vitrine.nav_properties') ?></a></li>
                    <li><a href="<?= base_url($currentLang . '/about') ?>"><?= lang('Vitrine.nav_about') ?></a></li>
                    <li><a href="<?= base_url($currentLang . '/blog') ?>"><?= lang('Vitrine.nav_blog') ?></a></li>
                    <li><a href="<?= base_url($currentLang . '/estimate') ?>"><?= lang('Vitrine.nav_estimate') ?></a></li>
                </ul>
            </div>

            <!-- Property types -->
            <div class="col-lg-2 col-md-6">
                <h6 class="rb-footer-title"><?= lang('Vitrine.nav_properties') ?></h6>
                <ul class="list-unstyled rb-footer-links">
                    <li><a href="<?= base_url($currentLang . '/properties?type=appartement') ?>"><?= lang('Vitrine.search_type_apartment') ?></a></li>
                    <li><a href="<?= base_url($currentLang . '/properties?type=villa') ?>"><?= lang('Vitrine.search_type_villa') ?></a></li>
                    <li><a href="<?= base_url($currentLang . '/properties?type=terrain') ?>"><?= lang('Vitrine.search_type_terrain') ?></a></li>
                    <li><a href="<?= base_url($currentLang . '/properties?type=commercial') ?>"><?= lang('Vitrine.search_type_commercial') ?></a></li>
                    <li><a href="<?= base_url($currentLang . '/properties?type=bureau') ?>"><?= lang('Vitrine.search_type_bureau') ?></a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-4 col-md-6">
                <h6 class="rb-footer-title"><?= lang('Vitrine.footer_contact') ?></h6>
                <ul class="list-unstyled rb-footer-links">
                    <li><i class="bi bi-geo-alt me-2 text-warning"></i>123 Boulevard Mohammed V, Casablanca</li>
                    <li><a href="tel:+212522000000"><i class="bi bi-telephone me-2 text-warning"></i>+212 5 22 00 00 00</a></li>
                    <li><a href="mailto:contact@rebencia.com"><i class="bi bi-envelope me-2 text-warning"></i>contact@rebencia.com</a></li>
                    <li><i class="bi bi-clock me-2 text-warning"></i><?= lang('Vitrine.contact_hours_val') ?></li>
                </ul>
            </div>
        </div>

        <hr class="rb-footer-divider">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <small class="text-white-50">
                    <?= str_replace('{year}', date('Y'), lang('Vitrine.footer_rights')) ?>
                </small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small>
                    <a href="#" class="text-white-50 me-3"><?= lang('Vitrine.footer_legal') ?></a>
                    <a href="#" class="text-white-50"><?= lang('Vitrine.footer_privacy') ?></a>
                </small>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Vitrine JS -->
<script src="<?= base_url('assets/js/vitrine.js') ?>"></script>
<?= $this->renderSection('scripts') ?>

</body>
</html>
