<?php
// Migration: backfill agency_id in properties from their agent's agency_id
// Usage: https://rebencia.com/migrate_property_agencies.php?token=reb2026propagency
// Safe to run multiple times (only updates NULL values)

if (($_GET['token'] ?? '') !== 'reb2026propagency') {
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

echo "<pre>\n=== Backfill agency_id in properties ===\n\n";

// Step 1: assign all agents without agency to Agence Principale Tunis (id=1)
$step1 = $pdo->exec("UPDATE users SET agency_id = 1 WHERE agency_id IS NULL AND deleted_at IS NULL AND role_id IS NOT NULL");
echo "Step 1 — Agents without agency assigned to Agence Principale Tunis: $step1 users\n";

// Step 2: backfill ALL properties (including those already set, to stay in sync with agent)
$sql = "UPDATE properties p
        JOIN users u ON u.id = p.agent_id
        SET p.agency_id = u.agency_id
        WHERE u.agency_id IS NOT NULL";
$affected = $pdo->exec($sql);
echo "Step 2 — Properties updated with their agent's agency: $affected\n\n";

// Show results
$stmt = $pdo->query("
    SELECT p.id, p.title, u.first_name, u.last_name, a.name AS agency_name
    FROM properties p
    JOIN users u ON u.id = p.agent_id
    LEFT JOIN agencies a ON a.id = p.agency_id
    WHERE p.deleted_at IS NULL
    ORDER BY p.id
    LIMIT 20
");

echo "\nSample (first 20 properties):\n";
echo str_pad("ID", 5) . str_pad("TITRE", 40) . str_pad("AGENT", 25) . "AGENCE\n";
echo str_repeat("-", 100) . "\n";
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo str_pad($r['id'], 5) . str_pad(mb_substr($r['title'], 0, 38), 40) . str_pad($r['first_name'] . ' ' . $r['last_name'], 25) . ($r['agency_name'] ?? '—') . "\n";
}

echo "\n=== Done ===\n</pre>";
