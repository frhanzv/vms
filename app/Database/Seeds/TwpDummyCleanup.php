<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/** Removes what TwpDummySeeder added (requests DUMMY-WP-*, their workers / visits / logs, cards DUMMY-TWP-*). */
class TwpDummyCleanup extends Seeder
{
    public function run()
    {
        $db   = $this->db;
        $reqs = array_column($db->table('twp_requests')->select('id')->like('app_no', 'DUMMY-WP-', 'after')->get()->getResultArray(), 'id');

        if ($reqs) {
            $workers = array_column($db->table('twp_workers')->select('id')->whereIn('request_id', $reqs)->get()->getResultArray(), 'id');
            if ($workers) {
                $db->table('twp_card_logs')->whereIn('worker_id', $workers)->delete();
                $db->table('twp_visits')->whereIn('worker_id', $workers)->delete();
                $db->table('twp_workers')->whereIn('id', $workers)->delete();
            }
            $db->table('twp_requests')->whereIn('id', $reqs)->delete();
        }
        $db->table('twp_cards')->like('card_no', 'DUMMY-TWP-', 'after')->delete();

        echo 'Removed ' . count($reqs) . " dummy TWP requests and the dummy TWP cards.\n";
    }
}
