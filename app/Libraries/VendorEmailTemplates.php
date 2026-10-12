<?php

namespace App\Libraries;

/**
 * The two vendor emails, editable in Config > Email Template like the visitor ones:
 *
 *   VENDOR_INVITATION - sent to a company when an admin creates it
 *   VENDOR_REGISTERED - sent to the client's admins when that company registers
 *
 * A client's own row wins; else the default (client_id NULL) row; else the built-in text below.
 */
class VendorEmailTemplates
{
    public const INVITATION = 'VENDOR_INVITATION';
    public const REGISTERED = 'VENDOR_REGISTERED';

    public static function isVendorCode(string $code): bool
    {
        return in_array(strtoupper(trim($code)), [self::INVITATION, self::REGISTERED], true);
    }

    public static function defaults(string $code): array
    {
        $base = ['primary_color' => '#137FEC', 'content_bg_color' => '#F8F9FA', 'text_color' => '#333333'];

        if (strtoupper($code) === self::REGISTERED) {
            return $base + [
                'subject'      => 'Vendor registration waiting for approval - {{company_name}}',
                'header_title' => 'Vendor Registration',
                'button_text'  => 'Open SafeG',
                'link_key'     => 'login_link',
                'body'         => "Hello,\n\n{{company_name}} (SSM No {{ssm_no}}) has registered a vendor account under {{client_name}} and is waiting for your approval.\n\nContact: {{contact_name}} · {{contact_email}}\n\nPlease open Config > User, find the account and press Verify.\n\nThank you.",
            ];
        }

        return $base + [
            'subject'      => 'Please register your company on SafeG - {{client_name}}',
            'header_title' => 'Vendor Registration Invitation',
            'button_text'  => 'Register your company',
            'link_key'     => 'register_link',
            'body'         => "Hello {{company_name}},\n\n{{client_name}} has added your company to its vendor list in SafeG. Please register your company account so you can submit and track your vendor pass requests online.\n\nYour company details are already filled in. After you register, {{client_name}} will verify your account and you can log in.\n\nThank you.",
        ];
    }

    /** Sample values for the preview in Config. */
    public static function sampleContext(string $code): array
    {
        return [
            'company_name'  => 'Vendor Company Sdn Bhd',
            'client_name'   => 'Client Name',
            'ssm_no'        => '201901012345',
            'contact_name'  => 'Contact Person',
            'contact_email' => 'contact@vendor.com',
            'register_link' => base_url('register'),
            'login_link'    => base_url('login'),
        ];
    }

    /** Placeholders the admin may use, for the hint under the editor. */
    public static function placeholders(string $code): array
    {
        return strtoupper($code) === self::REGISTERED
            ? ['company_name', 'ssm_no', 'client_name', 'contact_name', 'contact_email', 'login_link']
            : ['company_name', 'client_name', 'register_link'];
    }

    /** Stored row for this client, else the default row, else null. */
    public static function row(string $code, ?int $clientId): ?array
    {
        $db    = \Config\Database::connect();
        $code  = strtoupper($code);
        if (! $db->tableExists('email_templates')) {
            return null;
        }
        if ($clientId) {
            $r = $db->table('email_templates')->where('code', $code)->where('client_id', $clientId)->get()->getRowArray();
            if ($r) {
                return $r;
            }
        }

        return $db->table('email_templates')->where('code', $code)->where('client_id IS NULL', null, false)->get()->getRowArray() ?: null;
    }

    /** Create the vendor rows (with defaults) for this client if they are missing. */
    public static function ensure(?int $clientId): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('email_templates')) {
            return;
        }
        foreach ([self::INVITATION, self::REGISTERED] as $code) {
            $b = $db->table('email_templates')->where('code', $code);
            $clientId ? $b->where('client_id', $clientId) : $b->where('client_id IS NULL', null, false);
            if ($b->countAllResults() > 0) {
                continue;
            }
            $d   = self::defaults($code);
            $now = date('Y-m-d H:i:s');
            $db->table('email_templates')->insert([
                'client_id' => $clientId, 'code' => $code, 'subject' => $d['subject'], 'body' => $d['body'],
                'primary_color' => $d['primary_color'], 'content_bg_color' => $d['content_bg_color'], 'text_color' => $d['text_color'],
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    /**
     * @param array<string,string> $ctx placeholder values
     * @return array{subject:string,html:string}
     */
    public static function render(string $code, ?int $clientId, array $ctx): array
    {
        $d   = self::defaults($code);
        $row = self::row($code, $clientId) ?: [];

        $subjectTpl = trim((string) ($row['subject'] ?? '')) ?: $d['subject'];
        $bodyTpl    = trim((string) ($row['body'] ?? '')) ?: $d['body'];

        $linkKeys = ['register_link', 'login_link'];
        $primary  = trim((string) ($row['primary_color'] ?? '')) ?: $d['primary_color'];

        $plain = [];
        foreach ($ctx as $k => $v) {
            $plain['{{' . $k . '}}'] = (string) $v;
        }
        $subject = strtr($subjectTpl, $plain);

        // Body is plain text: escape it, then put the values in (links become clickable).
        $esc = [];
        foreach ($ctx as $k => $v) {
            $esc['{{' . $k . '}}'] = in_array($k, $linkKeys, true)
                ? '<a href="' . htmlspecialchars((string) $v, ENT_QUOTES) . '" style="color:' . htmlspecialchars($primary, ENT_QUOTES) . ';">' . esc((string) $v) . '</a>'
                : esc((string) $v);
        }
        $bodyHtml = nl2br(strtr(esc($bodyTpl), $esc));

        $logo = trim((string) ($row['logo_url'] ?? ''));
        if ($logo !== '' && ! preg_match('#^(https?:|data:image)#', $logo)) {
            $logo = base_url(ltrim(str_replace('\\', '/', $logo), '/'));
        }

        $html = view('emails/vendor_template', [
            'brand_name'       => 'SafeG',
            'header_title'     => $d['header_title'],
            'primary_color'    => $primary,
            'content_bg_color' => trim((string) ($row['content_bg_color'] ?? '')) ?: $d['content_bg_color'],
            'text_color'       => trim((string) ($row['text_color'] ?? '')) ?: $d['text_color'],
            'logo_src'         => $logo,
            'body_html'        => $bodyHtml,
            'button_text'      => $d['button_text'],
            'button_url'       => (string) ($ctx[$d['link_key']] ?? ''),
        ]);

        return ['subject' => $subject, 'html' => $html];
    }
}
