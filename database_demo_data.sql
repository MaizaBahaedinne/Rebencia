-- ============================================================
-- REBENCIA — Données de démonstration réalistes
-- Tunisie — Villes réelles, prix marché 2024/2025
-- Importer APRÈS le schéma principal et les migrations
--
-- Contenu :
--   • 6 agents (users)
--   • 20 biens immobiliers (properties)
--   • 24 clients (clients)
--   • 18 leads (leads)
--   • 16 visites (visits)
--   • Notes leads & historique statuts
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ============================================================
-- 1. UTILISATEURS – Agents de l'agence
-- ============================================================
-- password_hash = bcrypt cost 12 de : Agent@2024
-- $2y$12$K8c.nVwX7iGLR3oHYJDz4.mbQgXF9.z2Y6Uw/KAEi3BYIHkv6gd4K

INSERT INTO `users`
  (`role_id`, `first_name`, `last_name`, `email`, `phone`, `password_hash`, `status`, `created_at`, `updated_at`)
VALUES
-- Directeur (role_id=3)
(3, 'Karim',   'Ben Salah',   'k.bensalah@rebencia.com',  '+216 71 234 567', '$2y$12$It1sv68ypRE6p5pPKtIqhO1hyd0U7.ygSyX8CjlqAiyMmLy/8zXTe', 'active', '2025-01-10 08:00:00', '2025-01-10 08:00:00'),
-- Experts (role_id=4)
(4, 'Sana',    'Trabelsi',    's.trabelsi@rebencia.com',  '+216 22 345 678', '$2y$12$It1sv68ypRE6p5pPKtIqhO1hyd0U7.ygSyX8CjlqAiyMmLy/8zXTe', 'active', '2025-01-15 09:00:00', '2025-01-15 09:00:00'),
(4, 'Nizar',   'Hadj Khalil', 'n.hadjkhalil@rebencia.com','+216 55 456 789', '$2y$12$It1sv68ypRE6p5pPKtIqhO1hyd0U7.ygSyX8CjlqAiyMmLy/8zXTe', 'active', '2025-02-01 09:00:00', '2025-02-01 09:00:00'),
-- Coordinateur (role_id=5)
(5, 'Yasmine', 'Ayari',       'y.ayari@rebencia.com',     '+216 98 567 890', '$2y$12$It1sv68ypRE6p5pPKtIqhO1hyd0U7.ygSyX8CjlqAiyMmLy/8zXTe', 'active', '2025-02-10 08:30:00', '2025-02-10 08:30:00'),
-- Collaborateurs (role_id=6)
(6, 'Amine',   'Chaabane',    'a.chaabane@rebencia.com',  '+216 27 678 901', '$2y$12$It1sv68ypRE6p5pPKtIqhO1hyd0U7.ygSyX8CjlqAiyMmLy/8zXTe', 'active', '2025-03-01 09:00:00', '2025-03-01 09:00:00'),
(6, 'Rim',     'Meddeb',      'r.meddeb@rebencia.com',    '+216 50 789 012', '$2y$12$It1sv68ypRE6p5pPKtIqhO1hyd0U7.ygSyX8CjlqAiyMmLy/8zXTe', 'active', '2025-03-15 09:00:00', '2025-03-15 09:00:00');

-- Récupérer les IDs agents (on utilise les IDs 2–7, admin=1)
-- agent_ids : Karim=2, Sana=3, Nizar=4, Yasmine=5, Amine=6, Rim=7

-- ============================================================
-- 2. BIENS IMMOBILIERS – 20 annonces tunisiennes
-- ============================================================
INSERT INTO `properties`
  (`reference`, `agent_id`, `title`, `description`, `type`, `transaction_type`, `status`,
   `price`, `surface`, `rooms`, `bedrooms`, `bathrooms`, `floor`, `total_floors`,
   `parking`, `furnished`, `address`, `city`, `zone`,
   `latitude`, `longitude`, `features`,
   `is_published`, `published_at`, `published_by`, `featured`,
   `views_count`, `created_at`, `updated_at`)
VALUES

-- ── Tunis – La Marsa ──────────────────────────────────────────────────────────
('RB-2025-0001', 3,
 'Villa prestige avec piscine – La Marsa',
 'Magnifique villa de standing sur 3 niveaux, vue mer partielle, jardin paysager 500m², piscine chauffée, 4 chambres avec salle de bain attenante, salon double, cuisine équipée haut de gamme, salle de sport. Quartier résidentiel calme à 5 min de la plage.',
 'villa', 'sale', 'available',
 1850000.00, 380.00, 8, 4, 5, 0, 3,
 1, 1, 'Route Touristique, Km 12', 'La Marsa', 'Borj Cédria',
 36.8897, 10.3218,
 '{"piscine":true,"jardin":true,"gardien":true,"alarme":true,"climatisation":true,"ascenseur":false}',
 1, '2025-03-01 10:00:00', 2, 1,
 347, '2025-02-15 09:00:00', '2025-04-01 14:30:00'),

('RB-2025-0002', 4,
 'Appartement S+3 haut standing – Les Berges du Lac 2',
 'Superbe appartement au 4ème étage avec vue lac. Double salon, cuisine ouverte équipée, 3 chambres avec dressing, 2 salles de bain, terrasse 30m². Immeuble sécurisé avec gardiennage 24h/24, ascenseur, parking sous-sol.',
 'apartment', 'sale', 'available',
 720000.00, 175.00, 5, 3, 2, 4, 8,
 1, 1, 'Rue du Lac Michigan', 'Tunis', 'Lac 2',
 36.8447, 10.2406,
 '{"ascenseur":true,"gardien":true,"alarme":true,"climatisation":true,"piscine":false,"cave":true}',
 1, '2025-02-20 11:00:00', 2, 1,
 289, '2025-02-10 10:00:00', '2025-03-28 09:15:00'),

('RB-2025-0003', 3,
 'Appartement S+2 – Cité Ennasr',
 'Appartement propre et lumineux au 2ème étage, salon, 2 chambres, salle de bain, cuisine, balcon. Résidence avec gardien. Idéal pour famille ou investissement locatif. Proche commodités, écoles et transports.',
 'apartment', 'sale', 'sold',
 310000.00, 105.00, 4, 2, 1, 2, 5,
 1, 0, 'Avenue de la Liberté, Ennasr 2', 'Tunis', 'Ennasr',
 36.8612, 10.1957,
 '{"ascenseur":true,"gardien":true,"climatisation":false,"parking":false}',
 1, '2025-01-10 09:00:00', 2, 0,
 521, '2025-01-05 10:00:00', '2025-03-15 16:00:00'),

