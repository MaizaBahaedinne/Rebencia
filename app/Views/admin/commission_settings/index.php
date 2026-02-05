<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-dollar-sign"></i> Gestion des Commissions
    </h1>
</div>

<div class="row">
    <!-- Règles de Commission -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-cogs fa-4x text-primary"></i>
                </div>
                <h4 class="card-title">Règles de Commission</h4>
                <p class="text-muted">
                    Gérer les taux de commission par défaut pour chaque type de transaction et de bien.
                </p>
                <a href="<?= base_url('admin/commission-settings/rules') ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i> Accéder
                </a>
            </div>
        </div>
    </div>

    <!-- Surcharges -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-layer-group fa-4x text-warning"></i>
                </div>
                <h4 class="card-title">Exceptions Personnalisées</h4>
                <p class="text-muted">
                    Définir des taux spécifiques par agence, rôle ou utilisateur.
                </p>
                <a href="<?= base_url('admin/commission-settings/overrides') ?>" class="btn btn-warning">
                    <i class="fas fa-arrow-right"></i> Accéder
                </a>
            </div>
        </div>
    </div>

    <!-- Simulateur -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-calculator fa-4x text-success"></i>
                </div>
                <h4 class="card-title">Simulateur</h4>
                <p class="text-muted">
                    Calculer et prévisualiser les commissions avant application.
                </p>
                <a href="<?= base_url('admin/commission-settings/simulate') ?>" class="btn btn-success">
                    <i class="fas fa-arrow-right"></i> Accéder
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Comment fonctionne le système ?</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-primary">1. Règles par Défaut</h6>
                        <p class="text-muted small">
                            Les règles système définissent les taux de base pour chaque type de transaction.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-warning">2. Hiérarchie des Exceptions</h6>
                        <p class="text-muted small">
                            Utilisateur > Rôle > Agence > Système. Les règles les plus spécifiques l'emportent.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-success">3. Calcul Automatique</h6>
                        <p class="text-muted small">
                            Lors d'une transaction, le système applique automatiquement la règle appropriée avec TVA.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
