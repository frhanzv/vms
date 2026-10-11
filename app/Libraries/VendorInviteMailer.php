<?php

namespace App\Libraries;

/**
 * The two automatic emails of the vendor onboarding flow:
 *
 *  1. invite()     — client B adds vendor D under Config > Company: D gets
 *                    the link to this production and is asked to register (company pre-filled).
 *  2. registered() — D registered: B's admins are reminded to approve it.
 *
 * Never throws: a mail problem must not block creating a company or a
 * registration. Failures are logged and reported through the return value.
 */
class VendorInviteMailer
{
    /** @return array{0:bool,1:string} [sent, message] */
    public static function invite(array $company, ?array $client): array
    {
        helper('client_link');
        $to = trim((string) ($company['email'] ?? ''));
        if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return [false, 'This company has no valid email address.'];
        }

        $link   = client_link_url($client ?? '', 'register', (string) ($company['registration_no'] ?? ''));
        $byName = $client['name'] ?? 'the site administrator';
        $name   = esc((string) $company['name']);

        $html = '<p>Hello ' . $name . ',</p>'
            . '<p><strong>' . esc($byName) . '</strong> has added your company to its vendor list in SafeG. '
            . 'Please register your company account so you can submit and track your vendor pass requests online.</p>'
            . '<p><a href="' . $link . '" style="background:#137fec;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;">Register your company</a></p>'
            . '<p style="font-size:12px;color:#64748b;">Or open this link: ' . $link . '<br>Your company details are already filled in. After you register, ' . esc($byName) . ' will verify your account and you can log in.</p>';

        return self::send($to, 'Please register your company on SafeG - ' . $byName, $html);
    }

    /**
     * @param array $company the vendor company row
     * @param array $client  the client it registered under
     * @param array $user    the new (unverified) vendor account
     * @return array{0:bool,1:string}
     */
    public static function registered(array $company, array $client, array $user): array
    {
        helper('client_link');
        $recipients = client_admin_recipients((int) $client['id']);
        if ($recipients === []) {
            return [false, 'No admin with an email address found for ' . ($client['name'] ?? 'the client') . '.'];
        }

        $html = '<p>Hello,</p>'
            . '<p><strong>' . esc((string) $company['name']) . '</strong> (SSM No ' . esc((string) ($company['registration_no'] ?? '')) . ') has registered a vendor account '
            . 'under <strong>' . esc((string) $client['name']) . '</strong> and is waiting for your approval.</p>'
            . '<p>Contact: ' . esc((string) ($user['full_name'] ?? '')) . ' &middot; ' . esc((string) ($user['email'] ?? '')) . '</p>'
            . '<p>Please open <strong>Config &gt; User</strong>, find the account and press <strong>Verify</strong>.</p>'
            . '<p><a href="' . client_link_url($client, 'login') . '">Open SafeG</a></p>';

        $anyOk = false;
        foreach ($recipients as $r) {
            [$ok] = self::send($r['email'], 'Vendor registration waiting for approval - ' . $company['name'], $html);
            $anyOk = $anyOk || $ok;
        }

        return [$anyOk, $anyOk ? 'Reminder sent.' : 'Could not send the reminder email.'];
    }

    /** A short, readable reason for a failed send (the full SMTP log goes to writable/logs). */
    private static function reason(string $debug, $cfg): string
    {
        if (trim((string) $cfg->SMTPUser) === '' || trim((string) $cfg->SMTPPass) === '') {
            return 'the mail account is not set up. Put MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD and MAIL_FROM_ADDRESS in your .env file (for Gmail, use an App Password).';
        }
        if (stripos($debug, 'Username and Password not accepted') !== false || stripos($debug, '535') !== false) {
            return 'the mail server rejected the username or password (for Gmail, use an App Password).';
        }
        if (stripos($debug, 'Unable to connect') !== false || stripos($debug, 'smtp server') !== false || stripos($debug, 'fsockopen') !== false) {
            return 'could not connect to the mail server ' . $cfg->SMTPHost . ':' . $cfg->SMTPPort . '. Check MAIL_HOST / MAIL_PORT and your internet.';
        }

        return mb_substr($debug !== '' ? $debug : 'unknown error', 0, 220) . ' (see writable/logs for details)';
    }

    /** @return array{0:bool,1:string} */
    private static function send(string $to, string $subject, string $html): array
    {
        try {
            $cfg   = config('Email');
            $email = \Config\Services::email();
            $email->setMailType('html');
            $email->setFrom($cfg->fromEmail, $cfg->fromName);
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage($html);
            if ($email->send(false)) {
                return [true, 'Email sent to ' . $to . '.'];
            }
            $debug = trim(preg_replace('/\s+/', ' ', strip_tags($email->printDebugger([]))));
            log_message('error', 'VendorInviteMailer: send failed to ' . $to . ' - ' . $debug);
            $reason = self::reason($debug, $cfg);
            $email->clear();
        } catch (\Throwable $e) {
            log_message('error', 'VendorInviteMailer: ' . $e->getMessage());
            $reason = $e->getMessage();
        }

        return [false, 'The email could not be sent: ' . $reason];
    }
}