-- ── Tunis – Cité El Khadra / Menzah ──────────────────────────────────────────
('RB-2025-0004', 6,
 'Bureau commercial S+2 – Cité El Khadra',
 'Bureau aménagé au rez-de-chaussée d\'un immeuble récent. Espace open space 80m², 2 bureaux fermés, salle de réunion, kitchenette, 2 WC, parking. Idéal pour cabinet médical, juridique ou bureaux commerciaux.',
 'office', 'rent', 'available',
 3800.00, 130.00, 5, 0, 2, 0, 4,
 1, 0, 'Rue Alain Savary', 'Tunis', 'El Khadra',
 36.8331, 10.2012,
 '{"climatisation":true,"alarme":true,"parking":true,"ascenseur":true}',
 1, '2025-03-15 10:00:00', 3, 0,
 98, '2025-03-10 09:00:00', '2025-04-05 10:00:00'),

('RB-2025-0005', 7,
 'Appartement S+3 – El Menzah 6',
 'Bel appartement familial dans une résidence sécurisée. Salon spacieux, cuisine séparée, 3 grandes chambres, 2 salles de bain, débarras. Quartier calme, proche lycée Menzah, centre commercial et transports.',
 'apartment', 'sale', 'reserved',
 480000.00, 145.00, 5, 3, 2, 1, 6,
 1, 0, 'Résidence Les Oliviers, Menzah 6', 'Tunis', 'Menzah',
 36.8701, 10.1789,
 '{"gardien":true,"ascenseur":true,"cave":true,"parking":true,"climatisation":false}',
 1, '2025-02-25 10:00:00', 3, 0,
 203, '2025-02-20 09:00:00', '2025-04-08 11:00:00'),

-- ── Hammamet / Nabeul ─────────────────────────────────────────────────────────
('RB-2025-0006', 3,
 'Villa balnéaire S+4 avec vue mer – Hammamet Nord',
 'Propriété exceptionnelle en front de mer. Villa R+1, 5 chambres, 4 salles de bain, double salon, cuisine américaine, grande terrasse, jardin 700m², piscine à débordement. Accès direct plage privée.',
 'villa', 'sale', 'available',
 2400000.00, 420.00, 9, 5, 4, 0, 2,
 1, 1, 'Route de la Côte, Zone Touristique', 'Hammamet', 'Hammamet Nord',
 36.4000, 10.6167,
 '{"piscine":true,"jardin":true,"plage":true,"gardien":true,"alarme":true,"climatisation":true}',
 1, '2025-01-20 10:00:00', 2, 1,
 612, '2025-01-15 09:00:00', '2025-04-10 09:00:00'),

('RB-2025-0007', 4,
 'Terrain constructible 600m² – Nabeul',
 'Terrain plat, viabilisé (eau, électricité, téléphone), dans un lotissement approuvé. Tous commerces et services à 10 min. Idéal pour construction villa ou immeuble R+2. Titre foncier net.',
 'land', 'sale', 'available',
 185000.00, 600.00, 0, 0, 0, 0, 0,
 0, 0, 'Lotissement El Yasmine', 'Nabeul', 'Route de Hammamet',
 36.4517, 10.7355,
 '{"titre_foncier":true,"viabilise":true}',
 1, '2025-03-05 10:00:00', 3, 0,
 156, '2025-03-01 09:00:00', '2025-04-02 14:00:00'),

-- ── Sfax ──────────────────────────────────────────────────────────────────────
('RB-2025-0008', 6,
 'Appartement S+2 neuf – Sfax El Ain',
 'Appartement neuf dans une résidence en livraison. Finitions haut de gamme, double vitrage, isolation phonique et thermique, cuisine équipée, climatisation réversible. Garantie constructeur 10 ans.',
 'apartment', 'sale', 'available',
 265000.00, 110.00, 4, 2, 1, 3, 5,
 1, 1, 'Résidence Panorama, Route El Ain', 'Sfax', 'El Ain',
 34.7406, 10.7603,
 '{"neuf":true,"ascenseur":true,"parking":true,"climatisation":true}',
 1, '2025-04-01 10:00:00', 3, 0,
 87, '2025-03-28 09:00:00', '2025-04-10 11:00:00'),

('RB-2025-0009', 7,
 'Local commercial 250m² – Centre Sfax',
 'Local commercial en rez-de-chaussée d\'une artère commerçante animée. Grande vitrine, stockage arrière, bureaux à l\'étage. Clientèle importante, fort passage. Convient supermarché, pharmacie, showroom.',
 'commercial', 'rent', 'available',
 8500.00, 250.00, 0, 0, 2, 0, 1,
 0, 0, 'Avenue Habib Bourguiba', 'Sfax', 'Centre Ville',
 34.7373, 10.7609,
 '{"vitrine":true,"stockage":true,"climatisation":true}',
 1, '2025-03-20 10:00:00', 3, 0,
 134, '2025-03-18 09:00:00', '2025-04-05 10:00:00'),

-- ── Sousse ────────────────────────────────────────────────────────────────────
('RB-2025-0010', 4,
 'Studio meublé en location courte durée – Sousse',
 'Studio entièrement meublé et équipé, idéal location saisonnière ou professionnel. Kitchenette américaine, salle de bain moderne, lit double, climatisation, WiFi. Résidence avec piscine et parking.',
 'apartment', 'rent', 'available',
 900.00, 45.00, 1, 1, 1, 2, 4,
 0, 1, 'Résidence Médina Beach', 'Sousse', 'Kantaoui',
 35.9169, 10.6017,
 '{"meuble":true,"piscine":true,"wifi":true,"climatisation":true,"parking":true}',
 1, '2025-03-10 10:00:00', 3, 0,
 423, '2025-03-08 09:00:00', '2025-04-12 08:00:00'),

('RB-2025-0011', 3,
 'Villa double S+4 – Sahloul Sousse',
 'Grande villa familiale dans quartier résidentiel prisé. Double salon avec cheminée, 4 chambres, 3 salles de bain, cuisine équipée, buanderie, jardin 350m², piscine, 2 parkings couverts.',
 'villa', 'sale', 'available',
 980000.00, 280.00, 7, 4, 3, 0, 2,
 1, 0, 'Résidence Les Pins, Rue 14', 'Sousse', 'Sahloul',
 35.8498, 10.5951,
 '{"piscine":true,"jardin":true,"parking":true,"gardien":false,"cheminee":true}',
 1, '2025-02-14 10:00:00', 2, 0,
 267, '2025-02-10 09:00:00', '2025-04-01 10:00:00'),

-- ── Monastir ──────────────────────────────────────────────────────────────────
('RB-2025-0012', 7,
 'Appartement S+2 vue mer – Monastir',
 'Appartement au dernier étage avec vue panoramique sur mer. Salon lumineux, 2 chambres, salle de bain, cuisine indépendante, grande terrasse 40m². Résidence calme, à 5 min des plages et du port de plaisance.',
 'apartment', 'sale', 'available',
 390000.00, 120.00, 4, 2, 1, 5, 5,
 0, 0, 'Résidence Corniche, Bd de l\'Environnement', 'Monastir', 'Corniche',
 35.7780, 10.8265,
 '{"vue_mer":true,"terrasse":true,"ascenseur":true,"gardien":true}',
 1, '2025-03-22 10:00:00', 3, 0,
 178, '2025-03-20 09:00:00', '2025-04-09 11:00:00'),

