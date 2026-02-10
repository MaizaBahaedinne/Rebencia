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
        $data = [
            'title' => 'Gestion des Transactions',
            'transactions' => $this->transactionModel->select('transactions.*, properties.title as property_title, clients.first_name as client_name')
                ->join('properties', 'properties.id = transactions.property_id')
                ->join('clients', 'clients.id = transactions.client_id')
                ->orderBy('transactions.created_at', 'DESC')
                ->paginate(20)
        ];

        return view('admin/transactions/index', $data);
    }

    public function create()
    {
        $userModel = model('UserModel');
        $agencyModel = model('AgencyModel');
        
        // Get properties with owner and agent info
        $properties = $this->propertyModel
            ->select('properties.*, 
                     properties.owner_id,
                     properties.agent_id,
                     agents.agency_id as agent_agency_id,
                     CONCAT(owners.first_name, " ", owners.last_name) as owner_name,
                     CONCAT(agents.first_name, " ", agents.last_name) as agent_name,
                     agencies.name as agency_name')
            ->join('clients as owners', 'owners.id = properties.owner_id', 'left')
            ->join('users as agents', 'agents.id = properties.agent_id', 'left')
            ->join('agencies', 'agencies.id = agents.agency_id', 'left')
            ->findAll();
        
        $data = [
            'title' => 'Nouvelle Transaction',
            'properties' => $properties,
            'buyers' => $this->clientModel->findAll(),
            'agents' => $userModel->where('status', 'active')->findAll(),
            'agencies' => $agencyModel->findAll()
        ];

        return view('admin/transactions/create', $data);
    }

    public function store()
    {
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

        // Créer la transaction
        $transactionData = [
            'property_id' => $propertyId,
            'client_id' => $this->request->getPost('buyer_id'),
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

        if ($transactionId = $this->transactionModel->insert($transactionData)) {
            // Calculer automatiquement la commission avec le nouveau système
            try {
                $commissionData = [
                    'transaction_id' => $transactionId,
                    'property_id' => $propertyId,
                    'transaction_type' => $type,
                    'property_type' => $property['type'],
                    'transaction_amount' => $amount
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
            
            return redirect()->to('/admin/transactions');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
    }

    public function edit($id)
    {
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            return redirect()->to('/admin/transactions')->with('error', 'Transaction non trouvée');
        }

        $userModel = model('UserModel');
        $agencyModel = model('AgencyModel');

        $data = [
            'title' => 'Modifier Transaction',
            'transaction' => $transaction,
            'properties' => $this->propertyModel->where('status', 'published')->findAll(),
            'buyers' => $this->clientModel->whereIn('type', ['buyer', 'tenant'])->findAll(),
            'sellers' => $this->clientModel->whereIn('type', ['seller', 'landlord'])->findAll(),
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
            $shouldRecalculate = (
                $transaction['amount'] != $amount ||
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
                        'transaction_amount' => $amount
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
        $client = $this->clientModel->find($transaction['client_id']);
        
        $data = [
            'title' => 'Détails Commission - Transaction #' . $transaction['reference'],
            'transaction' => $transaction,
            'commission' => $commission,
            'property' => $property,
            'agent' => $agent,
            'client' => $client
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
        
        if (!$property || !$agent) {
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
                'transaction_amount' => $transaction['amount']
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
                'commission_percentage' => ($commission['total_commission_ht'] / $transaction['amount']) * 100,
                'commission_amount' => $commission['total_commission_ttc']
            ]);
            
            return redirect()->back()->with('success', 'Commission recalculée : ' . 
                number_format($commission['total_commission_ttc'], 2) . ' TND TTC');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}
