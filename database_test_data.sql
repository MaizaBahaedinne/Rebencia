-- Script pour générer des données de test pour les transactions
-- Exécuter ce script dans phpMyAdmin ou votre client MySQL

USE rebe_RebenciaDB;

-- Insérer des clients (particuliers et sociétés)
INSERT IGNORE INTO clients (type, first_name, last_name, email, phone, status, source, created_at) VALUES
('individual', 'Ahmed', 'Ben Salem', 'ahmed.bensalem@example.com', '+216 98 123 456', 'active', 'website', NOW()),
('individual', 'Fatma', 'Gharbi', 'fatma.gharbi@example.com', '+216 97 234 567', 'active', 'referral', NOW()),
('individual', 'Mohamed', 'Trabelsi', 'mohamed.trabelsi@example.com', '+216 96 345 678', 'active', 'social_media', NOW()),
('individual', 'Leila', 'Khemiri', 'leila.khemiri@example.com', '+216 95 456 789', 'active', 'walk_in', NOW()),
('individual', 'Karim', 'Bouazizi', 'karim.bouazizi@example.com', '+216 94 567 890', 'active', 'website', NOW()),
('individual', 'Sonia', 'Mejri', 'sonia.mejri@example.com', '+216 93 678 901', 'active', 'phone', NOW()),
('individual', 'Nadia', 'Sfar', 'nadia.sfar@example.com', '+216 92 789 012', 'active', 'referral', NOW()),
('company', 'Riadh', 'Hamdi', 'contact@hamdi-immo.tn', '+216 91 890 123', 'active', 'website', NOW());

-- Insérer des utilisateurs (agents) - mot de passe : password123
INSERT IGNORE INTO users (username, first_name, last_name, email, phone, password_hash, role_id, status, created_at) VALUES
('agent1', 'Youssef', 'Mansour', 'youssef.mansour@rebencia.com', '+216 92 111 222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, 'active', NOW()),
('agent2', 'Amira', 'Belgacem', 'amira.belgacem@rebencia.com', '+216 92 222 333', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, 'active', NOW()),
('coordinator1', 'Hichem', 'Jebali', 'hichem.jebali@rebencia.com', '+216 92 333 444', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 7, 'active', NOW());

-- Insérer des agences
INSERT IGNORE INTO agencies (code, name, type, is_headquarters, city, governorate, address, phone, email, status, created_at) VALUES
('REB-TUNIS', 'Rebencia Tunis Centre', 'siege', 1, 'Tunis', 'Tunis', 'Avenue Habib Bourguiba, Tunis', '+216 71 123 456', 'tunis@rebencia.com', 'active', NOW()),
('REB-SFAX', 'Rebencia Sfax', 'agence', 0, 'Sfax', 'Sfax', 'Avenue Majida Boulila, Sfax', '+216 74 234 567', 'sfax@rebencia.com', 'active', NOW()),
('REB-SOUSSE', 'Rebencia Sousse', 'agence', 0, 'Sousse', 'Sousse', 'Boulevard Yahia Ibn Omar, Sousse', '+216 73 345 678', 'sousse@rebencia.com', 'active', NOW());

-- Insérer des propriétés
INSERT IGNORE INTO properties (reference, title, type, transaction_type, price, rental_price, area_total, bedrooms, bathrooms, address, city, governorate, status, created_at) VALUES
('PROP-2026-001', 'Appartement S+2 Centre Ville', 'apartment', 'sale', 250000.00, NULL, 95.00, 2, 1, 'Avenue de la Liberté, Tunis', 'Tunis', 'Tunis', 'published', NOW()),
('PROP-2026-002', 'Villa S+4 avec Jardin', 'villa', 'sale', 850000.00, NULL, 280.00, 4, 3, 'Les Berges du Lac, Tunis', 'Tunis', 'Tunis', 'published', NOW()),
('PROP-2026-003', 'Appartement S+3 Meublé', 'apartment', 'rent', 1200.00, 1200.00, 120.00, 3, 2, 'Lac 2, Tunis', 'Tunis', 'Tunis', 'published', NOW()),
('PROP-2026-004', 'Bureau 150m² Centre Ville', 'office', 'rent', 2500.00, 2500.00, 150.00, 0, 1, 'Avenue Mohamed V, Tunis', 'Tunis', 'Tunis', 'published', NOW()),
('PROP-2026-005', 'Terrain Constructible 500m²', 'land', 'sale', 180000.00, NULL, 500.00, 0, 0, 'Zone Industrielle, Ariana', 'Ariana', 'Ariana', 'published', NOW());

-- Vérifier les données insérées
SELECT 'Clients insérés:' as Info, COUNT(*) as Total FROM clients;
SELECT 'Utilisateurs insérés:' as Info, COUNT(*) as Total FROM users;
SELECT 'Agences insérées:' as Info, COUNT(*) as Total FROM agencies;
SELECT 'Propriétés insérées:' as Info, COUNT(*) as Total FROM properties;
