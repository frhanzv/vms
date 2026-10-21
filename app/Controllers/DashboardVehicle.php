<?php

namespace App\Controllers;

use App\Libraries\VehicleDashboardStats;

/**
 * Dashboard > Vehicle (KPK dashboard-vehicle): today's registered vehicles and gate in / out,
 * vehicles entering per month, and the duration of stay table. Numbers: VehicleDashboardStats.
 */
class DashboardVehicle extends BaseController
{
    public function index()
    {
        helper(['dashboard_nav', 'access', 'feature', 'role']);

        if (! dashboard_tab_allowed('vehicle')) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not allowed to view the vehicle dashboard.');
        }

        $clientId = is_platform_superadmin() ? (int) $this->request->getGet('client_id') : (int) current_client_id();
        $perPage  = (int) $this->request->getGet('per_page');
        $perPage  = in_array($perPage, [10, 20, 30, 40], true) ? $perPage : 10;

        $stats = VehicleDashboardStats::collect(\Config\Database::connect(), $clientId, (int) $this->request->getGet('page'), $perPage, mb_substr(trim((string) $this->request->getGet('search')), 0, 60));

        return view('dashboard/vehicle', $stats + [
            'pageTitle'   => 'Vehicle Dashboard - SafeG',
            'currentDate' => date('M j, Y'),
        ]);
    }
}
