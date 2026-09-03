<?php

namespace App\Services;

class InvitationQrService
{
    public function issue(int $invitationId, ?int $clientId = null): string
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('invitation_qr_credentials')) {
            throw new \RuntimeException('Invitation QR migration has not been run.');
        }

        $token = $this->generateNumericToken();
        $tokenHash = hash('sha256', $token);
        $now = date('Y-m-d H:i:s');
        $expiresAt = $this->resolveExpiry($invitationId);

        $existing = $db->table('invitation_qr_credentials')
            ->where('invitation_id', $invitationId)
            ->get()->getRowArray();

        $data = [
            'client_id' => $clientId,
            'token_hash' => $tokenHash,
            'token_last4' => substr($token, -4),
            'expires_at' => $expiresAt,
            'revoked_at' => null,
            'updated_at' => $now,
        ];

        if ($existing) {
            $db->table('invitation_qr_credentials')->where('id', $existing['id'])->update($data);
        } else {
            $data['invitation_id'] = $invitationId;
            $data['created_at'] = $now;
            $db->table('invitation_qr_credentials')->insert($data);
        }

        return $token;
    }

    public function findValidCredential(string $token): ?array
    {
        if (! preg_match('/^\d{32}$/', $token)) {
            return null;
        }

        $db = \Config\Database::connect();
        if (! $db->tableExists('invitation_qr_credentials')) {
            return null;
        }

        return $db->table('invitation_qr_credentials')
            ->where('token_hash', hash('sha256', $token))
            ->where('revoked_at IS NULL', null, false)
            ->groupStart()
                ->where('expires_at IS NULL', null, false)
                ->orWhere('expires_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->get()->getRowArray() ?: null;
    }

    private function generateNumericToken(): string
    {
        do {
            $token = '';
            for ($i = 0; $i < 32; $i++) {
                $token .= (string) random_int(0, 9);
            }

            $exists = \Config\Database::connect()
                ->table('invitation_qr_credentials')
                ->where('token_hash', hash('sha256', $token))
                ->countAllResults() > 0;
        } while ($exists);

        return $token;
    }

    private function resolveExpiry(int $invitationId): ?string
    {
        $db = \Config\Database::connect();
        $schedule = $db->table('invitation_schedules')
            ->selectMax('date_to', 'expires_at')
            ->where('invitation_id', $invitationId)
            ->get()->getRowArray();

        $value = trim((string) ($schedule['expires_at'] ?? ''));
        if ($value !== '') {
            return $value;
        }

        $invitation = $db->table('invitations')->select('link_expiry')->where('id', $invitationId)->get()->getRowArray();
        $linkExpiry = trim((string) ($invitation['link_expiry'] ?? ''));

        if ($linkExpiry === '') {
            return null;
        }

        $timestamp = strtotime($linkExpiry);
        if ($timestamp === false) {
            return null;
        }

        return preg_match('/\d{2}:\d{2}/', $linkExpiry)
            ? date('Y-m-d H:i:s', $timestamp)
            : date('Y-m-d 23:59:59', $timestamp);
    }
}