-- ── Megrine / Ben Arous ───────────────────────────────────────────────────────
('RB-2025-0013', 6,
 'Appartement S+1 – Mégrine Coteaux',
 'Appartement fonctionnel au 1er étage, idéal primo-accédant ou jeune couple. Séjour, chambre, salle de bain, cuisine, cage d\'escalier entretenue. Proche métro Léger et zone industrielle.',
 'apartment', 'sale', 'available',
 195000.00, 72.00, 2, 1, 1, 1, 3,
 0, 0, 'Rue Ibn Sina, Mégrine', 'Mégrine', 'Coteaux',
 36.7952, 10.1911,
 '{"gardien":false,"ascenseur":false}',
 1, '2025-04-02 10:00:00', 3, 0,
 64, '2025-04-01 09:00:00', '2025-04-10 09:00:00'),

-- ── Ariana ────────────────────────────────────────────────────────────────────
('RB-2025-0014', 4,
 'Duplex S+3 – Ariana Ville',
 'Beau duplex dans résidence moderne. Rez : salon double, cuisine américaine, WC invités. Étage : 3 chambres, 2 salles de bain, espace bureau. Grande terrasse accessible. Parking 2 véhicules.',
 'apartment', 'sale', 'available',
 560000.00, 180.00, 7, 3, 2, 0, 1,
 1, 0, 'Résidence Jardins d\'Ariana, Avenue Taïeb Mhiri', 'Ariana', 'Ariana Soghra',
 36.8665, 10.1948,
 '{"duplex":true,"terrasse":true,"parking":true,"gardien":true,"ascenseur":false}',
 1, '2025-03-12 10:00:00', 2, 1,
 215, '2025-03-10 09:00:00', '2025-04-07 14:00:00'),

-- ── Bizerte ───────────────────────────────────────────────────────────────────
('RB-2025-0015', 7,
 'Maison de maître avec jardin – Bizerte Corniche',
 'Belle maison ancienne entièrement rénovée sur 3 niveaux. Cachet et authenticité préservés, hauteur sous plafond 3m. Jardin arboré 400m², terrasse toiture avec vue sur lac. 5 chambres, 3 salles de bain.',
 'house', 'sale', 'available',
 650000.00, 230.00, 8, 5, 3, 0, 3,
 1, 0, 'Rue de la Corniche', 'Bizerte', 'Corniche',
 37.2746, 9.8739,
 '{"jardin":true,"terrasse_toit":true,"renove":true,"piscine":false}',
 1, '2025-02-28 10:00:00', 2, 0,
 143, '2025-02-25 09:00:00', '2025-04-03 10:00:00'),

-- ── Djerba ────────────────────────────────────────────────────────────────────
('RB-2025-0016', 3,
 'Houch typique rénové – Djerba Houmt Souk',
 'Authentique houch djerbien rénové avec goût. Patio intérieur avec fontaine, 3 chambres voûtées, salon à la tunisienne, cuisine équipée moderne, piscine privée. Idéal maison de vacances ou gîte touristique.',
 'house', 'sale', 'available',
 420000.00, 160.00, 5, 3, 2, 0, 1,
 1, 1, 'Rue Farhat Hached, Houmt Souk', 'Djerba', 'Houmt Souk',
 33.8745, 10.8586,
 '{"patio":true,"piscine":true,"traditionnel":true,"renove":true}',
 1, '2025-01-25 10:00:00', 2, 1,
 489, '2025-01-20 09:00:00', '2025-04-05 09:00:00'),

-- ── Biens supplémentaires ────────────────────────────────────────────────────
('RB-2025-0017', 6,
 'Appartement S+2 location – Lac 1 Tunis',
 'Appartement meublé en location mensuelle. Salon, 2 chambres, cuisine équipée, clim. Immeuble sécurisé, ascenseur, parking. Quartier d\'affaires et diplomatique, proche ambassades et hôtels.',
 'apartment', 'rent', 'rented',
 2200.00, 95.00, 4, 2, 1, 3, 7,
 1, 1, 'Rue du Lac Victoria', 'Tunis', 'Lac 1',
 36.8401, 10.2318,
 '{"meuble":true,"ascenseur":true,"parking":true,"gardien":true,"climatisation":true}',
 1, '2025-01-18 10:00:00', 3, 0,
 312, '2025-01-15 09:00:00', '2025-03-01 10:00:00'),

('RB-2025-0018', 4,
 'Terrain agricole 2 hectares – Grombalia',
 'Terrain agricole avec oliviers centenaires et puits. Câble électrique en limite. Accès piste goudronnée. Idéal pour résidence secondaire rurale ou exploitation agricole. Titre foncier individuel.',
 'land', 'sale', 'available',
 95000.00, 20000.00, 0, 0, 0, 0, 0,
 0, 0, 'Route Régionale 27, Grombalia', 'Grombalia', 'Zone Agricole',
 36.6010, 10.5012,
 '{"olivier":true,"puits":true,"titre_foncier":true,"agricole":true}',
 1, '2025-03-25 10:00:00', 3, 0,
 67, '2025-03-22 09:00:00', '2025-04-10 09:00:00'),

('RB-2025-0019', 7,
 'Surface commerciale 400m² – Zone Industrielle Tunis Sud',
 'Grand local commercial/industriel avec quai de chargement, 3 phases, bureau intégré 40m², hauteur 6m sous faîtage, 2 portails. Idéal entrepôt, atelier, showroom automobile.',
 'commercial', 'rent', 'available',
 12000.00, 400.00, 0, 0, 1, 0, 1,
 0, 0, 'Route de Bir El Kassaa, Zone Industrielle', 'Ben Arous', 'Zone Industrielle',
 36.7510, 10.2289,
 '{"quai_chargement":true,"triphasé":true,"hauteur_6m":true,"bureau":true}',
 1, '2025-04-05 10:00:00', 3, 0,
 43, '2025-04-03 09:00:00', '2025-04-10 09:00:00'),

('RB-2025-0020', 3,
 'Penthouse S+4 avec terrasse 200m² – Les Berges du Lac 1',
 'Penthouse d\'exception au dernier étage (12ème). Vue 360° sur le lac, mer et ville. Double terrasse aménagée, jacuzzi extérieur, 4 chambres suites, salon de 80m², salle de cinéma, home automation. Prestige absolu.',
 'apartment', 'sale', 'available',
 2100000.00, 350.00, 8, 4, 4, 12, 12,
 1, 1, 'Tour Lac I, Avenue Taieb Mehiri', 'Tunis', 'Lac 1',
 36.8365, 10.2266,
 '{"jacuzzi":true,"terrasse_200m":true,"home_automation":true,"cinema":true,"vue_360":true,"ascenseur_prive":true}',
 1, '2025-02-05 10:00:00', 2, 1,
 834, '2025-02-01 09:00:00', '2025-04-12 08:30:00');

