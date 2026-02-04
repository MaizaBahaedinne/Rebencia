<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bell"></i> <?= esc($page_title) ?>
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
            <!-- Email Notifications -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-envelope"></i> Notifications par Email
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/settings/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_category" value="notifications">

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-gray-800 mb-3">Biens Immobiliers</h6>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_property_created" name="notif_email_property_created" 
                                           value="1" <?= ($settings['notif_email_property_created'] ?? '0') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_property_created">Nouveau bien ajouté</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_property_updated" name="notif_email_property_updated" 
                                           value="1" <?= ($settings['notif_email_property_updated'] ?? '0') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_property_updated">Bien modifié</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_property_sold" name="notif_email_property_sold" 
                                           value="1" <?= ($settings['notif_email_property_sold'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_property_sold">Bien vendu/loué</label>
                                </div>

                                <h6 class="font-weight-bold text-gray-800 mb-3">Transactions</h6>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_transaction_created" name="notif_email_transaction_created" 
                                           value="1" <?= ($settings['notif_email_transaction_created'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_transaction_created">Nouvelle transaction</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_transaction_completed" name="notif_email_transaction_completed" 
                                           value="1" <?= ($settings['notif_email_transaction_completed'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_transaction_completed">Transaction finalisée</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-gray-800 mb-3">Clients</h6>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_client_created" name="notif_email_client_created" 
                                           value="1" <?= ($settings['notif_email_client_created'] ?? '0') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_client_created">Nouveau client ajouté</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_lead_received" name="notif_email_lead_received" 
                                           value="1" <?= ($settings['notif_email_lead_received'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_lead_received">Nouveau lead reçu</label>
                                </div>

                                <h6 class="font-weight-bold text-gray-800 mb-3">Utilisateurs</h6>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_user_created" name="notif_email_user_created" 
                                           value="1" <?= ($settings['notif_email_user_created'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_user_created">Nouvel utilisateur créé</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_email_user_login" name="notif_email_user_login" 
                                           value="1" <?= ($settings['notif_email_user_login'] ?? '0') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_email_user_login">Connexion utilisateur</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold text-gray-800 mb-3">
                            <i class="fas fa-sms"></i> Notifications par SMS
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_sms_appointment" name="notif_sms_appointment" 
                                           value="1" <?= ($settings['notif_sms_appointment'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_sms_appointment">Rappel rendez-vous (24h avant)</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_sms_transaction" name="notif_sms_transaction" 
                                           value="1" <?= ($settings['notif_sms_transaction'] ?? '0') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_sms_transaction">Confirmation de transaction</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_sms_lead" name="notif_sms_lead" 
                                           value="1" <?= ($settings['notif_sms_lead'] ?? '0') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_sms_lead">Nouveau lead (à l'agent)</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_sms_payment" name="notif_sms_payment" 
                                           value="1" <?= ($settings['notif_sms_payment'] ?? '0') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_sms_payment">Confirmation de paiement</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold text-gray-800 mb-3">
                            <i class="fas fa-laptop"></i> Notifications Internes (In-App)
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_inapp_enabled" name="notif_inapp_enabled" 
                                           value="1" <?= ($settings['notif_inapp_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_inapp_enabled">Activer les notifications internes</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="notif_inapp_sound" name="notif_inapp_sound" 
                                           value="1" <?= ($settings['notif_inapp_sound'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="notif_inapp_sound">Son de notification</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notif_retention_days">Conservation (jours)</label>
                                    <input type="number" class="form-control" id="notif_retention_days" name="notif_retention_days" 
                                           value="<?= esc($settings['notif_retention_days'] ?? '30') ?>" min="7" max="365">
                                    <small class="form-text text-muted">Durée de conservation des notifications</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
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
                        <i class="fas fa-info-circle"></i> Informations
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Notifications Email:</strong></p>
                    <p class="text-muted small mb-3">Les emails seront envoyés aux administrateurs et utilisateurs concernés selon leurs rôles.</p>

                    <p class="mb-2"><strong>Notifications SMS:</strong></p>
                    <p class="text-muted small mb-3">Assurez-vous d'avoir configuré un fournisseur SMS dans la section "Configuration SMS".</p>

                    <p class="mb-2"><strong>Notifications Internes:</strong></p>
                    <p class="text-muted small mb-0">Affichées dans l'interface sous forme de badges et pop-ups.</p>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-warning">
                <div class="card-body">
                    <h6 class="font-weight-bold text-warning mb-2">
                        <i class="fas fa-exclamation-triangle"></i> Attention
                    </h6>
                    <p class="small mb-2">Trop de notifications peuvent:</p>
                    <ul class="small mb-0">
                        <li>Réduire la productivité des utilisateurs</li>
                        <li>Augmenter les coûts SMS/Email</li>
                        <li>Être considérées comme spam</li>
                    </ul>
                    <p class="small text-muted mt-2 mb-0">Activez uniquement les notifications essentielles.</p>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-success">
                <div class="card-body">
                    <h6 class="font-weight-bold text-success mb-2">
                        <i class="fas fa-lightbulb"></i> Recommandations
                    </h6>
                    <ul class="small mb-0">
                        <li><strong>Email:</strong> Événements importants uniquement</li>
                        <li><strong>SMS:</strong> Rappels urgents seulement</li>
                        <li><strong>In-App:</strong> Toutes les activités en temps réel</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
