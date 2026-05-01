<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Models\SystemLogModel;
use App\Models\DeploymentModel;
use App\Models\UserModel;

/**
 * SystemController – Logs système + Deploy Git.
 */
class SystemController extends BaseController
{
    // ============================================================
    // LOGS SYSTÈME
    // ============================================================

    /** Page /admin/system/logs */
    public function logs(): string
    {
        $this->requirePermission('system.logs');

        $perPage   = 50;
        $activeTab = $this->request->getGet('tab') ?? 'activity';

        $filters = [
            'level'     => $this->request->getGet('level'),
            'channel'   => $this->request->getGet('channel'),
            'module'    => $this->request->getGet('module'),
            'user_id'   => $this->request->getGet('user_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
            'search'    => $this->request->getGet('search'),
            'page'      => $this->request->getGet('page') ?? 1,
            'tab'       => $activeTab,
        ];

        $activityModel  = new ActivityLogModel();
        $activityResult = $activityModel->getFiltered($filters, $perPage);

        // Onglet système → lecture des fichiers logs CI4
        if ($activeTab === 'system') {
            $systemResult     = $this->parseCI4Logs($filters, $perPage);
            $systemLevelStats = $this->parseCI4LevelStats();
        } else {
            $systemResult     = ['data' => [], 'total' => $this->countCI4LogLines(), 'page' => 1, 'perPage' => $perPage];
            $systemLevelStats = $this->parseCI4LevelStats();
        }

        $result     = $activeTab === 'system' ? $systemResult : $activityResult;
        $totalPages = max(1, (int) ceil($result['total'] / $perPage));
        $curPage    = (int) $result['page'];

        // Modules distincts pour filtre activité
        $db      = \Config\Database::connect();
        $modules = $db->table('activity_logs')->distinct()->select('module')
                      ->where('module !=', '')->orderBy('module')->get()->getResultArray();
        $modules = array_column($modules, 'module');

        // Fichiers de log CI4 disponibles (dates) pour filtre système
        $logFiles = [];
        foreach (glob(WRITEPATH . 'logs/log-*.log') as $f) {
            if (preg_match('/log-(\d{4}-\d{2}-\d{2})\.log$/', $f, $m)) {
                $logFiles[] = $m[1];
            }
        }
        rsort($logFiles);

        return $this->render('admin/system/logs', [
            'page_title'      => 'Logs système',
            'filters'         => $filters,
            'activeTab'       => $activeTab,
            'logs'            => $result['data'],
            'activityStats'   => ['total' => $activityResult['total']],
            'systemStats'     => ['total' => $systemResult['total']],
            'systemLevelStats'=> $systemLevelStats,
            'users'           => (new UserModel())->getWithRole(),
            'modules'         => $modules,
            'channels'        => $logFiles,
            'curPage'         => $curPage,
            'totalPages'      => $totalPages,
            'total'           => $result['total'],
        ]);
    }

    /** Parse les fichiers logs CI4 depuis writable/logs/ */
    private function parseCI4Logs(array $filters = [], int $perPage = 50): array
    {
        $levelMap = [
            'emergency' => 'critical', 'alert'   => 'critical', 'critical' => 'critical',
            'error'     => 'error',    'warning'  => 'warning',
            'notice'    => 'info',     'info'     => 'info',     'debug'    => 'debug',
        ];

        $files = glob(WRITEPATH . 'logs/log-*.log');
        if (empty($files)) {
            return ['data' => [], 'total' => 0, 'page' => 1, 'perPage' => $perPage];
        }
        rsort($files); // plus récent en premier

        $entries = [];

        foreach ($files as $filePath) {
            if (!preg_match('/log-(\d{4}-\d{2}-\d{2})\.log$/', $filePath, $m)) {
                continue;
            }
            $fileDate = $m[1];

            // Filtre par date du fichier
            if (!empty($filters['date_from']) && $fileDate < $filters['date_from']) continue;
            if (!empty($filters['date_to'])   && $fileDate > $filters['date_to'])   continue;
            // Filtre par fichier (channel = date)
            if (!empty($filters['channel']) && $fileDate !== $filters['channel']) continue;

            $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (! $lines) continue;

            foreach (array_reverse($lines) as $line) {
                if (! preg_match('/^(\w+) - (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) --> (.+)$/', $line, $p)) {
                    continue;
                }
                $level   = $levelMap[strtolower($p[1])] ?? 'info';
                $datetime = $p[2];
                $message  = $p[3];

                if (!empty($filters['level'])  && $level !== $filters['level'])            continue;
                if (!empty($filters['search']) && stripos($message, $filters['search']) === false) continue;

                $entries[] = [
                    'level'      => $level,
                    'channel'    => $fileDate,
                    'message'    => $message,
                    'created_at' => $datetime,
                    'context'    => null,
                ];
            }
        }

        $total  = count($entries);
        $page   = max(1, (int) ($filters['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        return [
            'data'    => array_slice($entries, $offset, $perPage),
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ];
    }

    /** Compte total de lignes dans les logs CI4 (pour le badge de l'onglet). */
    private function countCI4LogLines(): int
    {
        $count = 0;
        foreach (glob(WRITEPATH . 'logs/log-*.log') as $f) {
            $lines = @file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines) {
                foreach ($lines as $line) {
                    if (preg_match('/^\w+ - \d{4}-\d{2}-\d{2}/', $line)) $count++;
                }
            }
        }
        return $count;
    }

    /** Stats par niveau sur les 24 dernières heures depuis les fichiers CI4. */
    private function parseCI4LevelStats(): array
    {
        $levelMap = [
            'emergency' => 'critical', 'alert'   => 'critical', 'critical' => 'critical',
            'error'     => 'error',    'warning'  => 'warning',
            'notice'    => 'info',     'info'     => 'info',     'debug'    => 'debug',
        ];
        $since  = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $today  = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $stats  = [];

        foreach (glob(WRITEPATH . 'logs/log-*.log') as $f) {
            if (!preg_match('/log-(\d{4}-\d{2}-\d{2})\.log$/', $f, $m)) continue;
            if ($m[1] < $yesterday) continue; // skip old files

            $lines = @file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (! $lines) continue;

            foreach ($lines as $line) {
                if (!preg_match('/^(\w+) - (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) --> /', $line, $p)) continue;
                if ($p[2] < $since) continue;
                $lvl = $levelMap[strtolower($p[1])] ?? 'info';
                $stats[$lvl] = ($stats[$lvl] ?? 0) + 1;
            }
        }

        return $stats;
    }

    /** Export CSV des logs. */
    public function exportLogs()
    {
        $this->requirePermission('system.logs');

        $type = $this->request->getGet('type') ?? 'activity';
        $filters = [
            'user_id'   => $this->request->getGet('user_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
        ];

        if ($type === 'system') {
            $model = new SystemLogModel();
            $rows  = $model->getForExport($filters);
            $cols  = ['created_at', 'level', 'channel', 'message', 'url', 'ip_address', 'user_name'];
        } else {
            $model = new ActivityLogModel();
            $rows  = $model->getForExport($filters);
            $cols  = ['created_at', 'user_name', 'action', 'module', 'description', 'ip_address'];
        }

        $filename = "rebencia_{$type}_logs_" . date('Ymd_His') . '.csv';

        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');
        fputcsv($output, $cols);

        foreach ($rows as $row) {
            $line = [];
            foreach ($cols as $col) {
                $line[] = $row[$col] ?? '';
            }
            fputcsv($output, $line);
        }

        fclose($output);
        exit;
    }

    // ============================================================
    // DÉPLOIEMENT GIT
    // ============================================================

    /** Page /admin/system/deploy */
    public function deploy(): string
    {
        $this->requirePermission('system.deploy');

        $deployModel = new DeploymentModel();
        $gitInfo     = $this->getGitInfo();

        return $this->render('admin/system/deploy', [
            'page_title'   => 'Déploiement Git',
            'deployments'  => $deployModel->getRecent(15),
            'git_info'     => $gitInfo,
            'app_version'  => $this->getAppVersion(),
        ]);
    }

    /** Vide le cache CI4 (writable/cache/*). */
    public function clearCache()
    {
        $this->requirePermission('system.deploy');
        $count = 0;
        foreach (glob(WRITEPATH . 'cache/*') as $file) {
            if (is_file($file) && @unlink($file)) {
                $count++;
            }
        }
        return redirect()->to(base_url('admin/system/deploy'))
            ->with('success', "Cache vidé — {$count} fichier(s) supprimé(s).");
    }

    /** Exécute uniquement les migrations (POST). */
    public function runMigrate()
    {
        $this->requirePermission('system.deploy');

        try {
            $runner = \Config\Services::migrations();
            $runner->latest('default');
            // Vider le cache CI4 (routes, vues, etc.)
            foreach (glob(WRITEPATH . 'cache/*') as $file) {
                if (is_file($file)) @unlink($file);
            }
            return redirect()->to(base_url('admin/system/deploy'))
                ->with('success', 'Migrations appliquées + cache vidé.');
        } catch (\Throwable $e) {
            return redirect()->to(base_url('admin/system/deploy'))
                ->with('error', 'Erreur migration : ' . $e->getMessage());
        }
    }

    /** Exécute un git pull (POST). */
    public function gitPull()
    {
        $this->requirePermission('system.deploy');

        $deployModel = new DeploymentModel();
        $deployId    = $deployModel->startDeployment($this->auth->id());

        $rootPath = ROOTPATH;
        $output   = [];
        $return   = 0;

        // 1. git pull
        exec("cd " . escapeshellarg($rootPath) . " && git pull 2>&1", $output, $return);

        // 2. Migrations via MigrationRunner CI4 (pas d'exec, fonctionne sur tout hébergement)
        $migrateLog    = '';
        $migrateReturn = 0;
        if ($return === 0) {
            try {
                $runner = \Config\Services::migrations();
                $runner->latest('default');
                // Vider le cache CI4 (routes compilées, vues, etc.)
                foreach (glob(WRITEPATH . 'cache/*') as $file) {
                    if (is_file($file)) @unlink($file);
                }
                $migrateLog = 'Migrations appliquées + cache vidé.';
            } catch (\Throwable $e) {
                $migrateReturn = 1;
                $migrateLog    = 'ERREUR migration : ' . $e->getMessage();
            }
        }

        $outputStr  = "=== git pull ===\n" . implode("\n", $output);
        if ($migrateLog !== '') {
            $outputStr .= "\n\n=== php spark migrate ===\n" . $migrateLog;
        }

        $status = ($return === 0 && $migrateReturn === 0) ? 'success' : 'failed';

        // Récupérer le nouveau commit
        $commitHash    = '';
        $commitMessage = '';
        if ($return === 0) {
            exec("cd " . escapeshellarg($rootPath) . " && git log -1 --format='%H' 2>&1", $hashOut);
            exec("cd " . escapeshellarg($rootPath) . " && git log -1 --format='%s' 2>&1", $msgOut);
            $commitHash    = trim($hashOut[0] ?? '');
            $commitMessage = trim($msgOut[0] ?? '');
        }

        $deployModel->completeDeployment($deployId, $status, $outputStr, $commitHash, $commitMessage);

        $this->log->activity(
            'system.deploy.' . $status, 'system', 'deployment', $deployId,
            "Git pull : {$status}"
        );

        if ($return === 0 && $migrateReturn === 0) {
            return redirect()->to(base_url('admin/system/deploy'))
                ->with('success', 'Déploiement réussi — git pull + migrations appliquées')
                ->with('deploy_output', $outputStr)
                ->with('deploy_success', true);
        } elseif ($return !== 0) {
            return redirect()->to(base_url('admin/system/deploy'))
                ->with('error', 'Échec du git pull.')
                ->with('deploy_output', $outputStr)
                ->with('deploy_success', false);
        } else {
            return redirect()->to(base_url('admin/system/deploy'))
                ->with('error', 'Git pull réussi mais échec des migrations.')
                ->with('deploy_output', $outputStr)
                ->with('deploy_success', false);
        }
    }

    // --------------------------------------------------------
    // Helpers privés
    // --------------------------------------------------------

    private function getGitInfo(): array
    {
        $rootPath = ROOTPATH;
        $info     = ['branch' => 'N/A', 'commit' => 'N/A', 'message' => 'N/A', 'date' => 'N/A'];

        if (! function_exists('exec')) {
            return $info;
        }

        exec("cd " . escapeshellarg($rootPath) . " && git rev-parse --abbrev-ref HEAD 2>&1", $branch);
        exec("cd " . escapeshellarg($rootPath) . " && git log -1 --format='%h' 2>&1", $commit);
        exec("cd " . escapeshellarg($rootPath) . " && git log -1 --format='%s' 2>&1", $message);
        exec("cd " . escapeshellarg($rootPath) . " && git log -1 --format='%ci' 2>&1", $date);

        $info['branch']  = trim($branch[0]  ?? 'N/A');
        $info['commit']  = trim($commit[0]  ?? 'N/A');
        $info['message'] = trim($message[0] ?? 'N/A');
        $info['date']    = trim($date[0]    ?? 'N/A');

        return $info;
    }

    private function getAppVersion(): string
    {
        $composerFile = ROOTPATH . 'composer.json';
        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            return $composer['version'] ?? '1.0.0';
        }
        return '1.0.0';
    }
}
