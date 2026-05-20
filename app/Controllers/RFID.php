<?php

namespace App\Controllers;

use App\Models\VisitorCardModel;
use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;
use App\Services\NotificationService;
use App\Libraries\InvitationEmailSender;
use CodeIgniter\RESTful\ResourceController;

class RFID extends ResourceController
{
    protected $format = 'json';
    protected $visitorCardModel;
    protected $invitationModel;
    protected $invitationVisitorModel;

    public function __construct()
    {
        $this->visitorCardModel = new VisitorCardModel();
        $this->invitationModel = new InvitationModel();
        $this->invitationVisitorModel = new InvitationVisitorModel();
    }

    /**
     * Handle RFID card scan (single reader)
     * GET /api/rfid/scan?card_epc=DD123...
     */
    public function scan()
    {
        $cardEpc = $this->request->getGet('card_epc');

        if (empty($cardEpc)) {
            return $this->failValidationError('Card EPC is required');
        }

        $card = $this->visitorCardModel
            ->where('card_id', $cardEpc)
            ->first();

        if (!$card) {
            log_message('warning', 'Unknown card scanned: ' . $cardEpc);
            return $this->respond([
                'success' => false,
                'message' => 'Card not registered in system',
                'card_epc' => $cardEpc
            ]);
        }

        if ($card['status'] !== 'active' && $card['status'] !== 'in_use') {
            return $this->respond([
                'success' => false,
                'message' => 'Card is ' . $card['status'],
                'card_epc' => $cardEpc
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Lock the invitation_visitors row to prevent concurrent scan race.
            // Join on vc.card_id so duplicate card_id rows don't cause a miss.
            // No date filter: admin card assignment is the authorization; date checks
            // would break multi-day visits or cards assigned for yesterday's schedule.
            $invitation = $db->query(
                'SELECT iv.*, iv.id as iv_id, i.full_name as visitor_name, i.company as visitor_company,
                        i.id as invitation_id, i.ic_passport, u.company_id, i.visitor_type_id
                 FROM invitation_visitors iv
                 JOIN visitor_cards vc ON vc.id = iv.visitor_card_id
                 JOIN invitations i ON i.id = iv.invitation_id
                 LEFT JOIN users u ON i.staff_id = u.staff_id
                 WHERE vc.card_id = ?
                 AND i.status = ?
                 AND iv.check_out_time IS NULL
                 FOR UPDATE',
                [$cardEpc, 'Approved']
            )->getRowArray();

            if (!$invitation) {
                $db->transRollback();
                $this->insertSecurityAlert(
                    'Unauthorized Access',
                    'high',
                    'Unknown',
                    'Security',
                    'Card is not assigned to any visitor. Card number: ' . $cardEpc
                );
                return $this->respond([
                    'success'        => false,
                    'access_granted' => false,
                    'action'         => 'denied',
                    'message'        => 'Access denied: Card is not assigned to any visitor.',
                    'card_epc'       => $cardEpc
                ]);
            }

            // Sync to the card row actually assigned to this visitor (handles duplicate card_id)
            $card['id'] = $invitation['visitor_card_id'];

            $action = 'checkin';
            $duration = null;

            if ($invitation['check_in_time'] && !$invitation['check_out_time']) {
                $action = 'checkout';
                $durationSeconds = time() - strtotime($invitation['check_in_time']);
                $duration = $this->formatDuration($durationSeconds);

                // Atomic: only check out if still checked in without checkout
                $db->table('invitation_visitors')
                    ->where('id', $invitation['iv_id'])
                    ->where('check_in_time IS NOT NULL')
                    ->where('check_out_time IS NULL')
                    ->update([
                        'check_out_time' => date('Y-m-d H:i:s'),
                        'version' => ($invitation['version'] ?? 1) + 1,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                if ($db->affectedRows() === 0) {
                    $db->transRollback();
                    return $this->respond([
                        'success' => false,
                        'message' => 'Visitor has already been checked out',
                        'card_epc' => $cardEpc
                    ]);
                }

                $this->visitorCardModel->update($card['id'], ['status' => 'active']);

            } elseif ($invitation['check_out_time']) {
                $db->transRollback();
                return $this->respond([
                    'success' => false,
                    'message' => 'Visitor has already checked out for today',
                    'card_epc' => $cardEpc
                ]);
            } else {
                // Atomic: only check in if not already checked in
                $db->table('invitation_visitors')
                    ->where('id', $invitation['iv_id'])
                    ->where('check_in_time IS NULL')
                    ->update([
                        'check_in_time' => date('Y-m-d H:i:s'),
                        'version' => ($invitation['version'] ?? 1) + 1,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                if ($db->affectedRows() === 0) {
                    $db->transRollback();
                    return $this->respond([
                        'success' => false,
                        'message' => 'Visitor has already been checked in',
                        'card_epc' => $cardEpc
                    ]);
                }

                $this->visitorCardModel->update($card['id'], ['status' => 'in_use']);
            }

            $this->logCardScan($card['id'], $invitation['invitation_id'], $action);

            $db->transComplete();

            $notifType = $action === 'checkin' ? 'check_in' : 'check_out';
            (new NotificationService())->dispatch($invitation['invitation_id'], $notifType);

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

            return $this->respond([
                'success' => true,
                'action' => $action,
                'visitor' => [
                    'name' => $invitation['visitor_name'] ?? 'Unknown',
                    'company' => $invitation['visitor_company'] ?? 'N/A'
                ],
                'time' => date('Y-m-d H:i:s'),
                'duration' => $duration,
                'card_epc' => $cardEpc
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'RFID scan error: ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Server error during scan',
                'card_epc' => $cardEpc
            ], 500);
        }
    }

    /**
     * Handle RFID card scan with lane information (multi-reader)
     * GET /api/rfid/scan-lane?card_epc=DD123...&lane_id=1&lane_type=entry
     */
    public function scanLane()
    {
        $cardEpc = $this->request->getGet('card_epc');
        $laneId = $this->request->getGet('lane_id');
        $laneType = $this->request->getGet('lane_type');

        if (empty($cardEpc)) {
            return $this->failValidationError('Card EPC is required');
        }

        $card = $this->visitorCardModel
            ->where('card_id', $cardEpc)
            ->first();

        if (!$card) {
            log_message('warning', 'Unknown card scanned: ' . $cardEpc . ' at lane ' . $laneId);
            return $this->respond([
                'success' => false,
                'message' => 'Card not registered in system',
                'card_epc' => $cardEpc
            ]);
        }

        if ($card['status'] !== 'active' && $card['status'] !== 'in_use') {
            return $this->respond([
                'success' => false,
                'message' => 'Card is ' . $card['status'],
                'card_epc' => $cardEpc
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Lock the row to prevent concurrent scan race.
            // Join on vc.card_id so duplicate card_id rows don't cause a miss.
            // No date filter: admin card assignment is the authorization; date checks
            // would break multi-day visits or cards assigned for yesterday's schedule.
            $invitation = $db->query(
                'SELECT iv.*, iv.id as iv_id, i.full_name as visitor_name, i.company as visitor_company,
                        i.id as invitation_id, i.ic_passport, u.company_id, i.visitor_type_id
                 FROM invitation_visitors iv
                 JOIN visitor_cards vc ON vc.id = iv.visitor_card_id
                 JOIN invitations i ON i.id = iv.invitation_id
                 LEFT JOIN users u ON i.staff_id = u.staff_id
                 WHERE vc.card_id = ?
                 AND i.status = ?
                 AND iv.check_out_time IS NULL
                 FOR UPDATE',
                [$cardEpc, 'Approved']
            )->getRowArray();

            if (!$invitation) {
                $db->transRollback();
                $laneName = $laneId ? ($db->table('lanes')->where('id', $laneId)->get()->getRowArray()['lane'] ?? '') : '';
                $this->insertSecurityAlert(
                    'Unauthorized Access',
                    'high',
                    'Unknown',
                    $laneName ?: 'Security',
                    'Card is not assigned to any visitor. Card number: ' . $cardEpc
                );
                return $this->respond([
                    'success'        => false,
                    'access_granted' => false,
                    'action'         => 'denied',
                    'message'        => 'Access denied: Card is not assigned to any visitor.',
                    'card_epc'       => $cardEpc
                ]);
            }

            // Sync to the card row actually assigned to this visitor (handles duplicate card_id)
            $card['id'] = $invitation['visitor_card_id'];

            $laneName = null;
            if ($laneId) {
                $laneName = $db->table('lanes')->where('id', $laneId)->get()->getRowArray()['lane'] ?? '';
                
                $accessCheck = $this->checkVisitorTypeAccess($invitation, (int) $laneId);
                if (!$accessCheck['granted']) {
                    $db->transRollback();
                    if ($db->tableExists('security_alerts')) {
                        $db->table('security_alerts')->insert([
                            'incident_type'   => 'Unauthorized Access',
                            'severity'        => 'high',
                            'visitor_name'    => $invitation['visitor_name'] ?? 'Unknown',
                            'location'        => $laneName ?: 'Security',
                            'description'     => 'Visitor attempted to access an unauthorized door (' . $laneName . ').',
                            'is_acknowledged' => 0,
                            'created_at'      => date('Y-m-d H:i:s'),
                            'updated_at'      => date('Y-m-d H:i:s'),
                        ]);
                    }
                    return $this->respond([
                        'success' => false,
                        'message' => $accessCheck['message'],
                        'card_epc' => $cardEpc
                    ]);
                }
            }

            $action = 'checkin';
            $duration = null;
            $now = date('Y-m-d H:i:s');
            $nextVersion = ($invitation['version'] ?? 1) + 1;
            $isTurnstile = ($laneId && stripos($laneName ?? '', 'TURNSTILE') !== false);

            if ($invitation['check_out_time']) {
                $db->transRollback();
                return $this->respond([
                    'success' => false,
                    'message' => 'Visitor has already checked out for today',
                    'card_epc' => $cardEpc
                ]);
            }

            if (!$invitation['check_in_time']) {
                $db->table('invitation_visitors')
                    ->where('id', $invitation['iv_id'])
                    ->where('check_in_time IS NULL')
                    ->update([
                        'check_in_time' => $now,
                        'version' => $nextVersion,
                        'updated_at' => $now
                    ]);

                if ($db->affectedRows() === 0) {
                    $db->transRollback();
                    return $this->respond([
                        'success' => false,
                        'message' => 'Visitor has already been checked in',
                        'card_epc' => $cardEpc
                    ]);
                }

                $this->visitorCardModel->update($card['id'], ['status' => 'in_use']);
            } else {
                $isCheckout = false;
                if ($laneType === 'exit' && $isTurnstile) {
                    $isCheckout = true;
                } elseif (!$laneType && $isTurnstile) {
                    $isCheckout = true;
                }

                if ($isCheckout) {
                    $action = 'checkout';
                    $duration = $this->formatDuration(time() - strtotime($invitation['check_in_time']));

                    $db->table('invitation_visitors')
                        ->where('id', $invitation['iv_id'])
                        ->where('check_out_time IS NULL')
                        ->update([
                            'check_out_time' => $now,
                            'version' => $nextVersion,
                            'updated_at' => $now,
                            'visitor_card_id' => null
                        ]);

                    $this->visitorCardModel->update($card['id'], ['status' => 'active']);
                } else {
                    $action = 'door_access';
                }
            }

            $this->logCardScan($card['id'], $invitation['invitation_id'], $action, $laneId);

            $db->transComplete();

            $notifType = $action === 'checkin' ? 'check_in' : 'check_out';
            (new NotificationService())->dispatch($invitation['invitation_id'], $notifType);

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

            return $this->respond([
                'success' => true,
                'action' => $action,
                'visitor' => [
                    'name' => $invitation['visitor_name'] ?? 'Unknown',
                    'company' => $invitation['visitor_company'] ?? 'N/A'
                ],
                'lane' => [
                    'id' => $laneId,
                    'type' => $laneType
                ],
                'time' => $now,
                'duration' => $duration,
                'card_epc' => $cardEpc
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'RFID scanLane error: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return $this->respond([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'card_epc' => $cardEpc ?? 'unknown'
            ], 500);
        }
    }

    /**
     * Get RFID reader status
     * GET /api/rfid/status
     */
    public function status()
    {
        $config = config('RFIDReader');

        return $this->respond([
            'success' => true,
            'reader' => [
                'ip' => $config->readerIP,
                'port' => $config->readerPort,
                'protocol' => $config->protocol,
                'status' => 'configured'
            ]
        ]);
    }

    /**
     * Test RFID reader connection
     * GET /api/rfid/test-connection
     */
    public function testConnection()
    {
        $config = config('RFIDReader');

        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to create socket'
            ]);
        }

        $result = @socket_connect($socket, $config->readerIP, $config->readerPort);
        socket_close($socket);

        if ($result === false) {
            return $this->respond([
                'success' => false,
                'message' => 'Cannot connect to RFID reader',
                'ip' => $config->readerIP,
                'port' => $config->readerPort
            ]);
        }

        return $this->respond([
            'success' => true,
            'message' => 'Successfully connected to RFID reader',
            'ip' => $config->readerIP,
            'port' => $config->readerPort
        ]);
    }

    /**
     * Log card scan to database
     */
    protected function logCardScan($cardId, $invitationId, $action, $laneId = null)
    {
        $db = \Config\Database::connect();
        $db->table('visitor_card_logs')->insert([
            'visitor_card_id' => $cardId,
            'invitation_id' => $invitationId,
            'action' => $action,
            'lane_id' => $laneId,
            'scanned_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Format duration in human-readable format
     */
    protected function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%d hour%s %d minute%s', $hours, $hours > 1 ? 's' : '', $minutes, $minutes != 1 ? 's' : '');
        }

        return sprintf('%d minute%s', $minutes, $minutes != 1 ? 's' : '');
    }

    /**
     * Check if the given lane is permitted for the visitor's type via pathway config.
     * Returns ['granted' => bool, 'message' => string|null].
     */
    protected function checkVisitorTypeAccess(array $invitation, int $laneId): array
    {
        if (empty($invitation['visitor_type_id'])) {
            return [
                'granted' => false,
                'message' => 'Access denied: No visitor type assigned — door access cannot be determined.',
            ];
        }

        $db = \Config\Database::connect();

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

        $inPathway = $db->table('pathway_lanes')
            ->where('pathway_id', $pathway['id'])
            ->where('lane_id', $laneId)
            ->countAllResults() > 0;

        if (!$inPathway) {
            $typeName = $visitorType['name'] ?? 'Unknown';
            return [
                'granted' => false,
                'message' => "Access denied: visitor type \"{$typeName}\" is not authorised for this lane.",
            ];
        }

        return ['granted' => true, 'message' => null];
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
}
