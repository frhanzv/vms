<?php

namespace App\Controllers;

use App\Models\VideoModel;
use App\Models\InvitationModel;
use App\Models\ClientFeatureModel;
use App\Libraries\InvitationProcessFlowService;
use App\Services\InvitationApprovalService;

class SecurityBriefing extends BaseController
{
    protected $videoModel;
    protected $invitationModel;
    protected InvitationProcessFlowService $invitationProcessFlowService;
    protected InvitationApprovalService $invitationApprovalService;

    public function __construct()
    {
        $this->videoModel = new VideoModel();
        $this->invitationModel = new InvitationModel();
        $this->invitationProcessFlowService = new InvitationProcessFlowService();
        $this->invitationApprovalService = new InvitationApprovalService();
    }

    public function index()
    {
        // Get invitation token from URL parameter
        $token = $this->request->getGet('token');
        $flowStep = $this->invitationProcessFlowService->resolveFlowStepForRoute(
            'security/briefing',
            $this->request->getGet('flow_step')
        );

        $invitation = null;
        $invitationId = base64_decode((string) $token, true);
        if ($invitationId !== false && ctype_digit((string) $invitationId)) {
            $invitation = $this->invitationModel->find((int) $invitationId);
        }

        $clientId = (int) ($invitation['client_id'] ?? $invitation['company_id'] ?? 0);
        $auto = $clientId > 0 && (new ClientFeatureModel())->isEnabled($clientId, 'auto_approve_after_workflow');
        if (! $invitation || ! in_array($invitation['status'], ['Submitted', 'Approved'], true)
            || (! $auto && $invitation['status'] !== 'Approved')) {
            return redirect()->to(base_url('security/checkin?token=' . urlencode((string) $token)));
        }
        $activeVideo = $this->videoModel->getActiveVideoForClient($clientId);
        
        $data = [
            'pageTitle' => 'Security & Safety Briefing - SafeG',
            'token' => $token,
            'flow_step' => $flowStep,
            'visitor_name' => $invitation['full_name'] ?? 'Visitor',
            'company' => $invitation['company'] ?? '',
            'visit_date' => date('d/m/Y'),
            'briefing_video_url' => $activeVideo ? base_url($activeVideo['file_path']) : null,
            'video_duration' => 300, // Duration in seconds (5 minutes)
            'video_available' => !empty($activeVideo)
        ];

        return view('security/briefing', $data);
    }

