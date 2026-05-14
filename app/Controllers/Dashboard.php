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
        
        // Expected Today: approved invitations with at least one schedule slice overlapping today (no double-count, no "gap" days between disjoint visits)
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
        
        // One row per invitation: only schedules overlapping today (aggregated), plus on-site without a "today" slice
        $visitorsQuery = "SELECT i.*,
                                 iv.check_in_time,
                                 iv.check_out_time,
                                 COALESCE(i.invited_by, 'N/A') as host_name,
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
            
            if (!empty($visitor['check_out_time'])) {
                $timeDisplay = date('M j, Y g:i A', strtotime($visitor['check_out_time']));
                $dateRaw = $visitor['check_out_time'];
            } elseif (!empty($visitor['check_in_time'])) {
                $timeDisplay = date('M j, Y g:i A', strtotime($visitor['check_in_time']));
                $dateRaw = $visitor['check_in_time'];
            } else {
                $timeDisplay = date('g:i A', strtotime($visitor['date_from']));
                $dateRaw = $visitor['date_from'];
            }

            $visitors[] = [
                'id' => $visitor['id'] ?? 0,
                'name' => $visitor['full_name'] ?? 'N/A',
                'contact' => $visitor['contact'] ?? '',
                'company' => $visitor['company'] ?? 'N/A',
                'host' => $visitor['host_name'] ?? 'N/A',
                'time' => $timeDisplay,
                'date_raw' => $dateRaw,
                'status' => $status,
                'statusClass' => $statusClass,
                'hasImage' => false,
                'initials' => strtoupper(substr($visitor['full_name'] ?? 'NA', 0, 2))
            ];
        }
        
        // Build occupancy chart data from real check-in times today
        $occupancySlots = [
            '12am' => ['hour_start' => 0,  'hour_end' => 2,  'count' => 0],
            '2am'  => ['hour_start' => 2,  'hour_end' => 4,  'count' => 0],
            '4am'  => ['hour_start' => 4,  'hour_end' => 6,  'count' => 0],
            '6am'  => ['hour_start' => 6,  'hour_end' => 8,  'count' => 0],
            '8am'  => ['hour_start' => 8,  'hour_end' => 10, 'count' => 0],
            '10am' => ['hour_start' => 10, 'hour_end' => 12, 'count' => 0],
            '12pm' => ['hour_start' => 12, 'hour_end' => 14, 'count' => 0],
            '2pm'  => ['hour_start' => 14, 'hour_end' => 16, 'count' => 0],
            '4pm'  => ['hour_start' => 16, 'hour_end' => 18, 'count' => 0],
            '6pm'  => ['hour_start' => 18, 'hour_end' => 20, 'count' => 0],
            '8pm'  => ['hour_start' => 20, 'hour_end' => 22, 'count' => 0],
            '10pm' => ['hour_start' => 22, 'hour_end' => 24, 'count' => 0],
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
        
        // Recent activity: rolling 7-day window covering all transaction types
        $activitySince = date('Y-m-d H:i:s', strtotime('-7 days'));

        $activityParts = [];
        $activityParams = [];

        $activityParts[] = "SELECT 'created' AS type, i.full_name, i.created_at AS time,
               COALESCE(i.invited_by, 'System') AS actor, '' AS extra
               FROM invitations i WHERE i.created_at >= ?";
        $activityParams[] = $activitySince;

        $activityParts[] = "SELECT 'approved' AS type, i.full_name, i.updated_at AS time,
               COALESCE(i.invited_by, 'System') AS actor, '' AS extra
               FROM invitations i WHERE i.status = 'Approved'
               AND i.updated_at >= ? AND i.updated_at > i.created_at";
        $activityParams[] = $activitySince;

        $activityParts[] = "SELECT 'rejected' AS type, i.full_name, i.updated_at AS time,
               COALESCE(i.invited_by, 'System') AS actor, '' AS extra
               FROM invitations i WHERE i.status = 'Rejected'
               AND i.updated_at >= ? AND i.updated_at > i.created_at";
        $activityParams[] = $activitySince;

        $activityParts[] = "SELECT 'check_in' AS type, i.full_name, iv.check_in_time AS time,
               'Lobby' AS actor, '' AS extra
               FROM invitation_visitors iv
               JOIN invitations i ON i.id = iv.invitation_id
               WHERE iv.check_in_time IS NOT NULL AND iv.check_in_time >= ?";
        $activityParams[] = $activitySince;

        $activityParts[] = "SELECT 'check_out' AS type, i.full_name, iv.check_out_time AS time,
               'Exit' AS actor, '' AS extra
               FROM invitation_visitors iv
               JOIN invitations i ON i.id = iv.invitation_id
               WHERE iv.check_out_time IS NOT NULL AND iv.check_out_time >= ?";
        $activityParams[] = $activitySince;

        $activityParts[] = "SELECT 'door_access' AS type,
               COALESCE(i.full_name, 'Unknown Visitor') AS full_name,
               vcl.scanned_at AS time, l.lane AS actor, vcl.action AS extra
               FROM visitor_card_logs vcl
               JOIN lanes l ON l.id = vcl.lane_id
               LEFT JOIN invitations i ON i.id = vcl.invitation_id
               WHERE vcl.scanned_at >= ?";
        $activityParams[] = $activitySince;

        if ($db->tableExists('security_alerts')) {
            $activityParts[] = "SELECT 'security_alert' AS type,
                   COALESCE(sa.visitor_name, 'System') AS full_name,
                   sa.created_at AS time,
                   COALESCE(sa.location, 'Security') AS actor,
                   sa.incident_type AS extra
                   FROM security_alerts sa WHERE sa.created_at >= ?";
            $activityParams[] = $activitySince;
        }

        $activityQuery = 'SELECT * FROM (' . implode(' UNION ALL ', $activityParts) . ') act ORDER BY act.time DESC LIMIT 30';
        $activityData = $db->query($activityQuery, $activityParams)->getResultArray();

        $recentActivity = [];
        foreach ($activityData as $activity) {
            $type   = $activity['type'];
            $actor  = $activity['actor'] ?? '';
            $extra  = $activity['extra'] ?? '';
            $name   = $activity['full_name'] ?? 'N/A';

            $cfg = match ($type) {
                'created' => [
                    'label'       => 'Invitation Created',
                    'description' => 'Invitation created for <span class="font-semibold">' . esc($name) . '</span>',
                    'location'    => 'by ' . esc($actor),
                    'icon'        => 'add_circle',
                    'iconBg'      => 'bg-amber-100 dark:bg-amber-900/30',
                    'iconColor'   => 'text-amber-600 dark:text-amber-400',
                ],
                'approved' => [
                    'label'       => 'Invitation Approved',
                    'description' => 'Invitation approved for <span class="font-semibold">' . esc($name) . '</span>',
                    'location'    => 'by ' . esc($actor),
                    'icon'        => 'check_circle',
                    'iconBg'      => 'bg-green-100 dark:bg-green-900/30',
                    'iconColor'   => 'text-green-600 dark:text-green-400',
                ],
                'rejected' => [
                    'label'       => 'Invitation Rejected',
                    'description' => 'Invitation rejected for <span class="font-semibold">' . esc($name) . '</span>',
                    'location'    => 'by ' . esc($actor),
                    'icon'        => 'cancel',
                    'iconBg'      => 'bg-red-100 dark:bg-red-900/30',
                    'iconColor'   => 'text-red-600 dark:text-red-400',
                ],
                'check_in' => [
                    'label'       => 'Checked In',
                    'description' => '<span class="font-semibold">' . esc($name) . '</span> checked in',
                    'location'    => $actor,
                    'icon'        => 'login',
                    'iconBg'      => 'bg-green-100 dark:bg-green-900/30',
                    'iconColor'   => 'text-green-600 dark:text-green-400',
                ],
                'check_out' => [
                    'label'       => 'Checked Out',
                    'description' => '<span class="font-semibold">' . esc($name) . '</span> checked out',
                    'location'    => $actor,
                    'icon'        => 'logout',
                    'iconBg'      => 'bg-slate-100 dark:bg-slate-800',
                    'iconColor'   => 'text-slate-500 dark:text-slate-400',
                ],
                'door_access' => [
                    'label'       => $extra === 'checkin' ? 'Door Entry' : 'Door Exit',
                    'description' => '<span class="font-semibold">' . esc($name) . '</span> ' . ($extra === 'checkin' ? 'entered via' : 'exited via') . ' ' . esc($actor),
                    'location'    => esc($actor),
                    'icon'        => 'sensor_door',
                    'iconBg'      => 'bg-blue-100 dark:bg-blue-900/30',
                    'iconColor'   => 'text-blue-600 dark:text-blue-400',
                ],
                'security_alert' => [
                    'label'       => 'Security Alert',
                    'description' => 'Alert: <span class="font-semibold">' . esc($extra) . '</span>',
                    'location'    => esc($actor),
                    'icon'        => 'warning',
                    'iconBg'      => 'bg-red-100 dark:bg-red-900/30',
                    'iconColor'   => 'text-red-600 dark:text-red-400',
                ],
                default => [
                    'label'       => ucfirst(str_replace('_', ' ', $type)),
                    'description' => esc($name),
                    'location'    => esc($actor),
                    'icon'        => 'info',
                    'iconBg'      => 'bg-slate-100 dark:bg-slate-800',
                    'iconColor'   => 'text-slate-500 dark:text-slate-400',
                ],
            };

            $recentActivity[] = [
                'type'        => $type,
                'name'        => $name,
                'label'       => $cfg['label'],
                'description' => $cfg['description'],
                'time'        => $this->getTimeAgo($activity['time']),
                'location'    => $cfg['location'],
                'icon'        => $cfg['icon'],
                'iconBg'      => $cfg['iconBg'],
                'iconColor'   => $cfg['iconColor'],
                'initials'    => strtoupper(substr($name, 0, 2)),
            ];
        }
        
        // Build company list from the already-loaded visitors so it always matches the table
        $companyList = array_values(array_unique(array_filter(array_column($visitors, 'company'), fn($c) => $c && $c !== 'N/A')));
        sort($companyList);
        
        // ===== NEW SECTIONS DATA =====
        $widgets = $this->getSecurityAlertWidgets($outOfWindow);
        $criticalAlerts = $widgets['criticalAlerts'];
        $accessDeniedCount = $widgets['accessDeniedCount'];
        $overstayCount = $widgets['overstayCount'];
        $activeSecurityAlertCount = $widgets['activeSecurityAlertCount'];
        
        // 3. Currently On-Site visitors table (detailed, for the new section)
        $onSiteVisitorsQuery = "SELECT iv.id as visitor_id,
                                       COALESCE(i.full_name, iv.full_name) as visitor_name,
                                       i.ic_passport as ic_number,
                                       COALESCE(i.invited_by, 'N/A') as host_name,
                                       iv.check_in_time,
                                       COALESCE(ld.lane, 'N/A') as last_door_entry
                                FROM invitation_visitors iv
                                JOIN invitations i ON i.id = iv.invitation_id
                                LEFT JOIN (
                                    SELECT vcl.visitor_card_id, l.lane
                                    FROM visitor_card_logs vcl
                                    JOIN lanes l ON l.id = vcl.lane_id
                                    WHERE vcl.id IN (
                                        SELECT MAX(vcl2.id)
                                        FROM visitor_card_logs vcl2
                                        GROUP BY vcl2.visitor_card_id
                                    )
                                ) ld ON ld.visitor_card_id = iv.visitor_card_id
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
                'ic_number' => $v['ic_number'] ?? 'N/A',
                'host' => $v['host_name'] ?? 'N/A',
                'check_in_time' => !empty($v['check_in_time']) ? date('M j, Y g:i A', strtotime($v['check_in_time'])) : 'N/A',
                'last_door_entry' => $v['last_door_entry'] ?? 'N/A',
            ];
        }
        
        // Upcoming: next schedule slots strictly after now (includes later today); excludes past-only visits
        $upcomingAppointmentsQuery = "SELECT i.full_name as visitor_name,
                                             COALESCE(i.invited_by, 'N/A') as host_name,
                                             s.date_from, s.date_to, i.reason
                                      FROM invitations i
                                      JOIN invitation_schedules s ON s.invitation_id = i.id
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
                'time' => date('g:i A', strtotime($appt['date_from'])),
                'date' => date('M j, Y', strtotime($appt['date_from'])),
                'reason' => $appt['reason'] ?? 'Visit',
            ];
        }
        
        // Today's appointments: each schedule slice overlapping today; one visitor row per invitation (latest iv)
        $todayAppointmentsQuery = "SELECT i.full_name as visitor_name,
                                          COALESCE(i.invited_by, 'N/A') as host_name,
                                          s.date_from, s.date_to, i.reason,
                                          iv.check_in_time, iv.check_out_time
                                   FROM invitations i
                                   JOIN invitation_schedules s ON s.invitation_id = i.id
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
                'time' => date('g:i A', strtotime($appt['date_from'])),
                'end_time' => date('g:i A', strtotime($appt['date_to'])),
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
        
        // Build hourly data for chart (full 24 hours)
        $trafficHours = [];
        for ($h = 0; $h <= 23; $h++) {
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
            'currentDate' => date('M jS, Y'),
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
     * Acknowledge a security alert via AJAX.
     * Atomic: only acknowledges if not already acknowledged.
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
        
        // Only acknowledge if not already acknowledged
        $db->table('security_alerts')
            ->where('id', $alertId)
            ->where('is_acknowledged', 0)
            ->update([
                'is_acknowledged' => 1,
                'acknowledged_by' => $userId,
                'acknowledged_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        if ($db->affectedRows() === 0) {
            $alert = $db->table('security_alerts')->where('id', $alertId)->get()->getRowArray();
            if (!$alert) {
                return $this->response->setJSON(['success' => false, 'message' => 'Alert not found']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'This alert has already been acknowledged']);
        }

        $now = date('Y-m-d H:i:s');
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
        $widgets = $this->getSecurityAlertWidgets($outOfWindow);

        return $this->response->setJSON(['success' => true, 'message' => 'Alert acknowledged'] + $widgets);
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
        
        // Build hourly data for chart (full 24 hours)
        $trafficHours = [];
        for ($h = 0; $h <= 23; $h++) {
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
    
    /**
     * AJAX: Access Denied Incidents
     */
    public function accessDeniedData()
    {
        $db = \Config\Database::connect();
        $since24h = date('Y-m-d H:i:s', strtotime('-24 hours'));

        $alerts = [];
        if ($db->tableExists('security_alerts')) {
            $alerts = $db->query(
                "SELECT sa.id, sa.incident_type, sa.severity, sa.visitor_name,
                        sa.location, sa.description, sa.is_acknowledged,
                        sa.created_at, sa.acknowledged_at,
                        u.full_name as acknowledged_by_name
                 FROM security_alerts sa
                 LEFT JOIN users u ON u.id = sa.acknowledged_by
                 WHERE sa.created_at >= ?
                 AND sa.is_acknowledged = 1
                 AND (
                     LOWER(sa.incident_type) LIKE '%access%denied%'
                     OR LOWER(sa.incident_type) LIKE '%unauthorized%access%'
                     OR LOWER(sa.incident_type) LIKE '%access%refused%'
                 )
                 ORDER BY sa.acknowledged_at DESC, sa.created_at DESC",
                [$since24h]
            )->getResultArray();
        }

        return $this->response->setJSON(['success' => true, 'data' => $alerts]);
    }

    /**
     * AJAX: Visitor Overstay Alerts
     */
    public function overstayData()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $alertRows = [];
        if ($db->tableExists('security_alerts')) {
            $alertRows = $db->query(
                "SELECT sa.id, sa.incident_type, sa.severity, sa.visitor_name,
                        sa.location, sa.description, sa.created_at
                 FROM security_alerts sa
                 WHERE sa.is_acknowledged = 0
                 AND LOWER(sa.incident_type) LIKE '%overstay%'
                 ORDER BY sa.created_at DESC"
            )->getResultArray();
        }

        $physicalOverstays = $db->query(
            "SELECT iv.id, COALESCE(i.full_name, iv.full_name) as visitor_name,
                    COALESCE(i.invited_by, 'N/A') as host_name,
                    iv.check_in_time, COALESCE(i.location, 'N/A') as location,
                    (SELECT MAX(s.date_to) FROM invitation_schedules s WHERE s.invitation_id = i.id) as schedule_end
             FROM invitation_visitors iv
             INNER JOIN invitations i ON i.id = iv.invitation_id
             WHERE i.status = 'Approved'
             AND iv.check_in_time IS NOT NULL
             AND iv.check_out_time IS NULL
             AND (SELECT MAX(s.date_to) FROM invitation_schedules s WHERE s.invitation_id = i.id) < ?
             ORDER BY iv.check_in_time ASC",
            [$now]
        )->getResultArray();

        return $this->response->setJSON([
            'success'           => true,
            'alertRows'         => $alertRows,
            'physicalOverstays' => $physicalOverstays,
        ]);
    }

    /**
     * AJAX: Security alert detail
     */
    public function alertDetailData($id)
    {
        $db = \Config\Database::connect();
        $alert = null;
        if ($db->tableExists('security_alerts')) {
            $alert = $db->query(
                "SELECT sa.*, u.full_name as acknowledged_by_name
                 FROM security_alerts sa
                 LEFT JOIN users u ON u.id = sa.acknowledged_by
                 WHERE sa.id = ?",
                [$id]
            )->getRowArray();
        }

        if (!$alert) {
            return $this->response->setJSON(['success' => false, 'message' => 'Alert not found']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $alert]);
    }

    /**
     * AJAX: Currently On-Site visitors
     */
    public function onSiteData()
    {
        $db = \Config\Database::connect();
        $visitors = $db->query(
            "SELECT iv.id, COALESCE(i.full_name, iv.full_name) as visitor_name,
                    i.ic_passport as ic_number,
                    i.company, COALESCE(i.invited_by, 'N/A') as host_name,
                    iv.check_in_time, COALESCE(ld.lane, 'N/A') as last_door_entry,
                    COALESCE(iv.contact, i.contact) as contact,
                    i.visitor_email, i.profile_photo_path,
                    COALESCE(vt.name, 'N/A') as visitor_type_name,
                    vt.path as visitor_type_path
             FROM invitation_visitors iv
             JOIN invitations i ON i.id = iv.invitation_id
             LEFT JOIN visitor_types vt ON vt.id = i.visitor_type_id
             LEFT JOIN (
                 SELECT vcl.visitor_card_id, l.lane
                 FROM visitor_card_logs vcl
                 JOIN lanes l ON l.id = vcl.lane_id
                 WHERE vcl.id IN (
                     SELECT MAX(vcl2.id)
                     FROM visitor_card_logs vcl2
                     GROUP BY vcl2.visitor_card_id
                 )
             ) ld ON ld.visitor_card_id = iv.visitor_card_id
             WHERE i.status = 'Approved'
             AND iv.check_in_time IS NOT NULL
             AND iv.check_out_time IS NULL
             ORDER BY iv.check_in_time DESC"
        )->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $visitors]);
    }

    /**
     * AJAX: Expected Today visitors
     */
    public function expectedTodayData()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $visitors = $db->query(
            "SELECT i.full_name, i.visitor_email, i.company,
                    iv.check_in_time, iv.check_out_time,
                    COALESCE(i.invited_by, 'N/A') as host_name,
                    s.date_from, s.date_to
             FROM invitations i
             JOIN invitation_schedules s ON s.invitation_id = i.id
             LEFT JOIN invitation_visitors iv ON iv.id = (
                 SELECT iv2.id FROM invitation_visitors iv2
                 WHERE iv2.invitation_id = i.id ORDER BY iv2.id DESC LIMIT 1
             )
             WHERE i.status = 'Approved'
             AND DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?
             ORDER BY s.date_from ASC",
            [$today, $today]
        )->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $visitors]);
    }

    /**
     * AJAX: Checked Out Today visitors
     */
    public function checkedOutData()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $visitors = $db->query(
            "SELECT iv.id, COALESCE(i.full_name, iv.full_name) as visitor_name,
                    i.company, COALESCE(i.invited_by, 'N/A') as host_name,
                    iv.check_in_time, iv.check_out_time, COALESCE(i.location, 'N/A') as location
             FROM invitation_visitors iv
             JOIN invitations i ON i.id = iv.invitation_id
             WHERE i.status = 'Approved'
             AND DATE(iv.check_out_time) = ?
             ORDER BY iv.check_out_time DESC",
            [$today]
        )->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $visitors]);
    }

    /**
     * AJAX: Active Security Alerts
     */
    public function activeAlertsData()
    {
        $db = \Config\Database::connect();
        $alerts = [];
        if ($db->tableExists('security_alerts')) {
            $alerts = $db->query(
                "SELECT sa.id, sa.incident_type, sa.severity, sa.visitor_name,
                        sa.location, sa.description, sa.created_at, sa.is_acknowledged
                 FROM security_alerts sa
                 ORDER BY sa.is_acknowledged ASC, sa.created_at DESC"
            )->getResultArray();
        }

        return $this->response->setJSON(['success' => true, 'data' => $alerts]);
    }

    /**
     * AJAX: refresh security-related dashboard numbers + critical alert queue (no full page reload).
     */
    public function widgetSnapshot()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

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

        $widgets = $this->getSecurityAlertWidgets($outOfWindow);

        return $this->response->setJSON(['success' => true] + $widgets);
    }

    /**
     * Critical banner queue + Recent Alerts / Active counts (shared by index and widgetSnapshot).
     *
     * @param int $outOfWindow physical on-site past schedule (from invitation_visitors)
     *
     * @return array{criticalAlerts: list<array>, accessDeniedCount: int, overstayCount: int, activeSecurityAlertCount: int}
     */
    protected function getSecurityAlertWidgets(int $outOfWindow): array
    {
        $db = \Config\Database::connect();

        $criticalAlerts = [];
        $accessDeniedCount = 0;
        $overstayCount = 0;
        $activeSecurityAlertCount = 0;

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

            $since24h = date('Y-m-d H:i:s', strtotime('-24 hours'));

            $accessDeniedCount = (int) ($db->query(
                "SELECT COUNT(*) AS c FROM security_alerts
                 WHERE created_at >= ?
                 AND is_acknowledged = 1
                 AND (
                     LOWER(incident_type) LIKE '%access%denied%'
                     OR LOWER(incident_type) LIKE '%unauthorized%access%'
                     OR LOWER(incident_type) LIKE '%access%refused%'
                 )",
                [$since24h]
            )->getRow()->c ?? 0);

            $overstayCount = (int) ($db->query(
                "SELECT COUNT(*) AS c FROM security_alerts
                 WHERE is_acknowledged = 0
                 AND LOWER(incident_type) LIKE '%overstay%'",
                []
            )->getRow()->c ?? 0);

            $activeSecurityAlertCount = $db->table('security_alerts')
                ->countAllResults();
        }

        $overstayCount = max($overstayCount, $outOfWindow);

        return [
            'criticalAlerts' => $criticalAlerts,
            'accessDeniedCount' => $accessDeniedCount,
            'overstayCount' => $overstayCount,
            'activeSecurityAlertCount' => $activeSecurityAlertCount,
        ];
    }

    /**
     * AJAX: Quick check-in from dashboard (sets check_in_time = now on the visitor record)
     */
    public function quickCheckIn()
    {
        $invitationId = (int) $this->request->getPost('invitation_id');
        if (!$invitationId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid invitation ID']);
        }

        $db = \Config\Database::connect();

        $invitation = $db->table('invitations')->where('id', $invitationId)->where('status', 'Approved')->get()->getRowArray();
        if (!$invitation) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invitation not found or not approved']);
        }

        $visitor = $db->table('invitation_visitors')->where('invitation_id', $invitationId)->get()->getRowArray();
        if (!$visitor) {
            return $this->response->setJSON(['success' => false, 'message' => 'No visitor record found for this invitation']);
        }

        if (!empty($visitor['check_in_time'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Visitor has already checked in']);
        }

        $now = date('Y-m-d H:i:s');
        $db->table('invitation_visitors')
            ->where('id', $visitor['id'])
            ->update(['check_in_time' => $now, 'updated_at' => $now]);

        // Log to visitor_card_logs for reports
        $db->table('visitor_card_logs')->insert([
            'visitor_card_id' => $visitor['visitor_card_id'] ?? null,
            'invitation_id'   => $invitationId,
            'action'          => 'checkin',
            'lane_id'         => null, // Manual/Dashboard
            'scan_source'     => 'manual',
            'scanned_at'      => $now,
            'created_at'      => $now,
        ]);

        if ($db->affectedRows() === 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Check-in failed, please try again']);
        }

        return $this->response->setJSON([
            'success'        => true,
            'message'        => 'Visitor checked in successfully',
            'check_in_time'  => date('h:i A', strtotime($now)),
            'visitor_name'   => $invitation['full_name'] ?? 'N/A',
        ]);
    }

    /**
     * AJAX: Full recent activity feed (last 30 days, up to 100 rows)
     */
    public function recentActivityData()
    {
        $db = \Config\Database::connect();
        $since = date('Y-m-d H:i:s', strtotime('-30 days'));

        $parts = [];
        $params = [];

        $parts[] = "SELECT 'created' AS type, i.full_name, i.created_at AS time,
               COALESCE(i.invited_by, 'System') AS actor, '' AS extra
               FROM invitations i WHERE i.created_at >= ?";
        $params[] = $since;

        $parts[] = "SELECT 'approved' AS type, i.full_name, i.updated_at AS time,
               COALESCE(i.invited_by, 'System') AS actor, '' AS extra
               FROM invitations i WHERE i.status = 'Approved'
               AND i.updated_at >= ? AND i.updated_at > i.created_at";
        $params[] = $since;

        $parts[] = "SELECT 'rejected' AS type, i.full_name, i.updated_at AS time,
               COALESCE(i.invited_by, 'System') AS actor, '' AS extra
               FROM invitations i WHERE i.status = 'Rejected'
               AND i.updated_at >= ? AND i.updated_at > i.created_at";
        $params[] = $since;

        $parts[] = "SELECT 'check_in' AS type, i.full_name, iv.check_in_time AS time,
               'Lobby' AS actor, '' AS extra
               FROM invitation_visitors iv
               JOIN invitations i ON i.id = iv.invitation_id
               WHERE iv.check_in_time IS NOT NULL AND iv.check_in_time >= ?";
        $params[] = $since;

        $parts[] = "SELECT 'check_out' AS type, i.full_name, iv.check_out_time AS time,
               'Exit' AS actor, '' AS extra
               FROM invitation_visitors iv
               JOIN invitations i ON i.id = iv.invitation_id
               WHERE iv.check_out_time IS NOT NULL AND iv.check_out_time >= ?";
        $params[] = $since;

        $parts[] = "SELECT 'door_access' AS type,
               COALESCE(i.full_name, 'Unknown Visitor') AS full_name,
               vcl.scanned_at AS time, l.lane AS actor, vcl.action AS extra
               FROM visitor_card_logs vcl
               JOIN lanes l ON l.id = vcl.lane_id
               LEFT JOIN invitations i ON i.id = vcl.invitation_id
               WHERE vcl.scanned_at >= ?";
        $params[] = $since;

        if ($db->tableExists('security_alerts')) {
            $parts[] = "SELECT 'security_alert' AS type,
                   COALESCE(sa.visitor_name, 'System') AS full_name,
                   sa.created_at AS time,
                   COALESCE(sa.location, 'Security') AS actor,
                   sa.incident_type AS extra
                   FROM security_alerts sa WHERE sa.created_at >= ?";
            $params[] = $since;
        }

        $rows = $db->query(
            'SELECT * FROM (' . implode(' UNION ALL ', $parts) . ') act ORDER BY act.time DESC LIMIT 100',
            $params
        )->getResultArray();

        $typeLabels = [
            'created'        => 'Invitation Created',
            'approved'       => 'Invitation Approved',
            'rejected'       => 'Invitation Rejected',
            'check_in'       => 'Checked In',
            'check_out'      => 'Checked Out',
            'door_access'    => 'Door Access',
            'security_alert' => 'Security Alert',
        ];

        $data = array_map(function ($row) use ($typeLabels) {
            $type  = $row['type'];
            $extra = $row['extra'] ?? '';
            $label = $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
            if ($type === 'door_access') {
                $label = $extra === 'checkin' ? 'Door Entry' : 'Door Exit';
            }
            return [
                'type'     => $type,
                'label'    => $label,
                'name'     => $row['full_name'] ?? 'N/A',
                'actor'    => $row['actor'] ?? '',
                'extra'    => $extra,
                'time'     => $row['time'],
                'time_ago' => $this->getTimeAgo($row['time']),
            ];
        }, $rows);

        return $this->response->setJSON(['success' => true, 'data' => $data]);
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
