<?php

namespace App\Libraries;

/**
 * The small KPK Config lists that share one shape (fields + Active/Inactive status, client_id NULL =
 * shared by every client). Config > <list> is driven by these definitions (App\Controllers\LookupConfig),
 * and the helpers below are what the rest of VMS reads: TWP rates, Approving Branch, release reasons,
 * kiosk closing time.
 */
class ConfigLookups
{
    public const DAYS = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];

    /**
     * kind: text | textarea | select | money | time | date | businesstype | state | meetingroom
     * filter: field shown as a list filter; sort: field => label for Sort By A-Z / Z-A;
     * client_pick: the platform superadmin can pick a client and manage that client's rows.
     *
     * @return array<string, array{table:string, title:string, access:string, noun:string, search:list<string>, order:list<string>, unique:list<string>, fields:array<string, array>}>
     */
    public static function definitions(): array
    {
        return [
            'payment-types' => [
                'table' => 'payment_types', 'title' => 'Payment Type', 'access' => 'payment_type', 'noun' => 'payment type',
                'hint' => 'Amount charged per pass by type (STAFF = per worker, LORRY = per worker with a vehicle). Used for the Temporary Work Pass total.',
                'search' => ['type', 'period'], 'order' => ['type', 'period'], 'unique' => ['type', 'period'],
                'client_pick' => true, // KPK payment-info: the admin picks the company whose rates to manage
                'fields' => [
                    'type'   => ['label' => 'Payment Type', 'kind' => 'select', 'options' => ['STAFF', 'LORRY'], 'required' => true],
                    'period' => ['label' => 'Period', 'kind' => 'select', 'options' => ['ONE WEEK', 'ONE MONTH'], 'required' => true],
                    'amount' => ['label' => 'Amount (RM)', 'kind' => 'money', 'required' => true],
                ],
            ],
            'branch-offices' => [
                'table' => 'branch_offices', 'title' => 'Branch Office', 'access' => 'branch_office', 'noun' => 'branch office',
                'hint' => 'Approving branches (KSB / KPK) with name and address. The name shows in the staff Approving Branch list.',
                'search' => ['name', 'code', 'state'], 'order' => ['code', 'name'], 'unique' => ['code'],
                'sort' => ['name' => 'Branch'], // KPK Sort By: Branch A-Z / Z-A
                'fields' => [
                    'code'    => ['label' => 'Branch Code', 'kind' => 'select', 'options' => ['KSB', 'KPK'], 'required' => true],
                    'name'    => ['label' => 'Branch Name', 'kind' => 'text', 'max' => 150, 'required' => true],
                    'address' => ['label' => 'Branch Address', 'kind' => 'textarea', 'max' => 500, 'required' => true],
                    'state'   => ['label' => 'State', 'kind' => 'state', 'required' => true],
                ],
            ],
            'release-reasons' => [
                'table' => 'release_reasons', 'title' => 'Release Reason', 'access' => 'release_reason', 'noun' => 'release reason',
                'hint' => 'Reasons an officer picks when releasing someone from the blacklist.',
                'search' => ['reason'], 'order' => ['blacklist_type', 'reason'], 'unique' => ['reason', 'blacklist_type'],
                'filter' => 'blacklist_type', // KPK: Blacklist / Suspend filter on the list
                'fields' => [
                    'reason'         => ['label' => 'Release Reason', 'kind' => 'text', 'max' => 255, 'required' => true],
                    'blacklist_type' => ['label' => 'Blacklist Type', 'kind' => 'select', 'options' => ['BLACKLIST', 'SUSPEND'], 'required' => true],
                ],
            ],
            'operating-hours' => [
                'table' => 'operating_hours', 'title' => 'Operating Hours', 'access' => 'operating_hours', 'noun' => 'operating hours',
                'hint' => 'Opening and closing time per day. A kiosk walk-in pass expires at the day\'s closing time.',
                'search' => ['day'], 'order' => ['day'], 'unique' => ['day'],
                'clock_toggle' => true, // KPK 12H / 24H button
                'fields' => [
                    'day'        => ['label' => 'Day', 'kind' => 'select', 'options' => self::DAYS, 'required' => true],
                    'start_time' => ['label' => 'Start Time', 'kind' => 'time', 'required' => true],
                    'end_time'   => ['label' => 'End Time', 'kind' => 'time', 'required' => true],
                ],
            ],
            'meeting-sessions' => [
                'table' => 'meeting_sessions', 'title' => 'Meeting Session', 'access' => 'meeting_session', 'noun' => 'meeting session',
                'hint' => 'Sessions visitors can be booked into: meeting room (Location Visited marked as meeting room), date, time and how many visitors.',
                'search' => [], 'search_room' => true, 'order' => ['session_date', 'start_time'], 'unique' => ['location_visited_id', 'session_date', 'start_time'],
                'clock_toggle' => true, // KPK 12H / 24H button
                'fields' => [
                    'location_visited_id' => ['label' => 'Meeting Room', 'kind' => 'meetingroom', 'required' => true],
                    'session_date'        => ['label' => 'Session Date', 'kind' => 'date', 'required' => true],
                    'start_time'          => ['label' => 'Start Time', 'kind' => 'time', 'required' => true],
                    'end_time'            => ['label' => 'End Time', 'kind' => 'time', 'required' => true],
                    'num_visitor'         => ['label' => 'Number Of Visitor', 'kind' => 'select', 'options' => ['5', '10', '15', '20', '25', '30', '35', '40', '45', '50'], 'required' => true],
                ],
            ],
            'bank-names' => [
                'table' => 'bank_names', 'title' => 'Bank Name', 'access' => 'bank_name', 'noun' => 'bank name',
                'hint' => 'List of banks.',
                'search' => ['name'], 'order' => ['name'], 'unique' => ['name'],
                'fields' => [
                    'name' => ['label' => 'Bank Name', 'kind' => 'text', 'max' => 150, 'required' => true],
                ],
            ],
            'sub-business-types' => [
                'table' => 'sub_business_types', 'title' => 'Sub Business Type', 'access' => 'sub_business_type', 'noun' => 'sub business type',
                'hint' => 'Sub categories under a business type, and whether the PLI document is mandatory or optional.',
                'search' => ['name'], 'order' => ['name'], 'unique' => ['name', 'business_type_id'],
                'filter' => 'business_type_id', // KPK: Business Type filter on the list
                'fields' => [
                    'name'             => ['label' => 'Sub Business Type', 'kind' => 'text', 'max' => 150, 'required' => true],
                    'business_type_id' => ['label' => 'Business Type', 'kind' => 'businesstype', 'required' => true],
                    'pli'              => ['label' => 'PLI', 'kind' => 'select', 'options' => ['' => 'DEFAULT', 'MANDATORY' => 'MANDATORY', 'OPTIONAL' => 'OPTIONAL']],
                ],
            ],
        ];
    }

    /** The signed-in user's client (null for the platform superadmin = shared rows only). */
    public static function currentClient(): ?int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? null : ((int) current_client_id() ?: null);
    }

    /** Active rows a client can use: its own first, then the shared ones (client_id NULL). */
    public static function active(string $table, ?int $clientId, string $orderBy = 'id'): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists($table)) {
            return [];
        }
        $b = $db->table($table)->where('status', 'active');
        if ($clientId) {
            $b->groupStart()->where('client_id', $clientId)->orWhere('client_id IS NULL', null, false)->groupEnd()
                ->orderBy('client_id IS NULL', 'ASC', false);
        } else {
            $b->where('client_id IS NULL', null, false);
        }

        return $b->orderBy($orderBy)->get()->getResultArray();
    }

    /**
     * TWP rates from Payment Type: STAFF = per worker, LORRY = per worker with a vehicle.
     * The client's own active row wins over a shared one; with several periods ONE MONTH (the KPK
     * default) is preferred, then the latest one updated. A type with no row keeps $fallback.
     *
     * @param array{worker: float, vehicle: float} $fallback
     * @return array{worker: float, vehicle: float, source: array{worker: string, vehicle: string}}
     */
    public static function twpRates(?int $clientId, array $fallback): array
    {
        $out = $fallback + ['source' => ['worker' => 'settings', 'vehicle' => 'settings']];
        $pick = [];
        foreach (self::active('payment_types', $clientId, 'updated_at DESC, id') as $r) {
            $key = ['STAFF' => 'worker', 'LORRY' => 'vehicle'][strtoupper((string) $r['type'])] ?? null;
            if (! $key) {
                continue;
            }
            $rank = [$r['client_id'] === null ? 1 : 0, strtoupper((string) $r['period']) === 'ONE MONTH' ? 0 : 1];
            if (! isset($pick[$key]) || $rank < $pick[$key][0]) {
                $pick[$key] = [$rank, $r];
            }
        }
        foreach ($pick as $key => [, $r]) {
            $out[$key]           = (float) $r['amount'];
            $out['source'][$key] = 'payment_type';
        }

        return $out;
    }

    /**
     * Approving Branch choices keyed by the KSB / KPK code VMS approval uses, labelled with the
     * Branch Office name when one is set up ("KSB - Kuantan Office"). Without branch offices it is
     * the original fixed list.
     *
     * @return array<string, string>
     */
    public static function branchOptions(?int $clientId, string $blank = '-- Select --', string $both = 'Both (KSB + KPK)'): array
    {
        $names = [];
        foreach (self::active('branch_offices', $clientId, 'id') as $r) {
            $code = strtoupper((string) $r['code']);
            if (in_array($code, ['KSB', 'KPK'], true) && ! isset($names[$code])) {
                $names[$code] = trim((string) $r['name']);
            }
        }
        $label = static fn(string $c) => isset($names[$c]) && $names[$c] !== '' && strcasecmp($names[$c], $c) !== 0 ? "{$c} - {$names[$c]}" : $c;

        return ['' => $blank, 'KSB' => $label('KSB'), 'KPK' => $label('KPK'), 'BOTH' => $both];
    }

    /** Active release reasons for a blacklist type (BLACKLIST / SUSPEND). */
    public static function releaseReasons(?int $clientId, string $type = 'BLACKLIST'): array
    {
        $out = [];
        foreach (self::active('release_reasons', $clientId, 'reason') as $r) {
            if (strtoupper((string) $r['blacklist_type']) === $type && ! in_array($r['reason'], $out, true)) {
                $out[] = $r['reason'];
            }
        }

        return $out;
    }

    /** Closing time ('H:i:s') set for the day of $date, or null when no operating hours are set. */
    public static function closingTime(?int $clientId, ?string $date = null): ?string
    {
        $day = strtoupper(date('l', $date ? strtotime($date) : time()));
        foreach (self::active('operating_hours', $clientId, 'id') as $r) {
            if (strtoupper((string) $r['day']) === $day && ! empty($r['end_time'])) {
                return date('H:i:s', strtotime((string) $r['end_time']));
            }
        }

        return null;
    }
}
