<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommissionRuleModel;
use App\Models\CommissionOverrideModel;
use App\Models\TransactionCommissionModel;
use App\Models\CommissionLogModel;
use App\Services\CommissionCalculatorService;

/**
 * Commission Settings Controller
 * Manages advanced commission rules, overrides, and calculations
 */
class CommissionSettings extends BaseController
{
    protected $ruleModel;
    protected $overrideModel;
    protected $commissionModel;
    protected $logModel;
    protected $calculator;

    public function __construct()
    {
        $this->ruleModel = new CommissionRuleModel();
        $this->overrideModel = new CommissionOverrideModel();
        $this->commissionModel = new TransactionCommissionModel();
        $this->logModel = new CommissionLogModel();
        $this->calculator = new CommissionCalculatorService();
    }

    // ========================================================================
    // DASHBOARD
    // ========================================================================

    /**
     * Commission settings dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Gestion des Commissions'
        ];

        return view('admin/commission_settings/index', $data);
    }

    // ========================================================================
    // COMMISSION RULES MANAGEMENT
    // ========================================================================

    /**
     * List all commission rules
     */
    public function rules()
    {
        $data = [
            'title' => 'Règles de Commission',
            'rules' => $this->ruleModel->getActiveRulesGrouped()
        ];

        return view('admin/commission_settings/rules/index', $data);
    }

    /**
     * Create new rule form
     */
    public function createRule()
    {
        $data = [
            'title' => 'Nouvelle Règle de Commission'
        ];

        return view('admin/commission_settings/rules/create', $data);
    }

