<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-dollar-sign"></i> Détails Commission
    </h1>
    <div>
        <a href="<?= base_url('admin/transactions') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <?php if ($commission && $commission['payment_status'] !== 'paid' && canUpdate('transactions')): ?>
            <a href="<?= base_url('admin/transactions/mark-commission-paid/' . $transaction['id']) ?>" 
               class="btn btn-success"
               onclick="return confirm('Confirmer le paiement de cette commission ?')">
                <i class="fas fa-check"></i> Marquer Payée
            </a>
        <?php endif; ?>
        <?php if (canUpdate('transactions')): ?>
            <a href="<?= base_url('admin/transactions/recalculate-commission/' . $transaction['id']) ?>" 
               class="btn btn-warning"
               onclick="return confirm('Recalculer cette commission ? Cela écrasera les données actuelles.')">
                <i class="fas fa-sync"></i> Recalculer
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Informations Transaction -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-contract"></i> Transaction</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Référence :</strong></td>
                        <td><?= esc($transaction['reference']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Type :</strong></td>
                        <td>
                            <span class="badge bg-<?= $transaction['type'] === 'sale' ? 'primary' : 'success' ?>">
                                <?= $transaction['type'] === 'sale' ? 'Vente' : 'Location' ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Montant :</strong></td>
                        <td><strong><?= number_format($transaction['amount'], 2) ?> TND</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Date :</strong></td>
                        <td><?= date('d/m/Y', strtotime($transaction['transaction_date'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Statut :</strong></td>
                        <td>
                            <span class="badge bg-info"><?= ucfirst($transaction['status']) ?></span>
                        </td>
                    </tr>
                </table>
                
                <hr>
                
                <h6 class="mb-3"><i class="fas fa-building"></i> Bien</h6>
                <p class="mb-1"><strong><?= esc($property['title'] ?? 'N/A') ?></strong></p>
                <p class="text-muted small mb-0">
                    <?= ucfirst($property['type'] ?? 'N/A') ?> - 
                    Réf: <?= esc($property['reference'] ?? 'N/A') ?>
                </p>
                
                <hr>
                
                <h6 class="mb-3"><i class="fas fa-user-tie"></i> Agent</h6>
                <p class="mb-0">
                    <strong><?= esc($agent['first_name'] ?? '') ?> <?= esc($agent['last_name'] ?? '') ?></strong>
                </p>
                
                <hr>
                
                <h6 class="mb-3"><i class="fas fa-users"></i> Parties</h6>
                <p class="mb-1"><strong>Acheteur/Locataire :</strong><br>
                    <?= esc($buyer['first_name'] ?? '') ?> <?= esc($buyer['last_name'] ?? '') ?>
                </p>
                <?php if ($seller): ?>
                <p class="mb-0"><strong>Vendeur/Propriétaire :</strong><br>
                    <?= esc($seller['first_name'] ?? '') ?> <?= esc($seller['last_name'] ?? '') ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Détails Commission -->
    <div class="col-md-8">
        <?php if ($commission): ?>
            <!-- Statut Paiement -->
            <div class="alert alert-<?= $commission['payment_status'] === 'paid' ? 'success' : 'warning' ?> mb-4">
                <i class="fas fa-<?= $commission['payment_status'] === 'paid' ? 'check-circle' : 'clock' ?>"></i>
                <strong>Statut : </strong>
                <?php
                $statusLabels = [
                    'pending' => 'En attente de paiement',
                    'partial' => 'Partiellement payée',
                    'paid' => 'Payée'
                ];
                echo $statusLabels[$commission['payment_status']] ?? $commission['payment_status'];
                ?>
                <?php if ($commission['payment_date']): ?>
                    - Payée le <?= date('d/m/Y', strtotime($commission['payment_date'])) ?>
                <?php endif; ?>
            </div>

            <!-- Règle Appliquée -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Règle Appliquée</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Niveau :</strong> 
                                <span class="badge bg-<?= $commission['override_level'] === 'user' ? 'danger' : 
                                    ($commission['override_level'] === 'role' ? 'warning' : 
                                    ($commission['override_level'] === 'agency' ? 'info' : 'secondary')) ?>">
                                    <?= ucfirst($commission['override_level']) ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Type Commission :</strong> 
                                <?= ucfirst($commission['buyer_commission_type']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calcul Acheteur/Locataire -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Commission Acheteur/Locataire</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td>Base de calcul :</td>
                            <td class="text-end">
                                <strong><?= $commission['buyer_commission_value'] ?> 
                                <?= $commission['buyer_commission_type'] === 'percentage' ? '%' : 
                                    ($commission['buyer_commission_type'] === 'months' ? 'mois' : 'TND') ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Montant HT :</td>
                            <td class="text-end"><?= number_format($commission['buyer_commission_ht'], 2) ?> TND</td>
                        </tr>
                        <tr>
                            <td>TVA (19%) :</td>
                            <td class="text-end"><?= number_format($commission['buyer_commission_vat'], 2) ?> TND</td>
                        </tr>
                        <tr class="table-active">
                            <td><strong>Total TTC :</strong></td>
                            <td class="text-end"><strong class="text-primary fs-5"><?= number_format($commission['buyer_commission_ttc'], 2) ?> TND</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Calcul Vendeur/Propriétaire -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-home"></i> Commission Vendeur/Propriétaire</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td>Base de calcul :</td>
                            <td class="text-end">
                                <strong><?= $commission['seller_commission_value'] ?> 
                                <?= $commission['seller_commission_type'] === 'percentage' ? '%' : 
                                    ($commission['seller_commission_type'] === 'months' ? 'mois' : 'TND') ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Montant HT :</td>
                            <td class="text-end"><?= number_format($commission['seller_commission_ht'], 2) ?> TND</td>
                        </tr>
                        <tr>
                            <td>TVA (19%) :</td>
                            <td class="text-end"><?= number_format($commission['seller_commission_vat'], 2) ?> TND</td>
                        </tr>
                        <tr class="table-active">
                            <td><strong>Total TTC :</strong></td>
                            <td class="text-end"><strong class="text-success fs-5"><?= number_format($commission['seller_commission_ttc'], 2) ?> TND</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Total Commission -->
            <div class="card bg-dark text-white mb-4">
                <div class="card-body">
                    <h3 class="mb-3"><i class="fas fa-coins"></i> Commission Totale</h3>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-1">HT</div>
                            <h4><?= number_format($commission['total_commission_ht'], 2) ?> TND</h4>
                        </div>
                        <div class="col-4">
                            <div class="mb-1">TVA</div>
                            <h4><?= number_format($commission['total_commission_vat'], 2) ?> TND</h4>
                        </div>
                        <div class="col-4">
                            <div class="mb-1">TTC</div>
                            <h3 class="text-warning"><?= number_format($commission['total_commission_ttc'], 2) ?> TND</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Répartition Agent/Agence -->
            <?php if ($commission['agent_commission_amount'] && $commission['agency_commission_amount']): ?>
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-percentage"></i> Répartition Agent / Agence</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h6 class="text-muted">Part Agent (<?= $commission['agent_commission_percentage'] ?>%)</h6>
                                <h3 class="text-primary"><?= number_format($commission['agent_commission_amount'], 2) ?> TND</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Part Agence (<?= 100 - $commission['agent_commission_percentage'] ?>%)</h6>
                            <h3 class="text-success"><?= number_format($commission['agency_commission_amount'], 2) ?> TND</h3>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Aucune commission calculée pour cette transaction.</strong>
                <p class="mb-0 mt-2">
                    Cliquez sur "Recalculer" pour générer automatiquement la commission selon les règles configurées.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
