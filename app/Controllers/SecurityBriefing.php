<?php

namespace App\Controllers;

use App\Models\VideoModel;

class SecurityBriefing extends BaseController
{
    protected $videoModel;

    public function __construct()
    {
        $this->videoModel = new VideoModel();
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
        // AJAX endpoint to validate video completion
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
            
            // Calculate completion percentage
            $completionPercentage = ($watchedDuration / $videoDuration) * 100;
            
            // Require at least 90% completion
            if ($completionPercentage >= 90) {
                // TODO: Update database to mark briefing as completed
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Briefing completed successfully',
                    'redirect_url' => base_url('security/facial-verification?token=' . $token)
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
        // Handle facial verification completion
        $json = $this->request->getJSON();
        $token = $json->token ?? '';
        
        // TODO: Save facial verification data to database
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Facial verification completed successfully',
            'redirect_url' => base_url('security/completed?token=' . $token)
        ]);
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