<?php
// Désactive toutes les zones géographiques
// Usage: https://rebencia.com/deactivate_zones.php?token=reb2026zones

if (($_GET['token'] ?? '') !== 'reb2026zones') {
    http_response_code(403);
    die('Forbidden');
}

$envFile = dirname(__DIR__) . '/.env';
$dbHost = 'localhost'; $dbUser = 'root'; $dbPass = ''; $dbName = 'rebencia';
if (file_exists($envFile)) {
    foreach (file($envFile) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_starts_with($line, 'database.default.hostname')) $dbHost = explode('=', $line, 2)[1] ?? $dbHost;
        if (str_starts_with($line, 'database.default.username')) $dbUser = explode('=', $line, 2)[1] ?? $dbUser;
        if (str_starts_with($line, 'database.default.password')) $dbPass = explode('=', $line, 2)[1] ?? $dbPass;
        if (str_starts_with($line, 'database.default.database'))  $dbName = explode('=', $line, 2)[1] ?? $dbName;
    }
}
$dbHost = trim(str_replace(['"',"'", ' '], '', $dbHost));
$dbUser = trim(str_replace(['"',"'", ' '], '', $dbUser));
$dbPass = trim(str_replace(['"',"'"], '', $dbPass));
$dbName = trim(str_replace(['"',"'", ' '], '', $dbName));

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die('DB connection failed: ' . htmlspecialchars($e->getMessage()));
}

echo "<pre>\n=== Désactivation de toutes les zones ===\n\n";

// Compter avant
$before = $pdo->query("SELECT COUNT(*) FROM zones WHERE is_active = 1")->fetchColumn();
echo "Zones actives avant : $before\n";

// Désactiver tout
$affected = $pdo->exec("UPDATE zones SET is_active = 0");
echo "Zones désactivées   : $affected\n";

// Vérifier
$after = $pdo->query("SELECT COUNT(*) FROM zones WHERE is_active = 1")->fetchColumn();
echo "Zones actives après : $after\n";

echo "\n=== Done ===\n</pre>";
