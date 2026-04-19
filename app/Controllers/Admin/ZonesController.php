<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ZoneModel;

/**
 * ZonesController – Gestion des zones géographiques.
 *
 * Hiérarchie : Pays → Région / État (optionnel) → Ville (+ code postal) → Quartier
 */
class ZonesController extends BaseController
{
    protected ZoneModel $model;
    protected \CodeIgniter\Database\BaseConnection $db;

    private const VALID_TYPES = ['pays', 'region', 'ville', 'quartier'];

    public function __construct()
    {
        $this->model = new ZoneModel();
        $this->db    = \Config\Database::connect();
    }

    // ── LISTE ────────────────────────────────────────────────────────

    public function index(): string
    {
        $this->requirePermission('zones.view');

        $activeTab = $this->request->getGet('tab') ?? 'pays';
        if (! in_array($activeTab, self::VALID_TYPES)) {
            $activeTab = 'pays';
        }

        $search = trim((string) $this->request->getGet('search'));
        $page   = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit  = 300;
        $offset = ($page - 1) * $limit;

        // On ne charge que le tab actif pour éviter de rapatrier des milliers
        // de lignes en une seule requête (ex: 4868 quartiers).
        $filters = [
            'type'   => $activeTab,
            'search' => $search ?: null,
            'limit'  => $limit,
            'offset' => $offset,
        ];

        return $this->render('admin/zones/index', [
            'page_title'  => 'Zones géographiques',
            'counts'      => $this->model->countByType(),
            'active_list' => $this->model->getWithParent($filters),
            'activeTab'   => $activeTab,
            'typeMeta'    => ZoneModel::TYPE_META,
            'search'      => $search,
            'page'        => $page,
            'limit'       => $limit,
        ]);
    }

    // ── CRÉATION ─────────────────────────────────────────────────────

    public function create(string $type = 'pays'): string
    {
        $this->requirePermission('zones.create');

        if (! in_array($type, self::VALID_TYPES)) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Type de zone invalide.');
        }

