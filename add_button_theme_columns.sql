-- Script SQL pour ajouter les colonnes de personnalisation des boutons à une table theme_settings existante
-- À exécuter si vous avez déjà une table theme_settings sans les colonnes des boutons

-- Ajouter les colonnes pour le design des boutons primaires
ALTER TABLE `theme_settings` 
ADD COLUMN IF NOT EXISTS `button_bg_color` varchar(7) DEFAULT '#667eea' AFTER `border_radius`,
ADD COLUMN IF NOT EXISTS `button_text_color` varchar(7) DEFAULT '#ffffff' AFTER `button_bg_color`,
ADD COLUMN IF NOT EXISTS `button_hover_bg_color` varchar(7) DEFAULT '#764ba2' AFTER `button_text_color`,
ADD COLUMN IF NOT EXISTS `button_hover_text_color` varchar(7) DEFAULT '#ffffff' AFTER `button_hover_bg_color`,
ADD COLUMN IF NOT EXISTS `button_border_width` varchar(10) DEFAULT '0px' AFTER `button_hover_text_color`,
ADD COLUMN IF NOT EXISTS `button_border_color` varchar(7) DEFAULT '#667eea' AFTER `button_border_width`,
ADD COLUMN IF NOT EXISTS `button_padding` varchar(20) DEFAULT '12px 30px' AFTER `button_border_color`,
ADD COLUMN IF NOT EXISTS `button_font_size` varchar(20) DEFAULT '16px' AFTER `button_padding`,
ADD COLUMN IF NOT EXISTS `button_font_weight` varchar(10) DEFAULT '500' AFTER `button_font_size`,
ADD COLUMN IF NOT EXISTS `button_secondary_bg_color` varchar(7) DEFAULT '#6c757d' AFTER `button_font_weight`,
ADD COLUMN IF NOT EXISTS `button_secondary_text_color` varchar(7) DEFAULT '#ffffff' AFTER `button_secondary_bg_color`,
ADD COLUMN IF NOT EXISTS `button_secondary_hover_bg_color` varchar(7) DEFAULT '#5a6268' AFTER `button_secondary_text_color`,
ADD COLUMN IF NOT EXISTS `button_secondary_hover_text_color` varchar(7) DEFAULT '#ffffff' AFTER `button_secondary_hover_bg_color`,
ADD COLUMN IF NOT EXISTS `link_color` varchar(7) DEFAULT '#667eea' AFTER `button_secondary_hover_text_color`,
ADD COLUMN IF NOT EXISTS `link_hover_color` varchar(7) DEFAULT '#764ba2' AFTER `link_color`,
ADD COLUMN IF NOT EXISTS `link_decoration` varchar(20) DEFAULT 'none' AFTER `link_hover_color`,
ADD COLUMN IF NOT EXISTS `page_max_width` varchar(20) DEFAULT '1200px' AFTER `link_decoration`;

-- Mettre à jour la ligne existante avec les valeurs par défaut si elles sont NULL
UPDATE `theme_settings` 
SET 
    `button_bg_color` = COALESCE(`button_bg_color`, '#667eea'),
    `button_text_color` = COALESCE(`button_text_color`, '#ffffff'),
    `button_hover_bg_color` = COALESCE(`button_hover_bg_color`, '#764ba2'),
    `button_hover_text_color` = COALESCE(`button_hover_text_color`, '#ffffff'),
    `button_border_width` = COALESCE(`button_border_width`, '0px'),
    `button_border_color` = COALESCE(`button_border_color`, '#667eea'),
    `button_padding` = COALESCE(`button_padding`, '12px 30px'),
    `button_font_size` = COALESCE(`button_font_size`, '16px'),
    `button_font_weight` = COALESCE(`button_font_weight`, '500'),
    `button_secondary_bg_color` = COALESCE(`button_secondary_bg_color`, '#6c757d'),
    `button_secondary_text_color` = COALESCE(`button_secondary_text_color`, '#ffffff'),
    `button_secondary_hover_bg_color` = COALESCE(`button_secondary_hover_bg_color`, '#5a6268'),
    `button_secondary_hover_text_color` = COALESCE(`button_secondary_hover_text_color`, '#ffffff'),
    `link_color` = COALESCE(`link_color`, '#667eea'),
    `link_hover_color` = COALESCE(`link_hover_color`, '#764ba2'),
    `link_decoration` = COALESCE(`link_decoration`, 'none'),
    `page_max_width` = COALESCE(`page_max_width`, '1200px')
WHERE id = 1;

SELECT 'Colonnes de personnalisation des boutons ajoutées avec succès!' AS message;
