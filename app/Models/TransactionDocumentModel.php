<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionDocumentModel extends Model
{
    protected $table = 'transaction_documents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'transaction_id', 'document_type', 'file_name', 'file_path', 
        'file_size', 'mime_type', 'uploaded_by', 'description', 'version'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Get documents for transaction
     */
    public function getForTransaction($transactionId)
    {
        return $this->select('transaction_documents.*, CONCAT(users.first_name, " ", users.last_name) as uploader_name')
            ->join('users', 'users.id = transaction_documents.uploaded_by', 'left')
            ->where('transaction_id', $transactionId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get latest version of document
     */
    public function getLatestVersion($transactionId, $documentType)
    {
        return $this->where('transaction_id', $transactionId)
            ->where('document_type', $documentType)
            ->orderBy('version', 'DESC')
            ->first();
    }
}
