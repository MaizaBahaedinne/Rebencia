-- ============================================================
-- REBENCIA — Enrichissement champs projet immobilier sur leads
-- À exécuter sur : rebencia (local) et rebe_RebenciaDB (production)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Types de biens multiples (JSON : ["apartment","villa",...])
ALTER TABLE `leads`
  ADD COLUMN `property_types` JSON NULL COMMENT 'Types de biens souhaités (tableau JSON)' AFTER `property_type`;

-- Zones souhaitées (JSON : [12, 45, ...] = IDs zones)
ALTER TABLE `leads`
  ADD COLUMN `desired_zone_ids` JSON NULL COMMENT 'IDs de zones souhaitées (tableau JSON)' AFTER `desired_location`;

-- Surface min/max
ALTER TABLE `leads`
  ADD COLUMN `surface_min` SMALLINT UNSIGNED NULL COMMENT 'Surface min souhaitée (m²)' AFTER `desired_surface`,
  ADD COLUMN `surface_max` SMALLINT UNSIGNED NULL COMMENT 'Surface max souhaitée (m²)' AFTER `surface_min`;

-- Caractéristiques souhaitées
ALTER TABLE `leads`
  ADD COLUMN `rooms_min`      TINYINT UNSIGNED NULL COMMENT 'Nombre de pièces min' AFTER `surface_max`,
  ADD COLUMN `bedrooms_min`   TINYINT UNSIGNED NULL COMMENT 'Nombre de chambres min' AFTER `rooms_min`,
  ADD COLUMN `bathrooms_min`  TINYINT UNSIGNED NULL COMMENT 'Nombre de SDB min' AFTER `bedrooms_min`,
  ADD COLUMN `floor_min`      TINYINT NULL COMMENT 'Étage min souhaité (0=RDC, -1=indifférent)' AFTER `bathrooms_min`,
  ADD COLUMN `floor_max`      TINYINT NULL COMMENT 'Étage max souhaité' AFTER `floor_min`;

-- Options souhaitées (booléens)
ALTER TABLE `leads`
  ADD COLUMN `wants_parking`   TINYINT(1) NULL COMMENT 'Parking souhaité' AFTER `floor_max`,
  ADD COLUMN `wants_elevator`  TINYINT(1) NULL COMMENT 'Ascenseur souhaité' AFTER `wants_parking`,
  ADD COLUMN `wants_garden`    TINYINT(1) NULL COMMENT 'Jardin souhaité' AFTER `wants_elevator`,
  ADD COLUMN `wants_pool`      TINYINT(1) NULL COMMENT 'Piscine souhaitée' AFTER `wants_garden`,
  ADD COLUMN `wants_terrace`   TINYINT(1) NULL COMMENT 'Terrasse souhaitée' AFTER `wants_pool`;

-- État de construction souhaité
ALTER TABLE `leads`
  ADD COLUMN `construction_state` ENUM('new','good','to_refresh','to_renovate','any') NULL DEFAULT 'any'
    COMMENT 'État du bien souhaité' AFTER `wants_terrace`;

-- Meublé
ALTER TABLE `leads`
  ADD COLUMN `furnished` ENUM('furnished','unfurnished','any') NULL DEFAULT 'any'
    COMMENT 'Préférence meublé' AFTER `construction_state`;

-- Orientation souhaitée
ALTER TABLE `leads`
  ADD COLUMN `orientation` VARCHAR(50) NULL COMMENT 'Orientation souhaitée (nord, sud, est, ouest)' AFTER `furnished`;

-- Date limite d'acquisition souhaitée
ALTER TABLE `leads`
  ADD COLUMN `target_date` DATE NULL COMMENT 'Date limite d\'acquisition souhaitée' AFTER `orientation`;

SET FOREIGN_KEY_CHECKS = 1;