        return $this->render('admin/zones/form', [
            'page_title'   => 'Ajouter : ' . ZoneModel::TYPE_META[$type]['label'],
            'zone'         => [],
            'zoneType'     => $type,
            'pays_list'    => $this->model->getByType('pays'),
            'preselect'    => ['pays_id' => null, 'region_id' => null, 'ville_id' => null],
            'regions_list' => [],
            'villes_list'  => [],
        ]);
    }

    // ── STORE ────────────────────────────────────────────────────────

    public function store()
    {
        $this->requirePermission('zones.create');

        $type = $this->request->getPost('type');
        if (! in_array($type, self::VALID_TYPES)) {
            return redirect()->back()->withInput()->with('error', 'Type de zone invalide.');
        }

        if (! $this->validate($this->validationRules($type))) {
            return redirect()->back()->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->resolveParentId($type);

        $id = $this->model->insert([
            'type'      => $type,
            'name'      => trim($this->request->getPost('name')),
            'code'      => $this->request->getPost('code') ? trim($this->request->getPost('code')) : null,
            'parent_id' => $parentId,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        $this->log->activity('create', 'zones', 'zone', $id,
            'Zone créée : ' . $this->request->getPost('name'));

        return redirect()->to(base_url('admin/zones/' . $id))
                         ->with('success', ZoneModel::TYPE_META[$type]['label'] . ' créé(e) avec succès.');
    }

    // ── DÉTAIL ───────────────────────────────────────────────────────

    public function show(int $id): string
    {
        $this->requirePermission('zones.view');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        return $this->render('admin/zones/show', [
            'page_title' => $zone['name'],
            'zone'       => $zone,
            'chain'      => $this->model->getParentChain($zone),
            'children'   => $this->model->getChildren($id),
            'typeMeta'   => ZoneModel::TYPE_META,
        ]);
    }

    // ── ÉDITION ──────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        $chain    = $this->model->getParentChain($zone);
        $paysId   = $chain['pays']   ? (int) $chain['pays']['id']   : null;
        $regionId = $chain['region'] ? (int) $chain['region']['id'] : null;
        $villeId  = $chain['ville']  ? (int) $chain['ville']['id']  : null;

        return $this->render('admin/zones/form', [
            'page_title'   => 'Modifier : ' . $zone['name'],
            'zone'         => $zone,
            'zoneType'     => $zone['type'],
            'pays_list'    => $this->model->getByType('pays'),
            'preselect'    => ['pays_id' => $paysId, 'region_id' => $regionId, 'ville_id' => $villeId],
            'regions_list' => $paysId   ? $this->model->getByParent($paysId)   : [],
            'villes_list'  => $regionId ? $this->model->getByParent($regionId) : ($paysId ? $this->model->getByParent($paysId) : []),
        ]);
    }

    public function update(int $id)
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        $type = $this->request->getPost('type');
        if (! in_array($type, self::VALID_TYPES)) {
            return redirect()->back()->withInput()->with('error', 'Type de zone invalide.');
        }

        if (! $this->validate($this->validationRules($type))) {
            return redirect()->back()->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->resolveParentId($type);

        if ($parentId === $id) {
            return redirect()->back()->withInput()
                             ->with('error', 'Une zone ne peut pas être son propre parent.');
        }

        $this->model->update($id, [
            'name'      => trim($this->request->getPost('name')),
            'code'      => $this->request->getPost('code') ? trim($this->request->getPost('code')) : null,
            'parent_id' => $parentId,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        $this->log->activity('update', 'zones', 'zone', $id,
            'Zone modifiée : ' . $this->request->getPost('name'));

        return redirect()->to(base_url('admin/zones/' . $id))
                         ->with('success', 'Zone mise à jour.');
    }

    // ── TOGGLE STATUT ────────────────────────────────────────────────

    public function toggleStatus(int $id)
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable.');
        }

        $this->model->update($id, ['is_active' => $zone['is_active'] ? 0 : 1]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    // ── SUPPRESSION ──────────────────────────────────────────────────

    public function delete(int $id)
    {
        $this->requirePermission('zones.delete');

        $zone = $this->model->find($id);
        if (! $zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable.');
        }

        $children = $this->model->getChildren($id);
        if (! empty($children)) {
            return redirect()->back()
                             ->with('error', 'Impossible de supprimer : cette zone possède des sous-zones.');
        }

        $this->model->delete($id);

        $this->log->activity('delete', 'zones', 'zone', $id,
            'Zone supprimée : ' . $zone['name']);

        return redirect()->to(base_url('admin/zones?tab=' . $zone['type']))
                         ->with('success', 'Zone supprimée.');
    }

    // ── AJAX ─────────────────────────────────────────────────────────

    public function childrenJson(int $parentId)
    {
        $this->requirePermission('zones.view');
        return $this->json($this->model->getByParent($parentId));
    }

    // ── IMPORT JSON ──────────────────────────────────────────────────

    public function importPage(): string
    {
        $this->requirePermission('zones.create');

        return $this->render('admin/zones/import', [
            'page_title' => 'Importer des zones (JSON)',
            'counts'     => $this->model->countByType(),
        ]);
    }

    /**
     * Traitement du fichier JSON uploadé.
     *
     * Formats JSON supportés :
     *
     * Format A — objet indexé par gouvernorat (format migration interne) :
     * {
     *   "Ariana": [
     *     ["Ariana Ville", "Cite El Intissar 1", "2091", "2058"],
     *     ...
     *   ]
     * }
     *
     * Format B — tableau d'objets avec clés explicites :
     * [
     *   { "gouvernorat": "Ariana", "delegation": "Ariana Ville",
     *     "localite": "Cite El Intissar 1", "cp": "2091" },
     *   ...
     * ]
     *
     * Format C — objet avec clé "gouvernorats" :
     * {
     *   "pays": "Tunisie",
     *   "gouvernorats": [
     *     { "nom": "Ariana", "delegations": [
     *         { "nom": "Ariana Ville", "cp": "2058", "localites": [
     *             { "nom": "Cite El Intissar 1", "cp": "2091" }, ...
     *         ]}
     *     ]}
     *   ]
     * }
     */
    public function import()
    {
        $this->requirePermission('zones.create');

        $file = $this->request->getFile('json_file');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Fichier invalide ou absent.');
        }

        if (strtolower($file->getClientExtension()) !== 'json') {
            return redirect()->back()->with('error', 'Le fichier doit avoir l\'extension .json.');
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Fichier trop volumineux (max 10 Mo).');
        }

        $raw = file_get_contents($file->getTempName());
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->with('error', 'JSON invalide : ' . json_last_error_msg());
        }

        // ── Normaliser vers Format A (govName => [[del,loc,cp,cpVille], ...])
        $normalized = $this->normalizeJson($data);
        if ($normalized === null) {
            return redirect()->back()->with('error', 'Format JSON non reconnu. Consultez la documentation.');
        }

        $paysName = $this->request->getPost('pays_name') ?: 'Tunisie';
        $paysName = trim(strip_tags($paysName));
        if ($paysName === '') { $paysName = 'Tunisie'; }

        $stats = $this->runImport($normalized, $paysName);

        $this->log->activity('import', 'zones', 'json', null,
            "Import JSON : {$stats['regions']} régions, {$stats['villes']} villes, {$stats['quartiers']} quartiers.");

        return redirect()->to(base_url('admin/zones/import'))
            ->with('success', sprintf(
                'Import terminé : %d région(s), %d ville(s), %d quartier(s) ajouté(s).',
                $stats['regions'], $stats['villes'], $stats['quartiers']
            ));
    }

    /**
     * Purge toutes les zones d'un pays (soft-delete ou suppression physique).
     */
    public function purge()
    {
        $this->requirePermission('zones.delete');

        $paysName = trim(strip_tags((string) $this->request->getPost('pays_name')));
        $mode     = $this->request->getPost('mode') === 'hard' ? 'hard' : 'soft';

        if ($paysName === '') {
            return redirect()->back()->with('error', 'Nom du pays manquant.');
        }

        $paysRow = $this->db->table('zones')
            ->where('type', 'pays')
            ->where('name', $paysName)
            ->where('deleted_at', null)
            ->get()->getRowArray();

        if (! $paysRow) {
            return redirect()->back()->with('error', "Pays « {$paysName} » introuvable.");
        }

        $paysId = (int) $paysRow['id'];
        $now    = date('Y-m-d H:i:s');

        if ($mode === 'hard') {
            // Suppression physique en cascade
            $this->db->query("DELETE q FROM zones q
                INNER JOIN zones v ON v.id = q.parent_id
                INNER JOIN zones r ON r.id = v.parent_id
                WHERE q.type = 'quartier' AND r.parent_id = {$paysId}");
            $this->db->query("DELETE v FROM zones v
                INNER JOIN zones r ON r.id = v.parent_id
                WHERE v.type = 'ville' AND r.parent_id = {$paysId}");
            $this->db->table('zones')->where('type', 'region')->where('parent_id', $paysId)->delete();
            $this->db->table('zones')->where('id', $paysId)->delete();
            $msg = "Toutes les zones du pays « {$paysName} » ont été supprimées définitivement.";
        } else {
            // Soft-delete : on pose deleted_at
            $this->db->query("UPDATE zones q
                INNER JOIN zones v ON v.id = q.parent_id
                INNER JOIN zones r ON r.id = v.parent_id
                SET q.deleted_at = '{$now}'
                WHERE q.type = 'quartier' AND r.parent_id = {$paysId} AND q.deleted_at IS NULL");
            $this->db->query("UPDATE zones v
                INNER JOIN zones r ON r.id = v.parent_id
                SET v.deleted_at = '{$now}'
                WHERE v.type = 'ville' AND r.parent_id = {$paysId} AND v.deleted_at IS NULL");
            $this->db->table('zones')
                ->where('type', 'region')->where('parent_id', $paysId)->where('deleted_at', null)
                ->set('deleted_at', $now)->update();
            $this->db->table('zones')
                ->where('id', $paysId)->where('deleted_at', null)
                ->set('deleted_at', $now)->update();
            $msg = "Toutes les zones du pays « {$paysName} » ont été archivées (soft-delete).";
        }

        $this->log->activity('purge', 'zones', 'pays', $paysId, $msg);

        return redirect()->to(base_url('admin/zones/import'))->with('success', $msg);
    }

    // ── HELPERS IMPORT ───────────────────────────────────────────────

    /**
     * Normalise n'importe quel format JSON vers :
     * [ "GovName" => [ ["delegation","localite","cp_localite","cp_ville"], ... ], ... ]
     * Retourne null si le format n'est pas reconnu.
     */
    private function normalizeJson(mixed $data): ?array
    {
        // Format A : { "GovName": [["del","loc","cp","cpv"], ...] }
        if (is_array($data) && ! array_is_list($data)) {
            $first = reset($data);
            if (is_array($first) && isset($first[0]) && is_array($first[0])) {
                return $data; // déjà normalisé
            }
        }

        // Format B : liste plate d'objets { gouvernorat, delegation, localite, cp }
        if (is_array($data) && array_is_list($data) && isset($data[0]['gouvernorat'])) {
            $out = [];
            foreach ($data as $row) {
                $gov = trim($row['gouvernorat'] ?? '');
                $del = trim($row['delegation']  ?? $row['ville'] ?? '');
                $loc = trim($row['localite']    ?? $row['quartier'] ?? $del);
                $cp  = trim((string) ($row['cp'] ?? $row['code_postal'] ?? ''));
                if ($gov === '' || $del === '') { continue; }
                $out[$gov][] = [$del, $loc, $cp, $cp];
            }
            // Calculer cp_ville = cp le plus fréquent de chaque délégation
            foreach ($out as $gov => &$entries) {
                $cpFreq = [];
                foreach ($entries as $e) { $cpFreq[$e[0]][$e[2]] = ($cpFreq[$e[0]][$e[2]] ?? 0) + 1; }
                foreach ($entries as &$e) {
                    arsort($cpFreq[$e[0]]);
                    $e[3] = (string) array_key_first($cpFreq[$e[0]]);
                }
            }
            unset($entries, $e);
            return $out ?: null;
        }

        // Format C : { pays, gouvernorats: [{nom, delegations:[{nom,cp,localites:[{nom,cp}]}]}] }
        if (isset($data['gouvernorats']) && is_array($data['gouvernorats'])) {
            $out = [];
            foreach ($data['gouvernorats'] as $gov) {
                $govName = trim($gov['nom'] ?? $gov['name'] ?? '');
                if ($govName === '') { continue; }
                foreach ($gov['delegations'] ?? [] as $del) {
                    $delName = trim($del['nom'] ?? $del['name'] ?? '');
                    $cpVille = trim((string) ($del['cp'] ?? ''));
                    if ($delName === '') { continue; }
                    foreach ($del['localites'] ?? [['nom' => $delName, 'cp' => $cpVille]] as $loc) {
                        $locName = trim($loc['nom'] ?? $loc['name'] ?? $delName);
                        $cpLoc   = trim((string) ($loc['cp'] ?? $cpVille));
                        $out[$govName][] = [$delName, $locName, $cpLoc, $cpVille];
                    }
                }
            }
            return $out ?: null;
        }

        return null;
    }

    /**
     * Exécute l'import normalisé en base par bulk INSERT IGNORE.
     * Retourne ['regions'=>int, 'villes'=>int, 'quartiers'=>int]
     */
    private function runImport(array $normalized, string $paysName): array
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        // ── 1. Pays ──────────────────────────────────────────────────
        $paysRow = $db->table('zones')
            ->where('type', 'pays')->where('name', $paysName)->where('deleted_at', null)
            ->get()->getRowArray();

        if (! $paysRow) {
            $db->table('zones')->insert([
                'type' => 'pays', 'name' => $paysName, 'code' => null,
                'parent_id' => null, 'is_active' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $paysId = (int) $db->insertID();
        } else {
            $paysId = (int) $paysRow['id'];
        }

        // ── 2. Régions (INSERT IGNORE) ───────────────────────────────
        $govNames  = array_keys($normalized);
        $regionBatch = array_map(fn($g) => [
            'type' => 'region', 'name' => $g, 'code' => null,
            'parent_id' => $paysId, 'is_active' => 1,
            'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null,
        ], $govNames);

        $this->bulkInsertIgnore($db, 'zones', $regionBatch);

        // Relire les IDs des régions
        $regionRows = $db->table('zones')
            ->select('id, name')
            ->where('type', 'region')->where('parent_id', $paysId)->where('deleted_at', null)
            ->get()->getResultArray();
        $regionIds = array_column($regionRows, 'id', 'name');

        // ── 3. Villes (INSERT IGNORE) ────────────────────────────────
        $villeBatch = [];
        foreach ($normalized as $govName => $entries) {
            $govId = $regionIds[$govName] ?? null;
            if (! $govId) { continue; }
            $seen = [];
            foreach ($entries as [$del, , , $cpVille]) {
                if (isset($seen[$del])) { continue; }
                $seen[$del] = true;
                $villeBatch[] = [
                    'type' => 'ville', 'name' => $del, 'code' => $cpVille ?: null,
                    'parent_id' => (int) $govId, 'is_active' => 1,
                    'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null,
                ];
            }
        }
        $this->bulkInsertIgnore($db, 'zones', $villeBatch);

        // Relire les IDs des villes
        $villeRows = $db->table('zones')
            ->select('id, name, parent_id')
            ->where('type', 'ville')->where('deleted_at', null)
            ->get()->getResultArray();
        $villeIds = []; // "{parentId}:{name}" => id
        foreach ($villeRows as $v) {
            $villeIds[$v['parent_id'] . ':' . $v['name']] = (int) $v['id'];
        }

        // ── 4. Quartiers par lots de 500 (INSERT IGNORE) ─────────────
        $quartierBatch = [];
        foreach ($normalized as $govName => $entries) {
            $govId = $regionIds[$govName] ?? null;
            if (! $govId) { continue; }
            foreach ($entries as [$del, $loc, $cpLoc]) {
                $villeKey = $govId . ':' . $del;
                $villeId  = $villeIds[$villeKey] ?? null;
                if (! $villeId) { continue; }
                $quartierBatch[] = [
                    'type' => 'quartier', 'name' => $loc, 'code' => $cpLoc ?: null,
                    'parent_id' => $villeId, 'is_active' => 1,
                    'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null,
                ];
                if (count($quartierBatch) >= 500) {
                    $this->bulkInsertIgnore($db, 'zones', $quartierBatch);
                    $quartierBatch = [];
                }
            }
        }
        if (! empty($quartierBatch)) {
            $this->bulkInsertIgnore($db, 'zones', $quartierBatch);
        }

        return [
            'regions'   => count($regionBatch),
            'villes'    => count($villeBatch),
            'quartiers' => count($normalized) > 0
                ? array_sum(array_map(fn($e) => count($e), $normalized))
                : 0,
        ];
    }

    /**
     * INSERT IGNORE en bulk — idempotent, évite les doublons.
     */
    private function bulkInsertIgnore(\CodeIgniter\Database\BaseConnection $db, string $table, array $rows): void
    {
        if (empty($rows)) { return; }

        $columns = array_keys($rows[0]);
        $colsSql = implode(', ', array_map(fn($c) => "`{$c}`", $columns));
        $placeholders = [];
        $bindings = [];
        foreach ($rows as $row) {
            $placeholders[] = '(' . implode(', ', array_fill(0, count($row), '?')) . ')';
            foreach ($row as $val) { $bindings[] = $val; }
        }
        $db->query("INSERT IGNORE INTO `{$table}` ({$colsSql}) VALUES " . implode(', ', $placeholders), $bindings);
    }

    // ── HELPERS PRIVÉS ───────────────────────────────────────────────

    /**
     * Résout le parent_id selon le type de zone.
     *   pays     → null
     *   region   → pays_id
     *   ville    → region_id si sélectionné, sinon pays_id
     *   quartier → ville_id
     */
    private function resolveParentId(string $type): ?int
    {
        return match ($type) {
            'pays'     => null,
            'region'   => $this->request->getPost('pays_id')
                            ? (int) $this->request->getPost('pays_id')
                            : null,
            'ville'    => $this->request->getPost('region_id')
                            ? (int) $this->request->getPost('region_id')
                            : ($this->request->getPost('pays_id')
                                ? (int) $this->request->getPost('pays_id')
                                : null),
            'quartier' => $this->request->getPost('ville_id')
                            ? (int) $this->request->getPost('ville_id')
                            : null,
            default    => null,
        };
    }

    private function validationRules(string $type): array
    {
        $rules = [
            'type' => 'required|in_list[pays,region,ville,quartier]',
            'name' => 'required|min_length[1]|max_length[150]',
        ];

        if (in_array($type, ['region', 'ville', 'quartier'])) {
            $rules['pays_id'] = 'required|is_natural_no_zero';
        }
        if ($type === 'quartier') {
            $rules['ville_id'] = 'required|is_natural_no_zero';
        }

        return $rules;
    }
}
