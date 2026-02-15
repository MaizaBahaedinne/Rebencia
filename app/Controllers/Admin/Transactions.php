<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CommissionCalculatorService;

class Transactions extends BaseController
{
    protected $transactionModel;
    protected $propertyModel;
    protected $clientModel;
    protected $commissionModel;
    protected $transactionCommissionModel;
    protected $commissionCalculator;
    protected $userModel;

    public function __construct()
    {
        $this->transactionModel = model('TransactionModel');
        $this->propertyModel = model('PropertyModel');
        $this->clientModel = model('ClientModel');
        $this->commissionModel = model('CommissionModel');
        $this->transactionCommissionModel = model('TransactionCommissionModel');
        $this->userModel = model('UserModel');
        $this->commissionCalculator = new CommissionCalculatorService();
    }

    public function index()
    {
        // Get current user
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        
        $query = $this->transactionModel->select('transactions.*, properties.title as property_title, clients.first_name as client_name')
            ->join('properties', 'properties.id = transactions.property_id')
            ->join('clients', 'clients.id = transactions.client_id');
        
        // If user is not super admin, filter by agent_id (show only transactions assigned to current user)
        if ($currentRoleLevel && $currentRoleLevel != 100) { // role_level 100 = super admin
            $query->where('transactions.agent_id', $currentUserId);
        }
        
        $data = [
            'title' => 'Gestion des Transactions',
            'transactions' => $query
                ->orderBy('transactions.created_at', 'DESC')
                ->paginate(20)
        ];

        return view('admin/transactions/index', $data);
    }

