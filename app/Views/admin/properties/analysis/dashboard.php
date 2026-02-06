<!-- app/Views/admin/properties/analysis/dashboard.php -->
<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h2>
                <i class="fas fa-chart-line"></i> Dashboard Investisseur
                <small class="text-muted"><?= $property['reference'] ?></small>
            </h2>
            <a href="/admin/properties/view/<?= $property['id'] ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la propriété
            </a>
        </div>
    </div>
    
    <!-- Métriques Clés -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-percent"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rendement Net</span>
                    <span class="info-box-number"><?= number_format($financial['metrics']['net_yield'] ?? 0, 2) ?>%</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Cap Rate</span>
                    <span class="info-box-number"><?= number_format($financial['metrics']['cap_rate'] ?? 0, 2) ?>%</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Prix/m²</span>
                    <span class="info-box-number"><?= number_format($financial['metrics']['price_per_sqm'] ?? 0, 0) ?> TND</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Payback Period</span>
                    <span class="info-box-number"><?= number_format($financial['metrics']['payback_period'] ?? 0, 1) ?> ans</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Score d'Attractivité -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Score d'Attractivité</h3>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 30px;">
                        <?php
                        $scoreColor = $attractionScore >= 80 ? 'success' : ($attractionScore >= 60 ? 'info' : 'warning');
                        ?>
                        <div class="progress-bar bg-<?= $scoreColor ?>" 
                             role="progressbar" 
                             style="width: <?= $attractionScore ?>%;">
                            <?= $attractionScore ?>/100
                        </div>
                    </div>
                    
                    <p><strong>Évaluation:</strong> 
                        <?php if ($attractionScore >= 80): ?>
                            <span class="badge badge-success">Excellent</span>
                        <?php elseif ($attractionScore >= 60): ?>
                            <span class="badge badge-info">Bon</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Moyen</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Comparaison Marché</h3>
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> 
                        <?php
                        $status = $marketComparison['status'] ?? 'unknown';
                        $statusLabels = [
                            'underpriced' => ['label' => 'Sous-évalué', 'class' => 'success'],
                            'fair_value' => ['label' => 'Prix correct', 'class' => 'info'],
                            'overpriced' => ['label' => 'Surévalué', 'class' => 'danger']
                        ];
                        $statusInfo = $statusLabels[$status] ?? ['label' => 'Inconnu', 'class' => 'secondary'];
                        ?>
                        <span class="badge badge-<?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
                    </p>
                    
                    <p><strong>Différence vs marché:</strong> 
                        <?= number_format($marketComparison['difference_percent'] ?? 0, 1) ?>%
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Détails Financiers -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Analyse Financière Détaillée</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Prix d'Achat</th>
                            <td><?= number_format($property['price'] ?? 0, 0) ?> TND</td>
                            <th>Loyer Mensuel</th>
                            <td><?= number_format($property['rental_price'] ?? 0, 0) ?> TND</td>
                        </tr>
                        <tr>
                            <th>Revenus Annuels</th>
                            <td><?= number_format(($property['rental_price'] ?? 0) * 12, 0) ?> TND</td>
                            <th>Charges Annuelles</th>
                            <td><?= number_format($financial['annual_expenses'] ?? 0, 0) ?> TND</td>
                        </tr>
                        <tr>
                            <th>Revenu Net Annuel</th>
                            <td><strong><?= number_format($financial['net_annual_income'] ?? 0, 0) ?> TND</strong></td>
                            <th>ROI Annuel</th>
                            <td><strong><?= number_format($financial['metrics']['roi_annual'] ?? 0, 2) ?>%</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <a href="/admin/properties/<?= $property['id'] ?>/financial-report" class="btn btn-primary">
                <i class="fas fa-file-alt"></i> Rapport Financier Complet
            </a>
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
