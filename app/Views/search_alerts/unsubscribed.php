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
                    <h1 class="display-5 fw-bold mb-3">Désabonnement réussi</h1>
                    <p class="lead text-muted mb-4">
                        Votre alerte de recherche a été désactivée avec succès. Vous ne recevrez plus de notifications 
                        pour cette alerte.
                    </p>
                    
                    <div class="alert alert-info">
                        <p class="mb-0">
                            Vous pouvez créer une nouvelle alerte à tout moment en définissant vos nouveaux critères de recherche.
                        </p>
                    </div>
                    
                    <div class="mt-5">
                        <a href="<?= base_url('/') ?>" class="btn btn-orpi-primary btn-lg me-2">
                            <i class="fas fa-home me-2"></i> Retour à l'accueil
                        </a>
                        <a href="<?= base_url('creer-une-alerte') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-bell me-2"></i> Créer une nouvelle alerte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
