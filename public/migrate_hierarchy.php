<?php
/**
 * Migration temporaire — Hiérarchie des rôles Rebencia
 * Exécuter UNE seule fois puis supprimer ce fichier.
 * 
 * Accès : /migrate_hierarchy.php?token=reb2026hierarchy
 */

$token = $_GET['token'] ?? '';
if ($token !== 'reb2026hierarchy') {
    http_response_code(403);
    die('Accès refusé.');
}

// Configuration DB (même logique que CI4)
$host   = getenv('DB_HOSTNAME') ?: 'localhost';
$dbname = getenv('DB_DATABASE') ?: 'rebe_RebenciaDB';
$user   = getenv('DB_USERNAME') ?: 'rebe_DB_User';
$pass   = getenv('DB_PASSWORD') ?: '';

// Essai de lire depuis le .env CI4
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $key = trim($key); $val = trim($val, " \t\n\r\"'");
            match ($key) {
                'database.default.hostname' => $host   = $val,
                'database.default.database' => $dbname = $val,
                'database.default.username' => $user   = $val,
                'database.default.password' => $pass   = $val,
                default => null,
            };
        }
    }
}

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connexion DB échouée : ' . htmlspecialchars($e->getMessage()));
}

echo '<h3>Migration : Hiérarchie des rôles</h3>';
echo '<p><strong>Base :</strong> ' . htmlspecialchars($dbname) . ' sur <strong>' . htmlspecialchars($host) . '</strong></p>';

$steps = [

    ['Table organizations', "
        CREATE TABLE IF NOT EXISTS `organizations` (
            `id`         INT          NOT NULL AUTO_INCREMENT,
            `name`       VARCHAR(150) NOT NULL,
            `logo`       VARCHAR(255) NULL DEFAULT NULL,
            `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
            `created_at` DATETIME     NULL DEFAULT NULL,
            `updated_at` DATETIME     NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['ALTER roles ADD hierarchy_level', "
        ALTER TABLE `roles`
            ADD COLUMN IF NOT EXISTS `hierarchy_level` TINYINT(1) NOT NULL DEFAULT 5 AFTER `is_active`
    "],

    ['ALTER agencies ADD organization_id', "
        ALTER TABLE `agencies`
            ADD COLUMN IF NOT EXISTS `organization_id` INT NULL DEFAULT NULL AFTER `id`
    "],

    ['ALTER users ADD organization_id', "
        ALTER TABLE `users`
            ADD COLUMN IF NOT EXISTS `organization_id` INT NULL DEFAULT NULL AFTER `agency_id`
    "],

    ['SET hierarchy_level super_admin=1', "
        UPDATE `roles` SET `hierarchy_level` = 1 WHERE `name` = 'super_admin'
    "],
    ['SET hierarchy_level admin=2', "
        UPDATE `roles` SET `hierarchy_level` = 2 WHERE `name` = 'admin'
    "],
    ['SET hierarchy_level director/coordinator=4', "
        UPDATE `roles` SET `hierarchy_level` = 4 WHERE `name` IN ('director', 'coordinator')
    "],
    ['SET hierarchy_level expert/collaborator=5', "
        UPDATE `roles` SET `hierarchy_level` = 5 WHERE `name` IN ('expert', 'collaborator')
    "],

    ['SET hierarchy_level rôles super_admin alternatifs (ex: "Super Admin")', "
        UPDATE `roles` SET `hierarchy_level` = 1
        WHERE LOWER(`name`) LIKE '%super%admin%'
           OR LOWER(`name`) LIKE '%super admin%'
    "],

    ['INSERT rôle PDG', "
        INSERT IGNORE INTO `roles` (`name`, `label`, `description`, `color`, `is_active`, `hierarchy_level`, `created_at`, `updated_at`)
        VALUES ('pdg', 'PDG', 'Président Directeur Général — vision multi-agences', '#6f42c1', 1, 3, NOW(), NOW())
    "],
    ['INSERT rôle Directeur Général', "
        INSERT IGNORE INTO `roles` (`name`, `label`, `description`, `color`, `is_active`, `hierarchy_level`, `created_at`, `updated_at`)
        VALUES ('directeur_general', 'Directeur Général', 'Directeur Général — vision multi-agences', '#0d6efd', 1, 3, NOW(), NOW())
    "],

    ['Permissions PDG (tout sauf system.*)', "
        INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
        SELECT r.id, p.id, NOW()
        FROM `permissions` p
        CROSS JOIN `roles` r
        WHERE r.name = 'pdg'
          AND p.name NOT IN ('system.deploy', 'system.settings', 'system.logs')
    "],
    ['Permissions Directeur Général (tout sauf system.*)', "
        INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
        SELECT r.id, p.id, NOW()
        FROM `permissions` p
        CROSS JOIN `roles` r
        WHERE r.name = 'directeur_general'
          AND p.name NOT IN ('system.deploy', 'system.settings', 'system.logs')
    "],
];

foreach ($steps as [$label, $sql]) {
    try {
        $pdo->exec(trim($sql));
        echo "<div style='color:green'>✅ {$label}</div>";
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        // Ignorer les erreurs "colonne déjà présente" ou "table déjà existante"
        if (str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate column')) {
            echo "<div style='color:orange'>⏭️ SKIP {$label} (déjà présent)</div>";
        } else {
            echo "<div style='color:red'>❌ {$label} — " . htmlspecialchars($msg) . "</div>";
        }
    }
}

// Résumé final
echo '<hr>';
$rows = $pdo->query("SELECT name, label, hierarchy_level, (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) AS nb_perms FROM roles r ORDER BY hierarchy_level, id")->fetchAll(PDO::FETCH_ASSOC);
echo '<table border="1" cellpadding="4" cellspacing="0"><tr><th>Rôle</th><th>Label</th><th>Niveau</th><th>Permissions</th></tr>';
foreach ($rows as $r) {
    $lvl = $r['hierarchy_level'];
    $badge = match((int)$lvl) { 1 => '🔴 SuperAdmin', 2 => '🟠 Admin', 3 => '🟣 PDG/DG', 4 => '🟢 Dir.Agence', 5 => '🔵 Collab', default => "Niv.$lvl" };
    echo "<tr><td>{$r['name']}</td><td>{$r['label']}</td><td>{$badge}</td><td>{$r['nb_perms']}</td></tr>";
}
echo '</table>';
echo '<p style="color:green; font-weight:bold">✅ Migration hiérarchie appliquée avec succès.</p>';
echo '<p style="color:red">⚠️ <strong>Supprimez ce fichier immédiatement</strong> : <code>/migrate_hierarchy.php</code></p>';
