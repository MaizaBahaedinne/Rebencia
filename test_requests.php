<!DOCTYPE html>
<html>
<head>
    <title>Test Property Requests</title>
</head>
<body>
    <h1>Test Property Requests</h1>
    <?php
    // Load CodeIgniter
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap the application
    $app = require_once FCPATH . '../app/Config/Boot/production.php';
    
    // Get database connection
    $db = \Config\Database::connect();
    
    // Query property_requests
    $query = $db->query("SELECT pr.*, p.reference, p.title, c.first_name, c.last_name, c.phone 
                         FROM property_requests pr 
                         LEFT JOIN properties p ON p.id = pr.property_id 
                         LEFT JOIN clients c ON c.id = pr.client_id 
                         ORDER BY pr.created_at DESC 
                         LIMIT 20");
    
    $requests = $query->getResultArray();
    
    echo "<h2>Total requests: " . count($requests) . "</h2>";
    
    if (empty($requests)) {
        echo "<p style='color: red;'>Aucune demande trouvée dans la base de données !</p>";
        
        // Check if table exists
        $tableQuery = $db->query("SHOW TABLES LIKE 'property_requests'");
        if ($tableQuery->getNumRows() == 0) {
            echo "<p style='color: red;'>La table 'property_requests' n'existe pas !</p>";
        } else {
            echo "<p style='color: green;'>La table 'property_requests' existe.</p>";
            
            // Check table structure
            $structureQuery = $db->query("DESCRIBE property_requests");
            $structure = $structureQuery->getResultArray();
            echo "<h3>Structure de la table:</h3>";
            echo "<pre>";
            print_r($structure);
            echo "</pre>";
        }
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Date</th><th>Type</th><th>Client</th><th>Téléphone</th><th>Propriété</th><th>Message</th><th>Statut</th></tr>";
        foreach ($requests as $req) {
            echo "<tr>";
            echo "<td>" . $req['id'] . "</td>";
            echo "<td>" . $req['created_at'] . "</td>";
            echo "<td>" . $req['request_type'] . "</td>";
            echo "<td>" . $req['first_name'] . ' ' . $req['last_name'] . "</td>";
            echo "<td>" . $req['phone'] . "</td>";
            echo "<td>" . $req['reference'] . ' - ' . substr($req['title'], 0, 30) . "</td>";
            echo "<td>" . substr($req['message'], 0, 50) . "</td>";
            echo "<td>" . $req['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>
</body>
</html>
