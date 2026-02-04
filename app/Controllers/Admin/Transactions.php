<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Transactions extends BaseController
{
    protected $transactionModel;
    protected $propertyModel;
    protected $clientModel;
    protected $commissionModel;

    public function __construct()
    {
        $this->transactionModel = model('TransactionModel');
        $this->propertyModel = model('PropertyModel');
        $this->clientModel = model('ClientModel');
        $this->commissionModel = model('CommissionModel');
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
        
        $data = [
            'title' => 'Nouvelle Transaction',
            'properties' => $this->propertyModel->where('status', 'published')->findAll(),
            'buyers' => $this->clientModel->whereIn('type', ['buyer', 'tenant'])->findAll(),
            'sellers' => $this->clientModel->whereIn('type', ['seller', 'landlord'])->findAll(),
            'agents' => $userModel->where('role_id >=', 6)->findAll(),
            'agencies' => $agencyModel->where('status', 'active')->findAll()
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

        // Calcul de la commission
        $amount = $this->request->getPost('amount');
        $commissionPercentage = $this->request->getPost('commission_percentage') ?? 3;
        $commissionAmount = ($amount * $commissionPercentage) / 100;

        $data = [
            'property_id' => $this->request->getPost('property_id'),
            'buyer_id' => $this->request->getPost('buyer_id'),
            'seller_id' => $this->request->getPost('seller_id'),
            'agent_id' => $this->request->getPost('agent_id'),
            'agency_id' => $this->request->getPost('agency_id') ?? session()->get('agency_id'),
            'type' => $this->request->getPost('type'),
            'transaction_date' => $this->request->getPost('transaction_date'),
            'amount' => $amount,
            'commission_percentage' => $commissionPercentage,
            'commission_amount' => $commissionAmount,
            'commission_paid' => $this->request->getPost('commission_paid') ?? 0,
            'contract_number' => $this->request->getPost('contract_number'),
            'notary' => $this->request->getPost('notary'),
            'status' => $this->request->getPost('status') ?? 'pending',
            'notes' => $this->request->getPost('notes')
        ];

        if ($transactionId = $this->transactionModel->insert($data)) {
            // Créer l'entrée de commission
            $this->createCommissionEntry($transactionId, $data['agent_id'], $commissionAmount);
            
            // Trigger notification
            $notificationHelper = new \App\Libraries\NotificationHelper();
            $notificationHelper->notifyTransactionCreated($transactionId, $data, session()->get('user_id'));
            
            return redirect()->to('/admin/transactions')->with('success', 'Transaction créée avec succès');
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

        // Recalcul de la commission si montant ou pourcentage modifié
        $amount = $this->request->getPost('amount');
        $commissionPercentage = $this->request->getPost('commission_percentage');
        $commissionAmount = ($amount * $commissionPercentage) / 100;

        $data = [
            'property_id' => $this->request->getPost('property_id'),
            'buyer_id' => $this->request->getPost('buyer_id'),
            'seller_id' => $this->request->getPost('seller_id'),
            'agent_id' => $this->request->getPost('agent_id'),
            'agency_id' => $this->request->getPost('agency_id'),
            'type' => $this->request->getPost('type'),
            'transaction_date' => $this->request->getPost('transaction_date'),
            'amount' => $amount,
            'commission_percentage' => $commissionPercentage,
            'commission_amount' => $commissionAmount,
            'commission_paid' => $this->request->getPost('commission_paid'),
            'contract_number' => $this->request->getPost('contract_number'),
            'notary' => $this->request->getPost('notary'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->transactionModel->update($id, $data)) {
            return redirect()->to('/admin/transactions')->with('success', 'Transaction modifiée avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification');
    }

    public function delete($id)
    {
        if ($this->transactionModel->delete($id)) {
            return redirect()->to('/admin/transactions')->with('success', 'Transaction supprimée');
        }

        return redirect()->to('/admin/transactions')->with('error', 'Erreur lors de la suppression');
    }

    /**
     * Créer une entrée de commission pour la transaction
     */
    private function createCommissionEntry($transactionId, $agentId, $commissionAmount)
    {
        $commissionData = [
            'transaction_id' => $transactionId,
            'user_id' => $agentId,
            'amount' => $commissionAmount,
            'percentage' => $this->request->getPost('commission_percentage') ?? 3,
            'status' => 'pending',
            'type' => 'agent_commission'
        ];

        return $this->commissionModel->insert($commissionData);
    }
}
