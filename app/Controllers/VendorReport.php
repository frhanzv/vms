<?php

namespace App\Controllers;

class VendorReport extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'Vendor Report - SafeG',
        ];

        return view('reports/vendor_report', $data);
    }

    /**
     * Now backed by real gate data (vendor_visits / vendor_card_logs) instead
     * of just pass status — this is what makes it a genuine equivalent of
     * KPK's VendorInPremise / VendorOutOfWindow / VendorReport, merged into
     * one page with a Presence filter rather than three separate pages.
     */
    public function generate()
    {
        $db = \Config\Database::connect();

        $from     = trim((string) ($this->request->getPost('from') ?? $this->request->getGet('from') ?? ''));
        $to       = trim((string) ($this->request->getPost('to') ?? $this->request->getGet('to') ?? ''));
        $status   = trim((string) ($this->request->getPost('status') ?? $this->request->getGet('status') ?? 'all'));
        $presence = trim((string) ($this->request->getPost('presence') ?? $this->request->getGet('presence') ?? 'all'));

        if ($from === '' || $to === '') {
            $to   = date('Y-m-d');
            $from = date('Y-m-d', strtotime('-30 days'));
        }

        helper('role');

        $sql = "SELECT v.*,
                       MIN(vv.check_in_time)  AS first_checkin,
                       MAX(vv.check_out_time) AS last_checkout,
                       MAX(CASE WHEN vv.check_out_time IS NULL AND vv.check_in_time IS NOT NULL THEN 1 ELSE 0 END) AS is_on_site
                FROM vendors v
                LEFT JOIN vendor_visits vv ON vv.vendor_id = v.id
                WHERE DATE(v.created_at) BETWEEN ? AND ?";
        $params = [$from, $to];

        if (! is_platform_superadmin()) {
            $sql .= " AND v.company_id = ?";
            $params[] = current_company_id();
        }
        if ($status !== 'all') {
            $sql .= " AND v.status = ?";
            $params[] = $status;
        }

        $sql .= " GROUP BY v.id ORDER BY v.created_at DESC LIMIT 2000";

        $rows = $db->query($sql, $params)->getResultArray();
        $truncated = count($rows) >= 2000;

        $today = date('Y-m-d');
        $counts = ['total' => 0, 'in_premise' => 0, 'out_of_window' => 0, 'checked_out' => 0, 'not_yet_arrived' => 0];

        $vendors = [];
        foreach ($rows as $row) {
            $counts['total']++;

            $onSite = ((int) $row['is_on_site']) === 1;
            $passExpired = !empty($row['pass_expiry']) && strtotime($row['pass_expiry']) < strtotime($today);

            if ($onSite && $passExpired) {
                $presenceStatus = 'Out of Window'; // still on-site but past pass validity
                $counts['out_of_window']++;
            } elseif ($onSite) {
                $presenceStatus = 'In Premise';
                $counts['in_premise']++;
            } elseif (!empty($row['last_checkout'])) {
                $presenceStatus = 'Checked Out';
                $counts['checked_out']++;
            } else {
                $presenceStatus = 'Not Yet Arrived';
                $counts['not_yet_arrived']++;
            }

            if ($presence !== 'all' && $presenceStatus !== $presence) {
                continue;
            }

            $vendors[] = [
                'app_no'              => $row['app_no'] ?? 'N/A',
                'date_of_application' => $row['date_of_application'] ?? ($row['created_at'] ? date('d/m/Y', strtotime($row['created_at'])) : '-'),
                'full_name'           => $row['full_name'] ?? 'N/A',
                'ic_no_masked'        => mask_ic_passport($row['ic_no'] ?: $row['passport_no'] ?? '', 'N/A'),
                'vendor_company_name' => $row['vendor_company_name'] ?? 'N/A',
                'status'              => $row['status'] ?? 'Pending',
                'pass_expiry'         => $row['pass_expiry'] ? date('d/m/Y', strtotime($row['pass_expiry'])) : '-',
                'check_in'            => $row['first_checkin'] ? date('d/m/Y g:i A', strtotime($row['first_checkin'])) : '-',
                'check_out'           => $row['last_checkout'] ? date('d/m/Y g:i A', strtotime($row['last_checkout'])) : '-',
                'presence'            => $presenceStatus,
            ];
        }

        return $this->response->setJSON([
            'success'   => true,
            'vendors'   => $vendors,
            'counts'    => $counts,
            'date_from' => $from,
            'date_to'   => $to,
            'truncated' => $truncated,
            'message'   => $truncated ? 'Results limited to 2000 rows. Narrow the date range for full data.' : null,
        ]);
    }
}
