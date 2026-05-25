<?php

namespace App\Controllers;

class VisitorReport extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Visitor Report - SafeG',
        ];

        return view('reports/visitor_report', $data);
    }

    public function generate()
    {
        $db = \Config\Database::connect();

        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        $where = "";
        if ($from && $to) {
            $where .= " AND DATE(i.created_at) BETWEEN " . $db->escape($from) . " AND " . $db->escape($to);
        }

        $sql = "SELECT
                    i.id               AS invitation_id,
                    i.full_name        AS visitor_name,
                    i.contact          AS contact_no,
                    i.ic_passport      AS ic_no,
                    i.company          AS visitor_company,
                    i.invited_by       AS person_visited,
                    i.staff_id         AS staff_id,
                    i.reason           AS visit_reason,
                    i.location         AS i_location,
                    i.status           AS visit_status,
                    DATE(i.created_at) AS visit_date,
                    MIN(CASE WHEN vcl.action = 'checkin' THEN vcl.scanned_at ELSE NULL END) AS checkin_time,
                    MAX(CASE WHEN vcl.action = 'checkout' THEN vcl.scanned_at ELSE NULL END) AS checkout_time,
                    MIN(iv.check_in_time)  AS reg_checkin_time,
                    MAX(iv.check_out_time) AS reg_checkout_time,
                    COUNT(CASE WHEN vcl.action != 'assigned' THEN vcl.id END) AS total_scans,
                    (SELECT MAX(s.date_to) FROM invitation_schedules s WHERE s.invitation_id = i.id) as schedule_end,
                    (
                        SELECT GROUP_CONCAT(DISTINCT COALESCE(sl_vl.name, sl_vd.name) ORDER BY COALESCE(sl_vl.name, sl_vd.name) SEPARATOR ', ')
                        FROM visitor_card_logs vcl2
                        LEFT JOIN lanes l2      ON l2.id              = vcl2.lane_id
                        LEFT JOIN sub_locations sl_vl ON sl_vl.location_id = l2.location_id
                        LEFT JOIN sub_locations sl_vd ON sl_vd.id          = vcl2.sub_location_id
                        WHERE vcl2.invitation_id = i.id
                          AND vcl2.action != 'assigned'
                          AND COALESCE(sl_vl.name, sl_vd.name) IS NOT NULL
                    ) AS all_lanes,
                    (
                        SELECT COALESCE(sl_ll.name, sl_ld.name)
                        FROM visitor_card_logs vcl3
                        LEFT JOIN lanes l3      ON l3.id              = vcl3.lane_id
                        LEFT JOIN sub_locations sl_ll ON sl_ll.location_id = l3.location_id
                        LEFT JOIN sub_locations sl_ld ON sl_ld.id          = vcl3.sub_location_id
                        WHERE vcl3.invitation_id = i.id
                          AND vcl3.action != 'assigned'
                          AND COALESCE(sl_ll.name, sl_ld.name) IS NOT NULL
                        ORDER BY vcl3.scanned_at DESC, vcl3.id DESC
                        LIMIT 1
                    ) AS last_lane_full
                FROM invitations i
                LEFT JOIN visitor_card_logs vcl ON vcl.invitation_id = i.id
                LEFT JOIN invitation_visitors iv ON iv.invitation_id = i.id
                WHERE 1=1" . $where . "
                GROUP BY
                    i.id, i.full_name, i.contact, i.ic_passport,
                    i.company, i.invited_by, i.staff_id, i.reason,
                    i.location, i.status, DATE(i.created_at)
                ORDER BY DATE(i.created_at) ASC, i.full_name ASC";

        $rows = $db->query($sql)->getResultArray();

        $visitors = [];
        $completedCount = 0;
        $activeCount = 0;
        $todayVisitors = 0;
        $todayStr = date('Y-m-d');

        foreach ($rows as $row) {
            $checkInSource = $row['reg_checkin_time'] ?: $row['checkin_time'];
            $checkOutSource = $row['reg_checkout_time'] ?: $row['checkout_time'];

            $checkinTimeStr = $checkInSource ? date('g:i A', strtotime((string) $checkInSource)) : '-';
            $checkoutTimeStr = $checkOutSource ? date('g:i A', strtotime((string) $checkOutSource)) : '-';
            
            $durationStr = '-';
            if ($checkInSource) {
                $start = strtotime((string) $checkInSource);
                $end = $checkOutSource ? strtotime((string) $checkOutSource) : time();
                $diff = max(0, $end - $start);
                
                // Dashboard logic: if overstaying, show '+' duration relative to schedule end
                $schedEnd = !empty($row['schedule_end']) ? strtotime((string) $row['schedule_end']) : null;
                $now = time();
                if (!$checkOutSource && $schedEnd && $now > $schedEnd) {
                    $overDiff = $now - $schedEnd;
                    $overHours = floor($overDiff / 3600);
                    $overMins = floor(($overDiff % 3600) / 60);
                    $durationStr = sprintf("+%02d:%02d h", $overHours, $overMins);
                } else {
                    $hours = floor($diff / 3600);
                    $mins = floor(($diff % 3600) / 60);
                    $durationStr = sprintf("%02d:%02d h", $hours, $mins) . ($checkOutSource ? '' : ' (ongoing)');
                }
            }
            
            if ($checkOutSource) {
                $completedCount++;
                $visitStatus = 'Completed';
                $currentLocation = 'Out';
            } else {
                $activeCount++;
                $visitStatus = 'Active';
                $currentLocation = $row['last_lane_full'] ?? $row['location'] ?? 'N/A';
            }
            
            if ($row['visit_date'] === $todayStr) {
                $todayVisitors++;
            }
            
            $locationAccessed = $row['all_lanes'] ?? 'N/A';

            $visitors[] = [
                'visitor_name'      => $row['visitor_name']    ?? 'N/A',
                'contact_no'        => $row['contact_no']      ?? 'N/A',
                'ic_no'             => $row['ic_no']           ?? 'N/A',
                'visitor_company'   => $row['visitor_company'] ?? 'N/A',
                'person_visited'    => $row['person_visited']  ?? 'N/A',
                'staff_id'          => $row['staff_id']        ?? 'N/A',
                'visit_reason'      => $row['visit_reason']    ?? 'N/A',
                'visit_status'      => $visitStatus,
                'visit_date'        => $row['visit_date']      ? date('M j, Y', strtotime($row['visit_date'])) : '-',
                'checkin_time'      => $checkinTimeStr,
                'checkout_time'     => $checkoutTimeStr,
                'duration'          => $durationStr,
                'current_location'  => $currentLocation,
                'location_accessed' => $locationAccessed,
            ];
        }

        return $this->response->setJSON([
            'success'         => true,
            'visitors'        => $visitors,
            'total_visitors'  => count($visitors),
            'completed'       => $completedCount,
            'active_visitors' => $activeCount,
            'today_visitors'  => $todayVisitors,
        ]);
    }
}