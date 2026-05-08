<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;
use App\Services\NotificationService;
use App\Libraries\InvitationEmailSender;
use CodeIgniter\RESTful\ResourceController;

class QRCode extends ResourceController
{
    protected $format = 'json';
    protected $invitationModel;
    protected $invitationVisitorModel;

    public function __construct()
    {
        $this->invitationModel        = new InvitationModel();
        $this->invitationVisitorModel = new InvitationVisitorModel();
    }

    /**
     * Handle QR code scan (single reader / USB HID)
     * GET /api/qr/scan?qr_code=123  or  ?qr_code=INV-123
     */
    public function scan()
    {
        $raw = $this->request->getGet('qr_code');

        if (empty($raw)) {
            return $this->failValidationError('QR code value is required');
        }

        $invitationId = $this->parseQrCode($raw);

        if (!$invitationId) {
            return $this->respond([
                'success'  => false,
                'message'  => 'Invalid QR code format',
                'qr_code'  => $raw,
            ]);
        }

        return $this->processCheckInOut($invitationId, $raw, null, null);
    }

    /**
     * Handle QR code scan with lane information (multi-reader)
     * GET /api/qr/scan-lane?qr_code=INV-123&lane_id=1&lane_type=entry
     */
    public function scanLane()
    {
        $raw      = $this->request->getGet('qr_code');
        $laneId   = $this->request->getGet('lane_id');
        $laneType = $this->request->getGet('lane_type');

        if (empty($raw)) {
            return $this->failValidationError('QR code value is required');
        }

        $invitationId = $this->parseQrCode($raw);

        if (!$invitationId) {
            return $this->respond([
                'success'  => false,
                'message'  => 'Invalid QR code format',
                'qr_code'  => $raw,
            ]);
        }

        return $this->processCheckInOut($invitationId, $raw, $laneId, $laneType);
    }

