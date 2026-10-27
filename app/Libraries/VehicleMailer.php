<?php

namespace App\Libraries;

/**
 * KPK "sendVehicleApprovalAndReject": after a vehicle pass is approved or rejected, the person who
 * applied gets an email with the application details. Subject "[SAFE G] Vehicle Pass".
 *
 * Never throws: a mail problem must not undo the approval. Failures are logged.
 */
class VehicleMailer
{
    /** @param string $decision 'APPROVED' or 'REJECTED' */
    public static function decision(array $v, string $decision, string $remark = ''): bool
    {
        try {
            $db   = \Config\Database::connect();
            $user = (int) ($v['created_by_user'] ?? 0) > 0
                ? $db->table('users')->select('email')->where('id', (int) $v['created_by_user'])->get()->getRowArray()
                : null;
            $to = trim((string) ($user['email'] ?? ''));
            if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
                log_message('info', 'VehicleMailer: no email for the applicant of ' . ($v['app_no'] ?? $v['id']) . ', nothing sent.');

                return false;
            }

            $type = $db->table('vehicle_types')->select('name')->where('id', (int) ($v['vehicle_type_id'] ?? 0))->get()->getRowArray();
            $cd   = trim((string) ($v['collect_date'] ?? ''));
            $html = view('emails/vehicle_decision', [
                'decision' => $decision,
                'rows'     => [
                    'APPLICATION NO'           => (string) ($v['app_no'] ?? ''),
                    'FULL NAME'                => (string) ($v['owner_name'] ?? ''),
                    'IC / PASSPORT / STAFF ID' => (string) (($v['owner_ic'] ?? '') ?: ($v['staff_no'] ?? '')),
                    'DATE OF COLLECTION'       => ($cd !== '' && ! str_starts_with($cd, '0000')) ? date('d/m/Y', strtotime($cd)) : '',
                    'VEHICLE TYPE'             => (string) ($type['name'] ?? ''),
                    'VEHICLE REGISTRATION NO'  => (string) ($v['plate_no'] ?? ''),
                    'REMARK'                   => $remark,
                ],
                'siteUrl' => base_url(),
                'brand'   => 'SafeG',
            ]);

            $cfg   = config('Email');
            $email = \Config\Services::email();
            $email->setMailType('html');
            $email->setFrom($cfg->fromEmail, $cfg->fromName);
            $email->setTo($to);
            $email->setSubject('[SAFE G] Vehicle Pass');
            $email->setMessage($html);
            if ($email->send(false)) {
                return true;
            }
            log_message('error', 'VehicleMailer: send failed to ' . $to . ' - ' . trim(preg_replace('/\s+/', ' ', strip_tags($email->printDebugger([])))));
        } catch (\Throwable $e) {
            log_message('error', 'VehicleMailer: ' . $e->getMessage());
        }

        return false;
    }
}
