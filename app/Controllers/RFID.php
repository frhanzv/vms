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

        // Find the visitor card
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

        // Check card status
        if ($card['status'] !== 'active' && $card['status'] !== 'in_use') {
            return $this->respond([
                'success' => false,
                'message' => 'Card is ' . $card['status'],
                'card_epc' => $cardEpc
            ]);
        }

        // Find active invitation using this card
        $db = \Config\Database::connect();
        $builder = $db->table('invitation_visitors iv');
        $builder->select('iv.*, iv.id as iv_id, i.full_name as visitor_name, i.company as visitor_company, i.id as invitation_id');
        $builder->join('invitations i', 'i.id = iv.invitation_id');
        $builder->where('iv.visitor_card_id', $card['id']);
        $builder->where('i.status', 'Approved');
        $builder->where('DATE(i.created_at)', date('Y-m-d'));
        $invitation = $builder->get()->getRowArray();

        if (!$invitation) {
            return $this->respond([
                'success' => false,
                'message' => 'No active invitation found for this card today',
                'card_epc' => $cardEpc
            ]);
        }

        // Determine check-in or check-out
        $action = 'checkin';
        $duration = null;

        if ($invitation['check_in_time']) {
            // Already checked in, this is check-out
            $action = 'checkout';
            $checkInTime = strtotime($invitation['check_in_time']);
            $currentTime = time();
            $durationSeconds = $currentTime - $checkInTime;
            $duration = $this->formatDuration($durationSeconds);

            // Update check-out time
            $db->table('invitation_visitors')
                ->where('id', $invitation['iv_id'])
                ->update([
                    'check_out_time' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Update card status to active
            $this->visitorCardModel->update($card['id'], ['status' => 'active']);

        } else {
            // This is check-in
            $db->table('invitation_visitors')
                ->where('id', $invitation['iv_id'])
                ->update([
                    'check_in_time' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Update card status to in_use
            $this->visitorCardModel->update($card['id'], ['status' => 'in_use']);
        }

        // Log the scan
        $this->logCardScan($card['id'], $invitation['invitation_id'], $action);

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
    }

    /**
     * Handle RFID card scan with lane information (multi-reader)
     * GET /api/rfid/scan-lane?card_epc=DD123...&lane_id=1&lane_type=entry
     */
    public function scanLane()
    {
        try {
            $cardEpc = $this->request->getGet('card_epc');
            $laneId = $this->request->getGet('lane_id');
            $laneType = $this->request->getGet('lane_type');

            if (empty($cardEpc)) {
                return $this->failValidationError('Card EPC is required');
            }

            // Find the visitor card
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

            // Check card status
            if ($card['status'] !== 'active' && $card['status'] !== 'in_use') {
                return $this->respond([
                    'success' => false,
                    'message' => 'Card is ' . $card['status'],
                    'card_epc' => $cardEpc
                ]);
            }

        // Find active invitation using this card
        $db = \Config\Database::connect();
        $builder = $db->table('invitation_visitors iv');
        $builder->select('iv.*, iv.id as iv_id, i.full_name as visitor_name, i.company as visitor_company, i.id as invitation_id');
        $builder->join('invitations i', 'i.id = iv.invitation_id');
        $builder->where('iv.visitor_card_id', $card['id']);
        $builder->where('i.status', 'Approved');
        $builder->where('DATE(i.created_at)', date('Y-m-d'));
        $invitation = $builder->get()->getRowArray();

        if (!$invitation) {
            return $this->respond([
                'success' => false,
                'message' => 'No active invitation found for this card today',
                'card_epc' => $cardEpc
            ]);
        }

        // Determine action based on lane type and current status
        $action = 'checkin';
        $duration = null;

        if ($laneType === 'exit') {
            // Exit lane always means check-out
            $action = 'checkout';

            if ($invitation['check_in_time']) {
                $checkInTime = strtotime($invitation['check_in_time']);
                $currentTime = time();
                $durationSeconds = $currentTime - $checkInTime;
                $duration = $this->formatDuration($durationSeconds);
            }

            // Update check-out time
            $db->table('invitation_visitors')
                ->where('id', $invitation['iv_id'])
                ->update([
                    'check_out_time' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Update card status to active
            $this->visitorCardModel->update($card['id'], ['status' => 'active']);

        } else {
            // Entry lane or bidirectional
            if ($invitation['check_in_time'] && !$invitation['check_out_time']) {
                // Already checked in but not checked out - this could be exit
                // For bidirectional lanes, we check if they're exiting
                $action = 'checkout';

                $checkInTime = strtotime($invitation['check_in_time']);
                $currentTime = time();
                $durationSeconds = $currentTime - $checkInTime;
                $duration = $this->formatDuration($durationSeconds);

                $db->table('invitation_visitors')
                    ->where('id', $invitation['iv_id'])
                    ->update([
                        'check_out_time' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                $this->visitorCardModel->update($card['id'], ['status' => 'active']);
            } else {
                // This is check-in
                $db->table('invitation_visitors')
                    ->where('id', $invitation['iv_id'])
                    ->update([
                        'check_in_time' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                $this->visitorCardModel->update($card['id'], ['status' => 'in_use']);
            }
        }

        // Log the scan
        $this->logCardScan($card['id'], $invitation['invitation_id'], $action, $laneId);

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
            'time' => date('Y-m-d H:i:s'),
            'duration' => $duration,
            'card_epc' => $cardEpc
        ]);
        } catch (\Exception $e) {
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
