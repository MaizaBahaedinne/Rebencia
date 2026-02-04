<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-envelope"></i> <?= esc($page_title) ?>
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
                        <i class="fas fa-server"></i> Configuration SMTP
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/settings/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_category" value="email">

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="email_enabled" name="email_enabled" 
                                       value="1" <?= ($settings['email_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="email_enabled">Activer l'envoi d'emails</label>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="smtp_host">Serveur SMTP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                   value="<?= esc($settings['smtp_host'] ?? 'smtp.gmail.com') ?>" required>
                            <small class="form-text text-muted">Ex: smtp.gmail.com, smtp.office365.com</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="smtp_port">Port <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                           value="<?= esc($settings['smtp_port'] ?? '587') ?>" required>
                                    <small class="form-text text-muted">587 (TLS) ou 465 (SSL)</small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="smtp_encryption">Chiffrement</label>
                                    <select class="form-control" id="smtp_encryption" name="smtp_encryption">
                                        <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                        <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                        <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>Aucun</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="smtp_username">Nom d'Utilisateur SMTP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                   value="<?= esc($settings['smtp_username'] ?? '') ?>" required autocomplete="off">
                            <small class="form-text text-muted">Souvent identique à l'adresse email</small>
                        </div>

                        <div class="form-group">
                            <label for="smtp_password">Mot de Passe SMTP</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                       value="<?= esc($settings['smtp_password'] ?? '') ?>" autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Laissez vide pour ne pas modifier</small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="email_from_address">Adresse Expéditeur <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email_from_address" name="email_from_address" 
                                   value="<?= esc($settings['email_from_address'] ?? 'noreply@rebencia.com') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email_from_name">Nom de l'Expéditeur</label>
                            <input type="text" class="form-control" id="email_from_name" name="email_from_name" 
                                   value="<?= esc($settings['email_from_name'] ?? 'Rebencia') ?>">
                        </div>

                        <div class="form-group">
                            <label for="email_reply_to">Adresse de Réponse</label>
                            <input type="email" class="form-control" id="email_reply_to" name="email_reply_to" 
                                   value="<?= esc($settings['email_reply_to'] ?? 'contact@rebencia.com') ?>">
                        </div>

                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" onclick="testEmail()">
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
                        <i class="fas fa-question-circle"></i> Configuration Gmail
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Serveur SMTP:</strong> smtp.gmail.com</p>
                    <p class="mb-2"><strong>Port:</strong> 587 (TLS)</p>
                    <p class="mb-2"><strong>Nom d'utilisateur:</strong> votre-email@gmail.com</p>
                    <p class="mb-3"><strong>Mot de passe:</strong> Mot de passe d'application</p>
                    
                    <div class="alert alert-warning small mb-0">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                        Pour Gmail, vous devez créer un "Mot de passe d'application" depuis votre compte Google (Sécurité > Validation en 2 étapes > Mots de passe d'applications).
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-2">
                        <i class="fas fa-info-circle"></i> Autres Fournisseurs
                    </h6>
                    
                    <p class="small mb-2"><strong>Office 365:</strong></p>
                    <p class="text-muted small mb-3">smtp.office365.com:587 (TLS)</p>
                    
                    <p class="small mb-2"><strong>Outlook.com:</strong></p>
                    <p class="text-muted small mb-3">smtp-mail.outlook.com:587 (TLS)</p>
                    
                    <p class="small mb-2"><strong>Yahoo Mail:</strong></p>
                    <p class="text-muted small mb-0">smtp.mail.yahoo.com:465 (SSL)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('smtp_password');
    const icon = document.getElementById('toggleIcon');
    
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

function testEmail() {
    const testEmail = prompt('Entrez une adresse email pour recevoir un email de test:');
    if (testEmail) {
        alert('Fonctionnalité de test à implémenter. Email de test serait envoyé à: ' + testEmail);
    }
}
</script>
<?= $this->endSection() ?>
