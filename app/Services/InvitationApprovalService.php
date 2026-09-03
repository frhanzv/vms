<?php

namespace App\Services;

use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;

class InvitationApprovalService
{
    private InvitationModel $invitationModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
    }

    /**
     * @return array{success:bool,message:string,notification_sent?:bool}
     */
    public function approve(int $invitationId): array
    {
        try {
            $invitation = $this->invitationModel->find($invitationId);

            if (! $invitation) {
                return ['success' => false, 'message' => 'Invitation not found'];
            }

            if ($invitation['status'] === 'Approved') {
                return ['success' => true, 'message' => 'This request has already been approved'];
            }

            if ($invitation['status'] === 'Rejected') {
                return ['success' => false, 'message' => 'This request has already been rejected and cannot be approved'];
            }

            if ($invitation['status'] !== 'Submitted') {
                return [
                    'success' => false,
                    'message' => 'Only submitted requests can be approved (current status: ' . $invitation['status'] . ')',
                ];
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $now = date('Y-m-d H:i:s');
            $db->table('invitations')
                ->where('id', $invitationId)
                ->where('status', 'Submitted')
                ->update([
                    'status' => 'Approved',
                    'version' => ($invitation['version'] ?? 1) + 1,
                    'updated_at' => $now,
                ]);

            if ($db->affectedRows() === 0) {
                $db->transRollback();

                $latest = $this->invitationModel->find($invitationId);
                if (($latest['status'] ?? null) === 'Approved') {
                    return ['success' => true, 'message' => 'This request has already been approved'];
                }

                return [
                    'success' => false,
                    'message' => 'This request has already been processed. Please refresh the page.',
                ];
            }

            $visitorModel = new InvitationVisitorModel();
            if (! $visitorModel->where('invitation_id', $invitationId)->first()) {
                $visitorModel->insert([
                    'invitation_id' => $invitationId,
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
                return ['success' => false, 'message' => 'Failed to approve request due to a database error'];
            }

            $notificationSent = (new NotificationService())->dispatch($invitationId, 'request_approved');
            if (! $notificationSent) {
                log_message('warning', 'Invitation approved but approval/QR notification failed for invitation ID: ' . $invitationId);
            }

            return [
                'success' => true,
                'message' => 'Request approved successfully',
                'notification_sent' => $notificationSent,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Invitation approval failed for ID ' . $invitationId . ': ' . $e->getMessage());

            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }
}
