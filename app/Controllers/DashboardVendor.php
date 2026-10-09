<?php

namespace App\Controllers;

use App\Libraries\VendorDashboardStats;

/**
 * Vendor dashboard — the vendor-pass pipeline (Draft → Pending → Process →
 * Printing → Issuance → Active) with expiring passes and who needs action.
 *
 * Staff see every vendor company of their client. A vendor company account
 * (role vendor_admin) sees only its own company, and is shown quick actions
 * instead of the staff pipeline links. Numbers: App\Libraries\VendorDashboardStats.
 */
class DashboardVendor extends BaseController
{
    public function index()
    {
        helper(['dashboard_nav', 'dashboard_chart', 'access', 'feature', 'vendor_company']);

        if (! dashboard_tab_allowed('vendor')) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not allowed to view the vendor dashboard.');
        }

        $isVendorAccount = is_vendor_admin();
        $clientId        = is_platform_superadmin() ? null : (int) current_company_id();
        $regNo           = null;
        $locked          = false;

        if ($isVendorAccount) {
            $company = current_vendor_company();
            $regNo   = trim((string) ($company['registration_no'] ?? ''));
            $locked  = ($regNo === '');
            $regNo   = $locked ? '' : $regNo;
        }

        $stats = VendorDashboardStats::collect(\Config\Database::connect(), $clientId, $regNo, $locked);

        return view('dashboard/vendor', $stats + [
            'pageTitle'       => 'Vendor Dashboard - SafeG',
            'currentDate'     => date('M j, Y'),
            'isVendorAccount' => $isVendorAccount,
            'companyName'     => $isVendorAccount ? (string) ($company['name'] ?? $company['company_name'] ?? '') : '',
            'locked'          => $locked,
            'canAdd'          => has_access('vendor_pass_list', 'edit'),
        ]);
    }
}
