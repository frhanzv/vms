<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Gate in / gate out for KPK Temporary Work Passes, the same toggle as StaffGate:
 * a scan with no open visit is a gate-in, with an open visit a gate-out.
 *
 * A TWP card is a row in twp_cards (KPK "Temporary Work Pass Card") issued to one
 * worker (twp_workers.card_id) from the Pass List until it is returned.
 *
 * Gate-in is refused (logged 'denied') unless:
 *   - the request and the worker are Approved and the card is Issued
 *   - now is inside the pass window (date_from .. date_to)
 *   - the lane is in the request's Location Access, when it has any.
 * Gate-out is always allowed so nobody is trapped on site.
 */
class TwpGate
{
    /** Dashboard "Near Out Of Window": inside and the window ends within this many minutes. */
    public const NEAR_MINUTES = 60;

    public static function ready(BaseConnection $db): bool
    {
        static $ready = null;

        return $ready ??= ($db->tableExists('twp_visits') && $db->tableExists('twp_cards'));
    }

    /** Active blacklist hit for an IC / passport, or null. */
    public static function blacklisted(BaseConnection $db, ?string ...$ids): bool
    {
        $ids = array_values(array_filter(array_map(static fn($v) => trim((string) $v), $ids), 'strlen'));

        return $ids && $db->tableExists('blacklist')
            && $db->table('blacklist')->whereIn('ic_passport_no', $ids)->where('status', 'active')->countAllResults() > 0;
    }

    /** Handle a scan of card EPC $epc. Null when it is not a TWP card (the caller carries on). */
    public static function scan(BaseConnection $db, string $epc, ?int $laneId = null): ?array
    {
        if (! self::ready($db) || $epc === '') {
            return null;
        }
        $card = $db->table('twp_cards')->where('card_id', $epc)->get()->getRowArray();
        if (! $card) {
            return null;
        }
        $cardId = (int) $card['id'];
        $now    = date('Y-m-d H:i:s');
        $db->transStart();

        $open   = $db->query('SELECT * FROM twp_visits WHERE card_id = ? AND check_out_time IS NULL ORDER BY id DESC LIMIT 1 FOR UPDATE', [$cardId])->getRowArray();
        $worker = $db->query("SELECT w.*, r.status AS request_status, r.date_from, r.date_to, r.location_access, r.company_name
                FROM twp_workers w JOIN twp_requests r ON r.id = w.request_id
                WHERE " . ($open ? 'w.id = ?' : "w.card_id = ? AND w.card_status = 'Issued'") . ' LIMIT 1', [$open ? (int) $open['worker_id'] : $cardId])->getRowArray();
        $name = (string) ($worker['name'] ?? 'TWP card ' . $card['card_no']);

        // ---- Gate out
        if ($open) {
            $db->table('twp_visits')->where('id', (int) $open['id'])->where('check_out_time IS NULL', null, false)
                ->update(['check_out_time' => $now, 'check_out_lane_id' => $laneId, 'updated_at' => $now]);
            self::log($db, (int) $open['worker_id'], $cardId, 'checkout', $laneId, null, $now);
            $db->transComplete();
            $body             = self::body(true, 'checkout', 'Checked out: ' . $name, $epc, $name);
            $body['duration'] = StaffGate::formatDuration(max(0, strtotime($now) - strtotime((string) $open['check_in_time'])));

            return $body;
        }

        // ---- Gate in
        $reason = self::denyReason($db, $card, $worker, $laneId, $now);
        if ($reason !== null) {
            self::log($db, $worker ? (int) $worker['id'] : null, $cardId, 'denied', $laneId, $reason, $now);
            $db->transComplete();
            $body          = self::body(false, 'denied', 'Access denied: ' . $reason, $epc, $name);
            $body['alert'] = 'Temporary work pass refused at gate (' . $name . ', card ' . $card['card_no'] . '): ' . $reason;

            return $body;
        }
        $db->table('twp_visits')->insert(['worker_id' => (int) $worker['id'], 'card_id' => $cardId, 'check_in_time' => $now, 'check_in_lane_id' => $laneId, 'created_at' => $now]);
        self::log($db, (int) $worker['id'], $cardId, 'checkin', $laneId, null, $now);
        $db->transComplete();

        return self::body(true, 'checkin', 'Checked in: ' . $name, $epc, $name);
    }

    private static function denyReason(BaseConnection $db, array $card, ?array $w, ?int $laneId, string $now): ?string
    {
        if (($card['status'] ?? '') !== 'active') {
            return 'TWP card is inactive.';
        }
        if (! $w) {
            return 'TWP card is not issued to any worker.';
        }
        if (($w['request_status'] ?? '') !== 'Approved' || ($w['status'] ?? '') !== 'Approved') {
            return 'work pass is ' . ($w['request_status'] !== 'Approved' ? $w['request_status'] : $w['status']) . '.';
        }
        if (! empty($w['date_from']) && $now < $w['date_from']) {
            return 'pass is valid from ' . date('d/m/Y H:i', strtotime($w['date_from'])) . '.';
        }
        if (! empty($w['date_to']) && $now > $w['date_to']) {
            return 'pass expired on ' . date('d/m/Y H:i', strtotime($w['date_to'])) . '.';
        }
        if (self::blacklisted($db, $w['ic_no'] ?? null, $w['passport_no'] ?? null)) {
            return 'worker is blacklisted.';
        }
        if ($laneId) {
            $allowed = array_values(array_filter(array_map('trim', explode(',', (string) ($w['location_access'] ?? ''))), 'strlen'));
            if ($allowed) {
                $gate = trim((string) ($db->query('SELECT loc.location_access FROM lanes l LEFT JOIN locations loc ON loc.id = l.location_id WHERE l.id = ?', [$laneId])->getRowArray()['location_access'] ?? ''));
                if ($gate !== '' && ! in_array(strtolower($gate), array_map('strtolower', $allowed), true)) {
                    return 'no location access to ' . $gate . '.';
                }
            }
        }

        return null;
    }

    private static function log(BaseConnection $db, ?int $workerId, int $cardId, string $action, ?int $laneId, ?string $msg, string $at): void
    {
        $db->table('twp_card_logs')->insert(['worker_id' => $workerId, 'card_id' => $cardId, 'action' => $action, 'lane_id' => $laneId,
            'message' => $msg !== null ? mb_substr($msg, 0, 255) : null, 'scanned_at' => $at]);
    }

    private static function body(bool $ok, string $action, string $message, string $epc, string $name): array
    {
        return ['success' => $ok, 'access_granted' => $ok, 'action' => $action, 'holder_type' => 'twp', 'message' => $message, 'worker_name' => $name, 'card_epc' => $epc];
    }
}
