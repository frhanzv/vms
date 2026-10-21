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

    /** KPK module config "vpDirectClose": approving a pass issues it straight away. Set VEHICLE_DIRECT_CLOSE=true in .env. */
    public static function approveIssues(): bool
    {
        return filter_var(function_exists('env') ? env('VEHICLE_DIRECT_CLOSE', false) : false, FILTER_VALIDATE_BOOLEAN);
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
     * Draft or Terminated, a deleted pass, or Issued but past its expiry.
     */
    /**
     * @param int|int[]|null $ignoreId pass id(s) to leave out (itself, and the pass it renews)
     * @param string|null $stickerYear when given, only a pass of the same year (or with no year) blocks
     * @param int|null $companyId the company asking for the plate. KPK looks at the pass that already holds the plate:
     *        if ITS type allows duplicate plates it only conflicts with the same company, otherwise it always conflicts.
     *
     * A Rejected pass keeps the plate until it is deleted, so Rejected blocks as well.
     */
    public static function plateHolder(BaseConnection $db, string $plate, $ignoreId = null, ?string $stickerYear = null, ?int $companyId = null): ?array
    {
        $plate = static::normalizePlate($plate);
        if ($plate === '') {
            return null;
        }
        $b = $db->table('vehicles v')->select('v.id, v.app_no, v.status, v.pass_expiry, v.client_id, v.renewed_from, v.sticker_year, v.vendor_company_id, t.duplicate_plate_ok AS holder_dup_ok')
            ->join('vehicle_types t', 't.id = v.vehicle_type_id', 'left')->where('v.plate_no', $plate)
            ->whereIn('v.status', ['Pending', 'Approved', 'Rejected', 'Issued']);
        if ($db->fieldExists('soft_delete', 'vehicles')) {
            $b->groupStart()->where('v.soft_delete', 0)->orWhere('v.soft_delete IS NULL', null, false)->groupEnd();
        }
        if ($stickerYear !== null && $stickerYear !== '') {
            $b->groupStart()->where('v.sticker_year', $stickerYear)->orWhere('v.sticker_year IS NULL', null, false)->orWhere('v.sticker_year', '')->groupEnd();
        }
        $ignore = array_values(array_filter(array_map('intval', (array) $ignoreId)));
        if ($ignore) {
            $b->whereNotIn('v.id', $ignore);
        }
        $today = date('Y-m-d');
        foreach ($b->get()->getResultArray() as $r) {
            if ($r['status'] === 'Issued' && ! empty($r['pass_expiry']) && $r['pass_expiry'] < $today) {
                continue;
            }
            if ((int) ($r['holder_dup_ok'] ?? 0) === 1 && (! $companyId || (int) $r['vendor_company_id'] !== $companyId)) {
                continue;
            }

            return $r;
        }

        return null;
    }

    public static function plateMessage(string $plate, array $holder): string
    {
        return "Plate number '" . static::normalizePlate($plate) . "' already has a vehicle pass ({$holder['app_no']}, status: {$holder['status']}) for this sticker year. "
            . 'Terminate or let that pass expire (or delete it if it was rejected) first, or use a vehicle type that allows duplicate plates.';
    }

    /** KPK limits: heaviest truck head / trailer allowed on a Cargo | Non-Cargo pass. */
    public const MAX_TRUCK_WEIGHT   = 15000;
    public const MAX_TRAILER_WEIGHT = 10000;

    /** Truck head / trailer numbers are stored without spaces (KPK). */
    public static function stripSpaces(string $s): string
    {
        return preg_replace('/\s+/', '', $s) ?? '';
    }

    /** Weight rule for a pass: returns an error text or null. $get reads a posted field. */
    public static function weightError(array $type, callable $get): ?string
    {
        foreach ([['truck_weight_req', 'truck_weight', 'Truck Weight', self::MAX_TRUCK_WEIGHT], ['trailer_weight_req', 'trailer_weight', 'Trailer Weight', self::MAX_TRAILER_WEIGHT]] as [$flag, $field, $label, $max]) {
            if ((int) ($type[$flag] ?? 0) !== 1) {
                continue;
            }
            $w = trim((string) $get($field));
            if ($w === '') {
                continue;
            }
            if (! ctype_digit($w) || (int) $w > $max) {
                return "{$label} is invalid (digits only, maximum {$max}).";
            }
        }

        return null;
    }

    /** Pass expiry for a sticker year: 31 Dec of that year; a staff pass runs 100 years. */
    public static function expiryFor(?string $stickerYear, bool $staff = false): string
    {
        $y = ctype_digit((string) $stickerYear) && $stickerYear !== '' ? (int) $stickerYear : (int) date('Y');

        return ($staff ? $y + 100 : $y) . '-12-31';
    }

    /** Oct, Nov, Dec: the year-end window (KPK). */
    public static function yearEnd(): bool
    {
        return (int) date('n') >= 10;
    }

    /** In the year-end window a renewal / resubmission must be for next year's sticker. Returns an error text or null. */
    public static function yearEndError(?string $stickerYear): ?string
    {
        if (static::yearEnd() && (string) $stickerYear !== (string) ((int) date('Y') + 1)) {
            return 'Invalid sticker year: from October a renewal must be for ' . ((int) date('Y') + 1) . '.';
        }

        return null;
    }

    private static function envInt(string $key, int $default): int
    {
        $v = function_exists('env') ? env($key, $default) : $default;

        return is_numeric($v) ? (int) $v : $default;
    }

    /**
     * KPK vehicle limits. Only car / motorbike / van types count. Staff: max active passes per person
     * (VEHICLE_MAX_STAFF, default 5; 10 in Oct-Dec). Company: its own allocation per category if set
     * (companies.vehicle_max_car / _motorbike / _van, doubled in Oct-Dec), otherwise a default total of
     * VEHICLE_MAX_COMPANY (2; 4 in Oct-Dec). Returns an error text or null.
     */
    public static function limitError(BaseConnection $db, string $category, ?int $companyId, ?int $staffId, ?int $ignoreId = null): ?string
    {
        $cats = ['car', 'motorbike', 'van'];
        if (! in_array($category, $cats, true)) {
            return null;
        }
        $ye = static::yearEnd();
        $live = static function ($b) use ($db, $ignoreId) {
            if ($db->fieldExists('soft_delete', 'vehicles')) {
                $b->groupStart()->where('v.soft_delete', 0)->orWhere('v.soft_delete IS NULL', null, false)->groupEnd();
            }
            if ($ignoreId) {
                $b->where('v.id !=', $ignoreId);
            }

            return $b;
        };

        if ($staffId) {
            $max = $ye ? static::envInt('VEHICLE_MAX_STAFF_YEAR_END', 10) : static::envInt('VEHICLE_MAX_STAFF', 5);
            $n   = $live($db->table('vehicles v')->join('vehicle_types t', 't.id = v.vehicle_type_id')->where('v.staff_id', $staffId)->where('v.pass_for', 'staff')
                ->whereIn('t.category', $cats)->whereIn('v.status', ['Pending', 'Approved', 'Issued']))->countAllResults();

            return $n >= $max ? "This staff member already has {$n} vehicle pass(es); the limit is {$max}." : null;
        }
        if (! $companyId) {
            return null;
        }
        $co    = $db->table('companies')->where('id', $companyId)->get()->getRowArray() ?: [];
        $alloc = $co['vehicle_max_' . $category] ?? null;
        $base  = $live($db->table('vehicles v')->join('vehicle_types t', 't.id = v.vehicle_type_id')->where('v.vendor_company_id', $companyId)->where('v.pass_for', 'vendor')
            ->whereIn('v.status', ['Approved', 'Issued']));
        if ($alloc !== null && $alloc !== '') {
            $max = (int) $alloc * ($ye ? 2 : 1);
            $n   = (clone $base)->where('t.category', $category)->countAllResults();

            return $n >= $max ? "The company has reached its {$category} limit ({$max})." : null;
        }
        $max = $ye ? static::envInt('VEHICLE_MAX_COMPANY_YEAR_END', 4) : static::envInt('VEHICLE_MAX_COMPANY', 2);
        $n   = (clone $base)->whereIn('t.category', $cats)->countAllResults();

        return $n >= $max ? "The company has reached its vehicle limit ({$max})." : null;
    }

    /** Derived status shown to users: an Issued pass past its expiry reads Expired (KPK expiry scheduler). */
    public static function displayStatus(array $v): string
    {
        $s = (string) ($v['status'] ?? '');
        if ($s === 'Issued' && ! empty($v['pass_expiry']) && $v['pass_expiry'] < date('Y-m-d')) {
            return 'Expired';
        }

        return $s;
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

    /**
     * Vehicle Operations a type can be requested under (empty = any). Worked out from the type's KPK flags
     * (Container / Cargo / Non-Cargo); types saved before the flags existed fall back to their single operation.
     */
    public static function typeOperations(array $t): array
    {
        if (array_key_exists('is_container', $t)) {
            $ops = [];
            if (! empty($t['is_container'])) {
                $ops[] = 'CONTAINER';
            }
            if (! empty($t['is_non_cargo'])) {
                $ops[] = 'NON-CARGO';
            }
            if (! empty($t['is_cargo']) || ! empty($t['is_non_cargo'])) {
                $ops[] = 'CARGO AND NON-CARGO';
            }
            if ($ops) {
                $ops[] = 'CONTAINER, CARGO AND NON-CARGO';
            }

            return $ops;
        }

        return ($t['operation'] ?? '') !== '' ? [(string) $t['operation']] : [];
    }

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

    /** Pages of the vehicle module whose columns Config > Page Settings controls (registry key => title). */
    public const LIST_PAGES = ['all' => 'My Vehicle Pass', 'staff' => 'Staff Vehicle Pass', 'request' => 'Request List', 'reviewed' => 'Approved / Reject List', 'closed' => 'Closed List'];

    /**
     * Buttons / actions of the vehicle pages, per list page: key => label. Page Settings stores the on/off flag
     * (client_form_fields, form vehicle_buttons); no row means the button shows. Keys are prefixed "veh_" so
     * they never clash with vendor / staff keys. Actions on the pass screen are listed under the list they belong to.
     */
    /** Buttons that are OFF until someone ticks them in Page Settings. */
    public const BUTTONS_OFF_BY_DEFAULT = ['veh_edit_request', 'veh_delete_request'];

    public static function listButtons(string $stage): array
    {
        $details = ['veh_details_' . $stage => 'Details action'];
        switch ($stage) {
            case 'all':
                return [
                    'veh_request_button'       => 'Request button (all vehicle lists)',
                    'veh_staff_request_button' => 'New Staff Request button (all vehicle lists)',
                    'veh_stat_cards_all'       => 'Summary cards',
                ] + $details + [
                    'veh_edit_action'   => 'Edit action',
                    'veh_submit_action' => 'Submit action',
                    'veh_renew_action'  => 'Renew action',
                    'veh_delete_action' => 'Delete action',
                ];
            case 'staff':
                return ['veh_stat_cards_staff' => 'Summary cards'] + $details;
            case 'request':
                return $details + ['veh_edit_request' => 'Edit action (icon on each row)', 'veh_delete_request' => 'Delete action (icon on each row)',
                    'veh_approve_action' => 'Approve action', 'veh_reject_action' => 'Reject action'];
            case 'reviewed':
                return $details + ['veh_pay_action' => 'Pay online action', 'veh_issue_action' => 'Issue Sticker action'];
            case 'closed':
                return ['veh_stat_cards_closed' => 'Summary cards'] + $details + [
                    'veh_direct_close_button' => 'New Issued Pass button',
                    'veh_reissue_action'      => 'Reissue tool',
                    'veh_serial_action'       => 'Serial No tool',
                    'veh_receipt_action'      => 'Receipt No tool',
                    'veh_editinfo_action'     => 'Edit Vehicle Info tool',
                    'veh_type_action'         => 'Vehicle Type tool',
                    'veh_locations_action'    => 'Edit Location Access tool',
                    'veh_terminate_action'    => 'Terminate Pass action',
                ];
        }

        return $details;
    }

    /** Is this button switched on for the logged-in user's client? (platform superadmin / no client: always) */
    public static function buttonOn(string $key): bool
    {
        static $flags = null;
        if ($flags === null) {
            $flags = [];
            try {
                helper('feature');
                $cid = function_exists('current_client_id') ? (int) current_client_id() : 0;
                if ($cid > 0) {
                    foreach ((new \App\Models\ClientFormFieldModel())->getForCompanyForm($cid, 'vehicle_buttons') as $row) {
                        if (strpos((string) $row['field_key'], 'veh_') === 0) {
                            $flags[$row['field_key']] = (int) $row['is_enabled'] === 1;
                        }
                    }
                }
            } catch (\Throwable $e) {
                $flags = [];
            }
        }

        return $flags[$key] ?? ! in_array($key, self::BUTTONS_OFF_BY_DEFAULT, true);
    }

    /**
     * Request-form field settings (Config > Dynamic Form Fields > Vehicle Pass Request) for the logged-in user's
     * client: [key => ['on' => bool, 'required' => bool]]. A field with no saved choice is on, and required only
     * where the form always insisted on it.
     */
    private static array $ffCache = [];

    public static function forgetFormFields(): void
    {
        self::$ffCache = [];
    }

    /** Dynamic Form Fields of the vehicle request form: "vehicle_pass_request", or "vehicle_staff_request" for a staff pass. */
    public static function formFields(bool $staff = false): array
    {
        $form = $staff ? 'vehicle_staff_request' : 'vehicle_pass_request';
        $cid  = 0;
        try {
            helper('feature');
            $cid = function_exists('current_client_id') ? (int) current_client_id() : 0;
        } catch (\Throwable $e) {
            $cid = 0;
        }
        $ck = $form . ':' . $cid;
        if (! isset(self::$ffCache[$ck])) {
            $map = [];
            try {
                foreach ((new \App\Models\ClientFormFieldModel())->getForCompanyForm($cid, $form) as $row) {
                    $map[$row['field_key']] = ['on' => (int) $row['is_enabled'] === 1, 'required' => (int) $row['is_enabled'] === 1 && (int) $row['is_required'] === 1];
                }
            } catch (\Throwable $e) {
                $map = []; // settings table not there yet: use what the form always did
                foreach (\App\Models\ClientFormFieldModel::vehiclePassFields() as $d) {
                    $map[$d['field_key']] = ['on' => (bool) ($d['default_enabled'] ?? true), 'required' => (bool) ($d['default_required'] ?? false)];
                }
            }
            self::$ffCache[$ck] = $map;
        }

        return self::$ffCache[$ck];
    }

    public static function fieldOn(string $key, bool $staff = false): bool
    {
        return self::formFields($staff)[$key]['on'] ?? true;
    }

    public static function fieldRequired(string $key, bool $staff = false): bool
    {
        return self::fieldOn($key, $staff) && (self::formFields($staff)[$key]['required'] ?? false);
    }

    /**
     * Columns of a vehicle list page: key => ['label', 'locked'?, 'cnc_only'?]. A locked column is always shown.
     * Same column set the list has always had; the keys are what Page Settings stores.
     */
    public static function listColumns(string $stage): array
    {
        $c = [];
        if (in_array($stage, ['request', 'reviewed'], true)) {
            $c['type_of_app'] = ['label' => 'Type Of App'];
        }
        $c += [
            'app_no' => ['label' => 'App No'], 'receipt_no' => ['label' => 'Receipt No'], 'date' => ['label' => 'Date'], 'app_for' => ['label' => 'App For'], 'year' => ['label' => 'Year'],
            'name' => ['label' => 'Name', 'locked' => true], 'ic' => ['label' => 'IC / Passport / Staff ID'], 'company' => ['label' => 'Company / Department'],
            'reg_no' => ['label' => 'Reg No', 'locked' => true], 'type' => ['label' => 'Type Of Vehicle'],
            'operation' => ['label' => 'Operation (Cargo | Non-Cargo)', 'cnc_only' => true], 'head_number' => ['label' => 'Head Number (Cargo | Non-Cargo)', 'cnc_only' => true],
        ];
        if (in_array($stage, ['all', 'staff', 'reviewed'], true)) {
            $c['collect_date'] = ['label' => 'Date Collection'];
        }
        if ($stage === 'closed') {
            $c['serial_no']   = ['label' => 'Serial No'];
            $c['issue_date']  = ['label' => 'Date Of Issuance'];
        }
        $c['status'] = ['label' => 'Status', 'locked' => true];
        if (in_array($stage, ['all', 'staff', 'reviewed'], true)) {
            $c['next_action'] = ['label' => 'Next Action'];
        }
        if (in_array($stage, ['all', 'staff', 'closed'], true)) {
            $c['sticker_status'] = ['label' => 'Sticker Status'];
            $c['road_tax']       = ['label' => 'Road Tax Expiry'];
            $c['insurance']      = ['label' => 'Insurance Expiry'];
        }
        $c['remarks'] = ['label' => 'Remarks'];

        return $c;
    }

    /** Next-action text shown in the lists (KPK nextAction). */
    public static function nextAction(array $v): string
    {
        if (static::displayStatus($v) === 'Expired') {
            return 'Renew Pass';
        }
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
