<?php

namespace App\Libraries;

class WhatsAppService
{
    private $accountSid;
    private $authToken;
    private $whatsappNumber;
    private $enabled;

    public function __construct()
    {
        $settingModel = model('SettingModel');
        $this->accountSid = $settingModel->get('twilio_account_sid', '');
        $this->authToken = $settingModel->get('twilio_auth_token', '');
        $this->whatsappNumber = $settingModel->get('twilio_whatsapp_number', '');
        $this->enabled = $settingModel->get('enable_whatsapp', '0') == '1';
    }

    /**
     * Send WhatsApp message
     */
    public function send($to, $message)
    {
        if (!$this->enabled || empty($this->accountSid) || empty($this->authToken)) {
            return false;
        }

        // Format numbers
        $from = 'whatsapp:' . $this->whatsappNumber;
        $to = 'whatsapp:' . $to;

        // Twilio API call
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";

        $data = [
            'From' => $from,
            'To' => $to,
            'Body' => $message
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, $this->accountSid . ':' . $this->authToken);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    /**
     * Send property notification
     */
    public function sendPropertyNotification($clientPhone, $propertyData)
    {
        $message = "🏠 *REBENCIA - Nouveau Bien*\n\n";
        $message .= "*" . $propertyData['title'] . "*\n";
        $message .= "📍 " . $propertyData['zone'] . "\n";
        $message .= "💰 " . number_format($propertyData['price'], 0) . " TND\n";
        $message .= "📏 " . $propertyData['surface'] . " m²\n\n";
        $message .= "Voir: " . base_url('properties/view/' . $propertyData['id']);

        return $this->send($clientPhone, $message);
    }

    /**
     * Send appointment reminder
     */
    public function sendAppointmentReminder($clientPhone, $appointmentData)
    {
        $message = "📅 *REBENCIA - Rappel Rendez-vous*\n\n";
        $message .= $appointmentData['title'] . "\n";
        $message .= "🕐 " . date('d/m/Y H:i', strtotime($appointmentData['scheduled_at'])) . "\n";
        if ($appointmentData['location']) {
            $message .= "📍 " . $appointmentData['location'] . "\n";
        }
        $message .= "\nÀ bientôt ! 👋";

        return $this->send($clientPhone, $message);
    }

    /**
     * Send transaction update
     */
    public function sendTransactionUpdate($clientPhone, $transactionData, $status)
    {
        $statusMessages = [
            'signed' => '🎉 Félicitations ! Votre transaction a été signée.',
            'completed' => '✅ Transaction complétée avec succès !',
            'cancelled' => '❌ La transaction a été annulée.'
        ];

        $message = "*REBENCIA - Mise à jour Transaction*\n\n";
        $message .= $statusMessages[$status] ?? 'Mise à jour de votre transaction';
        $message .= "\n\nRéférence: " . $transactionData['reference'];
        $message .= "\nMontant: " . number_format($transactionData['amount'], 2) . " TND";

        return $this->send($clientPhone, $message);
    }
}
