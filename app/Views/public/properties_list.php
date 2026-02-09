<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4" style="background: var(--primary-color); color: white;">
    <div class="container">
        <h1 class="h3 mb-0">Toutes les propriétés</h1>
        <p class="mb-0"><?= count($properties) ?> propriété(s) disponible(s)</p>
    </div>
</section>

<!-- Properties List -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($properties)): ?>
            <div class="row g-4">
                <?php foreach ($properties as $property): ?>
                    <div class="col-md-4">
                        <div class="card h-100 property-card">
                            <?php 
                            $imageSrc = !empty($property['main_image']) && !empty($property['main_image']['file_path']) 
                                ? base_url('uploads/properties/' . $property['main_image']['file_path'])
                                : base_url('uploads/properties/placeholder.jpg');
                            ?>
                            <img src="<?= $imageSrc ?>" class="card-img-top" alt="<?= esc($property['title']) ?>" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary"><?= ucfirst($property['type']) ?></span>
                                    <span class="badge bg-success"><?= ucfirst($property['transaction_type']) ?></span>
                                </div>
                                <h5 class="card-title"><?= esc($property['title']) ?></h5>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-map-marker-alt"></i> <?= esc($property['city']) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 text-primary mb-0"><?= number_format($property['price'], 0, ',', ' ') ?> TND</span>
                                </div>
                                <div class="d-flex gap-3 text-muted small mb-3">
                                    <?php if (isset($property['surface']) && $property['surface']): ?>
                                        <span><i class="fas fa-ruler-combined"></i> <?= $property['surface'] ?> m²</span>
                                    <?php endif; ?>
                                    <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
                                        <span><i class="fas fa-bed"></i> <?= $property['bedrooms'] ?></span>
                                    <?php endif; ?>
                                    <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
                                        <span><i class="fas fa-bath"></i> <?= $property['bathrooms'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= base_url('properties/' . $property['reference']) ?>" class="btn btn-primary w-100">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucune propriété disponible pour le moment.
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.property-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
</style>

<?= $this->endSection() ?>