    public function validateCompletion()
    {
        try {
            $json = $this->request->getJSON(true);
            $token = is_array($json) ? (string) ($json['token'] ?? '') : '';
            $id = base64_decode($token, true);
            if ($id === false || ! ctype_digit($id) || ! ($invitation = $this->invitationModel->find((int) $id))) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid invitation']);
            }
            $clientId = (int) (($invitation['client_id'] ?? 0) ?: ($invitation['company_id'] ?? 0));
            $auto = $clientId > 0 && (new ClientFeatureModel())->isEnabled($clientId, 'auto_approve_after_workflow');
            if (! in_array($invitation['status'], ['Submitted', 'Approved'], true)
                || (! $auto && $invitation['status'] !== 'Approved')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Please wait for your visit to be approved before completing the safety briefing.']);
            }
            $watched = $json['watched_duration'] ?? null;
            $duration = $json['video_duration'] ?? null;
            if (($json['acknowledged'] ?? false) !== true || ! is_numeric($watched) || ! is_numeric($duration) || ! is_finite((float) $watched)
                || ! is_finite((float) $duration) || $duration <= 0 || $watched < 0 || $watched / $duration < 0.9) {
                return $this->response->setJSON(['success' => false, 'message' => 'Please watch the entire video to proceed']);
            }
            $db = \Config\Database::connect();
            if (empty($invitation['video_watched'])) {
                $db->table('invitations')->where('id', (int) $id)->where('status', $invitation['status'])
                    ->update(['video_watched' => 1, 'video_watched_at' => date('Y-m-d H:i:s'),
                        'video_completion_percentage' => min(100, round($watched / $duration * 100, 2)),
                        'updated_at' => date('Y-m-d H:i:s')]);
            }
            if ($auto && $invitation['status'] === 'Submitted') {
                $result = $this->invitationApprovalService->approve((int) $id, false);
                if (empty($result['success'])) {
                    return $this->response->setJSON($result);
                }
            }
            $result = (new \App\Services\InvitationQrDeliveryService())->deliver((int) $id);
            if (! empty($result['notification_sent'])) {
                $result['redirect_url'] = base_url('security/completed?token=' . urlencode($token));
            } elseif (! empty($result['success'])) {
                // Keep the visitor here so a failed email can be retried.
                $result['success'] = false;
            }
            return $this->response->setJSON($result);
        } catch (\Throwable $e) {
            log_message('error', 'Briefing completion failed: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Unable to complete the briefing. Please retry or contact reception.']);
        }
    }
    public function facialVerification()
    {
        $token = $this->request->getGet('token');
        $flowStep = $this->invitationProcessFlowService->resolveFlowStepForRoute(
            'security/facial-verification',
            $this->request->getGet('flow_step')
        );

        $data = [
            'pageTitle' => 'Facial Verification - SafeG',
            'token' => $token,
            'flow_step' => $flowStep,
        ];

        return view('security/FacialRecognition', $data);
    }

    public function facialComplete()
    {
        $json = $this->request->getJSON();
        $token = $json->token ?? '';
        $imageData = $json->image ?? '';
        $flowStepRaw = $json->flow_step ?? '';

        $currentFacialStep = $this->invitationProcessFlowService->resolveFlowStepForRoute(
            'security/facial-verification',
            is_string($flowStepRaw) ? $flowStepRaw : null
        );

        try {
            if ($token && $imageData) {
                $invitationId = base64_decode($token);
                $invitation = $this->invitationModel->find($invitationId);

                if (!$invitation) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid invitation'
                    ]);
                }

                // Idempotent: if already verified, just redirect
                if (!empty($invitation['facial_verified_at'])) {
                    $nextUrl = $this->invitationProcessFlowService->getNextStepUrl($currentFacialStep, $token)
                        ?? base_url('security/completed?token=' . urlencode((string) $token));

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Facial verification was already completed',
                        'redirect_url' => $nextUrl
                    ]);
                }

                $imageData = str_replace('data:image/png;base64,', '', $imageData);
                $imageData = str_replace(' ', '+', $imageData);
                $decodedImage = base64_decode($imageData);
                
                $uploadPath = WRITEPATH . 'uploads/facial/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $filename = 'facial_' . $invitationId . '_' . time() . '.png';
                $filePath = $uploadPath . $filename;
                
                file_put_contents($filePath, $decodedImage);
                
                // Atomic: only update if not yet verified
                $db = \Config\Database::connect();
                $db->table('invitations')
                    ->where('id', $invitationId)
                    ->where('facial_verified_at IS NULL')
                    ->update([
                        'facial_verification_image' => 'facial/' . $filename,
                        'facial_verified_at' => date('Y-m-d H:i:s'),
                        'version' => ($invitation['version'] ?? 1) + 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
            
            $nextUrl = $this->invitationProcessFlowService->getNextStepUrl($currentFacialStep, $token)
                ?? base_url('security/completed?token=' . urlencode((string) $token));

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Facial verification completed successfully',
                'redirect_url' => $nextUrl
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Facial verification error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save facial verification: ' . $e->getMessage()
            ]);
        }
    }

    public function completed()
    {
        $token = $this->request->getGet('token');
        $autoMode = false;

        if (is_string($token) && $token !== '') {
            $decodedId = base64_decode($token, true);
            if ($decodedId !== false && ctype_digit($decodedId)) {
                $invitation = $this->invitationModel->find((int) $decodedId);
                if ($invitation) {
                    $clientId = (int) ($invitation['client_id'] ?? 0);
                    if ($clientId <= 0) {
                        $clientId = (int) ($invitation['company_id'] ?? 0);
                    }

                    $autoMode = ($invitation['status'] ?? '') === 'Approved'
                        && ! empty($invitation['video_watched'])
                        && \Config\Database::connect()->table('invitation_qr_deliveries')->where('invitation_id', (int) $decodedId)->where('status', 'sent')->countAllResults() > 0;
                }
            }
        }
        
        $data = [
            'pageTitle' => 'Registration Complete - SafeG',
            'token' => $token,
            'auto_mode' => $autoMode,
        ];

        return view('security/completed', $data);
    }

    public function checkin()
    {
        $token = $this->request->getGet('token');
        $approvalStep = $this->invitationProcessFlowService->resolveFlowStepForRoute(
            'security/checkin',
            $this->request->getGet('flow_step')
        );

        $id = base64_decode((string) $token, true);
        $invitation = $id !== false && ctype_digit($id) ? $this->invitationModel->find((int) $id) : null;
        $nextAfterApproval = ($invitation['status'] ?? '') === 'Approved'
            ? base_url('security/briefing?token=' . urlencode((string) $token))
            : null;
        $data = [
            'pageTitle' => 'Approval & Check-in - SafeG',
            'token' => $token,
            'next_after_approval_url' => $nextAfterApproval,
        ];

        return view('security/checkin', $data);
    }

    public function confirmCheckin()
    {
        // Process final check-in
        $token = $this->request->getPost('token');
        $acknowledged = $this->request->getPost('acknowledged');
        
        if ($acknowledged === 'true') {
            // TODO: Update database to mark visitor as checked in
            // Generate visitor badge/pass
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Check-in successful! Please proceed to reception.',
                'redirect_url' => base_url('security/badge?token=' . $token)
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must acknowledge the safety protocols to proceed'
            ]);
        }
    }
}
