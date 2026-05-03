<?php
/**
 * Migration temporaire — manager_id sur la table users (organigramme)
 * Exécuter UNE seule fois puis supprimer ce fichier.
 *
 * Accès : /migrate_manager.php?token=reb2026manager
 */

$token = $_GET['token'] ?? '';
if ($token !== 'reb2026manager') {
    http_response_code(403);
    die('Accès refusé.');
}

$host   = getenv('DB_HOSTNAME') ?: 'localhost';
$dbname = getenv('DB_DATABASE') ?: 'rebe_RebenciaDB';
$user   = getenv('DB_USERNAME') ?: 'rebe_DB_User';
$pass   = getenv('DB_PASSWORD') ?: '';

$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k); $v = trim($v, " \t\n\r\"'");
            match ($k) {
                'database.default.hostname' => $host   = $v,
                'database.default.database' => $dbname = $v,
                'database.default.username' => $user   = $v,
                'database.default.password' => $pass   = $v,
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

$steps = [
    ['ADD COLUMN manager_id', "
        ALTER TABLE `users`
        ADD COLUMN IF NOT EXISTS `manager_id` INT UNSIGNED NULL DEFAULT NULL AFTER `agency_id`
    "],
    ['ADD FK fk_users_manager_id', "
        ALTER TABLE `users`
        ADD CONSTRAINT IF NOT EXISTS `fk_users_manager_id`
        FOREIGN KEY (`manager_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
    "],
];

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Migration manager_id</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container" style="max-width:700px;">
<h3>Migration : manager_id (organigramme)</h3>
<p><strong>Base :</strong> <?= htmlspecialchars($dbname) ?> sur <strong><?= htmlspecialchars($host) ?></strong></p>
<?php
foreach ($steps as [$label, $sql]) {
    try {
        $pdo->exec($sql);
        echo "<div class='alert alert-success py-1 mb-1'>✅ {$label}</div>";
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        // Ignore "duplicate column" or "already exists" errors
        if (str_contains($msg, 'Duplicate') || str_contains($msg, 'already exists') || str_contains($msg, 'errno: 121')) {
            echo "<div class='alert alert-warning py-1 mb-1'>⚠️ {$label} — déjà existant, ignoré</div>";
        } else {
            echo "<div class='alert alert-danger py-1 mb-1'>❌ {$label} — " . htmlspecialchars($msg) . "</div>";
        }
    }
}
?>
<hr>
<div class="alert alert-success">✅ Migration manager_id appliquée.</div>
<p>⚠️ <strong>Supprimez ce fichier immédiatement</strong> : <code>/migrate_manager.php</code></p>
</div>
</body>
</html>
