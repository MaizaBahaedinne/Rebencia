<!-- Étape 5: Pièces et Proximités -->
<div class="row g-4">
    <!-- Composition détaillée des pièces -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3">
            <i class="fas fa-th-large text-primary"></i> Composition détaillée des pièces
        </h6>
    </div>

    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-bordered" id="rooms-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40%">Nom de la pièce</th>
                        <th style="width: 30%">Type</th>
                        <th style="width: 20%">Surface (m²)</th>
                        <th style="width: 10%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="rooms-tbody">
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $index => $room): ?>
                            <tr>
                                <td>
                                    <input type="text" 
                                           class="form-control form-control-sm" 
                                           name="rooms[<?= $index ?>][room_name]" 
                                           value="<?= esc($room['room_name']) ?>"
                                           placeholder="Ex: Chambre principale">
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" name="rooms[<?= $index ?>][room_type]">
                                        <option value="bedroom" <?= $room['room_type'] == 'bedroom' ? 'selected' : '' ?>>Chambre</option>
                                        <option value="living_room" <?= $room['room_type'] == 'living_room' ? 'selected' : '' ?>>Salon</option>
                                        <option value="dining_room" <?= $room['room_type'] == 'dining_room' ? 'selected' : '' ?>>Salle à manger</option>
                                        <option value="kitchen" <?= $room['room_type'] == 'kitchen' ? 'selected' : '' ?>>Cuisine</option>
                                        <option value="bathroom" <?= $room['room_type'] == 'bathroom' ? 'selected' : '' ?>>Salle de bain</option>
                                        <option value="toilet" <?= $room['room_type'] == 'toilet' ? 'selected' : '' ?>>Toilette</option>
                                        <option value="office" <?= $room['room_type'] == 'office' ? 'selected' : '' ?>>Bureau</option>
                                        <option value="balcony" <?= $room['room_type'] == 'balcony' ? 'selected' : '' ?>>Balcon</option>
                                        <option value="terrace" <?= $room['room_type'] == 'terrace' ? 'selected' : '' ?>>Terrasse</option>
                                        <option value="garage" <?= $room['room_type'] == 'garage' ? 'selected' : '' ?>>Garage</option>
                                        <option value="storage" <?= $room['room_type'] == 'storage' ? 'selected' : '' ?>>Rangement</option>
                                        <option value="other" <?= $room['room_type'] == 'other' ? 'selected' : '' ?>>Autre</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm room-area" 
                                           name="rooms[<?= $index ?>][area_m2]" 
                                           value="<?= esc($room['area_m2']) ?>"
                                           step="0.01"
                                           min="0">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-room-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-room-btn">
                                <i class="fas fa-plus"></i> Ajouter une pièce
                            </button>
                            <span class="ms-3 text-muted">
                                Surface totale: <strong id="total-room-area">0</strong> m²
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Proximités -->
    <div class="col-12 mt-4">
        <h6 class="border-bottom pb-2 mb-3">
            <i class="fas fa-map-marker-alt text-success"></i> Proximités et accès
        </h6>
    </div>

    <?php
    $proximityTypes = [
        'transport_public' => ['label' => 'Transport public', 'icon' => 'fa-bus'],
        'ecole' => ['label' => 'École', 'icon' => 'fa-school'],
        'administration' => ['label' => 'Administration', 'icon' => 'fa-building'],
        'municipalite' => ['label' => 'Municipalité', 'icon' => 'fa-landmark'],
        'hopital' => ['label' => 'Hôpital', 'icon' => 'fa-hospital'],
        'commerces' => ['label' => 'Commerces', 'icon' => 'fa-shopping-cart'],
        'mosquee' => ['label' => 'Mosquée', 'icon' => 'fa-mosque'],
        'eglise' => ['label' => 'Église', 'icon' => 'fa-church']
    ];
    
    // Convertir les proximités en tableau associatif
    $proximitiesMap = [];
    if (!empty($proximities)) {
        foreach ($proximities as $prox) {
            $proximitiesMap[$prox['proximity_type']] = $prox;
        }
    }
    ?>

    <?php foreach ($proximityTypes as $type => $info): ?>
        <?php 
        $hasAccess = isset($proximitiesMap[$type]) ? $proximitiesMap[$type]['has_access'] : 0;
        $distance = isset($proximitiesMap[$type]) ? $proximitiesMap[$type]['distance_m'] : '';
        $distanceText = isset($proximitiesMap[$type]) ? $proximitiesMap[$type]['distance_text'] : '';
        ?>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input proximity-checkbox" 
                               type="checkbox" 
                               id="proximity_<?= $type ?>" 
                               name="proximities[<?= $type ?>][has_access]" 
                               value="1"
                               <?= $hasAccess ? 'checked' : '' ?>
                               data-target="proximity-details-<?= $type ?>">
                        <label class="form-check-label fw-bold" for="proximity_<?= $type ?>">
                            <i class="fas <?= $info['icon'] ?> me-1"></i>
                            <?= $info['label'] ?>
                        </label>
                    </div>
                    
                    <div id="proximity-details-<?= $type ?>" class="proximity-details" style="display: <?= $hasAccess ? 'block' : 'none' ?>">
                        <div class="mb-2">
                            <label class="form-label small mb-1">Distance (m)</label>
                            <input type="number" 
                                   class="form-control form-control-sm" 
                                   name="proximities[<?= $type ?>][distance_m]" 
                                   value="<?= esc($distance) ?>"
                                   step="1"
                                   min="0"
                                   placeholder="Ex: 500">
                        </div>
                        <div>
                            <label class="form-label small mb-1">Distance (texte)</label>
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="proximities[<?= $type ?>][distance_text]" 
                                   value="<?= esc($distanceText) ?>"
                                   placeholder="Ex: 5 minutes à pied">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let roomIndex = <?= !empty($rooms) ? count($rooms) : 0 ?>;
    
    // Ajouter une pièce
    document.getElementById('add-room-btn').addEventListener('click', function() {
        const tbody = document.getElementById('rooms-tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" 
                       class="form-control form-control-sm" 
                       name="rooms[${roomIndex}][room_name]" 
                       placeholder="Ex: Chambre principale">
            </td>
            <td>
                <select class="form-select form-select-sm" name="rooms[${roomIndex}][room_type]">
                    <option value="bedroom">Chambre</option>
                    <option value="living_room">Salon</option>
                    <option value="dining_room">Salle à manger</option>
                    <option value="kitchen">Cuisine</option>
                    <option value="bathroom">Salle de bain</option>
                    <option value="toilet">Toilette</option>
                    <option value="office">Bureau</option>
                    <option value="balcony">Balcon</option>
                    <option value="terrace">Terrasse</option>
                    <option value="garage">Garage</option>
                    <option value="storage">Rangement</option>
                    <option value="other">Autre</option>
                </select>
            </td>
            <td>
                <input type="number" 
                       class="form-control form-control-sm room-area" 
                       name="rooms[${roomIndex}][area_m2]" 
                       step="0.01"
                       min="0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-room-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        roomIndex++;
        calculateTotalArea();
    });
    
    // Supprimer une pièce (délégation d'événement)
    document.getElementById('rooms-tbody').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-room-btn') || e.target.closest('.remove-room-btn')) {
            const btn = e.target.classList.contains('remove-room-btn') ? e.target : e.target.closest('.remove-room-btn');
            btn.closest('tr').remove();
            calculateTotalArea();
        }
    });
    
    // Calculer la surface totale
    function calculateTotalArea() {
        let total = 0;
        document.querySelectorAll('.room-area').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('total-room-area').textContent = total.toFixed(2);
    }
    
    // Écouter les changements sur les surfaces
    document.getElementById('rooms-tbody').addEventListener('input', function(e) {
        if (e.target.classList.contains('room-area')) {
            calculateTotalArea();
        }
    });
    
    // Calculer au chargement
    calculateTotalArea();
    
    // Toggle proximité details
    document.querySelectorAll('.proximity-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const targetId = this.getAttribute('data-target');
            const details = document.getElementById(targetId);
            if (details) {
                details.style.display = this.checked ? 'block' : 'none';
            }
        });
    });
});
</script>
