<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Reports extends BaseController
{
    protected $propertyModel;
    protected $clientModel;
    protected $transactionModel;
    protected $userModel;

    public function __construct()
    {
        $this->propertyModel = model('PropertyModel');
        $this->clientModel = model('ClientModel');
        $this->transactionModel = model('TransactionModel');
        $this->userModel = model('UserModel');
    }

    /**
     * Reports dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Rapports & Exports',
            'page_title' => 'Rapports & Exports'
        ];

        return view('admin/reports/index', $data);
    }

    /**
     * Export properties to Excel
     */
    public function exportProperties()
    {
        $status = $this->request->getGet('status');
        $type = $this->request->getGet('type');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $db = \Config\Database::connect();
        $builder = $db->table('properties p');
        $builder->select('p.*, z.name as zone_name, u.full_name as agent_name, a.name as agency_name');
        $builder->join('zones z', 'z.id = p.zone_id', 'left');
        $builder->join('users u', 'u.id = p.agent_id', 'left');
        $builder->join('agencies a', 'a.id = p.agency_id', 'left');

        if ($status) {
            $builder->where('p.status', $status);
        }
        if ($type) {
            $builder->where('p.type', $type);
        }
        if ($dateFrom) {
            $builder->where('p.created_at >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('p.created_at <=', $dateTo . ' 23:59:59');
        }

        $properties = $builder->get()->getResultArray();

        return $this->generateExcel($properties, 'properties', 'Rapport_Proprietes');
    }

    /**
     * Export clients to Excel
     */
    public function exportClients()
    {
        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $db = \Config\Database::connect();
        $builder = $db->table('clients c');
        $builder->select('c.*, u.full_name as agent_name');
        $builder->join('users u', 'u.id = c.agent_id', 'left');

        if ($type) {
            $builder->where('c.type', $type);
        }
        if ($status) {
            $builder->where('c.status', $status);
        }
        if ($dateFrom) {
            $builder->where('c.created_at >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('c.created_at <=', $dateTo . ' 23:59:59');
        }

        $clients = $builder->get()->getResultArray();

        return $this->generateExcel($clients, 'clients', 'Rapport_Clients');
    }

    /**
     * Export transactions to Excel
     */
    public function exportTransactions()
    {
        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $db = \Config\Database::connect();
        $builder = $db->table('transactions t');
        $builder->select('t.*, p.title as property_title, p.reference as property_ref, 
                         c.full_name as client_name, u.full_name as agent_name');
        $builder->join('properties p', 'p.id = t.property_id', 'left');
        $builder->join('clients c', 'c.id = t.client_id', 'left');
        $builder->join('users u', 'u.id = t.agent_id', 'left');

        if ($type) {
            $builder->where('t.type', $type);
        }
        if ($status) {
            $builder->where('t.status', $status);
        }
        if ($dateFrom) {
            $builder->where('t.completion_date >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('t.completion_date <=', $dateTo);
        }

        $transactions = $builder->orderBy('t.completion_date', 'DESC')->get()->getResultArray();

        return $this->generateExcel($transactions, 'transactions', 'Rapport_Transactions');
    }

    /**
     * Monthly commissions report
     */
    public function exportCommissions()
    {
        $month = $this->request->getGet('month') ?: date('Y-m');
        $agentId = $this->request->getGet('agent_id');

        $db = \Config\Database::connect();
        $builder = $db->table('commissions c');
        $builder->select('c.*, t.reference as transaction_ref, u.full_name as agent_name, 
                         p.title as property_title');
        $builder->join('transactions t', 't.id = c.transaction_id', 'left');
        $builder->join('users u', 'u.id = c.user_id', 'left');
        $builder->join('properties p', 'p.id = t.property_id', 'left');
        $builder->where('DATE_FORMAT(c.created_at, "%Y-%m")', $month);

        if ($agentId) {
            $builder->where('c.user_id', $agentId);
        }

        $commissions = $builder->orderBy('c.created_at', 'DESC')->get()->getResultArray();

        return $this->generateExcel($commissions, 'commissions', 'Rapport_Commissions_' . $month);
    }

    /**
     * Generate Excel file
     */
    private function generateExcel($data, $type, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers based on type
        $headers = $this->getHeaders($type);
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0d6efd']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Fill data
        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($this->getRowData($item, $type) as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Add borders
        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastCol . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
            ]
        ]);

        // Output file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Get headers for Excel
     */
    private function getHeaders($type)
    {
        switch ($type) {
            case 'properties':
                return ['ID', 'Référence', 'Titre', 'Type', 'Statut', 'Prix', 'Zone', 'Agent', 'Agence', 'Date création'];
            case 'clients':
                return ['ID', 'Nom', 'Type', 'Statut', 'Email', 'Téléphone', 'Agent', 'Date création'];
            case 'transactions':
                return ['ID', 'Référence', 'Propriété', 'Client', 'Type', 'Montant', 'Commission %', 'Commission TND', 'Statut', 'Date', 'Agent'];
            case 'commissions':
                return ['ID', 'Transaction', 'Propriété', 'Agent', 'Montant', 'Pourcentage', 'Statut', 'Date'];
            default:
                return [];
        }
    }

    /**
     * Get row data for Excel
     */
    private function getRowData($item, $type)
    {
        switch ($type) {
            case 'properties':
                return [
                    $item['id'],
                    $item['reference'],
                    $item['title'],
                    ucfirst($item['type']),
                    ucfirst($item['status']),
                    number_format($item['price'], 0, ',', ' ') . ' TND',
                    $item['zone_name'],
                    $item['agent_name'],
                    $item['agency_name'],
                    date('d/m/Y', strtotime($item['created_at']))
                ];
            case 'clients':
                return [
                    $item['id'],
                    $item['full_name'],
                    ucfirst($item['type']),
                    ucfirst($item['status']),
                    $item['email'],
                    $item['phone'],
                    $item['agent_name'],
                    date('d/m/Y', strtotime($item['created_at']))
                ];
            case 'transactions':
                return [
                    $item['id'],
                    $item['reference'],
                    $item['property_ref'] . ' - ' . $item['property_title'],
                    $item['client_name'],
                    ucfirst($item['type']),
                    number_format($item['amount'], 0, ',', ' ') . ' TND',
                    $item['commission_percentage'] . '%',
                    number_format($item['commission_amount'], 0, ',', ' ') . ' TND',
                    ucfirst($item['status']),
                    date('d/m/Y', strtotime($item['completion_date'])),
                    $item['agent_name']
                ];
            case 'commissions':
                return [
                    $item['id'],
                    $item['transaction_ref'],
                    $item['property_title'],
                    $item['agent_name'],
                    number_format($item['amount'], 0, ',', ' ') . ' TND',
                    $item['percentage'] . '%',
                    ucfirst($item['status']),
                    date('d/m/Y', strtotime($item['created_at']))
                ];
            default:
                return [];
        }
    }
}
