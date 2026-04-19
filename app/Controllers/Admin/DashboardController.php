<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PropertyModel;
use App\Models\LeadModel;
use App\Models\ActivityLogModel;

/**
 * DashboardController – Dashboard adapté par rôle.
 */
class DashboardController extends BaseController
{
    public function index(): string
    {
        $role = session()->get('user_role');

        $userModel     = new UserModel();
        $propertyModel = new PropertyModel();
        $leadModel     = new LeadModel();

        $userId = $this->auth->id();

        $data = [
            'page_title'    => 'Tableau de bord',
            'user_stats'    => $userModel->getStats(),
            'property_stats'=> $propertyModel->getStats(),
            'lead_stats'    => $leadModel->getStats(),
        ];

        switch ($role) {
            case 'director':
                // Directeur : vision globale + activité récente
                $activityModel = new ActivityLogModel();
                $data['recent_activity'] = $activityModel->getFiltered([], 10)['data'];
                $data['lead_pipeline']   = $leadModel->getPipeline();
                return $this->render('admin/dashboards/director', $data);

            case 'expert':
                // Expert : mes biens + mes performances
                $data['my_properties'] = $propertyModel->getFiltered(
                    ['agent_id' => $userId], 10
                )['data'];
                $data['lead_stats'] = $leadModel->getStats($userId);
                return $this->render('admin/dashboards/expert', $data);

            case 'coordinator':
                // Coordinateur : leads équipe + pipeline
                $data['lead_pipeline']   = $leadModel->getPipeline();
                $data['unassigned_leads'] = $leadModel->getFiltered(
                    ['assigned_to' => 0, 'status' => 'new'], 10
                )['data'];
                return $this->render('admin/dashboards/coordinator', $data);

            case 'collaborator':
            default:
                // Collaborateur : mes leads assignés + biens
                $data['my_leads']      = $leadModel->getFiltered(['assigned_to' => $userId], 10)['data'];
                $data['my_properties'] = $propertyModel->getFiltered(['agent_id' => $userId], 5)['data'];
                return $this->render('admin/dashboards/collaborator', $data);
        }
    }
}
