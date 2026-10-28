<?php

namespace App\Libraries;

use App\Controllers\GateMonitoring;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Kurita-style dynamic report templates for VMS: the section catalog (data source + columns),
 * the rows for a period, Excel / PDF output and the schedule rules.
 */
class ReportBuilder
{
    public const FREQUENCIES = ['manual' => 'Manual only', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'];
    public const FORMATS     = ['xlsx' => 'Excel (.xlsx)', 'pdf' => 'PDF', 'both' => 'Excel + PDF'];
    public const WEEKDAYS    = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    public const WEEKS       = ['first', 'second', 'third', 'fourth', 'last'];
    private const MAX_ROWS   = 5000;
    private const PDF_ROWS   = 500; // dompdf runs out of memory on big tables; Excel keeps every row

    private $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['client_visibility', 'vendor_client_scope']);
    }

    /** Every section a template can include, grouped by module (label, description, columns, the date the period filters on). */
    public function catalog(): array
    {
        return [
            'visitors' => ['module' => 'Visitor', 'label' => 'Visitor Visits', 'description' => 'Each visitor check-in / check-out with host, reason and location.', 'columns' => [
                'full_name' => 'Name', 'ic_passport' => 'IC / Passport', 'company' => 'Company', 'contact' => 'Contact', 'vehicle_registration' => 'Vehicle No',
                'host' => 'Host', 'reason' => 'Reason', 'location' => 'Location', 'status' => 'Status', 'check_in_time' => 'Check In', 'check_out_time' => 'Check Out']],
            'vendor_passes' => ['module' => 'Vendor', 'label' => 'Vendor Passes', 'description' => 'Vendor pass applications, status, card and expiry.', 'columns' => [
                'app_no' => 'App No', 'full_name' => 'Name', 'ic_no' => 'IC / Passport', 'vendor_company_name' => 'Company', 'contact_no' => 'Contact',
                'type_of_application' => 'Type Of Application', 'location_access' => 'Location Access', 'status' => 'Status', 'card_status' => 'Card Status',
                'pass_expiry' => 'Pass Expiry', 'created_at' => 'Applied On']],
            'staff_passes' => ['module' => 'Staff', 'label' => 'Staff Passes', 'description' => 'Staff pass applications, department, status and card expiry.', 'columns' => [
                'app_no' => 'App No', 'full_name' => 'Name', 'ic_passport' => 'IC / Passport', 'staff_no' => 'Staff No', 'department' => 'Department',
                'designation' => 'Designation', 'location_access' => 'Location Access', 'status' => 'Status', 'card_status' => 'Card Status',
                'card_expiry' => 'Card Expiry', 'created_at' => 'Applied On']],
            'vehicle_passes' => ['module' => 'Vehicle', 'label' => 'Vehicle Passes', 'description' => 'Vehicle pass applications, owner, payment and expiry.', 'columns' => [
                'app_no' => 'App No', 'plate_no' => 'Plate No', 'vehicle_type' => 'Vehicle Type', 'owner_name' => 'Owner', 'company_name' => 'Company',
                'location_access' => 'Location Access', 'status' => 'Status', 'payment_status' => 'Payment Status', 'sticker_serial' => 'Sticker No',
                'pass_expiry' => 'Pass Expiry', 'created_at' => 'Applied On']],
            'gate_transactions' => ['module' => 'Gate', 'label' => 'Gate In / Out', 'description' => 'Every gate transaction (staff, vendor, visitor, manual opening).', 'columns' => [
                'name' => 'Name', 'pass_type' => 'Type Of Pass', 'ic' => 'IC / Passport', 'location' => 'Location', 'in_lane' => 'In Lane', 'out_lane' => 'Out Lane',
                'card_no' => 'Card ID', 'plate_no' => 'Plate No', 'time_in' => 'Time In', 'time_out' => 'Time Out', 'manual_reason' => 'Manual Reason', 'status' => 'Status']],
            'manual_openings' => ['module' => 'Gate', 'label' => 'Manual Gate Openings', 'description' => 'Barriers opened by hand from Gate Monitoring and why.', 'columns' => [
                'opened_at' => 'Opened At', 'location' => 'Location', 'lane' => 'Lane', 'direction' => 'Direction', 'reason' => 'Reason', 'opened_by' => 'Opened By']],
            'blacklist' => ['module' => 'Security', 'label' => 'Blacklist', 'description' => 'Blacklisted people, reason and release date.', 'columns' => [
                'name' => 'Name', 'ic_passport_no' => 'IC / Passport', 'type' => 'Type', 'reason' => 'Reason', 'blacklist_date' => 'Blacklist Date',
                'status' => 'Status', 'released_date' => 'Released Date']],
        ];
    }

    /** The section rows (only the chosen columns) for a company and an optional period. */
    public function rows(string $key, array $columns, int $clientId, ?string $from = null, ?string $to = null): array
    {
        [$sql, $dateCol] = $this->source($key, $clientId) ?? [null, null];
        if ($sql === null) {
            return [];
        }
        $where = [];
        if ($from) {
            $where[] = "{$dateCol} >= " . $this->db->escape($from . ' 00:00:00');
        }
        if ($to) {
            $where[] = "{$dateCol} <= " . $this->db->escape($to . ' 23:59:59');
        }
        $rows = $this->db->query("SELECT * FROM ({$sql}) r" . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY {$dateCol} DESC LIMIT " . self::MAX_ROWS)->getResultArray();
        $cols = array_intersect(array_keys($this->catalog()[$key]['columns']), $columns);

        return array_map(static fn($r) => array_map(static fn($c) => $r[$c] ?? '', array_combine($cols, $cols)), $rows);
    }

    /** [SQL, date column] for one section, already scoped to what this client may see (0 = everything). */
    private function source(string $key, int $c): ?array
    {
        $t = fn(string $table) => $this->db->tableExists($table);
        switch ($key) {
            case 'visitors':
                if (! $t('invitation_visitors') || ! $t('invitations')) {
                    return null;
                }
                $scope = $c > 0 ? ' WHERE ' . visibility_invitation_sql($c, 'i') : '';

                return ["SELECT iv.full_name, iv.ic_passport, iv.company, iv.contact, iv.vehicle_registration, i.invited_by AS host, i.reason, i.location, i.status,
                        iv.check_in_time, iv.check_out_time, COALESCE(iv.check_in_time, iv.created_at) AS report_date
                    FROM invitation_visitors iv JOIN invitations i ON i.id = iv.invitation_id{$scope}", 'r.report_date'];
            case 'vendor_passes':
                $scope = $c > 0 ? ' WHERE ' . vendor_client_scope_sql($c, 'v') : '';

                return $t('vendors') ? ["SELECT v.app_no, v.full_name, COALESCE(NULLIF(v.ic_no,''), v.passport_no) AS ic_no, v.vendor_company_name, v.contact_no, v.type_of_application,
                        v.location_access, v.status, v.card_status, v.pass_expiry, v.created_at FROM vendors v{$scope}", 'r.created_at'] : null;
            case 'staff_passes':
                $scope = $c > 0 ? ' WHERE ' . visibility_staff_sql($c, 's') : '';

                return $t('staff') ? ["SELECT s.app_no, s.full_name, s.ic_passport, s.staff_no, s.department, s.designation, s.location_access, s.status, s.card_status,
                        s.card_expiry, s.created_at FROM staff s{$scope}", 'r.created_at'] : null;
            case 'vehicle_passes':
                $scope = $c > 0 ? ' AND ' . VehicleClientApprovals::scopeSql($this->db, $c, 'v') : '';

                return $t('vehicles') ? ["SELECT v.app_no, v.plate_no, vt.name AS vehicle_type, v.owner_name, v.company_name, v.location_access, v.status, v.payment_status,
                        v.sticker_serial, v.pass_expiry, v.created_at FROM vehicles v LEFT JOIN vehicle_types vt ON vt.id = v.vehicle_type_id
                    WHERE COALESCE(v.soft_delete, 0) = 0{$scope}", 'r.created_at'] : null;
            case 'gate_transactions':
                return ['SELECT tx.*, COALESCE(tx.time_in, tx.time_out) AS report_date FROM (' . (new GateMonitoring())->unionSql($c) . ') tx', 'r.report_date'];
            case 'manual_openings':
                if (! $t('gate_manual_openings')) {
                    return null;
                }
                $scope = $c > 0 ? " WHERE l.client_id = {$c}" : '';

                return ["SELECT m.opened_at, l.location_access AS location, ln.lane, m.direction, m.reason, COALESCE(u.username, '') AS opened_by
                    FROM gate_manual_openings m LEFT JOIN locations l ON l.id = m.location_id LEFT JOIN lanes ln ON ln.id = m.lane_id
                    LEFT JOIN users u ON u.id = m.opened_by{$scope}", 'r.opened_at'];
            case 'blacklist':
                return $t('blacklist') ? ['SELECT name, ic_passport_no, type, reason, blacklist_date, status, released_date FROM blacklist', 'r.blacklist_date'] : null;
        }

        return null;
    }

    /** Selected sections with their labels, column labels and rows, ready for preview / export. */
    public function build(array $tpl, ?string $from = null, ?string $to = null): array
    {
        $catalog = $this->catalog();
        $out     = [];
        $chosen  = $this->sections($tpl);
        foreach (array_intersect_key($catalog, $chosen) as $key => $sec) {
            $cols = $chosen[$key];
            $cols = array_values(array_intersect(array_keys($catalog[$key]['columns']), $cols));
            $out[$key] = [
                'label'   => $catalog[$key]['label'],
                'columns' => array_intersect_key($catalog[$key]['columns'], array_flip($cols)),
                'rows'    => $this->rows($key, $cols, (int) ($tpl['company_id'] ?? 0), $from, $to),
            ];
        }

        return $out;
    }

    /** {section: [columns]} from the stored JSON. */
    public function sections(array $tpl): array
    {
        $s = is_array($tpl['sections'] ?? null) ? $tpl['sections'] : (json_decode((string) ($tpl['sections'] ?? ''), true) ?: []);

        return array_filter(array_map(static fn($v) => array_values((array) ($v['columns'] ?? $v)), $s));
    }

    /** One worksheet per section (Kurita: Excel always keeps sections apart). */
    public function xlsx(array $tpl, array $data): string
    {
        $book = new Spreadsheet();
        $book->removeSheetByIndex(0);
        foreach ($data ?: ['empty' => ['label' => 'Report', 'columns' => [], 'rows' => []]] as $section) {
            $sheet = $book->createSheet();
            $sheet->setTitle(mb_substr(preg_replace('/[\\\\\/?*\[\]:]/', '', $section['label']), 0, 31));
            $sheet->fromArray(array_values($section['columns']), null, 'A1');
            $sheet->getStyle('1:1')->getFont()->setBold(true);
            if ($section['rows']) {
                $sheet->fromArray(array_map('array_values', $section['rows']), null, 'A2', true);
            }
            foreach (range(1, max(1, count($section['columns']))) as $i) {
                $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }
        }
        $book->setActiveSheetIndex(0);
        $fh = fopen('php://temp', 'w+');
        (new Xlsx($book))->save($fh);
        rewind($fh);
        $bin = stream_get_contents($fh);
        fclose($fh);

        return $bin;
    }

    /** PDF: sectioned = one table per section, combined = one table with a Report Section column. */
    public function pdf(array $tpl, array $data, ?string $from, ?string $to): string
    {
        foreach ($data as &$s) {
            $s['total'] = count($s['rows']);
            $s['rows']  = array_slice($s['rows'], 0, self::PDF_ROWS);
        }
        unset($s);
        if (($tpl['report_layout'] ?? 'sectioned') === 'combined' && $data) {
            $cols = ['report_section' => 'Report Section'];
            $rows = [];
            foreach ($data as $key => $s) {
                foreach ($s['columns'] as $c => $label) {
                    $cols["{$key}.{$c}"] = $label;
                }
            }
            foreach ($data as $key => $s) {
                foreach ($s['rows'] as $r) {
                    $row = array_fill_keys(array_keys($cols), '');
                    $row['report_section'] = $s['label'];
                    foreach ($r as $c => $v) {
                        $row["{$key}.{$c}"] = $v;
                    }
                    $rows[] = $row;
                }
            }
            $data = ['combined' => ['label' => 'Combined', 'columns' => $cols, 'rows' => array_slice($rows, 0, self::PDF_ROWS), 'total' => array_sum(array_column($data, 'total'))]];
        }
        $pdf = new Dompdf(['isRemoteEnabled' => false]);
        $pdf->loadHtml(view('report_templates/pdf', ['tpl' => $tpl, 'data' => $data, 'from' => $from, 'to' => $to, 'limit' => self::PDF_ROWS]));
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        return $pdf->output();
    }

    /** The file(s) for this template's format: [[filename, content, mime], ...]. */
    public function attachments(array $tpl, ?string $from = null, ?string $to = null): array
    {
        $data  = $this->build($tpl, $from, $to);
        $base  = preg_replace('/[^A-Za-z0-9]+/', '-', trim($tpl['name'])) . '-' . date('Ymd-His');
        $files = [];
        foreach (($tpl['format'] ?? 'xlsx') === 'both' ? ['xlsx', 'pdf'] : [$tpl['format'] ?? 'xlsx'] as $f) {
            $files[] = $f === 'pdf'
                ? ['filename' => "{$base}.pdf", 'content' => $this->pdf($tpl, $data, $from, $to), 'mime' => 'application/pdf']
                : ['filename' => "{$base}.xlsx", 'content' => $this->xlsx($tpl, $data), 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        }

        return $files;
    }

    /** Email the report to the template's recipients; returns an error message or null. */
    public function send(array $tpl, ?string $from = null, ?string $to = null): ?string
    {
        $recipients = json_decode((string) ($tpl['recipients'] ?? '[]'), true) ?: [];
        if (! $recipients) {
            return 'Add at least one email recipient before sending this report.';
        }
        $cfg   = config('Email');
        $email = \Config\Services::email();
        $email->setFrom($cfg->fromEmail, $cfg->fromName);
        $email->setTo($recipients);
        $email->setSubject('[SAFE G] Report - ' . $tpl['name']);
        $email->setMessage("Please find attached the {$tpl['name']} report for your review.\n\nGenerated on: " . date('d M Y, g:i A')
            . ($from || $to ? "\nPeriod: " . ($from ?: '...') . ' to ' . ($to ?: '...') : '') . "\n\nThis is an automated email. Please do not reply.");
        foreach ($this->attachments($tpl, $from, $to) as $f) {
            $email->attach($f['content'], 'attachment', $f['filename'], $f['mime']);
        }
        if (! $email->send(false)) {
            log_message('error', 'ReportBuilder send failed: ' . $email->printDebugger(['headers']));

            return 'The email could not be sent. Check the email settings.';
        }

        return null;
    }

    public function scheduleLabel(array $tpl): string
    {
        $c    = json_decode((string) ($tpl['schedule_config'] ?? '[]'), true) ?: [];
        $time = $c['time'] ?? '08:00';
        $day  = ucfirst($c['weekday'] ?? 'monday');
        $week = ucfirst($c['week'] ?? 'first');

        return match ($tpl['frequency'] ?? 'manual') {
            'daily'   => "Every day at {$time}",
            'weekly'  => "Every {$day} at {$time}",
            'monthly' => "{$week} {$day} of every month at {$time}",
            'yearly'  => "{$week} {$day} of " . date('F', mktime(0, 0, 0, (int) ($c['month'] ?? 1), 1)) . " at {$time}",
            default   => 'Manual only',
        };
    }

    /**
     * When a scheduled template is due at $now, the period it covers: [from, to] (yesterday, last week,
     * last month, last year). Null when it is not due or was already sent for this slot.
     */
    public function due(array $tpl, int $now): ?array
    {
        $f = $tpl['frequency'] ?? 'manual';
        if ($f === 'manual' || empty($tpl['is_active'])) {
            return null;
        }
        $c    = json_decode((string) ($tpl['schedule_config'] ?? '[]'), true) ?: [];
        $slot = strtotime(date('Y-m-d', $now) . ' ' . ($c['time'] ?? '08:00'));
        $day  = $c['weekday'] ?? 'monday';
        $nth  = static fn(int $ts) => (string) date('Y-m-d', strtotime(($c['week'] ?? 'first') . " {$day} of " . date('F Y', $ts)));
        $ok = match ($f) {
            'daily'   => true,
            'weekly'  => strtolower(date('l', $now)) === $day,
            'monthly' => date('Y-m-d', $now) === $nth($now),
            'yearly'  => (int) date('n', $now) === (int) ($c['month'] ?? 1) && date('Y-m-d', $now) === $nth($now),
            default   => false,
        };
        if (! $ok || $now < $slot || (! empty($tpl['last_sent_at']) && strtotime($tpl['last_sent_at']) >= $slot)) {
            return null;
        }

        return match ($f) {
            'daily'   => [date('Y-m-d', strtotime('-1 day', $now)), date('Y-m-d', strtotime('-1 day', $now))],
            'weekly'  => [date('Y-m-d', strtotime('-7 days', $now)), date('Y-m-d', strtotime('-1 day', $now))],
            'monthly' => [date('Y-m-01', strtotime('first day of last month', $now)), date('Y-m-t', strtotime('first day of last month', $now))],
            default   => [(date('Y', $now) - 1) . '-01-01', (date('Y', $now) - 1) . '-12-31'],
        };
    }
}
