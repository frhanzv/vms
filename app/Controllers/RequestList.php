<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;
use App\Models\VisitorLicenseModel;
use App\Models\VisitorEquipmentModel;
use App\Libraries\InvitationProcessFlowService;

class RequestList extends BaseController
{
    protected $invitationModel;
    protected $scheduleModel;
    protected $licenseModel;
    protected $equipmentModel;
    protected $flowService;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->scheduleModel = new InvitationScheduleModel();
        $this->licenseModel = new VisitorLicenseModel();
        $this->equipmentModel = new VisitorEquipmentModel();
        $this->flowService = new InvitationProcessFlowService();
    }

    public function index()
    {
        $activeSteps = $this->flowService->getOrderedSteps(true);
        $requiresBriefing = false;
        $requiresFacial = false;

        foreach ($activeSteps as $step) {
            if ($step['key'] === 'approval') {
                break;
            }
            if ($step['key'] === 'security_briefing' || $step['key'] === 'video') {
                $requiresBriefing = true;
            }
            if ($step['key'] === 'facial_verification' || $step['key'] === 'take_photo') {
                $requiresFacial = true;
            }
        }

        // Get all submitted invitations that meet the workflow requirements
        $query = $this->invitationModel->where('status', 'Submitted');
        
        if ($requiresBriefing) {
            $query->where('video_watched', 1);
        }
        if ($requiresFacial) {
            $query->where('facial_verified_at IS NOT NULL');
        }

        $submittedRequests = $query->orderBy('created_at', 'DESC')->findAll();

        $requestsList = [];
        foreach ($submittedRequests as $index => $request) {
            $schedule = $this->scheduleModel
                ->where('invitation_id', $request['id'])
                ->orderBy('date_from', 'ASC')
                ->first();

            $date = $schedule ? date('d/m/Y', strtotime($schedule['date_from'])) : date('d/m/Y', strtotime($request['created_at']));
            $reason = $request['reason'] ?? ($request['purpose_of_visit'] ?? '-');

            $requestsList[] = [
                'id' => $request['id'],
                'no' => $index + 1,
                'date' => $date,
                'name' => strtoupper($request['full_name']),
                'ic_passport' => $request['ic_passport'] ?? '-',
                'type' => 'Invitation',
                'status' => 'Pending', // Legacy design shows Pending for awaiting approval
                'reason' => strtoupper($reason)
            ];
        }

        $data = [
            'pageTitle' => 'Visitor Pass Request List - SafeG',
            'requestsList' => $requestsList
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
