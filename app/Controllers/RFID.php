<?php

namespace App\Controllers;

use App\Models\VisitorCardModel;
use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;
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
            // Lock the invitation_visitors row to prevent concurrent scan race
            $invitation = $db->query(
                'SELECT iv.*, iv.id as iv_id, i.full_name as visitor_name, i.company as visitor_company, i.id as invitation_id
                 FROM invitation_visitors iv
                 JOIN invitations i ON i.id = iv.invitation_id
                 WHERE iv.visitor_card_id = ?
                 AND i.status = ?
                 AND DATE(i.created_at) = ?
                 FOR UPDATE',
                [$card['id'], 'Approved', date('Y-m-d')]
            )->getRowArray();

            if (!$invitation) {
                $db->transRollback();
                return $this->respond([
                    'success' => false,
                    'message' => 'No active invitation found for this card today',
                    'card_epc' => $cardEpc
                ]);
            }

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
            // Lock the row to prevent concurrent scan race
            $invitation = $db->query(
                'SELECT iv.*, iv.id as iv_id, i.full_name as visitor_name, i.company as visitor_company, i.id as invitation_id
                 FROM invitation_visitors iv
                 JOIN invitations i ON i.id = iv.invitation_id
                 WHERE iv.visitor_card_id = ?
                 AND i.status = ?
                 AND DATE(i.created_at) = ?
                 FOR UPDATE',
                [$card['id'], 'Approved', date('Y-m-d')]
            )->getRowArray();

            if (!$invitation) {
                $db->transRollback();
                return $this->respond([
                    'success' => false,
                    'message' => 'No active invitation found for this card today',
                    'card_epc' => $cardEpc
                ]);
            }

            $action = 'checkin';
            $duration = null;
            $now = date('Y-m-d H:i:s');
            $nextVersion = ($invitation['version'] ?? 1) + 1;

            if ($laneType === 'exit') {
                $action = 'checkout';

                if ($invitation['check_out_time']) {
                    $db->transRollback();
                    return $this->respond([
                        'success' => false,
                        'message' => 'Visitor has already checked out',
                        'card_epc' => $cardEpc
                    ]);
                }

                if ($invitation['check_in_time']) {
                    $duration = $this->formatDuration(time() - strtotime($invitation['check_in_time']));
                }

                $db->table('invitation_visitors')
                    ->where('id', $invitation['iv_id'])
                    ->where('check_out_time IS NULL')
                    ->update([
                        'check_out_time' => $now,
                        'version' => $nextVersion,
                        'updated_at' => $now
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

            } else {
                if ($invitation['check_in_time'] && !$invitation['check_out_time']) {
                    $action = 'checkout';
                    $duration = $this->formatDuration(time() - strtotime($invitation['check_in_time']));

                    $db->table('invitation_visitors')
                        ->where('id', $invitation['iv_id'])
                        ->where('check_in_time IS NOT NULL')
                        ->where('check_out_time IS NULL')
                        ->update([
                            'check_out_time' => $now,
                            'version' => $nextVersion,
                            'updated_at' => $now
                        ]);

                    if ($db->affectedRows() === 0) {
                        $db->transRollback();
                        return $this->respond([
                            'success' => false,
                            'message' => 'Visitor status has changed. Please try again.',
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
                }
            }

            $this->logCardScan($card['id'], $invitation['invitation_id'], $action, $laneId);

            $db->transComplete();

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
}
