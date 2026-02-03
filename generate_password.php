<?php
// Génération du hash pour le mot de passe Admin@2026
$password = 'Admin@2026';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Mot de passe: $password\n";
echo "Hash: $hash\n";

// Vérification
if (password_verify($password, $hash)) {
    echo "✓ Hash vérifié avec succès!\n";
} else {
    echo "✗ Erreur de vérification\n";
}
?>
