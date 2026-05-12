<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * When device_assignments is empty, seed Check-In + Check-Out rows per lane so a fresh
 * `php spark migrate` shows 26 devices in the UI (13 lanes × 2). Extra lanes beyond the
 * first 13 by id are skipped here so the seeded list stays at 26; add more assignments in the app if needed.
 */
class SeedDeviceAssignmentsFromLanesIfEmpty extends Migration
{
    /** First N lanes (by id) receive seeded device pairs; N × 2 = 26 devices. */
    private const SEED_LANE_LIMIT = 13;

    public function up()
    {
        if (! $this->db->tableExists('device_assignments') || ! $this->db->tableExists('lanes')) {
            return;
        }

        if ($this->db->table('device_assignments')->countAllResults() > 0) {
            return;
        }

        $lanes = $this->db->table('lanes')->orderBy('id', 'ASC')->get()->getResultArray();
        if ($lanes === []) {
            return;
        }

        $lanes = array_slice($lanes, 0, self::SEED_LANE_LIMIT);

        $pick = static function (array $lane, array $cols): ?string {
            foreach ($cols as $col) {
                $ip = trim((string) ($lane[$col] ?? ''));
                if ($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    return $ip;
                }
            }

            return null;
        };

        $now        = date('Y-m-d H:i:s');
        $seq        = 1;
        $rows       = [];
        $hasVersion = $this->db->fieldExists('version', 'device_assignments');

        foreach ($lanes as $lane) {
            $laneId = (int) $lane['id'];

            $ipIn = $pick($lane, ['kiosk_ip', 'antena_ip', 'rfid_reader_ip']);
            if ($ipIn === null) {
                $ipIn = '192.168.0.' . (200 + $seq);
            }

            $rowIn = [
                'device_id'             => '008825113' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
                'ip_address'            => $ipIn,
                'status'                => 'Offline',
                'registration_status'   => 'Registered',
                'location_id'           => $laneId,
                'type'                  => 'Check-In',
                'last_heartbeat'        => $now,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
            if ($hasVersion) {
                $rowIn['version'] = 1;
            }
            $rows[] = $rowIn;
            $seq++;

            $ipOut = $pick($lane, ['antena_ip', 'kiosk_ip', 'rfid_reader_ip']);
            if ($ipOut === null || $ipOut === $ipIn) {
                $ipOut = '192.168.0.' . (200 + $seq);
            }

            $rowOut = [
                'device_id'             => '008825113' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
                'ip_address'            => $ipOut,
                'status'                => 'Offline',
                'registration_status'   => 'Registered',
                'location_id'           => $laneId,
                'type'                  => 'Check-Out',
                'last_heartbeat'        => $now,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
            if ($hasVersion) {
                $rowOut['version'] = 1;
            }
            $rows[] = $rowOut;
            $seq++;
        }

        $this->db->table('device_assignments')->insertBatch($rows);
    }

    public function down()
    {
    }
}
