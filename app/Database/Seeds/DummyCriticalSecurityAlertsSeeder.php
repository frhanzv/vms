<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Demo rows for Host Dashboard → Critical Security Alert banner and Access Denied modal.
 *
 * Run: php spark db:seed DummyCriticalSecurityAlertsSeeder
 *
 * - Empty DB: inserts 4 unacknowledged critical alerts.
 * - Already has older dummy rows (no V3 marker): inserts only V3.
 * - Has V3 but no V4 marker: inserts only V4.
 *
 * Remove: DELETE FROM security_alerts WHERE description LIKE '%DEMO_CRITICAL_ALERT_SEED%';
 */
class DummyCriticalSecurityAlertsSeeder extends Seeder
{
    private function baseRow(string $description, array $rest): array
    {
        $now = date('Y-m-d H:i:s');

        return array_merge([
            'severity'          => 'critical',
            'is_acknowledged'   => 0,
            'created_at'        => $now,
            'updated_at'        => $now,
        ], $rest, [
            'description' => $description,
        ]);
    }

    public function run()
    {
        $db = $this->db;

        if (! $db->tableExists('security_alerts')) {
            echo "Table security_alerts does not exist. Run migrations first.\n";

            return;
        }

        $broadCount = $db->table('security_alerts')
            ->like('description', 'DEMO_CRITICAL_ALERT_SEED')
            ->countAllResults();

        $v3Exists = $db->table('security_alerts')
            ->like('description', 'DEMO_CRITICAL_ALERT_SEED_V3')
            ->countAllResults() > 0;

        $v4Exists = $db->table('security_alerts')
            ->like('description', 'DEMO_CRITICAL_ALERT_SEED_V4')
            ->countAllResults() > 0;

        $allFour = [
            $this->baseRow(
                'DEMO_CRITICAL_ALERT_SEED_V1 Inactive card presented: visitor pass status is inactive/revoked while invitation INV-2026-DEMO-01 is still on file. Tap rejected at reader.',
                [
                    'incident_type' => 'Access Denied — Inactive Visitor Pass',
                    'location'      => 'Main Lobby — Turnstile A',
                    'visitor_name'  => 'Demo Visitor — Inactive Card',
                ]
            ),
            $this->baseRow(
                'DEMO_CRITICAL_ALERT_SEED_V2 Invitation visit ended (schedule date_to passed); visitor record and pass no longer authorized. Attempt logged against visitor list / pass information.',
                [
                    'incident_type' => 'Unauthorized Access — Visit Window Expired',
                    'location'      => 'Parking — Gate B',
                    'visitor_name'  => 'Demo Visitor — Overdue Visit',
                ]
            ),
            $this->baseRow(
                'DEMO_CRITICAL_ALERT_SEED_V3 Reader rejected tap: pass linked to ended visit; invitation schedule and visitor list show no active window. Possible tailgating / wrong card.',
                [
                    'incident_type' => 'Access Denied — Reader Rejected (Visit Not Active)',
                    'location'      => 'Side Entrance — Reader 12',
                    'visitor_name'  => 'Demo Visitor — Reader Retry',
                ]
            ),
            $this->baseRow(
                'DEMO_CRITICAL_ALERT_SEED_V4 Credential matched active blacklist entry; reader held door and notified security. Photo capture queued for review.',
                [
                    'incident_type' => 'Blacklist Match — Reader Hold',
                    'location'      => 'Executive Lobby — Reader 04',
                    'visitor_name'  => 'Demo Visitor — Blacklist Probe',
                ]
            ),
        ];

        if ($broadCount === 0) {
            $db->table('security_alerts')->insertBatch($allFour);
            echo 'Inserted ' . count($allFour) . " dummy critical security alert(s). Refresh the dashboard.\n";

            return;
        }

        if (! $v3Exists) {
            $db->table('security_alerts')->insert($allFour[2]);
            echo "Inserted 1 new dummy critical alert (V3). Refresh the dashboard.\n";

            return;
        }

        if (! $v4Exists) {
            $db->table('security_alerts')->insert($allFour[3]);
            echo "Inserted 1 new dummy critical alert (V4). Refresh the dashboard.\n";

            return;
        }

        echo "Dummy alerts already include V4. Nothing to insert.\n";
    }
}
