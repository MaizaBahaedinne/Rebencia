<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AgentsSeeder extends Seeder
{
    public function run()
    {
        $userModel = model('UserModel');
        $roleModel = model('RoleModel');
        
        // Récupérer les managers existants
        $managers = $userModel->join('roles', 'roles.id = users.role_id')
            ->whereIn('roles.level', [85, 92, 95]) // Managers et PDG
            ->where('users.status', 'active')
            ->select('users.id, users.first_name, users.last_name')
            ->findAll();
        
        if (empty($managers)) {
            echo "Aucun manager trouvé. Création impossible.\n";
            return;
        }
        
        // Récupérer le rôle Agent (level 50)
        $agentRole = $roleModel->where('level', 50)->first();
        if (!$agentRole) {
            echo "Rôle Agent non trouvé.\n";
            return;
        }
        
        $prenoms = ['Ahmed', 'Mohamed', 'Fatma', 'Sarra', 'Mariem', 'Youssef', 'Amine', 'Leila', 'Nour', 'Salah'];
        $noms = ['Ben Ali', 'Trabelsi', 'Gharbi', 'Hamdi', 'Jebari', 'Messaoudi', 'Khelifi', 'Bouazizi', 'Chebbi', 'Mahmoudi'];
        $governorates = ['Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Sousse', 'Sfax', 'Monastir'];
        
        $agents = [];
        for ($i = 1; $i <= 10; $i++) {
            $prenom = $prenoms[array_rand($prenoms)];
            $nom = $noms[array_rand($noms)];
            $manager = $managers[array_rand($managers)];
            $governorate = $governorates[array_rand($governorates)];
            
            $agents[] = [
                'first_name' => $prenom,
                'last_name' => $nom,
                'email' => strtolower($prenom . '.' . $nom . $i . '@rebencia.com'),
                'password' => password_hash('agent123', PASSWORD_DEFAULT),
                'phone' => '+ 216 ' . rand(20, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'role_id' => $agentRole['id'],
                'manager_id' => $manager['id'],
                'status' => 'active',
                'address' => rand(1, 200) . ' Avenue Habib Bourguiba, ' . $governorate,
                'date_joined' => date('Y-m-d', strtotime('-' . rand(30, 730) . ' days')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        $this->db->table('users')->insertBatch($agents);
        
        echo "✓ 10 agents créés avec succès et affectés aux managers.\n";
        
        // Afficher les informations
        foreach ($agents as $agent) {
            echo "  - {$agent['first_name']} {$agent['last_name']} ({$agent['email']}) - Mot de passe: agent123\n";
        }
    }
}
