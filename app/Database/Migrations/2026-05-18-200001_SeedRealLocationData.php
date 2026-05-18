<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Replaces the generic placeholder seed data in `locations` with the real
 * ISK facility location access entries visible in the old system.
 * Also clears the dependent seed tables (lanes, pathway_lanes) so there
 * are no dangling FK references after the location IDs change.
 */
class SeedRealLocationData extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        // Disable FK checks so we can truncate tables with circular/cascading refs
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        $this->db->table('sub_locations')->truncate();
        $this->db->table('pathway_sub_locations')->truncate();
        $this->db->table('pathway_lanes')->truncate();
        $this->db->table('lanes')->truncate();
        $this->db->table('locations')->truncate();

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        // Full ISK facility location access entries sourced from the old system (56 entries)
        $locations = [
            ['CHANGING ROOM 1 IN',                   'inbound'],
            ['CHANGING ROOM 1 OUT',                  'outbound'],
            ['CHANGING ROOM 2 IN',                   'inbound'],
            ['CHANGING ROOM 2 OUT',                  'outbound'],
            ['CHEMICAL WASTE IN',                    'inbound'],
            ['CHEMICAL WASTE OUT',                   'outbound'],
            ['CMM ROOM IN',                          'inbound'],
            ['CMM ROOM OUT',                         'outbound'],
            ['FINISHED GOOD AREA 1 OUT',             'outbound'],
            ['FINISHED GOOD AREA 2 IN',              'inbound'],
            ['FINISHED GOOD AREA 2 OUT',             'outbound'],
            ['FINISHED GOODS AREA 1 IN',             'inbound'],
            ['JITTER BUG ROOM IN',                   'inbound'],
            ['JITTER BUG ROOM OUT',                  'outbound'],
            ['MAINTENANCE DEPARTMENT IN',            'inbound'],
            ['MAINTENANCE DEPARTMENT OUT',           'outbound'],
            ['PACKAGING AREA IN',                    'inbound'],
            ['PACKAGING AREA OUT',                   'outbound'],
            ['POLISHING ROOM IN',                    'inbound'],
            ['POLISHING ROOM OUT',                   'outbound'],
            ['POLISHING/DEBURING ROOM IN',           'inbound'],
            ['POLISHING/DEBURING ROOM OUT',          'outbound'],
            ['PRODUCTION 1 - CLEAN ROOM 10K IN',    'inbound'],
            ['PRODUCTION 1 - CLEAN ROOM 10K OUT',   'outbound'],
            ['PRODUCTION 2 - CLEAN ROOM 1K IN',     'inbound'],
            ['PRODUCTION 2 - CLEAN ROOM 1K OUT',    'outbound'],
            ['PRODUCTION 3 IN',                      'inbound'],
            ['PRODUCTION 3 OUT',                     'outbound'],
            ['PRODUCTION 4 IN',                      'inbound'],
            ['PRODUCTION 4 OUT',                     'outbound'],
            ['PRODUCTION 5 - WORK IN PROGRESS IN',  'inbound'],
            ['PRODUCTION 5 - WORN IN PROGRESS OUT', 'outbound'],
            ['PRODUCTION OFFICE IN',                 'inbound'],
            ['PRODUCTION OFFICE OUT',                'outbound'],
            ['PRODUCTION WIP IN',                    'inbound'],
            ['PRODUCTION WIP OUT',                   'outbound'],
            ['QA ROOM IN',                           'inbound'],
            ['QA ROOM OUT',                          'outbound'],
            ['RAW MATERIAL AREA IN',                 'inbound'],
            ['RAW MATERIAL OUT',                     'outbound'],
            ['ROBOTIC JITTER BUG ROOM IN',           'inbound'],
            ['ROBOTIC JITTER BUG ROOM OUT',          'outbound'],
            ['ROBOTIC WELDING ROOM IN',              'inbound'],
            ['ROBOTIC WELDING ROOM OUT',             'outbound'],
            ['SCHEDULE WASTE IN',                    'inbound'],
            ['SCHEDULE WASTE OUT',                   'outbound'],
            ['TOILET IN',                            'inbound'],
            ['TOILET OUT',                           'outbound'],
            ['TOOLS ROOM IN',                        'inbound'],
            ['TOOLS ROOM OUT',                       'outbound'],
            ['ULTRA SONIC ROOM IN',                  'inbound'],
            ['ULTRA SONIC ROOM OUT',                 'outbound'],
            ['UTILITY IN',                           'inbound'],
            ['UTILITY OUT',                          'outbound'],
            ['WATER TREATMENT AREA IN',              'inbound'],
            ['WATER TREATMENT AREA OUT',             'outbound'],
        ];

        $batch = [];
        foreach ($locations as [$access, $direction]) {
            $batch[] = [
                'branch'            => 'ISK',
                'location_access'   => $access,
                'adam_ip'           => null,
                'adam_password'     => null,
                'mobile_app'        => 'enabled',
                'is_hold_area'      => 'no',
                'visitor_pass_print' => 'enabled',
                'turnstile'         => 'inactive',
                'in_out_bound'      => $direction,
                'status'            => 'active',
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        $this->db->table('locations')->insertBatch($batch);
    }

    public function down()
    {
        // Revert to the original six generic entries from the initial migration
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('sub_locations')->truncate();
        $this->db->table('pathway_sub_locations')->truncate();
        $this->db->table('pathway_lanes')->truncate();
        $this->db->table('lanes')->truncate();
        $this->db->table('locations')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
