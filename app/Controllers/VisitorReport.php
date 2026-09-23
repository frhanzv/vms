<?php

namespace App\Controllers;

use App\Models\MobileKioskSettingModel;

class VisitorReport extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Visitor Report - SafeG',
            'visitorReportColumns' => $this->visitorReportColumnConfig(),
        ];

        return view('reports/visitor_report', $data);
    }

    private function visitorReportColumnConfig(): array
    {
        $defaults = array_fill_keys([
            'no', 'date', 'full_name', 'ic_passport', 'contact', 'company',
            'current_location', 'location_accessed', 'check_in', 'check_out',
            'purpose', 'duration', 'host', 'status',
        ], true);

        $clientId = current_client_id();
        $config = (new MobileKioskSettingModel())->getClientConfigMap($clientId > 0 ? $clientId : null);
        $saved = ! empty($config['visitor_report_columns'])
            ? json_decode((string) $config['visitor_report_columns'], true)
            : null;

        if (is_array($saved)) {
            foreach ($defaults as $key => $value) {
                if (array_key_exists($key, $saved)) {
                    $defaults[$key] = (bool) $saved[$key];
                }
            }
        }

        return $defaults;
    }

    public function saveColumnSettings()
    {
        $keys = [
            'no', 'date', 'full_name', 'ic_passport', 'contact', 'company',
            'current_location', 'location_accessed', 'check_in', 'check_out',
            'purpose', 'duration', 'host', 'status',
        ];
        $posted = $this->request->getPost('visitor_report_columns');
        $posted = is_array($posted) ? $posted : [];
        $columns = [];
        foreach ($keys as $key) {
            $columns[$key] = array_key_exists($key, $posted);
        }

        $clientId = current_client_id();
        (new MobileKioskSettingModel())->saveClientSetting(
            $clientId > 0 ? $clientId : null,
            'visitor_report_columns',
            json_encode($columns)
        );

        return redirect()->to(base_url('report/visitor'))
            ->with('success', 'Visitor report columns updated!');
    }

    public function generate()
    {
        $db = \Config\Database::connect();

        $from = trim((string) ($this->request->getPost('from') ?? $this->request->getGet('from') ?? ''));
        $to   = trim((string) ($this->request->getPost('to') ?? $this->request->getGet('to') ?? ''));

        if ($from === '' || $to === '') {
            $to   = date('Y-m-d');
            $from = date('Y-m-d', strtotime('-30 days'));
        }

        $where = " AND DATE(COALESCE(iv.check_in_time, i.created_at)) BETWEEN " . $db->escape($from) . " AND " . $db->escape($to);
        $where .= $this->hostReportWhereSql($db);

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
                    i.registration_source,
                    DATE(COALESCE(iv.check_in_time, i.created_at)) AS visit_date,
                    MIN(CASE WHEN vcl.action = 'checkin' THEN vcl.scanned_at ELSE NULL END) AS checkin_time,
                    MAX(CASE WHEN vcl.action = 'checkout' THEN vcl.scanned_at ELSE NULL END) AS checkout_time,
                    iv.id AS visitor_row_id,
                    iv.check_in_time  AS reg_checkin_time,
                    iv.check_out_time AS reg_checkout_time,
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
                    i.id, iv.id, iv.check_in_time, iv.check_out_time,
                    i.full_name, i.contact, i.ic_passport,
                    i.company, i.invited_by, i.staff_id, i.reason,
                    i.location, i.status, i.registration_source, DATE(i.created_at)
                ORDER BY COALESCE(iv.check_in_time, i.created_at) DESC, i.id DESC, iv.id DESC
                LIMIT 2000";

        $rows = $db->query($sql)->getResultArray();
        $truncated = count($rows) >= 2000;

        $visitors = [];
        $walkInVisitors = 0;
        $invitationVisitors = 0;
        $expectedVisitors = 0;

        foreach ($rows as $row) {
            [$checkInSource, $checkOutSource] = $this->resolveVisitCycleTimes($row);
            $isInvitation = strcasecmp(trim((string) ($row['registration_source'] ?? '')), 'Invitation') === 0;

            if ($checkInSource) {
                if ($isInvitation) {
                    $invitationVisitors++;
                } else {
                    $walkInVisitors++;
                }
            } elseif ($isInvitation && strcasecmp((string) ($row['visit_status'] ?? ''), 'Approved') === 0) {
                $expectedVisitors++;
            }

            $checkinTimeStr = $checkInSource ? date('g:i A', strtotime((string) $checkInSource)) : '-';
            $checkoutTimeStr = $checkOutSource ? date('g:i A', strtotime((string) $checkOutSource)) : '-';
            
            $durationStr = '-';
            if ($checkInSource) {
                $start = strtotime((string) $checkInSource);
                $end = $checkOutSource ? strtotime((string) $checkOutSource) : time();
                $diff = max(0, $end - $start);

                // Duration always means total elapsed visit time. Whether the
                // visit is still active is already represented by Status.
                $hours = floor($diff / 3600);
                $mins = floor(($diff % 3600) / 60);
                $durationStr = sprintf("%02d:%02d h", $hours, $mins);
            }
            
            if ($checkOutSource) {
                $visitStatus = 'Completed';
                $currentLocation = 'Out';
            } elseif ($checkInSource) {
                $visitStatus = 'Active';
                $currentLocation = $row['last_lane_full'] ?? $row['location'] ?? 'N/A';
            } else {
                $visitStatus = $isInvitation ? 'Expected' : 'Pending';
                $currentLocation = 'N/A';
            }
            
            $locationAccessed = $row['all_lanes'] ?? 'N/A';

            $visitors[] = [
                'visitor_name'      => $row['visitor_name']    ?? 'N/A',
                'contact_no'        => $row['contact_no']      ?? 'N/A',
                'ic_no'             => $row['ic_no'] ?? 'N/A',
                'ic_no_masked'      => mask_ic_passport($row['ic_no'] ?? '', 'N/A'),
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
            'total_visitors'       => $walkInVisitors + $invitationVisitors,
            'walk_in_visitors'     => $walkInVisitors,
            'invitation_visitors'  => $invitationVisitors,
            'expected_visitors'    => $expectedVisitors,
            'date_from'       => $from,
            'date_to'         => $to,
            'truncated'       => $truncated,
            'message'         => $truncated ? 'Results limited to 2000 rows. Narrow the date range for full data.' : null,
        ]);
    }

    /**
     * Resolve one paired entry cycle without borrowing a timestamp from another cycle.
     *
     * Current records store each cycle in invitation_visitors. Event-log aggregates are
     * retained only as a fallback for legacy invitations that have no cycle row.
     *
     * @return array{0:?string,1:?string}
     */
    private function resolveVisitCycleTimes(array $row): array
    {
        $hasCycleRow = ! empty($row['visitor_row_id']);
        $checkIn = $hasCycleRow
            ? ($row['reg_checkin_time'] ?: null)
            : ($row['checkin_time'] ?: null);
        $checkOut = $hasCycleRow
            ? ($row['reg_checkout_time'] ?: null)
            : ($row['checkout_time'] ?: null);

        if ($checkIn && $checkOut) {
            $checkInTimestamp = strtotime((string) $checkIn);
            $checkOutTimestamp = strtotime((string) $checkOut);
            if ($checkInTimestamp !== false
                && $checkOutTimestamp !== false
                && $checkOutTimestamp < $checkInTimestamp) {
                $checkOut = null;
            }
        }

        return [$checkIn, $checkOut];
    }

    private function hostReportWhereSql($db): string
    {
        helper('role');
        if (! role_matches(session()->get('role'), 'host')) {
            return '';
        }

        $refs = array_values(array_unique(array_filter([
            trim((string) session()->get('staff_id')),
            trim((string) session()->get('username')),
            trim((string) session()->get('full_name')),
            trim((string) session()->get('email')),
        ], static fn($v) => $v !== '')));

        if ($refs === []) {
            return ' AND 1 = 0';
        }

        $parts = [];
        foreach ($refs as $ref) {
            $escaped = $db->escape($ref);
            $parts[] = "i.staff_id = {$escaped}";
            $parts[] = "i.invited_by = {$escaped}";
        }

        return ' AND (' . implode(' OR ', $parts) . ')';
    }
}
