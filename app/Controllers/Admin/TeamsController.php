<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AgencyModel;
use App\Models\UserModel;

/**
 * TeamsController – Gestion des équipes par agence.
 *
 * Une équipe = une agence + ses membres (users.agency_id).
 * Scope hiérarchique via getDataScope().
 */
class TeamsController extends BaseController
{
    protected AgencyModel $agencyModel;
    protected UserModel   $userModel;

    public function __construct()
    {
        $this->agencyModel = new AgencyModel();
        $this->userModel   = new UserModel();
    }

    // --------------------------------------------------------
    // Liste des équipes
    // --------------------------------------------------------
    public function index(): string
    {
        $this->requirePermission('users.view');

        $scope = $this->getDataScope();
        $db    = \Config\Database::connect();

        $builder = $db->table('agencies a')
            ->select("a.id, a.name, a.city, a.logo, a.is_active, a.email, a.phone,
                (SELECT COUNT(*) FROM users u      WHERE u.agency_id = a.id AND u.deleted_at IS NULL) AS members_count,
                (SELECT COUNT(*) FROM properties p WHERE p.agency_id = a.id AND p.deleted_at IS NULL) AS properties_count,
                (SELECT COUNT(*) FROM leads l
                    JOIN users ul ON l.assigned_to = ul.id
                    WHERE ul.agency_id = a.id AND l.deleted_at IS NULL) AS leads_count,
                (SELECT COUNT(*) FROM visits v
                    JOIN users uv ON v.agent_id = uv.id
                    WHERE uv.agency_id = a.id AND v.deleted_at IS NULL) AS visits_count,
                (SELECT COUNT(*) FROM leads l2
                    JOIN users ul2 ON l2.assigned_to = ul2.id
                    WHERE ul2.agency_id = a.id AND l2.status = 'won' AND l2.deleted_at IS NULL) AS leads_won")
            ->where('a.deleted_at IS NULL');

        if ($scope['type'] === 'organization') {
            $builder->where('a.organization_id', (int) $scope['value']);
        } elseif ($scope['type'] === 'agency') {
            $builder->where('a.id', (int) $scope['value']);
        } elseif ($scope['type'] === 'own') {
            $agencyId = (int) session()->get('agency_id');
            if ($agencyId) {
                $builder->where('a.id', $agencyId);
            }
        }

        $teams = $builder->orderBy('a.name', 'ASC')->get()->getResultArray();

        return $this->render('admin/teams/index', [
            'page_title' => 'Gestion des équipes',
            'teams'      => $teams,
        ]);
    }

    // --------------------------------------------------------
    // Détail d'une équipe
    // --------------------------------------------------------
    public function show(int $id): string
    {
        $this->requirePermission('users.view');
        $this->checkTeamAccess($id);

        $db   = \Config\Database::connect();
        $team = $this->agencyModel->findDetail($id);
        if (! $team) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Équipe introuvable.');
        }

        // Stats complémentaires
        $team['leads_count'] = (int) $db->table('leads l')
            ->join('users ul', 'l.assigned_to = ul.id')
            ->where('ul.agency_id', $id)
            ->where('l.deleted_at IS NULL')
            ->countAllResults();

        $team['visits_count'] = (int) $db->table('visits v')
            ->join('users uv', 'v.agent_id = uv.id')
            ->where('uv.agency_id', $id)
            ->where('v.deleted_at IS NULL')
            ->countAllResults();

        $team['leads_won'] = (int) $db->query(
            "SELECT COUNT(*) AS cnt FROM leads l
             JOIN users ul ON l.assigned_to = ul.id
             WHERE ul.agency_id = ? AND l.status = 'won' AND l.deleted_at IS NULL",
            [$id]
        )->getRowArray()['cnt'];

        // Membres avec leurs stats individuelles
        $members = $db->table('users u')
            ->select("u.id, u.first_name, u.last_name, u.email, u.phone,
                      u.status, u.avatar, u.last_login_at,
                      COALESCE(r.label, r.name) AS role_label,
                      COALESCE(r.color, '#6c757d') AS role_color,
                      r.name AS role_name,
                      r.hierarchy_level,
                      (SELECT COUNT(*) FROM leads l  WHERE l.assigned_to = u.id AND l.deleted_at IS NULL) AS leads_count,
                      (SELECT COUNT(*) FROM visits v WHERE v.agent_id   = u.id AND v.deleted_at IS NULL) AS visits_count,
                      (SELECT COUNT(*) FROM leads l2 WHERE l2.assigned_to = u.id AND l2.status = 'won' AND l2.deleted_at IS NULL) AS leads_won,
                      (SELECT COUNT(*) FROM properties p WHERE p.agent_id = u.id AND p.deleted_at IS NULL) AS properties_count")
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.agency_id', $id)
            ->where('u.deleted_at IS NULL')
            ->orderBy('r.hierarchy_level', 'ASC')
            ->orderBy('u.first_name', 'ASC')
            ->get()->getResultArray();

        // Utilisateurs disponibles pour ajout (pas encore dans cette agence)
        $canManage = $this->auth->hasPermission('users.edit');
        $available = [];
        if ($canManage) {
            $available = $db->table('users u')
                ->select("u.id, u.first_name, u.last_name, u.email,
                          COALESCE(r.label, r.name) AS role_label,
                          COALESCE(r.color, '#6c757d') AS role_color")
                ->join('roles r', 'r.id = u.role_id')
                ->where('u.deleted_at IS NULL')
                ->where("(u.agency_id IS NULL OR u.agency_id != {$id})")
                ->orderBy('u.first_name', 'ASC')
                ->get()->getResultArray();
        }

        // Activité récente (10 dernières actions de l'équipe)
        $recentActivity = $db->table('activity_logs al')
            ->select('al.action, al.module, al.description, al.created_at,
                      u.first_name, u.last_name')
            ->join('users u', 'u.id = al.user_id')
            ->where('u.agency_id', $id)
            ->orderBy('al.created_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        return $this->render('admin/teams/show', [
            'page_title'     => 'Équipe — ' . $team['name'],
            'team'           => $team,
            'members'        => $members,
            'available'      => $available,
            'canManage'      => $canManage,
            'recentActivity' => $recentActivity,
        ]);
    }

    // --------------------------------------------------------
    // Ajouter un membre à l'équipe
    // --------------------------------------------------------
    public function addMember(int $id)
    {
        $this->requirePermission('users.edit');
        $this->checkTeamAccess($id);

        $userId = (int) $this->request->getPost('user_id');
        $user   = $this->userModel->find($userId);

        if (! $user) {
            return redirect()->to(base_url("admin/teams/{$id}"))->with('error', 'Utilisateur introuvable.');
        }

        $this->userModel->update($userId, ['agency_id' => $id]);

        $this->log->activity('team.member.add', 'teams', 'user', $userId,
            "Ajout de {$user['first_name']} {$user['last_name']} à l'équipe #{$id}");

        return redirect()->to(base_url("admin/teams/{$id}"))->with('success', 'Membre ajouté à l\'équipe.');
    }

    // --------------------------------------------------------
    // Retirer un membre de l'équipe
    // --------------------------------------------------------
    public function removeMember(int $id)
    {
        $this->requirePermission('users.edit');
        $this->checkTeamAccess($id);

        $userId = (int) $this->request->getPost('user_id');
        $user   = $this->userModel->find($userId);

        if (! $user || (int) $user['agency_id'] !== $id) {
            return redirect()->to(base_url("admin/teams/{$id}"))->with('error', 'Ce membre n\'appartient pas à cette équipe.');
        }

        $this->userModel->update($userId, ['agency_id' => null]);

        $this->log->activity('team.member.remove', 'teams', 'user', $userId,
            "Retrait de {$user['first_name']} {$user['last_name']} de l'équipe #{$id}");

        return redirect()->to(base_url("admin/teams/{$id}"))->with('success', 'Membre retiré de l\'équipe.');
    }

    // --------------------------------------------------------
    // Vérification d'accès à une équipe selon la hiérarchie
    // --------------------------------------------------------
    private function checkTeamAccess(int $agencyId): void
    {
        $scope = $this->getDataScope();

        if ($scope['type'] === 'all') {
            return;
        }

        if ($scope['type'] === 'organization') {
            $agency = $this->agencyModel->find($agencyId);
            if ($agency && (int) ($agency['organization_id'] ?? 0) === (int) $scope['value']) {
                return;
            }
        }

        if ($scope['type'] === 'agency' && (int) $scope['value'] === $agencyId) {
            return;
        }

        if ($scope['type'] === 'own' && (int) session()->get('agency_id') === $agencyId) {
            return;
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException('Accès refusé à cette équipe.');
    }
}
