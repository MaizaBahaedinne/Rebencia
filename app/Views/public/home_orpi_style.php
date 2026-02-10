<?= $this->extend('layouts/public_orpi_style') ?>

<?= $this->section('content') ?>

<!-- Hero Section with Search -->
<section class="orpi-hero">
    <div class="container orpi-hero-content">
        <div class="text-center">
            <h1 class="display-3 fw-bold mb-3" style="font-size: 3.5rem;">
                Trouvez le bien immobilier de vos rêves
            </h1>
            <p class="lead fs-4 mb-4">
                Des milliers de propriétés à vendre et à louer en Tunisie
            </p>
        </div>
        
        <!-- Search Card -->
        <div class="orpi-search-card">
            <!-- Tabs -->
            <div class="orpi-search-tabs">
                <button class="orpi-search-tab active" onclick="switchTab('sale')">
                    <i class="fas fa-shopping-cart me-2"></i> Acheter
                </button>
                <button class="orpi-search-tab" onclick="switchTab('rent')">
                    <i class="fas fa-key me-2"></i> Louer
                </button>
            </div>
            
            <!-- Search Form -->
            <form action="<?= base_url('search') ?>" method="get">
                <input type="hidden" name="transaction_type" id="transactionType" value="sale">
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Type de bien</label>
                        <select name="type" class="form-select form-select-lg">
                            <option value="">Tous les types</option>
                            <option value="apartment">Appartement</option>
                            <option value="villa">Villa</option>
                            <option value="house">Maison</option>
                            <option value="land">Terrain</option>
                            <option value="office">Bureau</option>
                            <option value="commercial">Commercial</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 position-relative">
                        <label class="form-label fw-semibold text-dark">Ville</label>
                        <input type="text" name="city" id="cityAutocomplete" class="form-control form-control-lg" placeholder="Ex: Tunis, Sousse, Hammamet..." autocomplete="off">
                        <div id="cityDropdown" class="autocomplete-dropdown" style="display: none; position: absolute; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-height: 300px; overflow-y: auto; width: 100%;"></div>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Budget max (TND)</label>
                        <input type="number" name="price_max" class="form-control form-control-lg" placeholder="Ex: 500000">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">&nbsp;</label>
                        <button type="submit" class="btn btn-orpi-primary w-100 btn-lg">
                            <i class="fas fa-search me-2"></i> Rechercher
                        </button>
                    </div>
                </div>
                
                <!-- Advanced Filters -->
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none fw-semibold" style="color: var(--primary-color);" onclick="toggleAdvanced(); return false;">
                        <i class="fas fa-sliders-h me-2"></i> Plus de critères
                    </a>
                </div>
                
                <div id="advancedFilters" style="display: none;">
                    <hr class="my-3">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label text-dark">Prix min (TND)</label>
                            <input type="number" name="price_min" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-dark">Prix max (TND)</label>
                            <input type="number" name="price_max_advanced" class="form-control" placeholder="Ex: 500000">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-dark">Surface min (m²)</label>
                            <input type="number" name="surface_min" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-dark">Surface max (m²)</label>
                            <input type="number" name="surface_max" class="form-control" placeholder="Ex: 200">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-dark">Chambres min</label>
                            <input type="number" name="bedrooms_min" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-dark">Salles de bain</label>
                            <input type="number" name="bathrooms_min" class="form-control" placeholder="0">
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label class="form-label text-dark fw-semibold">Équipements</label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="parking" id="parking">
                                <label class="form-check-label text-dark" for="parking">
                                    <i class="fas fa-car me-2"></i> Parking
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="piscine" id="piscine">
                                <label class="form-check-label text-dark" for="piscine">
                                    <i class="fas fa-swimming-pool me-2"></i> Piscine
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="jardin" id="jardin">
                                <label class="form-check-label text-dark" for="jardin">
                                    <i class="fas fa-tree me-2"></i> Jardin
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="balcon" id="balcon">
                                <label class="form-check-label text-dark" for="balcon">
                                    <i class="fas fa-home me-2"></i> Balcon
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="ascenseur" id="ascenseur">
                                <label class="form-check-label text-dark" for="ascenseur">
                                    <i class="fas fa-elevator me-2"></i> Ascenseur
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="climatisation" id="climatisation">
                                <label class="form-check-label text-dark" for="climatisation">
                                    <i class="fas fa-snowflake me-2"></i> Climatisation
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="meuble" id="meuble">
                                <label class="form-check-label text-dark" for="meuble">
                                    <i class="fas fa-couch me-2"></i> Meublé
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="securite" id="securite">
                                <label class="form-check-label text-dark" for="securite">
                                    <i class="fas fa-shield-alt me-2"></i> Sécurité 24/7
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Nos services les plus populaires</h2>
            <p class="lead text-muted">Accessibles en un clic</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="orpi-service-card">
                    <div class="orpi-service-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h4 class="mb-3">Estimer mon bien</h4>
                    <p class="text-muted">Obtenez une estimation gratuite de votre propriété en quelques clics</p>
                    <a href="<?= base_url('contact') ?>" class="btn btn-orpi-secondary mt-3">
                        Estimer maintenant <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="orpi-service-card">
                    <div class="orpi-service-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4 class="mb-3">Vendre mon bien</h4>
                    <p class="text-muted">Confiez-nous la vente de votre propriété avec un accompagnement personnalisé</p>
                    <a href="<?= base_url('contact') ?>" class="btn btn-orpi-secondary mt-3">
                        En savoir plus <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="orpi-service-card">
                    <div class="orpi-service-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h4 class="mb-3">Créer une alerte</h4>
                    <p class="text-muted">Recevez les nouvelles annonces correspondant à vos critères par email</p>
                    <a href="<?= base_url('contact') ?>" class="btn btn-orpi-secondary mt-3">
                        Créer une alerte <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="orpi-service-card">
                    <div class="orpi-service-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h4 class="mb-3">Louer mon bien</h4>
                    <p class="text-muted">Confiez la location de votre bien à nos experts en gestion locative</p>
                    <a href="<?= base_url('contact') ?>" class="btn btn-orpi-secondary mt-3">
                        Me faire rappeler <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Les derniers biens publiés</h2>
            <p class="lead text-muted">Découvrez notre sélection de propriétés d'exception</p>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($featured_properties)): ?>
                <?php foreach (array_slice($featured_properties, 0, 6) as $property): ?>
                    <div class="col-md-4">
                        <div class="orpi-property-card">
                            <div class="orpi-property-image">
                                <?php 
                                $imageSrc = !empty($property['main_image']) && !empty($property['main_image']['file_path']) 
                                    ? base_url('uploads/properties/' . $property['main_image']['file_path'])
                                    : base_url('uploads/properties/placeholder.jpg');
                                ?>
                                <img src="<?= $imageSrc ?>" alt="<?= esc($property['title']) ?>">
                                <div class="orpi-property-badge">
                                    <?= $property['transaction_type'] === 'sale' ? 'VENTE' : 'LOCATION' ?>
                                </div>
                                <div class="orpi-property-price">
                                    <?= number_format($property['price'], 0, ',', ' ') ?> TND
                                </div>
                            </div>
                            <div class="p-3">
                                <h5 class="mb-2"><?= esc($property['title']) ?></h5>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i><?= esc($property['city']) ?>
                                </p>
                                <div class="d-flex gap-3 mb-3 text-muted">
                                    <?php if (isset($property['surface']) && $property['surface']): ?>
                                        <span><i class="fas fa-ruler-combined me-1"></i> <?= $property['surface'] ?> m²</span>
                                    <?php endif; ?>
                                    <?php if (isset($property['bedrooms']) && $property['bedrooms']): ?>
                                        <span><i class="fas fa-bed me-1"></i> <?= $property['bedrooms'] ?></span>
                                    <?php endif; ?>
                                    <?php if (isset($property['bathrooms']) && $property['bathrooms']): ?>
                                        <span><i class="fas fa-bath me-1"></i> <?= $property['bathrooms'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= base_url('properties/' . $property['reference']) ?>" class="btn btn-orpi-primary w-100">
                                    <i class="fas fa-eye me-2"></i> Voir le bien
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Aucune propriété disponible pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="<?= base_url('properties') ?>" class="btn btn-orpi-secondary btn-lg">
                <i class="fas fa-th me-2"></i> Voir tous les biens
            </a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5" style="background: var(--primary-color); color: white;">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-home fa-4x mb-3"></i>
                    <h2 class="fw-bold display-4">500+</h2>
                    <p class="fs-5">Propriétés disponibles</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-users fa-4x mb-3"></i>
                    <h2 class="fw-bold display-4">1000+</h2>
                    <p class="fs-5">Clients satisfaits</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-building fa-4x mb-3"></i>
                    <h2 class="fw-bold display-4">50+</h2>
                    <p class="fs-5">Agences partenaires</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="fas fa-handshake fa-4x mb-3"></i>
                    <h2 class="fw-bold display-4">300+</h2>
                    <p class="fs-5">Transactions réussies</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Pourquoi choisir REBENCIA ?</h2>
            <p class="lead text-muted">Votre partenaire immobilier de confiance</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div style="width: 80px; height: 80px; background: rgba(102, 126, 234, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--primary-color); font-size: 2rem;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="mb-3">Sécurité</h4>
                    <p class="text-muted">Transactions sécurisées et transparentes pour votre tranquillité d'esprit</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div style="width: 80px; height: 80px; background: rgba(102, 126, 234, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--primary-color); font-size: 2rem;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h4 class="mb-3">Expertise</h4>
                    <p class="text-muted">Des professionnels qualifiés à votre écoute tout au long de votre projet</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div style="width: 80px; height: 80px; background: rgba(102, 126, 234, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: var(--primary-color); font-size: 2rem;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="mb-3">Satisfaction</h4>
                    <p class="text-muted">Plus de 95% de nos clients nous recommandent</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function switchTab(type) {
    document.getElementById('transactionType').value = type;
    document.querySelectorAll('.orpi-search-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.closest('.orpi-search-tab').classList.add('active');
}

function toggleAdvanced() {
    const advanced = document.getElementById('advancedFilters');
    advanced.style.display = advanced.style.display === 'none' ? 'block' : 'none';
}

// City autocomplete
const cities = [
    'Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Sfax', 'Sousse', 'Monastir', 
    'Hammamet', 'Nabeul', 'Bizerte', 'Gabès', 'Kairouan', 'Gafsa', 'Kasserine',
    'Mahdia', 'Médenine', 'Tataouine', 'Tozeur', 'Kébili', 'Jendouba', 
    'Le Kef', 'Siliana', 'Béja', 'Zaghouan', 'La Marsa', 'Carthage', 
    'Sidi Bou Said', 'La Goulette', 'Ennasr', 'Menzah', 'Manar', 'Lac'
];

const cityInput = document.getElementById('cityAutocomplete');
const cityDropdown = document.getElementById('cityDropdown');

if (cityInput) {
    cityInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        
        if (value.length < 2) {
            cityDropdown.style.display = 'none';
            return;
        }
        
        const filtered = cities.filter(city => 
            city.toLowerCase().includes(value)
        );
        
        if (filtered.length === 0) {
            cityDropdown.style.display = 'none';
            return;
        }
        
        cityDropdown.innerHTML = filtered.map(city => 
            `<div class="autocomplete-item" style="padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #eee; color: #333;" 
                  onmouseover="this.style.background='#f8f9fa'" 
                  onmouseout="this.style.background='white'"
                  onclick="selectCity('${city}')">${city}</div>`
        ).join('');
        
        cityDropdown.style.display = 'block';
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target !== cityInput && e.target.parentElement !== cityDropdown) {
            cityDropdown.style.display = 'none';
        }
    });
}

function selectCity(city) {
    cityInput.value = city;
    cityDropdown.style.display = 'none';
}
</script>
<?= $this->endSection() ?>
