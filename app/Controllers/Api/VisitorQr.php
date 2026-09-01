<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\InvitationEmailSender;
use App\Services\InvitationQrService;
use CodeIgniter\API\ResponseTrait;

class VisitorQr extends BaseController
{
    use ResponseTrait;

    public function verify()
    {
        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $token = trim((string) ($payload['qr_token'] ?? $payload['token'] ?? ''));
        $credential = (new InvitationQrService())->findValidCredential($token);
        if (! $credential) {
            return $this->respond(['success' => false, 'valid' => false, 'message' => 'Invalid QR code'], 404);
        }

        if (! empty($credential['expires_at']) && strtotime($credential['expires_at']) < time()) {
            return $this->respond(['success' => false, 'valid' => false, 'message' => 'QR code has expired'], 410);
        }

        $db = \Config\Database::connect();
        $invitation = (new InvitationEmailSender())->getInvitationDetails((int) $credential['invitation_id']);

        if (! $invitation || ($invitation['status'] ?? '') !== 'Approved') {
            return $this->respond(['success' => false, 'valid' => false, 'message' => 'Visitor invitation is not approved'], 403);
        }

        $visitState = $db->table('invitation_visitors')
            ->select('check_in_time, check_out_time')
            ->where('invitation_id', $invitation['id'])
            ->orderBy('id', 'DESC')
            ->get()->getRowArray() ?: [];

        $photo = trim((string) ($invitation['profile_photo_path'] ?? ''));
        $presence = ! empty($visitState['check_out_time'])
            ? 'Checked Out'
            : (! empty($visitState['check_in_time']) ? 'Inside' : 'Not Checked In');
        $hostUser = is_array($invitation['host_user'] ?? null) ? $invitation['host_user'] : [];

        return $this->respond([
            'success' => true,
            'valid' => true,
            'visitor' => [
                'name' => $invitation['full_name'] ?? '',
                'contact' => $invitation['contact'] ?? '',
                'company' => $invitation['company_name'] ?? '',
                'reason' => $invitation['reason_name'] ?? '',
                'photo_url' => $photo !== '' ? base_url('uploads/' . ltrim($photo, '/')) : null,
            ],
            'host' => [
                'name' => $hostUser['full_name'] ?? ($invitation['invited_by'] ?? ''),
                'contact' => $hostUser['contact_no'] ?? ($invitation['host_contact'] ?? ''),
            ],
            'visit' => [
                'invitation_id' => (int) $invitation['id'],
                'status' => $invitation['status'],
                'safety_video_completed' => ! empty($invitation['video_watched']),
                'presence_status' => $presence,
                'qr_expires_at' => $credential['expires_at'],
            ],
        ]);
    }
}