-- ============================================================
-- 3. CLIENTS – 24 profils réalistes
-- ============================================================
-- zone_pays_id / zone_region_id / zone_ville_id mis à NULL
-- (ces IDs dépendent des données de la table zones qui varient)
INSERT INTO `clients`
  (`client_type`, `first_name`, `last_name`, `phone`, `email`,
   `profession`, `company`,
   `address`, `zone_pays_id`, `zone_region_id`, `zone_ville_id`, `postal_code`,
   `property_type_id`, `budget_min`, `budget_max`, `desired_zone`,
   `owner_location`, `desired_price`,
   `status`, `assigned_to`, `source`, `notes`,
   `created_at`, `updated_at`)
VALUES

-- Acheteurs
('acheteur', 'Mohamed', 'Bouzid',       '+216 22 112 233', 'mbouzid@gmail.com',
 'Ingénieur', 'STEG', '45 Rue Ali Belhouane, Tunis', NULL, NULL, NULL, '1001',
 NULL, 450000.00, 650000.00, 'Menzah, Ennasr, Lac',
 NULL, NULL,
 'actif', 3, 'site_web', 'Cherche S+3 pour famille de 4. Préfère Menzah ou Ennasr. Disponible les week-ends.',
 '2025-02-01 10:00:00', '2025-04-05 09:00:00'),

('acheteur', 'Salma', 'Hamdi',          '+216 55 223 344', 'salma.hamdi@outlook.com',
 'Médecin', 'Clinique Taoufik', '12 Av. du Président Bourguiba, Ariana', NULL, NULL, NULL, '2080',
 NULL, 700000.00, 1200000.00, 'La Marsa, Sidi Bou Saïd, Gammarth',
 NULL, NULL,
 'actif', 4, 'referral', 'Cliente VIP référée par Me Ben Ali. Budget flexible. Cherche villa ou duplex prestige.',
 '2025-01-20 11:00:00', '2025-04-08 10:00:00'),

('acheteur', 'Walid', 'Mansouri',       '+216 98 334 455', 'walid.mansouri@yahoo.fr',
 'Commerçant', NULL, '78 Rue de Marseille, Sfax', NULL, NULL, NULL, '3000',
 NULL, 250000.00, 380000.00, 'Sfax Centre, El Ain, Chihia',
 NULL, NULL,
 'contacte', 4, 'appel', 'Cherche S+2 pour s\'installer avec sa femme. Préfère rez-de-chaussée ou 1er étage.',
 '2025-02-15 09:00:00', '2025-03-20 11:00:00'),

('acheteur', 'Ines', 'Cherif',          '+216 27 445 566', 'ines.cherif@gmail.com',
 'Enseignante', 'Université de Sousse', '34 Cité Erriadh, Sousse', NULL, NULL, NULL, '4000',
 NULL, 350000.00, 500000.00, 'Sahloul, Khezama, Hammam Sousse',
 NULL, NULL,
 'actif', 7, 'facebook', 'Primo-accédante. Dossier bancaire en cours. Intéressée par S+3 avec parking.',
 '2025-03-01 09:00:00', '2025-04-10 08:00:00'),

('acheteur', 'Tarek', 'Mejri',          '+216 20 556 677', 'tarek.mejri@hotmail.com',
 'Avocat', 'Cabinet Mejri & Associés', '15 Rue Alain Savary, Tunis', NULL, NULL, NULL, '1001',
 NULL, 900000.00, 1500000.00, 'Les Berges du Lac, Cité Jardins',
 NULL, NULL,
 'en_attente', 3, 'email', 'Client exigeant. A visité 3 biens sans suite. Cherche prestige + vue lac.',
 '2025-01-10 10:00:00', '2025-04-09 14:00:00'),

('acheteur', 'Yasmine', 'Bouaziz',      '+216 50 667 788', 'y.bouaziz@gmail.com',
 'Architecte', 'Atelier Bouaziz', '5 Impasse des Roses, La Marsa', NULL, NULL, NULL, '2078',
 NULL, 600000.00, 900000.00, 'La Marsa, Gammarth, Sidi Bou Saïd',
 NULL, NULL,
 'nouveau', 3, 'site_web', 'Cherche villa ou grande maison avec potentiel de rénovation/extension.',
 '2025-04-08 10:00:00', '2025-04-08 10:00:00'),

('acheteur', 'Bilel', 'Sassi',          '+216 25 778 899', 'bilel.sassi@gmail.com',
 'Chef d\'entreprise', 'Société TRAD', '22 Zone Industrielle, Ben Arous', NULL, NULL, NULL, '2013',
 NULL, 1500000.00, 2500000.00, 'Hammamet, Yasmine Hammamet',
 NULL, NULL,
 'actif', 3, 'referral', 'Investisseur. Cherche bien balnéaire à haut rendement locatif ou revente. Budget solide.',
 '2025-02-20 10:00:00', '2025-04-07 11:00:00'),

('acheteur', 'Amira', 'Triki',          '+216 52 889 900', 'amira.triki@live.com',
 'Pharmacienne', 'Pharmacie Central', '67 Avenue Habib Thamer, Tunis', NULL, NULL, NULL, '1002',
 NULL, 280000.00, 350000.00, 'Mégrine, Ben Arous, Hammam Lif',
 NULL, NULL,
 'converti', 6, 'agence', 'A acheté S+2 à Mégrine en janvier 2025. Client clôturé. Peut donner des références.',
 '2024-12-01 10:00:00', '2025-01-30 16:00:00'),

-- Locataires
('locataire', 'Omar', 'Slimane',        '+216 97 001 122', 'o.slimane@gmail.com',
 'Consultant', 'McKinsey Tunisie', '8 Rue du Lac Assal, Tunis', NULL, NULL, NULL, '1053',
 NULL, 1500.00, 2500.00, 'Lac 1, Lac 2, Les Berges',
 NULL, NULL,
 'actif', 4, 'site_web', 'Expatrié. Cherche appartement meublé haut de gamme pour 12 mois. Employeur paie.',
 '2025-03-05 10:00:00', '2025-04-10 09:00:00'),

('locataire', 'Fatima', 'Ben Amor',     '+216 23 112 233', 'fatima.benamor@yahoo.fr',
 'Étudiante', 'Université Centrale', '3 Rue des Étudiants, Bab Bhar', NULL, NULL, NULL, '1000',
 NULL, 600.00, 900.00, 'Tunis 1, Lafran, El Omrane',
 NULL, NULL,
 'contacte', 7, 'facebook', 'Cherche studio ou S+1 proche université. Caution parent disponible.',
 '2025-03-20 11:00:00', '2025-04-05 10:00:00'),

('locataire', 'Khaled', 'Gharbi',       '+216 54 223 334', 'k.gharbi@hotmail.com',
 'Directeur Commercial', 'Orange Tunisie', '10 Av. Kheireddine Pacha, Tunis', NULL, NULL, NULL, '1002',
 NULL, 2000.00, 3500.00, 'Lac, Berges du Lac, Ariana Soghra',
 NULL, NULL,
 'actif', 3, 'appel', 'Muté de Sfax à Tunis. Cherche S+3 meublé pour famille (femme + 2 enfants). Urgent.',
 '2025-04-01 09:00:00', '2025-04-10 10:00:00'),

