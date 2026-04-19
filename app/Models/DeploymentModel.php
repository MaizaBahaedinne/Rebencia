<?php

namespace App\Models;

use CodeIgniter\Model;

class DeploymentModel extends Model
{
    protected $table         = 'deployments';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'commit_hash', 'commit_message', 'branch',
        'deployed_by', 'status', 'output',
        'started_at', 'completed_at', 'created_at',
    ];

    /**
     * Retourne les N derniers déploiements.
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->db->table('deployments d')
            ->select('d.*, CONCAT(u.first_name, " ", u.last_name) AS deployer')
            ->join('users u', 'u.id = d.deployed_by', 'left')
            ->orderBy('d.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Crée un enregistrement de déploiement et retourne son ID.
     */
    public function startDeployment(int $userId): int
    {
        $this->insert([
            'deployed_by' => $userId,
            'status'      => 'running',
            'started_at'  => date('Y-m-d H:i:s'),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->getInsertID();
    }

    /**
     * Met à jour un déploiement avec le résultat.
     */
    public function completeDeployment(int $id, string $status, string $output, string $commitHash = '', string $commitMessage = ''): void
    {
        $this->update($id, [
            'status'         => $status,
            'output'         => $output,
            'commit_hash'    => $commitHash,
            'commit_message' => $commitMessage,
            'completed_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
