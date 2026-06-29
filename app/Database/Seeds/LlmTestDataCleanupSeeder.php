<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Remove ONLY LLM / demo test data seeded by LlmTestDataSeeder and related seeders.
 * Real production rows are untouched.
 *
 * Run:
 *   php spark db:seed LlmTestDataCleanupSeeder
 *
 * Tags removed:
 *   - invitations.registration_source IN ('LLM Demo', 'LLM Demo Bulk')
 *   - staff.remark LIKE '%LLM_DEMO_DATA%'
 *   - blacklist.reason LIKE '%LLM_DEMO_DATA%'
 *   - security_alerts.description LIKE '%LLM_DEMO_DATA%' OR sync_uid LIKE 'LLM-%ALERT-%'
 *   - visitor_cards.card_id LIKE 'LLM-DEMO-%'
 *   - companies.registration_no LIKE 'LLM-%'
 *   - security_alerts.description LIKE '%DEMO_CRITICAL_ALERT_SEED%' (dashboard dummy alerts)
 */
class LlmTestDataCleanupSeeder extends Seeder
{
    use LlmSeedSupport;

    public function run(): void
    {
        $db = $this->db;

        echo "=== LLM Test Data Cleanup ===\n";
        echo "Removing only tagged demo/LLM seed rows...\n\n";

        $counts = [];

        // Child rows first where FK does not cascade (or for explicit clarity).
        if ($db->tableExists('invitation_visitors')) {
            $counts['invitation_visitors'] = $this->deleteJoined(
                'invitation_visitors iv',
                'invitations i ON i.id = iv.invitation_id',
                "i.registration_source IN ('LLM Demo', 'LLM Demo Bulk')"
            );
        }

        if ($db->tableExists('invitation_schedules')) {
            $counts['invitation_schedules'] = $this->deleteJoined(
                'invitation_schedules sch',
                'invitations i ON i.id = sch.invitation_id',
                "i.registration_source IN ('LLM Demo', 'LLM Demo Bulk')"
            );
        }

        if ($db->tableExists('visitor_card_logs')) {
            $counts['visitor_card_logs'] = $this->deleteJoined(
                'visitor_card_logs vcl',
                'invitations i ON i.id = vcl.invitation_id',
                "i.registration_source IN ('LLM Demo', 'LLM Demo Bulk')"
            );
        }

        if ($db->tableExists('security_alerts')) {
            $counts['security_alerts (demo invitations)'] = $this->deleteJoined(
                'security_alerts sa',
                'invitations i ON i.id = sa.invitation_id',
                "i.registration_source IN ('LLM Demo', 'LLM Demo Bulk')"
            );
        }

        if ($db->tableExists('invitations')) {
            $counts['invitations'] = $db->table('invitations')
                ->whereIn('registration_source', ['LLM Demo', 'LLM Demo Bulk'])
                ->delete();
        }

        if ($db->tableExists('staff_cards') && $db->tableExists('staff')) {
            $counts['staff_cards'] = $this->deleteJoined(
                'staff_cards sc',
                'staff s ON s.id = sc.staff_id',
                "s.remark LIKE '%{$this->llmMarker}%'"
            );
        }

        if ($db->tableExists('staff')) {
            $counts['staff'] = $db->table('staff')
                ->like('remark', $this->llmMarker)
                ->delete();
        }

        if ($db->tableExists('blacklist')) {
            $counts['blacklist'] = $db->table('blacklist')
                ->like('reason', $this->llmMarker)
                ->delete();
        }

        if ($db->tableExists('security_alerts')) {
            $counts['security_alerts (LLM tagged)'] = $db->table('security_alerts')
                ->groupStart()
                    ->like('description', $this->llmMarker)
                    ->orLike('description', 'DEMO_CRITICAL_ALERT_SEED')
                    ->orLike('sync_uid', 'LLM-DEMO-ALERT', 'after')
                    ->orLike('sync_uid', 'LLM-BULK-ALERT', 'after')
                ->groupEnd()
                ->delete();
        }

        if ($db->tableExists('visitor_cards')) {
            $counts['visitor_cards'] = $db->table('visitor_cards')
                ->like('card_id', 'LLM-DEMO', 'after')
                ->delete();
        }

        if ($db->tableExists('companies')) {
            $counts['companies'] = $db->table('companies')
                ->like('registration_no', 'LLM-', 'after')
                ->delete();
        }

        echo "\n=== Deleted ===\n";
        foreach ($counts as $label => $count) {
            echo str_pad($label . ':', 36) . (int) $count . "\n";
        }

        echo "\nDone. Real data was not targeted.\n";
        echo "Re-seed later with: php spark db:seed LlmTestDataSeeder\n";
    }

    private function deleteJoined(string $fromAlias, string $join, string $where): int
    {
        $sql = "DELETE {$fromAlias} FROM {$fromAlias} JOIN {$join} WHERE {$where}";

        $this->db->query($sql);

        return (int) $this->db->affectedRows();
    }
}
