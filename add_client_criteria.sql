-- ============================================================
-- Ajout des nouveaux critères clients
-- À exécuter sur rebe_RebenciaDB (production) et rebencia (local)
-- ============================================================

ALTER TABLE `clients`
  ADD COLUMN `bathrooms_min`      TINYINT UNSIGNED NULL          COMMENT 'Nb salles de bain minimum'      AFTER `bedrooms_min`,
  ADD COLUMN `parking_min`        TINYINT UNSIGNED NULL          COMMENT 'Nb places parking minimum'      AFTER `bathrooms_min`,
  ADD COLUMN `construction_state` ENUM('neuf','recent','ancien','a_renover','indifferent') NULL
                                                                 COMMENT 'État du bien recherché'          AFTER `parking_min`,
  ADD COLUMN `furnished`          ENUM('meuble','semi_meuble','vide','indifferent') NULL
                                                                 COMMENT 'Meublement souhaité'             AFTER `construction_state`;
