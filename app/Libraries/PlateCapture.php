<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Plate capture at the gate (KPK AlprService + insertAlprPhoto + saveNoVehicleStickerReport).
 *
 * After every successful tap the lane's car plate camera (lanes.cam_id_1) is asked for its last read
 * (ALPR_URL/last?c=<cam>&limit=1, the KPK ALPR integration). The plate is ALWAYS stored against the
 * gate transaction, also when the vehicle is not registered or its type only needs the pass tapped,
 * so Monitoring shows one of three categories: registered, tap_only, unregistered.
 *
 * Set ALPR_URL in .env (KPK pymsoft.alpr.url, e.g. http://10.10.201.3:8080/alpr/integration).
 * Without it nothing is captured and the gate works as before.
 */
class PlateCapture
{
    public const LABELS = ['registered' => 'Registered', 'tap_only' => 'Registered (Tap Only)', 'unregistered' => 'Unregistered'];

    /** How old a camera read may be to belong to this tap. */
    private const MAX_AGE = 120;

    public static function enabled(): bool
    {
        return trim((string) env('ALPR_URL', '')) !== '';
    }

    /** Called by the gate controllers after a granted check-in / check-out. Never throws. */
    public static function afterTap(BaseConnection $db, string $epc, ?int $laneId, string $action, string $holderType = ''): ?array
    {
        try {
            if (! $laneId || ! self::enabled() || ! in_array($action, ['checkin', 'checkout'], true) || ! $db->tableExists('gate_plate_captures')) {
                return null;
            }
            $lane = $db->query('SELECT l.id, l.cam_id_1, loc.client_id FROM lanes l LEFT JOIN locations loc ON loc.id = l.location_id WHERE l.id = ?', [$laneId])->getRowArray();
            $cam  = trim((string) ($lane['cam_id_1'] ?? ''));
            if ($cam === '') {
                return null;
            }
            $read = self::lastRead($cam);
            if (! $read || trim((string) ($read['refNo'] ?? '')) === '') {
                return null;
            }

            return self::record($db, [
                'company_id' => $lane['client_id'] ?? null, 'lane_id' => $laneId, 'card_epc' => $epc, 'holder_type' => $holderType,
                'direction' => $action === 'checkin' ? 'in' : 'out', 'plate_no' => $read['refNo'], 'camera' => $read['camera'] ?? $cam,
                'alpr_ref' => $read['id'] ?? null, 'image_path' => $read['path'] ?? null, 'captured_at' => self::parseDate($read['dateTime'] ?? null),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'PlateCapture::afterTap ' . $e->getMessage());

            return null;
        }
    }

    /** Store one plate read: works out the category and the transaction it belongs to. */
    public static function record(BaseConnection $db, array $d): array
    {
        $now   = date('Y-m-d H:i:s');
        $plate = strtoupper(preg_replace('/\s+/', ' ', trim((string) $d['plate_no'])));
        $veh   = self::vehicle($db, $plate);

        $row = [
            'company_id'   => $d['company_id'] ?? null,
            'lane_id'      => $d['lane_id'] ?? null,
            'tx_key'       => $d['tx_key'] ?? self::txKey($db, (string) ($d['card_epc'] ?? ''), (string) ($d['direction'] ?? 'in')),
            'holder_type'  => $d['holder_type'] ?? null,
            'card_epc'     => $d['card_epc'] ?? null,
            'direction'    => $d['direction'] ?? 'in',
            'plate_no'     => mb_substr($plate, 0, 40),
            'category'     => ! $veh ? 'unregistered' : ((int) ($veh['capture_plate'] ?? 1) === 0 ? 'tap_only' : 'registered'),
            'vehicle_id'   => $veh['id'] ?? null,
            'vehicle_type' => $veh['type_name'] ?? null,
            'camera'       => $d['camera'] ?? null,
            'alpr_ref'     => $d['alpr_ref'] ?? null,
            'image_path'   => $d['image_path'] ?? null,
            'captured_at'  => $d['captured_at'] ?? $now,
            'created_at'   => $now,
        ];
        if (! empty($row['alpr_ref']) && self::enabled() && ($img = self::saveImage((string) $row['alpr_ref'])) !== null) {
            $row['image_path'] = $img;
        }
        $db->table('gate_plate_captures')->insert($row);
        $row['id'] = (int) $db->insertID();

        return $row;
    }

    /** Registered vehicle with this plate (spaces ignored), with its type's capture_plate setting. */
    private static function vehicle(BaseConnection $db, string $plate): ?array
    {
        if ($plate === '' || ! $db->tableExists('vehicles')) {
            return null;
        }
        $typeJoin = $db->fieldExists('vehicle_type_id', 'vehicles') && $db->tableExists('vehicle_types');
        $sql      = 'SELECT v.id' . ($typeJoin ? ', vt.name AS type_name, vt.capture_plate' : '') . ' FROM vehicles v'
            . ($typeJoin ? ' LEFT JOIN vehicle_types vt ON vt.id = v.vehicle_type_id' : '')
            . " WHERE UPPER(REPLACE(v.plate_no, ' ', '')) = ? ORDER BY (v.status = 'Issued') DESC, v.id DESC LIMIT 1";

        return $db->query($sql, [str_replace(' ', '', $plate)])->getRowArray() ?: null;
    }

    /** Monitoring key (staff-v-1, vendor-v-2, visitor-v-3, twp-v-4) of the visit this card just opened or closed. */
    private static function txKey(BaseConnection $db, string $epc, string $direction): ?string
    {
        if ($epc === '') {
            return null;
        }
        $col   = $direction === 'out' ? 'check_out_time' : 'check_in_time';
        $since = date('Y-m-d H:i:s', time() - self::MAX_AGE);
        $parts = [];
        $vc    = $db->table('visitor_cards')->select('id')->where('card_id', $epc)->get()->getResultArray();
        if ($ids = implode(',', array_map('intval', array_column($vc, 'id')))) {
            foreach ([['staff', 'staff_visits', 'card_id'], ['vendor', 'vendor_visits', 'vendor_card_id'], ['visitor', 'invitation_visitors', 'visitor_card_id']] as [$k, $t, $c]) {
                if ($db->tableExists($t) && $db->fieldExists($c, $t)) {
                    $parts[] = "SELECT '{$k}' AS k, id, {$col} AS at FROM {$t} WHERE {$c} IN ({$ids}) AND {$col} >= '{$since}'";
                }
            }
        }
        if ($db->tableExists('twp_cards')) {
            $parts[] = "SELECT 'twp' AS k, v.id, v.{$col} AS at FROM twp_visits v JOIN twp_cards c ON c.id = v.card_id WHERE c.card_id = " . $db->escape($epc) . " AND v.{$col} >= '{$since}'";
        }
        if (! $parts) {
            return null;
        }
        $hit = $db->query('SELECT * FROM (' . implode(' UNION ALL ', $parts) . ') x ORDER BY at DESC, id DESC LIMIT 1')->getRowArray();

        return $hit ? $hit['k'] . '-v-' . $hit['id'] : null;
    }

    /** KPK getResultAlpr: the camera's latest read, or null when the ALPR server does not answer. */
    private static function lastRead(string $cam): ?array
    {
        $res  = self::get('/last?c=' . rawurlencode($cam) . '&limit=1');
        $read = $res['result'][0] ?? null;
        if (! is_array($read)) {
            return null;
        }
        $at = self::parseDate($read['dateTime'] ?? null);

        return $at && strtotime($at) < time() - self::MAX_AGE ? null : $read;
    }

    /** KPK getImageResultAlpr: download the plate photo into public/uploads/plate_captures. */
    private static function saveImage(string $refId): ?string
    {
        $res = self::get('/image?id=' . rawurlencode($refId));
        $raw = is_string($res['result'] ?? null) ? base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $res['result']), true) : false;
        if (! $raw) {
            return null;
        }
        $dir = 'uploads/plate_captures/' . date('Ym');
        if (! is_dir(FCPATH . $dir)) {
            mkdir(FCPATH . $dir, 0775, true);
        }
        $file = $dir . '/' . preg_replace('/[^A-Za-z0-9_-]/', '', $refId) . '.jpg';

        return file_put_contents(FCPATH . $file, $raw) !== false ? $file : null;
    }

    private static function get(string $path): ?array
    {
        $ctx  = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 3, 'ignore_errors' => true]]);
        $body = @file_get_contents(rtrim((string) env('ALPR_URL'), '/') . $path, false, $ctx);

        return $body ? (json_decode($body, true) ?: null) : null;
    }

    /** ALPR dates are dd/MM/yyyy HH:mm:ss. */
    private static function parseDate(?string $s): ?string
    {
        $d = $s ? \DateTime::createFromFormat('d/m/Y H:i:s', $s) : false;

        return $d ? $d->format('Y-m-d H:i:s') : null;
    }
}
