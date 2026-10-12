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
    /** key => [name, owner, trigger, locked (cannot be turned off), icon] — the order is fixed: each step needs the one before. */
    public const STEPS = [
        'company_setup'        => ['Company Setup',        'Staff',          'Staff creates the vendor company and invites it',               true,  'domain_add'],
        'vendor_registration'  => ['Vendor Registration',  'Vendor',         'Vendor registers with its company link',                         true,  'how_to_reg'],
        'account_verification' => ['Account Verification', 'Staff',          'Staff verifies the vendor account so it can log in',             false, 'verified_user'],
        'pass_request'         => ['Pass Request',         'Vendor / Staff', 'Vendor requests its employee passes (staff can do it for them)', true,  'assignment'],
        'pass_approval'        => ['Pass Approval',        'Staff',          'Staff approve or reject the employee pass request',              true,  'fact_check'],
        'card_photo'           => ['Card Photo',           'Staff',          'Staff upload or take the card photo on the Process List',        false, 'photo_camera'],
        'card_details'         => ['Card Details',         'Staff',          'Staff save the card expiry and worker type',                      true,  'badge'],
        'card_printing'        => ['Card Printing',        'Staff',          'Staff print the card',                                            false, 'print'],
        'card_issuance'        => ['Card Issuance',        'Staff',          'Staff hand the card to the collector',                            true,  'qr_code_2'],
        'completion'           => ['Completion',           'Staff',          'Pass is closed and the card is active',                           true,  'check_circle'],
    ];

    /** Steps whose on/off really changes behaviour today. */
    public const ENFORCED = ['account_verification', 'card_photo', 'card_printing'];

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

    /** Steps in order with the saved name / description / on-off applied. */
    public static function steps(BaseConnection $db, int $clientId): array
    {
        $own = $clientId > 0 ? self::rows($db, $clientId) : [];
        $def = self::rows($db, 0);
        $out = [];
        $n   = 0;
        foreach (self::STEPS as $key => [$name, $owner, $trigger, $locked, $icon]) {
            $row = $own[$key] ?? $def[$key] ?? null;
            $out[] = [
                'key' => $key, 'order' => ++$n, 'owner' => $owner, 'locked' => $locked, 'icon' => $icon, 'enforced' => in_array($key, self::ENFORCED, true),
                'name' => ($own[$key]['step_name'] ?? '') !== '' ? $own[$key]['step_name'] : (($def[$key]['step_name'] ?? '') !== '' ? $def[$key]['step_name'] : $name),
                'trigger' => ($own[$key]['trigger_event'] ?? '') !== '' ? $own[$key]['trigger_event'] : (($def[$key]['trigger_event'] ?? '') !== '' ? $def[$key]['trigger_event'] : $trigger),
                'is_active' => $locked ? true : (! $row || (int) $row['is_active'] === 1),
                'custom' => $row !== null && ($own[$key] ?? null) !== null,
            ];
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
}
