<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/** Removes what VehicleDummySeeder added (vehicles DUMMY-V-*, visits with version 99, the "DUMMY ..." vehicle types). */
class VehicleDummyCleanup extends Seeder
{
    public function run()
    {
        $db  = $this->db;
        $ids = array_column($db->table('vehicles')->select('id')->like('app_no', 'DUMMY-V-', 'after')->get()->getResultArray(), 'id');

        if ($ids) {
            foreach (['vehicle_drivers', 'vehicle_stickers', 'vehicle_files', 'vehicle_status_logs', 'vehicle_reissues'] as $t) {
                if ($db->tableExists($t)) {
                    $db->table($t)->whereIn('vehicle_id', $ids)->delete();
                }
            }
            $db->table('vehicles')->whereIn('id', $ids)->delete();
        }

        // visits: the tap logs of the dummy visits are matched by their exact check-in / check-out times
        if ($db->tableExists('vendor_visits')) {
            $v = $db->table('vendor_visits')->where('version', 99)->get()->getResultArray();
            if ($db->tableExists('vendor_card_logs')) {
                foreach ($v as $r) {
                    foreach (['checkin' => $r['check_in_time'], 'checkout' => $r['check_out_time']] as $action => $at) {
                        if ($at) {
                            $db->table('vendor_card_logs')->where(['vendor_id' => $r['vendor_id'], 'action' => $action, 'scanned_at' => $at])->delete();
                        }
                    }
                }
            }
            $db->table('vendor_visits')->where('version', 99)->delete();
        }
        if ($db->tableExists('staff_visits')) {
            $db->table('staff_visits')->where('version', 99)->delete();
        }
        $db->table('vehicle_types')->like('name', 'DUMMY ', 'after')->delete();

        echo 'Removed ' . count($ids) . " dummy vehicles and their gate visits.\n";
    }
}
