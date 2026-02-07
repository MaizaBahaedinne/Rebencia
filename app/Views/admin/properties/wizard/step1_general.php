<!-- Étape 1: Informations générales -->
<div class="row g-4">
    <!-- Référence -->
    <div class="col-md-4">
        <label for="reference" class="form-label">
            Référence <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="reference" 
               name="reference" 
               value="<?= old('reference', $property['reference'] ?? '') ?>"
               <?= $isEdit ? 'readonly' : 'required' ?>>
        <small class="text-muted">Auto-générée si vide</small>
    </div>

    <!-- Type de bien -->
    <div class="col-md-4">
        <label for="type" class="form-label">
            Type de bien <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="type" name="type" required>
            <option value="">-- Sélectionner --</option>
            <option value="apartment" <?= old('type', $property['type'] ?? '') == 'apartment' ? 'selected' : '' ?>>Appartement</option>
            <option value="villa" <?= old('type', $property['type'] ?? '') == 'villa' ? 'selected' : '' ?>>Villa</option>
            <option value="house" <?= old('type', $property['type'] ?? '') == 'house' ? 'selected' : '' ?>>Maison</option>
            <option value="land" <?= old('type', $property['type'] ?? '') == 'land' ? 'selected' : '' ?>>Terrain</option>
            <option value="office" <?= old('type', $property['type'] ?? '') == 'office' ? 'selected' : '' ?>>Bureau</option>
            <option value="commercial" <?= old('type', $property['type'] ?? '') == 'commercial' ? 'selected' : '' ?>>Local commercial</option>
            <option value="warehouse" <?= old('type', $property['type'] ?? '') == 'warehouse' ? 'selected' : '' ?>>Entrepôt</option>
            <option value="other" <?= old('type', $property['type'] ?? '') == 'other' ? 'selected' : '' ?>>Autre</option>
        </select>
    </div>

    <!-- Type de transaction -->
    <div class="col-md-4">
        <label for="transaction_type" class="form-label">
            Type de transaction <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="transaction_type" name="transaction_type" required>
            <option value="sale" <?= old('transaction_type', $property['transaction_type'] ?? 'sale') == 'sale' ? 'selected' : '' ?>>Vente</option>
            <option value="rent" <?= old('transaction_type', $property['transaction_type'] ?? '') == 'rent' ? 'selected' : '' ?>>Location</option>
            <option value="both" <?= old('transaction_type', $property['transaction_type'] ?? '') == 'both' ? 'selected' : '' ?>>Vente & Location</option>
        </select>
    </div>

    <!-- Titre FR -->
    <div class="col-md-12">
        <label for="title" class="form-label">
            Titre (Français) <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control" 
               id="title" 
               name="title" 
               value="<?= old('title', $property['title'] ?? '') ?>"
               placeholder="Ex: Bel appartement au centre ville"
               required>
    </div>

    <!-- Titre AR -->
    <div class="col-md-6">
        <label for="title_ar" class="form-label">Titre (Arabe)</label>
        <input type="text" 
               class="form-control" 
               id="title_ar" 
               name="title_ar" 
               value="<?= old('title_ar', $property['title_ar'] ?? '') ?>"
               dir="rtl">
    </div>

    <!-- Titre EN -->
    <div class="col-md-6">
        <label for="title_en" class="form-label">Titre (Anglais)</label>
        <input type="text" 
               class="form-control" 
               id="title_en" 
               name="title_en" 
               value="<?= old('title_en', $property['title_en'] ?? '') ?>">
    </div>

    <!-- Description FR -->
    <div class="col-md-12">
        <label for="description" class="form-label">
            Description (Français) <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" 
                  id="description" 
                  name="description" 
                  rows="5" 
                  required><?= old('description', $property['description'] ?? '') ?></textarea>
    </div>

    <!-- Description AR -->
    <div class="col-md-6">
        <label for="description_ar" class="form-label">Description (Arabe)</label>
        <textarea class="form-control" 
                  id="description_ar" 
                  name="description_ar" 
                  rows="4"
                  dir="rtl"><?= old('description_ar', $property['description_ar'] ?? '') ?></textarea>
    </div>

    <!-- Description EN -->
    <div class="col-md-6">
        <label for="description_en" class="form-label">Description (Anglais)</label>
        <textarea class="form-control" 
                  id="description_en" 
                  name="description_en" 
                  rows="4"><?= old('description_en', $property['description_en'] ?? '') ?></textarea>
    </div>

    <!-- Date de disponibilité -->
    <div class="col-md-4">
        <label for="disponibilite_date" class="form-label">Date de disponibilité</label>
        <input type="date" 
               class="form-control" 
               id="disponibilite_date" 
               name="disponibilite_date" 
               value="<?= old('disponibilite_date', $property['disponibilite_date'] ?? '') ?>">
    </div>

    <!-- Statut -->
    <div class="col-md-4">
        <label for="status" class="form-label">
            Statut <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="status" name="status" required>
            <option value="draft" <?= old('status', $property['status'] ?? 'draft') == 'draft' ? 'selected' : '' ?>>Brouillon</option>
            <option value="published" <?= old('status', $property['status'] ?? '') == 'published' ? 'selected' : '' ?>>Publié</option>
            <option value="reserved" <?= old('status', $property['status'] ?? '') == 'reserved' ? 'selected' : '' ?>>Réservé</option>
            <option value="sold" <?= old('status', $property['status'] ?? '') == 'sold' ? 'selected' : '' ?>>Vendu</option>
            <option value="rented" <?= old('status', $property['status'] ?? '') == 'rented' ? 'selected' : '' ?>>Loué</option>
            <option value="archived" <?= old('status', $property['status'] ?? '') == 'archived' ? 'selected' : '' ?>>Archivé</option>
        </select>
    </div>

    <!-- À la une -->
    <div class="col-md-4">
        <label class="form-label d-block">Options</label>
        <div class="form-check form-switch">
            <input class="form-check-input" 
                   type="checkbox" 
                   id="featured" 
                   name="featured" 
                   value="1"
                   <?= old('featured', $property['featured'] ?? 0) ? 'checked' : '' ?>>
            <label class="form-check-label" for="featured">
                <i class="fas fa-star text-warning"></i> Bien à la une
            </label>
        </div>
    </div>
</div>
