<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Signatures extends BaseController
{
    protected $signatureModel;
    protected $documentModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Sign document page
     */
    public function sign($documentId)
    {
        $documentModel = model('TransactionDocumentModel');
        $document = $documentModel->find($documentId);

        if (!$document) {
            return redirect()->back()->with('error', 'Document non trouvé');
        }

        $data = [
            'title' => 'Signer le Document',
            'page_title' => 'Signature Électronique',
            'document' => $document
        ];

        return view('admin/signatures/sign', $data);
    }

    /**
     * Save signature
     */
    public function saveSignature()
    {
        $data = $this->request->getPost();
        
        $signatureData = [
            'document_id' => $data['document_id'],
            'signer_type' => $data['signer_type'],
            'signer_id' => session()->get('user_id'),
            'signer_name' => $data['signer_name'],
            'signer_email' => $data['signer_email'],
            'signature_data' => $data['signature_data'],
            'ip_address' => $this->request->getIPAddress(),
            'signed_at' => date('Y-m-d H:i:s'),
            'status' => 'signed'
        ];

        $this->db->table('signatures')->insert($signatureData);

        // Create notification
        $notificationModel = model('NotificationModel');
        $notificationModel->insert([
            'user_id' => session()->get('user_id'),
            'type' => 'success',
            'title' => 'Document signé',
            'message' => 'Le document a été signé avec succès',
            'link' => '/admin/documents/' . $data['document_id']
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Signature enregistrée avec succès'
        ]);
    }

    /**
     * Get signatures for document
     */
    public function getSignatures($documentId)
    {
        $signatures = $this->db->table('signatures')
            ->where('document_id', $documentId)
            ->orderBy('signed_at', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'signatures' => $signatures
        ]);
    }

    /**
     * View signature
     */
    public function view($signatureId)
    {
        $signature = $this->db->table('signatures')
            ->where('id', $signatureId)
            ->get()
            ->getRowArray();

        if (!$signature) {
            return redirect()->back()->with('error', 'Signature non trouvée');
        }

        $data = [
            'title' => 'Détails Signature',
            'page_title' => 'Signature',
            'signature' => $signature
        ];

        return view('admin/signatures/view', $data);
    }

    /**
     * Request signature from client
     */
    public function requestSignature()
    {
        $data = $this->request->getPost();
        
        // Create pending signature request
        $signatureData = [
            'document_id' => $data['document_id'],
            'signer_type' => 'client',
            'signer_id' => $data['client_id'],
            'signer_name' => $data['client_name'],
            'signer_email' => $data['client_email'],
            'signature_data' => '',
            'status' => 'pending'
        ];

        $this->db->table('signatures')->insert($signatureData);

        // Send email with signature link
        $emailService = new \App\Libraries\EmailService();
        $emailService->send(
            $data['client_email'],
            'Demande de signature - REBENCIA',
            $this->getSignatureRequestEmail($data)
        );

        return redirect()->back()->with('success', 'Demande de signature envoyée');
    }

    /**
     * Get signature request email template
     */
    private function getSignatureRequestEmail($data)
    {
        $link = base_url('signatures/sign/' . $data['document_id']);
        
        return '
        <h2>Demande de Signature Électronique</h2>
        <p>Bonjour ' . esc($data['client_name']) . ',</p>
        <p>Vous êtes invité à signer le document suivant :</p>
        <p><strong>Document:</strong> ' . esc($data['document_name']) . '</p>
        <p><a href="' . $link . '" style="background:#0d6efd;color:white;padding:12px 30px;text-decoration:none;border-radius:5px;">Signer le Document</a></p>
        <p>Cordialement,<br>REBENCIA</p>';
    }
}
