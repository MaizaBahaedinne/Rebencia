<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TransactionTestDataSeeder extends Seeder
{
    public function run()
    {
        // Générer des clients (acheteurs et vendeurs)
        $clients = [
            [
                'type' => 'buyer',
                'first_name' => 'Ahmed',
                'last_name' => 'Ben Salem',
                'email' => 'ahmed.bensalem@example.com',
                'phone' => '+216 98 123 456',
                'status' => 'active',
                'source' => 'website',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'buyer',
                'first_name' => 'Fatma',
                'last_name' => 'Gharbi',
                'email' => 'fatma.gharbi@example.com',
                'phone' => '+216 97 234 567',
                'status' => 'active',
                'source' => 'referral',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'tenant',
                'first_name' => 'Mohamed',
                'last_name' => 'Trabelsi',
                'email' => 'mohamed.trabelsi@example.com',
                'phone' => '+216 96 345 678',
                'status' => 'active',
                'source' => 'social_media',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'seller',
                'first_name' => 'Leila',
                'last_name' => 'Khemiri',
                'email' => 'leila.khemiri@example.com',
                'phone' => '+216 95 456 789',
                'status' => 'active',
                'source' => 'walk_in',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'seller',
                'first_name' => 'Karim',
                'last_name' => 'Bouazizi',
                'email' => 'karim.bouazizi@example.com',
                'phone' => '+216 94 567 890',
                'status' => 'active',
                'source' => 'website',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'landlord',
                'first_name' => 'Sonia',
                'last_name' => 'Mejri',
                'email' => 'sonia.mejri@example.com',
                'phone' => '+216 93 678 901',
                'status' => 'active',
                'source' => 'phone',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($clients as $client) {
            $this->db->table('clients')->insert($client);
        }

        // Générer des utilisateurs (agents)
        $users = [
            [
                'username' => 'agent1',
                'first_name' => 'Youssef',
                'last_name' => 'Mansour',
                'email' => 'youssef.mansour@rebencia.com',
                'phone' => '+216 92 111 222',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role_id' => 6, // Agent
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'agent2',
                'first_name' => 'Amira',
                'last_name' => 'Belgacem',
                'email' => 'amira.belgacem@rebencia.com',
                'phone' => '+216 92 222 333',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role_id' => 6, // Agent
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'coordinator1',
                'first_name' => 'Hichem',
                'last_name' => 'Jebali',
                'email' => 'hichem.jebali@rebencia.com',
                'phone' => '+216 92 333 444',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role_id' => 7, // Coordinateur
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($users as $user) {
            $this->db->table('users')->insert($user);
        }

        // Générer des agences
        $agencies = [
            [
                'code' => 'REB-TUNIS',
                'name' => 'Rebencia Tunis Centre',
                'type' => 'siege',
                'city' => 'Tunis',
                'governorate' => 'Tunis',
                'address' => 'Avenue Habib Bourguiba, Tunis',
                'phone' => '+216 71 123 456',
                'email' => 'tunis@rebencia.com',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'REB-SFAX',
                'name' => 'Rebencia Sfax',
                'type' => 'branch',
                'city' => 'Sfax',
                'governorate' => 'Sfax',
                'address' => 'Avenue Majida Boulila, Sfax',
                'phone' => '+216 74 234 567',
                'email' => 'sfax@rebencia.com',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'REB-SOUSSE',
                'name' => 'Rebencia Sousse',
                'type' => 'branch',
                'city' => 'Sousse',
                'governorate' => 'Sousse',
                'address' => 'Boulevard Yahia Ibn Omar, Sousse',
                'phone' => '+216 73 345 678',
                'email' => 'sousse@rebencia.com',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($agencies as $agency) {
            $this->db->table('agencies')->insert($agency);
        }

        // Générer des propriétés
        $properties = [
            [
                'reference' => 'PROP-2026-001',
                'title' => 'Appartement S+2 Centre Ville',
                'type' => 'apartment',
                'transaction_type' => 'sale',
                'price' => 250000,
                'rental_price' => null,
                'area' => 95,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'address' => 'Avenue de la Liberté, Tunis',
                'city' => 'Tunis',
                'governorate' => 'Tunis',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'reference' => 'PROP-2026-002',
                'title' => 'Villa S+4 avec Jardin',
                'type' => 'villa',
                'transaction_type' => 'sale',
                'price' => 850000,
                'rental_price' => null,
                'area' => 280,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'address' => 'Les Berges du Lac, Tunis',
                'city' => 'Tunis',
                'governorate' => 'Tunis',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'reference' => 'PROP-2026-003',
                'title' => 'Appartement S+3 Meublé',
                'type' => 'apartment',
                'transaction_type' => 'rent',
                'price' => null,
                'rental_price' => 1200,
                'area' => 120,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'address' => 'Lac 2, Tunis',
                'city' => 'Tunis',
                'governorate' => 'Tunis',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'reference' => 'PROP-2026-004',
                'title' => 'Bureau 150m² Centre Ville',
                'type' => 'office',
                'transaction_type' => 'rent',
                'price' => null,
                'rental_price' => 2500,
                'area' => 150,
                'bedrooms' => 0,
                'bathrooms' => 1,
                'address' => 'Avenue Mohamed V, Tunis',
                'city' => 'Tunis',
                'governorate' => 'Tunis',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'reference' => 'PROP-2026-005',
                'title' => 'Terrain Constructible 500m²',
                'type' => 'land',
                'transaction_type' => 'sale',
                'price' => 180000,
                'rental_price' => null,
                'area' => 500,
                'bedrooms' => 0,
                'bathrooms' => 0,
                'address' => 'Zone Industrielle, Ariana',
                'city' => 'Ariana',
                'governorate' => 'Ariana',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($properties as $property) {
            $this->db->table('properties')->insert($property);
        }

        echo "✅ Données de test générées avec succès !\n";
        echo "   - 6 clients (acheteurs, vendeurs, locataires, bailleurs)\n";
        echo "   - 3 utilisateurs (agents et coordinateur)\n";
        echo "   - 3 agences\n";
        echo "   - 5 propriétés\n";
    }
}
