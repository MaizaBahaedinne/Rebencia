<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="orpi-hero" style="padding: 80px 0;">
    <div class="container orpi-hero-content">
        <div class="text-center">
            <h1 class="display-3 fw-bold mb-3">À propos de REBENCIA</h1>
            <p class="lead fs-4">Votre partenaire immobilier de confiance en Tunisie</p>
        </div>
    </div>
</section>

<!-- Notre Histoire -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="<?= base_url('assets/images/about-us.jpg') ?>" alt="REBENCIA" class="img-fluid rounded shadow" 
                     style="width: 100%; height: 400px; object-fit: cover;"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22600%22 height=%22400%22%3E%3Crect width=%22600%22 height=%22400%22 fill=%22%23667eea%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2240%22 fill=%22white%22%3EREBENCIA%3C/text%3E%3C/svg%3E'">
            </div>
            <div class="col-md-6">
                <h2 class="display-5 fw-bold mb-4">Notre Histoire</h2>
                <p class="lead">
                    Depuis plus de 10 ans, <strong>REBENCIA</strong> accompagne ses clients dans leurs projets immobiliers les plus importants.
                </p>
                <p>
                    Fondée en 2014, notre agence s'est rapidement imposée comme un acteur majeur de l'immobilier de prestige en Tunisie. 
                    Notre expertise couvre l'ensemble du territoire tunisien, des prestigieux quartiers de la capitale aux stations balnéaires les plus recherchées.
                </p>
                <p>
                    Avec une équipe de professionnels passionnés et expérimentés, nous mettons notre savoir-faire au service de votre réussite, 
                    que vous souhaitiez acheter, vendre ou louer un bien immobilier.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Nos Valeurs -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Nos Valeurs</h2>
            <p class="lead text-muted">Ce qui nous guide au quotidien</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem;">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4 class="mb-3">Confiance</h4>
                    <p class="text-muted">
                        La transparence et l'honnêteté sont au cœur de notre approche. Nous bâtissons des relations durables basées sur la confiance mutuelle.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h4 class="mb-3">Professionnalisme</h4>
                    <p class="text-muted">
                        Notre équipe est formée aux meilleures pratiques du secteur. Nous vous garantissons un service de qualité à chaque étape.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="mb-3">Proximité</h4>
                    <p class="text-muted">
                        Nous sommes à votre écoute et disponibles pour répondre à toutes vos questions. Votre satisfaction est notre priorité.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notre Expertise -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Notre Expertise</h2>
            <p class="lead text-muted">Des services complets pour tous vos besoins</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center p-4">
                    <i class="fas fa-home fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Vente</h5>
                    <p class="text-muted">Accompagnement complet de la mise en vente à la signature</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center p-4">
                    <i class="fas fa-key fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Location</h5>
                    <p class="text-muted">Gestion locative et recherche de locataires qualifiés</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center p-4">
                    <i class="fas fa-calculator fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Estimation</h5>
                    <p class="text-muted">Évaluation précise de votre bien par nos experts</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="text-center p-4">
                    <i class="fas fa-users fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h5>Conseil</h5>
                    <p class="text-muted">Expertise juridique et fiscale pour vos projets</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chiffres Clés -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">REBENCIA en Chiffres</h2>
        </div>
        
        <div class="row text-center">
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="p-4">
                    <h2 class="display-3 fw-bold">10+</h2>
                    <p class="fs-5">Années d'expérience</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="p-4">
                    <h2 class="display-3 fw-bold">500+</h2>
                    <p class="fs-5">Biens vendus</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="p-4">
                    <h2 class="display-3 fw-bold">1000+</h2>
                    <p class="fs-5">Clients satisfaits</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <h2 class="display-3 fw-bold">50+</h2>
                    <p class="fs-5">Agences partenaires</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notre Équipe -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Notre Équipe</h2>
            <p class="lead text-muted">Des professionnels à votre service</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="text-center">
                    <div style="width: 150px; height: 150px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 3rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4>Direction</h4>
                    <p class="text-muted">Vision stratégique et développement</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div style="width: 150px; height: 150px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 3rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Conseillers</h4>
                    <p class="text-muted">Accompagnement personnalisé de vos projets</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center">
                    <div style="width: 150px; height: 150px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 3rem;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>Support</h4>
                    <p class="text-muted">Service client réactif et disponible</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Prêt à démarrer votre projet ?</h2>
        <p class="lead mb-4">Contactez-nous dès aujourd'hui pour une consultation gratuite</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="<?= base_url('contact') ?>" class="btn btn-orpi-primary btn-lg">
                <i class="fas fa-envelope me-2"></i> Contactez-nous
            </a>
            <a href="<?= base_url('properties') ?>" class="btn btn-orpi-secondary btn-lg">
                <i class="fas fa-search me-2"></i> Voir nos biens
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
