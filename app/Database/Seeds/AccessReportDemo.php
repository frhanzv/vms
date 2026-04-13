<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Demo data for Access Report + Chronology (visitor_card_logs → lanes → locations).
 *
 * Run: php spark db:seed AccessReportDemo
 *
 * - Older sample scans go on the first active lane only.
 * - Today's scans are written for every active lane, so "today" + any active location works.
 * - A location only appears if it has at least one lane with status active (Config → Lanes).
 */
class AccessReportDemo extends Seeder
{
    private const DEMO_COMPANY = 'DEMO_SEED_ACCESS_REPORT';

    public function run()
    {
        $db = $this->db;

        $lanes = $db->table('lanes la')
            ->select('la.id AS lane_id, la.location_id, loc.branch, loc.location_access')
            ->join('locations loc', 'loc.id = la.location_id')
            ->where('la.status', 'active')
            ->where('loc.status', 'active')
            ->orderBy('la.location_id', 'ASC')
            ->orderBy('la.id', 'ASC')
            ->get()
            ->getResultArray();

        if ($lanes === []) {
            echo "No active lane with an active location found. Add one in Config, then run again.\n";

            return;
        }

        $primaryLaneId = (int) $lanes[0]['lane_id'];

        $this->removePreviousDemo($db);

        $suffix = bin2hex(random_bytes(3));

        $cards = [
            ['card_id' => "DEMO-VC-A-{$suffix}", 'serial_no' => "DEMO-SN-A-{$suffix}", 'status' => 'in_use'],
            ['card_id' => "DEMO-VC-B-{$suffix}", 'serial_no' => "DEMO-SN-B-{$suffix}", 'status' => 'in_use'],
            ['card_id' => "DEMO-VC-C-{$suffix}", 'serial_no' => "DEMO-SN-C-{$suffix}", 'status' => 'in_use'],
        ];

        $cardIds = [];
        foreach ($cards as $c) {
            $db->table('visitor_cards')->insert([
                'card_id'    => $c['card_id'],
                'serial_no'  => $c['serial_no'],
                'status'     => $c['status'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $cardIds[] = (int) $db->insertID();
        }

        $invitations = [
            [
                'full_name'            => 'Demo Visitor — Alice Tan',
                'ic_passport'          => '900101-14-5678',
                'contact'              => '0123456701',
                'company'              => self::DEMO_COMPANY,
                'vehicle_registration' => 'WWW 1',
                'location'             => 'Reception',
                'invited_by'           => 'Ahmad Razak',
                'staff_id'             => 'STF-1001',
                'reason'               => 'Vendor meeting',
                'status'               => 'Approved',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'full_name'            => 'Demo Visitor — Bob Lee',
                'ic_passport'          => '850505-10-1234',
                'contact'              => '0123456702',
                'company'              => self::DEMO_COMPANY,
                'vehicle_registration' => 'BDE 8888',
                'location'             => 'Reception',
                'invited_by'           => 'Lisa Wong',
                'staff_id'             => 'STF-2044',
                'reason'               => 'Maintenance',
                'status'               => 'Approved',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'full_name'            => 'Demo Visitor — Carol Ng',
                'ic_passport'          => '921212-08-9999',
                'contact'              => '0123456703',
                'company'              => self::DEMO_COMPANY,
                'vehicle_registration' => null,
                'location'             => 'Reception',
                'invited_by'           => 'Security Desk',
                'staff_id'             => null,
                'reason'               => 'Interview',
                'status'               => 'Approved',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
        ];

        $invitationIds = [];
        foreach ($invitations as $inv) {
            $db->table('invitations')->insert($inv);
            $invitationIds[] = (int) $db->insertID();
        }

        foreach ($invitationIds as $i => $invitationId) {
            $db->table('invitation_visitors')->insert([
                'invitation_id'        => $invitationId,
                'visitor_card_id'      => $cardIds[$i],
                'full_name'            => $invitations[$i]['full_name'],
                'ic_passport'          => $invitations[$i]['ic_passport'],
                'contact'              => $invitations[$i]['contact'],
                'company'              => $invitations[$i]['company'],
                'vehicle_registration' => $invitations[$i]['vehicle_registration'],
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);
        }

        $scanSets = [
            // Alice: multiple passes (total_access > 1)
            [
                ['t' => '-2 days 08:55:00', 'action' => 'checkin'],
                ['t' => '-2 days 12:10:00', 'action' => 'checkout'],
                ['t' => '-2 days 13:05:00', 'action' => 'checkin'],
                ['t' => '-1 days 09:00:00', 'action' => 'checkout'],
            ],
            // Bob: two scans
            [
                ['t' => '-1 days 10:20:00', 'action' => 'checkin'],
                ['t' => '-1 days 16:45:00', 'action' => 'checkout'],
            ],
            // Carol: single scan
            [
                ['t' => '-1 days 11:30:00', 'action' => 'checkin'],
            ],
        ];

        foreach ($scanSets as $idx => $scans) {
            foreach ($scans as $scan) {
                $scannedAt = date('Y-m-d H:i:s', strtotime($scan['t']));
                $db->table('visitor_card_logs')->insert([
                    'visitor_card_id' => $cardIds[$idx],
                    'invitation_id'   => $invitationIds[$idx],
                    'action'          => $scan['action'],
                    'lane_id'         => $primaryLaneId,
                    'scanned_at'      => $scannedAt,
                    'created_at'      => $scannedAt,
                ]);
            }
        }

        // Same calendar day as the server — works with From/To = today in the report UI.
        $todayByVisitor = [
            [
                ['t' => 'today 09:05:00', 'action' => 'checkin'],
                ['t' => 'today 11:30:00', 'action' => 'checkout'],
                ['t' => 'today 15:10:00', 'action' => 'checkin'],
            ],
            [
                ['t' => 'today 10:40:00', 'action' => 'checkin'],
                ['t' => 'today 13:00:00', 'action' => 'checkout'],
            ],
            [
                ['t' => 'today 12:45:00', 'action' => 'checkin'],
            ],
        ];

        foreach ($lanes as $laneRow) {
            $laneId = (int) $laneRow['lane_id'];
            foreach ($todayByVisitor as $idx => $scans) {
                foreach ($scans as $scan) {
                    $scannedAt = date('Y-m-d H:i:s', strtotime($scan['t']));
                    $db->table('visitor_card_logs')->insert([
                        'visitor_card_id' => $cardIds[$idx],
                        'invitation_id'   => $invitationIds[$idx],
                        'action'          => $scan['action'],
                        'lane_id'         => $laneId,
                        'scanned_at'      => $scannedAt,
                        'created_at'      => $scannedAt,
                    ]);
                }
            }
        }

        echo "Demo access data inserted.\n";
        echo "Invitations: " . implode(', ', $invitationIds) . "\n";
        echo "Locations with demo data (active lanes only):\n";
        foreach ($lanes as $L) {
            echo "  - ID {$L['location_id']}: {$L['branch']} — {$L['location_access']} (lane {$L['lane_id']})\n";
        }
        echo "For 'today', use From 00:00 → To 23:59 on " . date('Y-m-d') . ".\n";
        echo "Older sample scans (2 days back) exist only on lane {$primaryLaneId}.\n";
    }

    private function removePreviousDemo($db): void
    {
        $rows = $db->table('invitations')
            ->select('id')
            ->where('company', self::DEMO_COMPANY)
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return;
        }

        $invIds = array_map(static fn (array $r) => (int) $r['id'], $rows);

        $db->table('visitor_card_logs')->whereIn('invitation_id', $invIds)->delete();

        $ivRows = $db->table('invitation_visitors')
            ->select('visitor_card_id')
            ->whereIn('invitation_id', $invIds)
            ->get()
            ->getResultArray();

        $cardIds = array_values(array_unique(array_filter(array_map(
            static fn (array $r) => isset($r['visitor_card_id']) ? (int) $r['visitor_card_id'] : null,
            $ivRows
        ))));

        $db->table('invitation_visitors')->whereIn('invitation_id', $invIds)->delete();
        $db->table('invitations')->whereIn('id', $invIds)->delete();

        if ($cardIds !== []) {
            $db->table('visitor_cards')->whereIn('id', $cardIds)->delete();
        }
    }
}
