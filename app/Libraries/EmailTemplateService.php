<?php

namespace App\Libraries;

class EmailTemplateService
{
    public const INVITATION_KEY = 'email_template_invitation';
    public const PROCESS_INVITATION = 'invitation';
    public const PROCESS_REGISTRATION_SUBMITTED = 'registration_submitted';
    public const PROCESS_APPROVAL = 'approval';
    public const PROCESS_REJECTION = 'rejection';
    public const PROCESS_REMINDER = 'reminder';

    public function getProcessOptions(): array
    {
        return [
            ['key' => self::PROCESS_INVITATION, 'label' => 'Invitation Email'],
            ['key' => self::PROCESS_REGISTRATION_SUBMITTED, 'label' => 'Registration Submitted Email'],
            ['key' => self::PROCESS_APPROVAL, 'label' => 'Approval Email'],
            ['key' => self::PROCESS_REJECTION, 'label' => 'Rejection Email'],
            ['key' => self::PROCESS_REMINDER, 'label' => 'Reminder Email'],
        ];
    }

    public function isSupportedProcess(string $process): bool
    {
        foreach ($this->getProcessOptions() as $option) {
            if ($option['key'] === $process) {
                return true;
            }
        }

        return false;
    }

    public function getStorageKey(string $process): string
    {
        if ($process === self::PROCESS_INVITATION) {
            // Keep legacy key for backward compatibility.
            return self::INVITATION_KEY;
        }

        return 'email_template_' . $process;
    }

    public function getDefaultTemplate(string $process): array
    {
        $defaults = [
            self::PROCESS_INVITATION => [
                'subject' => 'Visitor Invitation - Complete Your Registration',
                'header_title' => 'Visitor Invitation',
                'intro_line' => 'You have been invited to visit {{company}}. Please complete your registration by clicking the button below.',
                'button_text' => 'Complete Registration',
                'notes_title' => 'Important Notes',
                'notes_items' => [
                    'Please complete your registration before your visit',
                    'Bring a valid ID (IC/Passport) for verification',
                    'This invitation expires on: {{link_expiry_date}}',
                    'Contact security if you have any questions',
                ],
            ],
            self::PROCESS_REGISTRATION_SUBMITTED => [
                'subject' => 'Registration Submitted Successfully',
                'header_title' => 'Registration Submitted',
                'intro_line' => 'Dear {{visitor_name}}, your registration has been submitted successfully for {{company}}.',
                'button_text' => 'View Details',
                'notes_title' => 'Notes',
                'notes_items' => [
                    'Our team will review your submission.',
                    'You will receive another email once your request is processed.',
                ],
            ],
            self::PROCESS_APPROVAL => [
                'subject' => 'Visitor Request Approved',
                'header_title' => 'Request Approved',
                'intro_line' => 'Dear {{visitor_name}}, your visitor request to {{company}} has been approved.',
                'button_text' => 'Open Pass',
                'notes_title' => 'Important Notes',
                'notes_items' => [
                    'Please bring a valid ID during check-in.',
                    'Follow on-site safety and security instructions.',
                ],
            ],
            self::PROCESS_REJECTION => [
                'subject' => 'Visitor Request Update',
                'header_title' => 'Request Update',
                'intro_line' => 'Dear {{visitor_name}}, your visitor request to {{company}} could not be approved at this time.',
                'button_text' => 'Contact Support',
                'notes_title' => 'More Information',
                'notes_items' => [
                    'Please contact the host or security team for clarification.',
                    'You may submit a new request if needed.',
                ],
            ],
            self::PROCESS_REMINDER => [
                'subject' => 'Reminder: Complete Your Visitor Registration',
                'header_title' => 'Registration Reminder',
                'intro_line' => 'Dear {{visitor_name}}, this is a reminder to complete your visitor registration for {{company}}.',
                'button_text' => 'Complete Registration',
                'notes_title' => 'Reminder Notes',
                'notes_items' => [
                    'Please complete the registration before arrival.',
                    'Ensure your contact and ID details are correct.',
                ],
            ],
        ];

        $selected = $defaults[$process] ?? $defaults[self::PROCESS_INVITATION];

        return array_merge([
            'brand_name' => 'SafeG',
            'footer_text' => 'This is an automated message from SafeG Visitor Management System',
            'primary_color' => '#137fec',
            'content_bg_color' => '#f8f9fa',
            'text_color' => '#333333',
        ], $selected);
    }

    public function normalizeTemplate(string $process, $raw): array
    {
        $defaults = $this->getDefaultTemplate($process);
        $payload = is_array($raw) ? $raw : [];

        $template = array_merge($defaults, $payload);
        $template['subject'] = trim((string) $template['subject']) ?: $defaults['subject'];
        $template['brand_name'] = trim((string) $template['brand_name']) ?: $defaults['brand_name'];
        $template['header_title'] = trim((string) $template['header_title']) ?: $defaults['header_title'];
        $template['intro_line'] = trim((string) $template['intro_line']) ?: $defaults['intro_line'];
        $template['button_text'] = trim((string) $template['button_text']) ?: $defaults['button_text'];
        $template['notes_title'] = trim((string) $template['notes_title']) ?: $defaults['notes_title'];
        $template['footer_text'] = trim((string) $template['footer_text']) ?: $defaults['footer_text'];

        $template['primary_color'] = $this->normalizeHexColor($template['primary_color'], $defaults['primary_color']);
        $template['content_bg_color'] = $this->normalizeHexColor($template['content_bg_color'], $defaults['content_bg_color']);
        $template['text_color'] = $this->normalizeHexColor($template['text_color'], $defaults['text_color']);

        if (is_string($template['notes_items'])) {
            $items = preg_split('/\r\n|\r|\n/', $template['notes_items']);
            $template['notes_items'] = array_values(array_filter(array_map('trim', $items), static fn($item) => $item !== ''));
        }

        if (!is_array($template['notes_items']) || $template['notes_items'] === []) {
            $template['notes_items'] = $defaults['notes_items'];
        }

        $template['notes_items'] = array_map(static fn($item) => trim((string) $item), $template['notes_items']);

        return $template;
    }

    public function getDefaultInvitationTemplate(): array
    {
        return $this->getDefaultTemplate(self::PROCESS_INVITATION);
    }

    public function normalizeInvitationTemplate($raw): array
    {
        return $this->normalizeTemplate(self::PROCESS_INVITATION, $raw);
    }

    public function applyPlaceholders(string $text, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $value) {
            $replace['{{' . $key . '}}'] = (string) $value;
        }

        return strtr($text, $replace);
    }

    private function normalizeHexColor($value, string $fallback): string
    {
        $color = trim((string) $value);
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            return strtoupper($color);
        }

        return $fallback;
    }
}

