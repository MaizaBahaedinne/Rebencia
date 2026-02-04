<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sms"></i> <?= esc($page_title) ?>
        </h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-mobile-alt"></i> Configuration SMS
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/settings/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_category" value="sms">

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="sms_enabled" name="sms_enabled" 
                                       value="1" <?= ($settings['sms_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="sms_enabled">Activer l'envoi de SMS</label>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="sms_provider">Fournisseur SMS</label>
                            <select class="form-control" id="sms_provider" name="sms_provider" onchange="updateProviderFields()">
                                <option value="twilio" <?= ($settings['sms_provider'] ?? 'twilio') === 'twilio' ? 'selected' : '' ?>>Twilio</option>
                                <option value="vonage" <?= ($settings['sms_provider'] ?? '') === 'vonage' ? 'selected' : '' ?>>Vonage (Nexmo)</option>
                                <option value="tunisiesms" <?= ($settings['sms_provider'] ?? '') === 'tunisiesms' ? 'selected' : '' ?>>Tunisie SMS</option>
                                <option value="custom" <?= ($settings['sms_provider'] ?? '') === 'custom' ? 'selected' : '' ?>>Personnalisé</option>
                            </select>
                        </div>

                        <div class="form-group" id="field_account_sid">
                            <label for="sms_account_sid">Account SID / API Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sms_account_sid" name="sms_account_sid" 
                                   value="<?= esc($settings['sms_account_sid'] ?? '') ?>">
                        </div>

                        <div class="form-group" id="field_auth_token">
                            <label for="sms_auth_token">Auth Token / API Secret <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="sms_auth_token" name="sms_auth_token" 
                                       value="<?= esc($settings['sms_auth_token'] ?? '') ?>" autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSmsPassword()">
                                        <i class="fas fa-eye" id="toggleSmsIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Laissez vide pour ne pas modifier</small>
                        </div>

                        <div class="form-group">
                            <label for="sms_sender_id">Numéro/ID Expéditeur</label>
                            <input type="text" class="form-control" id="sms_sender_id" name="sms_sender_id" 
                                   value="<?= esc($settings['sms_sender_id'] ?? 'REBENCIA') ?>">
                            <small class="form-text text-muted">Numéro de téléphone ou nom alphanumérique (max 11 caractères)</small>
                        </div>

                        <div class="form-group" id="field_api_url">
                            <label for="sms_api_url">URL de l'API (pour fournisseur personnalisé)</label>
                            <input type="url" class="form-control" id="sms_api_url" name="sms_api_url" 
                                   value="<?= esc($settings['sms_api_url'] ?? '') ?>">
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Notifications par SMS</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="sms_new_lead" name="sms_new_lead" 
                                       value="1" <?= ($settings['sms_new_lead'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="sms_new_lead">Nouveau lead / demande de contact</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="sms_appointment" name="sms_appointment" 
                                       value="1" <?= ($settings['sms_appointment'] ?? '1') === '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="sms_appointment">Rappel de rendez-vous (24h avant)</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="sms_transaction" name="sms_transaction" 
                                       value="1" <?= ($settings['sms_transaction'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="sms_transaction">Confirmation de transaction</label>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" onclick="testSms()">
                                <i class="fas fa-paper-plane"></i> Tester la Configuration
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-question-circle"></i> Twilio
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2"><strong>1. Créer un compte:</strong> <a href="https://www.twilio.com/try-twilio" target="_blank">twilio.com/try-twilio</a></p>
                    <p class="small mb-2"><strong>2. Récupérer les identifiants:</strong></p>
                    <ul class="small mb-2">
                        <li>Account SID</li>
                        <li>Auth Token</li>
                    </ul>
                    <p class="small mb-2"><strong>3. Acheter un numéro:</strong></p>
                    <p class="text-muted small mb-0">Depuis la console Twilio → Phone Numbers → Buy a Number</p>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-2">
                        <i class="fas fa-info-circle"></i> Tunisie SMS
                    </h6>
                    <p class="small mb-2">Service local tunisien pour l'envoi de SMS en masse.</p>
                    <p class="small mb-2"><strong>Website:</strong> <a href="https://www.tunisiesms.tn" target="_blank">tunisiesms.tn</a></p>
                    <p class="text-muted small mb-0">Configuration: API Key + Secret depuis votre espace client</p>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-warning">
                <div class="card-body">
                    <h6 class="font-weight-bold text-warning mb-2">
                        <i class="fas fa-exclamation-triangle"></i> Coûts
                    </h6>
                    <p class="small mb-2"><strong>Twilio:</strong> ~0.0075 USD/SMS</p>
                    <p class="small mb-2"><strong>Vonage:</strong> ~0.01 USD/SMS</p>
                    <p class="text-muted small mb-0">Les prix varient selon les pays de destination</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSmsPassword() {
    const input = document.getElementById('sms_auth_token');
    const icon = document.getElementById('toggleSmsIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function updateProviderFields() {
    const provider = document.getElementById('sms_provider').value;
    const apiUrlField = document.getElementById('field_api_url');
    
    if (provider === 'custom') {
        apiUrlField.style.display = 'block';
    } else {
        apiUrlField.style.display = 'none';
    }
}

function testSms() {
    const testPhone = prompt('Entrez un numéro de téléphone pour recevoir un SMS de test (+216XXXXXXXX):');
    if (testPhone) {
        alert('Fonctionnalité de test à implémenter. SMS de test serait envoyé à: ' + testPhone);
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    updateProviderFields();
});
</script>
<?= $this->endSection() ?>
