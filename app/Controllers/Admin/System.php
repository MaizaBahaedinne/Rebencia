<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class System extends BaseController
{
    protected $auditLogger;

    public function __construct()
    {
        $this->auditLogger = new \App\Libraries\AuditLogger();
    }

    /**
     * System dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Système & Administration',
            'page_title' => 'Système',
            'recentLogs' => $this->auditLogger->getRecentLogs(100),
            'backups' => $this->getBackupsList()
        ];

        return view('admin/system/index', $data);
    }

    /**
     * Create manual backup
     */
    public function createBackup()
    {
        $command = ROOTPATH . 'spark db:backup';
        exec($command . ' 2>&1', $output, $return);

        if ($return === 0) {
            return redirect()->back()->with('success', 'Backup créé avec succès');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de la création du backup');
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup($filename)
    {
        $filepath = WRITEPATH . 'backups/database/' . $filename;

        if (!file_exists($filepath)) {
            return redirect()->back()->with('error', 'Fichier non trouvé');
        }

        return $this->response->download($filepath, null);
    }

    /**
     * Delete backup
     */
    public function deleteBackup($filename)
    {
        $filepath = WRITEPATH . 'backups/database/' . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
            return redirect()->back()->with('success', 'Backup supprimé');
        }

        return redirect()->back()->with('error', 'Fichier non trouvé');
    }

    /**
     * View audit logs
     */
    public function auditLogs()
    {
        $module = $this->request->getGet('module');
        $action = $this->request->getGet('action');
        $userId = $this->request->getGet('user_id');

        $db = \Config\Database::connect();
        $builder = $db->table('audit_logs')
            ->select('audit_logs.*, CONCAT(users.first_name, " ", users.last_name) as user_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC');

        if ($module) {
            $builder->where('audit_logs.module', $module);
        }
        if ($action) {
            $builder->where('audit_logs.action', $action);
        }
        if ($userId) {
            $builder->where('audit_logs.user_id', $userId);
        }

        $logs = $builder->paginate(50);
        $pager = $db->table('audit_logs')->pager;

        $data = [
            'title' => 'Journal d\'Audit',
            'page_title' => 'Audit Logs',
            'logs' => $logs,
            'pager' => $pager,
            'users' => model('UserModel')->findAll()
        ];

        return view('admin/system/audit_logs', $data);
    }

    /**
     * Get list of backups
     */
    private function getBackupsList()
    {
        $backupDir = WRITEPATH . 'backups/database';
        
        if (!is_dir($backupDir)) {
            return [];
        }

        $files = glob($backupDir . '/*.sql');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size' => filesize($file),
                'date' => filemtime($file)
            ];
        }

        // Sort by date desc
        usort($backups, function($a, $b) {
            return $b['date'] - $a['date'];
        });

        return $backups;
    }

    /**
     * System info
     */
    public function info()
    {
        $db = \Config\Database::connect();

        $data = [
            'title' => 'Informations Système',
            'page_title' => 'System Info',
            'phpVersion' => phpversion(),
            'ciVersion' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'dbVersion' => $db->getVersion(),
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'diskSpace' => disk_free_space('/'),
            'totalSpace' => disk_total_space('/')
        ];

        return view('admin/system/info', $data);
    }
}
