<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-cog text-secondary"></i>
            <?= esc($page_title) ?>
        </h1>
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Paramètres</li>
            </ol>
        </nav>
    </div>
</div>

<form action="<?= base_url('admin/settings/update') ?>" method="POST">
    
    <?php 
    $categoryLabels = [
        'general' => ['label' => 'Paramètres Généraux', 'icon' => 'fa-cog', 'color' => 'primary'],
        'commissions' => ['label' => 'Commissions', 'icon' => 'fa-dollar-sign', 'color' => 'success'],
        'email' => ['label' => 'Configuration Email', 'icon' => 'fa-envelope', 'color' => 'info'],
        'notifications' => ['label' => 'Notifications', 'icon' => 'fa-bell', 'color' => 'warning'],
        'integrations' => ['label' => 'Intégrations', 'icon' => 'fa-plug', 'color' => 'danger'],
        'template' => ['label' => 'Apparence & Template', 'icon' => 'fa-palette', 'color' => 'purple']
    ];
    
    foreach ($settings as $category => $categorySettings): 
        $categoryInfo = $categoryLabels[$category] ?? ['label' => ucfirst($category), 'icon' => 'fa-cog', 'color' => 'secondary'];
    ?>
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-<?= $categoryInfo['color'] ?> bg-opacity-10">
                <h5 class="mb-0 text-<?= $categoryInfo['color'] ?>">
                    <i class="fas <?= $categoryInfo['icon'] ?>"></i>
                    <?= $categoryInfo['label'] ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($categorySettings as $setting): ?>
                        <div class="col-md-6">
                            <label class="form-label">
                                <?= esc($setting['description'] ?: $setting['key_name']) ?>
                            </label>
                            
                            <?php if ($setting['type'] === 'boolean'): ?>
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           name="<?= esc($setting['key_name']) ?>" 
                                           value="1"
                                           <?= $setting['value'] == '1' ? 'checked' : '' ?>>
                                </div>
                            
                            <?php elseif ($setting['type'] === 'textarea'): ?>
                                <textarea class="form-control" 
                                          name="<?= esc($setting['key_name']) ?>" 
                                          rows="3"><?= esc($setting['value']) ?></textarea>
                            
                            <?php elseif ($setting['type'] === 'number'): ?>
                                <input type="number" 
                                       class="form-control" 
                                       name="<?= esc($setting['key_name']) ?>" 
                                       value="<?= esc($setting['value']) ?>"
                                       step="0.01">
                            
                            <?php elseif (strpos($setting['key_name'], 'color') !== false): ?>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color" 
                                           name="<?= esc($setting['key_name']) ?>" 
                                           value="<?= esc($setting['value']) ?>"
                                           style="max-width: 80px;">
                                    <input type="text" 
                                           class="form-control" 
                                           value="<?= esc($setting['value']) ?>"
                                           readonly
                                           style="max-width: 120px;">
                                </div>
                            
                            <?php else: ?>
                                <input type="<?= strpos($setting['key_name'], 'password') !== false ? 'password' : 'text' ?>" 
                                       class="form-control" 
                                       name="<?= esc($setting['key_name']) ?>" 
                                       value="<?= esc($setting['value']) ?>"
                                       <?= strpos($setting['key_name'], 'email') !== false ? 'type="email"' : '' ?>>
                            <?php endif; ?>
                            
                            <?php if ($setting['key_name'] === 'default_commission_rate'): ?>
                                <small class="text-muted">Taux appliqué par défaut lors de la création d'une transaction</small>
                            <?php elseif ($setting['key_name'] === 'smtp_host'): ?>
                                <small class="text-muted">Ex: smtp.gmail.com, smtp.office365.com</small>
                            <?php elseif ($setting['key_name'] === 'google_maps_api_key'): ?>
                                <small class="text-muted">Obtenir une clé API sur <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    
    <?php endforeach; ?>
    
    <div class="text-end">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Enregistrer les Paramètres
        </button>
    </div>
</form>

<?= $this->endSection() ?>
