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
     * Note: this reports off the `vendors` table only (application status +
     * pass_expiry). KPK's real VendorInPremise / VendorOutOfWindow reports
     * are driven by live gate-scan logs, which VMS doesn't have wired up for
     * vendors yet — that's a separate follow-on (would need a vendor_card_logs
     * table + kiosk scan endpoint, same shape as visitor_card_logs).
     */
    public function generate()
    {
        $db = \Config\Database::connect();

        $from   = trim((string) ($this->request->getPost('from') ?? $this->request->getGet('from') ?? ''));
        $to     = trim((string) ($this->request->getPost('to') ?? $this->request->getGet('to') ?? ''));
        $status = trim((string) ($this->request->getPost('status') ?? $this->request->getGet('status') ?? 'all'));

        if ($from === '' || $to === '') {
            $to   = date('Y-m-d');
            $from = date('Y-m-d', strtotime('-30 days'));
        }

        helper('role');
        $builder = $db->table('vendors')
            ->where('DATE(created_at) >=', $from)
            ->where('DATE(created_at) <=', $to);

        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }

        if ($status !== 'all') {
            $builder->where('status', $status);
        }

        $rows = $builder->orderBy('created_at', 'DESC')->limit(2000)->get()->getResultArray();
        $truncated = count($rows) >= 2000;

        $today = date('Y-m-d');
        $counts = ['total' => 0, 'active' => 0, 'expiring_soon' => 0, 'expired' => 0, 'not_issued' => 0];

        $vendors = [];
        foreach ($rows as $row) {
            $counts['total']++;

            $passValidity = 'Not Issued';
            if (!empty($row['pass_expiry'])) {
                $daysLeft = (strtotime($row['pass_expiry']) - strtotime($today)) / 86400;
                if ($daysLeft < 0) {
                    $passValidity = 'Expired';
                    $counts['expired']++;
                } elseif ($daysLeft <= 30) {
                    $passValidity = 'Expiring Soon';
                    $counts['expiring_soon']++;
                } else {
                    $passValidity = 'Active';
                    $counts['active']++;
                }
            } else {
                $counts['not_issued']++;
            }

            $vendors[] = [
                'app_no'              => $row['app_no'] ?? 'N/A',
                'date_of_application' => $row['date_of_application'] ?? ($row['created_at'] ? date('d/m/Y', strtotime($row['created_at'])) : '-'),
                'full_name'           => $row['full_name'] ?? 'N/A',
                'ic_no_masked'        => mask_ic_passport($row['ic_no'] ?: $row['passport_no'] ?? '', 'N/A'),
                'vendor_company_name' => $row['vendor_company_name'] ?? 'N/A',
                'designation'         => $row['designation'] ?? '-',
                'status'              => $row['status'] ?? 'Pending',
                'pass_expiry'         => $row['pass_expiry'] ? date('d/m/Y', strtotime($row['pass_expiry'])) : '-',
                'pass_validity'       => $passValidity,
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
