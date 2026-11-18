<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Vendor pass renewal (KPK renewPortPass), with two VMS changes:
 *  - the Renew button opens N months before the pass expires, N set per client on the Vendor Workflow page
 *  - the OLD card keeps working at the gate while the renewal goes through approval, process, printing
 *    and issuance. The new card (renew_card_id) and expiry (renew_expiry) are held aside and only replace
 *    the old ones when the renewed card is issued (finish()).
 *
 * The renewal reuses the same vendors row (like KPK): status goes back to Pending, the Pass No (receipt_no)
 * is cleared so the pass passes through the Process List again, and renewal_pending marks it.
 */
class VendorRenewal
{
    public const DEFAULT_MONTHS = 1;
    public const MAX_MONTHS     = 12;

    public static function ready(BaseConnection $db): bool
    {
        return $db->fieldExists('renewal_pending', 'vendors');
    }

    private static function key(int $clientId): string
    {
        return 'vendor_renewal_months_' . $clientId;
    }

    /** Months before expiry the Renew button opens: the client's own value, else the platform default, else 1. */
    public static function months(BaseConnection $db, int $clientId): int
    {
        foreach (array_unique([$clientId, 0]) as $cid) {
            $v = $db->table('settings')->select('setting_value')->where('setting_key', self::key($cid))->get()->getRow('setting_value');
            if ($v !== null && $v !== '') {
                return max(1, min(self::MAX_MONTHS, (int) $v));
            }
        }

        return self::DEFAULT_MONTHS;
    }

    public static function saveMonths(BaseConnection $db, int $clientId, int $months): void
    {
        $key = self::key($clientId);
        $now = date('Y-m-d H:i:s');
        $val = (string) max(1, min(self::MAX_MONTHS, $months));
        $db->table('settings')->where('setting_key', $key)->countAllResults() > 0
            ? $db->table('settings')->where('setting_key', $key)->update(['setting_value' => $val, 'updated_at' => $now])
            : $db->table('settings')->insert(['setting_key' => $key, 'setting_value' => $val, 'created_at' => $now, 'updated_at' => $now]);
    }

    /** First day the Renew button shows for this pass (Y-m-d), or null without an expiry. */
    public static function opensOn(array $v, int $months): ?string
    {
        return empty($v['pass_expiry']) ? null : date('Y-m-d', strtotime($v['pass_expiry'] . " -{$months} months"));
    }

    /** Why this pass cannot be renewed now, or null when it can. */
    public static function block(BaseConnection $db, array $v): ?string
    {
        if (! self::ready($db)) {
            return 'Run php spark migrate first.';
        }
        if (! empty($v['renewal_pending'])) {
            return 'A renewal for this pass is already in progress.';
        }
        // Closed = a card was issued. A renewal that was rejected keeps its card, so it can be renewed again.
        if (($v['card_status'] ?? '') !== 'Active' || ! in_array($v['status'] ?? '', ['Approved', 'Rejected'], true)) {
            return 'Only a closed vendor pass (card issued and active) can be renewed.';
        }
        $months = self::months($db, (int) ($v['company_id'] ?? 0));
        $opens  = self::opensOn($v, $months);
        if ($opens === null) {
            return 'This pass has no expiry date, so there is nothing to renew.';
        }
        if (date('Y-m-d') < $opens) {
            return 'Renewal opens on ' . date('d M Y', strtotime($opens)) . ' (' . $months . ' month' . ($months > 1 ? 's' : '') . ' before the pass expires on ' . date('d M Y', strtotime($v['pass_expiry'])) . ').';
        }
        if (TwpGate::blacklisted($db, $v['ic_no'] ?? null, $v['passport_no'] ?? null)) {
            return 'IC No or Passport No is blacklisted / suspended.';
        }

        return null;
    }

    public static function canRenew(BaseConnection $db, array $v): bool
    {
        return self::block($db, $v) === null;
    }

    /**
     * What the Renew action shows on a list row: ['pending'] while a renewal runs, ['open'] when it can start now,
     * ['opens', 'Y-m-d'] before the window, or null (not a closed pass / not allowed).
     */
    public static function rowState(BaseConnection $db, array $v, bool $allowed): ?array
    {
        if (! self::ready($db) || ($v['card_status'] ?? '') !== 'Active') {
            return null;
        }
        if (! empty($v['renewal_pending'])) {
            return ['pending'];
        }
        if (! $allowed) {
            return null;
        }
        if (self::block($db, $v) === null) {
            return ['open'];
        }
        $opens = self::opensOn($v, self::months($db, (int) ($v['company_id'] ?? 0)));

        return $opens !== null && date('Y-m-d') < $opens ? ['opens', $opens] : null;
    }

    /** The expiry the renewal is working towards (Process / Printing show this), else the live one. */
    public static function expiryOf(array $v): ?string
    {
        return ! empty($v['renewal_pending']) ? ($v['renew_expiry'] ?? null) : ($v['pass_expiry'] ?? null);
    }

    /** The row as the Process / Printing steps work on it: during a renewal, the new card and expiry instead of the live ones. */
    public static function working(array $v): array
    {
        if (! empty($v['renewal_pending'])) {
            $v['pass_expiry'] = $v['renew_expiry'] ?? null;
            $v['card_id']     = $v['renew_card_id'] ?? null;
        }

        return $v;
    }

    /** Process / Printing / Issuance lists: passes waiting for a card, including renewals whose old card is still active. */
    public static function needsCardSql(BaseConnection $db): string
    {
        return self::ready($db)
            ? "(COALESCE(card_status, 'Inactive') <> 'Active' OR renewal_pending = 1)"
            : "COALESCE(card_status, 'Inactive') <> 'Active'";
    }

    /**
     * The renewed card is handed over (or the card steps are off): the new card and expiry replace the old ones.
     * The old card goes back to the card pool unless the same card was kept.
     */
    public static function finish(BaseConnection $db, int $vendorId): void
    {
        if (! self::ready($db)) {
            return;
        }
        $v = $db->table('vendors')->where('id', $vendorId)->get()->getRowArray();
        if (! $v || empty($v['renewal_pending'])) {
            return;
        }
        $update = ['renewal_pending' => 0, 'renew_card_id' => null, 'renew_expiry' => null, 'card_status' => 'Active'];
        if (! empty($v['renew_expiry'])) {
            $update['pass_expiry'] = $v['renew_expiry'];
        }
        if (! empty($v['renew_card_id']) && (int) $v['renew_card_id'] !== (int) $v['card_id']) {
            if (! empty($v['card_id'])) {
                $db->table('visitor_cards')->where('id', (int) $v['card_id'])->where('status !=', 'lost')->update(['status' => 'active']);
            }
            $update['card_id'] = (int) $v['renew_card_id'];
        }
        $db->table('vendors')->where('id', $vendorId)->update($update);
    }

    /** A renewal was rejected: the old card and expiry stay as they are; a card set aside for it is released. */
    public static function cancel(BaseConnection $db, array $v): array
    {
        if (! self::ready($db) || empty($v['renewal_pending'])) {
            return [];
        }
        if (! empty($v['renew_card_id']) && (int) $v['renew_card_id'] !== (int) $v['card_id']) {
            $db->table('visitor_cards')->where('id', (int) $v['renew_card_id'])->where('status', 'in_use')->update(['status' => 'active']);
        }

        return ['renewal_pending' => 0, 'renew_card_id' => null, 'renew_expiry' => null, 'card_status' => 'Active'];
    }
}
