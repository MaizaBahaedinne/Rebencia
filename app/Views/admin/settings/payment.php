<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-credit-card"></i> <?= esc($page_title) ?>
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
                        <i class="fas fa-money-check-alt"></i> Méthodes de Paiement Acceptées
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/settings/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_category" value="payment">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-left-success mb-3">
                                    <div class="card-body">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="payment_cash" name="payment_cash" 
                                                   value="1" <?= ($settings['payment_cash'] ?? '1') === '1' ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="payment_cash">
                                                <i class="fas fa-money-bill-wave text-success"></i> <strong>Espèces</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-left-info mb-3">
                                    <div class="card-body">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="payment_check" name="payment_check" 
                                                   value="1" <?= ($settings['payment_check'] ?? '1') === '1' ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="payment_check">
                                                <i class="fas fa-money-check text-info"></i> <strong>Chèque</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-left-primary mb-3">
                                    <div class="card-body">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="payment_bank_transfer" name="payment_bank_transfer" 
                                                   value="1" <?= ($settings['payment_bank_transfer'] ?? '1') === '1' ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="payment_bank_transfer">
                                                <i class="fas fa-university text-primary"></i> <strong>Virement Bancaire</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-left-warning mb-3">
                                    <div class="card-body">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="payment_card" name="payment_card" 
                                                   value="1" <?= ($settings['payment_card'] ?? '0') === '1' ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="payment_card">
                                                <i class="fas fa-credit-card text-warning"></i> <strong>Carte Bancaire</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold text-gray-800 mb-3">
                            <i class="fas fa-university"></i> Informations Bancaires
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_name">Nom de la Banque</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" 
                                           value="<?= esc($settings['bank_name'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_branch">Agence</label>
                                    <input type="text" class="form-control" id="bank_branch" name="bank_branch" 
                                           value="<?= esc($settings['bank_branch'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bank_account_name">Titulaire du Compte</label>
                            <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" 
                                   value="<?= esc($settings['bank_account_name'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_account_number">Numéro de Compte (RIB)</label>
                                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" 
                                           value="<?= esc($settings['bank_account_number'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_iban">IBAN</label>
                                    <input type="text" class="form-control" id="bank_iban" name="bank_iban" 
                                           value="<?= esc($settings['bank_iban'] ?? '') ?>" placeholder="TN59...">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bank_swift">Code SWIFT/BIC</label>
                            <input type="text" class="form-control" id="bank_swift" name="bank_swift" 
                                   value="<?= esc($settings['bank_swift'] ?? '') ?>">
                            <small class="form-text text-muted">Pour les virements internationaux</small>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold text-gray-800 mb-3">
                            <i class="fas fa-file-invoice-dollar"></i> Conditions de Paiement
                        </h6>

                        <div class="form-group">
                            <label for="payment_terms">Délai de Paiement (jours)</label>
                            <select class="form-control" id="payment_terms" name="payment_terms">
                                <option value="0" <?= ($settings['payment_terms'] ?? '0') === '0' ? 'selected' : '' ?>>Paiement immédiat</option>
                                <option value="7" <?= ($settings['payment_terms'] ?? '') === '7' ? 'selected' : '' ?>>7 jours</option>
                                <option value="15" <?= ($settings['payment_terms'] ?? '') === '15' ? 'selected' : '' ?>>15 jours</option>
                                <option value="30" <?= ($settings['payment_terms'] ?? '') === '30' ? 'selected' : '' ?>>30 jours</option>
                                <option value="45" <?= ($settings['payment_terms'] ?? '') === '45' ? 'selected' : '' ?>>45 jours</option>
                                <option value="60" <?= ($settings['payment_terms'] ?? '') === '60' ? 'selected' : '' ?>>60 jours</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="payment_notes">Notes de Paiement</label>
                            <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3"><?= esc($settings['payment_notes'] ?? 'Paiement à effectuer dans les délais convenus. Merci de mentionner le numéro de facture lors du paiement.') ?></textarea>
                            <small class="form-text text-muted">Ce texte apparaîtra sur les factures et documents de paiement</small>
                        </div>

                        <div class="form-group">
                            <label for="late_fee_percentage">Frais de Retard (%)</label>
                            <input type="number" class="form-control" id="late_fee_percentage" name="late_fee_percentage" 
                                   value="<?= esc($settings['late_fee_percentage'] ?? '0') ?>" min="0" max="100" step="0.1">
                            <small class="form-text text-muted">Pourcentage appliqué en cas de retard de paiement (0 = désactivé)</small>
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
                    <p class="mb-2"><strong>Méthodes de Paiement:</strong></p>
                    <p class="text-muted small mb-3">Sélectionnez les moyens de paiement acceptés par votre agence. Ils apparaîtront dans les options lors de l'enregistrement des transactions.</p>

                    <p class="mb-2"><strong>Informations Bancaires:</strong></p>
                    <p class="text-muted small mb-3">Ces informations seront affichées sur les factures et documents de paiement.</p>

                    <p class="mb-2"><strong>IBAN Tunisie:</strong></p>
                    <p class="text-muted small mb-0">Format: TN59 XXXX XXXX XXXX XXXX XXXX XX (24 caractères)</p>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-success">
                <div class="card-body">
                    <h6 class="font-weight-bold text-success mb-2">
                        <i class="fas fa-check-circle"></i> Bonnes Pratiques
                    </h6>
                    <ul class="small mb-0">
                        <li>Vérifiez régulièrement vos informations bancaires</li>
                        <li>Définissez des délais de paiement clairs</li>
                        <li>Activez les frais de retard si nécessaire</li>
                        <li>Conservez une copie des RIB clients</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
