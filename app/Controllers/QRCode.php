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
     *   IC / passport only    exact match on invitations.ic_passport (Approved), latest id
     *   123                   plain invitation id (legacy)
     *   INV-123, VIS-123      prefixed (case-insensitive)
     *   Multi-line legacy     first line if VIS-/INV-, else treat whole first line as ic_passport
     */
    protected function parseQrCode(string $raw): ?int
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $line = $raw;
        if (preg_match('/\R/u', $raw)) {
            $parts = preg_split('/\R/u', $raw, 2);
            $first = trim((string) ($parts[0] ?? ''));
            $second = trim((string) ($parts[1] ?? ''));
            if (preg_match('/^(INV|VIS)-/i', $first)) {
                $line = $first;
            } elseif ($first !== '' && $second !== '') {
                // Legacy: line1 IC, line2 VIS — try both
                $byIc = $this->findInvitationIdByIcPassport($first);
                if ($byIc !== null) {
                    return $byIc;
                }
                $line = $second;
            } else {
                $line = $first;
            }
        }

        // INV-{id}, VIS-{id}
        if (preg_match('/^INV-(\d+)$/i', $line, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/^VIS-(\d+)$/i', $line, $m)) {
            return (int) $m[1];
        }

        // Document number only (approval QR encodes ic_passport as stored)
        $byIc = $this->findInvitationIdByIcPassport($line);
        if ($byIc !== null) {
            return $byIc;
        }

        // Plain integer — invitation id (legacy cards)
        if (ctype_digit($line)) {
            return (int) $line;
        }

        return null;
    }

    /**
     * Latest approved invitation for this IC/passport value.
     */
    protected function findInvitationIdByIcPassport(string $doc): ?int
    {
        $doc = trim($doc);
        if ($doc === '') {
            return null;
        }

        $row = $this->invitationModel
            ->where('ic_passport', $doc)
            ->where('status', 'Approved')
            ->orderBy('id', 'DESC')
            ->first();

        if ($row && isset($row['id'])) {
            return (int) $row['id'];
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
                'SELECT i.*, u.company_id, vt.name as visitor_type_name
                 FROM invitations i
                 LEFT JOIN users u ON i.staff_id = u.staff_id
                 LEFT JOIN visitor_types vt ON vt.id = i.visitor_type_id
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

            // Access control: validate lane against visitor type's pathway (entry only)
            if ($laneId && $laneType !== 'exit') {
                $accessCheck = $this->checkVisitorTypeAccess($invitation, (int) $laneId);
                if (!$accessCheck['granted']) {
                    $db->transRollback();
                    return $this->respond([
                        'success'        => false,
                        'access_granted' => false,
                        'action'         => 'denied',
                        'message'        => $accessCheck['message'],
                        'visitor'        => [
                            'name'         => $invitation['full_name']        ?? 'Unknown',
                            'company'      => $invitation['company']          ?? 'N/A',
                            'visitor_type' => $invitation['visitor_type_name'] ?? null,
                        ],
                        'qr_code'        => $rawQr,
                    ]);
                }
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

            $resident = strtoupper(trim((string) ($invitation['resident'] ?? '')));
            $idDoc    = trim((string) ($invitation['ic_passport'] ?? ''));
            $response = [
                'success'        => true,
                'access_granted' => true,
                'action'         => $action,
                'visitor'        => [
                    'name'         => $invitation['full_name']        ?? 'Unknown',
                    'company'      => $invitation['company']          ?? 'N/A',
                    'visitor_type' => $invitation['visitor_type_name'] ?? null,
                    'resident'     => $resident !== '' ? $resident : null,
                    'ic_passport'  => $idDoc,
                    'id_document'  => $idDoc !== ''
                        ? (($resident === 'FOREIGN' || (empty($resident) && preg_match('/[A-Z]/i', $idDoc))) ? ('Passport No.: ' . $idDoc) : ('IC No.: ' . $idDoc))
                        : null,
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
     * Check if the given lane is permitted for the visitor's type via pathway config.
     * Returns ['granted' => bool, 'message' => string|null].
     * When no visitor type or no pathway is set, access is unrestricted.
     */
    protected function checkVisitorTypeAccess(array $invitation, int $laneId): array
    {
        if (empty($invitation['visitor_type_id'])) {
            return ['granted' => true, 'message' => null];
        }

        $db = \Config\Database::connect();

        $visitorType = $db->table('visitor_types')
            ->where('id', $invitation['visitor_type_id'])
            ->get()->getRowArray();

        if (!$visitorType || empty($visitorType['path'])) {
            return ['granted' => true, 'message' => null];
        }

        $pathway = $db->table('pathways')
            ->where('name', $visitorType['path'])
            ->where('status', 'active')
            ->get()->getRowArray();

        if (!$pathway) {
            return ['granted' => true, 'message' => null];
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
