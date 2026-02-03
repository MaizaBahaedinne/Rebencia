<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 100px 0; color: white;">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Trouvez votre propriété de rêve</h1>
        <p class="lead mb-5">Des milliers de biens immobiliers à vendre et à louer en Tunisie</p>
        
        <!-- Quick Search -->
        <div class="card shadow-lg" style="max-width: 900px; margin: 0 auto;">
            <div class="card-body p-4">
                <form action="<?= base_url('search') ?>" method="get">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="transaction_type" class="form-select">
                                <option value="">Type</option>
                                <option value="sale">Vente</option>
                                <option value="rent">Location</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">Catégorie</option>
                                <option value="apartment">Appartement</option>
                                <option value="villa">Villa</option>
                                <option value="house">Maison</option>
                                <option value="land">Terrain</option>
                                <option value="office">Bureau</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="city" class="form-control" placeholder="Ville">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Propriétés en vedette</h2>
        <div class="row g-4">
            <?php if (!empty($featured_properties)): ?>
                <?php foreach ($featured_properties as $property): ?>
                    <div class="col-md-4">
                        <div class="card property-card h-100">
                            <img src="<?= base_url('uploads/properties/placeholder.jpg') ?>" class="card-img-top" alt="<?= esc($property['title']) ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2"><?= ucfirst($property['type']) ?></span>
                                <h5 class="card-title"><?= esc($property['title']) ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i> <?= esc($property['city']) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">
                                        <?= number_format($property['price'], 0, ',', ' ') ?> TND
                                    </span>
                                    <a href="<?= base_url('properties/' . $property['reference']) ?>" class="btn btn-sm btn-outline-primary">
                                        Voir détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center text-muted">Aucune propriété en vedette pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-home fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">500+</h3>
                    <p class="text-muted">Propriétés</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">1000+</h3>
                    <p class="text-muted">Clients satisfaits</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-building fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">50+</h3>
                    <p class="text-muted">Agences</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">300+</h3>
                    <p class="text-muted">Transactions</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Properties -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Dernières propriétés</h2>
        <div class="row g-4">
            <?php if (!empty($latest_properties)): ?>
                <?php foreach ($latest_properties as $property): ?>
                    <div class="col-md-3">
                        <div class="card property-card h-100">
                            <img src="<?= base_url('uploads/properties/placeholder.jpg') ?>" class="card-img-top" alt="<?= esc($property['title']) ?>" style="height: 150px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title"><?= esc(substr($property['title'], 0, 40)) ?>...</h6>
                                <p class="text-primary fw-bold mb-0">
                                    <?= number_format($property['price'], 0, ',', ' ') ?> TND
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= base_url('properties') ?>" class="btn btn-primary btn-lg">
                Voir toutes les propriétés <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
