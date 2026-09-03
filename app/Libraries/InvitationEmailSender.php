<?php

namespace App\Libraries;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\VisitReasonModel;
use App\Models\LocationModel;
use App\Models\CompanyModel;
use App\Models\VisitorTypeModel;
use App\Models\SettingModel;
use App\Models\EmailTemplateModel;
use App\Models\UserModel;
use App\Models\ClientFormFieldModel;
use App\Models\ClientModel;

/**
 * Sends invitation registration emails (shared by InvitationList and VisitorRegistration).
 */
class InvitationEmailSender
{
    protected InvitationModel $invitationModel;
    protected InvitationScheduleModel $scheduleModel;
    protected VisitReasonModel $visitReasonModel;
    protected LocationModel $locationModel;
    protected CompanyModel $companyModel;
    protected VisitorTypeModel $visitorTypeModel;
    protected SettingModel $settingModel;
    protected EmailTemplateService $emailTemplateService;
    protected EmailTemplateModel $emailTemplateModel;
    protected UserModel $userModel;
    protected \Config\Email $emailConfig;
    protected ClientFormFieldModel $clientFormFieldModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->visitReasonModel = new VisitReasonModel();
        $this->locationModel = new LocationModel();
        $this->companyModel = new CompanyModel();
        $this->visitorTypeModel = new VisitorTypeModel();
        $this->settingModel = new SettingModel();
        $this->emailTemplateService = new EmailTemplateService();
        $this->emailTemplateModel = new EmailTemplateModel();
        $this->userModel = new UserModel();
        $this->emailConfig = config('Email');
        $this->clientFormFieldModel = new ClientFormFieldModel();
    }

    protected function getInvitationEmailDetailFields(array $invitation): array
    {
        $fields = [
            'company' => true,
            'location' => true,
            'reason' => true,
            'invited_by' => true,
            'schedule' => true,
            'host_contact' => false,
            'visitor_type' => false,
            'link_expiry' => true,
        ];
        $clientId = (int) ($invitation['client_id'] ?? $invitation['company_id'] ?? 0);
        if ($clientId <= 0) {
            return $fields;
        }

        $stored = $this->clientFormFieldModel
            ->where('client_id', $clientId)
            ->where('form_type', 'invitation')
            ->findAll();
        $stored = array_column($stored, 'is_enabled', 'field_key');

        foreach (['company_visited' => 'company', 'location' => 'location', 'reason' => 'reason', 'schedule' => 'schedule', 'host_contact' => 'host_contact', 'visitor_type' => 'visitor_type', 'link_expiry' => 'link_expiry'] as $configKey => $emailKey) {
            if (array_key_exists($configKey, $stored)) {
                $fields[$emailKey] = (bool) $stored[$configKey];
            }
        }

        return $fields;
    }

    protected function getConfiguredTemplateRaw(string $process, array $invitation): ?string
    {
        $clientId = (int) ($invitation['client_id'] ?? $invitation['company_id'] ?? 0);
        if ($clientId > 0) {
            $value = $this->settingModel->getSetting(
                $this->emailTemplateService->getClientStorageKey($process, $clientId)
            );
            if ($value) {
                return (string) $value;
            }
        }

        $value = $this->settingModel->getSetting($this->emailTemplateService->getStorageKey($process));
        return $value ? (string) $value : null;
    }

    protected function findConfiguredCrudTemplate(array $codes, array $invitation): ?array
    {
        $clientId = (int) ($invitation['client_id'] ?? $invitation['company_id'] ?? 0);
        foreach ($codes as $code) {
            if ($clientId > 0) {
                $row = $this->emailTemplateModel->where('client_id', $clientId)->where('code', $code)->first();
                if ($row) {
                    return $row;
                }
            }
            $row = $this->emailTemplateModel->where('client_id', null)->where('code', $code)->first();
            if ($row) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Collect all secondary recipients (host + configured roles) for an invitation,
     * excluding the visitor themselves to avoid duplicates.
     *
     * @param array<string, mixed> $invitation
     * @param string|null $eventType
     * @return array<int, array{email: string, full_name: string}>
     */
    protected function getSecondaryRecipients(array $invitation, ?string $eventType = null): array
    {
        $recipients = [];
        $seen = [];
        $visitorEmail = strtolower((string) ($invitation['visitor_email'] ?? ''));

        // Default legacy fallback if no config found
        $configuredRoles = ['host', 'admin', 'clientsuperadmin'];

        if ($eventType) {
            $configRaw = $this->settingModel->getSetting('email_recipient_roles_config');
            if ($configRaw) {
                $config = json_decode((string) $configRaw, true);
                if (is_array($config) && isset($config[$eventType])) {
                    $configuredRoles = $config[$eventType];
                }
            }
        }

        // 1. Host
        if (in_array('host', $configuredRoles)) {
            $host = $invitation['host_user'] ?? null;
            if (is_array($host) && !empty($host['email']) && (!isset($host['receive_email_notifications']) || $host['receive_email_notifications'] == 1)) {
                $email = strtolower((string) $host['email']);
                if ($email !== $visitorEmail && !isset($seen[$email])) {
                    $recipients[] = ['email' => $host['email'], 'full_name' => $host['full_name'] ?? ''];
                    $seen[$email] = true;
                }
            }
        }

        // 2. Configured roles in the company
        $dbRoles = array_filter($configuredRoles, fn($r) => $r !== 'host');
        if (!empty($dbRoles) && !empty($invitation['company'])) {
            $users = $this->userModel
                ->select('id, full_name, email, contact_no')
                ->whereIn('role', array_values($dbRoles))
                ->where('company_id', $invitation['company'])
                ->where('is_active', 1)
                ->where('receive_email_notifications', 1)
                ->findAll();

            foreach ($users as $user) {
                if (empty($user['email'])) {
                    continue;
                }
                $email = strtolower((string) $user['email']);
                if ($email !== $visitorEmail && !isset($seen[$email])) {
                    $recipients[] = ['email' => $user['email'], 'full_name' => $user['full_name'] ?? ''];
                    $seen[$email] = true;
                }
            }
        }

        return $recipients;
    }

    /**
     * Send a copy of an already-rendered email message to secondary recipients.
     */
    protected function sendToSecondaryRecipients(string $subject, string $message, array $recipients): void
    {
        foreach ($recipients as $recipient) {
            try {
                $email = \Config\Services::email();
                $email->initialize([
                    'protocol'    => $this->emailConfig->protocol,
                    'SMTPHost'    => $this->emailConfig->SMTPHost,
                    'SMTPUser'    => $this->emailConfig->SMTPUser,
                    'SMTPPass'    => $this->emailConfig->SMTPPass,
                    'SMTPPort'    => $this->emailConfig->SMTPPort,
                    'SMTPCrypto'  => $this->emailConfig->SMTPCrypto,
                    'SMTPTimeout' => $this->emailConfig->SMTPTimeout,
                    'mailType'    => $this->emailConfig->mailType,
                    'charset'     => $this->emailConfig->charset,
                    'newline'     => $this->emailConfig->newline,
                    'CRLF'        => $this->emailConfig->CRLF,
                ]);
                $email->setMailType('html');
                $email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
                $email->setTo($recipient['email']);
                $email->setSubject($subject);
                $email->setMessage($message);
                $email->send();
            } catch (\Exception $e) {
                log_message('error', 'Secondary recipient email failed to ' . $recipient['email'] . ': ' . $e->getMessage());
            }
        }
    }

    public function invitationsSupportVisitorType(): bool
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists('visitor_types')) {
                $cached = false;

                return false;
            }
            $fields = array_map('strtolower', $db->getFieldNames('invitations'));
            $cached = in_array('visitor_type_id', $fields, true);
        } catch (\Throwable $e) {
            log_message('debug', 'invitationsSupportVisitorType: ' . $e->getMessage());
            $cached = false;
        }

        return $cached;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getInvitationDetails(int $invitationId): ?array
    {
        $invitation = $this->invitationModel->find($invitationId);
        if (! $invitation) {
            return null;
        }

        $companyValue = $invitation['company'] ?? '';
        $company = is_numeric($companyValue)
            ? $this->companyModel->find((int) $companyValue)
            : null;
        $invitation['company_name'] = $company['name']
            ?? ((string) $companyValue !== '' ? (string) $companyValue : 'Not specified');

        // The invitation company is the visitor's employer. The client is the
        // organisation/site being visited (for example, GXO).
        $clientId = (int) ($invitation['client_id'] ?? 0);
        $client = $clientId > 0 ? (new ClientModel())->find($clientId) : null;
        $invitation['client_name'] = trim((string) ($client['name'] ?? ''));
        if ($invitation['client_name'] === '') {
            $invitation['client_name'] = 'Not specified';
        }

        $locationValue = $invitation['location'] ?? '';
        $location = is_numeric($locationValue)
            ? $this->locationModel->find((int) $locationValue)
            : null;
        $resolvedLocation = $location
            ? trim(($location['branch'] ?? '') . ' - ' . ($location['location_access'] ?? ''), ' -')
            : '';
        $invitation['location_name'] = $resolvedLocation !== ''
            ? $resolvedLocation
            : ((string) $locationValue !== '' ? (string) $locationValue : 'Not specified');

        $reasonValue = $invitation['reason'] ?? '';
        $reason = is_numeric($reasonValue)
            ? $this->visitReasonModel->find((int) $reasonValue)
            : null;
        $invitation['reason_name'] = $reason['reason']
            ?? ((string) $reasonValue !== '' ? (string) $reasonValue : 'Not specified');

        $invitation['visitor_type_name'] = '';
        if ($this->invitationsSupportVisitorType() && ! empty($invitation['visitor_type_id'])) {
            $vt = $this->visitorTypeModel->find((int) $invitation['visitor_type_id']);
            $invitation['visitor_type_name'] = $vt ? $vt['name'] : '';
        }

        $schedules = $this->scheduleModel->where('invitation_id', $invitationId)->findAll();
        $invitation['schedules'] = $schedules;

        $invitation['visitor_email'] = $invitation['visitor_email'] ?? $invitation['contact'] . '@example.com';

        // Resolve host user via staff_id
        $invitation['host_user'] = null;
        if (!empty($invitation['staff_id'])) {
            $invitation['host_user'] = $this->userModel
                ->select('id, full_name, email, contact_no, receive_email_notifications')
                ->where('staff_id', $invitation['staff_id'])
                ->where('is_active', 1)
                ->first();
        }

        // Resolve company admins (clientsuperadmin + admin) for the invitation's company
        $invitation['company_admins'] = [];
        if (!empty($invitation['company'])) {
            $invitation['company_admins'] = $this->userModel->getCompanyAdmins((int) $invitation['company']);
        }

        return $invitation;
    }

    public function send(int $invitationId, ?string $registrationLinkOverride = null): bool
    {
        try {
            $invitation = $this->getInvitationDetails($invitationId);

            if (! $invitation) {
                log_message('error', 'Invitation not found for ID: ' . $invitationId);

                return false;
            }

            if (empty($invitation['visitor_email'])) {
                log_message('warning', 'No visitor email found for invitation ID: ' . $invitationId);

                return false;
            }

            $email = \Config\Services::email();
            $email->initialize([
                'protocol' => $this->emailConfig->protocol,
                'SMTPHost' => $this->emailConfig->SMTPHost,
                'SMTPUser' => $this->emailConfig->SMTPUser,
                'SMTPPass' => $this->emailConfig->SMTPPass,
                'SMTPPort' => $this->emailConfig->SMTPPort,
                'SMTPCrypto' => $this->emailConfig->SMTPCrypto,
                'SMTPTimeout' => $this->emailConfig->SMTPTimeout,
                'mailType' => $this->emailConfig->mailType,
                'charset' => $this->emailConfig->charset,
                'newline' => $this->emailConfig->newline,
                'CRLF' => $this->emailConfig->CRLF,
            ]);
            $email->setMailType('html');

            $registrationLink = $registrationLinkOverride
                ?: base_url('visitor-registration?token=' . base64_encode((string) $invitationId));

            $templateRaw = $this->getConfiguredTemplateRaw(EmailTemplateService::PROCESS_INVITATION, $invitation);
            $templateConfig = $this->emailTemplateService->normalizeTemplate(
                EmailTemplateService::PROCESS_INVITATION,
                $templateRaw ? json_decode((string) $templateRaw, true) : []
            );
            $detailFields = $this->getInvitationEmailDetailFields($invitation);
            if (! $detailFields['link_expiry']) {
                $templateConfig['notes_items'] = array_values(array_filter(
                    (array) ($templateConfig['notes_items'] ?? []),
                    static fn ($note): bool => ! str_contains(strtolower((string) $note), 'expir')
                        && ! str_contains((string) $note, '{{link_expiry_date}}')
                ));
            }

            $placeholderContext = [
                'visitor_name' => $invitation['full_name'],
                'company' => $invitation['company_name'],
                'location' => $invitation['location_name'],
                'reason' => $invitation['reason_name'],
                'invited_by' => $invitation['invited_by'],
                'link_expiry_date' => ! empty($invitation['link_expiry'])
                    ? date('d/m/Y', strtotime((string) $invitation['link_expiry']))
                    : '',
                'registration_link' => $registrationLink,
            ];

            $crudTemplate = $this->findConfiguredCrudTemplate(['INVITATION', 'VISITOR_INVITE'], $invitation);

            $customSubject = null;
            $customBodyHtml = null;
            $customColors = null;
            $customLogoCid = null;
            if (is_array($crudTemplate)) {
                $rawSubject = trim((string) ($crudTemplate['subject'] ?? ''));
                $rawBody = (string) ($crudTemplate['body'] ?? '');
                $logoUrl = $crudTemplate['logo_url'] ?? null;
                if (!empty($logoUrl)) {
                    $logoPath = FCPATH . ltrim($logoUrl, '/');
                    if (is_file($logoPath)) {
                        $email->attach($logoPath, 'inline', basename($logoPath));
                        $customLogoCid = $email->setAttachmentCID($logoPath);
                    }
                }
                if ($rawSubject !== '') {
                    $customSubject = $this->emailTemplateService->applyPlaceholders($rawSubject, $placeholderContext);
                }
                if (trim($rawBody) !== '') {
                    $customBodyText = $this->emailTemplateService->applyPlaceholders($rawBody, $placeholderContext);
                    $customBodyHtml = nl2br(esc($customBodyText));
                }
                $customColors = [
                    'primary_color' => $crudTemplate['primary_color'] ?? null,
                    'content_bg_color' => $crudTemplate['content_bg_color'] ?? null,
                    'text_color' => $crudTemplate['text_color'] ?? null,
                ];
            }

            $emailData = [
                'visitor_name' => $invitation['full_name'],
                'company' => $invitation['company_name'],
                'location' => $invitation['location_name'],
                'reason' => $invitation['reason_name'],
                'other_reason' => $invitation['other_reason'],
                'invited_by' => $invitation['invited_by'],
                'host_contact' => trim((string) (($invitation['host_user']['contact_no'] ?? '') ?: ($invitation['host_contact'] ?? ''))),
                'visitor_type' => $invitation['visitor_type_name'] ?? '',
                'schedules' => $invitation['schedules'],
                'detail_fields' => $detailFields,
                'registration_link' => $registrationLink,
                'link_expiry' => $invitation['link_expiry'],
                'template' => $templateConfig,
                'intro_line' => $this->emailTemplateService->applyPlaceholders($templateConfig['intro_line'], $placeholderContext),
                'notes_items' => array_map(
                    fn ($item) => $this->emailTemplateService->applyPlaceholders((string) $item, $placeholderContext),
                    $templateConfig['notes_items']
                ),
                'custom_body_html' => $customBodyHtml,
                'custom_colors' => $customColors,
                'custom_logo_cid' => $customLogoCid,
            ];

            $message = view('emails/invitation_template', $emailData);

            $email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $email->setTo($invitation['visitor_email']);
            $email->setSubject($customSubject ?: $templateConfig['subject']);
            $email->setMessage($message);

            $result = $email->send();

            if ($result) {
                log_message('info', 'Email sent successfully to: ' . $invitation['visitor_email']);
                $this->sendToSecondaryRecipients(
                    $customSubject ?: $templateConfig['subject'],
                    $message,
                    $this->getSecondaryRecipients($invitation, EmailTemplateService::PROCESS_INVITATION)
                );
            } else {
                log_message('error', 'Email sending failed to: ' . $invitation['visitor_email']);
                log_message('error', 'Email error: ' . $email->printDebugger(['headers', 'subject']));
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Email sending failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Walk-in visitors have already supplied their details at the kiosk, so the
     * invitation email button takes them straight to the safety briefing.
     */
    public function sendWalkInBriefing(int $invitationId): bool
    {
        $token = base64_encode((string) $invitationId);
        $briefingLink = (new InvitationProcessFlowService())
            ->buildStepUrl('security_briefing', $token)
            ?? base_url('security/briefing?' . http_build_query([
                'token' => $token,
                'flow_step' => 'security_briefing',
            ]));

        return $this->send($invitationId, $briefingLink);
    }

    public function sendApproval(int $invitationId): bool
    {
        try {
            $invitation = $this->getInvitationDetails($invitationId);

            if (! $invitation) {
                log_message('error', 'Invitation not found for ID: ' . $invitationId);

                return false;
            }

            if (empty($invitation['visitor_email'])) {
                log_message('warning', 'No visitor email found for invitation ID: ' . $invitationId);

                return false;
            }

            $email = \Config\Services::email();
            $email->initialize([
                'protocol' => $this->emailConfig->protocol,
                'SMTPHost' => $this->emailConfig->SMTPHost,
                'SMTPUser' => $this->emailConfig->SMTPUser,
                'SMTPPass' => $this->emailConfig->SMTPPass,
                'SMTPPort' => $this->emailConfig->SMTPPort,
                'SMTPCrypto' => $this->emailConfig->SMTPCrypto,
                'SMTPTimeout' => $this->emailConfig->SMTPTimeout,
                'mailType' => $this->emailConfig->mailType,
                'charset' => $this->emailConfig->charset,
                'newline' => $this->emailConfig->newline,
                'CRLF' => $this->emailConfig->CRLF,
            ]);
            $email->setMailType('html');

            $templateRaw = $this->getConfiguredTemplateRaw(EmailTemplateService::PROCESS_APPROVAL, $invitation);
            $templateConfig = $this->emailTemplateService->normalizeTemplate(
                EmailTemplateService::PROCESS_APPROVAL,
                $templateRaw ? json_decode((string) $templateRaw, true) : []
            );
            $detailFields = $this->getInvitationEmailDetailFields($invitation);

            $placeholderContext = [
                'visitor_name' => $invitation['full_name'],
                'company' => $invitation['client_name'],
                'location' => $invitation['location_name'],
                'reason' => $invitation['reason_name'],
                'invited_by' => $invitation['invited_by'],
                'link_expiry_date' => date('d/m/Y', strtotime($invitation['link_expiry'])),
            ];

            $crudTemplate = $this->findConfiguredCrudTemplate(['APPROVAL', 'VISITOR_REQ_APPROVAL'], $invitation);

            $customSubject = null;
            $customBodyHtml = null;
            $customColors = null;
            $customLogoCid = null;
            if (is_array($crudTemplate)) {
                $rawSubject = trim((string) ($crudTemplate['subject'] ?? ''));
                $rawBody = (string) ($crudTemplate['body'] ?? '');
                $logoUrl = $crudTemplate['logo_url'] ?? null;
                if (!empty($logoUrl)) {
                    $logoPath = FCPATH . ltrim($logoUrl, '/');
                    if (is_file($logoPath)) {
                        $email->attach($logoPath, 'inline', basename($logoPath));
                        $customLogoCid = $email->setAttachmentCID($logoPath);
                    }
                }
                if ($rawSubject !== '') {
                    $customSubject = $this->emailTemplateService->applyPlaceholders($rawSubject, $placeholderContext);
                }
                if (trim($rawBody) !== '') {
                    $customBodyText = $this->emailTemplateService->applyPlaceholders($rawBody, $placeholderContext);
                    $customBodyHtml = nl2br(esc($customBodyText));
                }
                $customColors = [
                    'primary_color' => $crudTemplate['primary_color'] ?? null,
                    'content_bg_color' => $crudTemplate['content_bg_color'] ?? null,
                    'text_color' => $crudTemplate['text_color'] ?? null,
                ];
            }

            // The QR contains only a random numeric credential. Personal data is
            // resolved by the authenticated SafeG app after scanning the code.
            $passIdDisplay = 'VIS-' . $invitationId;
            $qrCodeData = (new \App\Services\InvitationQrService())->issue(
                $invitationId,
                isset($invitation['client_id']) ? (int) $invitation['client_id'] : null
            );

            $options = new \chillerlan\QRCode\QROptions([
                'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
                'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::L,
                'scale'           => 5,
                'outputBase64'    => false,
            ]);
            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $qrCodeBinary = $qrcode->render($qrCodeData);
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeBinary);

            $qrDir = WRITEPATH . 'uploads/email_qr/';
            if (! is_dir($qrDir)) {
                mkdir($qrDir, 0775, true);
            }
            $qrFilePath = $qrDir . 'approval_qr_' . $invitationId . '_' . time() . '.png';
            file_put_contents($qrFilePath, $qrCodeBinary);
            // When attaching a file path, let CodeIgniter read the file and
            // determine its MIME type. Passing a MIME type here makes the
            // framework treat the path string itself as buffered image data.
            $email->attach($qrFilePath, 'inline', basename($qrFilePath));
            $qrCid = $email->setAttachmentCID($qrFilePath);

            $hostUser = is_array($invitation['host_user'] ?? null) ? $invitation['host_user'] : [];
            $hostName = trim((string) ($hostUser['full_name'] ?? $invitation['invited_by'] ?? ''));
            $hostContact = trim((string) ($hostUser['contact_no'] ?? $invitation['host_contact'] ?? ''));

            $emailData = [
                'visitor_name' => $invitation['full_name'],
                'visitor_contact' => $invitation['contact'] ?? '',
                'visitor_company' => $invitation['company_name'],
                'company' => $invitation['client_name'],
                'location' => $invitation['location_name'],
                'reason' => $invitation['reason_name'],
                'other_reason' => $invitation['other_reason'],
                'invited_by' => $invitation['invited_by'],
                'host_name' => $hostName,
                'host_contact' => $hostContact,
                'schedules' => ! empty($detailFields['schedule']) ? $invitation['schedules'] : [],
                'template' => $templateConfig,
                'intro_line' => $this->emailTemplateService->applyPlaceholders($templateConfig['intro_line'], $placeholderContext),
                'notes_items' => array_map(
                    fn ($item) => $this->emailTemplateService->applyPlaceholders((string) $item, $placeholderContext),
                    $templateConfig['notes_items']
                ),
                'custom_body_html' => $customBodyHtml,
                'custom_colors' => $customColors,
                'custom_logo_cid' => $customLogoCid,
                'qr_code_text' => $passIdDisplay,
                'visitor_id_document_line' => '',
                'qr_code_image_url' => null,
                'qr_code_base64' => $qrCodeBase64,
                'qr_code_cid' => $qrCid,
            ];

            $message = view('emails/approval_template', $emailData);

            $email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $email->setTo($invitation['visitor_email']);
            $email->setSubject($customSubject ?: $templateConfig['subject']);
            $email->setMessage($message);

            $result = $email->send();

            if ($result) {
                log_message('info', 'Approval email sent successfully to: ' . $invitation['visitor_email']);
                $this->sendToSecondaryRecipients(
                    $customSubject ?: $templateConfig['subject'],
                    $message,
                    $this->getSecondaryRecipients($invitation, EmailTemplateService::PROCESS_APPROVAL)
                );
            } else {
                log_message('error', 'Approval email sending failed to: ' . $invitation['visitor_email']);
                log_message('error', 'Email error: ' . $email->printDebugger(['headers', 'subject']));
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Approval email sending failed: ' . $e->getMessage());

            return false;
        }
    }

    public function sendRejection(int $invitationId): bool
    {
        try {
            $invitation = $this->getInvitationDetails($invitationId);

            if (! $invitation) {
                log_message('error', 'Invitation not found for ID: ' . $invitationId);
                return false;
            }

            if (empty($invitation['visitor_email'])) {
                log_message('warning', 'No visitor email found for invitation ID: ' . $invitationId);
                return false;
            }

            $email = \Config\Services::email();
            $email->initialize([
                'protocol' => $this->emailConfig->protocol,
                'SMTPHost' => $this->emailConfig->SMTPHost,
                'SMTPUser' => $this->emailConfig->SMTPUser,
                'SMTPPass' => $this->emailConfig->SMTPPass,
                'SMTPPort' => $this->emailConfig->SMTPPort,
                'SMTPCrypto' => $this->emailConfig->SMTPCrypto,
                'SMTPTimeout' => $this->emailConfig->SMTPTimeout,
                'mailType' => $this->emailConfig->mailType,
                'charset' => $this->emailConfig->charset,
                'newline' => $this->emailConfig->newline,
                'CRLF' => $this->emailConfig->CRLF,
            ]);
            $email->setMailType('html');

            $templateRaw = $this->getConfiguredTemplateRaw(EmailTemplateService::PROCESS_REJECTION, $invitation);
            $templateConfig = $this->emailTemplateService->normalizeTemplate(
                EmailTemplateService::PROCESS_REJECTION,
                $templateRaw ? json_decode((string) $templateRaw, true) : []
            );

            $placeholderContext = [
                'visitor_name' => $invitation['full_name'],
                'company' => $invitation['company_name'],
                'location' => $invitation['location_name'],
                'reason' => $invitation['reason_name'],
                'invited_by' => $invitation['invited_by'],
                'link_expiry_date' => date('d/m/Y', strtotime($invitation['link_expiry'])),
            ];

            $crudTemplate = $this->findConfiguredCrudTemplate(['REJECTION', 'VISITOR_REQ_REJECT'], $invitation);

            $customSubject = null;
            $customBodyHtml = null;
            $customColors = null;
            $customLogo = null;
            $customLogoCid = null;
            if (is_array($crudTemplate)) {
                $rawSubject = trim((string) ($crudTemplate['subject'] ?? ''));
                $rawBody = (string) ($crudTemplate['body'] ?? '');
                $customLogo = $crudTemplate['logo_url'] ?? null;
                if (!empty($customLogo)) {
                    $logoPath = FCPATH . ltrim($customLogo, '/');
                    if (is_file($logoPath)) {
                        $email->attach($logoPath, 'inline', basename($logoPath));
                        $customLogoCid = $email->setAttachmentCID($logoPath);
                    }
                }
                if ($rawSubject !== '') {
                    $customSubject = $this->emailTemplateService->applyPlaceholders($rawSubject, $placeholderContext);
                }
                if (trim($rawBody) !== '') {
                    $customBodyText = $this->emailTemplateService->applyPlaceholders($rawBody, $placeholderContext);
                    $customBodyHtml = nl2br(esc($customBodyText));
                }
                $customColors = [
                    'primary_color' => $crudTemplate['primary_color'] ?? null,
                    'content_bg_color' => $crudTemplate['content_bg_color'] ?? null,
                    'text_color' => $crudTemplate['text_color'] ?? null,
                ];
            }

            $emailData = [
                'visitor_name' => $invitation['full_name'],
                'company' => $invitation['company_name'],
                'location' => $invitation['location_name'],
                'reason' => $invitation['reason_name'],
                'other_reason' => $invitation['other_reason'],
                'invited_by' => $invitation['invited_by'],
                'schedules' => $invitation['schedules'],
                'template' => $templateConfig,
                'intro_line' => $this->emailTemplateService->applyPlaceholders($templateConfig['intro_line'], $placeholderContext),
                'notes_items' => array_map(
                    fn ($item) => $this->emailTemplateService->applyPlaceholders((string) $item, $placeholderContext),
                    $templateConfig['notes_items']
                ),
                'custom_body_html' => $customBodyHtml,
                'custom_colors' => $customColors,
                'custom_logo' => $customLogo,
                'custom_logo_cid' => $customLogoCid,
            ];

            $message = view('emails/rejection_template', $emailData);

            $email->setFrom($this->emailConfig->fromEmail, $this->emailConfig->fromName);
            $email->setTo($invitation['visitor_email']);
            $email->setSubject($customSubject ?: $templateConfig['subject']);
            $email->setMessage($message);

            $result = $email->send();

            if ($result) {
                log_message('info', 'Rejection email sent successfully to: ' . $invitation['visitor_email']);
                $this->sendToSecondaryRecipients(
                    $customSubject ?: $templateConfig['subject'],
                    $message,
                    $this->getSecondaryRecipients($invitation, EmailTemplateService::PROCESS_REJECTION)
                );
            } else {
                log_message('error', 'Rejection email sending failed to: ' . $invitation['visitor_email']);
                log_message('error', 'Email error: ' . $email->printDebugger(['headers', 'subject']));
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Rejection email sending failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Notify host + admins that a visitor has checked in.
     * The visitor is physically present so they are not emailed.
     */
    public function sendCheckIn(int $invitationId): bool
    {
        try {
            $invitation = $this->getInvitationDetails($invitationId);
            if (! $invitation) {
                return false;
            }

            $recipients = $this->getSecondaryRecipients($invitation, 'CHECK_IN');
            if (empty($recipients)) {
                return true;
            }

            $subject = 'Visitor Check-In: ' . ($invitation['full_name'] ?? 'Unknown');
            $body    = '<p>Hello,</p>'
                     . '<p><strong>' . esc($invitation['full_name'] ?? '') . '</strong> has checked in.</p>'
                     . '<ul>'
                     . '<li><strong>Time:</strong> ' . date('d/m/Y H:i:s') . '</li>'
                     . '<li><strong>Location:</strong> ' . esc($invitation['location_name'] ?? '') . '</li>'
                     . '<li><strong>Purpose:</strong> ' . esc($invitation['reason_name'] ?? '') . '</li>'
                     . '</ul>';

            $this->sendToSecondaryRecipients($subject, $body, $recipients);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'sendCheckIn failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify host + admins that a visitor has checked out.
     */
    public function sendCheckOut(int $invitationId): bool
    {
        try {
            $invitation = $this->getInvitationDetails($invitationId);
            if (! $invitation) {
                return false;
            }

            $recipients = $this->getSecondaryRecipients($invitation, 'CHECK_OUT');
            if (empty($recipients)) {
                return true;
            }

            $subject = 'Visitor Check-Out: ' . ($invitation['full_name'] ?? 'Unknown');
            $body    = '<p>Hello,</p>'
                     . '<p><strong>' . esc($invitation['full_name'] ?? '') . '</strong> has checked out.</p>'
                     . '<ul>'
                     . '<li><strong>Time:</strong> ' . date('d/m/Y H:i:s') . '</li>'
                     . '<li><strong>Location:</strong> ' . esc($invitation['location_name'] ?? '') . '</li>'
                     . '</ul>';

            $this->sendToSecondaryRecipients($subject, $body, $recipients);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'sendCheckOut failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify company admins that a blacklisted person attempted entry.
     * Takes company_id directly since there may be no invitation record.
     */
    public function sendBlacklistFlagged(int $companyId, string $name, string $icPassport, string $reason = ''): bool
    {
        try {
            $admins = $this->userModel->getCompanyAdmins($companyId);
            if (empty($admins)) {
                return true;
            }

            $subject = '[Security Alert] Blacklisted individual detected';
            $body    = '<p>A blacklisted individual attempted entry.</p>'
                     . '<ul>'
                     . '<li><strong>Name:</strong> ' . esc($name) . '</li>'
                     . '<li><strong>IC / Passport:</strong> ' . esc(mask_ic_passport($icPassport)) . '</li>'
                     . '<li><strong>Reason:</strong> ' . esc($reason ?: 'N/A') . '</li>'
                     . '<li><strong>Time:</strong> ' . date('d/m/Y H:i:s') . '</li>'
                     . '</ul>';

            $recipients = array_map(fn($a) => ['email' => $a['email'], 'full_name' => $a['full_name']], $admins);
            $this->sendToSecondaryRecipients($subject, $body, $recipients);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'sendBlacklistFlagged failed: ' . $e->getMessage());
            return false;
        }
    }
}
