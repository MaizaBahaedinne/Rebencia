<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration de rattrapage : insertion des quartiers Tunisie en bulk.
 *
 * La migration 140000_SeedTunisieZones a pu timeout sur le serveur de
 * production après avoir inséré les villes (261 délégations) sans avoir
 * finalisé les 4 868 quartiers (localités).
 *
 * Cette migration :
 *   1. Charge tous les IDs de villes existantes en 1 seule requête.
 *   2. Insère les quartiers par lots de 500 (INSERT IGNORE → idempotente).
 *   3. Ne touche pas aux pays / régions / villes déjà insérés.
 *
 * down() : supprime tous les quartiers rattachés à la hiérarchie Tunisie.
 */
class SeedTunisieQuartiers extends Migration
{
    private const BATCH_SIZE = 500;

    public function up(): void
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        // ── 1. Récupérer les IDs des gouvernorats ────────────────────
        $regionRows = $db->table('zones')
            ->select('id, name, parent_id')
            ->where('type', 'region')
            ->where('deleted_at', null)
            ->get()->getResultArray();

        // Repérer le pays Tunisie
        $paysRow = $db->table('zones')
            ->where('type', 'pays')
            ->where('name', 'Tunisie')
            ->where('deleted_at', null)
            ->get()->getRowArray();

        if (! $paysRow) {
            // La migration seed principale n'a pas tourné → on abandonne proprement.
            return;
        }

        $paysId = (int) $paysRow['id'];

        // Indexer les régions par nom
        $regionIds = []; // govName → id
        foreach ($regionRows as $r) {
            if ((int) $r['parent_id'] === $paysId) {
                $regionIds[$r['name']] = (int) $r['id'];
            }
        }

        // ── 2. Récupérer les IDs des villes ─────────────────────────
        $villeRows = $db->table('zones')
            ->select('id, name, parent_id')
            ->where('type', 'ville')
            ->where('deleted_at', null)
            ->get()->getResultArray();

        // Indexer ville par composite "govId:villeName"
        $villeIds = []; // "{govId}:{villeName}" → id
        foreach ($villeRows as $v) {
            $key = $v['parent_id'] . ':' . $v['name'];
            $villeIds[$key] = (int) $v['id'];
        }

        // ── 3. Construire les quartiers à insérer ─────────────────────
        $batch = [];

        foreach (SeedTunisieZones::DATA as $govName => $entries) {
            if (! isset($regionIds[$govName])) {
                continue; // gouvernorat absent → skip
            }
            $govId = $regionIds[$govName];

            foreach ($entries as [$del, $loc, $cp, /* cpVille */]) {
                $villeKey = $govId . ':' . $del;
                if (! isset($villeIds[$villeKey])) {
                    continue; // ville introuvable → skip
                }
                $villeId = $villeIds[$villeKey];

                $batch[] = [
                    'type'       => 'quartier',
                    'name'       => $loc,
                    'code'       => $cp ?: null,
                    'parent_id'  => $villeId,
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ];

                // Insérer par lots pour éviter le timeout
                if (count($batch) >= self::BATCH_SIZE) {
                    $this->insertBatch($db, $batch);
                    $batch = [];
                }
            }
        }

        // Dernier lot
        if (! empty($batch)) {
            $this->insertBatch($db, $batch);
        }
    }

    public function down(): void
    {
        $db = $this->db;

        $paysRow = $db->table('zones')
            ->where('type', 'pays')
            ->where('name', 'Tunisie')
            ->where('deleted_at', null)
            ->get()->getRowArray();

        if (! $paysRow) { return; }

        $paysId = (int) $paysRow['id'];

        // Supprimer uniquement les quartiers rattachés à la hiérarchie Tunisie
        $db->query("
            DELETE q FROM zones q
            INNER JOIN zones v ON v.id  = q.parent_id
            INNER JOIN zones r ON r.id  = v.parent_id
            WHERE q.type = 'quartier'
              AND r.parent_id = {$paysId}
        ");
    }

    /**
     * INSERT IGNORE pour ignorer les doublons (idempotence).
     */
    private function insertBatch(\CodeIgniter\Database\BaseConnection $db, array $rows): void
    {
        // Construire INSERT IGNORE manuellement car CI4 insertBatch n'a pas
        // d'option IGNORE native stable sur toutes les versions.
        $columns = array_keys($rows[0]);
        $colsSql = implode(', ', array_map(fn($c) => "`{$c}`", $columns));

        $placeholders = [];
        $bindings     = [];

        foreach ($rows as $row) {
            $placeholders[] = '(' . implode(', ', array_fill(0, count($row), '?')) . ')';
            foreach ($row as $val) {
                $bindings[] = $val;
            }
        }

        $sql = "INSERT IGNORE INTO `zones` ({$colsSql}) VALUES "
             . implode(', ', $placeholders);

        $db->query($sql, $bindings);
    }
}
