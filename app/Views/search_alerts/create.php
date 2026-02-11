<?= $this->extend('layouts/public_orpi_style') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center text-white">
                <h1 class="display-4 fw-bold mb-3">Créer une alerte</h1>
                <p class="lead">Recevez les nouvelles annonces correspondant à vos critères directement par email</p>
            </div>
        </div>
    </div>
</section>

<!-- Alert Form -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <!-- Form Card -->
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        
                        <form method="post" action="<?= base_url('creer-une-alerte/submit') ?>">
                            <?= csrf_field() ?>

                            <!-- Contact Information -->
                            <h4 class="mb-4"><i class="fas fa-user me-2"></i> Vos informations</h4>
                            
                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control form-control-lg" 
                                           value="<?= old('first_name') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control form-control-lg" 
                                           value="<?= old('last_name') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-lg" 
                                           value="<?= old('email') ?>" required>
                                    <small class="text-muted">Vous recevrez les alertes sur cet email</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" name="phone" class="form-control form-control-lg" 
                                           value="<?= old('phone') ?>" placeholder="Ex: +216 12 345 678">
                                </div>
                            </div>

                            <hr class="my-5">

                            <!-- Search Criteria -->
                            <h4 class="mb-4"><i class="fas fa-search me-2"></i> Critères de recherche</h4>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Type de transaction <span class="text-danger">*</span></label>
                                    <select name="transaction_type" class="form-select form-select-lg" required>
                                        <option value="sale" <?= old('transaction_type') === 'sale' ? 'selected' : '' ?>>Vente</option>
                                        <option value="rent" <?= old('transaction_type') === 'rent' ? 'selected' : '' ?>>Location</option>
                                        <option value="both" <?= old('transaction_type') === 'both' ? 'selected' : '' ?>>Les deux</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Type(s) de bien</label>
                                    <select name="property_type[]" class="form-select form-select-lg" multiple size="1">
                                        <option value="apartment">Appartement</option>
                                        <option value="villa">Villa</option>
                                        <option value="studio">Studio</option>
                                        <option value="office">Bureau</option>
                                        <option value="shop">Commerce</option>
                                        <option value="warehouse">Entrepôt</option>
                                        <option value="land">Terrain</option>
                                    </select>
                                    <small class="text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs</small>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Budget (TND)</label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prix minimum</label>
                                    <input type="number" name="price_min" class="form-control form-control-lg" 
                                           value="<?= old('price_min') ?>" placeholder="Ex: 100000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prix maximum</label>
                                    <input type="number" name="price_max" class="form-control form-control-lg" 
                                           value="<?= old('price_max') ?>" placeholder="Ex: 500000">
                                </div>
                            </div>

                            <!-- Surface Range -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Surface (m²)</label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Surface minimum</label>
                                    <input type="number" name="area_min" class="form-control form-control-lg" 
                                           value="<?= old('area_min') ?>" step="0.01" placeholder="Ex: 80">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Surface maximum</label>
                                    <input type="number" name="area_max" class="form-control form-control-lg" 
                                           value="<?= old('area_max') ?>" step="0.01" placeholder="Ex: 200">
                                </div>
                            </div>

                            <!-- Rooms -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Pièces minimum</label>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pièces</label>
                                    <input type="number" name="rooms_min" class="form-control form-control-lg" 
                                           value="<?= old('rooms_min') ?>" placeholder="Ex: 3">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Chambres</label>
                                    <input type="number" name="bedrooms_min" class="form-control form-control-lg" 
                                           value="<?= old('bedrooms_min') ?>" placeholder="Ex: 2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Salles de bain</label>
                                    <input type="number" name="bathrooms_min" class="form-control form-control-lg" 
                                           value="<?= old('bathrooms_min') ?>" placeholder="Ex: 1">
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Localisation</label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ville(s)</label>
                                    <input type="text" name="cities[]" class="form-control form-control-lg" 
                                           value="<?= old('cities') ?>" placeholder="Ex: Tunis, Sousse">
                                    <small class="text-muted">Séparez par des virgules</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Zone(s)</label>
                                    <select name="zones[]" class="form-select form-select-lg" multiple size="1">
                                        <?php foreach ($zones as $zone): ?>
                                            <option value="<?= $zone['id'] ?>">
                                                <?= esc($zone['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs</small>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold mb-3">Équipements souhaités</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-check-lg">
                                        <input class="form-check-input" type="checkbox" name="has_elevator" value="1" 
                                               id="alert_elevator" <?= old('has_elevator') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="alert_elevator">Ascenseur</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-check-lg">
                                        <input class="form-check-input" type="checkbox" name="has_parking" value="1" 
                                               id="alert_parking" <?= old('has_parking') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="alert_parking">Parking</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-check-lg">
                                        <input class="form-check-input" type="checkbox" name="has_garden" value="1" 
                                               id="alert_garden" <?= old('has_garden') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="alert_garden">Jardin</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-check-lg">
                                        <input class="form-check-input" type="checkbox" name="has_pool" value="1" 
                                               id="alert_pool" <?= old('has_pool') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="alert_pool">Piscine</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Notification Frequency -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Fréquence des notifications <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-12">
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="frequency" id="freq_instant" value="instant" 
                                               <?= old('frequency') === 'instant' ? 'checked' : '' ?> required>
                                        <label class="btn btn-outline-primary btn-lg" for="freq_instant">
                                            <i class="fas fa-bolt me-2"></i> Instantanée
                                            <small class="d-block text-muted">Dès qu'un bien correspond</small>
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="frequency" id="freq_daily" value="daily" 
                                               <?= old('frequency') === 'daily' || !old('frequency') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-primary btn-lg" for="freq_daily">
                                            <i class="fas fa-calendar-day me-2"></i> Quotidienne
                                            <small class="d-block text-muted">Un résumé par jour</small>
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="frequency" id="freq_weekly" value="weekly" 
                                               <?= old('frequency') === 'weekly' ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-primary btn-lg" for="freq_weekly">
                                            <i class="fas fa-calendar-week me-2"></i> Hebdomadaire
                                            <small class="d-block text-muted">Un résumé par semaine</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-bell me-2"></i> Créer mon alerte
                                </button>
                                <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                                    Annuler
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4">
                    <h5><i class="fas fa-info-circle me-2"></i> Comment ça marche ?</h5>
                    <ul class="mb-0">
                        <li>Vous définissez vos critères de recherche</li>
                        <li>Nous vous notifions lorsque de nouveaux biens correspondent</li>
                        <li>Vous pouvez modifier ou désactiver votre alerte à tout moment</li>
                        <li>Votre email ne sera jamais partagé avec des tiers</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
.form-check-lg .form-check-input {
    width: 1.5rem;
    height: 1.5rem;
    margin-top: 0.125rem;
}

.form-check-lg .form-check-label {
    padding-left: 0.5rem;
    font-size: 1.1rem;
}

.btn-group label.btn-lg {
    padding: 1rem;
}

.btn-group label.btn-lg small {
    font-size: 0.75rem;
}
</style>

<?= $this->endSection() ?>
