<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section" style="background: var(--primary-color); padding: 100px 0; color: white;">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Trouvez votre propriété de rêve</h1>
        <p class="lead mb-5">Des milliers de biens immobiliers à vendre et à louer en Tunisie</p>
        
        <!-- Quick Search -->
        <div class="card shadow-lg" style="max-width: 1100px; margin: 0 auto;">
            <div class="card-body p-4">
                <form action="<?= base_url('search') ?>" method="get" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="transaction_type" class="form-select">
                                <option value="">Type de transaction</option>
                                <option value="sale">Vente</option>
                                <option value="rent">Location</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">Type de bien</option>
                                <option value="apartment">Appartement</option>
                                <option value="villa">Villa</option>
                                <option value="house">Maison</option>
                                <option value="land">Terrain</option>
                                <option value="office">Bureau</option>
                                <option value="commercial">Commercial</option>
                            </select>
                        </div>
                        <div class="col-md-4 position-relative">
                            <input type="text" name="city" id="cityAutocomplete" class="form-control" placeholder="Ville (ex: Tunis, Sousse...)" autocomplete="off">
                            <div id="cityDropdown" class="autocomplete-dropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-height: 300px; overflow-y: auto; width: 100%;"></div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                    </div>
                    
                    <!-- Advanced Filters Toggle -->
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="button" class="btn btn-link text-white" id="toggleAdvanced">
                                <i class="fas fa-sliders-h"></i> Filtres avancés
                            </button>
                        </div>
                    </div>
                    
                    <!-- Advanced Filters -->
                    <div id="advancedFilters" style="display: none;">
                        <hr class="my-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small">Prix minimum (TND)</label>
                                <input type="number" name="price_min" class="form-control" placeholder="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Prix maximum (TND)</label>
                                <input type="number" name="price_max" class="form-control" placeholder="Illimité">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Surface min (m²)</label>
                                <input type="number" name="area_min" class="form-control" placeholder="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Surface max (m²)</label>
                                <input type="number" name="area_max" class="form-control" placeholder="Illimité">
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label class="form-label small">Chambres min</label>
                                <select name="bedrooms_min" class="form-select">
                                    <option value="">Toutes</option>
                                    <option value="1">1+</option>
                                    <option value="2">2+</option>
                                    <option value="3">3+</option>
                                    <option value="4">4+</option>
                                    <option value="5">5+</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Salles de bain min</label>
                                <select name="bathrooms_min" class="form-select">
                                    <option value="">Toutes</option>
                                    <option value="1">1+</option>
                                    <option value="2">2+</option>
                                    <option value="3">3+</option>
                                    <option value="4">4+</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Gouvernorat</label>
                                <select name="governorate" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="Tunis">Tunis</option>
                                    <option value="Ariana">Ariana</option>
                                    <option value="Ben Arous">Ben Arous</option>
                                    <option value="Manouba">Manouba</option>
                                    <option value="Nabeul">Nabeul</option>
                                    <option value="Sousse">Sousse</option>
                                    <option value="Monastir">Monastir</option>
                                    <option value="Mahdia">Mahdia</option>
                                    <option value="Sfax">Sfax</option>
                                    <option value="Bizerte">Bizerte</option>
                                    <option value="Gabès">Gabès</option>
                                    <option value="Médenine">Médenine</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Référence</label>
                                <input type="text" name="reference" class="form-control" placeholder="Ex: REF-001">
                            </div>
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
                            <?php 
                            $imageSrc = !empty($property['main_image']) && !empty($property['main_image']['file_path']) 
                                ? base_url('uploads/properties/' . $property['main_image']['file_path'])
                                : base_url('uploads/properties/placeholder.jpg');
                            ?>
                            <img src="<?= $imageSrc ?>" class="card-img-top" alt="<?= esc($property['title']) ?>" style="height: 200px; object-fit: cover;">
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
                                    <a href="<?= base_url('properties/' . $property['reference']) ?>" class="btn btn-sm btn-primary">
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
                            <?php 
                            $imageSrc = !empty($property['main_image']) && !empty($property['main_image']['file_path']) 
                                ? base_url('uploads/properties/' . $property['main_image']['file_path'])
                                : base_url('uploads/properties/placeholder.jpg');
                            ?>
                            <img src="<?= $imageSrc ?>" class="card-img-top" alt="<?= esc($property['title']) ?>" style="height: 150px; object-fit: cover;">
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

<!-- Autocomplete Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cityInput = document.getElementById('cityAutocomplete');
    const cityDropdown = document.getElementById('cityDropdown');
    const toggleBtn = document.getElementById('toggleAdvanced');
    const advancedFilters = document.getElementById('advancedFilters');
    let cities = [];
    let debounceTimer;
    
    // Toggle advanced filters
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (advancedFilters.style.display === 'none') {
                advancedFilters.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Masquer les filtres';
            } else {
                advancedFilters.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Filtres avancés';
            }
        });
    }
    
    // Load cities
    fetch('<?= base_url('api/cities') ?>')
        .then(response => response.json())
        .then(data => {
            cities = data;
        })
        .catch(error => console.error('Erreur chargement villes:', error));
    
    // Autocomplete functionality
    cityInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const searchTerm = this.value.trim().toLowerCase();
        
        if (searchTerm.length < 2) {
            cityDropdown.style.display = 'none';
            return;
        }
        
        debounceTimer = setTimeout(() => {
            const filtered = cities.filter(city => 
                city.toLowerCase().includes(searchTerm)
            ).slice(0, 10);
            
            if (filtered.length > 0) {
                cityDropdown.innerHTML = filtered.map(city => 
                    `<div class="autocomplete-item" style="padding: 10px; cursor: pointer; border-bottom: 1px solid #f0f0f0;" data-city="${city}">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>${city}
                    </div>`
                ).join('');
                
                cityDropdown.style.display = 'block';
                
                // Add click handlers
                cityDropdown.querySelectorAll('.autocomplete-item').forEach(item => {
                    item.addEventListener('click', function() {
                        cityInput.value = this.dataset.city;
                        cityDropdown.style.display = 'none';
                    });
                    
                    item.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#f8f9fa';
                    });
                    
                    item.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = 'white';
                    });
                });
            } else {
                cityDropdown.style.display = 'none';
            }
        }, 300);
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!cityInput.contains(e.target) && !cityDropdown.contains(e.target)) {
            cityDropdown.style.display = 'none';
        }
    });
});
</script>

<?= $this->endSection() ?>
