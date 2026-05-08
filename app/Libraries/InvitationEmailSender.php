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
    }

    /**
     * Collect all secondary recipients (host + company admins) for an invitation,
     * excluding the visitor themselves to avoid duplicates.
     *
     * @param array<string, mixed> $invitation
     * @return array<int, array{email: string, full_name: string}>
     */
    protected function getSecondaryRecipients(array $invitation): array
    {
        $recipients = [];
        $seen = [];
        $visitorEmail = strtolower((string) ($invitation['visitor_email'] ?? ''));

        // Host
        $host = $invitation['host_user'] ?? null;
        if (is_array($host) && !empty($host['email'])) {
            $email = strtolower((string) $host['email']);
            if ($email !== $visitorEmail && !isset($seen[$email])) {
                $recipients[] = ['email' => $host['email'], 'full_name' => $host['full_name'] ?? ''];
                $seen[$email] = true;
            }
        }

        // Company admins
        foreach ((array) ($invitation['company_admins'] ?? []) as $admin) {
            if (empty($admin['email'])) {
                continue;
            }
            $email = strtolower((string) $admin['email']);
            if ($email !== $visitorEmail && !isset($seen[$email])) {
                $recipients[] = ['email' => $admin['email'], 'full_name' => $admin['full_name'] ?? ''];
                $seen[$email] = true;
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

        $company = $this->companyModel->find($invitation['company']);
        $invitation['company_name'] = $company ? $company['name'] : $invitation['company'];

        $location = $this->locationModel->find($invitation['location']);
        $invitation['location_name'] = $location ? $location['name'] : $invitation['location'];

        $reason = $this->visitReasonModel->find($invitation['reason']);
        $invitation['reason_name'] = $reason ? $reason['reason'] : $invitation['reason'];

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
                ->select('id, full_name, email, contact_no')
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

    public function send(int $invitationId): bool
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

            $registrationLink = base_url('visitor-registration?token=' . base64_encode((string) $invitationId));

            $templateRaw = $this->settingModel->getSetting(
                $this->emailTemplateService->getStorageKey(EmailTemplateService::PROCESS_INVITATION)
            );
            $templateConfig = $this->emailTemplateService->normalizeTemplate(
                EmailTemplateService::PROCESS_INVITATION,
                $templateRaw ? json_decode((string) $templateRaw, true) : []
            );

            $placeholderContext = [
                'visitor_name' => $invitation['full_name'],
                'company' => $invitation['company_name'],
                'location' => $invitation['location_name'],
                'reason' => $invitation['reason_name'],
                'invited_by' => $invitation['invited_by'],
                'link_expiry_date' => date('d/m/Y', strtotime($invitation['link_expiry'])),
                'registration_link' => $registrationLink,
            ];

            $crudTemplate = $this->emailTemplateModel
                ->where('code', 'INVITATION')
                ->first();
            if (! $crudTemplate) {
                $crudTemplate = $this->emailTemplateModel
                    ->where('code', 'VISITOR_INVITE')
                    ->first();
            }

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
                'schedules' => $invitation['schedules'],
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
                    $this->getSecondaryRecipients($invitation)
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

            $templateRaw = $this->settingModel->getSetting(
                $this->emailTemplateService->getStorageKey(EmailTemplateService::PROCESS_APPROVAL)
            );
            $templateConfig = $this->emailTemplateService->normalizeTemplate(
                EmailTemplateService::PROCESS_APPROVAL,
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

            $crudTemplate = $this->emailTemplateModel
                ->where('code', 'APPROVAL')
                ->first();
            if (! $crudTemplate) {
                $crudTemplate = $this->emailTemplateModel
                    ->where('code', 'VISITOR_REQ_APPROVAL')
                    ->first();
            }

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

            // QR should match visible pass ID format, e.g. "VIS-23".
            $qrCodeData = 'VIS-' . $invitationId;
            $options = new \chillerlan\QRCode\QROptions([
                'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
                'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::L,
                'scale'           => 5,
                'outputBase64'    => false,
            ]);
            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $qrCodeBinary = $qrcode->render($qrCodeData);
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeBinary);
            $qrCodeImageUrl = 'https://quickchart.io/qr?size=240&text=' . rawurlencode($qrCodeData);

            $qrDir = WRITEPATH . 'uploads/email_qr/';
            if (! is_dir($qrDir)) {
                mkdir($qrDir, 0775, true);
            }
            $qrFilePath = $qrDir . 'approval_qr_' . $invitationId . '_' . time() . '.png';
            file_put_contents($qrFilePath, $qrCodeBinary);
            $email->attach($qrFilePath, 'inline', basename($qrFilePath), 'image/png');
            $qrCid = $email->setAttachmentCID($qrFilePath);

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
                'custom_logo_cid' => $customLogoCid,
                'qr_code_text' => $qrCodeData,
                'qr_code_image_url' => $qrCodeImageUrl,
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
                    $this->getSecondaryRecipients($invitation)
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

            $templateRaw = $this->settingModel->getSetting(
                $this->emailTemplateService->getStorageKey(EmailTemplateService::PROCESS_REJECTION)
            );
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

            $crudTemplate = $this->emailTemplateModel
                ->where('code', 'REJECTION')
                ->first();
            if (! $crudTemplate) {
                $crudTemplate = $this->emailTemplateModel
                    ->where('code', 'VISITOR_REQ_REJECT')
                    ->first();
            }

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
                    $this->getSecondaryRecipients($invitation)
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

            $recipients = $this->getSecondaryRecipients($invitation);
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

            $recipients = $this->getSecondaryRecipients($invitation);
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
                     . '<li><strong>IC / Passport:</strong> ' . esc($icPassport) . '</li>'
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
