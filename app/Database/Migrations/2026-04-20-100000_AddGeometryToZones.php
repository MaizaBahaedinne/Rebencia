<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * Migration : Ajout de la colonne geometry sur la table zones.
 *
 * Stocke un GeoJSON (Feature/FeatureCollection ou Polygon) sérialisé en JSON.
 * Type TEXT pour compatibilité maximale ; on n'utilise pas GEOMETRY MySQL
 * pour éviter les contraintes SRID et rester portable.
 */
class AddGeometryToZones extends Migration
{
    public function up(): void
    {
        try {
            $this->db->query("
                ALTER TABLE `zones`
                ADD COLUMN `geometry` MEDIUMTEXT NULL DEFAULT NULL
                AFTER `is_active`
            ");
        } catch (DatabaseException $e) {
            if (str_contains($e->getMessage(), 'Duplicate column name')) {
                return; // déjà présente
            }
            throw $e;
        }
    }

    public function down(): void
    {
        try {
            $this->db->query("ALTER TABLE `zones` DROP COLUMN `geometry`");
        } catch (DatabaseException $e) {
            // ignore si absente
        }
    }
}
