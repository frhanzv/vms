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
        
        // Get today's date and now
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // Expected Today: approved invitations whose schedule covers today
        $expectedToday = $db->table('invitations i')
            ->join('invitation_schedules s', 's.invitation_id = i.id')
            ->where('i.status', 'Approved')
            ->where('DATE(s.date_from) <=', $today)
            ->where('DATE(s.date_to) >=', $today)
            ->countAllResults();
            
        // Yesterday's expected count for trend
        $expectedYesterday = $db->table('invitations i')
            ->join('invitation_schedules s', 's.invitation_id = i.id')
            ->where('i.status', 'Approved')
            ->where('DATE(s.date_from) <=', $yesterday)
            ->where('DATE(s.date_to) >=', $yesterday)
            ->countAllResults();
        
        $trend = $expectedToday - $expectedYesterday;
            
        // Currently On-Site: checked in but not checked out
        $currentlyOnSite = $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->where('i.status', 'Approved')
            ->where('iv.check_in_time IS NOT NULL')
            ->where('iv.check_out_time IS NULL')
            ->countAllResults();
            
        // Checked Out today
        $checkedOut = $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->where('DATE(iv.check_out_time)', $today)
            ->countAllResults();
        
        // Out of Window: visitors still on-site but their schedule end time has passed
        $outOfWindow = $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->join('invitation_schedules s', 's.invitation_id = i.id')
            ->where('iv.check_in_time IS NOT NULL')
            ->where('iv.check_out_time IS NULL')
            ->where('s.date_to <', $now)
            ->countAllResults();
        
        // Get visitors expected today OR currently on-site (checked in but not checked out)
        $visitorsQuery = "SELECT i.*, 
                                 iv.check_in_time,
                                 iv.check_out_time,
                                 u.full_name as host_name,
                                 s.date_from,
                                 s.date_to
                          FROM invitations i
                          JOIN invitation_schedules s ON s.invitation_id = i.id
                          LEFT JOIN invitation_visitors iv ON iv.invitation_id = i.id
                          LEFT JOIN users u ON u.id = i.invited_by
                          WHERE i.status = 'Approved'
                          AND (
                              (DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?)
                              OR (iv.check_in_time IS NOT NULL AND iv.check_out_time IS NULL)
                          )
                          ORDER BY s.date_from ASC
                          LIMIT 10";
        
        $visitorsData = $db->query($visitorsQuery, [$today, $today])->getResultArray();
        
        $visitors = [];
        $tabCounts = ['all' => 0, 'preArrival' => 0, 'checkedIn' => 0, 'checkedOut' => 0];
        
        // Count all visitors for tabs (not limited to 10)
        $allVisitorsQuery = "SELECT iv.check_in_time, iv.check_out_time
                             FROM invitations i
                             JOIN invitation_schedules s ON s.invitation_id = i.id
                             LEFT JOIN invitation_visitors iv ON iv.invitation_id = i.id
                             WHERE i.status = 'Approved'
                             AND (
                                 (DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?)
                                 OR (iv.check_in_time IS NOT NULL AND iv.check_out_time IS NULL)
                             )";
        $allVisitorsData = $db->query($allVisitorsQuery, [$today, $today])->getResultArray();
        
        foreach ($allVisitorsData as $v) {
            $tabCounts['all']++;
            if (!empty($v['check_in_time'])) {
                if (!empty($v['check_out_time'])) {
                    $tabCounts['checkedOut']++;
                } else {
                    $tabCounts['checkedIn']++;
                }
            } else {
                $tabCounts['preArrival']++;
            }
        }
        
        foreach ($visitorsData as $visitor) {
            $status = 'Pre-Arrival';
            $statusClass = 'amber';
            
            if (!empty($visitor['check_in_time'])) {
                if (!empty($visitor['check_out_time'])) {
                    $status = 'Checked Out';
                    $statusClass = 'slate';
                } else {
                    $status = 'On-Site';
                    $statusClass = 'green';
                }
            }
            
            $visitors[] = [
                'name' => $visitor['full_name'] ?? 'N/A',
                'email' => $visitor['visitor_email'] ?? '',
                'company' => $visitor['company'] ?? 'N/A',
                'host' => $visitor['host_name'] ?? 'N/A',
                'time' => date('h:i A', strtotime($visitor['date_from'])),
                'status' => $status,
                'statusClass' => $statusClass,
                'hasImage' => false,
                'initials' => strtoupper(substr($visitor['full_name'] ?? 'NA', 0, 2))
            ];
        }
        
        // Build occupancy chart data from real check-in times today
        $occupancySlots = [
            '8am'  => ['hour_start' => 8,  'hour_end' => 10, 'count' => 0],
            '10am' => ['hour_start' => 10, 'hour_end' => 12, 'count' => 0],
            '12pm' => ['hour_start' => 12, 'hour_end' => 14, 'count' => 0],
            '2pm'  => ['hour_start' => 14, 'hour_end' => 16, 'count' => 0],
            '4pm'  => ['hour_start' => 16, 'hour_end' => 18, 'count' => 0],
            '6pm'  => ['hour_start' => 18, 'hour_end' => 20, 'count' => 0],
        ];
        
        $checkInsToday = $db->query(
            "SELECT HOUR(iv.check_in_time) as check_hour
             FROM invitation_visitors iv
             JOIN invitations i ON i.id = iv.invitation_id
             WHERE iv.check_in_time IS NOT NULL
             AND DATE(iv.check_in_time) = ?",
            [$today]
        )->getResultArray();
        
        $maxOccupancy = 1;
        foreach ($checkInsToday as $ci) {
            $hour = (int)$ci['check_hour'];
            foreach ($occupancySlots as $label => &$slot) {
                if ($hour >= $slot['hour_start'] && $hour < $slot['hour_end']) {
                    $slot['count']++;
                    break;
                }
            }
        }
        unset($slot);
        
        $occupancyChart = [];
        foreach ($occupancySlots as $label => $slot) {
            if ($slot['count'] > $maxOccupancy) {
                $maxOccupancy = $slot['count'];
            }
        }
        
        $peakLabel = '';
        $peakCount = 0;
        foreach ($occupancySlots as $label => $slot) {
            if ($slot['count'] > $peakCount) {
                $peakCount = $slot['count'];
                $peakLabel = $label;
            }
        }
        
        foreach ($occupancySlots as $label => $slot) {
            $percentage = $maxOccupancy > 0 ? round(($slot['count'] / $maxOccupancy) * 100) : 0;
            $occupancyChart[] = [
                'label' => $label,
                'count' => $slot['count'],
                'percentage' => $percentage,
                'isPeak' => ($label === $peakLabel && $peakCount > 0),
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
        
        // Get unique companies for filter
        $companies = $db->query(
            "SELECT DISTINCT i.company FROM invitations i
             JOIN invitation_schedules s ON s.invitation_id = i.id
             WHERE i.status = 'Approved'
             AND i.company IS NOT NULL AND i.company != ''
             AND DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?
             ORDER BY i.company ASC",
            [$today, $today]
        )->getResultArray();
        $companyList = array_column($companies, 'company');
        
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
                'outOfWindow' => $outOfWindow
            ],
            'trend' => $trend,
            'visitors' => $visitors,
            'tabCounts' => $tabCounts,
            'occupancyChart' => $occupancyChart,
            'companyList' => $companyList,
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
