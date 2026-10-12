<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Gate in / gate out for KPK staff passes.
 *
 * A staff card is a row in the shared visitor_cards pool bound to a staff pass
 * through staff.card_id (StaffCardInfo::readCard). It stays bound for the life
 * of the pass, so unlike a visitor card it is never released on check-out.
 *
 * Toggle, as for visitors and vendors: a scan with no open visit is a gate-in,
 * a scan with an open visit is a gate-out.
 *
 * Gate-in is refused (and logged as 'denied', which KPK's Staff Report shows
 * as "Rejected") unless, as in KPK:
 *   - the pass is Approved            (not Pending / Rejected / Suspended)
 *   - the card is Active              (not Inactive / Terminated)
 *   - the person is still employed    (is_active = 1, the Inactive Staff list)
 *   - the card has not expired        (card_expiry)
 *   - the lane is in the pass's Location Access, when the pass has any.
 * Gate-out is always allowed so nobody is trapped on site.
 */
class StaffGate
{
    /** KPK's out-of-window rule: on site for a day or more. */
    public const OUT_OF_WINDOW_HOURS = 24;

    public static function ready(BaseConnection $db): bool
    {
        static $ready = null;
        return $ready ??= ($db->tableExists('staff_visits') && $db->tableExists('staff_card_logs'));
    }

    /**
     * Handle a scan of $card (a visitor_cards row).
     *
     * Returns null when the card is not a staff card, so the caller can carry on
     * with its own "unknown card" handling. Otherwise returns the JSON body to
     * send; when it has an 'alert' key the caller should raise that security alert
     * (and drop the key before responding).
     */
    public static function scan(BaseConnection $db, array $card, ?int $laneId = null): ?array
    {
        if (! self::ready($db)) {
            return null;
        }

        $cardId = (int) $card['id'];
        $epc    = (string) ($card['card_id'] ?? '');
        $now    = date('Y-m-d H:i:s');

        $db->transStart();

        // Staff currently holding this card, or someone still inside on it
        // (a card terminated while its holder is on site must still let them out).
        $staff = $db->query('SELECT * FROM staff WHERE card_id = ? LIMIT 1 FOR UPDATE', [$cardId])->getRowArray();

        $openVisit = $staff
            ? $db->query('SELECT * FROM staff_visits WHERE staff_id = ? AND check_out_time IS NULL ORDER BY id DESC LIMIT 1 FOR UPDATE', [(int) $staff['id']])->getRowArray()
            : $db->query('SELECT * FROM staff_visits WHERE card_id = ? AND check_out_time IS NULL ORDER BY id DESC LIMIT 1 FOR UPDATE', [$cardId])->getRowArray();

        if (! $staff && ! $openVisit) {
            $db->transRollback();
            return null;
        }
        if (! $staff) {
            $staff = $db->table('staff')->where('id', (int) $openVisit['staff_id'])->get()->getRowArray() ?: ['id' => (int) $openVisit['staff_id'], 'full_name' => 'Staff #' . $openVisit['staff_id']];
        }
        $name = (string) ($staff['full_name'] ?? 'Staff');

        // ---- Gate out ----
        if ($openVisit) {
            $db->table('staff_visits')
                ->where('id', (int) $openVisit['id'])
                ->where('check_out_time IS NULL', null, false)
                ->update([
                    'check_out_time'    => $now,
                    'check_out_lane_id' => $laneId,
                    'version'           => (int) ($openVisit['version'] ?? 1) + 1,
                    'updated_at'        => $now,
                ]);

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return self::body(false, 'denied', 'Staff has already been checked out', $epc, $name);
            }

            self::log($db, (int) $staff['id'], $cardId, 'checkout', $laneId, null, $now);
            $db->transComplete();

            $seconds = max(0, strtotime($now) - strtotime((string) $openVisit['check_in_time']));
            $body    = self::body(true, 'checkout', 'Checked out: ' . $name, $epc, $name);
            $body['duration'] = self::formatDuration($seconds);

            return $body;
        }

        // ---- Gate in ----
        $reason = self::denyReason($db, $staff, $laneId);
        if ($reason !== null) {
            self::log($db, (int) $staff['id'], $cardId, 'denied', $laneId, $reason, $now);
            $db->transComplete();

            $body = self::body(false, 'denied', 'Access denied: ' . $reason, $epc, $name);
            $body['alert'] = 'Staff pass refused at gate (' . $name . ', card ' . $epc . '): ' . $reason;

            return $body;
        }

        $db->table('staff_visits')->insert([
            'staff_id'         => (int) $staff['id'],
            'card_id'          => $cardId,
            'check_in_time'    => $now,
            'check_in_lane_id' => $laneId,
            'created_at'       => $now,
        ]);
        self::log($db, (int) $staff['id'], $cardId, 'checkin', $laneId, null, $now);
        $db->transComplete();

        return self::body(true, 'checkin', 'Checked in: ' . $name, $epc, $name);
    }

    /** Why this staff pass may not gate in right now, or null when it may. */
    public static function denyReason(BaseConnection $db, array $staff, ?int $laneId): ?string
    {
        $status = (string) ($staff['status'] ?? '');
        if ($status !== 'Approved') {
            return 'staff pass is ' . ($status !== '' ? $status : 'not approved') . '.';
        }
        if (array_key_exists('is_active', $staff) && (int) $staff['is_active'] !== 1) {
            return 'staff is marked inactive.';
        }
        $cardStatus = (string) ($staff['card_status'] ?? '');
        if ($cardStatus !== 'Active') {
            return 'card is ' . ($cardStatus !== '' ? $cardStatus : 'not activated') . '.';
        }
        $expiry = (string) ($staff['card_expiry'] ?? '');
        if ($expiry !== '' && ! str_starts_with($expiry, '0000') && strtotime($expiry) < strtotime(date('Y-m-d'))) {
            return 'card expired on ' . date('d/m/Y', strtotime($expiry)) . '.';
        }

        if ($laneId) {
            $allowed = array_values(array_filter(array_map('trim', explode(',', (string) ($staff['location_access'] ?? ''))), 'strlen'));
            if ($allowed !== []) {
                $lane = $db->query(
                    'SELECT l.lane, loc.location_access FROM lanes l LEFT JOIN locations loc ON loc.id = l.location_id WHERE l.id = ?',
                    [$laneId]
                )->getRowArray();
                $gate = trim((string) ($lane['location_access'] ?? ''));
                if ($gate !== '' && ! in_array(strtolower($gate), array_map('strtolower', $allowed), true)) {
                    return 'no location access to ' . $gate . '.';
                }
            }
        }

        return null;
    }

    public static function formatDuration(int $seconds): string
    {
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        return $h > 0 ? sprintf('%dh %02dm', $h, $m) : sprintf('%dm', $m);
    }

    private static function log(BaseConnection $db, int $staffId, int $cardId, string $action, ?int $laneId, ?string $message, string $at): void
    {
        $db->table('staff_card_logs')->insert([
            'staff_id'   => $staffId,
            'card_id'    => $cardId,
            'action'     => $action,
            'lane_id'    => $laneId,
            'message'    => $message !== null ? mb_substr($message, 0, 255) : null,
            'scanned_at' => $at,
            'created_at' => $at,
        ]);
    }

    private static function body(bool $granted, string $action, string $message, string $epc, string $name): array
    {
        return [
            'success'        => $granted,
            'access_granted' => $granted,
            'action'         => $action,
            'holder_type'    => 'staff',
            'message'        => $message,
            'staff_name'     => $name,
            'card_epc'       => $epc,
        ];
    }
}
