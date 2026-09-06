<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;
use App\Models\ClientFeatureModel;
use App\Models\UserModel;
use App\Models\VisitorTypeModel;
use App\Services\InvitationQrService;
use CodeIgniter\API\ResponseTrait;

class GuardApi extends BaseController
{
    use ResponseTrait;

    private const TOKEN_TTL_SECONDS = 43200; // 12 hours

    public function login(): \CodeIgniter\HTTP\Response
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $username = trim((string) ($body['username'] ?? $body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($username === '' || $password === '') {
            return $this->failValidationErrors('username and password are required');
        }

        $user = (new UserModel())->verifyPassword($username, $password);
        if (! $user) {
            return $this->failUnauthorized('Invalid username or password');
        }

        $role = strtolower((string) ($user['role'] ?? ''));
        $allowedRoles = ['guard', 'security', 'officer', 'admin', 'clientsuperadmin', 'superadmin'];
        if (! in_array($role, $allowedRoles, true)) {
            return $this->failForbidden('This user is not allowed to use the guard app');
        }

        $token = $this->makeToken((int) $user['id']);
        $guard = $this->formatGuard($user);

        return $this->respond([
            'status' => 'success',
            'token'  => $token,
            'guard'  => $guard,
            'data'   => [
                'token' => $token,
                'guard' => $guard,
            ],
        ]);
    }

    public function visitorByQr(string $qrToken = ''): \CodeIgniter\HTTP\Response
    {
        $guard = $this->requireGuard();
        if ($guard instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $guard;
        }

        $qrToken = trim(urldecode($qrToken));
        if ($qrToken === '') {
            $qrToken = trim((string) ($this->request->getGet('qrToken') ?? $this->request->getGet('token') ?? ''));
        }
        if ($qrToken === '') {
            return $this->failValidationErrors('qrToken is required');
        }

        $visitor = $this->findVisitorByQrToken($qrToken);
        if (! $visitor) {
            return $this->failNotFound('Visitor not found for this QR');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $this->formatVisitor($visitor, $qrToken),
        ]);
    }

    public function checkIn(): \CodeIgniter\HTTP\Response
    {
        $guard = $this->requireGuard();
        if ($guard instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $guard;
        }

        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $visitorId = (int) ($body['visitor_id'] ?? $body['id'] ?? 0);
        $qrToken = trim((string) ($body['qr_token'] ?? $body['qrToken'] ?? $body['token'] ?? ''));

        if ($visitorId <= 0 && $qrToken === '') {
            return $this->failValidationErrors('visitor_id or qr_token is required');
        }

        $model = new InvitationModel();
        $visitor = $visitorId > 0 ? $model->find($visitorId) : $this->findVisitorByQrToken($qrToken);
        if (! $visitor) {
            return $this->failNotFound('Visitor not found');
        }

        if (strcasecmp((string) ($visitor['guard_entry_status'] ?? ''), 'Rejected') === 0) {
            return $this->failResourceExists('Entry for this visitor has already been rejected.');
        }

        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $visitorModel = new InvitationVisitorModel();
        $visitorRow = $this->findInvitationVisitorRow((int) $visitor['id']);

        if (! $visitorRow) {
            return $this->failNotFound('Visitor pass record not found');
        }

        $blockedReason = $this->passClosedReason(array_merge($visitor, [
            'checked_in_at' => $visitorRow['check_in_time'] ?? ($visitor['checked_in_at'] ?? null),
            'check_out_time' => $visitorRow['check_out_time'] ?? null,
        ]));
        if ($blockedReason !== null) {
            return $this->failResourceExists($blockedReason);
        }

        $today = date('Y-m-d');
        $checkInTime = $visitorRow['check_in_time'] ?? null;
        $checkedInToday = ! empty($checkInTime) && date('Y-m-d', strtotime((string) $checkInTime)) === $today;
        $action = 'checkin';
        $message = 'Time In recorded';

        if (! $checkedInToday) {
            $db->table('invitations')
                ->where('id', (int) $visitor['id'])
                ->groupStart()
                    ->where('guard_entry_status', 'Expected')
                    ->orWhere('guard_entry_status IS NULL', null, false)
                    ->orWhere('guard_entry_status', 'Approved')
                ->groupEnd()
                ->update([
                    'checked_in_at'          => $now,
                    'status'                 => 'Approved',
                    'guard_entry_status'     => 'Approved',
                    'guard_decided_at'       => $now,
                    'guard_decided_by'       => (int) $guard['id'],
                    'guard_rejection_reason' => null,
                    'updated_at'             => $now,
                ]);

            $visitorModel->where('id', (int) $visitorRow['id'])
                ->set(['check_in_time' => $now, 'check_out_time' => null, 'updated_at' => $now])
                ->update();
        } else {
            $elapsedSeconds = time() - strtotime((string) $checkInTime);
            if ($elapsedSeconds < 600) {
                $updatedVisitor = $model->find((int) $visitor['id']);
                $updatedVisitor['visitor_row_id'] = $visitorRow['id'] ?? null;
                $updatedVisitor['checked_in_at'] = $visitorRow['check_in_time'] ?? null;
                $updatedVisitor['check_out_time'] = $visitorRow['check_out_time'] ?? null;

                return $this->respond([
                    'status'  => 'success',
                    'message' => 'Visitor already timed in. Time Out is available after 10 minutes.',
                    'data'    => $this->formatVisitor($updatedVisitor, $qrToken),
                ]);
            }

            $action = 'checkout';
            $message = 'Time Out recorded';
            $visitorModel->where('id', (int) $visitorRow['id'])
                ->where('check_out_time IS NULL', null, false)
                ->set(['check_out_time' => $now, 'updated_at' => $now])
                ->update();
            if ($db->affectedRows() === 0) {
                return $this->failResourceExists('This visitor has already timed out. This QR can no longer be used for Time In or Time Out.');
            }
        }

        $updated = $model->find((int) $visitor['id']);
        $updatedVisitorRow = $this->findInvitationVisitorRow((int) $visitor['id']) ?? $visitorRow;
        $updated['visitor_row_id'] = $updatedVisitorRow['id'] ?? null;
        $updated['checked_in_at'] = $updatedVisitorRow['check_in_time'] ?? ($updated['checked_in_at'] ?? null);
        $updated['check_out_time'] = $updatedVisitorRow['check_out_time'] ?? null;
        $updated['guard_action'] = $action;

        return $this->respond([
            'status'  => 'success',
            'message' => $message,
            'data'    => $this->formatVisitor($updated, $qrToken),
        ]);
    }

