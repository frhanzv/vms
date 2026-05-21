<?php

namespace App\Controllers;

use App\Models\VisitorCardModel;
use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;
use App\Services\NotificationService;
use App\Libraries\InvitationEmailSender;
use CodeIgniter\RESTful\ResourceController;

class QRCode extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
    }

    /**
     * Handle card scan without lane (single reader / USB HID).
     * Card-based check-in requires a door/lane, so this endpoint validates
     * the card but denies check-in without a lane.
     *
     * GET /api/qr/scan?qr_code=CARD123
     */
    public function scan()
    {
        $raw = trim((string) ($this->request->getGet('qr_code') ?? $this->request->getGet('card_number') ?? ''));

        if ($raw === '') {
            return $this->failValidationError('Card number is required');
        }

        // Reject non-card QR types without triggering security alerts
        if (stripos($raw, 'VIS-') === 0 || stripos($raw, 'http') === 0) {
            return $this->respond([
                'success'        => false,
                'access_granted' => false,
                'action'         => 'denied',
                'message'        => 'Wrong QR type: only physical visitor card EPCs are accepted at this scanner.',
                'qr_code'        => $raw,
            ]);
        }

        $db   = \Config\Database::connect();
        $card = $db->table('visitor_cards')->where('card_id', $raw)->get()->getRowArray();

        if (!$card) {
            $this->insertSecurityAlert(
                'Unauthorized Access',
                'high',
                'Unknown',
                'Security',
                'Unregistered card scanned. Card EPC: ' . $raw
            );
            return $this->respond([
                'success'        => false,
                'access_granted' => false,
                'action'         => 'denied',
                'message'        => 'Card not found in inventory. Only physical visitor cards issued by this system are accepted.',
                'qr_code'        => $raw,
            ]);
        }

        if ($card['status'] !== 'active' && $card['status'] !== 'in_use') {
            return $this->respond([
                'success' => false,
                'message' => 'Card is ' . $card['status'] . ' and cannot be used',
                'qr_code' => $raw,
            ]);
        }

        return $this->respond([
            'success'        => false,
            'access_granted' => false,
            'action'         => 'denied',
            'message'        => 'Access denied: No door/lane selected. Please select a door for check-in.',
            'qr_code'        => $raw,
        ]);
    }

    /**
     * Handle card scan with lane information (multi-reader).
     * GET /api/qr/scan-lane?qr_code=CARD123&lane_id=1&lane_type=entry
     */
    public function scanLane()
    {
        $raw           = trim((string) ($this->request->getGet('qr_code') ?? $this->request->getGet('card_number') ?? ''));
        $laneId        = $this->request->getGet('lane_id');
        $subLocationId = $this->request->getGet('sub_location_id');
        $laneType      = $this->request->getGet('lane_type');

        if ($raw === '') {
            return $this->failValidationError('Card number is required');
        }

        return $this->processCardCheckInOut($raw, $laneId, $laneType, $subLocationId);
    }

    /**
     * Get scan reader status / active configuration.
     * GET /api/qr/status
     */
    public function status()
    {
        $db       = \Config\Database::connect();
        $setting  = $db->table('settings')->where('setting_key', 'scan_type')->get()->getRowArray();
        $scanType = $setting['setting_value'] ?? 'rfid';

        return $this->respond([
            'success'   => true,
            'scan_type' => $scanType,
            'reader'    => [
                'type'   => 'card_scanner',
                'mode'   => 'usb_hid',
                'status' => 'configured',
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Core check-in / check-out logic using card number.
     * Only card_id from visitor_cards is accepted — IC/passport not allowed.
     *
     * Validation order:
     *   1. Card must exist in visitor_cards
     *   2. Card status must be active or in_use
     *   3. Card must be assigned to a visitor (invitation_visitors.visitor_card_id)
     *   4. Visitor must have door access (pathway configured for visitor type)
     *   5. Standard checkin / checkout / door_access state machine
     */
    protected function processCardCheckInOut(string $cardNumber, ?string $laneId, ?string $laneType, ?string $subLocationId = null)
    {
        $db = \Config\Database::connect();

        // Reject non-card QR types immediately without triggering security alerts.
        // Visitor invitation QRs encode "VIS-{id}" or an IC/passport number;
        // registration QRs encode a URL. None of these are visitor card EPCs.
        if (stripos($cardNumber, 'VIS-') === 0) {
            return $this->respond([
                'success'        => false,
                'access_granted' => false,
                'action'         => 'denied',
                'message'        => 'Wrong QR type: visitor invitation QR cannot be used here. Scan the physical visitor card only.',
                'qr_code'        => $cardNumber,
            ]);
        }

        if (stripos($cardNumber, 'http://') === 0 || stripos($cardNumber, 'https://') === 0) {
            return $this->respond([
                'success'        => false,
                'access_granted' => false,
                'action'         => 'denied',
                'message'        => 'Wrong QR type: URL QR codes are not accepted. Scan the physical visitor card only.',
                'qr_code'        => $cardNumber,
            ]);
        }

        // 1. Look up card by card_id (EPC) in visitor card inventory
        $card = $db->table('visitor_cards')->where('card_id', $cardNumber)->get()->getRowArray();

        if (!$card) {
            $laneName = $this->getLaneName($laneId, $subLocationId);
            $this->insertSecurityAlert(
                'Unauthorized Access',
                'high',
                'Unknown',
                $laneName ?: 'Security',
                'Unregistered card scanned at door. Card EPC: ' . $cardNumber
            );
            return $this->respond([
                'success'        => false,
                'access_granted' => false,
                'action'         => 'denied',
                'message'        => 'Card not found in inventory. Only physical visitor cards issued by this system are accepted.',
                'qr_code'        => $cardNumber,
            ]);
        }

        // 2. Card must be active or in_use
        if ($card['status'] !== 'active' && $card['status'] !== 'in_use') {
            return $this->respond([
                'success' => false,
                'message' => 'Card is ' . $card['status'] . ' and cannot be used for check-in',
                'qr_code' => $cardNumber,
            ]);
        }

        $db->transStart();

        try {
            // 3. Card must be explicitly assigned to an approved visitor who hasn't checked out.
            //    Join on vc.card_id so duplicate card_id rows in visitor_cards don't cause a miss.
            //    Schedule date range is intentionally NOT checked here: the admin's explicit card
            //    assignment is the authorization — restricting by schedule date would deny access
            //    to visitors whose schedule started yesterday or spans multiple days.
            $invitation = $db->query(
                'SELECT iv.*, iv.id as iv_id,
                        i.full_name as visitor_name, i.company as visitor_company,
                        i.id as invitation_id, i.ic_passport, i.visitor_type_id,
                        u.company_id, vt.name as visitor_type_name
                 FROM invitation_visitors iv
                 JOIN visitor_cards vc ON vc.id = iv.visitor_card_id
                 JOIN invitations i ON i.id = iv.invitation_id
                 LEFT JOIN users u ON i.staff_id = u.staff_id
                 LEFT JOIN visitor_types vt ON vt.id = i.visitor_type_id
                 WHERE vc.card_id = ?
                 AND i.status = ?
                 AND iv.check_out_time IS NULL
                 FOR UPDATE',
                [$cardNumber, 'Approved']
            )->getRowArray();

            if (!$invitation) {
                $db->transRollback();
                $laneName = $this->getLaneName($laneId, $subLocationId);
                $this->insertSecurityAlert(
                    'Unauthorized Access',
                    'high',
                    'Unknown',
                    $laneName ?: 'Security',
                    'Card is not assigned to any visitor. Card number: ' . $cardNumber
                );
                return $this->respond([
                    'success'        => false,
                    'access_granted' => false,
                    'action'         => 'denied',
                    'message'        => 'Access denied: Card is not assigned to any visitor.',
                    'qr_code'        => $cardNumber,
                ]);
            }

            // Use the card row that is actually linked to this visitor (resolves duplicate card_id)
            $card['id'] = $invitation['visitor_card_id'];

            $laneName    = $this->getLaneName($laneId, $subLocationId);
            $doorId      = $subLocationId ?? $laneId;
            $isTurnstileEarly = ($doorId && stripos($laneName ?? '', 'TURNSTILE') !== false);

            // 4. Pathway/door-access check — only for internal doors.
            //    Turnstile is the universal entry/exit gate; every approved visitor may use it.
            if ($doorId && !$isTurnstileEarly) {
                $accessCheck = $this->checkVisitorTypeAccess($invitation, (int) $doorId, $subLocationId !== null);
                if (!$accessCheck['granted']) {
                    $db->transRollback();
                    $this->insertSecurityAlert(
                        'Unauthorized Access',
                        'high',
                        $invitation['visitor_name'] ?? 'Unknown',
                        $laneName ?: 'Security',
                        $accessCheck['message'] . ' Card number: ' . $cardNumber
                    );
                    return $this->respond([
                        'success'        => false,
                        'access_granted' => false,
                        'action'         => 'denied',
                        'message'        => $accessCheck['message'],
                        'visitor'        => [
                            'name'         => $invitation['visitor_name']      ?? 'Unknown',
                            'company'      => $invitation['visitor_company']   ?? 'N/A',
                            'visitor_type' => $invitation['visitor_type_name'] ?? null,
                        ],
                        'qr_code'        => $cardNumber,
                    ]);
                }
            }

            // 5. Turnstile-first state machine:
            //    Turnstile = entry/exit gate (must be first in, last out).
            //    All other doors = internal doors, only reachable after turnstile check-in.
            $action      = 'checkin';
            $duration    = null;
            $now         = date('Y-m-d H:i:s');
            $nextVer     = ((int) ($invitation['version'] ?? 1)) + 1;
            $isTurnstile = $isTurnstileEarly;

            if ($invitation['check_out_time']) {
                $db->transRollback();
                return $this->respond([
                    'success' => false,
                    'message' => 'Visitor has already checked out for today',
                    'qr_code' => $cardNumber,
                ]);
            }

            if ($isTurnstile) {
                // "Effectively inside" means the visitor entered TODAY — either via the turnstile
                // (check_in_time set today) or via internal doors when the turnstile was bypassed.
                // All checks are scoped to TODAY so that stale data from previous sessions
                // (old check_in_time or old door logs) never incorrectly trigger a checkout.
                $today = date('Y-m-d');

                $checkinToday = !empty($invitation['check_in_time'])
                    && date('Y-m-d', strtotime((string) $invitation['check_in_time'])) === $today;

                // Determine session-start anchor so old-session door logs don't bleed into
                // the current session. Prefer the most recent turnstile checkout log (clean
                // boundary). Fall back to invitation_visitors.updated_at (set when the card is
                // assigned / check_in_time written) for sessions that were closed by admin before
                // the checkout-log write was introduced — those have no checkout log at all.
                $lastCheckoutTs = $db->query(
                    "SELECT MAX(scanned_at) as ts FROM visitor_card_logs
                     WHERE invitation_id = ? AND action = 'checkout'",
                    [$invitation['invitation_id']]
                )->getRowArray()['ts'] ?? null;

                $sessionStart = $lastCheckoutTs
                    ?? ($invitation['updated_at'] ?? $invitation['created_at'] ?? '1970-01-01 00:00:00');

                $hasDoorActivityToday = (bool) $db->query(
                    "SELECT id FROM visitor_card_logs
                     WHERE invitation_id = ?
                       AND action IN ('door_checkin', 'door_checkout')
                       AND DATE(scanned_at) = ?
                       AND scanned_at > ?
                     LIMIT 1",
                    [$invitation['invitation_id'], $today, $sessionStart]
                )->getRowArray();

                $isEffectivelyInside = $checkinToday || $hasDoorActivityToday;

                // Respect Entry/Exit/Auto mode for turnstile.
                if ($laneType === 'entry' && $isEffectivelyInside) {
                    $db->transRollback();
                    return $this->respond([
                        'success'        => false,
                        'access_granted' => false,
                        'action'         => 'denied',
                        'message'        => 'Already checked in. Use the exit scanner to check out.',
                        'qr_code'        => $cardNumber,
                    ]);
                }
                if ($laneType === 'exit' && !$isEffectivelyInside) {
                    $db->transRollback();
                    return $this->respond([
                        'success'        => false,
                        'access_granted' => false,
                        'action'         => 'denied',
                        'message'        => 'Not checked in. Please check in at the entry turnstile first.',
                        'qr_code'        => $cardNumber,
                    ]);
                }

                if (!$isEffectivelyInside) {
                    // Not inside at all → entry check-in
                    $action = 'checkin';
                    $db->table('invitation_visitors')
                        ->where('id', $invitation['iv_id'])
                        ->where('check_in_time IS NULL')
                        ->update(['check_in_time' => $now, 'version' => $nextVer, 'updated_at' => $now]);

                    if ($db->affectedRows() === 0) {
                        $db->transRollback();
                        return $this->respond([
                            'success' => false,
                            'message' => 'Visitor has already been checked in',
                            'qr_code' => $cardNumber,
                        ]);
                    }

                    $db->table('visitor_cards')->where('id', $card['id'])->update(['status' => 'in_use']);
                } else {
                    // Inside (via turnstile or door bypass) → exit check-out.
                    // Block if the visitor still has an open internal door session,
                    // but only when Internal Door Checkout is ON.
                    $doorCheckoutSetting = $db->table('settings')
                        ->where('setting_key', 'door_checkout_required')
                        ->get()->getRowArray();
                    $doorCheckoutRequired = $doorCheckoutSetting
                        ? ($doorCheckoutSetting['setting_value'] ?? '1') : '1';

                    if ($doorCheckoutRequired !== '0') {
                        $openDoorLog = $db->query(
                            "SELECT action, lane_id, sub_location_id
                             FROM visitor_card_logs
                             WHERE invitation_id = ?
                               AND action IN ('door_checkin', 'door_checkout')
                             ORDER BY scanned_at DESC, id DESC
                             LIMIT 1",
                            [$invitation['invitation_id']]
                        )->getRowArray();

                        if ($openDoorLog && $openDoorLog['action'] === 'door_checkin') {
                            $openDoorName = $this->getLaneName($openDoorLog['lane_id'], $openDoorLog['sub_location_id']);
                            $db->transRollback();
                            return $this->respond([
                                'success'        => false,
                                'access_granted' => false,
                                'action'         => 'denied',
                                'message'        => 'Please check out from ' . ($openDoorName ?? 'the internal door') . ' before leaving.',
                                'visitor'        => [
                                    'name'    => $invitation['visitor_name']    ?? 'Unknown',
                                    'company' => $invitation['visitor_company'] ?? 'N/A',
                                ],
                                'qr_code'        => $cardNumber,
                            ]);
                        }
                    }

                    $action = 'checkout';
                    // If check_in_time was never set (turnstile was bypassed), stamp it now
                    // so the row is consistent and duration can be derived from door logs instead.
                    $checkoutUpdate = ['check_out_time' => $now, 'version' => $nextVer, 'updated_at' => $now];
                    if (empty($invitation['check_in_time'])) {
                        $checkoutUpdate['check_in_time'] = $now;
                        $duration = '0 minutes';
                    } else {
                        $duration = $this->formatDuration(time() - strtotime($invitation['check_in_time']));
                    }

                    $db->table('invitation_visitors')
                        ->where('id', $invitation['iv_id'])
                        ->where('check_out_time IS NULL')
                        ->update($checkoutUpdate);

                    // visitor_card_id intentionally kept — used as reuse history.
                    $db->table('visitor_cards')->where('id', $card['id'])->update(['status' => 'active']);
                }
            } else {
                // Internal door — check if turnstile-first is required (configurable via Scanner Management).
                // When disabled (e.g. turnstile broken), visitors can access internal doors directly.
                $turnstileRequiredSetting = $db->table('settings')
                    ->where('setting_key', 'turnstile_required')
                    ->get()->getRowArray();
                $turnstileRequired = $turnstileRequiredSetting ? ($turnstileRequiredSetting['setting_value'] ?? '1') : '1';

                if ($turnstileRequired !== '0') {
                    // Find a turnstile 'checkin' log for today that has NOT been followed by a
                    // 'checkout' log. This correctly scopes the check to the current session:
                    // a checkin that was already paired with a checkout (visitor left and came back)
                    // does NOT count — the visitor must scan the turnstile again for the new session.
                    // RFID also writes action='checkin' at any door, so we join location tables to
                    // confirm the log was specifically at a TURNSTILE-named location.
                    $today = date('Y-m-d');
                    $turnstileLog = $db->query(
                        "SELECT vcl.id
                         FROM visitor_card_logs vcl
                         LEFT JOIN sub_locations sl ON sl.id = vcl.sub_location_id
                         LEFT JOIN lanes la ON la.id = vcl.lane_id
                         WHERE vcl.invitation_id = ?
                           AND vcl.action = 'checkin'
                           AND DATE(vcl.scanned_at) = ?
                           AND (
                               (vcl.sub_location_id IS NOT NULL AND sl.name LIKE '%TURNSTILE%')
                               OR (vcl.sub_location_id IS NULL AND vcl.lane_id IS NOT NULL AND la.lane LIKE '%TURNSTILE%')
                           )
                           AND NOT EXISTS (
                               SELECT 1 FROM visitor_card_logs vcl2
                               WHERE vcl2.invitation_id = ?
                                 AND vcl2.action = 'checkout'
                                 AND vcl2.scanned_at > vcl.scanned_at
                           )
                         LIMIT 1",
                        [$invitation['invitation_id'], $today, $invitation['invitation_id']]
                    )->getRowArray();

                    if (!$turnstileLog) {
                        $db->transRollback();
                        return $this->respond([
                            'success'        => false,
                            'access_granted' => false,
                            'action'         => 'denied',
                            'message'        => 'Access denied: Please check in at the entry turnstile first.',
                            'qr_code'        => $cardNumber,
                        ]);
                    }
                }

                // Per-door state: find the visitor's most recent door action
                $lastDoorLog = $db->query(
                    'SELECT action, sub_location_id, lane_id
                     FROM visitor_card_logs
                     WHERE invitation_id = ?
                     AND action IN (\'door_checkin\', \'door_checkout\')
                     ORDER BY scanned_at DESC, id DESC
                     LIMIT 1',
                    [$invitation['invitation_id']]
                )->getRowArray();

                $currentDoorId  = $subLocationId ?? $laneId;
                $hasOpenDoor    = $lastDoorLog && $lastDoorLog['action'] === 'door_checkin';
                $openDoorId     = $hasOpenDoor ? ($lastDoorLog['sub_location_id'] ?? $lastDoorLog['lane_id']) : null;
                $openAtThisDoor = $hasOpenDoor && ((string) $openDoorId === (string) $currentDoorId);

                // Read door-checkout enforcement setting.
                // ON  = visitor must check out from current door before entering another (default).
                // OFF = visitor may freely check in to any door without checking out first.
                $doorCheckoutSetting = $db->table('settings')
                    ->where('setting_key', 'door_checkout_required')
                    ->get()->getRowArray();
                $doorCheckoutRequired = $doorCheckoutSetting ? ($doorCheckoutSetting['setting_value'] ?? '1') : '1';

                if ($laneType === 'entry') {
                    // Entry scanner: check-in only
                    if ($openAtThisDoor) {
                        $db->transRollback();
                        return $this->respond([
                            'success'        => false,
                            'access_granted' => false,
                            'action'         => 'denied',
                            'message'        => 'Already checked in here. Use the exit scanner to check out.',
                            'qr_code'        => $cardNumber,
                        ]);
                    }
                    if ($hasOpenDoor && !$openAtThisDoor && $doorCheckoutRequired !== '0') {
                        $openDoorName = $this->getLaneName($lastDoorLog['lane_id'], $lastDoorLog['sub_location_id']);
                        $db->transRollback();
                        return $this->respond([
                            'success'        => false,
                            'access_granted' => false,
                            'action'         => 'denied',
                            'message'        => 'Access denied: Please check out from ' . ($openDoorName ?? 'current door') . ' before entering another.',
                            'visitor'        => [
                                'name'         => $invitation['visitor_name']      ?? 'Unknown',
                                'company'      => $invitation['visitor_company']   ?? 'N/A',
                                'visitor_type' => $invitation['visitor_type_name'] ?? null,
                            ],
                            'qr_code'        => $cardNumber,
                        ]);
                    }
                    $action = 'door_checkin';

                } elseif ($laneType === 'exit') {
                    // Exit scanner: check-out only
                    if (!$hasOpenDoor) {
                        $db->transRollback();
                        return $this->respond([
                            'success'        => false,
                            'access_granted' => false,
                            'action'         => 'denied',
                            'message'        => 'Not checked in to any door.',
                            'qr_code'        => $cardNumber,
                        ]);
                    }
                    if (!$openAtThisDoor && $doorCheckoutRequired !== '0') {
                        $openDoorName = $this->getLaneName($lastDoorLog['lane_id'], $lastDoorLog['sub_location_id']);
                        $db->transRollback();
                        return $this->respond([
                            'success'        => false,
                            'access_granted' => false,
                            'action'         => 'denied',
                            'message'        => 'Not checked in at this door. Currently in ' . ($openDoorName ?? 'another door') . '.',
                            'visitor'        => [
                                'name'         => $invitation['visitor_name']      ?? 'Unknown',
                                'company'      => $invitation['visitor_company']   ?? 'N/A',
                                'visitor_type' => $invitation['visitor_type_name'] ?? null,
                            ],
                            'qr_code'        => $cardNumber,
                        ]);
                    }
                    $action = 'door_checkout';

                } else {
                    // Auto mode: first scan = check-in, second scan at same door = check-out
                    if ($openAtThisDoor) {
                        $action = 'door_checkout';
                    } elseif ($hasOpenDoor && $doorCheckoutRequired !== '0') {
                        $openDoorName = $this->getLaneName($lastDoorLog['lane_id'], $lastDoorLog['sub_location_id']);
                        $db->transRollback();
                        return $this->respond([
                            'success'        => false,
                            'access_granted' => false,
                            'action'         => 'denied',
                            'message'        => 'Access denied: Please check out from ' . ($openDoorName ?? 'current door') . ' before entering another.',
                            'visitor'        => [
                                'name'         => $invitation['visitor_name']      ?? 'Unknown',
                                'company'      => $invitation['visitor_company']   ?? 'N/A',
                                'visitor_type' => $invitation['visitor_type_name'] ?? null,
                            ],
                            'qr_code'        => $cardNumber,
                        ]);
                    } else {
                        $action = 'door_checkin';
                    }
                }
            }

            $this->logCardScan($card['id'], $invitation['invitation_id'], $action, $subLocationId !== null ? null : $laneId, $subLocationId);

            $db->transComplete();

            if ($action === 'checkin' || $action === 'checkout') {
                $notifType = $action === 'checkin' ? 'check_in' : 'check_out';
                (new NotificationService())->dispatch($invitation['invitation_id'], $notifType);
            }

            if ($action === 'checkin' && !empty($invitation['ic_passport'])) {
                $blacklisted = $db->table('blacklist')
                    ->whereIn('status', ['active', 'pending'])
                    ->where('ic_passport_no', $invitation['ic_passport'])
                    ->get()->getRowArray();

                if ($blacklisted) {
                    (new InvitationEmailSender())->sendBlacklistFlagged(
                        (int) ($invitation['company_id'] ?? 0),
                        $invitation['visitor_name'] ?? '',
                        $invitation['ic_passport'],
                        $blacklisted['reason'] ?? ''
                    );
                }
            }

            $response = [
                'success'        => true,
                'access_granted' => true,
                'action'         => $action,
                'visitor'        => [
                    'name'         => $invitation['visitor_name']      ?? 'Unknown',
                    'company'      => $invitation['visitor_company']   ?? 'N/A',
                    'visitor_type' => $invitation['visitor_type_name'] ?? null,
                ],
                'time'     => $now,
                'duration' => $duration,
                'qr_code'  => $cardNumber,
            ];

            if ($subLocationId) {
                $response['lane'] = ['id' => $subLocationId, 'name' => $laneName, 'type' => $laneType, 'source' => 'sub_location'];
            } elseif ($laneId) {
                $response['lane'] = ['id' => $laneId, 'name' => $laneName, 'type' => $laneType, 'source' => 'lane'];
            }

            return $this->respond($response);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Card scan error: ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Server error during card scan',
                'qr_code' => $cardNumber,
            ], 500);
        }
    }

    /**
     * Log card scan to visitor_card_logs.
     */
    protected function logCardScan(int $cardId, int $invitationId, string $action, ?string $laneId = null, ?string $subLocationId = null): void
    {
        $db = \Config\Database::connect();
        $db->table('visitor_card_logs')->insert([
            'visitor_card_id' => $cardId,
            'invitation_id'   => $invitationId,
            'action'          => $action,
            'lane_id'         => $laneId,
            'sub_location_id' => $subLocationId,
            'scan_source'     => 'qr_code',
            'scanned_at'      => date('Y-m-d H:i:s'),
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Check if the given lane is permitted for the visitor's type via pathway config.
     *
     * Denies access when:
     *   - No visitor type assigned (cannot determine door access)
     *   - Visitor type has no pathway configured (N/A door access)
     *   - Pathway is inactive
     *   - Lane is not in the assigned pathway
     */
    protected function checkVisitorTypeAccess(array $invitation, int $doorId, bool $isSubLocation = false): array
    {
        $db = \Config\Database::connect();

        if (empty($invitation['visitor_type_id'])) {
            return [
                'granted' => false,
                'message' => 'Access denied: No visitor type assigned — door access cannot be determined.',
            ];
        }

        $visitorType = $db->table('visitor_types')
            ->where('id', $invitation['visitor_type_id'])
            ->get()->getRowArray();

        if (!$visitorType || empty($visitorType['path'])) {
            return [
                'granted' => false,
                'message' => 'Access denied: No door access configured for this visitor type (N/A).',
            ];
        }

        $pathway = $db->table('pathways')
            ->where('name', $visitorType['path'])
            ->where('status', 'active')
            ->get()->getRowArray();

        if (!$pathway) {
            return [
                'granted' => false,
                'message' => 'Access denied: Assigned pathway is inactive or no longer exists.',
            ];
        }

        if ($isSubLocation) {
            $inPathway = $db->table('pathway_sub_locations')
                ->where('pathway_id', $pathway['id'])
                ->where('sub_location_id', $doorId)
                ->countAllResults() > 0;
        } else {
            $inPathway = $db->table('pathway_lanes')
                ->where('pathway_id', $pathway['id'])
                ->where('lane_id', $doorId)
                ->countAllResults() > 0;
        }

        if (!$inPathway) {
            $typeName = $visitorType['name'] ?? 'Unknown';
            return [
                'granted' => false,
                'message' => "Access denied: visitor type \"{$typeName}\" is not authorised for this door.",
            ];
        }

        return ['granted' => true, 'message' => null];
    }

    /**
     * Resolve door name from lane ID or sub_location ID.
     */
    protected function getLaneName(?string $laneId, ?string $subLocationId = null): ?string
    {
        $db = \Config\Database::connect();

        if ($subLocationId) {
            $row = $db->table('sub_locations')->where('id', $subLocationId)->get()->getRowArray();
            return $row['name'] ?? null;
        }

        if (!$laneId) {
            return null;
        }
        $row = $db->table('lanes')->where('id', $laneId)->get()->getRowArray();
        return $row['lane'] ?? null;
    }

    /**
     * Insert a row into security_alerts.
     */
    protected function insertSecurityAlert(string $type, string $severity, string $visitorName, string $location, string $description): void
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('security_alerts')) {
            $db->table('security_alerts')->insert([
                'incident_type'   => $type,
                'severity'        => $severity,
                'visitor_name'    => $visitorName,
                'location'        => $location,
                'description'     => $description,
                'is_acknowledged' => 0,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Format duration seconds into a human-readable string.
     */
    protected function formatDuration(int $seconds): string
    {
        $hours   = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%d hour%s %d minute%s', $hours, $hours > 1 ? 's' : '', $minutes, $minutes !== 1 ? 's' : '');
        }

        return sprintf('%d minute%s', $minutes, $minutes !== 1 ? 's' : '');
    }
}
