<!-- app/Views/admin/properties/config/edit.php -->
<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Configuration: <?= ucfirst($type) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="/admin/properties/config" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="configForm">
                        <h5>Features Activées</h5>
                        <div class="row">
                            <?php foreach ($availableFeatures as $key => $label): ?>
                            <div class="col-md-6">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="enable_<?= $key ?>" 
                                           name="enable_<?= $key ?>"
                                           value="1"
                                           <?= $config['enable_' . $key] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="enable_<?= $key ?>">
                                        <?= $label ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <h5>Champs Requis</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="required_rooms" 
                                           name="required_rooms"
                                           value="1"
                                           <?= $config['required_rooms'] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="required_rooms">
                                        Pièces requises
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="required_financial_data" 
                                           name="required_financial_data"
                                           value="1"
                                           <?= $config['required_financial_data'] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="required_financial_data">
                                        Données financières requises
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5>Paramètres Avancés</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Maximum de pièces autorisées</label>
                                    <input type="number" class="form-control" 
                                           name="max_rooms_allowed" 
                                           value="<?= $config['max_rooms_allowed'] ?? 50 ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Méthode d'évaluation par défaut</label>
                                    <select class="form-control" name="default_valuation_method">
                                        <option value="market_comparison" <?= $config['default_valuation_method'] === 'market_comparison' ? 'selected' : '' ?>>Comparaison marché</option>
                                        <option value="income_approach" <?= $config['default_valuation_method'] === 'income_approach' ? 'selected' : '' ?>>Approche revenus</option>
                                        <option value="cost_approach" <?= $config['default_valuation_method'] === 'cost_approach' ? 'selected' : '' ?>>Approche coûts</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-switch mt-4">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="show_roi_metrics" 
                                           name="show_roi_metrics"
                                           value="1"
                                           <?= $config['show_roi_metrics'] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="show_roi_metrics">
                                        Afficher métriques ROI
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Enregistrer la Configuration
                            </button>
                            <button type="button" class="btn btn-warning" id="resetBtn">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#configForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {};
        $(this).serializeArray().forEach(item => {
            formData[item.name] = item.value;
        });
        
        // Ajouter les checkboxes non cochées
        $('input[type="checkbox"]').each(function() {
            if (!$(this).is(':checked')) {
                formData[$(this).attr('name')] = 0;
            }
        });
        
        $.ajax({
            url: '/admin/properties/config/<?= $type ?>',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.success) {
                    alert('Configuration mise à jour avec succès');
                    window.location.href = '/admin/properties/config';
                } else {
                    alert('Erreur: ' + response.message);
                }
            },
            error: function() {
                alert('Erreur lors de la sauvegarde');
            }
        });
    });
    
    $('#resetBtn').on('click', function() {
        if (confirm('Réinitialiser la configuration aux valeurs par défaut ?')) {
            $.ajax({
                url: '/admin/properties/config/<?= $type ?>/reset',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        alert('Configuration réinitialisée');
                        location.reload();
                    }
                },
                error: function() {
                    alert('Erreur lors de la réinitialisation');
                }
            });
        }
    });
});
</script>

<?= $this->endSection() ?>
