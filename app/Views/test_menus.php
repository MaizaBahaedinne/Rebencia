<!DOCTYPE html>
<html>
<head>
    <title>Test Routes Menus</title>
</head>
<body>
    <h1>Test des Routes Menus</h1>
    <ul>
        <li><a href="<?= base_url('admin/menus') ?>">Liste des menus</a></li>
        <li><a href="<?= base_url('admin/menus/create') ?>">Créer un menu</a></li>
        <li><a href="<?= base_url('admin/menus/role-menus') ?>">Gestion par rôle</a></li>
    </ul>
    
    <h2>Routes définies :</h2>
    <pre><?php
    $routes = service('routes');
    print_r($routes->getRoutes());
    ?></pre>
</body>
</html>
