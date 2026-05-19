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
            // 3. Card must be assigned to a visitor with an approved invitation scheduled for today
            $invitation = $db->query(
                'SELECT iv.*, iv.id as iv_id,
                        i.full_name as visitor_name, i.company as visitor_company,
                        i.id as invitation_id, i.ic_passport, i.visitor_type_id,
                        u.company_id, vt.name as visitor_type_name
                 FROM invitation_visitors iv
                 JOIN invitations i ON i.id = iv.invitation_id
                 JOIN invitation_schedules isc ON isc.invitation_id = i.id
                 LEFT JOIN users u ON i.staff_id = u.staff_id
                 LEFT JOIN visitor_types vt ON vt.id = i.visitor_type_id
                 WHERE iv.visitor_card_id = ?
                 AND i.status = ?
                 AND DATE(isc.date_from) <= ?
                 AND DATE(isc.date_to) >= ?
                 FOR UPDATE',
                [$card['id'], 'Approved', date('Y-m-d'), date('Y-m-d')]
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
                if (!$invitation['check_in_time']) {
                    // First turnstile scan → entry check-in
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
                    // Subsequent turnstile scan → exit check-out
                    $action   = 'checkout';
                    $duration = $this->formatDuration(time() - strtotime($invitation['check_in_time']));

                    $db->table('invitation_visitors')
                        ->where('id', $invitation['iv_id'])
                        ->where('check_out_time IS NULL')
                        ->update([
                            'check_out_time'  => $now,
                            'version'         => $nextVer,
                            'updated_at'      => $now,
                            'visitor_card_id' => null,
                        ]);

                    $db->table('visitor_cards')->where('id', $card['id'])->update(['status' => 'active']);
                }
            } else {
                // Internal door — visitor must have entered via turnstile first
                if (!$invitation['check_in_time']) {
                    $db->transRollback();
                    return $this->respond([
                        'success'        => false,
                        'access_granted' => false,
                        'action'         => 'denied',
                        'message'        => 'Access denied: Please check in at the entry turnstile first.',
                        'qr_code'        => $cardNumber,
                    ]);
                }

                $action = 'door_access';
            }

            $this->logCardScan($card['id'], $invitation['invitation_id'], $action, $subLocationId !== null ? null : $laneId);

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
                $response['lane'] = ['id' => $subLocationId, 'type' => $laneType, 'source' => 'sub_location'];
            } elseif ($laneId) {
                $response['lane'] = ['id' => $laneId, 'type' => $laneType, 'source' => 'lane'];
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
    protected function logCardScan(int $cardId, int $invitationId, string $action, ?string $laneId = null): void
    {
        $db = \Config\Database::connect();
        $db->table('visitor_card_logs')->insert([
            'visitor_card_id' => $cardId,
            'invitation_id'   => $invitationId,
            'action'          => $action,
            'lane_id'         => $laneId,
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
