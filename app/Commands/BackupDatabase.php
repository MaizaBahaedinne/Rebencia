<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BackupDatabase extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:backup';
    protected $description = 'Create a database backup';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $dbName = $db->database;
        
        // Create backups directory if not exists
        $backupDir = WRITEPATH . 'backups/database';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Generate filename with timestamp
        $filename = $dbName . '_' . date('Y-m-d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        // MySQL dump command
        $host = $db->hostname;
        $username = $db->username;
        $password = $db->password;

        $command = "mysqldump -h {$host} -u {$username}";
        if (!empty($password)) {
            $command .= " -p'{$password}'";
        }
        $command .= " {$dbName} > {$filepath}";

        CLI::write('Creating database backup...', 'yellow');
        
        exec($command, $output, $return);

        if ($return === 0 && file_exists($filepath)) {
            $size = filesize($filepath);
            CLI::write('Backup created successfully!', 'green');
            CLI::write("File: {$filename}", 'white');
            CLI::write("Size: " . number_format($size / 1024, 2) . " KB", 'white');

            // Delete old backups (keep last 30 days)
            $this->cleanOldBackups($backupDir, 30);

            return 0;
        } else {
            CLI::error('Backup failed!');
            return 1;
        }
    }

    /**
     * Delete backups older than X days
     */
    private function cleanOldBackups($dir, $days)
    {
        $files = glob($dir . '/*.sql');
        $now = time();
        $deleted = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                    unlink($file);
                    $deleted++;
                }
            }
        }

        if ($deleted > 0) {
            CLI::write("Cleaned {$deleted} old backup(s)", 'cyan');
        }
    }
}
