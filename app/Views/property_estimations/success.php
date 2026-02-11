<?= $this->extend('layouts/public_orpi_style') ?>

<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto text-center">
                <div class="py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h1 class="display-5 fw-bold mb-3">Demande envoyée avec succès !</h1>
                    <p class="lead text-muted mb-4">
                        Merci pour votre demande d'estimation. Notre équipe va l'examiner et vous contactera très prochainement 
                        pour vous fournir une estimation détaillée de votre bien.
                    </p>
                    
                    <div class="alert alert-info text-start">
                        <h5><i class="fas fa-info-circle me-2"></i> Prochaines étapes</h5>
                        <ul class="mb-0">
                            <li>Analyse de votre demande par nos experts (24-48h)</li>
                            <li>Étude comparative du marché dans votre zone</li>
                            <li>Prise de contact pour programmer une visite si nécessaire</li>
                            <li>Remise de votre estimation personnalisée</li>
                        </ul>
                    </div>
                    
                    <div class="mt-5">
                        <a href="<?= base_url('/') ?>" class="btn btn-orpi-primary btn-lg me-2">
                            <i class="fas fa-home me-2"></i> Retour à l'accueil
                        </a>
                        <a href="<?= base_url('properties') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-search me-2"></i> Voir nos biens
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
