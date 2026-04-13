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
        
        // Expected Today: approved invitations with at least one schedule slice overlapping today (no double-count, no “gap” days between disjoint visits)
        $expectedToday = (int) ($db->query(
            'SELECT COUNT(*) AS c
             FROM invitations i
             WHERE i.status = ?
             AND EXISTS (
                 SELECT 1 FROM invitation_schedules s
                 WHERE s.invitation_id = i.id
                 AND DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?
             )',
            ['Approved', $today, $today]
        )->getRow()->c ?? 0);

        // Yesterday's expected count for trend
        $expectedYesterday = (int) ($db->query(
            'SELECT COUNT(*) AS c
             FROM invitations i
             WHERE i.status = ?
             AND EXISTS (
                 SELECT 1 FROM invitation_schedules s
                 WHERE s.invitation_id = i.id
                 AND DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?
             )',
            ['Approved', $yesterday, $yesterday]
        )->getRow()->c ?? 0);
        
        $trend = $expectedToday - $expectedYesterday;
            
        // Currently On-Site: checked in but not checked out
        $currentlyOnSite = $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->where('i.status', 'Approved')
            ->where('iv.check_in_time IS NOT NULL')
            ->where('iv.check_out_time IS NULL')
            ->countAllResults();
            
        // Checked Out today (approved invitations only)
        $checkedOut = $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->where('i.status', 'Approved')
            ->where('DATE(iv.check_out_time)', $today)
            ->countAllResults();

        // Out of Window: on-site visitors whose latest schedule end has passed (avoids duplicate rows from multiple schedule rows)
        $outOfWindow = (int) ($db->query(
            'SELECT COUNT(*) AS c
             FROM invitation_visitors iv
             INNER JOIN invitations i ON i.id = iv.invitation_id
             WHERE i.status = ?
             AND iv.check_in_time IS NOT NULL
             AND iv.check_out_time IS NULL
             AND (
                 SELECT MAX(s.date_to) FROM invitation_schedules s WHERE s.invitation_id = i.id
             ) < ?',
            ['Approved', $now]
        )->getRow()->c ?? 0);
        
        // One row per invitation: only schedules overlapping today (aggregated), plus on-site without a “today” slice
        $visitorsQuery = "SELECT i.*,
                                 iv.check_in_time,
                                 iv.check_out_time,
                                 u.full_name as host_name,
                                 COALESCE(slot.date_from, fs.date_from) AS date_from,
                                 COALESCE(slot.date_to, fs.date_to) AS date_to
                          FROM invitations i
                          LEFT JOIN (
                              SELECT invitation_id,
                                     MIN(date_from) AS date_from,
                                     MAX(date_to) AS date_to
                              FROM invitation_schedules
                              WHERE DATE(date_from) <= ? AND DATE(date_to) >= ?
                              GROUP BY invitation_id
                          ) slot ON slot.invitation_id = i.id
                          LEFT JOIN (
                              SELECT s1.invitation_id, s1.date_from, s1.date_to
                              FROM invitation_schedules s1
                              INNER JOIN (
                                  SELECT invitation_id, MIN(id) AS pick_id
                                  FROM invitation_schedules
                                  GROUP BY invitation_id
                              ) sp ON sp.pick_id = s1.id
                          ) fs ON fs.invitation_id = i.id
                          LEFT JOIN invitation_visitors iv ON iv.invitation_id = i.id
                          LEFT JOIN users u ON u.id = i.invited_by
                          WHERE i.status = 'Approved'
                          AND (
                              slot.invitation_id IS NOT NULL
                              OR (iv.check_in_time IS NOT NULL AND iv.check_out_time IS NULL)
                          )
                          ORDER BY COALESCE(slot.date_from, iv.check_in_time, fs.date_from) ASC
                          LIMIT 10";
        
        $visitorsData = $db->query($visitorsQuery, [$today, $today])->getResultArray();
        
        $visitors = [];
        $tabCounts = ['all' => 0, 'preArrival' => 0, 'checkedIn' => 0, 'checkedOut' => 0];
        
        // Count all visitors for tabs (same inclusion rules as the table above)
        $allVisitorsQuery = "SELECT iv.check_in_time, iv.check_out_time
                             FROM invitations i
                             LEFT JOIN (
                                 SELECT invitation_id,
                                        MIN(date_from) AS date_from,
                                        MAX(date_to) AS date_to
                                 FROM invitation_schedules
                                 WHERE DATE(date_from) <= ? AND DATE(date_to) >= ?
                                 GROUP BY invitation_id
                             ) slot ON slot.invitation_id = i.id
                             LEFT JOIN invitation_visitors iv ON iv.invitation_id = i.id
                             WHERE i.status = 'Approved'
                             AND (
                                 slot.invitation_id IS NOT NULL
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
             WHERE i.status = 'Approved'
             AND iv.check_in_time IS NOT NULL
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
        
        // Recent activity: rolling window (not “today only”, which is often empty early in the day)
        $activitySince = date('Y-m-d H:i:s', strtotime('-7 days'));
        $activityQuery = "SELECT * FROM (
                          SELECT 'check_in' AS type, i.full_name, iv.check_in_time AS time, 'Lobby' AS location
                          FROM invitation_visitors iv
                          JOIN invitations i ON i.id = iv.invitation_id
                          WHERE iv.check_in_time IS NOT NULL
                          AND iv.check_in_time >= ?
                          UNION ALL
                          SELECT 'check_out' AS type, i.full_name, iv.check_out_time AS time, 'Exit' AS location
                          FROM invitation_visitors iv
                          JOIN invitations i ON i.id = iv.invitation_id
                          WHERE iv.check_out_time IS NOT NULL
                          AND iv.check_out_time >= ?
                          UNION ALL
                          SELECT 'created' AS type, i.full_name, i.created_at AS time, COALESCE(u.full_name, 'System') AS location
                          FROM invitations i
                          LEFT JOIN users u ON u.id = i.invited_by
                          WHERE i.status = 'Approved'
                          AND i.created_at >= ?
                          ) act
                          ORDER BY act.time DESC
                          LIMIT 20";

        $activityData = $db->query($activityQuery, [$activitySince, $activitySince, $activitySince])->getResultArray();
        
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
        
        // ===== NEW SECTIONS DATA =====
        
        // 1. Critical Security Alerts (unacknowledged, high/critical severity)
        $criticalAlerts = [];
        if ($db->tableExists('security_alerts')) {
            $criticalAlertsData = $db->query(
                "SELECT * FROM security_alerts 
                 WHERE is_acknowledged = 0 
                 AND severity IN ('high', 'critical')
                 ORDER BY created_at DESC
                 LIMIT 5"
            )->getResultArray();
            
            foreach ($criticalAlertsData as $alert) {
                $criticalAlerts[] = [
                    'id' => $alert['id'],
                    'incident_type' => $alert['incident_type'],
                    'severity' => $alert['severity'],
                    'location' => $alert['location'] ?? 'Unknown',
                    'description' => $alert['description'] ?? '',
                    'visitor_name' => $alert['visitor_name'] ?? '',
                    'time' => !empty($alert['created_at']) ? date('Y-m-d h:i A', strtotime($alert['created_at'])) : 'N/A',
                    'time_ago' => !empty($alert['created_at']) ? $this->getTimeAgo($alert['created_at']) : 'N/A',
                ];
            }
        }
        
        // 2. Recent Alerts Summary: Access Denied (last 24h) & Visitor Overstay (active)
        $accessDeniedCount = 0;
        $overstayCount = 0;
        $activeSecurityAlertCount = 0;
        
        if ($db->tableExists('security_alerts')) {
            $since24h = date('Y-m-d H:i:s', strtotime('-24 hours'));

            // Access-related denials (incident_type strings vary by integration)
            $accessDeniedCount = (int) ($db->query(
                "SELECT COUNT(*) AS c FROM security_alerts
                 WHERE created_at >= ?
                 AND (
                     LOWER(incident_type) LIKE '%access%denied%'
                     OR LOWER(incident_type) LIKE '%unauthorized%access%'
                     OR LOWER(incident_type) LIKE '%access%refused%'
                 )",
                [$since24h]
            )->getRow()->c ?? 0);

            // Visitor overstay from alerts (unacknowledged; match flexible wording)
            $overstayCount = (int) ($db->query(
                "SELECT COUNT(*) AS c FROM security_alerts
                 WHERE is_acknowledged = 0
                 AND LOWER(incident_type) LIKE '%overstay%'",
                []
            )->getRow()->c ?? 0);
            
            // Total active security alerts
            $activeSecurityAlertCount = $db->table('security_alerts')
                ->where('is_acknowledged', 0)
                ->countAllResults();
        }
        
        // Physical overstays (on-site past window) vs alert rows — show the higher so the card reflects real risk
        $overstayCount = max($overstayCount, $outOfWindow);
        
        // 3. Currently On-Site visitors table (detailed, for the new section)
        $onSiteVisitorsQuery = "SELECT iv.id as visitor_id,
                                       COALESCE(iv.full_name, i.full_name) as visitor_name,
                                       COALESCE(u.full_name, 'N/A') as host_name,
                                       iv.check_in_time,
                                       COALESCE(i.location, 'N/A') as location
                                FROM invitation_visitors iv
                                JOIN invitations i ON i.id = iv.invitation_id
                                LEFT JOIN users u ON u.id = i.invited_by
                                WHERE i.status = 'Approved'
                                AND iv.check_in_time IS NOT NULL
                                AND iv.check_out_time IS NULL
                                ORDER BY iv.check_in_time DESC
                                LIMIT 50";
        $onSiteVisitorsData = $db->query($onSiteVisitorsQuery)->getResultArray();
        
        $onSiteVisitors = [];
        foreach ($onSiteVisitorsData as $v) {
            $onSiteVisitors[] = [
                'name' => $v['visitor_name'] ?? 'N/A',
                'host' => $v['host_name'] ?? 'N/A',
                'check_in_time' => !empty($v['check_in_time']) ? date('h:i A', strtotime($v['check_in_time'])) : 'N/A',
                'location' => $v['location'] ?? 'N/A',
            ];
        }
        
        // Upcoming: next schedule slots strictly after now (includes later today); excludes past-only visits
        $upcomingAppointmentsQuery = "SELECT i.full_name as visitor_name,
                                             u.full_name as host_name,
                                             s.date_from, s.date_to, i.reason
                                      FROM invitations i
                                      JOIN invitation_schedules s ON s.invitation_id = i.id
                                      LEFT JOIN users u ON u.id = i.invited_by
                                      WHERE i.status = 'Approved'
                                      AND s.date_from > ?
                                      AND DATE(s.date_to) >= ?
                                      ORDER BY s.date_from ASC
                                      LIMIT 10";
        $upcomingAppointmentsData = $db->query($upcomingAppointmentsQuery, [$now, $today])->getResultArray();
        
        $upcomingAppointments = [];
        foreach ($upcomingAppointmentsData as $appt) {
            $upcomingAppointments[] = [
                'visitor_name' => $appt['visitor_name'] ?? 'N/A',
                'host_name' => $appt['host_name'] ?? 'N/A',
                'time' => date('h:i A', strtotime($appt['date_from'])),
                'date' => date('M d, Y', strtotime($appt['date_from'])),
                'reason' => $appt['reason'] ?? 'Visit',
            ];
        }
        
        // Today's appointments: each schedule slice overlapping today; one visitor row per invitation (latest iv)
        $todayAppointmentsQuery = "SELECT i.full_name as visitor_name,
                                          u.full_name as host_name,
                                          s.date_from, s.date_to, i.reason,
                                          iv.check_in_time, iv.check_out_time
                                   FROM invitations i
                                   JOIN invitation_schedules s ON s.invitation_id = i.id
                                   LEFT JOIN users u ON u.id = i.invited_by
                                   LEFT JOIN invitation_visitors iv ON iv.id = (
                                       SELECT iv2.id FROM invitation_visitors iv2
                                       WHERE iv2.invitation_id = i.id
                                       ORDER BY iv2.id DESC
                                       LIMIT 1
                                   )
                                   WHERE i.status = 'Approved'
                                   AND DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?
                                   ORDER BY s.date_from ASC
                                   LIMIT 20";
        $todayAppointmentsData = $db->query($todayAppointmentsQuery, [$today, $today])->getResultArray();
        
        $todayAppointments = [];
        foreach ($todayAppointmentsData as $appt) {
            $apptStatus = 'Scheduled';
            if (!empty($appt['check_in_time'])) {
                if (!empty($appt['check_out_time'])) {
                    $apptStatus = 'Completed';
                } else {
                    $apptStatus = 'In Progress';
                }
            }
            
            $todayAppointments[] = [
                'visitor_name' => $appt['visitor_name'] ?? 'N/A',
                'host_name' => $appt['host_name'] ?? 'N/A',
                'time' => date('h:i A', strtotime($appt['date_from'])),
                'end_time' => date('h:i A', strtotime($appt['date_to'])),
                'reason' => $appt['reason'] ?? 'Visit',
                'status' => $apptStatus,
            ];
        }
        
        // Visitor traffic: check-ins today (approved invitations only)
        $trafficQuery = "SELECT HOUR(iv.check_in_time) AS hour, COUNT(*) AS count
                         FROM invitation_visitors iv
                         JOIN invitations i ON i.id = iv.invitation_id
                         WHERE i.status = 'Approved'
                         AND iv.check_in_time IS NOT NULL
                         AND DATE(iv.check_in_time) = ?
                         GROUP BY HOUR(iv.check_in_time)
                         ORDER BY hour ASC";
        $trafficData = $db->query($trafficQuery, [$today])->getResultArray();
        
        // Build hourly data for chart (6am to 10pm)
        $trafficHours = [];
        for ($h = 6; $h <= 22; $h++) {
            $found = false;
            foreach ($trafficData as $td) {
                if ((int)$td['hour'] === $h) {
                    $trafficHours[] = ['hour' => $h, 'label' => date('h A', mktime($h, 0, 0)), 'count' => (int)$td['count']];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $trafficHours[] = ['hour' => $h, 'label' => date('h A', mktime($h, 0, 0)), 'count' => 0];
            }
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
                'outOfWindow' => $outOfWindow
            ],
            'trend' => $trend,
            'visitors' => $visitors,
            'tabCounts' => $tabCounts,
            'occupancyChart' => $occupancyChart,
            'companyList' => $companyList,
            'recentActivity' => $recentActivity,
            // New data
            'criticalAlerts' => $criticalAlerts,
            'accessDeniedCount' => $accessDeniedCount,
            'overstayCount' => $overstayCount,
            'activeSecurityAlertCount' => $activeSecurityAlertCount,
            'onSiteVisitors' => $onSiteVisitors,
            'onSiteVisitorCount' => $currentlyOnSite,
            'upcomingAppointments' => $upcomingAppointments,
            'todayAppointments' => $todayAppointments,
            'trafficHours' => $trafficHours,
        ];

        return view('dashboard', $data);
    }
    
    /**
     * Acknowledge a security alert via AJAX
     */
    public function acknowledgeAlert()
    {
        $alertId = $this->request->getPost('alert_id');
        $userId = session()->get('user_id');
        
        if (!$alertId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid alert ID']);
        }
        
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('security_alerts')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Security alerts table not found']);
        }
        
        $db->table('security_alerts')
            ->where('id', $alertId)
            ->update([
                'is_acknowledged' => 1,
                'acknowledged_by' => $userId,
                'acknowledged_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        return $this->response->setJSON(['success' => true, 'message' => 'Alert acknowledged']);
    }
    
    /**
     * Get visitor traffic analytics data via AJAX
     */
    public function trafficData()
    {
        $fromDate = $this->request->getGet('from') ?? date('Y-m-d');
        $toDate = $this->request->getGet('to') ?? date('Y-m-d');
        
        $db = \Config\Database::connect();
        
        $trafficQuery = "SELECT DATE(iv.check_in_time) as date, HOUR(iv.check_in_time) as hour, COUNT(*) as count
                         FROM invitation_visitors iv
                         JOIN invitations i ON i.id = iv.invitation_id
                         WHERE i.status = 'Approved'
                         AND iv.check_in_time IS NOT NULL
                         AND DATE(iv.check_in_time) >= ?
                         AND DATE(iv.check_in_time) <= ?
                         GROUP BY DATE(iv.check_in_time), HOUR(iv.check_in_time)
                         ORDER BY date ASC, hour ASC";
        $trafficData = $db->query($trafficQuery, [$fromDate, $toDate])->getResultArray();
        
        // Build hourly data for chart
        $trafficHours = [];
        for ($h = 6; $h <= 22; $h++) {
            $totalCount = 0;
            foreach ($trafficData as $td) {
                if ((int)$td['hour'] === $h) {
                    $totalCount += (int)$td['count'];
                }
            }
            $trafficHours[] = ['hour' => $h, 'label' => date('h A', mktime($h, 0, 0)), 'count' => $totalCount];
        }
        
        return $this->response->setJSON(['success' => true, 'data' => $trafficHours]);
    }
    
    private function getTimeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 0) {
            return 'just now';
        }

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
