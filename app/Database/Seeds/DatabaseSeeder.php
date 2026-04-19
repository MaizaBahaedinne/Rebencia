<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "\n";
        echo "========================================\n";
        echo " REBENCIA - Génération des données test\n";
        echo "========================================\n\n";
        
        // 1. Rôles & permissions
        echo "1. Création des rôles et permissions...\n";
        $this->call('RolesSeeder');
        echo "\n";

        // 2. Créer les agents
        echo "2. Création des agents...\n";
        $this->call('AgentsSeeder');
        echo "\n";
        
        // 3. Créer les biens
        echo "3. Création des biens immobiliers...\n";
        $this->call('PropertiesSeeder');
        echo "\n";
        
        echo "========================================\n";
        echo " Génération terminée avec succès !\n";
        echo "========================================\n\n";
        
        echo "Informations de connexion:\n";
        echo "- Admin: admin@rebencia.com / admin (mot de passe par défaut)\n";
        echo "- Agents: [prenom].[nom][1-10]@rebencia.com / agent123\n";
        echo "\n";
        echo "Note: En tant qu'admin, vous pouvez cliquer sur l'icône verte 'Login As' dans la liste des utilisateurs\n";
        echo "      pour vous connecter en tant que n'importe quel utilisateur.\n\n";
    }
}
