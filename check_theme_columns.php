<?php
/**
 * Script de vérification et mise à jour de la table theme_settings
 * Exécuter avec : php check_theme_columns.php
 */

require __DIR__ . '/vendor/autoload.php';

// Charger l'environnement CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== Vérification de la table theme_settings ===\n\n";

// Vérifier si la table existe
if (!$db->tableExists('theme_settings')) {
    echo "❌ La table theme_settings n'existe pas!\n";
    echo "Veuillez exécuter le fichier database_manual_setup.sql\n";
    exit(1);
}

echo "✅ La table theme_settings existe\n\n";

// Récupérer les colonnes de la table
$fields = $db->getFieldNames('theme_settings');

echo "Colonnes actuelles (" . count($fields) . ") :\n";
foreach ($fields as $field) {
    echo "  - $field\n";
}

// Liste des colonnes requises
$requiredColumns = [
    'id',
    'primary_color',
    'secondary_color',
    'accent_color',
    'text_dark',
    'text_light',
    'background_light',
    'font_family_primary',
    'font_family_secondary',
    'font_size_base',
    'border_radius',
    'button_bg_color',
    'button_text_color',
    'button_hover_bg_color',
    'button_hover_text_color',
    'button_border_width',
    'button_border_color',
    'button_padding',
    'button_font_size',
    'button_font_weight',
    'button_secondary_bg_color',
    'button_secondary_text_color',
    'button_secondary_hover_bg_color',
    'button_secondary_hover_text_color',
    'link_color',
    'link_hover_color',
    'link_decoration',
    'page_max_width',
    'updated_at'
];

echo "\n=== Vérification des colonnes requises ===\n\n";

$missingColumns = [];
foreach ($requiredColumns as $column) {
    if (!in_array($column, $fields)) {
        $missingColumns[] = $column;
        echo "❌ Colonne manquante: $column\n";
    } else {
        echo "✅ $column\n";
    }
}

if (!empty($missingColumns)) {
    echo "\n⚠️  Colonnes manquantes détectées !\n";
    echo "Voulez-vous exécuter le script de migration maintenant ? (y/n): ";
    
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    
    if (trim($line) === 'y') {
        echo "\n=== Exécution de la migration ===\n\n";
        
        // Lire et exécuter le fichier SQL
        $sqlFile = __DIR__ . '/add_button_theme_columns.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            
            // Séparer les requêtes
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($queries as $query) {
                if (empty($query) || strpos($query, '--') === 0) {
                    continue;
                }
                
                try {
                    $db->query($query);
                    echo "✅ Requête exécutée avec succès\n";
                } catch (\Exception $e) {
                    echo "⚠️  " . $e->getMessage() . "\n";
                }
            }
            
            echo "\n✅ Migration terminée!\n";
        } else {
            echo "❌ Fichier add_button_theme_columns.sql introuvable\n";
        }
    } else {
        echo "\nMigration annulée. Exécutez manuellement :\n";
        echo "mysql -u votre_user -p votre_database < add_button_theme_columns.sql\n";
    }
} else {
    echo "\n✅ Toutes les colonnes requises sont présentes!\n";
}

// Vérifier les données
echo "\n=== Données actuelles ===\n\n";
$query = $db->query("SELECT * FROM theme_settings LIMIT 1");
$row = $query->getRowArray();

if ($row) {
    echo "Nombre de champs dans la base: " . count($row) . "\n";
    echo "Exemple de valeurs:\n";
    echo "  - primary_color: " . ($row['primary_color'] ?? 'N/A') . "\n";
    echo "  - button_bg_color: " . ($row['button_bg_color'] ?? 'N/A') . "\n";
    echo "  - link_color: " . ($row['link_color'] ?? 'N/A') . "\n";
    echo "  - page_max_width: " . ($row['page_max_width'] ?? 'N/A') . "\n";
} else {
    echo "❌ Aucune donnée dans la table theme_settings\n";
    echo "Exécutez database_manual_setup.sql pour initialiser les données\n";
}

echo "\n=== Vérification terminée ===\n";
