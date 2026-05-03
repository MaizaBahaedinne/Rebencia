<?php
// Script de déploiement temporaire — À SUPPRIMER après utilisation
$token = $_GET['token'] ?? '';
if ($token !== 'deploy2026rb') {
    http_response_code(403);
    die('Accès refusé');
}

header('Content-Type: text/plain; charset=utf-8');

$projectRoot = dirname(__DIR__);
chdir($projectRoot);

echo "=== Git Pull ===\n";
exec('git pull 2>&1', $out, $code);
echo implode("\n", $out) . "\n";
echo "Exit code: $code\n";