('locataire', 'Dorra', 'Mezghani',      '+216 29 334 445', 'dorra.m@gmail.com',
 'Responsable RH', 'Leoni Tunisie', '15 Cité Olympique, Bizerte', NULL, NULL, NULL, '7000',
 NULL, 800.00, 1200.00, 'Bizerte Centre, Corniche',
 NULL, NULL,
 'nouveau', 7, 'instagram', 'Cherche S+2 non meublé pour s\'installer avec sa sœur. Préfère calme.',
 '2025-04-10 10:00:00', '2025-04-10 10:00:00'),

-- Propriétaires
('proprietaire', 'Hedi', 'Chaari',      '+216 71 567 890', 'hedi.chaari@gmail.com',
 'Retraité', NULL, '23 Rue Ibn Khaldoun, Hammamet', NULL, NULL, NULL, '8050',
 NULL, NULL, NULL, NULL,
 'Villa 4 chambres, piscine, 320m², Hammamet Nord', 1700000.00,
 'actif', 3, 'agence', 'Propriétaire vendeur. Bien libre immédiatement. Pressé de vendre (succession).',
 '2025-02-10 10:00:00', '2025-04-05 11:00:00'),

('proprietaire', 'Nadia', 'Amor',       '+216 22 678 901', 'nadia.amor@gmail.com',
 'Chef d\'entreprise', 'PGS Holding', '45 Rue Abdelaziz Thaalbi, Tunis', NULL, NULL, NULL, '1002',
 NULL, NULL, NULL, NULL,
 'Appartement S+3, 160m², Lac 1, 6ème étage', 680000.00,
 'actif', 4, 'referral', 'Propriétaire. A déjà un acquéreur potentiel. Cherche confirmation prix marché.',
 '2025-03-15 10:00:00', '2025-04-08 09:00:00'),

('proprietaire', 'Sami', 'Zghal',       '+216 98 789 012', 'sami.zghal@outlook.com',
 'Promoteur', 'SZ Immobilier', '1 Rue du Commerce, Sousse', NULL, NULL, NULL, '4000',
 NULL, NULL, NULL, NULL,
 'Immeuble R+4, 8 appartements, Sahloul', 3200000.00,
 'en_attente', 3, 'appel', 'Promoteur souhaitant vendre son programme en bloc. Négociation possible sur prix.',
 '2025-01-25 10:00:00', '2025-03-30 14:00:00'),

-- Investisseurs
('investisseur', 'Rami', 'Jouini',      '+216 55 890 123', 'rami.jouini@gmail.com',
 'Banquier', 'BIAT', '7 Rue de Rome, Tunis', NULL, NULL, NULL, '1001',
 NULL, 400000.00, 800000.00, 'Tunis, Sousse, Hammamet',
 NULL, NULL,
 'actif', 3, 'email', 'Investisseur actif. Cherche biens avec rentabilité locative > 6%. Pas de défiscalisation.',
 '2025-02-05 10:00:00', '2025-04-09 10:00:00'),

('investisseur', 'Leila', 'Jelassi',    '+216 27 901 234', 'l.jelassi@gmail.com',
 'Notaire', 'Étude Jelassi', '89 Av. de la Liberté, Tunis', NULL, NULL, NULL, '1002',
 NULL, 1000000.00, 2000000.00, 'Djerba, Hammamet, Sousse',
 NULL, NULL,
 'actif', 4, 'referral', 'Investit dans le tourisme. Cherche bien à fort potentiel Airbnb ou hôtelier.',
 '2025-01-30 10:00:00', '2025-04-06 09:00:00'),

('investisseur', 'Adel', 'Khouaja',     '+216 50 012 345', 'adel.khouaja@hotmail.com',
 'Directeur Financier', 'Poulina Group', '33 Cité Jardins, Tunis', NULL, NULL, NULL, '1082',
 NULL, 250000.00, 500000.00, 'Tunis périphérie, Ariana, Manouba',
 NULL, NULL,
 'nouveau', 6, 'site_web', 'Diversification patrimoine. Cherche appartement à louer. Préfère neuf ou récent.',
 '2025-04-09 10:00:00', '2025-04-09 10:00:00'),

('acheteur', 'Rim', 'Ouardani',         '+216 23 123 456', 'rim.ouardani@gmail.com',
 'Infirmière', 'Hôpital La Rabta', '14 Rue Sidi Mahrez, Tunis', NULL, NULL, NULL, '1006',
 NULL, 180000.00, 250000.00, 'Mégrine, Rades, Hammam Lif',
 NULL, NULL,
 'contacte', 7, 'facebook', 'Budget serré. Cherche S+1 ou petit S+2. Peut bénéficier prêt logement social.',
 '2025-03-25 10:00:00', '2025-04-08 11:00:00'),

('acheteur', 'Zied', 'Hajji',           '+216 54 234 567', 'zied.hajji@live.com',
 'Informaticien', 'Telnet', '56 Rue Ibn Sina, Ariana', NULL, NULL, NULL, '2080',
 NULL, 500000.00, 700000.00, 'Ariana, El Ghazala, Riadh Andlous',
 NULL, NULL,
 'actif', 6, 'instagram', 'Cherche S+3 ou duplex récent. Préfère résidence sécurisée avec ascenseur.',
 '2025-02-25 10:00:00', '2025-04-07 10:00:00'),

('locataire', 'Sara', 'Brahmi',         '+216 71 345 678', 'sara.brahmi@gmail.com',
 'Journaliste', 'Mosaïque FM', '3 Rue de Hollande, Tunis', NULL, NULL, NULL, '1000',
 NULL, 1000.00, 1500.00, 'Tunis Centre, Mutuelleville, Menzah',
 NULL, NULL,
 'actif', 7, 'site_web', 'Cherche S+2 non meublé pour longue durée. Propriétaire sérieux demandé.',
 '2025-03-18 10:00:00', '2025-04-09 09:00:00'),

('proprietaire', 'Maher', 'Ksontini',   '+216 98 456 789', 'maher.k@gmail.com',
 'Médecin', 'Clinique Hannibal', '12 Résidence Jasmin, Nabeul', NULL, NULL, NULL, '8000',
 NULL, NULL, NULL, NULL,
 'Terrain constructible 800m², lotissement approuvé, Nabeul', 220000.00,
 'actif', 4, 'appel', 'Propriétaire terrien. Ouvert à la négociation. Titre foncier propre.',
 '2025-03-08 10:00:00', '2025-04-05 14:00:00'),

('investisseur', 'Ghassen', 'Belhadj',  '+216 22 567 890', 'ghassen.bh@gmail.com',
 'PDG', 'Belhadj Frères SARL', '100 Route de Bizerte, Tunis', NULL, NULL, NULL, '1080',
 NULL, 2000000.00, 5000000.00, 'Lac, Berges du Lac, Cité Jardins',
 NULL, NULL,
 'actif', 3, 'referral', 'Groupe familial. Cherche actif immobilier commercial ou résidentiel haut de gamme. Décision rapide.',
 '2025-01-15 10:00:00', '2025-04-10 09:00:00'),

