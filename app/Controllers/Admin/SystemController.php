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

        $activityModel = new ActivityLogModel();
        $systemModel   = new SystemLogModel();

        $activityResult = $activityModel->getFiltered($filters, $perPage);
        $systemResult   = $systemModel->getFiltered($filters, $perPage);

        // Sélectionner les données et le total selon l'onglet actif
        if ($activeTab === 'system') {
            $result = $systemResult;
        } else {
            $result = $activityResult;
        }

        // Pagination manuelle simple
        $totalPages = max(1, (int) ceil($result['total'] / $perPage));
        $curPage    = (int) $result['page'];

        // Listes distinctes pour les filtres
        $db = \Config\Database::connect();
        $modules  = $db->table('activity_logs')->select('DISTINCT module')->where('module !=', '')->orderBy('module')->get()->getResultArray();
        $modules  = array_column($modules, 'module');
        $channels = $db->table('system_logs')->select('DISTINCT channel')->where('channel !=', '')->orderBy('channel')->get()->getResultArray();
        $channels = array_column($channels, 'channel');

        return $this->render('admin/system/logs', [
            'page_title'      => 'Logs système',
            'filters'         => $filters,
            'activeTab'       => $activeTab,
            'logs'            => $result['data'],
            'activityStats'   => ['total' => $activityResult['total']],
            'systemStats'     => ['total' => $systemResult['total']],
            'systemLevelStats'=> $systemModel->getLevelStats(),
            'users'           => (new UserModel())->getWithRole(),
            'modules'         => $modules,
            'channels'        => $channels,
            'curPage'         => $curPage,
            'totalPages'      => $totalPages,
            'total'           => $result['total'],
        ]);
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
