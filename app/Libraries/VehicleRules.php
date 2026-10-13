<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/** Plate-number rules and status flow for the vehicle pass. Pure: takes the DB connection. */
class VehicleRules
{
    public const STATUSES = ['Draft', 'Pending', 'Approved', 'Rejected', 'Issued', 'Terminated'];

    /** Groups shown in the sidebar: Public and Cargo | Non-Cargo (company vehicles). */
    public const GROUPS = ['public' => 'Public', 'cnc' => 'Cargo | Non-Cargo'];

    /** A pass can be renewed once its expiry is this close (or already past). */
    public const RENEW_DAYS = 90;

    /**
     * Role > Vehicle access keys per group and page. 'reviewed' is the combined Approved / Reject list:
     * either key opens it, approved_list is also what lets a user issue the sticker.
     */
    public const KEYS = [
        'public' => [
            'all' => 'my_vehicle_pass', 'staff' => 'staff_vehicle_pass', 'staff_request' => 'new_staff_request', 'create' => 'my_vehicle_pass',
            'request' => 'public_request_list', 'approved' => 'public_approved_list', 'reject' => 'public_reject_list', 'closed' => 'public_closed_list',
            'delete_rejected' => 'public_reject_delete', 'delete_closed' => 'public_closed_delete',
        ],
        'cnc' => [
            'all' => 'cargo_my_vehicle_pass', 'create' => 'cargo_my_vehicle_pass',
            'request' => 'cargo_request_list', 'approved' => 'cargo_approved_list', 'reject' => 'cargo_reject_list', 'closed' => 'cargo_closed_list',
            'delete_rejected' => 'cargo_reject_delete', 'delete_closed' => 'cargo_closed_delete',
        ],
    ];

    /** The Daily Pass feature is built but hidden; set VEHICLE_DAILY_PASS=true in .env to switch it on. */
    public static function dailyEnabled(): bool
    {
        return filter_var(function_exists('env') ? env('VEHICLE_DAILY_PASS', false) : false, FILTER_VALIDATE_BOOLEAN);
    }

    /** Can this pass be renewed now? Returns an error text, or null when it can. */
    public static function renewBlock(array $v): ?string
    {
        if ($v['status'] !== 'Issued') {
            return 'Only an issued pass can be renewed.';
        }
        if (empty($v['pass_expiry'])) {
            return 'This pass has no expiry date.';
        }
        if ($v['pass_expiry'] > date('Y-m-d', strtotime('+' . self::RENEW_DAYS . ' days'))) {
            return 'This pass can be renewed within ' . self::RENEW_DAYS . ' days of its expiry (' . date('d/m/Y', strtotime($v['pass_expiry'])) . ').';
        }
        if (! empty($v['renewal_pending'])) {
            return 'A renewal for this pass is already in progress.';
        }

        return null;
    }

    /** Upper-case, no spaces or dashes: "abc 1234-d" -> "ABC1234D". */
    public static function normalizePlate(string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $plate) ?? '');
    }

    /**
     * Another live pass already holding this plate, or null. A plate is free when its pass is a
     * Draft, Rejected or Terminated, or Issued but past its expiry.
     */
    /**
     * @param int|int[]|null $ignoreId pass id(s) to leave out (itself, and the pass it renews)
     * @param string|null $stickerYear KPK checks the plate per sticker year: when given, only a pass of the same year (or with no year) blocks
     */
    public static function plateHolder(BaseConnection $db, string $plate, $ignoreId = null, ?string $stickerYear = null): ?array
    {
        $plate = static::normalizePlate($plate);
        if ($plate === '') {
            return null;
        }
        $b = $db->table('vehicles')->select('id, app_no, status, pass_expiry, client_id, renewed_from, sticker_year')->where('plate_no', $plate)
            ->whereIn('status', ['Pending', 'Approved', 'Issued']);
        if ($stickerYear !== null && $stickerYear !== '') {
            $b->groupStart()->where('sticker_year', $stickerYear)->orWhere('sticker_year IS NULL', null, false)->orWhere('sticker_year', '')->groupEnd();
        }
        $ignore = array_values(array_filter(array_map('intval', (array) $ignoreId)));
        if ($ignore) {
            $b->whereNotIn('id', $ignore);
        }
        $today = date('Y-m-d');
        foreach ($b->get()->getResultArray() as $r) {
            if ($r['status'] === 'Issued' && ! empty($r['pass_expiry']) && $r['pass_expiry'] < $today) {
                continue;
            }

            return $r;
        }

        return null;
    }

    public static function plateMessage(string $plate, array $holder): string
    {
        return "Plate number '" . static::normalizePlate($plate) . "' already has a vehicle pass ({$holder['app_no']}, status: {$holder['status']}) for this sticker year. "
            . 'Terminate or let that pass expire first, or ask for a vehicle type that allows duplicate plates.';
    }

    /** Allowed next status for an action, or null when the action is not allowed from $from. */
    public static function next(string $from, string $action): ?string
    {
        $map = [
            'submit'    => ['Draft' => 'Pending', 'Rejected' => 'Pending'],
            'approve'   => ['Pending' => 'Approved'],
            'reject'    => ['Pending' => 'Rejected'],
            'issue'     => ['Approved' => 'Issued'],
            'terminate' => ['Issued' => 'Terminated'],
        ];

        return $map[$action][$from] ?? null;
    }

    /** The extra form fields a vehicle type switches on (type column => [post field, label]). */
    public const TYPE_FIELDS = [
        'seater_req'         => ['seater_number', 'Seater Number'],
        'working_height_req' => ['working_height', 'Working Height'],
        'working_load_req'   => ['working_load', 'Working Load'],
        'truck_head_req'     => ['truck_head_number', 'Truck Head Number'],
        'trailer_req'        => ['trailer_number', 'Trailer Number'],
        'truck_weight_req'   => ['truck_weight', 'Truck Weight'],
        'chassis_req'        => ['chassis_number', 'Chassis Number'],
        'trailer_weight_req' => ['trailer_weight', 'Trailer Weight'],
        'trailer_type_req'   => ['trailer_type', 'Trailer Type'],
    ];

    /** KPK "Vehicle Operation" (Cargo | Non-Cargo): narrows which vehicle types are offered. */
    public const OPERATIONS = ['CONTAINER', 'CARGO AND NON-CARGO', 'NON-CARGO', 'CONTAINER, CARGO AND NON-CARGO'];

    /** KPK trailer sizes. */
    public const TRAILER_TYPES = ['20' => '20 Ft', '40' => '40 Ft', '45' => '45 Ft'];

    /** KPK payment types at issuance; LEDGER only for a company that has a ledger account. */
    public const PAYMENT_TYPES = ['CASH', 'LEDGER'];

    public const REISSUE_REASONS = ['Damage', 'Lost', 'Stolen'];

    /** Sticker years offered on a request: this year, plus next year from October (KPK). */
    public static function stickerYears(): array
    {
        $y = (int) date('Y');

        return ((int) date('n') >= 10) ? [(string) $y, (string) ($y + 1)] : [(string) $y];
    }

    /** Next-action text shown in the lists (KPK nextAction). */
    public static function nextAction(array $v): string
    {
        switch ($v['status'] ?? '') {
            case 'Pending':
                return 'Pending Approval';
            case 'Approved':
                return ((int) ($v['payment_required'] ?? 0) === 1 && ($v['payment_status'] ?? '') !== 'Paid' && ($v['payment_type'] ?? '') !== 'LEDGER') ? 'Proceed Payment' : 'Collect Sticker';
            default:
                return '';
        }
    }
}