    public function reject(): \CodeIgniter\HTTP\Response
    {
        $guard = $this->requireGuard();
        if ($guard instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $guard;
        }

        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $visitorId = (int) ($body['visitor_id'] ?? $body['id'] ?? 0);
        $qrToken = trim((string) ($body['qr_token'] ?? $body['qrToken'] ?? $body['token'] ?? ''));
        $reason = trim((string) ($body['reason'] ?? $body['rejection_reason'] ?? ''));

        if ($visitorId <= 0 && $qrToken === '') {
            return $this->failValidationErrors('visitor_id or qr_token is required');
        }

        $model = new InvitationModel();
        $visitor = $visitorId > 0 ? $model->find($visitorId) : $this->findVisitorByQrToken($qrToken);
        if (! $visitor) {
            return $this->failNotFound('Visitor not found');
        }

        if (! empty($visitor['checked_in_at'])
            || strcasecmp((string) ($visitor['guard_entry_status'] ?? ''), 'Approved') === 0) {
            return $this->failResourceExists('Entry for this visitor has already been approved.');
        }
        if (strcasecmp((string) ($visitor['guard_entry_status'] ?? ''), 'Rejected') === 0) {
            return $this->failResourceExists('Entry for this visitor has already been rejected.');
        }

        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $db->table('invitations')
            ->where('id', (int) $visitor['id'])
            ->where('checked_in_at IS NULL', null, false)
            ->groupStart()
                ->where('guard_entry_status', 'Expected')
                ->orWhere('guard_entry_status IS NULL', null, false)
            ->groupEnd()
            ->update([
                'guard_entry_status'     => 'Rejected',
                'guard_decided_at'       => $now,
                'guard_decided_by'       => (int) $guard['id'],
                'guard_rejection_reason' => $reason !== '' ? $reason : null,
                'updated_at'             => $now,
            ]);

        if ($db->affectedRows() === 0) {
            return $this->failResourceExists('An entry decision has already been recorded for this visitor.');
        }

        $updated = $model->find((int) $visitor['id']);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Entry rejected',
            'data'    => $this->formatVisitor($updated, $qrToken),
        ]);
    }

    private function requireGuard()
    {
        $token = $this->bearerToken();
        if ($token === '') {
            return $this->failUnauthorized('Bearer token is required');
        }

        $userId = $this->parseToken($token);
        if ($userId <= 0) {
            return $this->failUnauthorized('Invalid or expired token');
        }

        $user = (new UserModel())->where('is_active', 1)->find($userId);
        if (! $user) {
            return $this->failUnauthorized('Guard user is inactive');
        }

        return $user;
    }

    private function bearerToken(): string
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    private function makeToken(int $userId): string
    {
        $expires = time() + self::TOKEN_TTL_SECONDS;
        $payload = $userId . '|' . $expires;
        $signature = hash_hmac('sha256', $payload, $this->tokenSecret());

        return rtrim(strtr(base64_encode($payload . '|' . $signature), '+/', '-_'), '=');
    }

    private function parseToken(string $token): int
    {
        $decoded = base64_decode(strtr($token, '-_', '+/'), true);
        if (! is_string($decoded)) {
            return 0;
        }

        $parts = explode('|', $decoded);
        if (count($parts) !== 3) {
            return 0;
        }

        [$userId, $expires, $signature] = $parts;
        if ((int) $expires < time()) {
            return 0;
        }

        $payload = $userId . '|' . $expires;
        $expected = hash_hmac('sha256', $payload, $this->tokenSecret());

        return hash_equals($expected, $signature) ? (int) $userId : 0;
    }

    private function tokenSecret(): string
    {
        return (string) (env('encryption.key') ?: env('app.baseURL') ?: 'safeg-guard-api');
    }

    private function findVisitorByQrToken(string $qrToken): ?array
    {
        $model = new InvitationModel();

        $credential = (new InvitationQrService())->findValidCredential($qrToken);
        if ($credential) {
            $found = $model->find((int) ($credential['invitation_id'] ?? 0));
            if ($found) {
                return $this->withVisitorPassState($found);
            }
        }

        if (preg_match('/^VIS-(\d+)$/i', $qrToken, $matches)) {
            $found = $model->find((int) $matches[1]);
            if ($found) {
                return $this->withVisitorPassState($found);
            }
        }

        $invitation = $model->groupStart()
                ->where('ic_passport', $qrToken)
                ->orWhere('contact', $qrToken)
                ->orWhere('id', ctype_digit($qrToken) ? (int) $qrToken : 0)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($invitation) {
            return $this->withVisitorPassState($invitation);
        }

        $visitorRow = (new InvitationVisitorModel())
            ->groupStart()
                ->where('ic_passport', $qrToken)
                ->orWhere('contact', $qrToken)
                ->orWhere('id', ctype_digit($qrToken) ? (int) $qrToken : 0)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->first();

        if (! $visitorRow) {
            return null;
        }

        $parent = $model->find((int) ($visitorRow['invitation_id'] ?? 0)) ?? [];

        return array_merge($parent, [
            'id'              => $parent['id'] ?? $visitorRow['invitation_id'],
            'visitor_row_id'  => $visitorRow['id'] ?? null,
            'full_name'       => $visitorRow['full_name'] ?? ($parent['full_name'] ?? ''),
            'ic_passport'     => $visitorRow['ic_passport'] ?? ($parent['ic_passport'] ?? ''),
            'contact'         => $visitorRow['contact'] ?? ($parent['contact'] ?? ''),
            'company'         => $visitorRow['company'] ?? ($parent['company'] ?? ''),
            'checked_in_at'   => $visitorRow['check_in_time'] ?? ($parent['checked_in_at'] ?? null),
            'check_out_time'   => $visitorRow['check_out_time'] ?? null,
        ]);
    }

    private function findInvitationVisitorRow(int $invitationId): ?array
    {
        if ($invitationId <= 0) {
            return null;
        }

        return (new InvitationVisitorModel())
            ->where('invitation_id', $invitationId)
            ->orderBy('id', 'DESC')
            ->first();
    }

    private function withVisitorPassState(array $invitation): array
    {
        $visitorRow = $this->findInvitationVisitorRow((int) ($invitation['id'] ?? 0));
        if (! $visitorRow) {
            return $invitation;
        }

        return array_merge($invitation, [
            'visitor_row_id' => $visitorRow['id'] ?? null,
            'checked_in_at'  => $visitorRow['check_in_time'] ?? ($invitation['checked_in_at'] ?? null),
            'check_out_time' => $visitorRow['check_out_time'] ?? null,
        ]);
    }

    private function formatGuard(array $user): array
    {
        return [
            'id'       => (int) $user['id'],
            'name'     => $user['full_name'] ?? $user['username'] ?? 'Guard',
            'username' => $user['username'] ?? '',
            'email'    => $user['email'] ?? '',
            'role'     => $user['role'] ?? '',
        ];
    }

    private function formatVisitor(array $visitor, string $qrToken): array
    {
        $invitationStatus = (string) ($visitor['status'] ?? '');
        $storedEntryStatus = trim((string) ($visitor['guard_entry_status'] ?? ''));
        $entryStatus = $this->usesGuardEntryDecision($visitor)
            ? ($storedEntryStatus !== '' ? $storedEntryStatus : 'Expected')
            : $invitationStatus;

        $now = time();
        $today = date('Y-m-d');
        $checkInAt = $visitor['checked_in_at'] ?? null;
        $checkOutAt = $visitor['check_out_time'] ?? null;
        $checkedInToday = ! empty($checkInAt) && date('Y-m-d', strtotime((string) $checkInAt)) === $today;
        $canConfirm = true;
        $nextAction = 'checkin';
        $actionLabel = 'Confirm Time In';
        $note = 'After confirmation, Time In will be recorded in History.';

        if ($checkedInToday) {
            $elapsedSeconds = $now - strtotime((string) $checkInAt);
            if ($elapsedSeconds >= 600) {
                $nextAction = 'checkout';
                $actionLabel = 'Confirm Time Out';
                $note = 'This visitor is already timed in. Confirm Time Out to end this visit.';
            } else {
                $remainingMinutes = (int) ceil((600 - $elapsedSeconds) / 60);
                $canConfirm = false;
                $nextAction = 'details';
                $actionLabel = 'Back';
                $note = 'Visitor already timed in. Time Out is available after ' . max(1, $remainingMinutes) . ' minute(s).';
            }
        }

        if (strcasecmp($entryStatus, 'Rejected') === 0) {
            $canConfirm = false;
            $nextAction = 'details';
            $actionLabel = 'Back';
            $note = 'Entry for this visitor has been rejected.';
        }

        $blockedReason = $this->passClosedReason($visitor);
        if ($blockedReason !== null) {
            $canConfirm = false;
            $nextAction = 'details';
            $actionLabel = 'Back';
            $note = $blockedReason;
        }

        return [
            'id'            => (string) $visitor['id'],
            'qr_token'      => $qrToken !== '' ? $qrToken : ($visitor['ic_passport'] ?? (string) $visitor['id']),
            'name'          => $visitor['full_name'] ?? '',
            'full_name'     => $visitor['full_name'] ?? '',
            'visitor_name'  => $visitor['full_name'] ?? '',
            'ic'            => mask_ic_passport($visitor['ic_passport'] ?? '', ''),
            'ic_no'         => mask_ic_passport($visitor['ic_passport'] ?? '', ''),
            'email'         => $visitor['visitor_email'] ?? '',
            'phone'         => $visitor['contact'] ?? '',
            'phone_no'      => $visitor['contact'] ?? '',
            'company'       => $visitor['company'] ?? '',
            'company_name'  => $visitor['company'] ?? '',
            'host_name'     => $visitor['invited_by'] ?? '',
            'host_contact_no' => $visitor['host_contact'] ?? '',
            'host_contact'   => $visitor['host_contact'] ?? '',
            'department'    => $visitor['company_visited'] ?? '',
            'purpose'       => $visitor['reason'] ?? '',
            'visit_purpose' => $visitor['reason'] ?? '',
            'visit_date'    => $visitor['created_at'] ?? '',
            'status'        => $entryStatus,
            'entry_status'  => $entryStatus,
            'invitation_status' => $invitationStatus,
            'rejection_reason' => $visitor['guard_rejection_reason'] ?? null,
            'entry_decided_at' => $visitor['guard_decided_at'] ?? null,
            'visitor_type'  => $this->resolveVisitorType((int) ($visitor['visitor_type_id'] ?? 0)),
            'check_in_at'   => $checkInAt,
            'checked_in_at' => $checkInAt,
            'entry_time'    => $checkInAt,
            'check_out_at'  => $checkOutAt,
            'check_out_time' => $checkOutAt,
            'time_out'      => $checkOutAt,
            'next_action'   => $nextAction,
            'performed_action' => $visitor['guard_action'] ?? '',
            'action_label'  => $actionLabel,
            'can_confirm'   => $canConfirm,
            'note'          => $note,
            'valid_until'   => ! empty($checkInAt) ? date('Y-m-d 23:59:59', strtotime((string) $checkInAt)) : null,
        ];
    }

    private function passClosedReason(array $visitor): ?string
    {
        if (! empty($visitor['check_out_time'])) {
            return 'This visitor has already timed out. This QR can no longer be used for Time In or Time Out.';
        }
        $timeIn = $visitor['checked_in_at'] ?? null;
        if (! empty($timeIn) && date('Y-m-d', strtotime((string) $timeIn)) !== date('Y-m-d')) {
            return 'This QR expired at 11:59 PM on the Time In date. Please register a new visit.';
        }
        return null;
    }

    private function usesGuardEntryDecision(array $visitor): bool
    {
        $clientId = (int) ($visitor['client_id'] ?? 0);

        return $clientId > 0
            && (new ClientFeatureModel())->isEnabled($clientId, 'auto_approve_after_workflow');
    }

    private function resolveVisitorType(int $id): string
    {
        if ($id <= 0) {
            return '';
        }

        $type = (new VisitorTypeModel())->find($id);
        return $type ? ($type['name'] ?? '') : '';
    }
}
