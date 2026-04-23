<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\VisitReasonModel;
use App\Models\LocationModel;
use App\Models\CompanyModel;
use App\Models\VisitorTypeModel;
use App\Models\SettingModel;
use App\Models\EmailTemplateModel;

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

            $email = new Email();
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
            if (is_array($crudTemplate)) {
                $rawSubject = trim((string) ($crudTemplate['subject'] ?? ''));
                $rawBody = (string) ($crudTemplate['body'] ?? '');
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
            ];

            $message = view('emails/invitation_template', $emailData);

            $email->setFrom('noreply@safeg.com', 'SafeG VMS');
            $email->setTo($invitation['visitor_email']);
            $email->setSubject($customSubject ?: $templateConfig['subject']);
            $email->setMessage($message);

            $result = $email->send();

            if ($result) {
                log_message('info', 'Email sent successfully to: ' . $invitation['visitor_email']);
            } else {
                log_message('error', 'Email sending failed to: ' . $invitation['visitor_email']);
                log_message('error', 'Email error: ' . $email->printDebugger());
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

            $email = new Email();
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
                ->where('code', 'VISITOR_REQ_APPROVAL')
                ->first();

            $customSubject = null;
            $customBodyHtml = null;
            $customColors = null;
            if (is_array($crudTemplate)) {
                $rawSubject = trim((string) ($crudTemplate['subject'] ?? ''));
                $rawBody = (string) ($crudTemplate['body'] ?? '');
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

            // Generate QR Code
            $qrCodeData = 'VIS-' . str_pad((string) $invitationId, 5, '0', STR_PAD_LEFT);
            $options = new \chillerlan\QRCode\QROptions([
                'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
                'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::L,
                'scale'           => 5,
                'outputBase64'    => true,
            ]);
            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $qrCodeBase64 = $qrcode->render($qrCodeData);

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
                'qr_code_base64' => $qrCodeBase64,
            ];

            $message = view('emails/approval_template', $emailData);

            $email->setFrom('noreply@safeg.com', 'SafeG VMS');
            $email->setTo($invitation['visitor_email']);
            $email->setSubject($customSubject ?: $templateConfig['subject']);
            $email->setMessage($message);

            $result = $email->send();

            if ($result) {
                log_message('info', 'Approval email sent successfully to: ' . $invitation['visitor_email']);
            } else {
                log_message('error', 'Approval email sending failed to: ' . $invitation['visitor_email']);
                log_message('error', 'Email error: ' . $email->printDebugger());
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Approval email sending failed: ' . $e->getMessage());

            return false;
        }
    }
}
