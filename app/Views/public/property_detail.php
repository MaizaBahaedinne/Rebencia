<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Image Gallery Section -->
<section class="position-relative" style="background: #000;">
    <?php if (!empty($images)): ?>
        <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($images as $index => $image): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= base_url('uploads/properties/' . $image['file_path']) ?>" 
                             class="d-block w-100" 
                             alt="<?= esc($image['title']) ?>"
                             style="height: 600px; object-fit: contain;">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($images) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
                <div class="position-absolute bottom-0 end-0 m-3 bg-dark bg-opacity-75 text-white px-3 py-2 rounded">
                    <i class="fas fa-images"></i> <span id="currentSlide">1</span> / <?= count($images) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <img src="<?= base_url('uploads/properties/placeholder.jpg') ?>" 
             class="d-block w-100" 
             alt="Pas d'image"
             style="height: 600px; object-fit: cover;">
    <?php endif; ?>
</section>

<!-- Main Content -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <!-- Left Column - Property Details -->
            <div class="col-lg-8">
                <!-- Property Header -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <span class="badge bg-primary"><?= ucfirst($property['type']) ?></span>
                            <span class="badge bg-success"><?= ucfirst($property['transaction_type']) ?></span>
                            <?php if (isset($property['available']) && $property['available']): ?>
                                <span class="badge bg-info">Disponible</span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="h3 mb-3"><?= esc($property['title']) ?></h1>
                        
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt"></i> 
                            <?= esc($property['address'] ?? '') ?>, <?= esc($property['city'] ?? '') ?>
                            <?php if (isset($property['governorate']) && $property['governorate']): ?>
                                , <?= esc($property['governorate']) ?>
                            <?php endif; ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="h2 text-primary mb-0"><?= number_format($property['price'], 0, ',', ' ') ?> TND</h2>
                                <?php if ($property['transaction_type'] === 'rent'): ?>
                                    <small class="text-muted">par mois</small>
                                <?php endif; ?>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Réf: <?= esc($property['reference']) ?></small>
                                <?php if (isset($property['year_built']) && $property['year_built']): ?>
                                    <small class="text-muted d-block">Année: <?= $property['year_built'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Features -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Caractéristiques principales</h5>
                        <div class="row g-4">
                            <?php if (isset($property['surface']) && $property['surface']): ?>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-ruler-combined fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0"><?= $property['surface'] ?> m²</h6>
                                        <small class="text-muted">Surface</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-bed fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0"><?= $property['bedrooms'] ?></h6>
                                        <small class="text-muted">Chambres</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-bath fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0"><?= $property['bathrooms'] ?></h6>
                                        <small class="text-muted">Salles de bain</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['floor']) && $property['floor']): ?>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-building fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0"><?= $property['floor'] ?></h6>
                                        <small class="text-muted">Étage</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Description</h5>
                        <div class="property-description">
                            <?= nl2br(esc($property['description'] ?? '')) ?>
                        </div>
                    </div>
                </div>

                <!-- Detailed Characteristics -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Caractéristiques détaillées</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Type de bien</td>
                                            <td class="fw-bold"><?= ucfirst($property['type']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Transaction</td>
                                            <td class="fw-bold"><?= $property['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?></td>
                                        </tr>
                                        <?php if (isset($property['surface']) && $property['surface']): ?>
                                        <tr>
                                            <td class="text-muted">Surface habitable</td>
                                            <td class="fw-bold"><?= $property['surface'] ?> m²</td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
                                        <tr>
                                            <td class="text-muted">Nombre de chambres</td>
                                            <td class="fw-bold"><?= $property['bedrooms'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
                                        <tr>
                                            <td class="text-muted">Salles de bain</td>
                                            <td class="fw-bold"><?= $property['bathrooms'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <?php if (isset($property['floor']) && $property['floor']): ?>
                                        <tr>
                                            <td class="text-muted">Étage</td>
                                            <td class="fw-bold"><?= $property['floor'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['parking_spaces']) && $property['parking_spaces']): ?>
                                        <tr>
                                            <td class="text-muted">Parking</td>
                                            <td class="fw-bold"><?= $property['parking_spaces'] ?> place(s)</td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (isset($property['year_built']) && $property['year_built']): ?>
                                        <tr>
                                            <td class="text-muted">Année de construction</td>
                                            <td class="fw-bold"><?= $property['year_built'] ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td class="text-muted">Référence</td>
                                            <td class="fw-bold"><?= esc($property['reference']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Disponibilité</td>
                                            <td class="fw-bold">
                                                <?= (isset($property['available']) && $property['available']) ? 
                                                    '<span class="text-success">Disponible</span>' : 
                                                    '<span class="text-danger">Non disponible</span>' ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rooms -->
                <?php if (!empty($rooms)): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Composition du bien</h5>
                        <div class="row g-3">
                            <?php foreach ($rooms as $room): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start p-3 border rounded">
                                        <i class="fas fa-door-open text-primary me-3 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($room['name'] ?? $room['room_type'] ?? 'Pièce') ?></h6>
                                            <?php if (isset($room['surface']) && $room['surface']): ?>
                                                <p class="mb-0 text-muted small"><?= $room['surface'] ?> m²</p>
                                            <?php endif; ?>
                                            <?php if (isset($room['description']) && $room['description']): ?>
                                                <p class="mb-0 text-muted small"><?= esc($room['description']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Proximities -->
                <?php if (!empty($proximities)): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-location-arrow text-primary"></i> Proximités
                        </h5>
                        <div class="row g-2">
                            <?php foreach ($proximities as $proximity): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span><?= esc($proximity['name'] ?? $proximity['proximity_type'] ?? 'Proximité') ?></span>
                                        <?php if (isset($proximity['distance']) && $proximity['distance']): ?>
                                            <span class="ms-auto text-muted small"><?= $proximity['distance'] ?> m</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Energy Performance -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-leaf text-success"></i> Performance énergétique
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="small text-muted mb-2">Consommation énergétique</h6>
                                <div class="energy-scale">
                                    <?php
                                    $energy_classes = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                                    $energy_class = $property['energy_class'] ?? 'N/A';
                                    foreach ($energy_classes as $class):
                                        $active = ($class === $energy_class) ? 'active' : '';
                                    ?>
                                        <div class="energy-bar energy-<?= strtolower($class) ?> <?= $active ?>">
                                            <span><?= $class ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="small text-muted mb-2">Émissions de gaz à effet de serre</h6>
                                <div class="energy-scale">
                                    <?php
                                    $ges_class = $property['ges_class'] ?? 'N/A';
                                    foreach ($energy_classes as $class):
                                        $active = ($class === $ges_class) ? 'active' : '';
                                    ?>
                                        <div class="energy-bar energy-<?= strtolower($class) ?> <?= $active ?>">
                                            <span><?= $class ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!isset($property['energy_class']) || $property['energy_class'] === null): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> Informations non disponibles pour ce bien
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Contact Forms -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 20px;">
                    <!-- Visit Request Form -->
                    <div class="card mb-3 shadow">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-calendar-check text-primary"></i> Demander une visite
                            </h5>
                            <form id="visitForm">
                                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                <input type="hidden" name="request_type" value="visit">
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="name" placeholder="Nom complet *" required>
                                </div>
                                <div class="mb-3">
                                    <input type="email" class="form-control" name="email" placeholder="Email *" required>
                                </div>
                                <div class="mb-3">
                                    <input type="tel" class="form-control" name="phone" placeholder="Téléphone *" required>
                                </div>
                                <div class="mb-3">
                                    <input type="date" class="form-control" name="visit_date" placeholder="Date souhaitée">
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" name="message" rows="3" placeholder="Message (optionnel)"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-paper-plane"></i> Demander une visite
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Information Request Form -->
                    <div class="card mb-3 shadow">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-envelope text-primary"></i> Demande d'information
                            </h5>
                            <form id="infoForm">
                                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                <input type="hidden" name="request_type" value="information">
                                
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="name" placeholder="Nom complet *" required>
                                </div>
                                <div class="mb-3">
                                    <input type="email" class="form-control" name="email" placeholder="Email *" required>
                                </div>
                                <div class="mb-3">
                                    <input type="tel" class="form-control" name="phone" placeholder="Téléphone *" required>
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" name="message" rows="4" placeholder="Votre message *" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-info-circle"></i> Demander des informations
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="card shadow">
                        <div class="card-body">
                            <h6 class="card-title mb-3">Partager ce bien</h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-grow-1" onclick="shareProperty('facebook')">
                                    <i class="fab fa-facebook"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info flex-grow-1" onclick="shareProperty('twitter')">
                                    <i class="fab fa-twitter"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success flex-grow-1" onclick="shareProperty('whatsapp')">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary flex-grow-1" onclick="copyLink()">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Similar Properties -->
<?php if (!empty($similar_properties)): ?>
<section class="py-5 bg-white">
    <div class="container">
        <h3 class="mb-4">
            <i class="fas fa-home text-primary"></i> Publications similaires
        </h3>
        <div class="row g-4">
            <?php foreach ($similar_properties as $simProperty): ?>
                <div class="col-md-4">
                    <div class="card h-100 property-card">
                        <?php 
                        $imageSrc = !empty($simProperty['main_image']) && !empty($simProperty['main_image']['file_path']) 
                            ? base_url('uploads/properties/' . $simProperty['main_image']['file_path'])
                            : base_url('uploads/properties/placeholder.jpg');
                        ?>
                        <img src="<?= $imageSrc ?>" class="card-img-top" alt="<?= esc($simProperty['title']) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge bg-primary"><?= ucfirst($simProperty['type']) ?></span>
                                <span class="badge bg-success"><?= ucfirst($simProperty['transaction_type']) ?></span>
                            </div>
                            <h6 class="card-title"><?= esc($simProperty['title']) ?></h6>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt"></i> <?= esc($simProperty['city']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-primary fw-bold"><?= number_format($simProperty['price'], 0, ',', ' ') ?> TND</span>
                            </div>
                            <div class="d-flex gap-3 text-muted small mb-3">
                                <?php if (isset($simProperty['surface']) && $simProperty['surface']): ?>
                                    <span><i class="fas fa-ruler-combined"></i> <?= $simProperty['surface'] ?> m²</span>
                                <?php endif; ?>
                                <?php if (isset($simProperty['bedrooms']) && $simProperty['bedrooms']): ?>
                                    <span><i class="fas fa-bed"></i> <?= $simProperty['bedrooms'] ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url('properties/' . $simProperty['reference']) ?>" class="btn btn-outline-primary btn-sm w-100">
                                Voir ce bien
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
.property-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.property-description {
    line-height: 1.8;
}

.energy-scale {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.energy-bar {
    display: flex;
    align-items: center;
    padding: 8px 15px;
    border-radius: 4px;
    font-weight: bold;
    color: white;
    position: relative;
    opacity: 0.3;
}

.energy-bar.active {
    opacity: 1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.energy-bar span {
    font-size: 14px;
}

.energy-a { background: #319834; width: 40%; }
.energy-b { background: #4db34d; width: 50%; }
.energy-c { background: #b8d234; width: 60%; }
.energy-d { background: #f9e547; width: 70%; }
.energy-e { background: #f5c933; width: 80%; }
.energy-f { background: #f39333; width: 90%; }
.energy-g { background: #e8212e; width: 100%; }

.sticky-top {
    position: sticky;
    top: 20px;
}

@media (max-width: 991px) {
    .sticky-top {
        position: relative;
        top: 0;
    }
}
</style>

<script>
// Carousel slide counter
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('propertyCarousel');
    if (carousel) {
        carousel.addEventListener('slid.bs.carousel', function (e) {
            const currentSlide = document.getElementById('currentSlide');
            if (currentSlide) {
                currentSlide.textContent = e.to + 1;
            }
        });
    }
    
    // Visit Form
    const visitForm = document.getElementById('visitForm');
    if (visitForm) {
        visitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would send the form data via AJAX
            alert('Demande de visite envoyée avec succès!');
            this.reset();
        });
    }
    
    // Info Form
    const infoForm = document.getElementById('infoForm');
    if (infoForm) {
        infoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would send the form data via AJAX
            alert('Demande d\'information envoyée avec succès!');
            this.reset();
        });
    }
});

function shareProperty(platform) {
    const url = window.location.href;
    const title = '<?= addslashes($property['title']) ?>';
    
    let shareUrl;
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Lien copié dans le presse-papier!');
    });
}
</script>

<?= $this->endSection() ?>
