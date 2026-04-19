<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration : Normalisation des anciens types de zones
 *
 * L'ancienne ENUM utilisait : governorate, city, region, district
 * Le nouveau modèle utilise : region, ville, region, quartier
 *
 * Correspondances :
 *   governorate → region
 *   city        → ville
 *   district    → quartier
 *   region      → region  (inchangé)
 *
 * Idempotente : un UPDATE sur 0 lignes ne provoque aucune erreur.
 */
class NormalizeZoneTypes extends Migration
{
    public function up(): void
    {
        $db = $this->db;

        // governorate → region
        $db->query("UPDATE `zones` SET `type` = 'region'   WHERE `type` = 'governorate'");
        // city → ville
        $db->query("UPDATE `zones` SET `type` = 'ville'    WHERE `type` = 'city'");
        // district → quartier
        $db->query("UPDATE `zones` SET `type` = 'quartier' WHERE `type` = 'district'");
    }

    public function down(): void
    {
        $db = $this->db;

        $db->query("UPDATE `zones` SET `type` = 'district'    WHERE `type` = 'quartier'");
        $db->query("UPDATE `zones` SET `type` = 'city'        WHERE `type` = 'ville'");
        $db->query("UPDATE `zones` SET `type` = 'governorate' WHERE `type` = 'region'");
    }
}
