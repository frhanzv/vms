<?php

namespace App\Controllers;

use App\Libraries\StaffDashboardStats;

/**
 * Staff dashboard — the staff-pass side of the system on its own page: how
 * many staff, their status, pass cards (active / expiring / expired),
 * documents about to expire, and who needs attention. The numbers come from
 * App\Libraries\StaffDashboardStats.
 */
class DashboardStaff extends BaseController
{
    public function index()
    {
        helper(['dashboard_nav', 'dashboard_chart', 'access', 'feature']);

        if (! dashboard_tab_allowed('staff')) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not allowed to view the staff dashboard.');
        }

        $stats = StaffDashboardStats::collect(\Config\Database::connect());

        return view('dashboard/staff', $stats + [
            'pageTitle'   => 'Staff Dashboard - SafeG',
            'currentDate' => date('M j, Y'),
            'canAddStaff' => has_access('staff_pass_list', 'edit'),
        ]);
    }
}
