<!-- Étape 3: Caractéristiques techniques -->
<div class="row g-4">
    <!-- Surfaces -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-ruler-combined text-primary"></i> Surfaces</h6>
    </div>

    <div class="col-md-4">
        <label for="area_total" class="form-label">
            Surface totale (m²) <span class="text-danger">*</span>
        </label>
        <input type="number" 
               class="form-control" 
               id="area_total" 
               name="area_total" 
               value="<?= old('area_total', $property['area_total'] ?? '') ?>"
               step="0.01"
               required>
    </div>

    <div class="col-md-4">
        <label for="area_living" class="form-label">Surface habitable (m²)</label>
        <input type="number" 
               class="form-control" 
               id="area_living" 
               name="area_living" 
               value="<?= old('area_living', $property['area_living'] ?? '') ?>"
               step="0.01">
    </div>

    <div class="col-md-4">
        <label for="area_land" class="form-label">Surface terrain (m²)</label>
        <input type="number" 
               class="form-control" 
               id="area_land" 
               name="area_land" 
               value="<?= old('area_land', $property['area_land'] ?? '') ?>"
               step="0.01">
    </div>

    <!-- Pièces -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3"><i class="fas fa-door-open text-primary"></i> Composition</h6>
    </div>

    <div class="col-md-3">
        <label for="rooms" class="form-label">Nombre de pièces</label>
        <input type="number" 
               class="form-control" 
               id="rooms" 
               name="rooms" 
               value="<?= old('rooms', $property['rooms'] ?? '') ?>"
               min="0">
    </div>

    <div class="col-md-3">
        <label for="bedrooms" class="form-label">Chambres</label>
        <input type="number" 
               class="form-control" 
               id="bedrooms" 
               name="bedrooms" 
               value="<?= old('bedrooms', $property['bedrooms'] ?? '') ?>"
               min="0">
    </div>

    <div class="col-md-3">
        <label for="bathrooms" class="form-label">Salles de bain</label>
        <input type="number" 
               class="form-control" 
               id="bathrooms" 
               name="bathrooms" 
               value="<?= old('bathrooms', $property['bathrooms'] ?? '') ?>"
               min="0">
    </div>

    <div class="col-md-3">
        <label for="parking_spaces" class="form-label">Places parking</label>
        <input type="number" 
               class="form-control" 
               id="parking_spaces" 
               name="parking_spaces" 
               value="<?= old('parking_spaces', $property['parking_spaces'] ?? 0) ?>"
               min="0">
    </div>

    <!-- Étages -->
    <div class="col-md-4">
        <label for="floor" class="form-label">Étage</label>
        <input type="number" 
               class="form-control" 
               id="floor" 
               name="floor" 
               value="<?= old('floor', $property['floor'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label for="total_floors" class="form-label">Nombre total d'étages</label>
        <input type="number" 
               class="form-control" 
               id="total_floors" 
               name="total_floors" 
               value="<?= old('total_floors', $property['total_floors'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label for="construction_year" class="form-label">Année de construction</label>
        <input type="number" 
               class="form-control" 
               id="construction_year" 
               name="construction_year" 
               value="<?= old('construction_year', $property['construction_year'] ?? '') ?>"
               min="1900"
               max="<?= date('Y') + 5 ?>">
    </div>

    <!-- Caractéristiques -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3"><i class="fas fa-cog text-primary"></i> Caractéristiques</h6>
    </div>

    <div class="col-md-3">
        <label for="orientation" class="form-label">Orientation</label>
        <select class="form-select" id="orientation" name="orientation">
            <option value="">-- Sélectionner --</option>
            <option value="N" <?= old('orientation', $property['orientation'] ?? '') == 'N' ? 'selected' : '' ?>>Nord</option>
            <option value="S" <?= old('orientation', $property['orientation'] ?? '') == 'S' ? 'selected' : '' ?>>Sud</option>
            <option value="E" <?= old('orientation', $property['orientation'] ?? '') == 'E' ? 'selected' : '' ?>>Est</option>
            <option value="O" <?= old('orientation', $property['orientation'] ?? '') == 'O' ? 'selected' : '' ?>>Ouest</option>
            <option value="NE" <?= old('orientation', $property['orientation'] ?? '') == 'NE' ? 'selected' : '' ?>>Nord-Est</option>
            <option value="NO" <?= old('orientation', $property['orientation'] ?? '') == 'NO' ? 'selected' : '' ?>>Nord-Ouest</option>
            <option value="SE" <?= old('orientation', $property['orientation'] ?? '') == 'SE' ? 'selected' : '' ?>>Sud-Est</option>
            <option value="SO" <?= old('orientation', $property['orientation'] ?? '') == 'SO' ? 'selected' : '' ?>>Sud-Ouest</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="floor_type" class="form-label">Type de sol</label>
        <select class="form-select" id="floor_type" name="floor_type">
            <option value="">-- Sélectionner --</option>
            <option value="carrelage" <?= old('floor_type', $property['floor_type'] ?? '') == 'carrelage' ? 'selected' : '' ?>>Carrelage</option>
            <option value="marbre" <?= old('floor_type', $property['floor_type'] ?? '') == 'marbre' ? 'selected' : '' ?>>Marbre</option>
            <option value="parquet" <?= old('floor_type', $property['floor_type'] ?? '') == 'parquet' ? 'selected' : '' ?>>Parquet</option>
            <option value="beton_cire" <?= old('floor_type', $property['floor_type'] ?? '') == 'beton_cire' ? 'selected' : '' ?>>Béton ciré</option>
            <option value="moquette" <?= old('floor_type', $property['floor_type'] ?? '') == 'moquette' ? 'selected' : '' ?>>Moquette</option>
            <option value="mixte" <?= old('floor_type', $property['floor_type'] ?? '') == 'mixte' ? 'selected' : '' ?>>Mixte</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="gas_type" class="form-label">Type de gaz</label>
        <select class="form-select" id="gas_type" name="gas_type">
            <option value="">-- Sélectionner --</option>
            <option value="ville" <?= old('gas_type', $property['gas_type'] ?? '') == 'ville' ? 'selected' : '' ?>>Gaz de ville</option>
            <option value="bouteille" <?= old('gas_type', $property['gas_type'] ?? '') == 'bouteille' ? 'selected' : '' ?>>Bouteille</option>
            <option value="propane" <?= old('gas_type', $property['gas_type'] ?? '') == 'propane' ? 'selected' : '' ?>>Propane</option>
            <option value="aucun" <?= old('gas_type', $property['gas_type'] ?? 'aucun') == 'aucun' ? 'selected' : '' ?>>Aucun</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="standing" class="form-label">Standing</label>
        <select class="form-select" id="standing" name="standing">
            <option value="economic" <?= old('standing', $property['standing'] ?? 'standard') == 'economic' ? 'selected' : '' ?>>Économique</option>
            <option value="standard" <?= old('standing', $property['standing'] ?? 'standard') == 'standard' ? 'selected' : '' ?>>Standard</option>
            <option value="premium" <?= old('standing', $property['standing'] ?? '') == 'premium' ? 'selected' : '' ?>>Premium</option>
            <option value="luxury" <?= old('standing', $property['standing'] ?? '') == 'luxury' ? 'selected' : '' ?>>Luxe</option>
        </select>
    </div>

    <div class="col-md-4">
        <label for="condition_state" class="form-label">État du bien</label>
        <select class="form-select" id="condition_state" name="condition_state">
            <option value="new" <?= old('condition_state', $property['condition_state'] ?? 'good') == 'new' ? 'selected' : '' ?>>Neuf</option>
            <option value="excellent" <?= old('condition_state', $property['condition_state'] ?? 'good') == 'excellent' ? 'selected' : '' ?>>Excellent</option>
            <option value="good" <?= old('condition_state', $property['condition_state'] ?? 'good') == 'good' ? 'selected' : '' ?>>Bon état</option>
            <option value="to_renovate" <?= old('condition_state', $property['condition_state'] ?? '') == 'to_renovate' ? 'selected' : '' ?>>À rénover</option>
        </select>
    </div>

    <div class="col-md-4">
        <label for="legal_status" class="form-label">Statut légal</label>
        <select class="form-select" id="legal_status" name="legal_status">
            <option value="clear" <?= old('legal_status', $property['legal_status'] ?? 'clear') == 'clear' ? 'selected' : '' ?>>Clair</option>
            <option value="pending" <?= old('legal_status', $property['legal_status'] ?? '') == 'pending' ? 'selected' : '' ?>>En attente</option>
            <option value="issues" <?= old('legal_status', $property['legal_status'] ?? '') == 'issues' ? 'selected' : '' ?>>Problèmes</option>
        </select>
    </div>

    <!-- Performance énergétique -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3"><i class="fas fa-leaf text-success"></i> Performance énergétique</h6>
    </div>

    <div class="col-md-4">
        <label for="energy_class" class="form-label">Classe énergétique</label>
        <select class="form-select" id="energy_class" name="energy_class">
            <option value="">-- Non renseigné --</option>
            <option value="A" <?= old('energy_class', $property['energy_class'] ?? '') == 'A' ? 'selected' : '' ?>>A (Très économe)</option>
            <option value="B" <?= old('energy_class', $property['energy_class'] ?? '') == 'B' ? 'selected' : '' ?>>B</option>
            <option value="C" <?= old('energy_class', $property['energy_class'] ?? '') == 'C' ? 'selected' : '' ?>>C</option>
            <option value="D" <?= old('energy_class', $property['energy_class'] ?? '') == 'D' ? 'selected' : '' ?>>D</option>
            <option value="E" <?= old('energy_class', $property['energy_class'] ?? '') == 'E' ? 'selected' : '' ?>>E</option>
            <option value="F" <?= old('energy_class', $property['energy_class'] ?? '') == 'F' ? 'selected' : '' ?>>F</option>
            <option value="G" <?= old('energy_class', $property['energy_class'] ?? '') == 'G' ? 'selected' : '' ?>>G (Peu économe)</option>
        </select>
    </div>

    <div class="col-md-4">
        <label for="energy_consumption_kwh" class="form-label">Consommation (kWh/m²/an)</label>
        <input type="number" 
               class="form-control" 
               id="energy_consumption_kwh" 
               name="energy_consumption_kwh" 
               value="<?= old('energy_consumption_kwh', $property['energy_consumption_kwh'] ?? '') ?>"
               step="0.01">
    </div>

    <div class="col-md-4">
        <label for="co2_emission" class="form-label">Émission CO2 (kg/m²/an)</label>
        <input type="number" 
               class="form-control" 
               id="co2_emission" 
               name="co2_emission" 
               value="<?= old('co2_emission', $property['co2_emission'] ?? '') ?>"
               step="0.01">
    </div>

    <!-- Équipements -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3 mt-3"><i class="fas fa-check-square text-primary"></i> Équipements</h6>
    </div>

    <div class="col-md-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="has_elevator" name="has_elevator" value="1" <?= old('has_elevator', $property['has_elevator'] ?? 0) ? 'checked' : '' ?>>
            <label class="form-check-label" for="has_elevator">Ascenseur</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="has_parking" name="has_parking" value="1" <?= old('has_parking', $property['has_parking'] ?? 0) ? 'checked' : '' ?>>
            <label class="form-check-label" for="has_parking">Parking</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="has_garden" name="has_garden" value="1" <?= old('has_garden', $property['has_garden'] ?? 0) ? 'checked' : '' ?>>
            <label class="form-check-label" for="has_garden">Jardin</label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="has_pool" name="has_pool" value="1" <?= old('has_pool', $property['has_pool'] ?? 0) ? 'checked' : '' ?>>
            <label class="form-check-label" for="has_pool">Piscine</label>
        </div>
    </div>
</div>
