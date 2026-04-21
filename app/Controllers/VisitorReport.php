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
                    COUNT(vcl.id)       AS total_scans,
                    (SELECT CONCAT(l.id, '. ', l.lane) FROM visitor_card_logs vcl2 LEFT JOIN lanes l ON l.id = vcl2.lane_id WHERE vcl2.invitation_id = i.id ORDER BY vcl2.scanned_at DESC LIMIT 1) AS last_lane_full,
                    (SELECT GROUP_CONCAT(DISTINCT CONCAT(l.id, '. ', l.lane) SEPARATOR ', ') FROM visitor_card_logs vcl2 LEFT JOIN lanes l ON l.id = vcl2.lane_id WHERE vcl2.invitation_id = i.id) AS all_lanes
                FROM invitations i
                LEFT JOIN visitor_card_logs vcl ON vcl.invitation_id = i.id
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
            $checkinTimeStr = $row['checkin_time'] ? date('H:i:s', strtotime($row['checkin_time'])) : '-';
            $checkoutTimeStr = $row['checkout_time'] ? date('H:i:s', strtotime($row['checkout_time'])) : '-';
            
            $durationStr = '-';
            if ($row['checkin_time'] && $row['checkout_time']) {
                $diff = strtotime($row['checkout_time']) - strtotime($row['checkin_time']);
                if ($diff > 0) {
                    $hours = floor($diff / 3600);
                    $mins = floor(($diff / 60) % 60);
                    $durationStr = sprintf("%02d:%02d h", $hours, $mins);
                } else {
                    $durationStr = "00:00 h";
                }
            }
            
            if ($row['checkout_time']) {
                $completedCount++;
                $visitStatus = 'Completed';
                $currentLocation = 'Out';
            } else {
                $activeCount++;
                $visitStatus = 'Active';
                $currentLocation = $row['last_lane_full'] ?? 'N/A';
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
                'visit_date'        => $row['visit_date']      ? date('Y-m-d', strtotime($row['visit_date'])) : '-',
                'checkin_time'      => $checkinTimeStr,
                'checkout_time'     => $checkoutTimeStr,
                'duration'          => $durationStr,
                'current_location'  => $currentLocation,
                'location_accessed' => $locationAccessed,
                'total_scans'       => (int) $row['total_scans'],
            ];
        }

        return $this->response->setJSON([
            'success'         => true,
            'total_visitors'  => count($visitors),
            'completed'       => $completedCount,
            'active_visitors' => $activeCount,
            'today_visitors'  => $todayVisitors,
            'visitors'        => $visitors,
        ]);
    }
}
