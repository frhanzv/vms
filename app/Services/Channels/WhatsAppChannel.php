<?php

namespace App\Services\Channels;

use App\Models\InvitationModel;
use App\Models\ClientMessagingCredentialModel;
use App\Models\WhatsappTemplateModel;

class WhatsAppChannel
{
    public function send(int $invitationId, string $notificationType): bool
    {
        $invitationModel = new InvitationModel();
        $credModel       = new ClientMessagingCredentialModel();
        $templateModel   = new WhatsappTemplateModel();

        $invitation = $invitationModel->find($invitationId);
        if (! $invitation) {
            return false;
        }

        $companyId = (int) $invitation['company'];

        if (! $credModel->isActive($companyId, 'whatsapp')) {
            return false;
        }

        $cred = $credModel->getForCompany($companyId, 'whatsapp');
        if (! $cred) {
            return false;
        }

        $template = $templateModel->getTemplate($companyId, $notificationType);
        if (! $template) {
            log_message('info', "WhatsAppChannel: no template configured for '{$notificationType}', company {$companyId}");
            return false;
        }

        $phone = $this->normalizePhone((string) ($invitation['contact'] ?? ''));
        if (empty($phone)) {
            log_message('warning', "WhatsAppChannel: no valid phone for invitation {$invitationId}");
            return false;
        }

        return $this->sendTemplate(
            $cred['phone_number_id'],
            $cred['access_token'],
            $phone,
            $template['template_name'],
            $template['language_code']
        );
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (empty($phone)) {
            return '';
        }
        // Malaysian numbers: 01x → 601x
        if (str_starts_with($phone, '0')) {
            $phone = '6' . $phone;
        }
        return $phone;
    }

    protected function sendTemplate(
        string $phoneNumberId,
        string $accessToken,
        string $to,
        string $templateName,
        string $languageCode
    ): bool {
        $url     = "https://graph.facebook.com/v19.0/{$phoneNumberId}/messages";
        $payload = json_encode([
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'     => $templateName,
                'language' => ['code' => $languageCode],
            ],
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            log_message('error', "WhatsAppChannel: cURL error: {$curlError}");
            return false;
        }

        $data = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($data['messages'])) {
            log_message('info', "WhatsAppChannel: message sent to {$to}");
            return true;
        }

        log_message('error', "WhatsAppChannel: API error ({$httpCode}): {$response}");
        return false;
    }
}