('acheteur', 'Nesrine', 'Abid',         '+216 55 678 901', 'nesrine.abid@yahoo.fr',
 'Professeure', 'Lycée Carthage', '7 Av. Mohamed V, La Marsa', NULL, NULL, NULL, '2078',
 NULL, 380000.00, 520000.00, 'La Marsa, Marsa Plage',
 NULL, NULL,
 'contacte', 3, 'site_web', 'Cherche appartement ou petite villa proche plage. Budget fixe non négociable.',
 '2025-04-05 10:00:00', '2025-04-10 09:00:00');

-- ============================================================
-- 4. LEADS / CRM – 18 leads liés aux biens et aux agents
-- ============================================================
INSERT INTO `leads`
  (`assigned_to`, `property_id`, `first_name`, `last_name`, `email`, `phone`,
   `source`, `status`, `priority`,
   `budget_min`, `budget_max`, `desired_surface`, `desired_location`,
   `property_type`, `transaction_type`, `notes`, `next_follow_up`,
   `created_at`, `updated_at`)
VALUES

-- Lead 1 : Intéressé par la villa La Marsa (property 1)
(3, 1, 'Kamel', 'Belhaj', 'kamel.belhaj@gmail.com', '+216 22 100 001',
 'website', 'negotiating', 'high',
 1500000.00, 2000000.00, 350.00, 'La Marsa, Gammarth',
 'villa', 'sale', 'Client sérieux. A visité 2 fois. Veut réduction 5%. Décision fin avril.', '2025-04-25',
 '2025-02-20 10:00:00', '2025-04-10 09:00:00'),

-- Lead 2 : Appart Lac 2 (property 2)
(4, 2, 'Sirine', 'Fehri', 'sirine.fehri@outlook.com', '+216 50 200 002',
 'referral', 'interested', 'high',
 650000.00, 800000.00, 160.00, 'Lac 1, Lac 2',
 'apartment', 'sale', 'Référée par client Tarek Mejri. Visite prévue ce weekend. Dossier bancaire OK.', '2025-04-20',
 '2025-03-15 11:00:00', '2025-04-09 14:00:00'),

-- Lead 3 : Cherche S+2 Sfax
(4, 8, 'Rachid', 'Gharbi', 'r.gharbi@gmail.com', '+216 97 300 003',
 'phone', 'visit_done', 'medium',
 240000.00, 300000.00, 100.00, 'Sfax El Ain, Chihia',
 'apartment', 'sale', 'A visité bien Sfax El Ain. Positif. Attend avis de son épouse. Relance semaine prochaine.', '2025-04-22',
 '2025-03-20 09:00:00', '2025-04-08 11:00:00'),

-- Lead 4 : Villa balnéaire Hammamet (property 6)
(3, 6, 'Sonia', 'Zribi', 'sonia.zribi@gmail.com', '+216 55 400 004',
 'website', 'interested', 'high',
 2000000.00, 2800000.00, 400.00, 'Hammamet, Yasmine Hammamet',
 'villa', 'sale', 'Famille aisée. Mari en déplacement. Souhaite visite le 26 avril avec toute la famille.', '2025-04-26',
 '2025-03-25 10:00:00', '2025-04-10 09:00:00'),

-- Lead 5 : Location bureau El Khadra (property 4)
(7, 4, 'Nour', 'Hamrouni', 'nour.hamrouni@cabinet.tn', '+216 71 500 005',
 'website', 'negotiating', 'high',
 NULL, NULL, 120.00, 'Tunis Centre, El Khadra, Menzah',
 'office', 'rent', 'Cabinet médical souhaitant louer. Demande bail 3 ans. Discute prix (veut 3500 DT).', '2025-04-18',
 '2025-03-28 10:00:00', '2025-04-09 15:00:00'),

-- Lead 6 : Studio Sousse (property 10)
(7, 10, 'Ahmed', 'Riahi', 'ahmed.riahi@hotmail.com', '+216 54 600 006',
 'social', 'contacted', 'low',
 700.00, 1100.00, 40.00, 'Sousse, Kantaoui',
 'apartment', 'rent', 'Cherche location courte durée 2 mois (juin-juillet). Demande disponibilité.', '2025-04-28',
 '2025-04-05 10:00:00', '2025-04-07 11:00:00'),

-- Lead 7 : Penthouse Lac 1 (property 20)
(3, 20, 'Amine', 'Dridi', 'amine.dridi@hotmail.com', '+216 22 700 007',
 'referral', 'interested', 'high',
 1800000.00, 2500000.00, 320.00, 'Les Berges du Lac',
 'apartment', 'sale', 'Homme d\'affaires. Cherche prestige. Penthouse Lac 1 lui correspond. Visite à organiser.', '2025-04-21',
 '2025-03-10 10:00:00', '2025-04-09 09:00:00'),

-- Lead 8 : Terrain Nabeul (property 7)
(4, 7, 'Mohamed', 'Lajmi', 'm.lajmi@lajmi-construction.tn', '+216 98 800 008',
 'phone', 'visit_done', 'medium',
 160000.00, 220000.00, NULL, 'Nabeul, Hammamet',
 'land', 'sale', 'Constructeur. A visité terrain Nabeul. Souhaite vérifier cote d\'urbanisme avant offre.', '2025-04-24',
 '2025-04-01 09:00:00', '2025-04-09 14:00:00'),

-- Lead 9 : Appart Ariana (property 14)
(6, 14, 'Olfa', 'Sassi', 'olfa.sassi@email.tn', '+216 27 900 009',
 'website', 'new', 'medium',
 500000.00, 620000.00, 170.00, 'Ariana, Riadh Andlous',
 'apartment', 'sale', 'Nouveau lead reçu ce matin. À qualifier.', NULL,
 '2025-04-10 08:30:00', '2025-04-10 08:30:00'),

-- Lead 10 : Houch Djerba (property 16)
(3, 16, 'Lotfi', 'Brik', 'lotfi.brik@gmail.com', '+216 71 010 010',
 'website', 'interested', 'medium',
 380000.00, 480000.00, 150.00, 'Djerba',
 'house', 'sale', 'Tunisien résidant en France. Cherche résidence secondaire à Djerba. Visite lors de son prochain séjour en mai.', '2025-05-10',
 '2025-03-30 10:00:00', '2025-04-08 09:00:00'),

-- Lead 11 : Local commercial Sfax (property 9)
(7, 9, 'Wiem', 'Achouri', 'wiem.achouri@gmail.com', '+216 54 011 011',
 'walk_in', 'contacted', 'medium',
 NULL, NULL, 200.00, 'Sfax Centre',
 'commercial', 'rent', 'Cherche local pour ouvrir pharmacie. Intéressée par bien Sfax. Demande plan de masse.', '2025-04-23',
 '2025-04-03 11:00:00', '2025-04-07 14:00:00'),

