<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Curated LLM demo rows (12 visitors, 5 hosts, sample alerts).
 *
 * For full test dataset run: php spark db:seed LlmTestDataSeeder
 *
 * Run alone: php spark db:seed LlmDemoDataSeeder
 */
class LlmDemoDataSeeder extends Seeder
{
    private string $marker = 'LLM_DEMO_DATA';

    public function run()
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');

        foreach (['companies', 'staff', 'invitations', 'invitation_schedules', 'invitation_visitors', 'security_alerts'] as $table) {
            if (! $db->tableExists($table)) {
                echo "Missing table {$table}. Run migrations first.\n";
                return;
            }
        }

        $companies = [
            ['name' => 'LLM Demo Logistics', 'registration_no' => 'LLM-LOG-001', 'address' => 'Demo Warehouse Park', 'contact_no' => '+60311110001', 'email' => 'logistics.demo@example.com'],
            ['name' => 'LLM Demo Engineering', 'registration_no' => 'LLM-ENG-001', 'address' => 'Demo Engineering Block', 'contact_no' => '+60311110002', 'email' => 'engineering.demo@example.com'],
            ['name' => 'LLM Demo Catering', 'registration_no' => 'LLM-CAT-001', 'address' => 'Demo Cafeteria Wing', 'contact_no' => '+60311110003', 'email' => 'catering.demo@example.com'],
        ];

        foreach ($companies as $company) {
            if (! $db->table('companies')->where('registration_no', $company['registration_no'])->countAllResults()) {
                $db->table('companies')->insert($company + [
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                    'version' => 1,
                ]);
            }
        }

        $staffRows = [
            ['staff_no' => 'LLM-S001', 'full_name' => 'Ahmad Demo Host', 'department' => 'Reception', 'designation' => 'Front Desk Lead', 'contact_number' => '+60120001001', 'email' => 'ahmad.demo@safeg.test'],
            ['staff_no' => 'LLM-S002', 'full_name' => 'Nurul Demo Host', 'department' => 'Operations', 'designation' => 'Operations Manager', 'contact_number' => '+60120001002', 'email' => 'nurul.demo@safeg.test'],
            ['staff_no' => 'LLM-S003', 'full_name' => 'Ravi Demo Host', 'department' => 'Engineering', 'designation' => 'Facilities Engineer', 'contact_number' => '+60120001003', 'email' => 'ravi.demo@safeg.test'],
            ['staff_no' => 'LLM-S004', 'full_name' => 'Mei Ling Demo Host', 'department' => 'Security', 'designation' => 'Security Supervisor', 'contact_number' => '+60120001004', 'email' => 'meiling.demo@safeg.test'],
            ['staff_no' => 'LLM-S005', 'full_name' => 'Sarah Demo Host', 'department' => 'HR', 'designation' => 'HR Executive', 'contact_number' => '+60120001005', 'email' => 'sarah.demo@safeg.test'],
        ];

        foreach ($staffRows as $staff) {
            if (! $db->table('staff')->where('staff_no', $staff['staff_no'])->countAllResults()) {
                $db->table('staff')->insert($staff + [
                    'app_no' => 'APP-' . $staff['staff_no'],
                    'status' => 'Active',
                    'card_status' => 'Active',
                    'created_at' => $now,
                    'remark' => $this->marker,
                ]);
            }
        }

