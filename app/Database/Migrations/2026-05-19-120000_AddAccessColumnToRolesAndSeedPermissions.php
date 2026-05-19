<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccessColumnToRolesAndSeedPermissions extends Migration
{
    public function up()
    {
        // Add access column if it doesn't exist
        if (!$this->db->fieldExists('access', 'roles')) {
            $this->forge->addColumn('roles', [
                'access' => [
                    'type' => 'JSON',
                    'null' => true,
                    'after' => 'status',
                ],
            ]);
        }

        // Default permissions — all enabled
        $defaultAccess = [
            'dashboard' => [
                'main_menu' => true,
                'visitor_pass' => true,
                'emergency' => true,
                'vehicle' => true,
                'vendor_pass' => true,
                'gate_monitoring' => true,
                'student' => true,
                'visitor_dashboard' => true,
                'security_dashboard' => true,
            ],
            'mobile_app' => [
                'login' => true,
            ],
            'vendor_pass' => [
                'my_pass' => true,
                'staff' => true,
                'student' => true,
                'visitor_pass_invitation_list' => true,
                'visitor_pass_request_list' => true,
                'visitor_pass_list' => true,
                'request_list' => true,
                'reject_list' => true,
                'approved_list' => true,
                'urine_test' => true,
                'suspend_list' => true,
                'process_list' => true,
                'issuance_list' => true,
                'closed_list' => true,
                'vendor_pass_list' => true,
                'search' => true,
                'developer_tools' => true,
                'hse_training_calendar' => true,
                'edit_training' => true,
                'mark_attendance' => true,
                'delete_training' => true,
                'online_training' => true,
                'change_company' => true,
            ],
            'blacklist' => [
                'individual_request_list' => true,
                'approval_for_release' => true,
                'individual_closed_list' => true,
                'request_for_release' => true,
                'company_request_list' => true,
                'approval_for_entry_release' => true,
                'company_closed_list' => true,
                'company_request_for_release' => true,
            ],
            'vehicle' => [
                'my_vehicle_pass' => true,
                'my_vehicle_daily_pass' => true,
                'daily_pass_request_list' => true,
                'daily_pass_reject_list' => true,
                'daily_pass_closed_list' => true,
                'staff_vehicle_pass' => true,
                'new_staff_request' => true,
                'public_reject_list' => true,
                'public_reject_delete' => true,
                'public_request_list' => true,
                'public_approved_list' => true,
                'public_closed_list' => true,
                'public_closed_delete' => true,
                'cargo_my_vehicle_pass' => true,
                'cargo_reject_list' => true,
                'cargo_reject_delete' => true,
                'cargo_request_list' => true,
                'cargo_approved_list' => true,
                'cargo_closed_list' => true,
                'cargo_closed_delete' => true,
                'transfer_ownership' => true,
            ],
            'monitoring' => [
                'gate_in_out' => true,
                'on_hold_reject' => true,
                'monitoring' => true,
            ],
            'attendance' => [
                'staff_list' => true,
                'vendor_list' => true,
                'student_list' => true,
            ],
            'worker_kpi' => [
                'worker_registration' => true,
                'request_list' => true,
                'closed_list' => true,
                'zone' => true,
                'camera' => true,
                'url_setup' => true,
                'project' => true,
                'worker_project' => true,
            ],
            'visitor_pass_list' => [
                'invitations' => true,
                'request_list' => true,
                'visitors_list' => true,
            ],
            'staff_pass_list' => [
                'view' => true,
            ],
            'visitor_workflow' => [
                'view' => true,
            ],
            'report' => [
                'gate_in_out' => true,
                'gate_in_out_export' => true,
                'delete_gate_in' => true,
                'out_window_list' => true,
                'out_window_export' => true,
                'port_pass_monthly' => true,
                'port_pass_monthly_generate' => true,
                'port_pass_summary' => true,
                'company_permit_ageing' => true,
                'company_permit_monthly' => true,
                'vehicle_sticker_summary' => true,
                'blacklist_report' => true,
                'blacklist_report_export' => true,
                'attendance_report' => true,
                'visitor_report' => true,
                'monitoring_report' => true,
                'access_report' => true,
                'visitor_chronology' => true,
                'visitor_info_by_door' => true,
            ],
            'settings' => [
                'view' => true,
            ],
            'config' => [
                'alert_priority' => true,
                'api_management' => true,
                'general_settings' => true,
                'application_settings' => true,
                'email_notification_recipients' => true,
                'registration_type' => true,
                'business_type' => true,
                'blacklist_reason' => true,
                'role_management' => true,
                'user_management' => true,
                'company' => true,
                'sub_company' => true,
                'country' => true,
                'state' => true,
                'city' => true,
                'department' => true,
                'designation' => true,
                'location' => true,
                'lane' => true,
                'reject_reason' => true,
            ],
        ];

        $accessJson = json_encode($defaultAccess);

        // Update all roles that have empty/null or incomplete access data
        $this->db->query(
            "UPDATE roles SET access = ? WHERE access IS NULL OR access = '' OR access = 'null' OR access = '[]' OR access = '{}' OR LENGTH(access) < 100",
            [$accessJson]
        );
    }

    public function down()
    {
        // Don't drop the column on rollback — just clear the seeded data
        $this->db->table('roles')
            ->update(['access' => null]);
    }
}