    /**
     * Store new rule
     */
    public function storeRule()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|min_length[3]',
            'transaction_type' => 'required|in_list[sale,rent]',
            'property_type' => 'required',
            'buyer_commission_type' => 'required',
            'buyer_commission_value' => 'required|decimal',
            'seller_commission_type' => 'required',
            'seller_commission_value' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'transaction_type' => $this->request->getPost('transaction_type'),
            'property_type' => $this->request->getPost('property_type'),
            'buyer_commission_type' => $this->request->getPost('buyer_commission_type'),
            'buyer_commission_value' => $this->request->getPost('buyer_commission_value'),
            'buyer_commission_vat' => $this->request->getPost('buyer_commission_vat') ?: 19.00,
            'seller_commission_type' => $this->request->getPost('seller_commission_type'),
            'seller_commission_value' => $this->request->getPost('seller_commission_value'),
            'seller_commission_vat' => $this->request->getPost('seller_commission_vat') ?: 19.00,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'is_default' => $this->request->getPost('is_default') ? 1 : 0,
            'description' => $this->request->getPost('description')
        ];

        if ($ruleId = $this->ruleModel->insert($data)) {
            // Log creation
            $this->logModel->logAction('rule', $ruleId, 'create', null, $data, 'Nouvelle règle créée');
            
            return redirect()->to('/admin/commission-settings/rules')->with('success', 'Règle créée avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
    }

    /**
     * Edit rule form
     */
    public function editRule($id)
    {
        $rule = $this->ruleModel->find($id);
        
        if (!$rule) {
            return redirect()->to('/admin/commission-settings/rules')->with('error', 'Règle non trouvée');
        }

        $data = [
            'title' => 'Modifier la Règle',
            'rule' => $rule
        ];

        return view('admin/commission_settings/rules/edit', $data);
    }

    /**
     * Update rule
     */
    public function updateRule($id)
    {
        $rule = $this->ruleModel->find($id);
        
        if (!$rule) {
            return redirect()->to('/admin/commission-settings/rules')->with('error', 'Règle non trouvée');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'buyer_commission_type' => $this->request->getPost('buyer_commission_type'),
            'buyer_commission_value' => $this->request->getPost('buyer_commission_value'),
            'buyer_commission_vat' => $this->request->getPost('buyer_commission_vat'),
            'seller_commission_type' => $this->request->getPost('seller_commission_type'),
            'seller_commission_value' => $this->request->getPost('seller_commission_value'),
            'seller_commission_vat' => $this->request->getPost('seller_commission_vat'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'description' => $this->request->getPost('description')
        ];

        if ($this->ruleModel->update($id, $data)) {
            // Log update
            $this->logModel->logAction('rule', $id, 'update', $rule, $data, 'Règle modifiée');
            
            return redirect()->to('/admin/commission-settings/rules')->with('success', 'Règle mise à jour');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
    }

    // ========================================================================
    // COMMISSION OVERRIDES MANAGEMENT
    // ========================================================================

    /**
     * List overrides
     */
    public function overrides()
    {
        $data = [
            'title' => 'Surcharges de Commission',
            'overrides' => $this->overrideModel->select('commission_overrides.*, 
                agencies.name as agency_name, 
                roles.display_name as role_name,
                CONCAT(users.first_name, " ", users.last_name) as user_name')
                ->join('agencies', 'agencies.id = commission_overrides.agency_id', 'left')
                ->join('roles', 'roles.id = commission_overrides.role_id', 'left')
                ->join('users', 'users.id = commission_overrides.user_id', 'left')
                ->where('commission_overrides.is_active', 1)
                ->findAll()
        ];

        return view('admin/commission_settings/overrides/index', $data);
    }

    /**
     * Create override form
     */
    public function createOverride()
    {
        $agencyModel = model('AgencyModel');
        $roleModel = model('RoleModel');
        $userModel = model('UserModel');

        $data = [
            'title' => 'Nouvelle Surcharge',
            'agencies' => $agencyModel->where('status', 'active')->findAll(),
            'roles' => $roleModel->findAll(),
            'users' => $userModel->where('status', 'active')->findAll()
        ];

        return view('admin/commission_settings/overrides/create', $data);
    }

    /**
     * Store override
     */
    public function storeOverride()
    {
        $data = [
            'override_level' => $this->request->getPost('override_level'),
            'agency_id' => $this->request->getPost('agency_id') ?: null,
            'role_id' => $this->request->getPost('role_id') ?: null,
            'user_id' => $this->request->getPost('user_id') ?: null,
            'transaction_type' => $this->request->getPost('transaction_type'),
            'property_type' => $this->request->getPost('property_type'),
            'buyer_commission_type' => $this->request->getPost('buyer_commission_type') ?: null,
            'buyer_commission_value' => $this->request->getPost('buyer_commission_value') ?: null,
            'buyer_commission_vat' => $this->request->getPost('buyer_commission_vat') ?: null,
            'seller_commission_type' => $this->request->getPost('seller_commission_type') ?: null,
            'seller_commission_value' => $this->request->getPost('seller_commission_value') ?: null,
            'seller_commission_vat' => $this->request->getPost('seller_commission_vat') ?: null,
            'is_active' => 1,
            'notes' => $this->request->getPost('notes'),
            'created_by' => session()->get('user_id')
        ];

        if ($this->overrideModel->upsertOverride($data)) {
            $overrideId = $this->overrideModel->getInsertID();
            $this->logModel->logAction('override', $overrideId, 'create', null, $data, 'Surcharge créée');
            
            return redirect()->to('/admin/commission-settings/overrides')->with('success', 'Surcharge créée avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
    }

    // ========================================================================
    // COMMISSION SIMULATION
    // ========================================================================

    /**
     * Simulation tool
     */
    public function simulate()
    {
        $userModel = model('UserModel');
        $users = $userModel->where('status', 'active')->findAll();

        $data = [
            'title' => 'Simulateur de Commission',
            'users' => $users
        ];

        return view('admin/commission_settings/simulate', $data);
    }

    /**
     * Process simulation (AJAX)
     */
    public function processSimulation()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        try {
            // Get JSON input
            $json = $this->request->getJSON(true);
            
            // Fallback to POST if JSON is empty
            if (empty($json)) {
                $transactionType = $this->request->getPost('transaction_type');
                $propertyType = $this->request->getPost('property_type');
                $amount = (float) $this->request->getPost('amount');
                $userId = (int) $this->request->getPost('user_id');
            } else {
                $transactionType = $json['transaction_type'] ?? null;
                $propertyType = $json['property_type'] ?? null;
                $amount = (float) ($json['amount'] ?? 0);
                $userId = (int) ($json['user_id'] ?? 0);
            }
            
            // Validation basique
            if (empty($transactionType) || empty($propertyType) || $amount <= 0) {
                return $this->response->setJSON([
                    'error' => 'Veuillez remplir tous les champs requis (type de transaction, type de bien, montant)'
                ]);
            }
            
            // Si pas d'utilisateur spécifié, utiliser la session
            if (empty($userId)) {
                $userId = (int) session()->get('user_id');
            }
            
            $userModel = model('UserModel');
            $user = $userModel->find($userId);

            if (!$user) {
                return $this->response->setJSON(['error' => 'Utilisateur non trouvé']);
            }

            $result = $this->calculator->simulateCommission(
                $transactionType,
                $propertyType,
                $amount,
                $userId,
                $user['role_id'],
                $user['agency_id']
            );

            return $this->response->setJSON([
                'success' => true,
                'commission' => $result
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Commission simulation error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'error' => $e->getMessage(),
                'details' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]);
        }
    }

    // ========================================================================
    // AUDIT LOGS
    // ========================================================================

    /**
     * View audit logs
     */
    public function logs()
    {
        $logs = $this->logModel->getRecentLogsWithDetails(100);

        $data = [
            'title' => 'Journal d\'Audit des Commissions',
            'logs' => $logs
        ];

        return view('admin/commission_settings/logs', $data);
    }
}
