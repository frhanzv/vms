<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Vendor Workflow: the whole life of a vendor pass, who does each step (Staff / Vendor) and which
 * optional steps are switched on. Defaults live here; Config stores only the changes
 * (vendor_workflow_settings: client_id 0 = platform default, otherwise that client's own).
 * Lookup: client row -> platform row (0) -> built-in default.
 */
class VendorWorkflow
{
    /** key => [name, owner, trigger, locked (always false: every step can be turned off), icon]. The order only decides how the steps are listed. */
    public const STEPS = [
        'company_setup'        => ['Company Setup',        'Staff',          'Staff creates the vendor company and invites it', false,  'domain_add'],
        'vendor_registration'  => ['Vendor Registration',  'Vendor',         'Vendor registers with its company link', false,  'how_to_reg'],
        'account_verification' => ['Account Verification', 'Staff',          'Staff verifies the vendor account so it can log in', false, 'verified_user'],
        'pass_request'         => ['Pass Request',         'Vendor / Staff', 'Vendor requests its employee passes (staff can do it for them)', false, 'assignment'],
        'pass_approval'        => ['Pass Approval',        'Staff',          'Staff approve or reject the employee pass request', false,  'fact_check'],
        'card_processing'      => ['Card Processing',      'Staff',          'Staff add the card photo, save the card details and print the card', false, 'badge'],
        'card_issuance'        => ['Card Issuance',        'Staff',          'Staff hand the card to the collector', false,  'qr_code_2'],
        'completion'           => ['Completion',           'Staff',          'Pass is closed and the card is active', false,  'check_circle'],
    ];

    /** Steps whose on/off really changes behaviour (completion is only a label: every pass still ends in the Closed List). */
    public const ENFORCED = ['company_setup', 'vendor_registration', 'account_verification', 'pass_request', 'pass_approval', 'card_processing', 'card_issuance'];

    private static array $cache = [];

    /** @return array<string,array> key => saved row, for one scope (0 = platform default) */
    private static function rows(BaseConnection $db, int $clientId): array
    {
        if (! $db->tableExists('vendor_workflow_settings')) {
            return [];
        }
        $k = spl_object_id($db) . ':' . $clientId;
        if (! isset(self::$cache[$k])) {
            self::$cache[$k] = array_column($db->table('vendor_workflow_settings')->where('client_id', $clientId)->get()->getResultArray(), null, 'step_key');
        }

        return self::$cache[$k];
    }

    public static function forget(): void
    {
        self::$cache = [];
    }

    public const OWNERS = ['Staff', 'Vendor', 'Vendor / Staff'];

    /** Steps in the saved sequence, with the saved name / description / on-off applied. Custom steps (added on Edit Sequence) are included. */
    public static function steps(BaseConnection $db, int $clientId): array
    {
        $own = $clientId > 0 ? self::rows($db, $clientId) : [];
        $def = self::rows($db, 0);
        $keys = array_keys(self::STEPS);
        foreach ([$def, $own] as $set) {
            foreach ($set as $k => $r) {
                if ((int) ($r['is_custom'] ?? 0) === 1 && ! in_array($k, $keys, true)) {
                    $keys[] = $k;
                }
            }
        }
        $out = [];
        foreach ($keys as $i => $key) {
            $row    = $own[$key] ?? $def[$key] ?? null;
            $custom = ! isset(self::STEPS[$key]);
            [$name, $owner, $trigger, $locked, $icon] = self::STEPS[$key] ?? ['', 'Staff', '', false, 'task_alt'];
            $pick = static fn (string $f, $fallback) => (($own[$key][$f] ?? '') !== '' && ($own[$key][$f] ?? null) !== null) ? $own[$key][$f] : ((($def[$key][$f] ?? '') !== '' && ($def[$key][$f] ?? null) !== null) ? $def[$key][$f] : $fallback);
            $sort = $own[$key]['sort_order'] ?? $def[$key]['sort_order'] ?? null;
            $out[] = [
                'key' => $key, 'icon' => $icon, 'locked' => $locked, 'is_custom' => $custom, 'enforced' => in_array($key, self::ENFORCED, true),
                'owner' => $custom ? $pick('owner', 'Staff') : $owner,
                'name' => $pick('step_name', $name),
                'trigger' => $pick('trigger_event', $trigger),
                'is_active' => $locked ? true : (! $row || (int) $row['is_active'] === 1),
                'own' => isset($own[$key]) || ($clientId === 0 && isset($def[$key])),
                '_sort' => $sort === null ? $i + 1 : (int) $sort, '_i' => $i,
            ];
        }
        usort($out, static fn ($x, $y) => [$x['_sort'], $x['_i']] <=> [$y['_sort'], $y['_i']]);
        foreach ($out as $n => &$r) {
            $r['order'] = $n + 1;
            unset($r['_sort'], $r['_i']);
        }

        return $out;
    }

    /** Is this step switched on? Locked and unknown steps are always on. */
    public static function enabled(BaseConnection $db, int $clientId, string $key): bool
    {
        if (! isset(self::STEPS[$key]) || self::STEPS[$key][3]) {
            return true;
        }
        $row = (($clientId > 0 ? self::rows($db, $clientId)[$key] ?? null : null)) ?? (self::rows($db, 0)[$key] ?? null);

        return ! $row || (int) $row['is_active'] === 1;
    }

    /** Does the client use this step? (same as enabled(), named for readability at call sites) */
    public static function on(BaseConnection $db, int $clientId, string $key): bool
    {
        return self::enabled($db, $clientId, $key);
    }

    /** Extra columns for a pass that has just been approved: with no card steps at all the pass is complete (card Active) right away. */
    public static function approvedUpdate(BaseConnection $db, int $clientId): array
    {
        return (! self::enabled($db, $clientId, 'card_processing') && ! self::enabled($db, $clientId, 'card_issuance')) ? ['card_status' => 'Active'] : [];
    }

    /** Pass Approval switched off: a freshly submitted (Pending) pass is approved on the spot. */
    public static function autoApprove(BaseConnection $db, int $vendorId, int $clientId): bool
    {
        if (self::enabled($db, $clientId, 'pass_approval')) {
            return false;
        }
        $db->table('vendors')->where('id', $vendorId)->where('status', 'Pending')
            ->update(['status' => 'Approved', 'next_action' => null, 'reject_reason' => null] + self::approvedUpdate($db, $clientId));
        $db->table('vendor_client_approvals')->where('vendor_id', $vendorId)->update(['status' => 'Approved', 'updated_at' => date('Y-m-d H:i:s')]);

        return true;
    }

    /**
     * Card steps were switched off: move passes that are already in flight to where they now belong, so none is left
     * waiting at a step that no longer exists. Turning a step back on never undoes this (an issued card stays issued).
     * Safe to run again.
     *
     * @param int[] $clientIds
     */
    public static function settle(BaseConnection $db, array $clientIds): void
    {
        foreach ($clientIds as $cid) {
            $proc = self::enabled($db, (int) $cid, 'card_processing');
            $iss  = self::enabled($db, (int) $cid, 'card_issuance');
            if ($iss) {
                continue;
            }
            $b = $db->table('vendors')->where('company_id', (int) $cid)->where('status', 'Approved')->where('card_status', 'Inactive');
            if ($proc) { // processing stays: only cards already printed skip the issuance step
                $b->where("receipt_no IS NOT NULL AND receipt_no <> ''", null, false);
            }
            $b->update(['card_status' => 'Active']);
        }
    }
}
