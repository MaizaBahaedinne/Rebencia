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

        $period = $this->getOption('period') ?? date('Y-m');
        $userId = $this->getOption('user-id');
        $agencyId = $this->getOption('agency-id');

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
}