    /**
     * Get QR scan reader status / active configuration
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
                'type'   => 'qr_code',
                'mode'   => 'usb_hid',
                'status' => 'configured',
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Parse the raw QR code string into an invitation ID.
     *
     * Supported formats:
     *   123          plain integer
     *   INV-123      prefixed
     *   inv-123      case-insensitive prefix
     */
    protected function parseQrCode(string $raw): ?int
    {
        $raw = trim($raw);

        // Plain integer
        if (ctype_digit($raw)) {
            return (int) $raw;
        }

        // INV-{id} prefix (case-insensitive)
        if (preg_match('/^INV-(\d+)$/i', $raw, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * Core check-in / check-out logic, shared by scan() and scanLane().
     */
    protected function processCheckInOut(int $invitationId, string $rawQr, ?string $laneId, ?string $laneType)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Lock the invitation row
            $invitation = $db->query(
                'SELECT i.*, u.company_id
                 FROM invitations i
                 LEFT JOIN users u ON i.staff_id = u.staff_id
                 WHERE i.id = ? AND i.status = ? FOR UPDATE',
                [$invitationId, 'Approved']
            )->getRowArray();

            if (!$invitation) {
                $db->transRollback();
                return $this->respond([
                    'success' => false,
                    'message' => 'No approved invitation found for this QR code',
                    'qr_code' => $rawQr,
                ]);
            }

            // Find or auto-create the invitation_visitors record
            $iv = $db->query(
                'SELECT * FROM invitation_visitors WHERE invitation_id = ? FOR UPDATE',
                [$invitationId]
            )->getRowArray();

            if (!$iv) {
                // Auto-create for QR check-in (no physical card needed)
                $newId = $db->table('invitation_visitors')->insert([
                    'invitation_id' => $invitationId,
                    'full_name'     => $invitation['full_name'],
                    'ic_passport'   => $invitation['ic_passport'] ?? '',
                    'contact'       => $invitation['contact']     ?? '',
                    'company'       => $invitation['company']     ?? '',
                    'version'       => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);

                $iv = $db->table('invitation_visitors')->where('id', $db->insertID())->get()->getRowArray();
            }

            $action   = 'checkin';
            $duration = null;
            $now      = date('Y-m-d H:i:s');
            $nextVer  = ((int) ($iv['version'] ?? 1)) + 1;

            if ($laneType === 'exit') {
                // Dedicated exit lane — force checkout
                $action = 'checkout';

                if ($iv['check_out_time']) {
                    $db->transRollback();
                    return $this->respond([
                        'success' => false,
                        'message' => 'Visitor has already checked out',
                        'qr_code' => $rawQr,
                    ]);
                }

                if ($iv['check_in_time']) {
                    $duration = $this->formatDuration(time() - strtotime($iv['check_in_time']));
                }

                $db->table('invitation_visitors')
                    ->where('id', $iv['id'])
                    ->where('check_out_time IS NULL')
                    ->update(['check_out_time' => $now, 'version' => $nextVer, 'updated_at' => $now]);

                if ($db->affectedRows() === 0) {
                    $db->transRollback();
                    return $this->respond([
                        'success' => false,
                        'message' => 'Visitor has already been checked out',
                        'qr_code' => $rawQr,
                    ]);
                }

            } elseif ($iv['check_in_time'] && !$iv['check_out_time']) {
                // Toggled checkout (single reader, both in+out)
                $action   = 'checkout';
                $duration = $this->formatDuration(time() - strtotime($iv['check_in_time']));

                $db->table('invitation_visitors')
                    ->where('id', $iv['id'])
                    ->where('check_in_time IS NOT NULL')
                    ->where('check_out_time IS NULL')
                    ->update(['check_out_time' => $now, 'version' => $nextVer, 'updated_at' => $now]);

                if ($db->affectedRows() === 0) {
                    $db->transRollback();
                    return $this->respond([
                        'success' => false,
                        'message' => 'Visitor has already been checked out',
                        'qr_code' => $rawQr,
                    ]);
                }

            } elseif ($iv['check_out_time']) {
                $db->transRollback();
                return $this->respond([
                    'success' => false,
                    'message' => 'Visitor has already checked out for today',
                    'qr_code' => $rawQr,
                ]);

            } else {
                // Check in
                $db->table('invitation_visitors')
                    ->where('id', $iv['id'])
                    ->where('check_in_time IS NULL')
                    ->update(['check_in_time' => $now, 'version' => $nextVer, 'updated_at' => $now]);

                if ($db->affectedRows() === 0) {
                    $db->transRollback();
                    return $this->respond([
                        'success' => false,
                        'message' => 'Visitor has already been checked in',
                        'qr_code' => $rawQr,
                    ]);
                }
            }

            $this->logQrScan($invitationId, $action, $laneId);

            $db->transComplete();

            $notifType = $action === 'checkin' ? 'check_in' : 'check_out';
            (new NotificationService())->dispatch($invitationId, $notifType);

            if ($action === 'checkin' && !empty($invitation['ic_passport'])) {
                $blacklisted = $db->table('blacklist')
                    ->whereIn('status', ['active', 'pending'])
                    ->where('ic_passport_no', $invitation['ic_passport'])
                    ->get()->getRowArray();

                if ($blacklisted) {
                    (new InvitationEmailSender())->sendBlacklistFlagged(
                        (int) ($invitation['company_id'] ?? 0),
                        $invitation['full_name'] ?? '',
                        $invitation['ic_passport'],
                        $blacklisted['reason'] ?? ''
                    );
                }
            }

            $response = [
                'success'  => true,
                'action'   => $action,
                'visitor'  => [
                    'name'    => $invitation['full_name'] ?? 'Unknown',
                    'company' => $invitation['company']   ?? 'N/A',
                ],
                'time'     => $now,
                'duration' => $duration,
                'qr_code'  => $rawQr,
            ];

            if ($laneId) {
                $response['lane'] = ['id' => $laneId, 'type' => $laneType];
            }

            return $this->respond($response);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'QRCode scan error: ' . $e->getMessage());
            return $this->respond([
                'success' => false,
                'message' => 'Server error during QR scan',
                'qr_code' => $rawQr,
            ], 500);
        }
    }

    /**
     * Log QR scan to visitor_card_logs (visitor_card_id is NULL for QR mode).
     */
    protected function logQrScan(int $invitationId, string $action, ?string $laneId = null)
    {
        $db = \Config\Database::connect();
        $db->table('visitor_card_logs')->insert([
            'visitor_card_id' => null,
            'invitation_id'   => $invitationId,
            'action'          => $action,
            'lane_id'         => $laneId,
            'scan_source'     => 'qr_code',
            'scanned_at'      => date('Y-m-d H:i:s'),
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
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
