<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-chart-line text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Analytics</li>
            </ol>
        </nav>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Taux de Conversion</h6>
                        <h2 class="mb-0"><?= number_format($conversionRate['rate'], 2) ?>%</h2>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
                <small class="text-muted">
                    <?= $conversionRate['converted'] ?> / <?= $conversionRate['total'] ?> clients convertis
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Temps Moyen Vente</h6>
                        <h2 class="mb-0"><?= $avgSaleTime ?> <small>jours</small></h2>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <small class="text-muted">Du lead à la signature</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Pipeline Value</h6>
                        <h2 class="mb-0"><?= number_format($pipelineValue['total'], 0) ?> <small>TND</small></h2>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-funnel-dollar"></i>
                    </div>
                </div>
                <small class="text-muted"><?= $pipelineValue['count'] ?> transactions en cours</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Revenu Mensuel</h6>
                        <h2 class="mb-0"><?= number_format(end($monthlyRevenue['revenue']) ?? 0, 0) ?> <small>TND</small></h2>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <small class="text-muted"><?= end($monthlyRevenue['deals']) ?? 0 ?> deals ce mois</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Monthly Revenue Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-area text-primary"></i> Évolution Revenus (12 derniers mois)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Property Performance -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie text-success"></i> Performance par Type</h5>
            </div>
            <div class="card-body">
                <canvas id="propertyTypeChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Agents & Client Sources -->
<div class="row g-4 mb-4">
    <!-- Top Agents -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-trophy text-warning"></i> Top 10 Agents (6 derniers mois)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th class="text-center">Deals</th>
                                <th class="text-end">Revenu Total</th>
                                <th class="text-end">Commission</th>
                                <th class="text-center">Temps Moyen</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topAgents as $index => $agent): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?= $index < 3 ? 'warning' : 'secondary' ?> me-2">
                                            #<?= $index + 1 ?>
                                        </span>
                                        <?= esc($agent['name']) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?= $agent['total_deals'] ?></span>
                                    </td>
                                    <td class="text-end">
                                        <strong><?= number_format($agent['total_revenue'], 0) ?> TND</strong>
                                    </td>
                                    <td class="text-end text-success">
                                        <?= number_format($agent['total_commission'], 0) ?> TND
                                    </td>
                                    <td class="text-center">
                                        <?= round($agent['avg_deal_time']) ?> jours
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/analytics/agent/' . $agent['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Source Analysis -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-users text-info"></i> Analyse Sources Clients</h5>
            </div>
            <div class="card-body">
                <canvas id="clientSourceChart" height="180"></canvas>
                
                <div class="mt-4">
                    <?php foreach ($clientSourceAnalysis as $source): 
                        $convRate = $source['count'] > 0 ? ($source['converted'] / $source['count']) * 100 : 0;
                    ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= esc($source['source']) ?></span>
                                <span class="text-muted"><?= $source['count'] ?> clients (<?= round($convRate) ?>% convertis)</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: <?= $convRate ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Property Performance Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-building text-primary"></i> Performance par Type de Bien</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th class="text-center">Total Biens</th>
                        <th class="text-center">Vendus</th>
                        <th class="text-center">Taux Vente</th>
                        <th class="text-center">Temps Moyen Vente</th>
                        <th class="text-end">Prix Moyen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($propertyPerformance as $perf): 
                        $saleRate = $perf['total_properties'] > 0 ? ($perf['sold_count'] / $perf['total_properties']) * 100 : 0;
                    ?>
                        <tr>
                            <td>
                                <i class="fas fa-<?= $perf['type'] === 'appartement' ? 'building' : 'home' ?>"></i>
                                <?= ucfirst($perf['type']) ?>
                            </td>
                            <td class="text-center"><?= $perf['total_properties'] ?></td>
                            <td class="text-center"><?= $perf['sold_count'] ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?= $saleRate > 50 ? 'success' : ($saleRate > 20 ? 'warning' : 'danger') ?>">
                                    <?= round($saleRate) ?>%
                                </span>
                            </td>
                            <td class="text-center"><?= round($perf['avg_days_to_sell'] ?? 0) ?> jours</td>
                            <td class="text-end"><?= number_format($perf['avg_sale_price'] ?? 0, 0) ?> TND</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Monthly Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($monthlyRevenue['months']) ?>,
        datasets: [
            {
                label: 'Revenu (TND)',
                data: <?= json_encode($monthlyRevenue['revenue']) ?>,
                borderColor: 'rgb(13, 110, 253)',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            },
            {
                label: 'Nombre de Deals',
                data: <?= json_encode($monthlyRevenue['deals']) ?>,
                borderColor: 'rgb(25, 135, 84)',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Revenu (TND)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Deals'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Property Type Performance
const propertyTypeData = <?= json_encode($propertyPerformance) ?>;
const propertyTypeCtx = document.getElementById('propertyTypeChart').getContext('2d');
new Chart(propertyTypeCtx, {
    type: 'doughnut',
    data: {
        labels: propertyTypeData.map(p => p.type),
        datasets: [{
            data: propertyTypeData.map(p => p.sold_count),
            backgroundColor: [
                'rgba(13, 110, 253, 0.8)',
                'rgba(25, 135, 84, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(220, 53, 69, 0.8)',
                'rgba(13, 202, 240, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Client Source Chart
const clientSourceData = <?= json_encode($clientSourceAnalysis) ?>;
const clientSourceCtx = document.getElementById('clientSourceChart').getContext('2d');
new Chart(clientSourceCtx, {
    type: 'bar',
    data: {
        labels: clientSourceData.map(s => s.source),
        datasets: [
            {
                label: 'Total Clients',
                data: clientSourceData.map(s => s.count),
                backgroundColor: 'rgba(13, 110, 253, 0.6)'
            },
            {
                label: 'Convertis',
                data: clientSourceData.map(s => s.converted),
                backgroundColor: 'rgba(25, 135, 84, 0.6)'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<style>
.icon-box {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
}
</style>

<?= $this->endSection() ?>
