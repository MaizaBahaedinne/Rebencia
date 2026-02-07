<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PropertiesSeeder extends Seeder
{
    public function run()
    {
        $propertyModel = model('PropertyModel');
        $zoneModel = model('ZoneModel');
        $userModel = model('UserModel');
        
        // Récupérer les zones
        $zones = $zoneModel->findAll();
        if (empty($zones)) {
            echo "Aucune zone trouvée.\n";
            return;
        }
        
        // Récupérer les agents
        $agents = $userModel->where('role_id', 6)->where('status', 'active')->findAll();
        if (empty($agents)) {
            echo "Aucun agent trouvé.\n";
            return;
        }
        
        $types = ['apartment', 'villa', 'house', 'land', 'commercial', 'office'];
        $transactionTypes = ['sale', 'rent', 'both'];
        $statuses = ['available', 'reserved', 'sold', 'rented'];
        $orientations = ['N', 'S', 'E', 'O', 'NE', 'NO', 'SE', 'SO'];
        $floorTypes = ['carrelage', 'marbre', 'parquet', 'beton_cire', 'mixte'];
        $gasTypes = ['ville', 'bouteille', 'aucun'];
        $standings = ['economic', 'standard', 'premium', 'luxury'];
        $conditions = ['new', 'excellent', 'good', 'to_renovate'];
        
        $cities = [
            'Tunis' => ['La Marsa', 'Carthage', 'Sidi Bou Said', 'El Menzah', 'Manar'],
            'Ariana' => ['Ariana Ville', 'Raoued', 'Soukra', 'Mnihla'],
            'Sousse' => ['Sousse Ville', 'Khezama', 'Sahloul', 'Hammam Sousse'],
            'Sfax' => ['Sfax Ville', 'Sakiet Ezzit', 'Sakiet Eddaier'],
            'Nabeul' => ['Nabeul Ville', 'Hammamet', 'Korba', 'Menzel Temime']
        ];
        
        $titresPrefixes = [
            'apartment' => ['Appartement', 'Studio', 'F2', 'F3', 'F4', 'Duplex'],
            'villa' => ['Villa', 'Villa de luxe', 'Villa moderne', 'Villa avec piscine'],
            'house' => ['Maison', 'Maison traditionnelle', 'Maison rénovée'],
            'land' => ['Terrain', 'Terrain constructible', 'Terrain agricole'],
            'commercial' => ['Local commercial', 'Boutique', 'Bureau commercial'],
            'office' => ['Bureau', 'Espace de bureau', 'Bureau moderne']
        ];
        
        $properties = [];
        
        for ($i = 1; $i <= 100; $i++) {
            $type = $types[array_rand($types)];
            $transactionType = $transactionTypes[array_rand($transactionTypes)];
            $zone = $zones[array_rand($zones)];
            $agent = $agents[array_rand($agents)];
            
            $cityKeys = array_keys($cities);
            $city = $cityKeys[array_rand($cityKeys)];
            $neighborhood = $cities[$city][array_rand($cities[$city])];
            
            $titrePrefix = $titresPrefixes[$type][array_rand($titresPrefixes[$type])];
            $titre = $titrePrefix . ' à ' . $neighborhood;
            
            // Prix selon le type et standing
            $standing = $standings[array_rand($standings)];
            $basePrice = match($type) {
                'apartment' => rand(80000, 350000),
                'villa' => rand(250000, 1200000),
                'house' => rand(150000, 600000),
                'land' => rand(50000, 500000),
                'commercial' => rand(100000, 800000),
                'office' => rand(80000, 400000)
            };
            
            $standingMultiplier = match($standing) {
                'economic' => 0.7,
                'standard' => 1.0,
                'premium' => 1.5,
                'luxury' => 2.5
            };
            
            $price = round($basePrice * $standingMultiplier, -3);
            $rentalPrice = $transactionType !== 'sale' ? round($price * 0.005, -1) : null;
            
            // Surfaces
            $areaTotal = match($type) {
                'apartment' => rand(45, 180),
                'villa' => rand(200, 600),
                'house' => rand(100, 300),
                'land' => rand(200, 2000),
                'commercial' => rand(30, 300),
                'office' => rand(25, 150)
            };
            
            $areaLiving = $type !== 'land' ? round($areaTotal * 0.8) : null;
            $areaLand = in_array($type, ['villa', 'house', 'land']) ? rand($areaTotal, $areaTotal * 3) : null;
            
            // Pièces
            $rooms = $type !== 'land' ? rand(1, 6) : 0;
            $bedrooms = $type !== 'land' && $type !== 'commercial' && $type !== 'office' ? rand(1, min(5, $rooms)) : 0;
            $bathrooms = $type !== 'land' ? rand(1, min(3, ceil($rooms / 2))) : 0;
            
            $properties[] = [
                'reference' => 'PROP-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'type' => $type,
                'transaction_type' => $transactionType,
                'title_fr' => $titre,
                'title_ar' => null,
                'title_en' => null,
                'description_fr' => "Magnifique {$titrePrefix} situé dans un quartier calme et résidentiel de {$neighborhood}. "
                    . "Proche de toutes commodités (transports, écoles, commerces). "
                    . ($type !== 'land' ? "Composé de {$rooms} pièces dont {$bedrooms} chambres. " : "")
                    . "Idéal pour " . ($transactionType === 'rent' ? 'location' : 'investissement') . ".",
                'description_ar' => null,
                'description_en' => null,
                'status' => $statuses[array_rand($statuses)],
                'featured' => rand(0, 100) > 80 ? 1 : 0,
                'disponibilite_date' => date('Y-m-d', strtotime('+' . rand(0, 90) . ' days')),
                
                // Location
                'zone_id' => $zone['id'],
                'governorate' => $city,
                'city' => $city,
                'neighborhood' => $neighborhood,
                'postal_code' => rand(1000, 9999),
                'address' => rand(1, 200) . ' ' . ['Avenue', 'Rue', 'Boulevard'][array_rand(['Avenue', 'Rue', 'Boulevard'])] . ' ' . ['Habib Bourguiba', 'de la Liberté', 'de la République'][array_rand(['Habib Bourguiba', 'de la Liberté', 'de la République'])],
                'hide_address' => rand(0, 100) > 70 ? 1 : 0,
                'latitude' => 36.8 + (rand(-100, 100) / 100),
                'longitude' => 10.18 + (rand(-100, 100) / 100),
                
                // Features
                'area_total' => $areaTotal,
                'area_living' => $areaLiving,
                'area_land' => $areaLand,
                'rooms' => $rooms,
                'bedrooms' => $bedrooms,
                'bathrooms' => $bathrooms,
                'parking_spaces' => rand(0, 3),
                'floor' => $type === 'apartment' ? rand(1, 8) : null,
                'total_floors' => $type === 'apartment' ? rand(4, 12) : null,
                'construction_year' => rand(1990, 2025),
                'orientation' => $orientations[array_rand($orientations)],
                'floor_type' => $type !== 'land' ? $floorTypes[array_rand($floorTypes)] : null,
                'gas_type' => $gasTypes[array_rand($gasTypes)],
                'standing' => $standing,
                'condition_state' => $conditions[array_rand($conditions)],
                'legal_status' => rand(0, 100) > 90 ? 'pending' : 'clear',
                
                // Energy
                'energy_class' => ['A', 'B', 'C', 'D', 'E'][array_rand(['A', 'B', 'C', 'D', 'E'])],
                'energy_consumption_kwh' => rand(80, 300),
                'co2_emission' => rand(10, 80),
                
                // Equipment
                'has_elevator' => $type === 'apartment' && rand(0, 100) > 50 ? 1 : 0,
                'has_parking' => rand(0, 100) > 40 ? 1 : 0,
                'has_garden' => in_array($type, ['villa', 'house']) && rand(0, 100) > 50 ? 1 : 0,
                'has_pool' => $type === 'villa' && $standing === 'luxury' && rand(0, 100) > 70 ? 1 : 0,
                
                // Pricing
                'price' => $price,
                'rental_price' => $rentalPrice,
                'promo_price' => rand(0, 100) > 85 ? round($price * 0.9, -3) : null,
                'promo_start_date' => null,
                'promo_end_date' => null,
                'charge_syndic' => $type === 'apartment' ? rand(30, 150) : null,
                'charge_water' => rand(20, 80),
                'charge_gas' => $gasTypes[array_rand($gasTypes)] !== 'aucun' ? rand(30, 100) : null,
                'charge_electricity' => rand(40, 150),
                'charge_other' => rand(0, 100) > 80 ? rand(20, 100) : null,
                
                // Meta
                'agent_id' => $agent['id'],
                'agency_id' => null,
                'created_by' => $agent['id'],
                'internal_notes' => 'Bien créé automatiquement pour test',
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 180) . ' days')),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        // Insérer par batch de 20
        $chunks = array_chunk($properties, 20);
        foreach ($chunks as $chunk) {
            $this->db->table('properties')->insertBatch($chunk);
        }
        
        echo "✓ 100 biens immobiliers créés avec succès.\n";
        echo "  Répartition: \n";
        $typeCount = array_count_values(array_column($properties, 'type'));
        foreach ($typeCount as $type => $count) {
            echo "    - {$type}: {$count} biens\n";
        }
    }
}
