<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .test-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-card h3 {
            margin-top: 0;
            color: #555;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .test-result {
            font-size: 16px;
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .error { color: #dc3545; }
        .summary {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .summary h2 {
            margin: 0 0 10px 0;
            font-size: 48px;
            color: #007bff;
        }
        .summary p {
            margin: 0;
            color: #666;
            font-size: 18px;
        }
        .back-link {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background: #0056b3;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>🧪 <?= esc($title) ?></h1>
    
    <div class="summary">
        <?php
        $total = count($results);
        $success = count(array_filter($results, fn($r) => strpos($r, '✅') !== false));
        $warnings = count(array_filter($results, fn($r) => strpos($r, '⚠️') !== false));
        $errors = count(array_filter($results, fn($r) => strpos($r, '❌') !== false));
        ?>
        <h2><?= $success ?>/<?= $total ?></h2>
        <p>
            <span class="success"><?= $success ?> succès</span> • 
            <span class="warning"><?= $warnings ?> avertissements</span> • 
            <span class="error"><?= $errors ?> erreurs</span>
        </p>
    </div>
    
    <div class="test-grid">
        <?php foreach ($results as $test => $result): ?>
            <div class="test-card">
                <h3><?= esc(str_replace('_', ' ', $test)) ?></h3>
                <div class="test-result 
                    <?= strpos($result, '✅') !== false ? 'success' : '' ?>
                    <?= strpos($result, '⚠️') !== false ? 'warning' : '' ?>
                    <?= strpos($result, '❌') !== false ? 'error' : '' ?>">
                    <?= $result ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (isset($property) && $property): ?>
        <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 30px;">
            <h2>Données Propriété</h2>
            <pre><?= print_r([
                'ID' => $property['id'] ?? 'N/A',
                'Type' => $property['type'] ?? 'N/A',
                'Prix' => ($property['price'] ?? 0) . ' TND',
                'Surface' => ($property['surface'] ?? 0) . ' m²',
                'Options' => count($property['options'] ?? []),
                'Pièces' => count($property['rooms'] ?? []),
                'Score Location' => $property['location_scoring']['overall_location_score'] ?? 'N/A',
                'Données Financières' => !empty($property['financial_data']) ? 'Oui' : 'Non'
            ], true) ?></pre>
        </div>
    <?php endif; ?>
    
    <div style="text-align: center;">
        <a href="/admin/properties" class="back-link">← Retour aux propriétés</a>
    </div>
</body>
</html>
