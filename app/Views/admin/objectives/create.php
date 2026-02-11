<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-bullseye me-2"></i><?= $title ?></h4>
    <a href="<?= base_url('admin/objectives') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?= base_url('admin/objectives/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type d'objectif <span class="text-danger">*</span></label>
                    <select name="type" id="objectiveType" class="form-select" required>
                        <option value="">Sélectionner...</option>
                        <option value="personal" <?= old('type') === 'personal' ? 'selected' : '' ?>>Personnel</option>
                        <option value="agency" <?= old('type') === 'agency' ? 'selected' : '' ?>>Agence</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Période (Mois) <span class="text-danger">*</span></label>
                    <input type="month" name="period" class="form-control" value="<?= old('period', date('Y-m')) ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3" id="userField" style="display: none;">
                    <label class="form-label">Utilisateur <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select">
                        <option value="">Sélectionner un utilisateur...</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3" id="agencyField" style="display: none;">
                    <label class="form-label">Agence <span class="text-danger">*</span></label>
                    <select name="agency_id" class="form-select">
                        <option value="">Sélectionner une agence...</option>
                        <?php foreach ($agencies as $agency): ?>
                            <option value="<?= $agency['id'] ?>" <?= old('agency_id') == $agency['id'] ? 'selected' : '' ?>>
                                <?= esc($agency['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <hr class="my-4">
            
            <h5 class="mb-3">Objectifs à atteindre</h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Chiffre d'affaires (DT)</label>
                    <input type="number" name="revenue_target" class="form-control" step="0.01" 
                           value="<?= old('revenue_target', 0) ?>" placeholder="0">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre de nouveaux contacts</label>
                    <input type="number" name="new_contacts_target" class="form-control" 
                           value="<?= old('new_contacts_target', 0) ?>" placeholder="0">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Biens pour location</label>
                    <input type="number" name="properties_rent_target" class="form-control" 
                           value="<?= old('properties_rent_target', 0) ?>" placeholder="0">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Biens pour vente</label>
                    <input type="number" name="properties_sale_target" class="form-control" 
                           value="<?= old('properties_sale_target', 0) ?>" placeholder="0">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nombre de transactions</label>
                    <input type="number" name="transactions_target" class="form-control" 
                           value="<?= old('transactions_target', 0) ?>" placeholder="0">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" 
                          placeholder="Notes ou commentaires..."><?= old('notes') ?></textarea>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Créer l'objectif
                </button>
                <a href="<?= base_url('admin/objectives') ?>" class="btn btn-secondary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#objectiveType').on('change', function() {
        const type = $(this).val();
        
        if (type === 'personal') {
            $('#userField').show();
            $('#agencyField').hide();
            $('select[name="user_id"]').prop('required', true);
            $('select[name="agency_id"]').prop('required', false);
        } else if (type === 'agency') {
            $('#userField').hide();
            $('#agencyField').show();
            $('select[name="user_id"]').prop('required', false);
            $('select[name="agency_id"]').prop('required', true);
        } else {
            $('#userField').hide();
            $('#agencyField').hide();
            $('select[name="user_id"]').prop('required', false);
            $('select[name="agency_id"]').prop('required', false);
        }
    });
    
    // Trigger on page load if type is selected
    <?php if (old('type')): ?>
    $('#objectiveType').trigger('change');
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
