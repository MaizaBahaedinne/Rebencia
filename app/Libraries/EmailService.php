<?php

namespace App\Libraries;

class EmailService
{
    private $settingModel;
    private $from;
    private $fromName;

    public function __construct()
    {
        $this->settingModel = model('SettingModel');
        $this->from = $this->settingModel->get('smtp_from_email', 'noreply@rebencia.com');
        $this->fromName = $this->settingModel->get('smtp_from_name', 'REBENCIA');
    }

    /**
     * Send email
     */
    public function send($to, $subject, $message, $attachments = [])
    {
        $email = \Config\Services::email();

        // Configure from settings
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => $this->settingModel->get('smtp_host', ''),
            'SMTPPort' => $this->settingModel->get('smtp_port', 587),
            'SMTPUser' => $this->settingModel->get('smtp_user', ''),
            'SMTPPass' => $this->settingModel->get('smtp_password', ''),
            'SMTPCrypto' => 'tls',
            'mailType' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];

        $email->initialize($config);
        $email->setFrom($this->from, $this->fromName);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);

        // Add attachments
        if (!empty($attachments)) {
            foreach ($attachments as $file) {
                $email->attach($file);
            }
        }

        // Send
        if ($email->send()) {
            return true;
        } else {
            log_message('error', 'Email error: ' . $email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Send property notification email
     */
    public function sendPropertyNotification($propertyData, $recipientEmail, $recipientName)
    {
        $subject = "Nouveau Bien - " . $propertyData['title'];
        $message = $this->getPropertyTemplate($propertyData, $recipientName);
        
        return $this->send($recipientEmail, $subject, $message);
    }

    /**
     * Send transaction notification email
     */
    public function sendTransactionNotification($transactionData, $recipientEmail, $recipientName)
    {
        $subject = "Nouvelle Transaction - " . $transactionData['reference'];
        $message = $this->getTransactionTemplate($transactionData, $recipientName);
        
        return $this->send($recipientEmail, $subject, $message);
    }

    /**
     * Send appointment reminder email
     */
    public function sendAppointmentReminder($appointmentData, $recipientEmail, $recipientName)
    {
        $subject = "Rappel Rendez-vous - " . date('d/m/Y H:i', strtotime($appointmentData['scheduled_at']));
        $message = $this->getAppointmentTemplate($appointmentData, $recipientName);
        
        return $this->send($recipientEmail, $subject, $message);
    }

    /**
     * Send commission notification email
     */
    public function sendCommissionNotification($commissionData, $recipientEmail, $recipientName)
    {
        $subject = "Commission " . ucfirst($commissionData['status']) . " - " . number_format($commissionData['amount'], 2) . " TND";
        $message = $this->getCommissionTemplate($commissionData, $recipientName);
        
        return $this->send($recipientEmail, $subject, $message);
    }

    /**
     * Property email template
     */
    private function getPropertyTemplate($property, $recipientName)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; }
                .property-card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                .button { display: inline-block; background: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🏠 REBENCIA</h1>
                    <p>Nouveau Bien Disponible</p>
                </div>
                <div class="content">
                    <p>Bonjour <strong>' . esc($recipientName) . '</strong>,</p>
                    <p>Un nouveau bien correspondant à vos critères vient d\'être ajouté à notre catalogue :</p>
                    
                    <div class="property-card">
                        <h2>' . esc($property['title']) . '</h2>
                        <p><strong>Type:</strong> ' . ucfirst($property['type']) . '</p>
                        <p><strong>Prix:</strong> ' . number_format($property['price'], 0) . ' TND</p>
                        <p><strong>Surface:</strong> ' . $property['surface'] . ' m²</p>
                        <p><strong>Zone:</strong> ' . esc($property['zone']) . '</p>
                        <p><strong>Référence:</strong> ' . $property['reference'] . '</p>
                    </div>
                    
                    <a href="' . base_url('admin/properties/view/' . $property['id']) . '" class="button">Voir le Bien</a>
                </div>
                <div class="footer">
                    <p>REBENCIA - Votre partenaire immobilier</p>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Transaction email template
     */
    private function getTransactionTemplate($transaction, $recipientName)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #198754, #157347); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; }
                .transaction-card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                .amount { font-size: 2em; color: #198754; font-weight: bold; text-align: center; margin: 20px 0; }
                .button { display: inline-block; background: #198754; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>💼 REBENCIA</h1>
                    <p>Nouvelle Transaction</p>
                </div>
                <div class="content">
                    <p>Bonjour <strong>' . esc($recipientName) . '</strong>,</p>
                    <p>Une nouvelle transaction a été créée et vous a été assignée :</p>
                    
                    <div class="transaction-card">
                        <h3>Référence: ' . $transaction['reference'] . '</h3>
                        <p><strong>Type:</strong> ' . ucfirst($transaction['type']) . '</p>
                        <p><strong>Statut:</strong> ' . ucfirst($transaction['status']) . '</p>
                        <div class="amount">' . number_format($transaction['amount'], 2) . ' TND</div>
                        <p><strong>Date:</strong> ' . date('d/m/Y', strtotime($transaction['transaction_date'])) . '</p>
                    </div>
                    
                    <a href="' . base_url('admin/transactions/edit/' . $transaction['id']) . '" class="button">Voir la Transaction</a>
                </div>
                <div class="footer">
                    <p>REBENCIA - Votre partenaire immobilier</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Appointment email template
     */
    private function getAppointmentTemplate($appointment, $recipientName)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ffc107, #ff9800); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; }
                .appointment-card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                .datetime { font-size: 1.5em; color: #ffc107; font-weight: bold; text-align: center; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📅 REBENCIA</h1>
                    <p>Rappel de Rendez-vous</p>
                </div>
                <div class="content">
                    <p>Bonjour <strong>' . esc($recipientName) . '</strong>,</p>
                    <p>Rappel de votre rendez-vous :</p>
                    
                    <div class="appointment-card">
                        <h3>' . esc($appointment['title']) . '</h3>
                        <div class="datetime">
                            📅 ' . date('d/m/Y', strtotime($appointment['scheduled_at'])) . '<br>
                            🕐 ' . date('H:i', strtotime($appointment['scheduled_at'])) . '
                        </div>
                        <p><strong>Lieu:</strong> ' . esc($appointment['location'] ?? 'À définir') . '</p>
                        <p><strong>Notes:</strong> ' . esc($appointment['notes'] ?? '-') . '</p>
                    </div>
                </div>
                <div class="footer">
                    <p>REBENCIA - Votre partenaire immobilier</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Commission email template
     */
    private function getCommissionTemplate($commission, $recipientName)
    {
        $statusColors = [
            'approved' => '#0dcaf0',
            'paid' => '#198754'
        ];
        $color = $statusColors[$commission['status']] ?? '#6c757d';

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, ' . $color . ', ' . $color . 'dd); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; }
                .commission-card { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                .amount { font-size: 2.5em; color: ' . $color . '; font-weight: bold; text-align: center; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>💰 REBENCIA</h1>
                    <p>Commission ' . ucfirst($commission['status']) . '</p>
                </div>
                <div class="content">
                    <p>Bonjour <strong>' . esc($recipientName) . '</strong>,</p>
                    <p>Votre commission a été ' . ($commission['status'] === 'approved' ? 'approuvée' : 'payée') . ' :</p>
                    
                    <div class="commission-card">
                        <div class="amount">' . number_format($commission['amount'], 2) . ' TND</div>
                        <p><strong>Type:</strong> ' . ucfirst($commission['type']) . '</p>
                        <p><strong>Pourcentage:</strong> ' . $commission['percentage'] . '%</p>
                        <p><strong>Transaction:</strong> ' . $commission['transaction_reference'] . '</p>
                    </div>
                </div>
                <div class="footer">
                    <p>REBENCIA - Votre partenaire immobilier</p>
                </div>
            </div>
        </body>
        </html>';
    }
}
