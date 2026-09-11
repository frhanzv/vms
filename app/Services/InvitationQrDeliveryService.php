<?php
namespace App\Services;

use CodeIgniter\Database\BaseConnection;

/** Deliver an entry QR only after approval and briefing, once per invitation. */
class InvitationQrDeliveryService
{
    public function __construct(private ?BaseConnection $db = null, private ?\Closure $send = null)
    {
        $this->db ??= \Config\Database::connect();
        $this->send ??= static fn(int $id): bool => (new \App\Libraries\InvitationEmailSender())->sendApproval($id);
    }

    public function deliver(int $id): array
    {
        $invitation = $this->db->table('invitations')->where('id', $id)->get()->getRowArray();
        if (! $invitation || $invitation['status'] !== 'Approved' || empty($invitation['video_watched'])) {
            return ['success' => false, 'message' => 'Approval and safety briefing completion are required before issuing a QR.'];
        }
        $table = 'invitation_qr_deliveries';
        $this->db->table($table)->ignore(true)->insert(['invitation_id' => $id, 'status' => 'pending']);
        // Atomic claim prevents repeated completion requests from rotating or resending the QR.
        $this->db->table($table)->where('invitation_id', $id)->whereIn('status', ['pending', 'failed'])
            ->update(['status' => 'sending', 'updated_at' => date('Y-m-d H:i:s')]);
        if ($this->db->affectedRows() === 0) {
            $row = $this->db->table($table)->where('invitation_id', $id)->get()->getRowArray();
            return ['success' => true, 'notification_sent' => $row['status'] === 'sent',
                'message' => $row['status'] === 'sent' ? 'Your QR email has already been sent.' : 'Your QR email is being processed.'];
        }
        try {
            $sent = ($this->send)($id);
        } catch (\Throwable $e) {
            log_message('error', 'QR delivery failed for invitation ' . $id . ': ' . $e->getMessage());
            $sent = false;
        }
        $this->db->table($table)->where('invitation_id', $id)->update([
            'status' => $sent ? 'sent' : 'failed', 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return ['success' => true, 'notification_sent' => $sent,
            'message' => $sent ? 'Briefing completed. Your QR code has been sent by email.'
                : 'Briefing completed, but the QR email could not be sent. Please retry or contact reception.'];
    }
}
