<?php

namespace App\Libraries;

class PermissionRegistry
{
    /**
     * Define all system permissions grouped by category.
     * Keys should be unique across the entire system.
     */
    public static function getPermissions(): array
    {
        return [
            'Dashboard' => [
                'dash_main_menu'       => 'Main Menu',
                'dash_visitor_pass'     => 'Visitor Pass',
                'dash_emergency'        => 'Emergency',
                'dash_vehicle'          => 'Vehicle',
                'dash_vendor_pass'      => 'Vendor Pass',
                'dash_gate_monitoring'  => 'Gate Monitoring',
                'dash_student'          => 'Student',
                'dash_visitor_dash'     => 'Visitor Dashboard',
                'dash_security_dash'    => 'Security Dashboard',
            ],
            'Mobile App' => [
                'mobile_login'          => 'Login',
            ],
            'Vendor Pass' => [
                'vendor_my_pass'        => 'My Pass',
                'vendor_staff'          => 'Staff',
                'vendor_student'        => 'Student',
                'vendor_visitor_invite' => 'Visitor Pass Invitation List',
                'vendor_visitor_req'    => 'Visitor Pass Request List',
                'vendor_visitor_list'   => 'Visitor Pass List',
                'vendor_pass_req_list'  => 'Vendor Pass Request List',
                'vendor_pass_reject'    => 'Vendor Pass Reject List',
                'vendor_pass_approve'   => 'Vendor Pass Approved List',
                'vendor_pass_urine'     => 'Vendor Pass Urine Test',
                'vendor_pass_suspend'   => 'Vendor Pass Suspend List',
                'vendor_pass_process'   => 'Vendor Pass Process List',
                'vendor_pass_issuance'  => 'Vendor Pass Issuance List',
                'vendor_pass_closed'    => 'Vendor Pass Closed List',
                'vendor_pass_list'      => 'Vendor Pass List',
                'vendor_search'         => 'Search Vendor Pass',
                'vendor_dev_tools'      => 'Developer Tools',
                'vendor_hse_calendar'   => 'HSE & Security Training Calendar',
                'vendor_hse_edit'       => 'Edit Training',
                'vendor_hse_attendance' => 'Mark Attendance',
                'vendor_hse_delete'     => 'Delete Training',
                'vendor_online_train'   => 'Online Training',
                'vendor_change_comp'    => 'Change Company',
            ],
            'Blacklist' => [
                'blacklist_ind_req'     => 'Blacklist Individual Request List',
                'blacklist_ind_approve' => 'Approval for Release',
                'blacklist_ind_closed'  => 'Blacklist Individual Closed List',
                'blacklist_ind_release' => 'Request for Release',
                'blacklist_comp_req'    => 'Blacklist Company Request List',
                'blacklist_comp_approve'=> 'Approval for Entry & Release',
                'blacklist_comp_closed' => 'Blacklist Company Closed List',
                'blacklist_comp_release'=> 'Request for Release',
            ],
            'Vehicle' => [
                'vehicle_public_my'      => 'My Vehicle Pass',
                'vehicle_public_daily'   => 'My Vehicle Daily Pass',
                'vehicle_public_req'     => 'Daily Pass Request List',
                'vehicle_public_reject'  => 'Daily Pass Reject List',
                'vehicle_public_closed'  => 'Daily Pass Closed List',
                'vehicle_public_staff'   => 'Staff Vehicle Pass',
                'vehicle_public_new'     => 'New Staff Request',
                'vehicle_public_list'    => 'Reject List',
                'vehicle_public_delete'  => 'Delete Application',
                'vehicle_cargo_my'       => 'My Vehicle Pass',
                'vehicle_cargo_reject'   => 'Reject List',
                'vehicle_cargo_req'      => 'Request List',
                'vehicle_cargo_approve'  => 'Approved List',
                'vehicle_cargo_closed'   => 'Closed List',
                'vehicle_transfer'       => 'Transfer Ownership',
            ],
            'Monitoring' => [
                'monitor_gate_io'        => 'Gate In/Out',
                'monitor_hold_reject'    => 'On Hold / Reject Gate In/Out',
                'monitor_active'         => 'Monitoring',
            ],
            'Attendance' => [
                'attend_staff'           => 'Staff List',
                'attend_vendor'          => 'Vendor List',
                'attend_student'         => 'Student List',
            ],
            'Worker KPI' => [
                'worker_reg'             => 'Worker Registration',
                'worker_req'             => 'Request List',
                'worker_closed'          => 'Closed List',
                'worker_zone'            => 'Zone',
                'worker_camera'          => 'Camera',
                'worker_url'             => 'URL Setup',
                'worker_project'         => 'Project',
                'worker_project_worker'  => 'Worker Project',
            ],
            'Report' => [
                'report_gate_io'         => 'Gate In/Out',
                'report_out_window'      => 'Out Window List',
                'report_port_pass_mon'   => 'Port Pass Monthly Report',
                'report_port_pass_sum'   => 'Port Pass Summary Report',
                'report_comp_ageing'     => 'Company Permit Ageing Report',
                'report_comp_permit_mon' => 'Company Permit Monthly Report',
                'report_vehicle_sum'     => 'Vehicle Sticker Summary Report',
                'report_blacklist'       => 'Blacklist Report',
                'report_attendance'      => 'Attendance Report',
                'report_visitor'         => 'Visitor Report',
                'report_monitoring'      => 'Monitoring Report',
                'report_access'          => 'ACCESS REPORT',
                'report_visitor_detail'  => 'VISITOR DETAIL & CHRONOLOGY',
                'report_visitor_info'    => 'VISITOR INFO BY DOOR',
            ],
            'Config' => [
                'config_role'            => 'Role Info',
                'config_user'            => 'User Info',
                'config_comp'            => 'Company Info',
                'config_vehicle_alloc'   => 'Vehicle Allocation',
                'config_country'         => 'Country Info',
                'config_state'           => 'State Info',
                'config_city'            => 'City Info',
                'config_dept'            => 'Department Info',
                'config_desig'           => 'Designation Info',
                'config_loc_access'      => 'Location Access Info',
                'config_sub_loc'         => 'Sub Location Access Info',
                'config_lane'            => 'Lane Info',
                'config_reg_type'        => 'Registration Type',
                'config_biz_type'        => 'Business Type',
                'config_reject_reason'   => 'Reject Reason',
                'config_blacklist_reason'=> 'Blacklist Reason',
                'config_release_reason'  => 'Release Reason',
                'config_vehicle_type'    => 'Vehicle Type',
                'config_license_class'   => 'License Class',
                'config_bank'            => 'Bank Name',
                'config_sub_biz'         => 'Sub Business Type',
                'config_card_template'   => 'Card Template - Preprint',
                'config_loc_visited'     => 'Location Visited',
                'config_announcement'    => 'Announcement Info',
                'config_dash_upload'     => 'Dashboard Upload Info',
                'config_cargo_type'      => 'Cargo Type',
                'config_warehouse'       => 'Warehouse Location',
                'config_visitor_card'    => 'Visitor Card',
                'config_meeting'         => 'Meeting Session',
                'config_app_config'      => 'App Config',
                'config_inspect_temp'    => 'Inspection Template',
                'config_offence_rate'    => 'Offence Rate',
                'config_shift'           => 'Shift',
                'config_permit_type'     => 'Additional Permit Type',
                'config_module'          => 'Module',
                'config_op_hours'        => 'Operating Hours',
                'config_evacuation'      => 'Evacuation',
                'config_staff_group'     => 'Staff Group',
                'config_group_shift'     => 'Group Shift',
                'config_staff_avail'     => 'Staff Availability',
                'config_branch'          => 'Branch Office',
                'config_holidays'        => 'Public Holidays',
                'config_visitor_qr'      => 'Visitor QR Code',
                'config_video_quest'     => 'Video & Questionnaire',
                'config_visitor_type'    => 'Visitor Type',
                'config_pathway'         => 'Pathway',
                'config_devices'         => 'Devices',
                'config_security_pri'    => 'Security Priority',
            ],
            'Sync Data' => [
                'sync_data'              => 'Sync Data',
            ]
        ];
    }

    /**
     * Flattened list of all permission keys.
     */
    public static function getAllKeys(): array
    {
        $keys = [];
        foreach (self::getPermissions() as $category => $perms) {
            foreach ($perms as $key => $label) {
                $keys[] = $key;
            }
        }
        return $keys;
    }
}