-- Lead 12 : Villa Sousse (property 11)
(6, 11, 'Belaid', 'Romdhane', 'belaid.r@gmail.com', '+216 20 012 012',
 'referral', 'won', 'high',
 900000.00, 1100000.00, 260.00, 'Sahloul, Khezama',
 'villa', 'sale', 'VENDU. Client a signé compromis le 2 mars 2025. Acte définitif prévu fin avril.', NULL,
 '2025-01-20 10:00:00', '2025-03-02 16:00:00'),

-- Lead 13 : Appart Monastir (property 12)
(7, 12, 'Jihen', 'Jerbi', 'jihen.jerbi@gmail.com', '+216 97 013 013',
 'social', 'interested', 'medium',
 360000.00, 420000.00, 115.00, 'Monastir, Skanes',
 'apartment', 'sale', 'Cherche appartement vue mer pour investissement Airbnb. Monastir idéal pour elle.', '2025-04-30',
 '2025-04-04 10:00:00', '2025-04-09 10:00:00'),

-- Lead 14 : Cherche S+1 Mégrine (property 13)
(6, 13, 'Anis', 'Baccouche', 'anis.baccouche@hotmail.com', '+216 50 014 014',
 'website', 'contacted', 'low',
 180000.00, 220000.00, 65.00, 'Mégrine, Ben Arous',
 'apartment', 'sale', 'Premier achat. A pris RDV pour visite semaine prochaine.', '2025-04-19',
 '2025-04-07 10:00:00', '2025-04-07 10:00:00'),

-- Lead 15 : Appart Lac 2 location (property 17)
(4, 17, 'Claire', 'Martin', 'claire.martin@bnp.fr', '+33 6 12 34 56 78',
 'referral', 'won', 'high',
 1800.00, 2500.00, 90.00, 'Lac 1, Lac 2',
 'apartment', 'rent', 'Expatriée française. A signé bail 1 an pour appart Lac 1. Emménagement 1er mars 2025.', NULL,
 '2025-02-01 10:00:00', '2025-02-28 14:00:00'),

-- Lead 16 : Duplex Ariana (property 14)
(3, 14, 'Haithem', 'Turki', 'h.turki@gmail.com', '+216 22 016 016',
 'phone', 'lost', 'medium',
 500000.00, 600000.00, 170.00, 'Ariana',
 'apartment', 'sale', 'A finalement acheté chez un concurrent. Lead perdu.', NULL,
 '2025-02-15 10:00:00', '2025-03-20 09:00:00'),

-- Lead 17 : Terrain agricole Grombalia (property 18)
(4, 18, 'Fathi', 'Marzouk', 'fathi.marzouk@gmail.com', '+216 55 017 017',
 'other', 'new', 'low',
 80000.00, 110000.00, NULL, 'Grombalia, Beni Khalled',
 'land', 'sale', 'Agriculteur cherchant terrain pour extension exploitation. Nouveau contact.', NULL,
 '2025-04-10 09:00:00', '2025-04-10 09:00:00'),

-- Lead 18 : Location local industriel (property 19)
(7, 19, 'Skander', 'Ben Miled', 'skander.bm@klass-autos.tn', '+216 98 018 018',
 'website', 'visit_done', 'high',
 NULL, NULL, 350.00, 'Ben Arous, Zone Industrielle',
 'commercial', 'rent', 'Concessionnaire auto. A visité local industriel. Très intéressé, attend accord associés.', '2025-04-16',
 '2025-04-06 10:00:00', '2025-04-09 11:00:00');

-- ============================================================
-- 5. NOTES LEADS – Historique des échanges
-- ============================================================
INSERT INTO `lead_notes` (`lead_id`, `user_id`, `note`, `created_at`) VALUES
(1,  3, 'Premier contact téléphonique. Client très intéressé par la villa La Marsa. Demande une réduction sur le prix.', '2025-02-21 10:00:00'),
(1,  3, 'Visite organisée le 28 février avec le client et son épouse. Retour très positif. Propose 1 750 000 DT.', '2025-03-01 14:00:00'),
(1,  3, 'Contre-offre du propriétaire : 1 820 000 DT. Client dit réfléchir.', '2025-03-15 09:00:00'),
(1,  3, 'Client revient avec offre ferme à 1 800 000 DT. Négociation en cours.', '2025-04-05 10:00:00'),

(2,  4, 'Référée par Tarek Mejri. Très bon profil. Veut visiter ce weekend.', '2025-03-16 09:00:00'),
(2,  4, 'Visite effectuée le 21 mars. Très intéressée. Dossier de financement en cours à la BIAT.', '2025-03-21 16:00:00'),

(3,  4, 'Appel entrant. Cherche appartement neuf à Sfax. Orienté vers RB-2025-0008.', '2025-03-21 10:00:00'),
(3,  4, 'Visite le 5 avril. Client positif, attend retour de son épouse pour décision.', '2025-04-05 17:00:00'),

(4,  3, 'Contact via site web. Famille cherchant villa balnéaire. Budget confortable.', '2025-03-26 09:00:00'),
(4,  3, 'Échange WhatsApp. Souhaite visite le 26 avril avec famille complète.', '2025-04-10 10:00:00'),

(5,  7, 'Cabinet médical souhaitant créer consultation à El Khadra. Loue actuellement à Menzah.', '2025-03-29 10:00:00'),
(5,  7, 'Contre-proposition à 3 500 DT/mois avec bail 3 ans indexé. Propriétaire réfléchit.', '2025-04-05 14:00:00'),

(7,  3, 'Contact par référence. Homme d\'affaires, cherche penthouse ou attique haut de gamme.', '2025-03-11 09:00:00'),
(7,  3, 'Envoi fiche technique penthouse Lac 1. Client très intéressé. Visite à organiser début mai.', '2025-04-09 10:00:00'),

(12, 6, 'Offre acceptée par le propriétaire. Compromis signé chez Me Bayoudh le 2 mars 2025.', '2025-03-02 15:00:00'),

(15, 4, 'Bail de 12 mois signé. Loyer 2 200 DT/mois. Caution = 2 mois. Entrée le 1er mars.', '2025-02-28 14:00:00');

-- ============================================================
-- 6. HISTORIQUE STATUTS LEADS
-- ============================================================
INSERT INTO `lead_status_history` (`lead_id`, `user_id`, `old_status`, `new_status`, `notes`, `created_at`) VALUES
(1,  3, NULL,           'new',          'Création du lead', '2025-02-20 10:00:00'),
(1,  3, 'new',          'contacted',    'Premier appel effectué', '2025-02-21 10:00:00'),
(1,  3, 'contacted',    'interested',   'Visite réalisée', '2025-03-01 14:00:00'),
(1,  3, 'interested',   'negotiating',  'Offre client à 1 800 000 DT', '2025-04-05 10:00:00'),

(2,  4, NULL,           'new',          'Lead entrant site web', '2025-03-15 11:00:00'),
(2,  4, 'new',          'contacted',    'Prise de contact', '2025-03-16 09:00:00'),
(2,  4, 'contacted',    'interested',   'Visite effectuée le 21/03', '2025-03-21 16:00:00'),

