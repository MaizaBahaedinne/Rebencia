<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SyncObjectivesCA extends BaseCommand
{
    protected $group       = 'Objectives';
    protected $name        = 'sync:objectives-ca';
    protected $description = 'Synchronise les chiffres d\'affaires réalisés avec les commissions payées';
    protected $usage       = 'sync:objectives-ca [--period=YYYY-MM] [--user-id=N] [--agency-id=N]';
    protected $arguments   = [];
    protected $options     = [
        'period'    => 'Période au format YYYY-MM (défaut: mois courant)',
        'user-id'   => 'ID de l\'utilisateur pour sync personnel',
        'agency-id' => 'ID de l\'agence pour sync agence',
    ];

    public function run(array $params = [])
    {
        $objectiveModel = model('ObjectiveModel');

        // Parse options from command line
        $period = $this->getPeriodFromParams($params) ?? date('Y-m');
        $userId = $this->getUserIdFromParams($params);
        $agencyId = $this->getAgencyIdFromParams($params);

        try {
            CLI::write('Synchronisation des objectifs CA pour la période: ' . $period, 'green');

            $objectiveModel->syncCAFromPaidCommissions(
                $userId ? (int) $userId : null,
                $agencyId ? (int) $agencyId : null,
                $period
            );

            CLI::write('✓ Synchronisation terminée avec succès!', 'green');
            return 0;
        } catch (\Exception $e) {
            CLI::error('Erreur: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Extract period from command line arguments
     */
    private function getPeriodFromParams(array $params): ?string
    {
        foreach ($params as $param) {
            if (strpos($param, '--period=') === 0) {
                return substr($param, 9);
            }
        }
        return null;
    }

    /**
     * Extract user-id from command line arguments
     */
    private function getUserIdFromParams(array $params): ?string
    {
        foreach ($params as $param) {
            if (strpos($param, '--user-id=') === 0) {
                return substr($param, 10);
            }
        }
        return null;
    }

    /**
     * Extract agency-id from command line arguments
     */
    private function getAgencyIdFromParams(array $params): ?string
    {
        foreach ($params as $param) {
            if (strpos($param, '--agency-id=') === 0) {
                return substr($param, 12);
            }
        }
        return null;
    }
}
