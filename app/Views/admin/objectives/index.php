<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-bullseye text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
    </div>
</div>

<?php if ($objective): ?>
    <!-- Progress Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Propriétés</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?= $objective['achieved_properties'] ?> / <?= $objective['target_properties'] ?></span>
                        <strong><?= round($progress['properties']) ?>%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: <?= min(100, $progress['properties']) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Clients</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?= $objective['achieved_clients'] ?> / <?= $objective['target_clients'] ?></span>
                        <strong><?= round($progress['clients']) ?>%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?= min(100, $progress['clients']) %>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Deals Signés</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?= $objective['achieved_deals'] ?> / <?= $objective['target_deals'] ?></span>
                        <strong><?= round($progress['deals']) ?>%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: <?= min(100, $progress['deals']) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Chiffre d'Affaires</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?= number_format($objective['achieved_revenue'], 0) ?> / <?= number_format($objective['target_revenue'], 0) ?> TND</span>
                        <strong><?= round($progress['revenue']) ?>%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: <?= min(100, $progress['revenue']) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Progress -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center py-5">
            <h2>Progression Globale</h2>
            <div class="display-1 text-primary mb-3"><?= round($progress['overall']) ?>%</div>
            
            <?php if ($objective['bonus_earned'] > 0): ?>
                <div class="alert alert-success">
                    <i class="fas fa-trophy"></i>
                    <strong>Félicitations !</strong> Vous avez atteint vos objectifs !
                    <br>Bonus gagné : <strong><?= number_format($objective['bonus_earned'], 2) ?> TND</strong>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Aucun objectif défini pour ce mois. Contactez votre manager.
    </div>
<?php endif; ?>

<!-- Leaderboard -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-trophy text-warning"></i>
            Classement du Mois
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Agent</th>
                        <th class="text-center">Deals</th>
                        <th class="text-end">Chiffre d'Affaires</th>
                        <th class="text-center">Progression</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $index => $agent): 
                        $agentProgress = $agent['target_revenue'] > 0 ? ($agent['achieved_revenue'] / $agent['target_revenue']) * 100 : 0;
                    ?>
                        <tr class="<?= $agent['user_id'] == session()->get('user_id') ? 'table-primary' : '' ?>">
                            <td>
                                <span class="badge bg-<?= $index < 3 ? 'warning' : 'secondary' ?>" style="font-size: 1.1em;">
                                    <?= $index < 3 ? '🏆' : '' ?> #<?= $index + 1 ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= esc($agent['agent_name']) ?></strong>
                                <?= $agent['user_id'] == session()->get('user_id') ? '<span class="badge bg-primary ms-2">Vous</span>' : '' ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= $agent['achieved_deals'] ?></span>
                            </td>
                            <td class="text-end">
                                <strong><?= number_format($agent['achieved_revenue'], 0) ?> TND</strong>
                            </td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px; min-width: 100px;">
                                    <div class="progress-bar bg-success" style="width: <?= min(100, round($agentProgress)) ?>%">
                                        <?= round($agentProgress) ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
