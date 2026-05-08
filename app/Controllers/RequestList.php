<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\VisitorLicenseModel;
use App\Models\VisitorEquipmentModel;

class RequestList extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $licenseModel;
    protected $equipmentModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->licenseModel = new VisitorLicenseModel();
        $this->equipmentModel = new VisitorEquipmentModel();
    }

    public function index()
    {
        // Get all submitted invitations with status 'Submitted'
        $submittedRequests = $this->invitationModel
            ->where('status', 'Submitted')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Format queue requests
        $queueRequests = [];
        foreach ($submittedRequests as $request) {
            $schedule = $this->scheduleModel
                ->where('invitation_id', $request['id'])
                ->orderBy('date_from', 'ASC')
                ->first();

            $timeUntilVisit = '';
            if ($schedule) {
                $visitTime = strtotime($schedule['date_from']);
                $now = time();
                $diff = $visitTime - $now;
                
                if ($diff > 0) {
                    $hours = floor($diff / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    
                    if ($hours > 0) {
                        $timeUntilVisit = $hours . 'h ' . $minutes . 'm';
                    } else {
                        $timeUntilVisit = $minutes . 'm';
                    }
                } else {
                    $timeUntilVisit = 'Now';
                }
            }

            $queueRequests[] = [
                'id' => $request['id'],
                'name' => $request['full_name'],
                'company' => $request['company'] ?? 'N/A',
                'host' => $request['invited_by'] ?? '',
                'time' => $timeUntilVisit,
                'photo' => $request['profile_photo_path'] ? base_url('uploads/' . $request['profile_photo_path']) : '',
                'initials' => strtoupper(substr($request['full_name'], 0, 2)),
                'status' => 'normal',
                'is_flagged' => false
            ];
        }

        // Get the first request as current
        $currentRequest = null;
        if (count($submittedRequests) > 0) {
            $first = $submittedRequests[0];
            $schedule = $this->scheduleModel
                ->where('invitation_id', $first['id'])
                ->orderBy('date_from', 'ASC')
                ->first();

            // Get equipment
            $equipment = $this->equipmentModel
                ->where('invitation_id', $first['id'])
                ->findAll();

            $assets = [];
            foreach ($equipment as $item) {
                $assets[] = [
                    'type' => 'inventory_2',
                    'name' => ($item['equipment_type'] ?? 'Equipment') . 
                            ($item['serial_number'] ? ' (Serial: ' . $item['serial_number'] . ')' : '')
                ];
            }

            $currentRequest = [
                'id' => 'VIS-' . $first['id'],
                'name' => $first['full_name'],
                'company' => $first['company'] ?? 'N/A',
                'host' => $first['invited_by'] ?? 'N/A',
                'arrival' => $schedule ? date('h:i A - d/m/Y', strtotime($schedule['date_from'])) : 'N/A',
                'purpose' => $first['reason'] ?? 'N/A',
                'photo' => $first['profile_photo_path'] ? base_url('uploads/' . $first['profile_photo_path']) : '',
                'government_id_image' => $first['government_id_path'] ? base_url('uploads/' . $first['government_id_path']) : '',
                'invitation_letter' => $first['invitation_letter_path'] ? base_url('uploads/' . $first['invitation_letter_path']) : '',
                'access_zones' => explode(', ', $first['location'] ?? ''),
                'assets' => $assets,
                'notes' => '',
                'watchlist_status' => 'cleared',
                'ai_match' => $first['facial_verified_at'] ? 95 : 0,
                'ic_passport' => $first['ic_passport'] ?? 'N/A',
                'contact' => $first['contact'] ?? 'N/A',
                'email' => $first['visitor_email'] ?? 'N/A',
                'vehicle' => $first['vehicle_registration'] ?? 'N/A',
                'staff_id' => $first['staff_id'] ?? 'N/A',
                'host_contact' => $first['host_contact'] ?? 'N/A',
                'company_visited' => $first['company_visited'] ?? 'N/A'
            ];
        }

        // Calculate stats
        $stats = [
            'pending' => $this->invitationModel->where('status', 'Pending')->countAllResults(),
            'flagged' => $this->invitationModel->where('status', 'Submitted')->countAllResults(),
            'expected' => $this->invitationModel->where('status', 'Approved')->countAllResults(),
            'rejected' => $this->invitationModel->where('status', 'Rejected')->countAllResults()
        ];

        $data = [
            'pageTitle' => 'Request List - SafeG',
            'stats' => $stats,
            'currentRequest' => $currentRequest,
            'queueRequests' => $queueRequests
        ];

        return view('requests/list', $data);
    }

    public function approve()
    {
        $json = $this->request->getJSON();
        $id = isset($json->id) ? (int) $json->id : 0;

        if ($id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request ID',
            ]);
        }

        $result = $this->approveInvitationById($id);

        return $this->response->setJSON($result);
    }

    /**
     * Approve multiple submitted requests in one action.
     */
    public function batchApprove()
    {
        $payload = $this->request->getJSON(true);
        $rawIds = $payload['ids'] ?? [];
        if (! is_array($rawIds) || $rawIds === []) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No requests selected',
            ]);
        }

        $ids = array_values(array_unique(array_filter(
            array_map(static fn ($v) => (int) $v, $rawIds),
            static fn ($id) => $id > 0
        )));

        if ($ids === []) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No valid request IDs',
            ]);
        }

        $approved = 0;
        $failed = [];
        foreach ($ids as $id) {
            $result = $this->approveInvitationById($id);
            if (! empty($result['success'])) {
                $approved++;
            } else {
                $failed[] = [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Failed',
                ];
            }
        }

        $msg = $approved === 1
            ? '1 request approved successfully.'
            : $approved . ' requests approved successfully.';
        if ($failed !== []) {
            $msg .= ' ' . count($failed) . ' could not be approved (already processed or invalid).';
        }

        return $this->response->setJSON([
            'success' => $approved > 0,
            'approved' => $approved,
            'failed' => $failed,
            'message' => $msg,
        ]);
    }

    /**
     * @return array{success:bool,message:string}
     */
    private function approveInvitationById(int $id): array
    {
        try {
            $invitation = $this->invitationModel->find($id);

            if (! $invitation) {
                return [
                    'success' => false,
                    'message' => 'Invitation not found',
                ];
            }

            if ($invitation['status'] === 'Approved') {
                return [
                    'success' => false,
                    'message' => 'This request has already been approved',
                ];
            }

            if ($invitation['status'] === 'Rejected') {
                return [
                    'success' => false,
                    'message' => 'This request has already been rejected and cannot be approved',
                ];
            }

            if ($invitation['status'] !== 'Submitted') {
                return [
                    'success' => false,
                    'message' => 'Only submitted requests can be approved (current status: ' . $invitation['status'] . ')',
                ];
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $db->table('invitations')
                ->where('id', $id)
                ->where('status', 'Submitted')
                ->update([
                    'status' => 'Approved',
                    'checked_in_at' => date('Y-m-d H:i:s'),
                    'version' => ($invitation['version'] ?? 1) + 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if ($db->affectedRows() === 0) {
                $db->transRollback();

                return [
                    'success' => false,
                    'message' => 'This request has already been processed by another user. Please refresh the page.',
                ];
            }

            $invitationVisitorModel = new \App\Models\InvitationVisitorModel();
            $existing = $invitationVisitorModel->where('invitation_id', $id)->first();

            if (! $existing) {
                $invitationVisitorModel->insert([
                    'invitation_id' => $id,
                    'full_name' => $invitation['full_name'] ?? 'Visitor',
                    'ic_passport' => ! empty($invitation['ic_passport']) ? $invitation['ic_passport'] : 'PENDING',
                    'contact' => $invitation['contact'] ?? 'N/A',
                    'visitor_card_id' => null,
                    'check_in_time' => null,
                    'check_out_time' => null,
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Failed to approve request due to a database error',
                ];
            }

            (new \App\Services\NotificationService())->dispatch($id, 'request_approved');

            return [
                'success' => true,
                'message' => 'Request approved successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    public function reject()
    {
        $json = $this->request->getJSON();
        $id = $json->id ?? null;
        $reason = $json->reason ?? '';

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request ID'
            ]);
        }

        try {
            $invitation = $this->invitationModel->find($id);

            if (!$invitation) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invitation not found'
                ]);
            }

            if ($invitation['status'] === 'Rejected') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This request has already been rejected'
                ]);
            }

            if ($invitation['status'] === 'Approved') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This request has already been approved and cannot be rejected'
                ]);
            }

            if ($invitation['status'] !== 'Submitted') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only submitted requests can be rejected (current status: ' . $invitation['status'] . ')'
                ]);
            }

            // Atomic update: only succeeds if status is still 'Submitted'
            $db = \Config\Database::connect();
            $db->table('invitations')
                ->where('id', $id)
                ->where('status', 'Submitted')
                ->update([
                    'status' => 'Rejected',
                    'other_reason' => $reason,
                    'version' => ($invitation['version'] ?? 1) + 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if ($db->affectedRows() === 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This request has already been processed by another user. Please refresh the page.'
                ]);
            }

            (new \App\Services\NotificationService())->dispatch($id, 'request_rejected');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Request rejected successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function pastVisits()
    {
        $json = $this->request->getJSON();
        $icPassport = $json->ic_passport ?? null;

        if (!$icPassport) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid IC/Passport number'
            ]);
        }

        try {
            // Fetch past visits for this visitor (exclude current pending/submitted ones)
            $visits = $this->invitationModel
                ->where('ic_passport', $icPassport)
                ->whereIn('status', ['Approved', 'Rejected'])
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->findAll();

            $formattedVisits = [];
            foreach ($visits as $visit) {
                // Get schedule for visit date
                $schedule = $this->scheduleModel
                    ->where('invitation_id', $visit['id'])
                    ->first();

                $formattedVisits[] = [
                    'id' => $visit['id'],
                    'visit_date' => $schedule ? date('d M Y', strtotime($schedule['visit_date'])) : 'N/A',
                    'status' => $visit['status'],
                    'purpose' => $visit['purpose_of_visit'],
                    'host_name' => $visit['host_name'],
                    'company_visited' => $visit['company_visited'],
                    'checked_in_at' => $visit['checked_in_at'] ? date('d M Y H:i', strtotime($visit['checked_in_at'])) : null,
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'visits' => $formattedVisits
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
