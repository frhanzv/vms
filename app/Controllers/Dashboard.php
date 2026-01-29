<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Get current user data
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        $currentUser = $userModel->find($userId);
        
        // Get database connection
        $db = \Config\Database::connect();
        $invitationModel = new InvitationModel();
        
        // Get today's date
        $today = date('Y-m-d');
        
        // Stats
        $expectedToday = $invitationModel
            ->where('status', 'Approved')
            ->where('DATE(created_at)', $today)
            ->countAllResults();
            
        $currentlyOnSite = $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->where('i.status', 'Approved')
            ->where('iv.check_in_time IS NOT NULL')
            ->where('iv.check_out_time IS NULL')
            ->countAllResults();
            
        $checkedOut = $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->where('DATE(iv.check_out_time)', $today)
            ->countAllResults();
        
        // Get visitors expected today with their status
        $visitorsQuery = "SELECT i.*, 
                                 iv.check_in_time,
                                 iv.check_out_time,
                                 u.full_name as host_name
                          FROM invitations i
                          LEFT JOIN invitation_visitors iv ON iv.invitation_id = i.id
                          LEFT JOIN users u ON u.id = i.invited_by
                          WHERE i.status = 'Approved'
                          AND DATE(i.created_at) = ?
                          ORDER BY i.created_at DESC
                          LIMIT 10";
        
        $visitorsData = $db->query($visitorsQuery, [$today])->getResultArray();
        
        $visitors = [];
        foreach ($visitorsData as $visitor) {
            $status = 'Pre-Arrival';
            $statusClass = 'amber';
            
            if ($visitor['check_in_time']) {
                if ($visitor['check_out_time']) {
                    $status = 'Checked Out';
                    $statusClass = 'slate';
                } else {
                    $status = 'On-Site';
                    $statusClass = 'green';
                }
            }
            
            $visitors[] = [
                'name' => $visitor['full_name'] ?? 'N/A',
                'email' => $visitor['email'] ?? '',
                'company' => $visitor['company'] ?? 'N/A',
                'host' => $visitor['host_name'] ?? 'N/A',
                'time' => date('h:i A', strtotime($visitor['created_at'])),
                'status' => $status,
                'statusClass' => $statusClass,
                'hasImage' => false,
                'initials' => strtoupper(substr($visitor['full_name'] ?? 'NA', 0, 2))
            ];
        }
        
        // Get recent activity
        $activityQuery = "SELECT 'check_in' as type, i.full_name, iv.check_in_time as time, 'Lobby' as location
                          FROM invitation_visitors iv
                          JOIN invitations i ON i.id = iv.invitation_id
                          WHERE iv.check_in_time IS NOT NULL
                          AND DATE(iv.check_in_time) = ?
                          UNION ALL
                          SELECT 'check_out' as type, i.full_name, iv.check_out_time as time, 'Exit' as location
                          FROM invitation_visitors iv
                          JOIN invitations i ON i.id = iv.invitation_id
                          WHERE iv.check_out_time IS NOT NULL
                          AND DATE(iv.check_out_time) = ?
                          UNION ALL
                          SELECT 'created' as type, i.full_name, i.created_at as time, u.full_name as location
                          FROM invitations i
                          LEFT JOIN users u ON u.id = i.invited_by
                          WHERE i.status = 'Approved'
                          AND DATE(i.created_at) = ?
                          ORDER BY time DESC
                          LIMIT 10";
        
        $activityData = $db->query($activityQuery, [$today, $today, $today])->getResultArray();
        
        $recentActivity = [];
        foreach ($activityData as $activity) {
            $action = '';
            $status = 'pending';
            $location = $activity['location'] ?? 'N/A';
            
            if ($activity['type'] == 'check_in') {
                $action = 'checked in';
                $status = 'online';
            } elseif ($activity['type'] == 'check_out') {
                $action = 'checked out';
                $status = 'offline';
            } else {
                $action = 'invitation created';
                $location = 'by ' . $location;
            }
            
            $timeAgo = $this->getTimeAgo($activity['time']);
            
            $recentActivity[] = [
                'name' => $activity['full_name'] ?? 'N/A',
                'action' => $action,
                'time' => $timeAgo,
                'location' => $location,
                'status' => $status,
                'initials' => strtoupper(substr($activity['full_name'] ?? 'NA', 0, 2))
            ];
        }
        
        $data = [
            'pageTitle' => 'Host Dashboard - SafeG',
            'currentDate' => date('F jS, Y'),
            'userName' => $currentUser['full_name'] ?? session()->get('full_name') ?? 'Admin',
            'userRole' => $currentUser['role'] ?? session()->get('role') ?? 'Admin',
            'userPhoto' => $currentUser['profile_photo'] ?? null,
            'stats' => [
                'expectedToday' => $expectedToday,
                'currentlyOnSite' => $currentlyOnSite,
                'checkedOut' => $checkedOut,
                'outOfWindow' => 0
            ],
            'visitors' => $visitors,
            'recentActivity' => $recentActivity
        ];

        return view('dashboard', $data);
    }
    
    private function getTimeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return $diff . ' sec ago';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' min ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }
    }
}