(5,  7, NULL,           'new',          'Lead entrant site web', '2025-03-28 10:00:00'),
(5,  7, 'new',          'contacted',    'Contact téléphonique', '2025-03-29 10:00:00'),
(5,  7, 'contacted',    'negotiating',  'Contre-proposition bail', '2025-04-05 14:00:00'),

(12, 6, NULL,           'new',          'Référence entrante', '2025-01-20 10:00:00'),
(12, 6, 'new',          'contacted',    'RDV visite planifié', '2025-01-22 09:00:00'),
(12, 6, 'contacted',    'visit_done',   'Visite réalisée', '2025-01-30 16:00:00'),
(12, 6, 'visit_done',   'negotiating',  'Offre verbale client', '2025-02-10 10:00:00'),
(12, 6, 'negotiating',  'won',          'Compromis signé', '2025-03-02 15:00:00'),

(16, 3, NULL,           'new',          'Lead entrant appel', '2025-02-15 10:00:00'),
(16, 3, 'new',          'contacted',    'RDV visite pris', '2025-02-16 09:00:00'),
(16, 3, 'contacted',    'visit_done',   'Visite réalisée', '2025-02-25 11:00:00'),
(16, 3, 'visit_done',   'lost',         'Client a acheté chez concurrent', '2025-03-20 09:00:00');

-- ============================================================
-- 7. VISITES – 16 visites planifiées/effectuées
-- ============================================================
INSERT INTO `visits`
  (`client_id`, `property_id`, `agent_id`, `visit_date`, `visit_time`, `duration`,
   `status`, `notes`, `feedback`, `feedback_notes`,
   `whatsapp_sent`, `reminder_sent`, `created_by`, `created_at`, `updated_at`)
VALUES

-- Visites effectuées (passées)
(1,  2,  3, '2025-02-28', '10:00:00', 90,  'effectuee', 'Visite complète bien Lac 2 avec M. Bouzid et son épouse.', 'interesse', 'Très positif, demande simulation bancaire BIAT. Rappeler lundi.', 1, 1, 3, '2025-02-25 10:00:00', '2025-02-28 12:00:00'),
(2,  1,  3, '2025-03-01', '14:30:00', 120, 'effectuee', 'Visite villa La Marsa avec Mme Hamdi. Vue jardin et piscine.', 'interesse', 'Coup de cœur. Souhaite négocier sur le prix. Budget max 1,2M.', 1, 1, 3, '2025-02-26 11:00:00', '2025-03-01 17:00:00'),
(3,  8,  4, '2025-04-05', '11:00:00', 60,  'effectuee', 'Visite appartement neuf Sfax El Ain avec M. Mansouri.', 'interesse', 'Client satisfait de l\'état du bien. Attend retour épouse.', 1, 1, 4, '2025-04-03 09:00:00', '2025-04-05 12:30:00'),
(5, 14,  3, '2025-02-25', '10:00:00', 75,  'effectuee', 'Visite duplex Ariana avec M. Mejri.', 'pas_interesse', 'Hauteur sous plafond insuffisante pour lui. Cherche plus grand.', 1, 1, 3, '2025-02-22 10:00:00', '2025-02-25 12:00:00'),
(7,  6,  3, '2025-03-12', '15:00:00', 120, 'effectuee', 'Visite villa Hammamet avec M. Sassi et associés.', 'negociation', 'Très intéressé pour investissement. Veut expertise et titre foncier.', 1, 1, 3, '2025-03-10 10:00:00', '2025-03-12 17:30:00'),
(9, 17,  4, '2025-02-20', '14:00:00', 45,  'effectuee', 'Visite appartement meublé Lac 1 avec M. Slimane.', 'interesse', 'Parfait pour ses besoins. Prêt à signer. Bail 12 mois.', 1, 1, 4, '2025-02-18 10:00:00', '2025-02-20 15:00:00'),
(12, 7,  4, '2025-04-09', '10:30:00', 60,  'effectuee', 'Visite terrain Nabeul avec M. Belhadj.', 'negociation', 'Intéressé. Demande vérification permis de construire avant offre ferme.', 1, 1, 4, '2025-04-07 09:00:00', '2025-04-09 12:00:00'),
(14, 3,  4, '2025-03-25', '11:00:00', 60,  'effectuee', 'Deuxième visite Appart Lac 2 avec Mme Amor.', 'interesse', 'Souhaite faire évaluer par expert Mme au pair. Attend rapport.', 1, 1, 4, '2025-03-22 10:00:00', '2025-03-25 12:30:00'),
(16, 11, 3, '2025-02-01', '10:00:00', 90,  'effectuee', 'Visite villa Sahloul avec Mme Jelassi.', 'interesse', 'Cliente convaincue du rendement locatif. Demande projection loyer.', 1, 1, 3, '2025-01-29 10:00:00', '2025-02-01 12:00:00'),
(18, 13, 6, '2025-04-08', '14:00:00', 45,  'effectuee', 'Visite S+1 Mégrine avec M. Khouaja.', 'pas_interesse', 'Trop petit pour ses besoins. Oriente vers S+2 neuf.', 1, 1, 6, '2025-04-06 10:00:00', '2025-04-08 15:00:00'),

-- Visites planifiées / confirmées (futures)
(4,  11, 7, '2025-04-20', '10:00:00', 90,  'confirmee', 'Visite villa Sahloul avec Mme Cherif. Bien correspond à son budget.', NULL, NULL, 1, 0, 7, '2025-04-12 09:00:00', '2025-04-14 10:00:00'),
(4,  2,  3, '2025-04-21', '14:00:00', 60,  'planifiee', 'Alternative Lac 2 proposée à Mme Cherif si villa trop grande.', NULL, NULL, 0, 0, 3, '2025-04-12 10:00:00', '2025-04-12 10:00:00'),
(19, 14, 6, '2025-04-22', '11:00:00', 75,  'confirmee', 'Visite duplex Ariana avec M. Hajji. Correspond parfaitement à sa recherche.', NULL, NULL, 1, 0, 6, '2025-04-10 10:00:00', '2025-04-14 09:00:00'),
(10, 4,  7, '2025-04-18', '10:00:00', 60,  'planifiee', 'Visite bureau El Khadra avec Mme Hamrouni (cabinet médical).', NULL, NULL, 1, 0, 7, '2025-04-09 15:00:00', '2025-04-09 15:00:00'),
(1,  20, 3, '2025-04-26', '15:00:00', 90,  'planifiee', 'Visite penthouse Lac 1 avec M. Bouzid. Upgraded budget.', NULL, NULL, 0, 0, 3, '2025-04-10 09:00:00', '2025-04-10 09:00:00'),
(23, 6,  3, '2025-04-26', '11:00:00', 120, 'planifiee', 'Grande visite villa Hammamet avec famille Belhadj + associés.', NULL, NULL, 1, 0, 3, '2025-04-10 09:00:00', '2025-04-10 09:00:00');

-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================
-- FIN — Données de démonstration Rebencia
-- Agents : k.bensalah@rebencia.com / Admin@2024 (tous les agents)
-- ============================================================
