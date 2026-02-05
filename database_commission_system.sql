-- ============================================================================
-- COMMISSION MANAGEMENT SYSTEM - DATABASE SCHEMA
-- Real Estate Platform - Rebencia
-- Version: 1.0
-- Date: 2026-02-05
-- ============================================================================

USE rebe_RebenciaDB;

-- ============================================================================
-- 1. COMMISSION RULES TABLE (System-level defaults)
-- ============================================================================
CREATE TABLE IF NOT EXISTS commission_rules (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Rule name (e.g., "Vente Appartement Standard")',
    
    -- Transaction context
    transaction_type ENUM('sale', 'rent') NOT NULL COMMENT 'Type de transaction',
    property_type ENUM('apartment', 'villa', 'house', 'land', 'commercial', 'office', 'business') NOT NULL COMMENT 'Type de bien',
    
    -- Buyer/Tenant commission
    buyer_commission_type ENUM('percentage', 'fixed', 'months') NOT NULL DEFAULT 'percentage',
    buyer_commission_value DECIMAL(10,2) NOT NULL COMMENT 'Valeur ou pourcentage',
    buyer_commission_vat DECIMAL(5,2) DEFAULT 19.00 COMMENT 'TVA applicable (%)',
    
    -- Seller/Owner commission
    seller_commission_type ENUM('percentage', 'fixed', 'months') NOT NULL DEFAULT 'percentage',
    seller_commission_value DECIMAL(10,2) NOT NULL COMMENT 'Valeur ou pourcentage',
    seller_commission_vat DECIMAL(5,2) DEFAULT 19.00 COMMENT 'TVA applicable (%)',
    
    -- Metadata
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Règle active',
    is_default TINYINT(1) DEFAULT 0 COMMENT 'Règle par défaut pour ce type',
    description TEXT COMMENT 'Description de la règle',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_transaction_property (transaction_type, property_type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Règles de commission par défaut du système';

-- ============================================================================
-- 2. COMMISSION OVERRIDES TABLE (Agency/Role/User level)
-- ============================================================================
CREATE TABLE IF NOT EXISTS commission_overrides (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Override scope (hierarchy)
    override_level ENUM('agency', 'role', 'user') NOT NULL COMMENT 'Niveau de surcharge',
    agency_id INT(10) UNSIGNED NULL COMMENT 'ID agence (si override_level = agency)',
    role_id INT(10) UNSIGNED NULL COMMENT 'ID rôle (si override_level = role)',
    user_id INT(10) UNSIGNED NULL COMMENT 'ID utilisateur (si override_level = user)',
    
    -- Transaction context (inherited from rule)
    transaction_type ENUM('sale', 'rent') NOT NULL,
    property_type ENUM('apartment', 'villa', 'house', 'land', 'commercial', 'office', 'business') NOT NULL,
    
    -- Buyer/Tenant commission override
    buyer_commission_type ENUM('percentage', 'fixed', 'months') NULL,
    buyer_commission_value DECIMAL(10,2) NULL,
    buyer_commission_vat DECIMAL(5,2) NULL,
    
    -- Seller/Owner commission override
    seller_commission_type ENUM('percentage', 'fixed', 'months') NULL,
    seller_commission_value DECIMAL(10,2) NULL,
    seller_commission_vat DECIMAL(5,2) NULL,
    
    -- Metadata
    is_active TINYINT(1) DEFAULT 1,
    notes TEXT COMMENT 'Raison de la surcharge',
    created_by INT(10) UNSIGNED COMMENT 'Qui a créé cette surcharge',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_agency (agency_id),
    INDEX idx_role (role_id),
    INDEX idx_user (user_id),
    INDEX idx_transaction_property (transaction_type, property_type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Surcharges de commission par agence/rôle/utilisateur';

-- ============================================================================
-- 3. TRANSACTION COMMISSIONS TABLE (Calculated commissions per transaction)
-- ============================================================================
CREATE TABLE IF NOT EXISTS transaction_commissions (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Transaction reference
    transaction_id INT(10) UNSIGNED NOT NULL COMMENT 'ID de la transaction',
    property_id INT(10) UNSIGNED NOT NULL COMMENT 'ID du bien',
    
    -- Applied rule tracking
    rule_id INT(10) UNSIGNED NULL COMMENT 'Règle utilisée',
    override_id INT(10) UNSIGNED NULL COMMENT 'Surcharge appliquée (si existe)',
    override_level ENUM('system', 'agency', 'role', 'user') NOT NULL DEFAULT 'system',
    
    -- Transaction details
    transaction_type ENUM('sale', 'rent') NOT NULL,
    property_type ENUM('apartment', 'villa', 'house', 'land', 'commercial', 'office', 'business') NOT NULL,
    transaction_amount DECIMAL(15,2) NOT NULL COMMENT 'Montant de la transaction',
    
    -- BUYER/TENANT COMMISSION
    buyer_commission_ht DECIMAL(15,2) NOT NULL COMMENT 'Commission acheteur HT',
    buyer_commission_vat DECIMAL(15,2) NOT NULL COMMENT 'TVA acheteur',
    buyer_commission_ttc DECIMAL(15,2) NOT NULL COMMENT 'Commission acheteur TTC',
    buyer_commission_type ENUM('percentage', 'fixed', 'months') NOT NULL,
    buyer_commission_value DECIMAL(10,2) NOT NULL,
    
    -- SELLER/OWNER COMMISSION
    seller_commission_ht DECIMAL(15,2) NOT NULL COMMENT 'Commission vendeur HT',
    seller_commission_vat DECIMAL(15,2) NOT NULL COMMENT 'TVA vendeur',
    seller_commission_ttc DECIMAL(15,2) NOT NULL COMMENT 'Commission vendeur TTC',
    seller_commission_type ENUM('percentage', 'fixed', 'months') NOT NULL,
    seller_commission_value DECIMAL(10,2) NOT NULL,
    
    -- TOTAL COMMISSION
    total_commission_ht DECIMAL(15,2) NOT NULL,
    total_commission_vat DECIMAL(15,2) NOT NULL,
    total_commission_ttc DECIMAL(15,2) NOT NULL,
    
    -- COMMISSION SPLITS (future implementation)
    agent_id INT(10) UNSIGNED COMMENT 'Agent responsable',
    agent_commission_percentage DECIMAL(5,2) DEFAULT 50.00 COMMENT '% de commission pour agent',
    agent_commission_amount DECIMAL(15,2) COMMENT 'Montant agent',
    agency_commission_amount DECIMAL(15,2) COMMENT 'Montant agence',
    
    -- Payment tracking
    payment_status ENUM('pending', 'partial', 'paid') DEFAULT 'pending',
    paid_amount DECIMAL(15,2) DEFAULT 0.00,
    payment_date DATE NULL,
    
    -- Metadata
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    calculated_by INT(10) UNSIGNED COMMENT 'Qui a calculé',
    validated_at TIMESTAMP NULL,
    validated_by INT(10) UNSIGNED NULL,
    
    notes TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (rule_id) REFERENCES commission_rules(id) ON DELETE SET NULL,
    FOREIGN KEY (override_id) REFERENCES commission_overrides(id) ON DELETE SET NULL,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (calculated_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (validated_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY uk_transaction (transaction_id),
    INDEX idx_property (property_id),
    INDEX idx_agent (agent_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_calculated_at (calculated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Commissions calculées par transaction';

-- ============================================================================
-- 4. COMMISSION LOGS TABLE (Audit trail)
-- ============================================================================
CREATE TABLE IF NOT EXISTS commission_logs (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- What changed
    entity_type ENUM('rule', 'override', 'commission', 'payment') NOT NULL,
    entity_id INT(10) UNSIGNED NOT NULL COMMENT 'ID de l\'entité modifiée',
    
    -- Action performed
    action ENUM('create', 'update', 'delete', 'calculate', 'validate', 'payment') NOT NULL,
    
    -- Who & When
    user_id INT(10) UNSIGNED NOT NULL,
    user_role VARCHAR(50),
    ip_address VARCHAR(45),
    
    -- Change details
    old_values JSON NULL COMMENT 'Anciennes valeurs',
    new_values JSON NULL COMMENT 'Nouvelles valeurs',
    description TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Journal d\'audit des commissions';

-- ============================================================================
-- INSERT DEFAULT COMMISSION RULES
-- ============================================================================

-- 1) Property Sale (Apartment, Villa, House, Land)
INSERT INTO commission_rules (name, transaction_type, property_type, 
    buyer_commission_type, buyer_commission_value, buyer_commission_vat,
    seller_commission_type, seller_commission_value, seller_commission_vat,
    is_active, is_default, description) 
VALUES
-- Apartments
('Vente Appartement - Standard', 'sale', 'apartment', 
    'percentage', 2.00, 19.00, 
    'percentage', 3.00, 19.00, 
    1, 1, 'Commission standard: 2% acheteur + 3% vendeur = 5% total'),

-- Villas
('Vente Villa - Standard', 'sale', 'villa', 
    'percentage', 2.00, 19.00, 
    'percentage', 3.00, 19.00, 
    1, 1, 'Commission standard: 2% acheteur + 3% vendeur = 5% total'),

-- Houses
('Vente Maison - Standard', 'sale', 'house', 
    'percentage', 2.00, 19.00, 
    'percentage', 3.00, 19.00, 
    1, 1, 'Commission standard: 2% acheteur + 3% vendeur = 5% total'),

-- Land
('Vente Terrain - Standard', 'sale', 'land', 
    'percentage', 2.00, 19.00, 
    'percentage', 3.00, 19.00, 
    1, 1, 'Commission standard: 2% acheteur + 3% vendeur = 5% total'),

-- Commercial/Office
('Vente Commercial - Standard', 'sale', 'commercial', 
    'percentage', 2.00, 19.00, 
    'percentage', 3.00, 19.00, 
    1, 1, 'Commission standard: 2% acheteur + 3% vendeur = 5% total'),

('Vente Bureau - Standard', 'sale', 'office', 
    'percentage', 2.00, 19.00, 
    'percentage', 3.00, 19.00, 
    1, 1, 'Commission standard: 2% acheteur + 3% vendeur = 5% total'),

-- 2) Business Sale (Fonds de commerce)
('Vente Fonds de Commerce - Standard', 'sale', 'business', 
    'percentage', 5.00, 19.00, 
    'percentage', 5.00, 19.00, 
    1, 1, 'Commission spéciale fonds de commerce: 5% acheteur + 5% vendeur = 10% total'),

-- 3) Rentals (all property types)
('Location Appartement - Standard', 'rent', 'apartment', 
    'months', 1.00, 19.00, 
    'months', 1.00, 19.00, 
    1, 1, 'Commission location: 1 mois locataire + 1 mois propriétaire = 2 mois total (HT)'),

('Location Villa - Standard', 'rent', 'villa', 
    'months', 1.00, 19.00, 
    'months', 1.00, 19.00, 
    1, 1, 'Commission location: 1 mois locataire + 1 mois propriétaire = 2 mois total (HT)'),

('Location Maison - Standard', 'rent', 'house', 
    'months', 1.00, 19.00, 
    'months', 1.00, 19.00, 
    1, 1, 'Commission location: 1 mois locataire + 1 mois propriétaire = 2 mois total (HT)'),

('Location Commercial - Standard', 'rent', 'commercial', 
    'months', 1.00, 19.00, 
    'months', 1.00, 19.00, 
    1, 1, 'Commission location: 1 mois locataire + 1 mois propriétaire = 2 mois total (HT)'),

('Location Bureau - Standard', 'rent', 'office', 
    'months', 1.00, 19.00, 
    'months', 1.00, 19.00, 
    1, 1, 'Commission location: 1 mois locataire + 1 mois propriétaire = 2 mois total (HT)');

-- ============================================================================
-- ADD PERMISSIONS FOR COMMISSION MANAGEMENT
-- ============================================================================

-- Delete existing commission permissions if they exist
DELETE FROM permissions WHERE module = 'commissions';

-- Insert commission permissions
INSERT INTO permissions (module, name, display_name, description, created_at) VALUES
('commissions', 'commissions_view', 'Voir les commissions', 'Consulter les commissions et rapports', NOW()),
('commissions', 'commissions_create', 'Calculer les commissions', 'Calculer les commissions sur transactions', NOW()),
('commissions', 'commissions_validate', 'Valider les commissions', 'Valider les commissions calculées', NOW()),
('commissions', 'commissions_edit_rules', 'Modifier les règles', 'Modifier les règles de commission système', NOW()),
('commissions', 'commissions_edit_overrides', 'Gérer les surcharges', 'Créer et modifier les surcharges de commission', NOW()),
('commissions', 'commissions_payments', 'Gérer les paiements', 'Enregistrer les paiements de commission', NOW()),
('commissions', 'commissions_reports', 'Rapports avancés', 'Accéder aux rapports détaillés', NOW());

-- Assign permissions to roles
-- Super Admin gets all permissions
INSERT INTO role_permissions (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT r.id, p.id, 1, 1, 1, 1, 1
FROM roles r
CROSS JOIN permissions p
WHERE r.level = 100 AND p.module = 'commissions';

-- Admin gets most permissions
INSERT INTO role_permissions (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT r.id, p.id, 
    CASE WHEN p.name IN ('commissions_edit_rules') THEN 0 ELSE 1 END,
    1,
    CASE WHEN p.name IN ('commissions_edit_rules') THEN 0 ELSE 1 END,
    0,
    CASE WHEN p.name IN ('commissions_validate', 'commissions_payments') THEN 1 ELSE 0 END
FROM roles r
CROSS JOIN permissions p
WHERE r.level BETWEEN 80 AND 89 AND p.module = 'commissions';

-- Managers can view and create
INSERT INTO role_permissions (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT r.id, p.id, 
    CASE WHEN p.name IN ('commissions_view', 'commissions_create', 'commissions_reports') THEN 1 ELSE 0 END,
    1,
    0, 0, 0
FROM roles r
CROSS JOIN permissions p
WHERE r.level BETWEEN 50 AND 79 AND p.module = 'commissions';

-- Agents can only view their own commissions
INSERT INTO role_permissions (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT r.id, p.id, 0, 1, 0, 0, 0
FROM roles r
CROSS JOIN permissions p
WHERE r.level BETWEEN 20 AND 49 AND p.module = 'commissions' AND p.name = 'commissions_view';

-- ============================================================================
-- VIEWS FOR REPORTING
-- ============================================================================

-- Commission summary by agent
CREATE OR REPLACE VIEW v_agent_commissions AS
SELECT 
    u.id as agent_id,
    CONCAT(u.first_name, ' ', u.last_name) as agent_name,
    u.agency_id,
    a.name as agency_name,
    COUNT(tc.id) as total_transactions,
    SUM(tc.total_commission_ht) as total_commission_ht,
    SUM(tc.total_commission_ttc) as total_commission_ttc,
    SUM(tc.agent_commission_amount) as agent_commission_total,
    SUM(tc.agency_commission_amount) as agency_commission_total,
    SUM(CASE WHEN tc.payment_status = 'paid' THEN tc.paid_amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN tc.payment_status = 'pending' THEN tc.total_commission_ttc ELSE 0 END) as total_pending
FROM users u
LEFT JOIN agencies a ON u.agency_id = a.id
LEFT JOIN transaction_commissions tc ON u.id = tc.agent_id
GROUP BY u.id, u.first_name, u.last_name, u.agency_id, a.name;

-- Commission summary by property type
CREATE OR REPLACE VIEW v_commission_by_property_type AS
SELECT 
    property_type,
    transaction_type,
    COUNT(*) as transaction_count,
    SUM(total_commission_ht) as total_ht,
    SUM(total_commission_ttc) as total_ttc,
    AVG(total_commission_ht) as avg_commission_ht
FROM transaction_commissions
GROUP BY property_type, transaction_type;

-- ============================================================================
-- INDEXES FOR PERFORMANCE
-- ============================================================================

-- Additional indexes for common queries
CREATE INDEX idx_commission_date_range ON transaction_commissions(calculated_at, payment_status);
CREATE INDEX idx_override_active_level ON commission_overrides(override_level, is_active);

-- ============================================================================
-- COMPLETION MESSAGE
-- ============================================================================
SELECT 'Commission system database schema created successfully!' as status,
       (SELECT COUNT(*) FROM commission_rules) as default_rules_count,
       (SELECT COUNT(*) FROM permissions WHERE module = 'commissions') as permissions_count;
