-- Script SQL pour créer manuellement les tables sliders et theme_settings
-- À exécuter si les migrations ne fonctionnent pas

-- Supprimer la table sliders si elle existe déjà avec l'ancienne structure
DROP TABLE IF EXISTS `sliders`;

-- Table sliders
CREATE TABLE `sliders` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `button1_text` varchar(100) DEFAULT NULL,
  `button1_link` varchar(255) DEFAULT NULL,
  `button2_text` varchar(100) DEFAULT NULL,
  `button2_link` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `animation_type` varchar(50) DEFAULT 'fade',
  `text_position` enum('left','center','right') DEFAULT 'center',
  `overlay_opacity` int(3) DEFAULT 50,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `display_order` (`display_order`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Supprimer la table theme_settings si elle existe déjà avec l'ancienne structure
DROP TABLE IF EXISTS `theme_settings`;

-- Table theme_settings
CREATE TABLE `theme_settings` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `primary_color` varchar(7) DEFAULT '#667eea',
  `secondary_color` varchar(7) DEFAULT '#764ba2',
  `accent_color` varchar(7) DEFAULT '#f5576c',
  `text_dark` varchar(7) DEFAULT '#2d3748',
  `text_light` varchar(7) DEFAULT '#ffffff',
  `background_light` varchar(7) DEFAULT '#f7fafc',
  `font_family_primary` varchar(100) DEFAULT 'Poppins',
  `font_family_secondary` varchar(100) DEFAULT 'Roboto',
  `font_size_base` varchar(20) DEFAULT '16px',
  `border_radius` varchar(20) DEFAULT '8px',
  `button_bg_color` varchar(7) DEFAULT '#667eea',
  `button_text_color` varchar(7) DEFAULT '#ffffff',
  `button_hover_bg_color` varchar(7) DEFAULT '#764ba2',
  `button_hover_text_color` varchar(7) DEFAULT '#ffffff',
  `button_border_width` varchar(10) DEFAULT '0px',
  `button_border_color` varchar(7) DEFAULT '#667eea',
  `button_padding` varchar(20) DEFAULT '12px 30px',
  `button_font_size` varchar(20) DEFAULT '16px',
  `button_font_weight` varchar(10) DEFAULT '500',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insérer les valeurs par défaut du thème
INSERT INTO `theme_settings` (
  `id`, `primary_color`, `secondary_color`, `accent_color`, `text_dark`, `text_light`, `background_light`, 
  `font_family_primary`, `font_family_secondary`, `font_size_base`, `border_radius`,
  `button_bg_color`, `button_text_color`, `button_hover_bg_color`, `button_hover_text_color`,
  `button_border_width`, `button_border_color`, `button_padding`, `button_font_size`, `button_font_weight`,
  `updated_at`
) 
VALUES (
  1, '#667eea', '#764ba2', '#f5576c', '#2d3748', '#ffffff', '#f7fafc', 
  'Poppins', 'Roboto', '16px', '8px',
  '#667eea', '#ffffff', '#764ba2', '#ffffff',
  '0px', '#667eea', '12px 30px', '16px', '500',
  NOW()
)
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Exemples de sliders (optionnel)
INSERT INTO `sliders` (`title`, `subtitle`, `description`, `image`, `button1_text`, `button1_link`, `button2_text`, `button2_link`, `display_order`, `is_active`, `animation_type`, `text_position`, `overlay_opacity`, `created_at`, `updated_at`) VALUES
('Trouvez votre bien idéal', 'Des milliers de propriétés disponibles', 'Découvrez notre sélection exclusive de biens immobiliers adaptés à vos besoins et votre budget.', NULL, 'Voir les biens', '/properties', 'Nous contacter', '/contact', 1, 1, 'fade', 'center', 50, NOW(), NOW()),
('Vendez rapidement', 'Expertise et accompagnement personnalisé', 'Nos agents vous accompagnent dans toutes les étapes de la vente de votre propriété.', NULL, 'Estimer mon bien', '/estimations', 'En savoir plus', '/about', 2, 1, 'slide', 'left', 60, NOW(), NOW()),
('Investissez intelligemment', 'Opportunités d\'investissement', 'Profitez de notre expertise pour réaliser des investissements immobiliers rentables.', NULL, 'Découvrir', '/properties', NULL, NULL, 3, 1, 'zoom', 'right', 55, NOW(), NOW());
