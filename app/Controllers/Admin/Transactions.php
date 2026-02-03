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
        $data = [
            'title' => 'Nouvelle Transaction',
            'properties' => $this->propertyModel->where('status', 'published')->findAll(),
            'clients' => $this->clientModel->where('status !=', 'archived')->findAll()
        ];

        return view('admin/transactions/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'property_id' => 'required|integer',
            'client_id' => 'required|integer',
            'type' => 'required|in_list[sale,rent]',
            'amount' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'property_id' => $this->request->getPost('property_id'),
            'client_id' => $this->request->getPost('client_id'),
            'agent_id' => session()->get('user_id'),
            'agency_id' => session()->get('agency_id'),
            'type' => $this->request->getPost('type'),
            'amount' => $this->request->getPost('amount'),
            'commission_percentage' => $this->request->getPost('commission_percentage') ?? 3,
            'status' => 'draft',
            'notes' => $this->request->getPost('notes')
        ];

        // Calculate commission
        $commissionPercentage = $data['commission_percentage'];
        $data['commission_amount'] = ($data['amount'] * $commissionPercentage) / 100;

        if ($transactionId = $this->transactionModel->insert($data)) {
            // Create commission entry
            $this->commissionModel->calculateCommission($transactionId, $data['agent_id'], $commissionPercentage);
            
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

        $data = [
            'title' => 'Modifier Transaction',
            'transaction' => $transaction,
            'properties' => $this->propertyModel->findAll(),
            'clients' => $this->clientModel->findAll()
        ];

        return view('admin/transactions/edit', $data);
    }

    public function update($id)
    {
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            return redirect()->to('/admin/transactions')->with('error', 'Transaction non trouvée');
        }

        $data = [
            'amount' => $this->request->getPost('amount'),
            'status' => $this->request->getPost('status'),
            'signature_date' => $this->request->getPost('signature_date'),
            'completion_date' => $this->request->getPost('completion_date'),
            'notes' => $this->request->getPost('notes')
        ];

        // Recalculate commission if amount changed
        if ($data['amount'] != $transaction['amount']) {
            $data['commission_amount'] = ($data['amount'] * $transaction['commission_percentage']) / 100;
        }

        if ($this->transactionModel->update($id, $data)) {
            return redirect()->to('/admin/transactions')->with('success', 'Transaction mise à jour');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
    }

    public function delete($id)
    {
        if ($this->transactionModel->delete($id)) {
            return redirect()->to('/admin/transactions')->with('success', 'Transaction supprimée');
        }

        return redirect()->to('/admin/transactions')->with('error', 'Erreur lors de la suppression');
    }
}
