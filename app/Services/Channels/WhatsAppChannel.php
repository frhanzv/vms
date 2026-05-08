<?php

namespace App\Services\Channels;

use App\Models\InvitationModel;
use App\Models\ClientMessagingCredentialModel;
use App\Models\WhatsappTemplateModel;
use App\Models\UserModel;
use App\Services\PlatformNotificationService;

class WhatsAppChannel
{
    public function send(int $invitationId, string $notificationType): bool
    {
        $invitationModel = new InvitationModel();
        $credModel       = new ClientMessagingCredentialModel();
        $templateModel   = new WhatsappTemplateModel();
        $userModel       = new UserModel();

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

        $phoneNumberId  = $cred['phone_number_id'];
        $accessToken    = $cred['access_token'];
        $templateName   = $template['template_name'];
        $languageCode   = $template['language_code'];

        // Send to visitor
        $visitorPhone = $this->normalizePhone((string) ($invitation['contact'] ?? ''));
        $result = false;
        if (! empty($visitorPhone)) {
            $result = $this->sendTemplate($phoneNumberId, $accessToken, $visitorPhone, $templateName, $languageCode, $companyId);
        } else {
            log_message('warning', "WhatsAppChannel: no valid phone for invitation {$invitationId}");
        }

        // Collect secondary recipients: host + company admins
        $secondaryPhones = [];
        $seen = [$visitorPhone => true];

        if (! empty($invitation['staff_id'])) {
            $host = $userModel->select('contact_no')
                ->where('staff_id', $invitation['staff_id'])
                ->where('is_active', 1)
                ->first();
            if ($host && ! empty($host['contact_no'])) {
                $normalized = $this->normalizePhone((string) $host['contact_no']);
                if ($normalized && ! isset($seen[$normalized])) {
                    $secondaryPhones[] = $normalized;
                    $seen[$normalized] = true;
                }
            }
        }

        foreach ($userModel->getCompanyAdmins($companyId) as $admin) {
            if (empty($admin['contact_no'])) {
                continue;
            }
            $normalized = $this->normalizePhone((string) $admin['contact_no']);
            if ($normalized && ! isset($seen[$normalized])) {
                $secondaryPhones[] = $normalized;
                $seen[$normalized] = true;
            }
        }

        foreach ($secondaryPhones as $phone) {
            $this->sendTemplate($phoneNumberId, $accessToken, $phone, $templateName, $languageCode, $companyId);
        }

        return $result;
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
        string $languageCode,
        int $companyId = 0
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

        if ($httpCode === 401 && $companyId > 0) {
            (new PlatformNotificationService())->notifyCredentialFailure($companyId, 'whatsapp');
        }

        return false;
    }
}
