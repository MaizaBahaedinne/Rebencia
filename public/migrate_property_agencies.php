<?php
// Migration: backfill agency_id in properties from their agent's agency_id
// Usage: https://rebencia.com/migrate_property_agencies.php?token=reb2026propagency
// Safe to run multiple times (only updates NULL values)

if (($_GET['token'] ?? '') !== 'reb2026propagency') {
    http_response_code(403);
    die('Forbidden');
}

require_once __DIR__ . '/../app/Config/Database.php';

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'rebencia';

// Try production DB name if local fails
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    $db_name = 'rebe_RebenciaDB';
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        die('DB connection failed: ' . mysqli_connect_error());
    }
}
mysqli_set_charset($conn, 'utf8mb4');

echo "<pre>\n=== Backfill agency_id in properties ===\n\n";

// Count properties without agency
$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM properties WHERE agency_id IS NULL AND deleted_at IS NULL");
$row = mysqli_fetch_assoc($res);
echo "Properties without agency: " . $row['cnt'] . "\n\n";

// Backfill from agent's agency
$sql = "UPDATE properties p
        JOIN users u ON u.id = p.agent_id
        SET p.agency_id = u.agency_id
        WHERE p.agency_id IS NULL AND u.agency_id IS NOT NULL";

if (mysqli_query($conn, $sql)) {
    $affected = mysqli_affected_rows($conn);
    echo "Updated: $affected properties now have an agency.\n";
} else {
    echo "ERROR: " . mysqli_error($conn) . "\n";
}

// Show results
$res = mysqli_query($conn, "
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
while ($r = mysqli_fetch_assoc($res)) {
    echo str_pad($r['id'], 5) . str_pad(mb_substr($r['title'], 0, 38), 40) . str_pad($r['first_name'] . ' ' . $r['last_name'], 25) . ($r['agency_name'] ?? '—') . "\n";
}

mysqli_close($conn);
echo "\n=== Done ===\n</pre>";
