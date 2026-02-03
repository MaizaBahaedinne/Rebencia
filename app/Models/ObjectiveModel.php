<?php

namespace App\Models;

use CodeIgniter\Model;

class ObjectiveModel extends Model
{
    protected $table = 'agent_objectives';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'period', 'year', 'month',
        'target_properties', 'target_clients', 'target_deals', 'target_revenue',
        'achieved_properties', 'achieved_clients', 'achieved_deals', 'achieved_revenue',
        'bonus_earned', 'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get current month objective for user
     */
    public function getCurrentObjective($userId)
    {
        return $this->where('user_id', $userId)
            ->where('year', date('Y'))
            ->where('month', date('m'))
            ->first();
    }

    /**
     * Update achievements
     */
    public function updateAchievements($userId)
    {
        $objective = $this->getCurrentObjective($userId);
        
        if (!$objective) {
            return false;
        }

        $db = \Config\Database::connect();
        
        // Count achievements for current month
        $stats = $db->query(
            "SELECT 
                (SELECT COUNT(*) FROM properties WHERE user_id = ? AND MONTH(created_at) = ? AND YEAR(created_at) = ?) as properties,
                (SELECT COUNT(*) FROM clients WHERE user_id = ? AND MONTH(created_at) = ? AND YEAR(created_at) = ?) as clients,
                (SELECT COUNT(*) FROM transactions WHERE user_id = ? AND status IN ('signed', 'completed') AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?) as deals,
                (SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE user_id = ? AND status IN ('signed', 'completed') AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?) as revenue",
            [
                $userId, date('m'), date('Y'),
                $userId, date('m'), date('Y'),
                $userId, date('m'), date('Y'),
                $userId, date('m'), date('Y')
            ]
        )->getRow();

        // Update objective
        $this->update($objective['id'], [
            'achieved_properties' => $stats->properties,
            'achieved_clients' => $stats->clients,
            'achieved_deals' => $stats->deals,
            'achieved_revenue' => $stats->revenue
        ]);

        // Calculate bonus if all targets met
        $bonus = 0;
        if ($stats->properties >= $objective['target_properties'] &&
            $stats->clients >= $objective['target_clients'] &&
            $stats->deals >= $objective['target_deals'] &&
            $stats->revenue >= $objective['target_revenue']) {
            
            $bonus = $objective['target_revenue'] * 0.1; // 10% bonus
            $this->update($objective['id'], [
                'bonus_earned' => $bonus,
                'status' => 'completed'
            ]);
        }

        return true;
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard($year = null, $month = null)
    {
        $year = $year ?: date('Y');
        $month = $month ?: date('m');

        return $this->select('agent_objectives.*, CONCAT(users.first_name, " ", users.last_name) as agent_name')
            ->join('users', 'users.id = agent_objectives.user_id')
            ->where('year', $year)
            ->where('month', $month)
            ->orderBy('achieved_revenue', 'DESC')
            ->findAll();
    }

    /**
     * Get progress percentage
     */
    public function getProgress($objective)
    {
        $progress = [
            'properties' => $objective['target_properties'] > 0 ? ($objective['achieved_properties'] / $objective['target_properties']) * 100 : 0,
            'clients' => $objective['target_clients'] > 0 ? ($objective['achieved_clients'] / $objective['target_clients']) * 100 : 0,
            'deals' => $objective['target_deals'] > 0 ? ($objective['achieved_deals'] / $objective['target_deals']) * 100 : 0,
            'revenue' => $objective['target_revenue'] > 0 ? ($objective['achieved_revenue'] / $objective['target_revenue']) * 100 : 0,
        ];

        $progress['overall'] = array_sum($progress) / 4;

        return $progress;
    }
}
