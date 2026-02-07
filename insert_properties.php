<?php
// Script d'insertion des 100 propriétés dans la base de données
$sqlFile = __DIR__ . '/properties_insert.sql';
$sql = file_get_contents($sqlFile);

// Configuration de la base de données
$host = 'localhost';
$dbname = 'rebe_RebenciaDB';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connexion à la base de données réussie.\n";
    echo "Insertion des 100 propriétés en cours...\n";
    
    $pdo->exec($sql);
    
    echo "✅ 100 propriétés insérées avec succès !\n\n";
    
    // Vérification
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM properties WHERE reference LIKE 'PROP-20260207-%'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total de propriétés insérées : " . $result['total'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
