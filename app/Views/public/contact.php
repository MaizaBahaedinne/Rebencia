<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="orpi-hero" style="padding: 80px 0;">
    <div class="container orpi-hero-content">
        <div class="text-center">
            <h1 class="display-3 fw-bold mb-3">Contactez-nous</h1>
            <p class="lead fs-4">Nous sommes à votre écoute pour répondre à toutes vos questions</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Formulaire de contact -->
            <div class="col-lg-7">
                <div class="bg-white p-4 p-md-5 rounded shadow">
                    <h2 class="mb-4">Envoyez-nous un message</h2>
                    
                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('contact/send') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Nom complet *</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                       value="<?= old('name') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email *</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                       value="<?= old('email') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Téléphone</label>
                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone" 
                                       value="<?= old('phone') ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="subject" class="form-label fw-semibold">Sujet *</label>
                                <select class="form-select form-select-lg" id="subject" name="subject" required>
                                    <option value="">Choisissez un sujet</option>
                                    <option value="Acheter un bien" <?= old('subject') === 'Acheter un bien' ? 'selected' : '' ?>>Acheter un bien</option>
                                    <option value="Vendre un bien" <?= old('subject') === 'Vendre un bien' ? 'selected' : '' ?>>Vendre un bien</option>
                                    <option value="Louer un bien" <?= old('subject') === 'Louer un bien' ? 'selected' : '' ?>>Louer un bien</option>
                                    <option value="Estimation gratuite" <?= old('subject') === 'Estimation gratuite' ? 'selected' : '' ?>>Estimation gratuite</option>
                                    <option value="Information générale" <?= old('subject') === 'Information générale' ? 'selected' : '' ?>>Information générale</option>
                                    <option value="Autre" <?= old('subject') === 'Autre' ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label fw-semibold">Message *</label>
                                <textarea class="form-control form-control-lg" id="message" name="message" 
                                          rows="6" required><?= old('message') ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-orpi-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Envoyer le message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Informations de contact -->
            <div class="col-lg-5">
                <div class="mb-4">
                    <h3 class="mb-4">Nos Coordonnées</h3>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-map-marker-alt fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h5>Adresse</h5>
                            <p class="text-muted mb-0">
                                Avenue Habib Bourguiba<br>
                                1000 Tunis, Tunisie
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-phone fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h5>Téléphone</h5>
                            <p class="text-muted mb-0">
                                <a href="tel:+21670123456" class="text-decoration-none">+216 70 123 456</a><br>
                                <a href="tel:+21620123456" class="text-decoration-none">+216 20 123 456</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-envelope fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h5>Email</h5>
                            <p class="text-muted mb-0">
                                <a href="mailto:contact@rebencia.com" class="text-decoration-none">contact@rebencia.com</a><br>
                                <a href="mailto:info@rebencia.com" class="text-decoration-none">info@rebencia.com</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h5>Horaires</h5>
                            <p class="text-muted mb-0">
                                Lundi - Vendredi: 9h - 18h<br>
                                Samedi: 9h - 13h<br>
                                Dimanche: Fermé
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-light p-4 rounded">
                    <h4 class="mb-3">Suivez-nous</h4>
                    <div class="d-flex gap-3">
                        <a href="#" class="orpi-social-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="orpi-social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="orpi-social-link">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="orpi-social-link">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-0">
    <div class="container-fluid p-0">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3193.527687!2d10.1815!3d36.8065!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzbCsDQ4JzIzLjQiTiAxMMKwMTAnNTMuNCJF!5e0!3m2!1sfr!2stn!4v1234567890"
            width="100%" 
            height="450" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Questions Fréquentes</h2>
            <p class="lead text-muted">Trouvez rapidement des réponses à vos questions</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Comment estimer gratuitement mon bien ?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Contactez-nous via le formulaire ci-dessus ou par téléphone. Un de nos experts vous contactera rapidement pour organiser une visite et vous fournir une estimation précise de votre bien.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Quels sont vos frais d'agence ?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Nos frais varient selon le type de transaction et le bien concerné. Contactez-nous pour un devis personnalisé et transparent.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Combien de temps pour vendre mon bien ?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Le délai de vente dépend de nombreux facteurs : localisation, prix, état du bien, marché actuel. En moyenne, nos biens se vendent en 3 à 6 mois.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Proposez-vous un service de gestion locative ?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Oui, nous proposons un service complet de gestion locative incluant la recherche de locataires, la rédaction du bail, et la gestion administrative.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
