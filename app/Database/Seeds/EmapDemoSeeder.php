<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmapDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('FacilityDataSeeder');

        // The newest map is normally the tenant that most recently opened E-map.
        $map = $this->db->table('emap_maps')->orderBy('id', 'DESC')->get()->getRowArray();
        if (!$map) {
            throw new \RuntimeException('Create and save an E-map before running EmapDemoSeeder.');
        }

        $layout = json_decode((string) $map['layout_json'], true);
        if (!is_array($layout)) {
            throw new \RuntimeException('The saved E-map layout is invalid.');
        }

        $zoneLinks = [
            'lobby'      => '1. VISITOR STAIRCASE',
            'office'     => '2. OPERATION OFFICE STAIRCASE',
            'meeting'    => '6. CMM ROOM',
            'production' => '4. VISITOR ENTRANCE TO PRODUCTION',
            'cafeteria'  => '9. ULTRASONIC ROOM',
            'loading'    => '11. 1K CLEANROOM',
            'warehouse'  => '3. HR & ADMIN ENTRANCE',
        ];

        $subRows = $this->db->table('sub_locations')->select('id, name, location_id')->get()->getResultArray();
        $subByName = [];
        foreach ($subRows as $row) {
            $subByName[$row['name']] = $row;
        }

        $zoneById = [];
        $layout['zones'] = $layout['zones'] ?? [];
        foreach ($layout['zones'] as &$zone) {
            $name = $zoneLinks[$zone['id']] ?? null;
            if ($name !== null && isset($subByName[$name])) {
                $zone['locationId'] = (int) $subByName[$name]['location_id'];
                $zone['subLocationId'] = (int) $subByName[$name]['id'];
            }
            $zoneById[$zone['id']] = $zone;
        }
        unset($zone);

        $lanes = $this->db->table('lanes')->select('id, lane')->get()->getResultArray();
        $laneByName = array_column($lanes, null, 'lane');
        $devices = $this->db->table('device_assignments')
            ->select('id, device_id, location_id, type')->orderBy('id', 'ASC')->get()->getResultArray();
        $devicesBySub = [];
        foreach ($devices as $device) {
            $devicesBySub[(int) $device['location_id']][] = $device;
        }

        $layout['assets'] = $layout['assets'] ?? [];
        $existingAssetIndexes = [];
        foreach ($layout['assets'] as $index => $existingAsset) {
            if (!empty($existingAsset['id'])) {
                $existingAssetIndexes[$existingAsset['id']] = $index;
            }
        }
        $demoTransitions = [
            ['demo-reader-01', 'Lobby to Office',       230, 160, 'lobby',      'office'],
            ['demo-reader-02', 'Office to Meeting',     610, 130, 'office',     'meeting'],
            ['demo-reader-03', 'Meeting to Production', 720, 206, 'meeting',    'production'],
            ['demo-reader-04', 'Production to Warehouse', 820, 416, 'production', 'warehouse'],
            ['demo-reader-05', 'Warehouse to Loading',  700, 470, 'warehouse',  'loading'],
            ['demo-reader-06', 'Loading to Cafeteria',  265, 430, 'loading',    'cafeteria'],
        ];

        foreach ($demoTransitions as [$id, $label, $x, $y, $fromId, $toId]) {
            if (!isset($zoneById[$fromId], $zoneById[$toId])) {
                continue;
            }
            $toSubId = (int) ($zoneById[$toId]['subLocationId'] ?? 0);
            $device = $devicesBySub[$toSubId][0] ?? null;
            $demoAsset = [
                'id' => $id,
                'type' => 'reader',
                'label' => $label,
                'x' => $x,
                'y' => $y,
                'laneId' => null,
                'deviceAssignmentId' => $device ? (int) $device['id'] : null,
                'fromZoneId' => $fromId,
                'toZoneId' => $toId,
                'transitionMode' => 'bidirectional',
            ];
            if (isset($existingAssetIndexes[$id])) {
                $layout['assets'][$existingAssetIndexes[$id]] = $demoAsset;
            } else {
                $layout['assets'][] = $demoAsset;
            }
        }

        $usage = [];
        $linkedAssets = 0;
        foreach ($layout['assets'] as &$asset) {
            if (!in_array($asset['type'] ?? '', ['door', 'reader'], true)) {
                continue;
            }

            $zone = $this->nearestZone($asset, array_values($zoneById));
            if (!$zone || empty($zone['subLocationId'])) {
                continue;
            }

            $connectedZones = $this->nearestZones($asset, array_values($zoneById), 2);
            if (count($connectedZones) === 2) {
                $asset['fromZoneId'] = $asset['fromZoneId'] ?? $connectedZones[0]['id'];
                $asset['toZoneId'] = $asset['toZoneId'] ?? $connectedZones[1]['id'];
                $asset['transitionMode'] = $asset['transitionMode'] ?? 'bidirectional';
            }

            $subId = (int) $zone['subLocationId'];
            $subName = null;
            foreach ($subByName as $name => $sub) {
                if ((int) $sub['id'] === $subId) {
                    $subName = $name;
                    break;
                }
            }
            if (!array_key_exists('laneId', $asset) && $subName !== null && isset($laneByName[$subName])) {
                $asset['laneId'] = (int) $laneByName[$subName]['id'];
            }

            $available = $devicesBySub[$subId] ?? [];
            if (empty($asset['deviceAssignmentId']) && $available) {
                $index = $usage[$subId] ?? 0;
                $asset['deviceAssignmentId'] = (int) $available[$index % count($available)]['id'];
                $usage[$subId] = $index + 1;
            }
            $linkedAssets++;
        }
        unset($asset);

        $this->db->table('emap_maps')->where('id', $map['id'])->update([
            'layout_json' => json_encode($layout, JSON_UNESCAPED_SLASHES),
            'version'     => (int) $map['version'] + 1,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $clientId = isset($map['client_id']) ? (int) $map['client_id'] : null;
        $this->seedVisitor(array_values($zoneById), $laneByName, $subByName, $clientId);
        $this->seedJohnDoe(array_values($zoneById), $laneByName, $subByName, $clientId);
        echo "  EmapDemoSeeder: {$linkedAssets} readers linked. Test Aisyah with EPC DD0000000000000000000001.\n";
    }

    private function nearestZone(array $asset, array $zones): ?array
    {
        return $this->nearestZones($asset, $zones, 1)[0] ?? null;
    }

    private function nearestZones(array $asset, array $zones, int $limit): array
    {
        $ranked = [];
        $x = (float) ($asset['x'] ?? 0);
        $y = (float) ($asset['y'] ?? 0);
        foreach ($zones as $zone) {
            $zx = (float) $zone['x'];
            $zy = (float) $zone['y'];
            $nearestX = max($zx, min($x, $zx + (float) $zone['w']));
            $nearestY = max($zy, min($y, $zy + (float) $zone['h']));
            $distance = (($x - $nearestX) ** 2) + (($y - $nearestY) ** 2);
            $ranked[] = ['distance' => $distance, 'zone' => $zone];
        }
        usort($ranked, static fn(array $a, array $b): int => $a['distance'] <=> $b['distance']);
        return array_map(static fn(array $item): array => $item['zone'], array_slice($ranked, 0, $limit));
    }

    private function seedVisitor(array $zones, array $laneByName, array $subByName, ?int $clientId): void
    {
        $now = date('Y-m-d H:i:s');
        $invitationBuilder = $this->db->table('invitations')->where('ic_passport', 'EMAP-DEMO-001');
        $clientId === null ? $invitationBuilder->where('client_id', null) : $invitationBuilder->where('client_id', $clientId);
        $invitation = $invitationBuilder->get()->getRowArray();
        $invitationData = [
            'client_id'   => $clientId,
            'full_name'   => 'Aisyah Demo Visitor',
            'contact'     => '0123456789',
            'company'     => 'SafeG Demo',
            'invited_by'  => 'Demo Administrator',
            'reason'      => 'E-Map live demonstration',
            'status'      => 'Approved',
            'checked_in_at' => $now,
            'updated_at'  => $now,
        ];
        if ($invitation) {
            $this->db->table('invitations')->where('id', $invitation['id'])->update($invitationData);
            $invitationId = (int) $invitation['id'];
        } else {
            $invitationData += ['ic_passport' => 'EMAP-DEMO-001', 'created_at' => $now];
            $this->db->table('invitations')->insert($invitationData);
            $invitationId = (int) $this->db->insertID();
        }

        $visitor = $this->db->table('invitation_visitors')->where('invitation_id', $invitationId)->get()->getRowArray();
        $visitorData = [
            'full_name'      => 'Aisyah Demo Visitor',
            'ic_passport'    => 'EMAP-DEMO-001',
            'contact'        => '0123456789',
            'company'        => 'SafeG Demo',
            'check_in_time'  => date('Y-m-d H:i:s', time() - 1800),
            'check_out_time' => null,
            'updated_at'     => $now,
        ];
        if ($visitor) {
            $this->db->table('invitation_visitors')->where('id', $visitor['id'])->update($visitorData);
            $visitorId = (int) $visitor['id'];
        } else {
            $visitorData += ['invitation_id' => $invitationId, 'created_at' => $now];
            $this->db->table('invitation_visitors')->insert($visitorData);
            $visitorId = (int) $this->db->insertID();
        }

        $cardId = $this->assignDemoCard('DD0000000000000000000001', 'EMAP-SERIAL-001');
        $this->db->table('invitation_visitors')->where('id', $visitorId)->update(['visitor_card_id' => $cardId]);

        $this->db->table('visitor_card_logs')->where('invitation_id', $invitationId)->delete();
        $movements = [];
        // Start at the lobby so reader 01 visibly moves the visitor to Office.
        $sequence = array_slice($zones, 0, 1);
        foreach ($sequence as $index => $zone) {
            if (empty($zone['subLocationId'])) {
                continue;
            }
            $subName = null;
            foreach ($subByName as $name => $sub) {
                if ((int) $sub['id'] === (int) $zone['subLocationId']) {
                    $subName = $name;
                    break;
                }
            }
            $movements[] = [
                'invitation_id'  => $invitationId,
                'action'         => $index === 0 ? 'checkin' : 'door_access',
                'lane_id'        => $subName && isset($laneByName[$subName]) ? (int) $laneByName[$subName]['id'] : null,
                'sub_location_id'=> (int) $zone['subLocationId'],
                'scanned_at'     => date('Y-m-d H:i:s', time() - ((count($sequence) - $index) * 90)),
                'scan_source'    => 'admin',
                'created_at'     => $now,
            ];
        }
        if ($movements) {
            $this->db->table('visitor_card_logs')->insertBatch($movements);
        }
    }

    private function seedJohnDoe(array $zones, array $laneByName, array $subByName, ?int $clientId): void
    {
        $now = date('Y-m-d H:i:s');
        $invitationBuilder = $this->db->table('invitations')->where('ic_passport', 'EMAP-DEMO-002');
        $clientId === null ? $invitationBuilder->where('client_id', null) : $invitationBuilder->where('client_id', $clientId);
        $invitation = $invitationBuilder->get()->getRowArray();
        $data = [
            'client_id' => $clientId,
            'full_name' => 'John Doe',
            'contact' => '0198765432',
            'company' => 'Acme Industries',
            'invited_by' => 'Demo Host',
            'reason' => 'E-Map visitor demonstration',
            'status' => 'Approved',
            'checked_in_at' => $now,
            'updated_at' => $now,
        ];
        if ($invitation) {
            $this->db->table('invitations')->where('id', $invitation['id'])->update($data);
            $invitationId = (int) $invitation['id'];
        } else {
            $data += ['ic_passport' => 'EMAP-DEMO-002', 'created_at' => $now];
            $this->db->table('invitations')->insert($data);
            $invitationId = (int) $this->db->insertID();
        }

        $visitor = $this->db->table('invitation_visitors')->where('invitation_id', $invitationId)->get()->getRowArray();
        $visitorData = [
            'full_name' => 'John Doe',
            'ic_passport' => 'EMAP-DEMO-002',
            'contact' => '0198765432',
            'company' => 'Acme Industries',
            'check_in_time' => date('Y-m-d H:i:s', time() - 1200),
            'check_out_time' => null,
            'updated_at' => $now,
        ];
        if ($visitor) {
            $this->db->table('invitation_visitors')->where('id', $visitor['id'])->update($visitorData);
            $visitorId = (int) $visitor['id'];
        } else {
            $visitorData += ['invitation_id' => $invitationId, 'created_at' => $now];
            $this->db->table('invitation_visitors')->insert($visitorData);
            $visitorId = (int) $this->db->insertID();
        }

        $cardId = $this->assignDemoCard('DD0000000000000000000002', 'EMAP-SERIAL-002');
        $this->db->table('invitation_visitors')->where('id', $visitorId)->update(['visitor_card_id' => $cardId]);

        $this->db->table('visitor_card_logs')->where('invitation_id', $invitationId)->delete();
        $movements = [];
        foreach (array_slice($zones, 0, 1) as $index => $zone) {
            if (empty($zone['subLocationId'])) continue;
            $subName = null;
            foreach ($subByName as $name => $sub) {
                if ((int) $sub['id'] === (int) $zone['subLocationId']) {
                    $subName = $name;
                    break;
                }
            }
            $movements[] = [
                'invitation_id' => $invitationId,
                'action' => $index === 0 ? 'checkin' : 'door_access',
                'lane_id' => $subName && isset($laneByName[$subName]) ? (int) $laneByName[$subName]['id'] : null,
                'sub_location_id' => (int) $zone['subLocationId'],
                'scanned_at' => date('Y-m-d H:i:s', time() - ((4 - $index) * 75)),
                'scan_source' => 'admin',
                'created_at' => $now,
            ];
        }
        if ($movements) {
            $this->db->table('visitor_card_logs')->insertBatch($movements);
        }
    }

    private function assignDemoCard(string $cardEpc, string $serialNo): int
    {
        $now = date('Y-m-d H:i:s');
        $card = $this->db->table('visitor_cards')->where('card_id', $cardEpc)->get()->getRowArray();
        $data = [
            'serial_no' => $serialNo,
            'status' => 'in_use',
            'updated_at' => $now,
        ];

        if ($card) {
            $this->db->table('visitor_cards')->where('id', $card['id'])->update($data);
            return (int) $card['id'];
        }

        $data += ['card_id' => $cardEpc, 'created_at' => $now];
        $this->db->table('visitor_cards')->insert($data);
        return (int) $this->db->insertID();
    }
}
