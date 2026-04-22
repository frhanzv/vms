<?php

namespace App\Controllers;

use App\Models\VideoModel;
use App\Models\InvitationModel;
use App\Libraries\InvitationProcessFlowService;

class SecurityBriefing extends BaseController
{
    protected $videoModel;
    protected $invitationModel;
    protected InvitationProcessFlowService $invitationProcessFlowService;

    public function __construct()
    {
        $this->videoModel = new VideoModel();
        $this->invitationModel = new InvitationModel();
        $this->invitationProcessFlowService = new InvitationProcessFlowService();
    }

    public function index()
    {
        // Get invitation token from URL parameter
        $token = $this->request->getGet('token');
        
        // Get active video from database
        $activeVideo = $this->videoModel->getActiveVideo();
        
        // TODO: Validate token and fetch invitation details from database
        // For now, using sample data
        
        $data = [
            'pageTitle' => 'Security & Safety Briefing - SafeG',
            'token' => $token,
            'visitor_name' => 'John Doe', // This would come from database
            'company' => 'ABC Construction Sdn Bhd',
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
            $json = $this->request->getJSON();
            
            if (!$json) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid request data'
                ]);
            }
            
            $token = $json->token ?? '';
            $watchedDuration = $json->watched_duration ?? 0;
            $videoDuration = $json->video_duration ?? 1;
            
            $completionPercentage = ($watchedDuration / $videoDuration) * 100;
            
            if ($completionPercentage >= 90) {
                if ($token) {
                    $invitationId = base64_decode($token);
                    $invitation = $this->invitationModel->find($invitationId);

                    if (!$invitation) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Invalid invitation'
                        ]);
                    }

                    // Idempotent: if already completed, just redirect
                    if (!empty($invitation['video_watched'])) {
                        $nextUrl = $this->invitationProcessFlowService->getNextStepUrl('security_briefing', $token)
                            ?? base_url('security/facial-verification?token=' . $token);

                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'Video briefing was already completed',
                            'redirect_url' => $nextUrl
                        ]);
                    }

                    // Atomic: only update if video_watched is still falsy
                    $db = \Config\Database::connect();
                    $db->table('invitations')
                        ->where('id', $invitationId)
                        ->groupStart()
                            ->where('video_watched', 0)
                            ->orWhere('video_watched IS NULL')
                        ->groupEnd()
                        ->update([
                            'video_watched' => true,
                            'video_watched_at' => date('Y-m-d H:i:s'),
                            'video_completion_percentage' => round($completionPercentage, 2),
                            'version' => ($invitation['version'] ?? 1) + 1,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
                
                $nextUrl = $this->invitationProcessFlowService->getNextStepUrl('security_briefing', $token)
                    ?? base_url('security/facial-verification?token=' . $token);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Briefing completed successfully',
                    'redirect_url' => $nextUrl
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please watch the entire video to proceed',
                    'completion' => round($completionPercentage, 2)
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function facialVerification()
    {
        $token = $this->request->getGet('token');
        
        $data = [
            'pageTitle' => 'Facial Verification - SafeG',
            'token' => $token
        ];

        return view('security/FacialRecognition', $data);
    }

    public function facialComplete()
    {
        $json = $this->request->getJSON();
        $token = $json->token ?? '';
        $imageData = $json->image ?? '';
        
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
                    $nextUrl = $this->invitationProcessFlowService->getNextStepUrl('facial_verification', $token)
                        ?? base_url('security/completed?token=' . $token);

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
            
            $nextUrl = $this->invitationProcessFlowService->getNextStepUrl('facial_verification', $token)
                ?? base_url('security/completed?token=' . $token);

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
        // Final completion page
        $token = $this->request->getGet('token');
        
        $data = [
            'pageTitle' => 'Registration Complete - SafeG',
            'token' => $token
        ];

        return view('security/completed', $data);
    }

    public function checkin()
    {
        // Final check-in page after facial verification
        $token = $this->request->getGet('token');
        
        // TODO: Verify facial verification was completed
        
        $data = [
            'pageTitle' => 'Check-in Confirmation - SafeG',
            'token' => $token
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