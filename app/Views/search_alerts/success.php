<?= $this->extend('layouts/public_orpi_style') ?>

<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto text-center">
                <div class="py-5">
                    <div class="mb-4">
                        <i class="fas fa-bell text-success" style="font-size: 80px;"></i>
                    </div>
                    <h1 class="display-5 fw-bold mb-3">Alerte créée avec succès !</h1>
                    <p class="lead text-muted mb-4">
                        Votre alerte de recherche est maintenant active. Vous recevrez des notifications par email 
                        dès qu'un bien correspondant à vos critères sera disponible.
                    </p>
                    
                    <div class="alert alert-info text-start">
                        <h5><i class="fas fa-info-circle me-2"></i> À propos de votre alerte</h5>
                        <ul class="mb-0">
                            <li>Vous recevrez les notifications selon la fréquence choisie</li>
                            <li>Un lien de désabonnement est inclus dans chaque email</li>
                            <li>Vous pouvez modifier vos préférences à tout moment</li>
                            <li>Vos données restent strictement confidentielles</li>
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