        $visits = [
            ['name' => 'Alicia Tan', 'ic' => 'LLM900101001', 'contact' => '+60170001001', 'company' => 'LLM Demo Logistics', 'host' => 'Ahmad Demo Host', 'staff_no' => 'LLM-S001', 'location' => 'Main Office - Reception Area', 'reason' => 'Delivery coordination', 'status' => 'Approved', 'from' => '+1 hour', 'to' => '+3 hours', 'visit_state' => 'expected'],
            ['name' => 'Benjamin Lee', 'ic' => 'LLM900101002', 'contact' => '+60170001002', 'company' => 'LLM Demo Engineering', 'host' => 'Ahmad Demo Host', 'staff_no' => 'LLM-S001', 'location' => 'North Branch - Lobby Entrance', 'reason' => 'Vendor briefing', 'status' => 'Approved', 'from' => '+2 hours', 'to' => '+5 hours', 'visit_state' => 'expected'],
            ['name' => 'Chandra Kumar', 'ic' => 'LLM900101003', 'contact' => '+60170001003', 'company' => 'LLM Demo Catering', 'host' => 'Nurul Demo Host', 'staff_no' => 'LLM-S002', 'location' => 'Cafeteria', 'reason' => 'Menu review', 'status' => 'Approved', 'from' => '+30 minutes', 'to' => '+4 hours', 'visit_state' => 'expected'],
            ['name' => 'Diana Wong', 'ic' => 'LLM900101004', 'contact' => '+60170001004', 'company' => 'LLM Demo Logistics', 'host' => 'Nurul Demo Host', 'staff_no' => 'LLM-S002', 'location' => 'Warehouse A - Loading', 'reason' => 'Inventory audit', 'status' => 'Approved', 'from' => '-2 hours', 'to' => '+3 hours', 'visit_state' => 'onsite'],
            ['name' => 'Ethan Lim', 'ic' => 'LLM900101005', 'contact' => '+60170001005', 'company' => 'LLM Demo Engineering', 'host' => 'Ravi Demo Host', 'staff_no' => 'LLM-S003', 'location' => 'Server Room', 'reason' => 'Network maintenance', 'status' => 'Approved', 'from' => '-1 hour', 'to' => '+2 hours', 'visit_state' => 'onsite'],
            ['name' => 'Farah Aziz', 'ic' => 'LLM900101006', 'contact' => '+60170001006', 'company' => 'LLM Demo Logistics', 'host' => 'Ahmad Demo Host', 'staff_no' => 'LLM-S001', 'location' => 'Main Entrance Gate', 'reason' => 'Courier handover', 'status' => 'Approved', 'from' => '-6 hours', 'to' => '-2 hours', 'visit_state' => 'overstay'],
            ['name' => 'Goh Wei Ming', 'ic' => 'LLM900101007', 'contact' => '+60170001007', 'company' => 'LLM Demo Engineering', 'host' => 'Ravi Demo Host', 'staff_no' => 'LLM-S003', 'location' => 'Office Level 2', 'reason' => 'Equipment calibration', 'status' => 'Approved', 'from' => '-5 hours', 'to' => '-1 hour', 'visit_state' => 'overstay'],
            ['name' => 'Hannah Chong', 'ic' => 'LLM900101008', 'contact' => '+60170001008', 'company' => 'LLM Demo Catering', 'host' => 'Sarah Demo Host', 'staff_no' => 'LLM-S005', 'location' => 'Cafeteria', 'reason' => 'Supplier meeting', 'status' => 'Approved', 'from' => '-7 hours', 'to' => '-5 hours', 'visit_state' => 'checkedout'],
            ['name' => 'Ivan Rahman', 'ic' => 'LLM900101009', 'contact' => '+60170001009', 'company' => 'LLM Demo Logistics', 'host' => 'Mei Ling Demo Host', 'staff_no' => 'LLM-S004', 'location' => 'Security Office', 'reason' => 'Incident review', 'status' => 'Approved', 'from' => '-4 hours', 'to' => '-2 hours', 'visit_state' => 'checkedout'],
            ['name' => 'Julia Nair', 'ic' => 'LLM900101010', 'contact' => '+60170001010', 'company' => 'LLM Demo Engineering', 'host' => 'Ahmad Demo Host', 'staff_no' => 'LLM-S001', 'location' => 'Workshop Phase 2', 'reason' => 'Site survey', 'status' => 'Pending', 'from' => '+1 day', 'to' => '+1 day +3 hours', 'visit_state' => 'future'],
            ['name' => 'Kevin Ong', 'ic' => 'LLM900101011', 'contact' => '+60170001011', 'company' => 'LLM Demo Logistics', 'host' => 'Nurul Demo Host', 'staff_no' => 'LLM-S002', 'location' => 'Warehouse B - Storage', 'reason' => 'Rejected test invitation', 'status' => 'Rejected', 'from' => '-1 day', 'to' => '-1 day +2 hours', 'visit_state' => 'none'],
            ['name' => 'Lina Bakar', 'ic' => 'LLM900101012', 'contact' => '+60170001012', 'company' => 'LLM Demo Catering', 'host' => 'Sarah Demo Host', 'staff_no' => 'LLM-S005', 'location' => 'Main Office - Meeting Room 3', 'reason' => 'Contract discussion', 'status' => 'Approved', 'from' => '+1 day', 'to' => '+1 day +2 hours', 'visit_state' => 'future'],
        ];

        $insertedInvitations = [];

