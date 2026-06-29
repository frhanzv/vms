<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Supporting module data for LLM testing: blacklist, security alerts, visitor cards.
 *
 * Run: php spark db:seed LlmModuleDataSeeder
 */
class LlmModuleDataSeeder extends Seeder
{
    use LlmSeedSupport;

    public function run(): void
    {
        set_time_limit(0);

        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        $this->seedVisitorCards($db, $now);
        $this->seedBlacklist($db, $now);
        $this->seedSecurityAlerts($db, $now);

        echo "LLM module data (visitor cards, blacklist, security alerts) ready.\n";
    }

    private function seedVisitorCards($db, string $now): void
    {
        if (! $db->tableExists('visitor_cards')) {
            return;
        }

        $existing = $db->table('visitor_cards')->like('card_id', 'LLM-DEMO', 'after')->countAllResults();
        $target   = $this->llmVisitorCardTarget;

        if ($existing >= $target) {
            echo "Visitor cards: {$existing} LLM-DEMO cards already exist.\n";

            return;
        }

        $batch = [];
        for ($i = $existing + 1; $i <= $target; $i++) {
            $row = [
                'card_id'    => 'LLM-DEMO-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'serial_no'  => 'LLM-SN-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if ($this->llmTableHasColumn($db, 'visitor_cards', 'version')) {
                $row['version'] = 1;
            }
            $batch[] = $row;

            if (count($batch) >= $this->llmBatchSize) {
                $db->table('visitor_cards')->insertBatch($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $db->table('visitor_cards')->insertBatch($batch);
        }

        echo 'Visitor cards: ' . $db->table('visitor_cards')->like('card_id', 'LLM-DEMO', 'after')->countAllResults() . " LLM-DEMO card(s).\n";
    }

    private function seedBlacklist($db, string $now): void
    {
        if (! $db->tableExists('blacklist')) {
            return;
        }

        $existing = $db->table('blacklist')->like('reason', $this->llmMarker)->countAllResults();
        $target   = $this->llmBlacklistTarget;

        if ($existing >= $target) {
            echo "Blacklist: {$existing} LLM demo entries already exist.\n";

            return;
        }

        $types    = ['Visitor', 'Staff', 'Contractor', 'Vendor'];
        $statuses = ['active', 'active', 'pending', 'closed', 'closed'];
        $reasons  = ['Security violation', 'Repeated unauthorized access', 'Forged documents', 'Policy breach', 'Theft incident'];
        $batch    = [];

        for ($i = $existing + 1; $i <= $target; $i++) {
            $first  = $this->llmFirstNames()[($i * 7) % count($this->llmFirstNames())];
            $last   = $this->llmLastNames()[($i * 11) % count($this->llmLastNames())];
            $status = $statuses[$i % count($statuses)];

            $batch[] = [
                'created_date'   => date('Y-m-d', strtotime('-' . (10 + ($i % 190)) . ' days')),
                'blacklist_date' => date('Y-m-d', strtotime('-' . (5 + ($i % 175)) . ' days')),
                'ic_passport_no' => 'LLM-BL' . str_pad((string) (1000 + $i), 5, '0', STR_PAD_LEFT),
                'staff_id'       => $i % 3 === 0 ? 'LLM-S' . str_pad((string) ($i % 2000), 4, '0', STR_PAD_LEFT) : null,
                'name'           => $first . ' ' . $last,
                'status'         => $status,
                'type'           => $types[$i % count($types)],
                'reason'         => $this->llmMarker . ' — ' . $reasons[$i % count($reasons)],
                'released_date'  => $status === 'closed' ? date('Y-m-d', strtotime('-' . ($i % 30) . ' days')) : null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];

            if (count($batch) >= $this->llmBatchSize) {
                $db->table('blacklist')->insertBatch($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $db->table('blacklist')->insertBatch($batch);
        }

        echo 'Blacklist LLM entries: ' . $db->table('blacklist')->like('reason', $this->llmMarker)->countAllResults() . "\n";
    }

    private function seedSecurityAlerts($db, string $now): void
    {
        if (! $db->tableExists('security_alerts')) {
            return;
        }

        $existing = $db->table('security_alerts')->like('description', $this->llmMarker)->countAllResults();
        $target   = $this->llmSecurityAlertTarget;

        if ($existing >= $target) {
            echo "Security alerts: {$existing} LLM demo alerts already exist.\n";

            return;
        }

        $incidents = [
            ['type' => 'Visitor Overstay Alert', 'severity' => 'high'],
            ['type' => 'Visitor Overstay Alert', 'severity' => 'medium'],
            ['type' => 'Access Denied - Invalid Pass', 'severity' => 'critical'],
            ['type' => 'Tailgating Attempt', 'severity' => 'high'],
            ['type' => 'Blacklist Match', 'severity' => 'critical'],
            ['type' => 'Unauthorized Area Access', 'severity' => 'high'],
            ['type' => 'Expired Pass Used', 'severity' => 'medium'],
            ['type' => 'Reader Offline', 'severity' => 'low'],
        ];

        $locations = $this->llmVisitLocations();
        $batch     = [];

        for ($i = $existing + 1; $i <= $target; $i++) {
            $incident = $incidents[$i % count($incidents)];
            $first    = $this->llmFirstNames()[$i % count($this->llmFirstNames())];
            $last     = $this->llmLastNames()[($i * 2) % count($this->llmLastNames())];
            $visitor  = $first . ' ' . $last;
            $location = $locations[$i % count($locations)];

            $row = [
                'sync_uid'        => 'LLM-BULK-ALERT-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'incident_type'   => $incident['type'],
                'severity'        => $incident['severity'],
                'visitor_name'    => $visitor,
                'location'        => $location,
                'description'     => $this->llmMarker . " {$visitor} at {$location} — incident #{$i}.",
                'is_acknowledged' => $i % 4 === 0 ? 1 : 0,
                'acknowledged_by' => null,
                'acknowledged_at' => $i % 4 === 0 ? date('Y-m-d H:i:s', strtotime('-' . ($i % 120) . ' minutes')) : null,
                'created_at'      => date('Y-m-d H:i:s', strtotime('-' . ($i * 5) . ' minutes')),
                'updated_at'      => $now,
            ];

            if ($this->llmTableHasColumn($db, 'security_alerts', 'version')) {
                $row['version'] = 1;
            }

            $batch[] = $row;

            if (count($batch) >= $this->llmBatchSize) {
                $db->table('security_alerts')->insertBatch($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $db->table('security_alerts')->insertBatch($batch);
        }

        echo 'Security alert LLM entries: ' . $db->table('security_alerts')->like('description', $this->llmMarker)->countAllResults() . "\n";
    }
}
