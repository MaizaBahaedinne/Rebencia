<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Property Details -->
<section class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('properties') ?>">Propriétés</a></li>
                <li class="breadcrumb-item active"><?= esc($property['reference']) ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Image Gallery -->
            <div class="col-lg-8">
                <?php if (!empty($images)): ?>
                    <div id="propertyCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($images as $index => $image): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= base_url('uploads/properties/' . $image['file_path']) ?>" 
                                         class="d-block w-100" 
                                         alt="<?= esc($image['title']) ?>"
                                         style="height: 500px; object-fit: cover; border-radius: 10px;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Précédent</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Suivant</span>
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Thumbnails -->
                    <?php if (count($images) > 1): ?>
                        <div class="row g-2 mb-4">
                            <?php foreach ($images as $index => $image): ?>
                                <div class="col-2">
                                    <img src="<?= base_url('uploads/properties/' . $image['file_path']) ?>" 
                                         class="img-thumbnail" 
                                         style="cursor: pointer; height: 80px; width: 100%; object-fit: cover;"
                                         onclick="document.querySelector('#propertyCarousel').carousel(<?= $index ?>)">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <img src="<?= base_url('uploads/properties/placeholder.jpg') ?>" 
                         class="img-fluid rounded mb-4" 
                         alt="Pas d'image"
                         style="height: 500px; width: 100%; object-fit: cover;">
                <?php endif; ?>

                <!-- Property Description -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title mb-3"><?= esc($property['title']) ?></h2>
                        
                        <div class="d-flex gap-2 mb-3">
                            <span class="badge bg-primary"><?= ucfirst($property['type']) ?></span>
                            <span class="badge bg-success"><?= ucfirst($property['transaction_type']) ?></span>
                            <span class="badge bg-info text-dark">Réf: <?= esc($property['reference']) ?></span>
                        </div>

                        <div class="mb-4">
                            <h3 class="h4 text-primary mb-0"><?= number_format($property['price'], 0, ',', ' ') ?> TND</h3>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?= esc($property['address'] ?? '') ?>, <?= esc($property['city'] ?? '') ?>
                                <?php if (isset($property['governorate']) && $property['governorate']): ?>
                                    , <?= esc($property['governorate']) ?>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="row g-3 mb-4">
                            <?php if (isset($property['surface']) && $property['surface']): ?>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-ruler-combined fa-2x text-primary mb-2"></i>
                                        <h5 class="mb-0"><?= $property['surface'] ?> m²</h5>
                                        <small class="text-muted">Surface</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-bed fa-2x text-primary mb-2"></i>
                                        <h5 class="mb-0"><?= $property['bedrooms'] ?></h5>
                                        <small class="text-muted">Chambres</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-bath fa-2x text-primary mb-2"></i>
                                        <h5 class="mb-0"><?= $property['bathrooms'] ?></h5>
                                        <small class="text-muted">Salles de bain</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($property['floor']) && $property['floor']): ?>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-building fa-2x text-primary mb-2"></i>
                                        <h5 class="mb-0"><?= $property['floor'] ?></h5>
                                        <small class="text-muted">Étage</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h4 class="mb-3">Description</h4>
                        <div class="mb-4">
                            <?= nl2br(esc($property['description'])) ?>
                        </div>

                        <?php if (!empty($rooms)): ?>
                            <h4 class="mb-3">Pièces</h4>
                            <div class="row g-3 mb-4">
                                <?php foreach ($rooms as $room): ?>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= esc($room['name'] ?? $room['room_type'] ?? 'Pièce') ?></h6>
                                                <p class="card-text small text-muted mb-0">
                                                    <?php if (isset($room['surface']) && $room['surface']): ?>
                                                        <?= $room['surface'] ?> m²
                                                    <?php endif; ?>
                                                    <?php if (isset($room['description']) && $room['description']): ?>
                                                        <br><?= esc($room['description']) ?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($proximities)): ?>
                            <h4 class="mb-3">Proximités</h4>
                            <div class="row g-2 mb-4">
                                <?php foreach ($proximities as $proximity): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <span><?= esc($proximity['name'] ?? $proximity['proximity_type'] ?? 'Proximité') ?></span>
                                            <?php if (isset($proximity['distance']) && $proximity['distance']): ?>
                                                <span class="text-muted ms-2">(<?= $proximity['distance'] ?> m)</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Form -->
                <div class="card mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope"></i> Demande d'information</h5>
                    </div>
                    <div class="card-body">
                        <form id="contactForm">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Nom complet" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" class="form-control" placeholder="Téléphone" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="4" placeholder="Votre message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane"></i> Envoyer
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Property Info -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Informations complémentaires</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>Référence:</strong> <?= esc($property['reference']) ?>
                            </li>
                            <?php if (isset($property['year_built']) && $property['year_built']): ?>
                                <li class="mb-2">
                                    <strong>Année de construction:</strong> <?= $property['year_built'] ?>
                                </li>
                            <?php endif; ?>
                            <?php if (isset($property['parking_spaces']) && $property['parking_spaces']): ?>
                                <li class="mb-2">
                                    <strong>Parking:</strong> <?= $property['parking_spaces'] ?> place(s)
                                </li>
                            <?php endif; ?>
                            <li class="mb-2">
                                <strong>Disponible:</strong> 
                                <?= (isset($property['available']) && $property['available']) ? '<span class="text-success">Oui</span>' : '<span class="text-danger">Non</span>' ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Properties -->
        <?php if (!empty($similar_properties)): ?>
            <section class="mt-5">
                <h3 class="mb-4">Propriétés similaires</h3>
                <div class="row g-4">
                    <?php foreach ($similar_properties as $simProperty): ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <?php 
                                $imageSrc = !empty($simProperty['main_image']) && !empty($simProperty['main_image']['file_path']) 
                                    ? base_url('uploads/properties/' . $simProperty['main_image']['file_path'])
                                    : base_url('uploads/properties/placeholder.jpg');
                                ?>
                                <img src="<?= $imageSrc ?>" class="card-img-top" alt="<?= esc($simProperty['title']) ?>" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title"><?= esc($simProperty['title']) ?></h6>
                                    <p class="text-muted small">
                                        <i class="fas fa-map-marker-alt"></i> <?= esc($simProperty['city']) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary fw-bold"><?= number_format($simProperty['price'], 0, ',', ' ') ?> TND</span>
                                        <a href="<?= base_url('properties/' . $simProperty['reference']) ?>" class="btn btn-sm btn-outline-primary">
                                            Voir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