    public function create()
    {
        // Get current user
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        
        $userModel = model('UserModel');
        $agencyModel = model('AgencyModel');
        
        // Get properties with agent and agency info
        $propertiesQuery = $this->propertyModel
            ->select('properties.*, 
                     agents.agency_id as agent_agency_id,
                     CONCAT(agents.first_name, " ", agents.last_name) as agent_name,
                     agencies.name as agency_name')
            ->join('users as agents', 'agents.id = properties.agent_id', 'left')
            ->join('agencies', 'agencies.id = agents.agency_id', 'left')
            ->where('properties.status', 'published');
        
        // If user is not super admin, filter by agent_id (show only their properties)
        if ($currentRoleLevel && $currentRoleLevel != 100) { // role_level 100 = super admin
            $propertiesQuery->where('properties.agent_id', $currentUserId);
        }
        
        $properties = $propertiesQuery->findAll();
        
        $data = [
            'title' => 'Nouvelle Transaction',
            'properties' => $properties,
            'buyers' => $this->clientModel->findAll(),
            'agents' => $userModel->where('status', 'active')->findAll(),
            'agencies' => $agencyModel->findAll()
        ];

        return view('admin/transactions/create', $data);
    }

    public function show($id)
    {
        $transaction = $this->transactionModel
            ->select('transactions.*, 
                     properties.title as property_title,
                     properties.reference as property_reference,
                     clients.first_name as client_first_name,
                     clients.last_name as client_last_name,
                     clients.phone as client_phone,
                     clients.email as client_email,
                     agents.first_name as agent_first_name,
                     agents.last_name as agent_last_name,
                     agencies.name as agency_name')
            ->join('properties', 'properties.id = transactions.property_id')
            ->join('clients', 'clients.id = transactions.client_id')
            ->join('users as agents', 'agents.id = transactions.agent_id', 'left')
            ->join('agencies', 'agencies.id = transactions.agency_id', 'left')
            ->find($id);
        
        if (!$transaction) {
            return redirect()->to('/admin/transactions')->with('error', 'Transaction non trouvée');
        }

        // Security check
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        if ($currentRoleLevel != 100 && $transaction['agent_id'] != $currentUserId) {
            return redirect()->to('/admin/transactions')->with('error', 'Accès refusé');
        }

        // Get commission details
        $commissions = $this->transactionCommissionModel
            ->where('transaction_id', $id)
            ->findAll();

        $data = [
            'title' => 'Détails de la Transaction',
            'transaction' => $transaction,
            'commissions' => $commissions
        ];

        return view('admin/transactions/show', $data);
    }

    public function store()
    {
        // Get current user
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        
        $validation = \Config\Services::validation();
        
        $rules = [
            'property_id' => 'required|is_natural_no_zero',
            'buyer_id' => 'required|is_natural_no_zero',
            'type' => 'required|in_list[sale,rent]',
            'transaction_date' => 'required|valid_date',
            'amount' => 'required|decimal',
            'agent_id' => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            log_message('warning', 'Transaction validation failed: ' . json_encode($validation->getErrors()));
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Récupérer les informations nécessaires
        $propertyId = $this->request->getPost('property_id');
        $agentId = $this->request->getPost('agent_id');
        $amount = $this->request->getPost('amount');
        $type = $this->request->getPost('type');
        
        // Récupérer le bien et l'agent
        $property = $this->propertyModel->find($propertyId);
        $agent = $this->userModel->find($agentId);
        
        if (!$property || !$agent) {
            return redirect()->back()->withInput()->with('error', 'Bien ou agent non trouvé');
        }
        
        // Security check: If user is not super admin, verify they own the property
        if ($currentRoleLevel && $currentRoleLevel != 100) { // role_level 100 = super admin
            if ($property['agent_id'] != $currentUserId) {
                return redirect()->back()->with('error', 'Vous n\'avez pas la permission de créer une transaction pour ce bien');
            }
        }

        // Créer la transaction
        $transactionData = [
            'property_id' => $propertyId,
            'client_id' => $this->request->getPost('buyer_id'),
            'seller_id' => $this->request->getPost('seller_id') ?: null,
            'agent_id' => $agentId,
            'agency_id' => $this->request->getPost('agency_id') ?? $agent['agency_id'] ?? session()->get('agency_id'),
            'type' => $type,
            'transaction_date' => $this->request->getPost('transaction_date'),
            'amount' => $amount,
            'contract_number' => $this->request->getPost('contract_number'),
            'notary' => $this->request->getPost('notary'),
            'status' => $this->request->getPost('status') ?? 'pending',
            'notes' => $this->request->getPost('notes')
        ];

        log_message('info', 'Creating transaction with data: ' . json_encode($transactionData));

        if ($transactionId = $this->transactionModel->insert($transactionData)) {
            // Calculer automatiquement la commission avec le nouveau système
            try {
                $commissionData = [
                    'transaction_id' => $transactionId,
                    'property_id' => $propertyId,
                    'transaction_type' => $type,
                    'property_type' => $property['type'],
                    'amount' => $amount
                ];
                
                $commission = $this->commissionCalculator->calculateCommission(
                    $commissionData,
                    $agentId,
                    $agent['role_id'],
                    $agent['agency_id'],
                    persist: true
                );
                
                // Mettre à jour la transaction avec les montants de commission (pour compatibilité)
                $this->transactionModel->update($transactionId, [
                    'commission_percentage' => ($commission['total_commission_ht'] / $amount) * 100,
                    'commission_amount' => $commission['total_commission_ttc'],
                    'commission_paid' => 0
                ]);
                
                session()->setFlashdata('success', 'Transaction créée avec succès. Commission calculée : ' . 
                    number_format($commission['total_commission_ttc'], 2) . ' TND TTC');
                
            } catch (\Exception $e) {
                log_message('error', 'Erreur calcul commission: ' . $e->getMessage());
                session()->setFlashdata('warning', 'Transaction créée mais erreur lors du calcul de commission: ' . $e->getMessage());
            }
            
            // Trigger notification
            $notificationHelper = new \App\Libraries\NotificationHelper();
            $notificationHelper->notifyTransactionCreated($transactionId, $transactionData, session()->get('user_id'));
            
            return redirect()->to('/admin/transactions/' . $transactionId);
        }

        log_message('error', 'Transaction insert failed. Errors: ' . json_encode($this->transactionModel->errors()));
        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création de la transaction. Veuillez vérifier vos données.');
    }

    public function edit($id)
    {
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            return redirect()->to('/admin/transactions')->with('error', 'Transaction non trouvée');
        }

        $userModel = model('UserModel');
        $agencyModel = model('AgencyModel');

        $buyers = $this->clientModel->whereIn('type', ['buyer', 'tenant'])->findAll();
        $sellers = $this->clientModel->whereIn('type', ['seller', 'landlord'])->findAll();

        if (!empty($transaction['client_id'])) {
            $buyerIds = array_column($buyers, 'id');
            if (!in_array($transaction['client_id'], $buyerIds, true)) {
                $currentBuyer = $this->clientModel->find($transaction['client_id']);
                if ($currentBuyer) {
                    array_unshift($buyers, $currentBuyer);
                }
            }
        }

        if (!empty($transaction['seller_id'])) {
            $sellerIds = array_column($sellers, 'id');
            if (!in_array($transaction['seller_id'], $sellerIds, true)) {
                $currentSeller = $this->clientModel->find($transaction['seller_id']);
                if ($currentSeller) {
                    array_unshift($sellers, $currentSeller);
                }
            }
        }

        $data = [
            'title' => 'Modifier Transaction',
            'transaction' => $transaction,
            'properties' => $this->propertyModel->where('status', 'published')->findAll(),
            'buyers' => $buyers,
            'sellers' => $sellers,
            'agents' => $userModel->where('role_id >=', 6)->findAll(),
            'agencies' => $agencyModel->where('status', 'active')->findAll()
        ];

        return view('admin/transactions/edit', $data);
    }

    public function update($id)
    {
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            return redirect()->to('/admin/transactions')->with('error', 'Transaction non trouvée');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'property_id' => 'required|is_natural_no_zero',
            'buyer_id' => 'required|is_natural_no_zero',
            'type' => 'required|in_list[sale,rent]',
            'transaction_date' => 'required|valid_date',
            'amount' => 'required|decimal',
            'agent_id' => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Récupérer les informations
        $propertyId = $this->request->getPost('property_id');
        $agentId = $this->request->getPost('agent_id');
        $amount = $this->request->getPost('amount');
        $type = $this->request->getPost('type');
        
        $property = $this->propertyModel->find($propertyId);
        $agent = $this->userModel->find($agentId);

        $data = [
            'property_id' => $propertyId,
            'client_id' => $this->request->getPost('buyer_id'),
            'seller_id' => $this->request->getPost('seller_id') ?: null,
            'agent_id' => $agentId,
            'agency_id' => $this->request->getPost('agency_id'),
            'type' => $type,
            'transaction_date' => $this->request->getPost('transaction_date'),
            'amount' => $amount,
            'contract_number' => $this->request->getPost('contract_number'),
            'notary' => $this->request->getPost('notary'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->transactionModel->update($id, $data)) {
            // Recalculer la commission si le montant, le type ou l'agent a changé
            $currentAmount = $transaction['amount'] ?? $transaction['transaction_amount'] ?? null;
            $shouldRecalculate = (
                $currentAmount != $amount ||
                $transaction['type'] != $type ||
                $transaction['agent_id'] != $agentId ||
                $transaction['property_id'] != $propertyId
            );
            
            if ($shouldRecalculate && $property && $agent) {
                try {
                    // Supprimer l'ancienne commission
                    $this->transactionCommissionModel->where('transaction_id', $id)->delete();
                    
                    // Recalculer
                    $commissionData = [
                        'transaction_id' => $id,
                        'property_id' => $propertyId,
                        'transaction_type' => $type,
                        'property_type' => $property['type'],
                        'amount' => $amount
                    ];
                    
                    $commission = $this->commissionCalculator->calculateCommission(
                        $commissionData,
                        $agentId,
                        $agent['role_id'],
                        $agent['agency_id'],
                        persist: true
                    );
                    
                    // Mettre à jour les montants
                    $this->transactionModel->update($id, [
                        'commission_percentage' => ($commission['total_commission_ht'] / $amount) * 100,
                        'commission_amount' => $commission['total_commission_ttc']
                    ]);
                    
                    session()->setFlashdata('success', 'Transaction modifiée. Commission recalculée : ' . 
                        number_format($commission['total_commission_ttc'], 2) . ' TND TTC');
                        
                } catch (\Exception $e) {
                    log_message('error', 'Erreur recalcul commission: ' . $e->getMessage());
                    session()->setFlashdata('success', 'Transaction modifiée');
                }
            } else {
                session()->setFlashdata('success', 'Transaction modifiée avec succès');
            }
            
            return redirect()->to('/admin/transactions');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification');
    }

    public function delete($id)
    {
        if ($this->transactionModel->delete($id)) {
            // Supprimer aussi les commissions associées
            $this->transactionCommissionModel->where('transaction_id', $id)->delete();
            
            return redirect()->to('/admin/transactions')->with('success', 'Transaction supprimée');
        }

        return redirect()->to('/admin/transactions')->with('error', 'Erreur lors de la suppression');
    }

    /**
     * Voir les détails de commission d'une transaction
     */
    public function viewCommission($id)
    {
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            return redirect()->to('/admin/transactions')->with('error', 'Transaction non trouvée');
        }
        
        // Récupérer la commission calculée
        $commission = $this->transactionCommissionModel->where('transaction_id', $id)->first();
        
        // Récupérer les détails de la transaction
        $property = $this->propertyModel->find($transaction['property_id']);
        $agent = $this->userModel->find($transaction['agent_id']);
        $buyer = $this->clientModel->find($transaction['client_id']);
        $seller = !empty($transaction['seller_id'])
            ? $this->clientModel->find($transaction['seller_id'])
            : null;
        
        $data = [
            'title' => 'Détails Commission - Transaction #' . $transaction['reference'],
            'transaction' => $transaction,
            'commission' => $commission,
            'property' => $property,
            'agent' => $agent,
            'buyer' => $buyer,
            'seller' => $seller
        ];
        
        return view('admin/transactions/commission_details', $data);
    }
    
    /**
     * Marquer une commission comme payée
     */
    public function markCommissionPaid($id)
    {
        if (!canUpdate('transactions')) {
            return redirect()->back()->with('error', 'Accès refusé');
        }
        
        $commission = $this->transactionCommissionModel->where('transaction_id', $id)->first();
        
        if (!$commission) {
            return redirect()->back()->with('error', 'Commission non trouvée');
        }
        
        try {
            $this->commissionCalculator->markCommissionPaid(
                $commission['id'],
                $commission['total_commission_ttc'],
                session()->get('user_id')
            );
            
            // Mettre à jour aussi la transaction
            $this->transactionModel->update($id, [
                'commission_paid' => 1
            ]);
            
            return redirect()->back()->with('success', 'Commission marquée comme payée');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Mettre a jour la repartition agent/agence
     */
    public function updateCommissionSplit($id)
    {
        if (!canUpdate('transactions')) {
            return redirect()->back()->with('error', 'Acces refuse');
        }

        $commission = $this->transactionCommissionModel->where('transaction_id', $id)->first();
        if (!$commission) {
            return redirect()->back()->with('error', 'Commission non trouvee');
        }

        $percentage = (float) $this->request->getPost('agent_commission_percentage');
        if ($percentage < 0 || $percentage > 100) {
            return redirect()->back()->with('error', 'Pourcentage invalide');
        }

        $totalTtc = (float) ($commission['total_commission_ttc'] ?? 0);
        if ($totalTtc <= 0) {
            return redirect()->back()->with('error', 'Montant de commission invalide');
        }

        $agentAmount = round($totalTtc * ($percentage / 100), 2);
        $agencyAmount = round($totalTtc - $agentAmount, 2);

        $this->transactionCommissionModel->update($commission['id'], [
            'agent_commission_percentage' => $percentage,
            'agent_commission_amount' => $agentAmount,
            'agency_commission_amount' => $agencyAmount
        ]);

        return redirect()->back()->with('success', 'Repartition mise a jour');
    }
    
    /**
     * Recalculer la commission d'une transaction
     */
    public function recalculateCommission($id)
    {
        if (!canUpdate('transactions')) {
            return redirect()->back()->with('error', 'Accès refusé');
        }
        
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            return redirect()->back()->with('error', 'Transaction non trouvée');
        }
        
        $property = $this->propertyModel->find($transaction['property_id']);
        $agent = $this->userModel->find($transaction['agent_id']);
        $transactionAmount = $transaction['amount'] ?? $transaction['transaction_amount'] ?? null;
        
        if (!$property || !$agent || !$transactionAmount) {
            return redirect()->back()->with('error', 'Données incomplètes');
        }
        
        try {
            // Supprimer l'ancienne commission
            $this->transactionCommissionModel->where('transaction_id', $id)->delete();
            
            // Recalculer
            $commissionData = [
                'transaction_id' => $id,
                'property_id' => $transaction['property_id'],
                'transaction_type' => $transaction['type'],
                'property_type' => $property['type'],
                'amount' => $transactionAmount
            ];
            
            $commission = $this->commissionCalculator->calculateCommission(
                $commissionData,
                $transaction['agent_id'],
                $agent['role_id'],
                $agent['agency_id'],
                persist: true
            );
            
            // Mettre à jour la transaction
            $this->transactionModel->update($id, [
                'commission_percentage' => ($commission['total_commission_ht'] / $transactionAmount) * 100,
                'commission_amount' => $commission['total_commission_ttc']
            ]);
            
            return redirect()->back()->with('success', 'Commission recalculée : ' . 
                number_format($commission['total_commission_ttc'], 2) . ' TND TTC');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}
