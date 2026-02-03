<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Documents extends BaseController
{
    protected $documentModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->documentModel = model('TransactionDocumentModel');
        $this->transactionModel = model('TransactionModel');
    }

    /**
     * List documents for a transaction
     */
    public function index($transactionId)
    {
        $transaction = $this->transactionModel->find($transactionId);
        
        if (!$transaction) {
            return redirect()->to('/admin/transactions')->with('error', 'Transaction introuvable');
        }

        $documents = $this->documentModel->getForTransaction($transactionId);

        $data = [
            'title' => 'Documents - Transaction #' . $transaction['reference'],
            'page_title' => 'Gestion Documents',
            'transaction' => $transaction,
            'documents' => $documents
        ];

        return view('admin/documents/index', $data);
    }

    /**
     * Upload document
     */
    public function upload($transactionId)
    {
        $transaction = $this->transactionModel->find($transactionId);
        
        if (!$transaction) {
            return redirect()->to('/admin/transactions')->with('error', 'Transaction introuvable');
        }

        $rules = [
            'document' => 'uploaded[document]|max_size[document,5120]|ext_in[document,pdf,doc,docx,jpg,jpeg,png]',
            'document_type' => 'required|in_list[contract,title_deed,id_copy,tax_document,other]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('document');
        
        if ($file->isValid() && !$file->hasMoved()) {
            $uploadPath = WRITEPATH . 'uploads/documents/' . $transactionId;
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            // Check for existing version
            $documentType = $this->request->getPost('document_type');
            $existing = $this->documentModel->getLatestVersion($transactionId, $documentType);
            $version = $existing ? $existing['version'] + 1 : 1;

            // Save to database
            $data = [
                'transaction_id' => $transactionId,
                'document_type' => $documentType,
                'file_name' => $file->getClientName(),
                'file_path' => $uploadPath . '/' . $newName,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'uploaded_by' => session()->get('user_id'),
                'description' => $this->request->getPost('description'),
                'version' => $version
            ];

            if ($this->documentModel->insert($data)) {
                // Notify transaction agent
                $notificationModel = model('NotificationModel');
                $notificationModel->createNotification(
                    $transaction['agent_id'],
                    'info',
                    'Nouveau document uploadé',
                    'Un document a été ajouté à la transaction #' . $transaction['reference'],
                    '/admin/documents/' . $transactionId,
                    'fa-file-upload'
                );

                return redirect()->back()->with('success', 'Document uploadé avec succès');
            }
        }

        return redirect()->back()->with('error', 'Erreur lors de l\'upload du document');
    }

    /**
     * Download document
     */
    public function download($documentId)
    {
        $document = $this->documentModel->find($documentId);
        
        if (!$document || !file_exists($document['file_path'])) {
            return redirect()->back()->with('error', 'Document introuvable');
        }

        return $this->response->download($document['file_path'], null)->setFileName($document['file_name']);
    }

    /**
     * Delete document
     */
    public function delete($documentId)
    {
        $document = $this->documentModel->find($documentId);
        
        if (!$document) {
            return redirect()->back()->with('error', 'Document introuvable');
        }

        // Delete file
        if (file_exists($document['file_path'])) {
            unlink($document['file_path']);
        }

        // Delete from database
        if ($this->documentModel->delete($documentId)) {
            return redirect()->back()->with('success', 'Document supprimé avec succès');
        }

        return redirect()->back()->with('error', 'Erreur lors de la suppression');
    }

    /**
     * Generate contract PDF
     */
    public function generateContract($transactionId)
    {
        $transaction = $this->transactionModel->find($transactionId);
        
        if (!$transaction) {
            return redirect()->back()->with('error', 'Transaction introuvable');
        }

        // Get transaction details with related data
        $db = \Config\Database::connect();
        $builder = $db->table('transactions t');
        $builder->select('t.*, p.title as property_title, p.reference as property_ref, p.address,
                         c.full_name as client_name, c.cin, c.address as client_address,
                         CONCAT(u.first_name, " ", u.last_name) as agent_name');
        $builder->join('properties p', 'p.id = t.property_id', 'left');
        $builder->join('clients c', 'c.id = t.client_id', 'left');
        $builder->join('users u', 'u.id = t.agent_id', 'left');
        $builder->where('t.id', $transactionId);
        $transactionData = $builder->get()->getRowArray();

        // Generate HTML contract
        $html = $this->generateContractHTML($transactionData);

        // Convert to PDF using TCPDF or mPDF (not installed yet, just save HTML for now)
        $uploadPath = WRITEPATH . 'uploads/documents/' . $transactionId;
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = 'contrat_' . $transaction['reference'] . '_' . date('Ymd') . '.html';
        $filepath = $uploadPath . '/' . $filename;
        file_put_contents($filepath, $html);

        // Save to database
        $data = [
            'transaction_id' => $transactionId,
            'document_type' => 'contract',
            'file_name' => str_replace('.html', '.pdf', $filename),
            'file_path' => $filepath,
            'file_size' => filesize($filepath),
            'mime_type' => 'text/html',
            'uploaded_by' => session()->get('user_id'),
            'description' => 'Contrat généré automatiquement',
            'version' => 1
        ];

        $this->documentModel->insert($data);

        return redirect()->back()->with('success', 'Contrat généré avec succès');
    }

    /**
     * Generate contract HTML template
     */
    private function generateContractHTML($data)
    {
        $contractType = $data['type'] === 'sale' ? 'VENTE' : 'LOCATION';
        
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contrat de ' . $contractType . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        h1 { text-align: center; color: #0d6efd; }
        .section { margin: 30px 0; }
        .label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        td { padding: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>CONTRAT DE ' . $contractType . '</h1>
    
    <div class="section">
        <h3>Article 1 : Identification des Parties</h3>
        <p><span class="label">Le Vendeur/Bailleur :</span></p>
        <p>Agence REBENCIA représentée par son agent : ' . esc($data['agent_name']) . '</p>
        
        <p><span class="label">L\'Acheteur/Locataire :</span></p>
        <p>Nom : ' . esc($data['client_name']) . '</p>
        <p>CIN : ' . esc($data['cin']) . '</p>
        <p>Adresse : ' . esc($data['client_address']) . '</p>
    </div>
    
    <div class="section">
        <h3>Article 2 : Désignation du Bien</h3>
        <p><span class="label">Référence :</span> ' . esc($data['property_ref']) . '</p>
        <p><span class="label">Désignation :</span> ' . esc($data['property_title']) . '</p>
        <p><span class="label">Adresse :</span> ' . esc($data['address']) . '</p>
    </div>
    
    <div class="section">
        <h3>Article 3 : Prix et Modalités de Paiement</h3>
        <p><span class="label">Prix total :</span> ' . number_format($data['amount'], 2, ',', ' ') . ' TND</p>
        <p><span class="label">Date de ' . ($data['type'] === 'sale' ? 'vente' : 'début de location') . ' :</span> ' . date('d/m/Y', strtotime($data['completion_date'])) . '</p>
    </div>
    
    <div class="section">
        <h3>Article 4 : Commission</h3>
        <p><span class="label">Taux :</span> ' . $data['commission_percentage'] . '%</p>
        <p><span class="label">Montant :</span> ' . number_format($data['commission_amount'], 2, ',', ' ') . ' TND</p>
    </div>
    
    <div class="section">
        <p>Fait à Tunis, le ' . date('d/m/Y') . '</p>
        <br><br>
        <table>
            <tr>
                <td style="text-align: center;">
                    <strong>Signature du Vendeur/Bailleur</strong>
                </td>
                <td style="text-align: center;">
                    <strong>Signature de l\'Acheteur/Locataire</strong>
                </td>
            </tr>
            <tr>
                <td style="height: 80px;"></td>
                <td style="height: 80px;"></td>
            </tr>
        </table>
    </div>
</body>
</html>';
    }
}