        foreach ($visits as $index => $visit) {
            $syncUid = 'LLM-DEMO-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            $existing = $db->table('invitations')->where('sync_uid', $syncUid)->get()->getRowArray();
            if ($existing) {
                $insertedInvitations[$syncUid] = (int) $existing['id'];
                continue;
            }

            $dateFrom = date('Y-m-d H:i:s', strtotime($visit['from']));
            $dateTo = date('Y-m-d H:i:s', strtotime($visit['to']));

            $db->table('invitations')->insert([
                'sync_uid' => $syncUid,
                'full_name' => $visit['name'],
                'ic_passport' => $visit['ic'],
                'contact' => $visit['contact'],
                'visitor_email' => strtolower(str_replace(' ', '.', $visit['name'])) . '@llm-demo.test',
                'company' => $visit['company'],
                'location' => $visit['location'],
                'invited_by' => $visit['host'],
                'staff_id' => $visit['staff_no'],
                'company_visited' => 'SafeG Demo Site',
                'host_contact' => '+60129990000',
                'reason' => $visit['reason'],
                'status' => $visit['status'],
                'registration_source' => 'LLM Demo',
                'created_at' => $now,
                'updated_at' => $now,
                'version' => 1,
            ]);
            $invitationId = (int) $db->insertID();
            $insertedInvitations[$syncUid] = $invitationId;

            $db->table('invitation_schedules')->insert([
                'sync_uid' => $syncUid . '-SCH',
                'invitation_id' => $invitationId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $checkIn = null;
            $checkOut = null;
            if ($visit['visit_state'] === 'onsite') {
                $checkIn = date('Y-m-d H:i:s', strtotime('-90 minutes'));
            } elseif ($visit['visit_state'] === 'overstay') {
                $checkIn = date('Y-m-d H:i:s', strtotime($visit['from'] . ' +15 minutes'));
            } elseif ($visit['visit_state'] === 'checkedout') {
                $checkIn = date('Y-m-d H:i:s', strtotime($visit['from'] . ' +10 minutes'));
                $checkOut = date('Y-m-d H:i:s', strtotime($visit['to'] . ' -15 minutes'));
            }

            $db->table('invitation_visitors')->insert([
                'sync_uid' => $syncUid . '-VIS',
                'invitation_id' => $invitationId,
                'check_in_time' => $checkIn,
                'check_out_time' => $checkOut,
                'full_name' => $visit['name'],
                'ic_passport' => $visit['ic'],
                'contact' => $visit['contact'],
                'company' => $visit['company'],
                'vehicle_registration' => 'LLM' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'created_at' => $now,
                'updated_at' => $now,
                'version' => 1,
            ]);
        }

        $alertRows = [
            ['incident_type' => 'Visitor Overstay Alert', 'severity' => 'high', 'visitor_name' => 'Farah Aziz', 'location' => 'Main Entrance Gate', 'description' => 'LLM_DEMO_DATA Farah Aziz exceeded scheduled visit window.'],
            ['incident_type' => 'Visitor Overstay Alert', 'severity' => 'medium', 'visitor_name' => 'Goh Wei Ming', 'location' => 'Office Level 2', 'description' => 'LLM_DEMO_DATA Goh Wei Ming still on-site after expected end time.'],
            ['incident_type' => 'Access Denied - Invalid Pass', 'severity' => 'critical', 'visitor_name' => 'Kevin Ong', 'location' => 'Warehouse B - Storage', 'description' => 'LLM_DEMO_DATA rejected visitor attempted entry with inactive pass.'],
            ['incident_type' => 'Tailgating Attempt', 'severity' => 'high', 'visitor_name' => 'Unknown Visitor', 'location' => 'North Branch - Lobby Entrance', 'description' => 'LLM_DEMO_DATA camera flagged two people entering on one pass.'],
            ['incident_type' => 'Blacklist Match', 'severity' => 'critical', 'visitor_name' => 'Demo Blacklist Visitor', 'location' => 'Main Office - Reception Area', 'description' => 'LLM_DEMO_DATA visitor matched blacklist watch record.'],
        ];

        foreach ($alertRows as $i => $alert) {
            if ($db->table('security_alerts')->like('description', $alert['description'])->countAllResults()) {
                continue;
            }

            $db->table('security_alerts')->insert($alert + [
                'sync_uid' => 'LLM-DEMO-ALERT-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'is_acknowledged' => $i === 1 ? 1 : 0,
                'acknowledged_by' => null,
                'acknowledged_at' => $i === 1 ? date('Y-m-d H:i:s', strtotime('-30 minutes')) : null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . (($i + 1) * 20) . ' minutes')),
                'updated_at' => $now,
                'version' => 1,
            ]);
        }

        echo "LLM demo data ready. Try questions like:\n";
        echo "- List all visitors hosted by Ahmad Demo Host\n";
        echo "- Which visitors are overstaying?\n";
        echo "- How many LLM Demo Logistics visitors checked out today?\n";
        echo "- Show critical security alerts from demo data\n";
    }
}
