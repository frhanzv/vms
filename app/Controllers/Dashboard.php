<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\InvitationModel;
use App\Models\InvitationVisitorModel;
use App\Models\DashboardWidgetPreferenceModel;

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
             )
             AND NOT EXISTS (
                 SELECT 1 FROM invitation_visitors iv
                 WHERE iv.invitation_id = i.id
                 AND (DATE(iv.check_in_time) = ? OR (iv.check_in_time IS NOT NULL AND iv.check_out_time IS NULL))
             )',
            ['Approved', $today, $today, $today]
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
             )
             AND NOT EXISTS (
                 SELECT 1 FROM invitation_visitors iv
                 WHERE iv.invitation_id = i.id
                 AND (DATE(iv.check_in_time) = ? OR (iv.check_in_time IS NOT NULL AND iv.check_out_time IS NULL))
             )',
            ['Approved', $yesterday, $yesterday, $yesterday]
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
        
        // Visitor traffic: every scan event today (excludes admin card-assignment logs)
        $trafficQuery = "SELECT HOUR(vcl.scanned_at) AS hour, COUNT(*) AS count
                         FROM visitor_card_logs vcl
                         JOIN invitations i ON i.id = vcl.invitation_id
                         WHERE i.status = 'Approved'
                         AND vcl.action != 'assigned'
                         AND DATE(vcl.scanned_at) = ?
                         GROUP BY HOUR(vcl.scanned_at)
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
            'currentDate' => date('M j, Y'),
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
            'widgetPreferences' => (new DashboardWidgetPreferenceModel())->getPreferences($userId),
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
        
        $trafficQuery = "SELECT DATE(vcl.scanned_at) as date, HOUR(vcl.scanned_at) as hour, COUNT(*) as count
                         FROM visitor_card_logs vcl
                         JOIN invitations i ON i.id = vcl.invitation_id
                         WHERE i.status = 'Approved'
                         AND vcl.action != 'assigned'
                         AND DATE(vcl.scanned_at) >= ?
                         AND DATE(vcl.scanned_at) <= ?
                         GROUP BY DATE(vcl.scanned_at), HOUR(vcl.scanned_at)
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

        $physicalOverstays = $db->query(
            "SELECT iv.id, COALESCE(i.full_name, iv.full_name) as visitor_name,
                    COALESCE(i.invited_by, 'N/A') as host_name,
                    iv.check_in_time, COALESCE(i.location, 'N/A') as location,
                    COALESCE(iv.contact, i.contact, 'N/A') as contact_no,
                    COALESCE(i.ic_passport, 'N/A') as ic_no,
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
                    vt.path as visitor_type_path,
                    i.location, COALESCE(s.staff_no, 'N/A') as staff_no
             FROM invitation_visitors iv
             JOIN invitations i ON i.id = iv.invitation_id
             LEFT JOIN visitor_types vt ON vt.id = i.visitor_type_id
             LEFT JOIN staff s ON s.id = i.staff_id OR s.staff_no = i.staff_id
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
                    i.contact as contact_no, i.ic_passport as ic_no,
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
             AND (iv.check_in_time IS NULL OR (DATE(iv.check_in_time) < ? AND iv.check_out_time IS NOT NULL))
             ORDER BY s.date_from ASC",
            [$today, $today, $today]
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
                    COALESCE(iv.contact, i.contact) as contact_no,
                    COALESCE(iv.ic_passport, i.ic_passport) as ic_no,
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

        if (! $db->tableExists('security_alerts')) {
            return $this->response->setJSON(['success' => true, 'data' => []]);
        }

        $builder = $db->table('security_alerts sa');
        $builder->select('sa.id, sa.incident_type, sa.severity, sa.visitor_name, sa.location, sa.description, sa.created_at, sa.is_acknowledged, 
                          COALESCE(iv.contact, i.contact) as contact_no, 
                          COALESCE(iv.ic_passport, i.ic_passport) as ic_no, 
                          h.full_name as host_name');
        $builder->join('invitations i', 'i.id = sa.invitation_id', 'left');
        $builder->join('invitation_visitors iv', 'iv.invitation_id = sa.invitation_id AND iv.full_name = sa.visitor_name', 'left');
        $builder->join('staff h', 'h.id = i.staff_id', 'left');
        $builder->orderBy('sa.is_acknowledged', 'ASC');
        $builder->orderBy('sa.created_at', 'DESC');

        $alerts = $builder->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $alerts]);
    }

    /**
     * AJAX: Analytics Assistant backed by the configured LLM service.
     */
    public function assistantAsk()
    {
        $db = \Config\Database::connect();
        if (! $this->assistantChatTablesReady($db)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assistant chat tables are missing. Please run database migrations.',
            ]);
        }

        $question = trim((string) ($this->request->getPost('message') ?? ''));
        $chatId = (int) ($this->request->getPost('chat_id') ?? 0);
        $userId = (int) (session()->get('user_id') ?? 0);

        if ($question === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a question.',
            ]);
        }

        if (strlen($question) > 1200) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please keep the question under 1200 characters.',
            ]);
        }

        $chat = $chatId > 0 ? $this->getAssistantChat($db, $chatId, $userId) : null;
        if (! $chat) {
            $chatId = $this->createAssistantChat($db, $userId, 'New Chat');
            $chat = $this->getAssistantChat($db, $chatId, $userId);
        }

        $history = $this->getAssistantChatHistory($db, $chatId, $userId, 20);
        $this->saveAssistantMessage($db, $chatId, 'user', $question);

        if (($chat['title'] ?? '') === 'New Chat') {
            $title = strlen($question) > 40 ? substr($question, 0, 37) . '...' : $question;
            $this->renameAssistantChat($db, $chatId, $userId, $title);
        }

        if ($this->isAssistantMetaQuestion($question)) {
            $metaAnswer = $this->buildAssistantMetaAnswer();
            $this->saveAssistantMessage($db, $chatId, 'assistant', $metaAnswer);
            return $this->response->setJSON([
                'success' => true,
                'answer'  => $metaAnswer,
                'sql'     => null,
                'chat_id' => $chatId,
                'title'   => $this->getAssistantChat($db, $chatId, $userId)['title'] ?? 'New Chat',
            ]);
        }

        $schema = $this->buildAssistantDatabaseSchema($db);
        $sqlResult = \Config\Services::llm()->generateText($this->buildAssistantSqlPrompt($question, $schema, $history), [
            'system_prompt' => 'You are an expert MySQL query writer for a Visitor Management System (VMS). Translate questions into one safe, correct MySQL SELECT query. Use only listed schema columns. Never use SQL keywords as table aliases. Return ONLY valid JSON: {"sql":"SELECT ..."}. No markdown, no explanation, no extra keys.',
            'temperature' => 0,
            'max_tokens' => 1200,
        ]);

        if (! $sqlResult['success']) {
            $message = $sqlResult['error'] ?? 'Analytics Assistant is unavailable.';
            $this->saveAssistantMessage($db, $chatId, 'assistant', $message);
            return $this->response->setJSON([
                'success' => false,
                'message' => $message,
                'chat_id' => $chatId,
            ]);
        }

        $sql = $this->normalizeAssistantSqlSyntax(rtrim(trim($this->extractAssistantSql($sqlResult['text'])), " \t\n\r\0\x0B;"));
        $validation = $this->validateAssistantSql($sql, array_keys($schema['tables']), $schema['blocked_columns']);

        if (! $validation['success']) {
            log_message('warning', 'Analytics Assistant rejected SQL: ' . $validation['message'] . ' | SQL: ' . $sql);
            $this->saveAssistantMessage($db, $chatId, 'assistant', $validation['message']);
            return $this->response->setJSON([
                'success' => false,
                'message' => $validation['message'],
                'chat_id' => $chatId,
            ]);
        }

        $sql = $this->applyAssistantQueryLimit($sql);

        $rows = null;
        $lastSqlError = null;
        $maxRetries = 2;

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                $rows = $db->query($sql)->getResultArray();
                $lastSqlError = null;
                break;
            } catch (\Throwable $e) {
                $lastSqlError = $e->getMessage();
                log_message('error', 'Analytics Assistant SQL attempt ' . ($attempt + 1) . ' failed: ' . $lastSqlError . ' | SQL: ' . $sql);

                if ($attempt < $maxRetries) {
                    $retryResult = \Config\Services::llm()->generateText(
                        $this->buildAssistantSqlPrompt($question, $schema, $history)
                        . "\n\nThe previous query failed with this MySQL error:\n" . $lastSqlError
                        . "\n\nPrevious (broken) SQL:\n" . $sql
                        . "\n\nFix the query and return corrected JSON: {\"sql\":\"SELECT ...\"}",
                        [
                            'system_prompt' => 'You are an expert MySQL query writer for a Visitor Management System (VMS). Fix the broken SQL query based on the error. Use only listed schema columns and never use SQL keywords as table aliases. Return ONLY valid JSON: {"sql":"SELECT ..."}. No markdown, no explanation.',
                            'temperature' => 0,
                            'max_tokens' => 1200,
                        ]
                    );

                    if (! $retryResult['success']) {
                        break;
                    }

                    $retrySql = $this->normalizeAssistantSqlSyntax(rtrim(trim($this->extractAssistantSql($retryResult['text'])), " \t\n\r\0\x0B;"));
                    $retryValidation = $this->validateAssistantSql($retrySql, array_keys($schema['tables']), $schema['blocked_columns']);

                    if (! $retryValidation['success']) {
                        log_message('warning', 'Analytics Assistant rejected retry SQL: ' . $retryValidation['message'] . ' | SQL: ' . $retrySql);
                        break;
                    }

                    $sql = $this->applyAssistantQueryLimit($retrySql);
                }
            }
        }

        if ($rows === null) {
            $message = 'I could not run the database query for that question.';
            $this->saveAssistantMessage($db, $chatId, 'assistant', $message);
            return $this->response->setJSON([
                'success' => false,
                'message' => $message,
                'chat_id' => $chatId,
            ]);
        }

        $summaryResult = \Config\Services::llm()->generateText($this->buildAssistantAnswerPrompt($question, $sql, $rows, $history), [
            'system_prompt' => 'You are the SafeG VMS Analytics Assistant. Explain SQL results clearly and helpfully for security officers, hosts, and reception staff. Format lists neatly. If results are empty, suggest why and what the user could try instead.',
            'temperature' => 0.3,
            'max_tokens' => 1200,
        ]);

        if (! $summaryResult['success']) {
            $fallback = $this->formatAssistantRowsFallback($rows);
            $this->saveAssistantMessage($db, $chatId, 'assistant', $fallback);
            return $this->response->setJSON([
                'success' => true,
                'answer' => $fallback,
                'sql' => $sql,
                'row_count' => count($rows),
                'chat_id' => $chatId,
                'title' => $this->getAssistantChat($db, $chatId, $userId)['title'] ?? 'New Chat',
            ]);
        }

        $this->saveAssistantMessage($db, $chatId, 'assistant', $summaryResult['text']);

        return $this->response->setJSON([
            'success' => true,
            'answer' => $summaryResult['text'],
            'model' => $summaryResult['model'] ?? null,
            'sql' => $sql,
            'row_count' => count($rows),
            'chat_id' => $chatId,
            'title' => $this->getAssistantChat($db, $chatId, $userId)['title'] ?? 'New Chat',
        ]);
    }

    public function assistantHistory()
    {
        $db = \Config\Database::connect();
        if (! $this->assistantChatTablesReady($db)) {
            return $this->response->setJSON(['success' => true, 'chats' => []]);
        }
        $userId = (int) (session()->get('user_id') ?? 0);

        $chats = $db->table('llm_chat_sessions')
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        $ids = array_column($chats, 'id');
        $messages = [];
        if ($ids !== []) {
            $rows = $db->table('llm_chat_messages')
                ->whereIn('session_id', $ids)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $messages[(int) $row['session_id']][] = [
                    'role' => $row['role'],
                    'text' => $row['content'],
                ];
            }
        }

        $data = array_map(static function (array $chat) use ($messages): array {
            $id = (int) $chat['id'];
            return [
                'id' => (string) $id,
                'title' => $chat['title'] ?: 'New Chat',
                'date' => ! empty($chat['updated_at']) ? date('M j', strtotime($chat['updated_at'])) : '',
                'messages' => $messages[$id] ?? [],
            ];
        }, $chats);

        return $this->response->setJSON(['success' => true, 'chats' => $data]);
    }

    public function assistantChatCreate()
    {
        $db = \Config\Database::connect();
        if (! $this->assistantChatTablesReady($db)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assistant chat tables are missing. Please run database migrations.',
            ]);
        }
        $userId = (int) (session()->get('user_id') ?? 0);
        $id = $this->createAssistantChat($db, $userId, 'New Chat');

        return $this->response->setJSON([
            'success' => true,
            'chat' => [
                'id' => (string) $id,
                'title' => 'New Chat',
                'date' => date('M j'),
                'messages' => [],
            ],
        ]);
    }

    public function assistantChatRename()
    {
        $db = \Config\Database::connect();
        if (! $this->assistantChatTablesReady($db)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Assistant chat tables are missing. Please run database migrations.',
            ]);
        }
        $userId = (int) (session()->get('user_id') ?? 0);
        $chatId = (int) ($this->request->getPost('chat_id') ?? 0);
        $title = trim((string) ($this->request->getPost('title') ?? ''));

        if ($chatId <= 0 || $title === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid chat title.']);
        }

        $title = strlen($title) > 80 ? substr($title, 0, 77) . '...' : $title;
        $this->renameAssistantChat($db, $chatId, $userId, $title);

        return $this->response->setJSON(['success' => true, 'title' => $title]);
    }

    public function assistantChatDelete()
    {
        $db = \Config\Database::connect();
        if (! $this->assistantChatTablesReady($db)) {
            return $this->response->setJSON(['success' => true]);
        }
        $userId = (int) (session()->get('user_id') ?? 0);
        $chatId = (int) ($this->request->getPost('chat_id') ?? 0);

        if ($chatId > 0) {
            $db->table('llm_chat_sessions')->where('id', $chatId)->where('user_id', $userId)->delete();
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function assistantChatClear()
    {
        $db = \Config\Database::connect();
        if (! $this->assistantChatTablesReady($db)) {
            return $this->response->setJSON(['success' => true]);
        }
        $userId = (int) (session()->get('user_id') ?? 0);

        $db->table('llm_chat_sessions')->where('user_id', $userId)->delete();

        return $this->response->setJSON(['success' => true]);
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
                    'time' => !empty($alert['created_at']) ? date('M j, Y g:i A', strtotime($alert['created_at'])) : 'N/A',
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

            $activeSecurityAlertCount = $db->table('security_alerts')
                ->countAllResults();
        }

        $overstayCount = $outOfWindow;

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
            'check_in_time'  => date('g:i A', strtotime($now)),
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

    /**
     * @return array{tables: array<string, list<array{name: string, type: string|null}>>, blocked_columns: list<string>}
     */
    protected function buildAssistantDatabaseSchema($db): array
    {
        $blockedPatterns = [
            'password', 'api_key', 'apikey', 'token', 'secret', 'credential',
            'smtp', 'session', 'remember', 'reset', 'otp', 'private_key', 'access_key',
        ];
        $systemTables = ['migrations'];
        $schema = [];
        $blockedColumns = [];

        foreach ($db->listTables() as $table) {
            if (in_array($table, $systemTables, true)) {
                continue;
            }

            $columns = [];
            foreach ($db->getFieldData($table) as $field) {
                $name = (string) ($field->name ?? '');
                if ($name === '') {
                    continue;
                }

                if ($this->isAssistantBlockedColumn($name, $blockedPatterns)) {
                    $blockedColumns[] = $name;
                    continue;
                }

                $columns[] = [
                    'name' => $name,
                    'type' => isset($field->type) ? (string) $field->type : null,
                ];
            }

            if ($columns !== []) {
                $schema[$table] = $columns;
            }
        }

        return [
            'tables' => $schema,
            'blocked_columns' => array_values(array_unique($blockedColumns)),
        ];
    }

    /**
     * @param list<string> $blockedPatterns
     */
    protected function isAssistantBlockedColumn(string $column, array $blockedPatterns): bool
    {
        $name = strtolower($column);
        foreach ($blockedPatterns as $pattern) {
            if (str_contains($name, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function assistantChatTablesReady($db): bool
    {
        return $db->tableExists('llm_chat_sessions') && $db->tableExists('llm_chat_messages');
    }

    protected function createAssistantChat($db, int $userId, string $title): int
    {
        $now = date('Y-m-d H:i:s');
        $db->table('llm_chat_sessions')->insert([
            'user_id' => $userId,
            'title' => $title,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $db->insertID();
    }

    protected function getAssistantChat($db, int $chatId, int $userId): ?array
    {
        if ($chatId <= 0) {
            return null;
        }

        $chat = $db->table('llm_chat_sessions')
            ->where('id', $chatId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        return $chat ?: null;
    }

    protected function renameAssistantChat($db, int $chatId, int $userId, string $title): void
    {
        $db->table('llm_chat_sessions')
            ->where('id', $chatId)
            ->where('user_id', $userId)
            ->update([
                'title' => $title,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    protected function saveAssistantMessage($db, int $chatId, string $role, string $content): void
    {
        if (! in_array($role, ['user', 'assistant'], true) || trim($content) === '') {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $db->table('llm_chat_messages')->insert([
            'session_id' => $chatId,
            'role' => $role,
            'content' => $content,
            'created_at' => $now,
        ]);
        $db->table('llm_chat_sessions')
            ->where('id', $chatId)
            ->update(['updated_at' => $now]);
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    protected function getAssistantChatHistory($db, int $chatId, int $userId, int $limit = 10): array
    {
        if (! $this->getAssistantChat($db, $chatId, $userId)) {
            return [];
        }

        $rows = $db->table('llm_chat_messages')
            ->where('session_id', $chatId)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $rows = array_reverse($rows);

        return array_map(static fn (array $row): array => [
            'role' => $row['role'],
            'content' => $row['content'],
        ], $rows);
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    protected function normalizeAssistantHistory(string $historyJson): array
    {
        $decoded = json_decode($historyJson, true);
        if (! is_array($decoded)) {
            return [];
        }

        $history = [];
        foreach (array_slice($decoded, -10) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $role = (string) ($item['role'] ?? '');
            $content = trim((string) ($item['content'] ?? ''));

            if (! in_array($role, ['user', 'assistant'], true) || $content === '') {
                continue;
            }

            $history[] = [
                'role' => $role,
                'content' => substr($content, 0, 1200),
            ];
        }

        return $history;
    }

    /**
     * @param list<array{role: string, content: string}> $history
     */
    protected function formatAssistantHistoryForPrompt(array $history): string
    {
        if ($history === []) {
            return 'No previous messages in this chat.';
        }

        $lines = [];
        foreach ($history as $message) {
            $lines[] = strtoupper($message['role']) . ': ' . $message['content'];
        }

        return implode("\n", $lines);
    }

    /**
     * @param array{tables: array<string, list<array{name: string, type: string|null}>>, blocked_columns: list<string>} $schema
     * @param list<array{role: string, content: string}> $history
     */
    protected function isAssistantMetaQuestion(string $question): bool
    {
        $q = strtolower(trim($question));
        $metaPatterns = [
            '/what (can|could) (i|we|you) ask/i',
            '/what (question|questions|things)/i',
            '/what (do you|can you) (know|do|help|answer)/i',
            '/what (are you|is this)/i',
            '/how (do|can) (i|we) use/i',
            '/help me/i',
            '/show (me )?(example|examples|sample|samples)/i',
            '/what (topics?|info|information|data)/i',
            '/what (is available|can be asked)/i',
            '/capabilities/i',
            '/^help$/i',
        ];

        foreach ($metaPatterns as $pattern) {
            if (preg_match($pattern, $q)) {
                return true;
            }
        }

        return false;
    }

    protected function buildAssistantMetaAnswer(): string
    {
        return "I can answer questions about data in the Visitor Management System database. Here are some things you can ask:\n\n"
            . "**Visitors & Check-in**\n"
            . "- Who is currently on-site?\n"
            . "- How many visitors checked in today?\n"
            . "- Show all visitors from [company name]\n"
            . "- When did [visitor name] check in?\n"
            . "- When will [visitor name]'s visit end?\n\n"
            . "**Security Alerts**\n"
            . "- How many active security alerts are there?\n"
            . "- Show all unacknowledged alerts\n"
            . "- Which location has the most alerts?\n\n"
            . "**Appointments & Invitations**\n"
            . "- How many visitors are expected today?\n"
            . "- Show upcoming appointments for [host name]\n"
            . "- List all pending invitations\n\n"
            . "**Overstay & Reporting**\n"
            . "- Which visitors are overstaying?\n"
            . "- How many visitors checked out today?\n"
            . "- Show visitor count by host\n\n"
            . "I also understand VMS terms like expected today, on-site, checked out, overstay, host, staff number, access denied, and active alerts.\n\n"
            . "Just ask in plain language — I will query the database and explain the results.";
    }

    protected function buildAssistantSqlPrompt(string $question, array $schema, array $history = []): string
    {
        $schemaLines = [];
        $relationships = [];

        $tableNames = array_keys($schema['tables']);

        foreach ($schema['tables'] as $table => $columns) {
            $colText = array_map(
                static fn (array $col): string => $col['name'] . ($col['type'] ? ' ' . $col['type'] : ''),
                $columns
            );
            $schemaLines[] = $table . '(' . implode(', ', $colText) . ')';

            foreach ($columns as $col) {
                if (! preg_match('/^(.+)_id$/', $col['name'], $m)) {
                    continue;
                }
                $base = $m[1];
                // Try plural, singular, and exact match
                foreach ([$base . 's', $base . 'es', $base] as $candidate) {
                    if (in_array($candidate, $tableNames, true)) {
                        $relationships[] = "{$table}.{$col['name']} → {$candidate}.id";
                        break;
                    }
                }
            }
        }

        $relationships = array_values(array_unique(array_merge(
            $this->buildAssistantRelationshipHints($schema),
            $relationships
        )));

        $relSection = $relationships
            ? "Known relationships (use for JOINs):\n" . implode("\n", $relationships) . "\n\n"
            : '';

        return "Create one MySQL SELECT query to answer the user's VMS question.\n\n"
            . "RULES:\n"
            . "- Return ONLY valid JSON: {\"sql\":\"SELECT ...\"} — no markdown, no extra text.\n"
            . "- Use only the tables and columns listed in the schema below.\n"
            . "- SELECT only. Never INSERT, UPDATE, DELETE, DROP, ALTER, CREATE, SET, SHOW, or stored procedures.\n"
            . "- Never use SELECT * or table.*. Name only the columns you need.\n"
            . "- Use JOINs when data spans multiple tables (see relationships below).\n"
            . "- Use safe table aliases such as i, iv, sch, sa, vc, vcl, l, s. Never use SQL keywords as aliases, especially is, in, on, by, group, order, select, where, join, from, left, right, inner, outer, limit, or having.\n"
            . "- Use COALESCE or IS NULL / IS NOT NULL to handle missing values.\n"
            . "- When a text column can be blank, use NULLIF(column, '') inside COALESCE.\n"
            . "- For \"today\" use CURDATE(); for date+time comparisons use DATE(column) = CURDATE().\n"
            . "- For visitor names, prefer COALESCE(NULLIF(invitation_visitors.full_name, ''), invitations.full_name).\n"
            . "- For contact numbers, prefer COALESCE(NULLIF(invitation_visitors.contact, ''), NULLIF(invitations.contact, '')).\n"
            . "- Do not invent columns. For check-out use invitation_visitors.check_out_time. For check-in use invitation_visitors.check_in_time or invitations.checked_in_at only if the question is about registration-level check-in.\n"
            . "- For scheduled end time, join invitation_schedules and use invitation_schedules.date_to. There is no expected_end column unless it appears in the schema.\n"
            . "- Host normally means invitations.invited_by. Staff number normally comes from staff.staff_no joined through invitations.staff_id.\n"
            . "- Add AS aliases on computed or ambiguous columns (e.g. COUNT(*) AS total).\n"
            . "- Default LIMIT 100 unless the question asks for a specific count (then use COUNT(*)).\n"
            . "- Use the chat history to resolve pronouns like \"that visitor\", \"same host\", \"those\", \"how many\".\n\n"
            . $this->buildAssistantDomainGuide($schema) . "\n\n"
            . $relSection
            . "Database schema:\n" . implode("\n", $schemaLines) . "\n\n"
            . "Chat history:\n" . $this->formatAssistantHistoryForPrompt($history) . "\n\n"
            . "User question: " . $question;
    }

    /**
     * @param array{tables: array<string, list<array{name: string, type: string|null}>>, blocked_columns: list<string>} $schema
     *
     * @return list<string>
     */
    protected function buildAssistantRelationshipHints(array $schema): array
    {
        $relationships = [];
        $explicit = [
            ['invitation_visitors', 'invitation_id', 'invitations', 'id'],
            ['invitation_visitors', 'visitor_card_id', 'visitor_cards', 'id'],
            ['invitation_schedules', 'invitation_id', 'invitations', 'id'],
            ['visitor_card_logs', 'invitation_id', 'invitations', 'id'],
            ['visitor_card_logs', 'visitor_card_id', 'visitor_cards', 'id'],
            ['visitor_card_logs', 'lane_id', 'lanes', 'id'],
            ['visitor_assets', 'invitation_id', 'invitations', 'id'],
            ['visitor_licenses', 'invitation_id', 'invitations', 'id'],
            ['security_alerts', 'invitation_id', 'invitations', 'id'],
            ['security_alerts', 'acknowledged_by', 'users', 'id'],
            ['invitations', 'visitor_type_id', 'visitor_types', 'id'],
            ['invitations', 'company_id', 'companies', 'id'],
            ['users', 'company_id', 'companies', 'id'],
            ['lanes', 'location_id', 'locations', 'id'],
            ['sub_locations', 'location_id', 'locations', 'id'],
            ['pathway_sub_locations', 'pathway_id', 'pathways', 'id'],
            ['pathway_sub_locations', 'sub_location_id', 'sub_locations', 'id'],
            ['staff_cards', 'staff_id', 'staff', 'id'],
        ];

        foreach ($explicit as [$fromTable, $fromColumn, $toTable, $toColumn]) {
            if (
                $this->assistantSchemaHasColumn($schema, $fromTable, $fromColumn)
                && $this->assistantSchemaHasColumn($schema, $toTable, $toColumn)
            ) {
                $relationships[] = "{$fromTable}.{$fromColumn} -> {$toTable}.{$toColumn}";
            }
        }

        if (
            $this->assistantSchemaHasColumn($schema, 'invitations', 'staff_id')
            && $this->assistantSchemaHasColumn($schema, 'staff', 'id')
        ) {
            $relationships[] = 'invitations.staff_id -> staff.id; if staff_id stores a staff number, also allow staff.staff_no = invitations.staff_id';
        }

        return array_values(array_unique($relationships));
    }

    /**
     * @param array{tables: array<string, list<array{name: string, type: string|null}>>, blocked_columns: list<string>} $schema
     */
    protected function assistantSchemaHasColumn(array $schema, string $table, string $column): bool
    {
        foreach ($schema['tables'][$table] ?? [] as $field) {
            if (strcasecmp($field['name'], $column) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array{tables: array<string, list<array{name: string, type: string|null}>>, blocked_columns: list<string>} $schema
     */
    protected function buildAssistantDomainGuide(array $schema): string
    {
        $lines = [
            'VMS domain guide:',
            '- invitations: appointment/request record. Important fields: full_name, contact, ic_passport, company, invited_by, staff_id, location, reason, status, registration_source.',
            '- invitation_visitors: physical visitor pass/check-in record. Important fields: check_in_time, check_out_time, full_name, contact, ic_passport, visitor_card_id.',
            '- invitation_schedules: scheduled visit windows. Use date_from and date_to for expected, appointment time, visit end, and overstay checks.',
            '- security_alerts: incident records. is_acknowledged = 0 means active/unacknowledged; severity can be low, medium, high, critical.',
            '- locations and lanes: physical access areas and device/door lanes.',
            '- visitor_card_logs: door/card scan history. action usually identifies checkin/checkout or entry/exit activity.',
            'Common VMS definitions:',
            "- Expected today: invitations.status = 'Approved' and an invitation_schedules row overlaps CURDATE(), excluding visitors already checked in today or still on-site unless the user asks otherwise.",
            "- Currently on-site: invitations.status = 'Approved' and invitation_visitors.check_in_time IS NOT NULL and invitation_visitors.check_out_time IS NULL.",
            "- Checked out today: invitations.status = 'Approved' and DATE(invitation_visitors.check_out_time) = CURDATE().",
            '- Overstaying: currently on-site and the latest invitation_schedules.date_to for that invitation is earlier than NOW().',
            '- Active security alerts: security_alerts.is_acknowledged = 0.',
            '- Access denied incidents: security_alerts.incident_type contains access denied, unauthorized access, or access refused.',
            '- Visitor IC/passport can be invitations.ic_passport or invitation_visitors.ic_passport.',
            '- Location can be invitations.location, security_alerts.location, lanes.lane, locations.location_access, or sub_locations.name depending on the question.',
        ];

        if (! isset($schema['tables']['security_alerts'])) {
            $lines[] = '- security_alerts is not available in this database, so do not query it.';
        }

        return implode("\n", $lines);
    }

    protected function extractAssistantSql(string $text): string
    {
        $clean = trim($text);
        $clean = preg_replace('/^```(?:json|sql)?\s*/i', '', $clean) ?? $clean;
        $clean = preg_replace('/\s*```$/', '', $clean) ?? $clean;
        $decoded = json_decode($clean, true);

        if (is_array($decoded) && isset($decoded['sql'])) {
            return trim((string) $decoded['sql']);
        }

        if (preg_match('/"sql"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/s', $clean, $m)) {
            return trim(stripcslashes($m[1]));
        }

        return trim($clean);
    }

    protected function normalizeAssistantSqlSyntax(string $sql): string
    {
        $clean = trim($sql);

        if ($clean === '') {
            return $clean;
        }

        $reservedAliases = $this->assistantReservedSqlAliases();
        $reservedPattern = implode('|', array_map(static fn (string $word): string => preg_quote($word, '/'), $reservedAliases));
        $clausePattern = 'ON|USING|JOIN|LEFT|RIGHT|INNER|OUTER|CROSS|WHERE|GROUP|ORDER|HAVING|LIMIT|UNION|,|$';
        $aliasMap = [];
        $aliasCounter = 1;

        $clean = preg_replace_callback(
            '/\b(FROM|JOIN)\s+(`?[a-zA-Z0-9_]+`?)\s+(?:AS\s+)?`?(' . $reservedPattern . ')`?(?=\s*(\b(?:' . $clausePattern . ')\b|,|$))/i',
            static function (array $match) use (&$aliasMap, &$aliasCounter): string {
                $badAlias = strtolower($match[3]);
                $nextToken = strtolower(trim($match[4] ?? ''));
                if (in_array($badAlias, ['left', 'right', 'inner', 'outer', 'cross'], true) && $nextToken === 'join') {
                    return $match[0];
                }

                if (! isset($aliasMap[$badAlias])) {
                    $aliasMap[$badAlias] = 'a' . $aliasCounter++;
                }

                return $match[1] . ' ' . $match[2] . ' ' . $aliasMap[$badAlias];
            },
            $clean
        ) ?? $clean;

        $clean = preg_replace_callback(
            '/,\s+(`?[a-zA-Z0-9_]+`?)\s+(?:AS\s+)?`?(' . $reservedPattern . ')`?(?=\s*(\b(?:' . $clausePattern . ')\b|,|$))/i',
            static function (array $match) use (&$aliasMap, &$aliasCounter): string {
                $badAlias = strtolower($match[2]);
                if (! isset($aliasMap[$badAlias])) {
                    $aliasMap[$badAlias] = 'a' . $aliasCounter++;
                }

                return ', ' . $match[1] . ' ' . $aliasMap[$badAlias];
            },
            $clean
        ) ?? $clean;

        foreach ($aliasMap as $badAlias => $safeAlias) {
            $clean = preg_replace('/(?<![a-zA-Z0-9_`])`?' . preg_quote($badAlias, '/') . '`?\s*\./i', $safeAlias . '.', $clean) ?? $clean;
        }

        return $clean;
    }

    /**
     * @return list<string>
     */
    protected function assistantReservedSqlAliases(): array
    {
        return [
            'all', 'and', 'as', 'between', 'by', 'case', 'cross', 'delete', 'desc',
            'distinct', 'else', 'end', 'exists', 'from', 'group', 'having', 'if',
            'in', 'inner', 'insert', 'is', 'join', 'left', 'like', 'limit', 'not',
            'null', 'on', 'or', 'order', 'outer', 'right', 'select', 'set', 'then',
            'union', 'update', 'using', 'when', 'where',
        ];
    }

    protected function hasAssistantReservedSqlAlias(string $sql): bool
    {
        $reservedPattern = implode('|', array_map(static fn (string $word): string => preg_quote($word, '/'), $this->assistantReservedSqlAliases()));
        $clausePattern = 'ON|USING|JOIN|LEFT|RIGHT|INNER|OUTER|CROSS|WHERE|GROUP|ORDER|HAVING|LIMIT|UNION|,|$';

        if (! preg_match_all('/\b(FROM|JOIN)\s+`?[a-zA-Z0-9_]+`?\s+(?:AS\s+)?`?(' . $reservedPattern . ')`?(?=\s*(\b(?:' . $clausePattern . ')\b|,|$))/i', $sql, $matches, PREG_SET_ORDER)) {
            return false;
        }

        foreach ($matches as $match) {
            $badAlias = strtolower($match[2]);
            $nextToken = strtolower(trim($match[3] ?? ''));
            if (in_array($badAlias, ['left', 'right', 'inner', 'outer', 'cross'], true) && $nextToken === 'join') {
                continue;
            }

            return true;
        }

        return (bool) preg_match('/,\s+`?[a-zA-Z0-9_]+`?\s+(?:AS\s+)?`?(' . $reservedPattern . ')`?(?=\s*(\b(?:' . $clausePattern . ')\b|,|$))/i', $sql);
    }

    /**
     * @param list<string> $allowedTables
     * @param list<string> $blockedColumns
     *
     * @return array{success: bool, message: string}
     */
    protected function validateAssistantSql(string $sql, array $allowedTables, array $blockedColumns): array
    {
        $normalized = trim($sql);
        $lower = strtolower($normalized);

        if ($normalized === '') {
            return ['success' => false, 'message' => 'I could not build a database query for that question.'];
        }

        if (! preg_match('/^select\b/i', $normalized)) {
            return ['success' => false, 'message' => 'Only read-only SELECT questions are allowed.'];
        }

        if (str_contains($normalized, ';') || str_contains($normalized, '--') || str_contains($normalized, '/*') || str_contains($normalized, '*/') || preg_match('/(^|\s)#/', $normalized)) {
            return ['success' => false, 'message' => 'The generated query was rejected for safety.'];
        }

        if (preg_match('/\b(insert|update|delete|drop|alter|truncate|create|replace|grant|revoke|call|set|use|show|describe|explain|handler|load|outfile|infile|lock|unlock)\b/i', $normalized)) {
            return ['success' => false, 'message' => 'Only read-only database questions are allowed.'];
        }

        if (preg_match('/\b(information_schema|mysql|performance_schema|sys)\b/i', $normalized)) {
            return ['success' => false, 'message' => 'System database tables are not available to the assistant.'];
        }

        if ($this->hasAssistantReservedSqlAlias($normalized)) {
            return ['success' => false, 'message' => 'The generated query used a reserved SQL keyword as a table alias.'];
        }

        if (preg_match_all('/\b(?:from|join)\s+`?([a-zA-Z0-9_]+)`?/i', $normalized, $matches)) {
            $allowedLookup = array_fill_keys(array_map('strtolower', $allowedTables), true);
            foreach ($matches[1] as $table) {
                if (! isset($allowedLookup[strtolower($table)])) {
                    return ['success' => false, 'message' => 'The generated query referenced a table that is not available to the assistant.'];
                }
            }
        }

        if (preg_match('/(^|\s)select\s+\*|\b[a-zA-Z0-9_]+\s*\.\s*\*/i', $normalized)) {
            return ['success' => false, 'message' => 'The assistant must request specific columns, not all columns.'];
        }

        foreach ($blockedColumns as $column) {
            if ($column !== '' && preg_match('/\b' . preg_quote(strtolower($column), '/') . '\b/i', $lower)) {
                return ['success' => false, 'message' => 'Credential and secret fields are not available to the assistant.'];
            }
        }

        return ['success' => true, 'message' => 'OK'];
    }

    protected function applyAssistantQueryLimit(string $sql): string
    {
        $clean = rtrim(trim($sql), " \t\n\r\0\x0B;");

        if (preg_match('/\blimit\s+(\d+)/i', $clean, $m)) {
            $limit = (int) $m[1];
            if ($limit > 100) {
                return preg_replace('/\blimit\s+\d+/i', 'LIMIT 100', $clean, 1) ?? $clean;
            }

            return $clean;
        }

        return $clean . ' LIMIT 100';
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param list<array{role: string, content: string}> $history
     */
    protected function buildAssistantAnswerPrompt(string $question, string $sql, array $rows, array $history = []): string
    {
        $rowCount = count($rows);
        $rowsJson = json_encode(array_slice($rows, 0, 100), JSON_PRETTY_PRINT);

        return "Answer the user's VMS question based on the SQL query results below.\n"
            . "Guidelines:\n"
            . "- Put the direct answer first, then supporting details.\n"
            . "- Be concise and clear. Use plain language a receptionist or security guard would understand.\n"
            . "- For count questions, answer with the number first.\n"
            . "- For 2 to 12 record lists, use a compact markdown table with the most useful columns.\n"
            . "- For longer lists, summarize first and then show the most important rows.\n"
            . "- If row count is 0, explain what was searched for and suggest the data might not exist, might use a different spelling, or that the field may be empty in the system.\n"
            . "- If row count is 100, note that results are capped at 100 and there may be more.\n"
            . "- For date/time fields, format them in a human-readable way.\n"
            . "- Prefer names, host names, locations, and times over raw IDs unless IDs were requested.\n"
            . "- Use the chat history to keep your answer consistent with prior context.\n\n"
            . "Chat history:\n" . $this->formatAssistantHistoryForPrompt($history) . "\n\n"
            . "User question: {$question}\n\n"
            . "SQL executed:\n{$sql}\n\n"
            . "Total rows returned: {$rowCount}\n"
            . "Results:\n" . $rowsJson;
    }

    /**
     * @param list<array<string, mixed>> $rows
     */
    protected function formatAssistantRowsFallback(array $rows): string
    {
        if ($rows === []) {
            return 'No matching database records were found.';
        }

        $preview = array_slice($rows, 0, 10);
        $lines = ['I found ' . count($rows) . ' record' . (count($rows) === 1 ? '' : 's') . '.'];

        foreach ($preview as $idx => $row) {
            $parts = [];
            foreach ($row as $key => $value) {
                $parts[] = $key . ': ' . (is_scalar($value) || $value === null ? (string) $value : json_encode($value));
            }
            $lines[] = ($idx + 1) . '. ' . implode(', ', $parts);
        }

        if (count($rows) > count($preview)) {
            $lines[] = 'Showing the first ' . count($preview) . ' records.';
        }

        return implode("\n", $lines);
    }

    /**
     * Build a compact dashboard snapshot for LLM analytics answers.
     *
     * @return array<string, mixed>
     */
    protected function buildAssistantAnalyticsContext(): array
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $expectedToday = (int) ($db->query(
            'SELECT COUNT(*) AS c
             FROM invitations i
             WHERE i.status = ?
             AND EXISTS (
                 SELECT 1 FROM invitation_schedules s
                 WHERE s.invitation_id = i.id
                 AND DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?
             )
             AND NOT EXISTS (
                 SELECT 1 FROM invitation_visitors iv
                 WHERE iv.invitation_id = i.id
                 AND (DATE(iv.check_in_time) = ? OR (iv.check_in_time IS NOT NULL AND iv.check_out_time IS NULL))
             )',
            ['Approved', $today, $today, $today]
        )->getRow()->c ?? 0);

        $onSite = $db->query(
            "SELECT COALESCE(i.full_name, iv.full_name) as visitor_name,
                    COALESCE(iv.contact, i.contact, 'N/A') as contact,
                    COALESCE(i.invited_by, 'N/A') as host_name,
                    COALESCE(i.location, 'N/A') as location,
                    iv.check_in_time,
                    COALESCE(s.staff_no, 'N/A') as staff_no
             FROM invitation_visitors iv
             JOIN invitations i ON i.id = iv.invitation_id
             LEFT JOIN staff s ON s.id = i.staff_id OR s.staff_no = i.staff_id
             WHERE i.status = 'Approved'
             AND iv.check_in_time IS NOT NULL
             AND iv.check_out_time IS NULL
             ORDER BY iv.check_in_time DESC
             LIMIT 25"
        )->getResultArray();

        $checkedOutToday = (int) ($db->query(
            'SELECT COUNT(*) AS c
             FROM invitation_visitors iv
             JOIN invitations i ON i.id = iv.invitation_id
             WHERE i.status = ?
             AND DATE(iv.check_out_time) = ?',
            ['Approved', $today]
        )->getRow()->c ?? 0);

        $overstays = $db->query(
            "SELECT COALESCE(i.full_name, iv.full_name) as visitor_name,
                    COALESCE(i.invited_by, 'N/A') as host_name,
                    COALESCE(i.location, 'N/A') as location,
                    iv.check_in_time,
                    (SELECT MAX(s.date_to) FROM invitation_schedules s WHERE s.invitation_id = i.id) as schedule_end
             FROM invitation_visitors iv
             INNER JOIN invitations i ON i.id = iv.invitation_id
             WHERE i.status = 'Approved'
             AND iv.check_in_time IS NOT NULL
             AND iv.check_out_time IS NULL
             AND (SELECT MAX(s.date_to) FROM invitation_schedules s WHERE s.invitation_id = i.id) < ?
             ORDER BY iv.check_in_time ASC
             LIMIT 25",
            [$now]
        )->getResultArray();

        $expectedRows = $db->query(
            "SELECT i.full_name as visitor_name,
                    COALESCE(i.invited_by, 'N/A') as host_name,
                    COALESCE(i.location, 'N/A') as location,
                    i.contact,
                    s.date_from,
                    s.date_to
             FROM invitations i
             JOIN invitation_schedules s ON s.invitation_id = i.id
             LEFT JOIN invitation_visitors iv ON iv.id = (
                 SELECT iv2.id FROM invitation_visitors iv2
                 WHERE iv2.invitation_id = i.id ORDER BY iv2.id DESC LIMIT 1
             )
             WHERE i.status = 'Approved'
             AND DATE(s.date_from) <= ? AND DATE(s.date_to) >= ?
             AND (iv.check_in_time IS NULL OR (DATE(iv.check_in_time) < ? AND iv.check_out_time IS NOT NULL))
             ORDER BY s.date_from ASC
             LIMIT 25",
            [$today, $today, $today]
        )->getResultArray();

        $alerts = [];
        $activeAlertCount = 0;
        if ($db->tableExists('security_alerts')) {
            $activeAlertCount = (int) ($db->query(
                'SELECT COUNT(*) AS c FROM security_alerts WHERE is_acknowledged = 0'
            )->getRow()->c ?? 0);

            $alerts = $db->query(
                "SELECT incident_type, severity, visitor_name, location, created_at, is_acknowledged
                 FROM security_alerts
                 ORDER BY is_acknowledged ASC, created_at DESC
                 LIMIT 25"
            )->getResultArray();
        }

        $trafficByHour = $db->query(
            "SELECT HOUR(iv.check_in_time) AS hour, COUNT(*) AS count
             FROM invitation_visitors iv
             JOIN invitations i ON i.id = iv.invitation_id
             WHERE i.status = 'Approved'
             AND iv.check_in_time IS NOT NULL
             AND DATE(iv.check_in_time) = ?
             GROUP BY HOUR(iv.check_in_time)
             ORDER BY hour ASC",
            [$today]
        )->getResultArray();

        return [
            'generated_at' => $now,
            'date' => $today,
            'summary' => [
                'expected_today' => $expectedToday,
                'currently_on_site' => count($onSite),
                'checked_out_today' => $checkedOutToday,
                'currently_overstaying' => count($overstays),
                'active_security_alerts' => $activeAlertCount,
            ],
            'currently_on_site' => $onSite,
            'expected_today' => $expectedRows,
            'currently_overstaying' => $overstays,
            'security_alerts' => $alerts,
            'checkins_by_hour' => $trafficByHour,
        ];
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

    public function getWidgetPreferences()
    {
        $userId = session()->get('user_id');
        $prefs  = (new DashboardWidgetPreferenceModel())->getPreferences($userId);
        return $this->response->setJSON($prefs);
    }

    public function saveWidgetPreferences()
    {
        $userId  = session()->get('user_id');
        $raw     = $this->request->getPost('widgets');
        $configs = $raw ? json_decode($raw, true) : null;
        if (!is_array($configs) || empty($configs)) {
            return $this->response->setJSON(['success' => false]);
        }
        (new DashboardWidgetPreferenceModel())->savePreferences($userId, $configs);
        return $this->response->setJSON(['success' => true]);
    }

    public function uploadPosterImage()
    {
        $file = $this->request->getFile('image');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No file uploaded']);
        }
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowed)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only JPG, PNG, GIF, WEBP allowed']);
        }
        $uploadPath = FCPATH . 'uploads/poster/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $userId   = session()->get('user_id');
        $filename = 'poster_' . $userId . '_' . time() . '.' . $file->getExtension();
        if ($file->move($uploadPath, $filename)) {
            return $this->response->setJSON(['success' => true, 'url' => base_url('uploads/poster/' . $filename)]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Upload failed']);
    }
}
