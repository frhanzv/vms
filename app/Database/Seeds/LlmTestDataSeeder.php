<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Master seeder — populates all LLM test data across modules (~2000 staff + ~2000 visitor passes).
 *
 * Run everything:
 *   php spark db:seed LlmTestDataSeeder
 *
 * Recommended order is handled automatically. Idempotent: safe to re-run.
 *
 * Cleanup — run only when test is finished:
 *   php spark db:seed LlmTestDataCleanupSeeder
 *
 * Manual SQL alternative is documented in LlmTestDataCleanupSeeder.php.
 */
class LlmTestDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== LLM Test Data Seeder ===\n\n";

        $this->call(LlmDemoDataSeeder::class);
        echo "\n";

        $this->call(LlmStaffPassSeeder::class);
        echo "\n";

        $this->call(LlmVisitorPassSeeder::class);
        echo "\n";

        $this->call(LlmModuleDataSeeder::class);
        echo "\n";

        $this->call(DummyCriticalSecurityAlertsSeeder::class);
        echo "\n";

        $this->printSummary();
    }

    private function printSummary(): void
    {
        $db = $this->db;

        $counts = [];
        if ($db->tableExists('staff')) {
            $counts['Staff pass list'] = $db->table('staff')->countAllResults();
        }
        if ($db->tableExists('invitations')) {
            $counts['Invitations (all)'] = $db->table('invitations')->countAllResults();
            $counts['Approved invitations'] = $db->table('invitations')->where('status', 'Approved')->countAllResults();
            $counts['Submitted (request list)'] = $db->table('invitations')->where('status', 'Submitted')->countAllResults();
            $counts['Pending invitations'] = $db->table('invitations')->where('status', 'Pending')->countAllResults();
        }
        if ($db->tableExists('invitation_visitors')) {
            $counts['Visitor pass list rows'] = $db->table('invitation_visitors iv')
                ->join('invitations i', 'i.id = iv.invitation_id')
                ->where('i.status', 'Approved')
                ->countAllResults();
        }
        if ($db->tableExists('blacklist')) {
            $counts['Blacklist entries'] = $db->table('blacklist')->countAllResults();
        }
        if ($db->tableExists('security_alerts')) {
            $counts['Security alerts'] = $db->table('security_alerts')->countAllResults();
        }
        if ($db->tableExists('visitor_cards')) {
            $counts['Visitor cards'] = $db->table('visitor_cards')->countAllResults();
        }
        if ($db->tableExists('staff_cards')) {
            $counts['Staff cards'] = $db->table('staff_cards')->countAllResults();
        }

        echo "=== Summary ===\n";
        foreach ($counts as $label => $count) {
            echo str_pad($label . ':', 28) . $count . "\n";
        }

        echo "\nSample LLM questions to try:\n";
        echo "- How many visitors are currently on-site?\n";
        echo "- List all visitors hosted by Ahmad Demo Host\n";
        echo "- Which visitors are overstaying?\n";
        echo "- Show all staff in the Engineering department\n";
        echo "- How many active blacklist entries are there?\n";
        echo "- Show unacknowledged critical security alerts\n";
        echo "- How many submitted visitor pass requests are pending approval?\n";
    }
}
