// public/assets/js/property-extended.js
// JavaScript pour gérer les tabs de données étendues

const PropertyExtended = {
    propertyId: null,
    
    init(propertyId) {
        this.propertyId = propertyId;
        this.bindEvents();
        this.updateLocationScore();
        this.updateFinancialMetrics();
        this.updateCostsTotal();
    },
    
    bindEvents() {
        // Pièces
        $('#addRoomBtn').on('click', () => this.addRoom());
        $('#saveRoomsBtn').on('click', () => this.saveRooms());
        $(document).on('click', '.remove-room', (e) => this.removeRoom(e));
        
        // Options
        $('#saveOptionsBtn').on('click', () => this.saveOptions());
        
        // Location
        $('.location-score').on('input', (e) => {
            $(e.target).next('.score-value').text(e.target.value);
            this.updateLocationScore();
        });
        $('#saveLocationBtn').on('click', () => this.saveLocation());
        
        // Financial
        $('#estimated_market_price, #estimated_rental_price').on('input', () => {
            this.updateFinancialMetrics();
        });
        $('#saveFinancialBtn').on('click', () => this.saveFinancial());
        
        // Costs
        $('.cost-input').on('input', () => this.updateCostsTotal());
        $('#saveCostsBtn').on('click', () => this.saveCosts());
        
        // Orientation
        $('#saveOrientationBtn').on('click', () => this.saveOrientation());
        
        // Media
        $('#uploadMediaBtn').on('click', () => this.uploadMedia());
        $(document).on('click', '.delete-media', (e) => this.deleteMedia(e));
    },
    
    // === ROOMS ===
    addRoom() {
        const index = $('#roomsContainer .room-form').length;
        const html = `
            <div class="room-form card mb-2">
                <div class="card-body">
                    <button type="button" class="btn btn-sm btn-danger float-right remove-room">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Nom pièce" name="rooms[${index}][name_fr]">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="rooms[${index}][room_type]">
                                <option value="living_room">Salon</option>
                                <option value="bedroom">Chambre</option>
                                <option value="kitchen">Cuisine</option>
                                <option value="bathroom">Salle de bain</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" placeholder="Surface (m²)" name="rooms[${index}][surface]" step="0.1">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" placeholder="Hauteur" name="rooms[${index}][height]" step="0.1">
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#roomsContainer').append(html);
    },
    
    removeRoom(e) {
        $(e.target).closest('.room-form').remove();
    },
    
    saveRooms() {
        const rooms = [];
        $('#roomsContainer .room-form').each(function() {
            const room = {
                name_fr: $(this).find('[name*="[name_fr]"]').val(),
                room_type: $(this).find('[name*="[room_type]"]').val(),
                surface: parseFloat($(this).find('[name*="[surface]"]').val()) || 0,
                height: parseFloat($(this).find('[name*="[height]"]').val()) || 0
            };
            rooms.push(room);
        });
        
        this.sendRequest(`/admin/properties/${this.propertyId}/rooms/save`, { rooms }, 'Pièces sauvegardées');
    },
    
    // === OPTIONS ===
    saveOptions() {
        const options = [];
        $('.option-checkbox:checked').each(function() {
            options.push({ option_id: parseInt($(this).val()) });
        });
        
        this.sendRequest(`/admin/properties/${this.propertyId}/options/save`, { options }, 'Options sauvegardées');
    },
    
    // === LOCATION ===
    updateLocationScore() {
        const scores = {
            proximity_to_schools: parseInt($('#proximity_to_schools').val()) || 0,
            proximity_to_transport: parseInt($('#proximity_to_transport').val()) || 0,
            proximity_to_shopping: parseInt($('#proximity_to_shopping').val()) || 0,
            area_safety_score: parseInt($('#area_safety_score').val()) || 0
        };
        
        // Calcul simplifié du score global (moyenne pondérée)
        const overall = Math.round(
            (scores.proximity_to_schools * 0.25) +
            (scores.proximity_to_transport * 0.30) +
            (scores.proximity_to_shopping * 0.20) +
            (scores.area_safety_score * 0.25)
        );
        
        $('#overallLocationScore').text(overall);
    },
    
    saveLocation() {
        const data = {
            proximity_to_schools: parseInt($('#proximity_to_schools').val()) || 0,
            proximity_to_transport: parseInt($('#proximity_to_transport').val()) || 0,
            proximity_to_shopping: parseInt($('#proximity_to_shopping').val()) || 0,
            proximity_to_parks: 50,
            proximity_to_healthcare: 50,
            proximity_to_restaurants: 50,
            proximity_to_entertainment: 50,
            area_safety_score: parseInt($('#area_safety_score').val()) || 0,
            noise_level_score: 50,
            area_cleanliness_score: 50
        };
        
        this.sendRequest(`/admin/properties/${this.propertyId}/location/save`, data, 'Localisation sauvegardée');
    },
    
    // === FINANCIAL ===
    updateFinancialMetrics() {
        const marketPrice = parseFloat($('#estimated_market_price').val()) || 0;
        const rentalPrice = parseFloat($('#estimated_rental_price').val()) || 0;
        
        if (marketPrice > 0 && rentalPrice > 0) {
            const annualRental = rentalPrice * 12;
            const grossYield = (annualRental / marketPrice) * 100;
            const netYield = grossYield * 0.85; // Estimation simple
            
            $('#grossYield').text(grossYield.toFixed(2));
            $('#netYield').text(netYield.toFixed(2));
            $('#capRate').text((netYield * 0.95).toFixed(2));
            
            const surface = parseFloat($('#surface').val()) || 1;
            $('#pricePerSqm').text((marketPrice / surface).toFixed(0));
        }
    },
    
    saveFinancial() {
        const data = {
            estimated_market_price: parseFloat($('#estimated_market_price').val()) || 0,
            estimated_rental_price: parseFloat($('#estimated_rental_price').val()) || 0
        };
        
        this.sendRequest(`/admin/properties/${this.propertyId}/financial/save`, data, 'Données financières sauvegardées');
    },
    
    // === COSTS ===
    updateCostsTotal() {
        let total = 0;
        $('.cost-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#totalMonthlyCosts').text(total.toFixed(2));
    },
    
    saveCosts() {
        const data = {
            monthly_syndic: parseFloat($('#monthly_syndic').val()) || 0,
            monthly_electricity: parseFloat($('#monthly_electricity').val()) || 0,
            monthly_water: parseFloat($('#monthly_water').val()) || 0,
            monthly_gas: 0,
            monthly_heating: 0,
            monthly_hoa_fees: 0,
            monthly_other: 0,
            annual_property_tax: 0,
            annual_income_tax: 0,
            annual_insurance: 0,
            annual_maintenance: 0
        };
        
        this.sendRequest(`/admin/properties/${this.propertyId}/costs/save`, data, 'Charges sauvegardées');
    },
    
    // === ORIENTATION ===
    saveOrientation() {
        const data = {
            primary_orientation: $('#primary_orientation').val(),
            sun_exposure: $('#sun_exposure').val(),
            morning_sun: 0,
            afternoon_sun: 0,
            evening_sun: 0
        };
        
        this.sendRequest(`/admin/properties/${this.propertyId}/orientation/save`, data, 'Orientation sauvegardée');
    },
    
    // === MEDIA ===
    uploadMedia() {
        const fileInput = document.getElementById('media_file');
        const file = fileInput.files[0];
        
        if (!file) {
            alert('Veuillez sélectionner un fichier');
            return;
        }
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('file_type', $('#media_file_type').val());
        
        $.ajax({
            url: `/admin/properties/${this.propertyId}/media/upload`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    alert('Fichier uploadé avec succès');
                    location.reload();
                }
            },
            error: () => alert('Erreur lors de l\'upload')
        });
    },
    
    deleteMedia(e) {
        const mediaId = $(e.target).closest('.delete-media').data('id');
        
        if (!confirm('Supprimer ce fichier ?')) return;
        
        $.ajax({
            url: `/admin/properties/media/${mediaId}`,
            type: 'DELETE',
            success: (response) => {
                if (response.success) {
                    $(`.media-item[data-id="${mediaId}"]`).remove();
                    alert('Fichier supprimé');
                }
            },
            error: () => alert('Erreur lors de la suppression')
        });
    },
    
    // === HELPER ===
    sendRequest(url, data, successMessage) {
        $.ajax({
            url: url,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: (response) => {
                if (response.success) {
                    alert(successMessage);
                } else {
                    alert('Erreur: ' + response.message);
                }
            },
            error: (xhr) => {
                alert('Erreur serveur: ' + xhr.responseText);
            }
        });
    }
};

// Auto-init si propertyId disponible
$(document).ready(function() {
    const propertyId = $('#propertyId').val();
    if (propertyId) {
        PropertyExtended.init(propertyId);
    }
});
