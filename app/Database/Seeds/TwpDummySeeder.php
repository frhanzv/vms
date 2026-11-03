<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Dummy Temporary Work Pass data, so the TWP pages, the TWP dashboard, the TWP report and Gate Monitoring have something to show.
 *
 *   php spark db:seed TwpDummySeeder       add the dummy data (running it twice does nothing the second time)
 *   php spark db:seed TwpDummyCleanup      remove exactly what this seeder added
 *
 * It uses your first client and your existing lanes / location access.
 * Everything is marked: requests have app_no "DUMMY-WP-xxxx", cards have card_no "DUMMY-TWP-xx".
 */
class TwpDummySeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        foreach (['twp_requests', 'twp_workers', 'twp_cards', 'twp_visits', 'twp_card_logs'] as $t) {
            if (! $db->tableExists($t)) {
                echo "Table {$t} is missing - run php spark migrate first.\n";
                return;
            }
        }
        if ($db->table('twp_requests')->like('app_no', 'DUMMY-WP-', 'after')->countAllResults() > 0) {
            echo "Dummy TWP data is already there. Run TwpDummyCleanup first if you want to start again.\n";
            return;
        }

        $clientId = (int) ($db->table('clients')->select('id')->orderBy('id')->get(1)->getRowArray()['id'] ?? 0) ?: null;
        $userId   = (int) ($db->table('users')->select('id')->where('role', 'superadmin')->orderBy('id')->get(1)->getRowArray()['id'] ?? 0) ?: null;
        $lanes    = $db->query("SELECT l.id, loc.location_access FROM lanes l JOIN locations loc ON loc.id = l.location_id
                                WHERE l.status = 'active' AND loc.location_access IS NOT NULL ORDER BY l.id LIMIT 6")->getResultArray();
        $access   = implode(',', array_unique(array_column($lanes, 'location_access')));
        $lane     = static fn(int $i) => $lanes ? (int) $lanes[$i % count($lanes)]['id'] : null;
        $now      = date('Y-m-d H:i:s');
        $at       = static fn(string $rel) => date('Y-m-d H:i:s', strtotime($rel));

        // ---- card pool
        $cards = [];
        for ($i = 1; $i <= 15; $i++) {
            $no = sprintf('DUMMY-TWP-%02d', $i);
            $db->table('twp_cards')->insert(['company_id' => $clientId, 'card_id' => 'E2000000DUMMYTWP' . sprintf('%04d', $i), 'card_no' => $no,
                'status' => $i === 15 ? 'inactive' : 'active', 'created_by' => $userId, 'created_at' => $now, 'updated_at' => $now]);
            $cards[] = (int) $db->insertID();
        }
        $nextCard = 0;

        $companies = ['Bina Jaya Sdn Bhd', 'Teknik Elektrik Maju', 'Kuat Scaffolding Services', 'Indah Cat & Hiasan', 'Alam Hijau Landscape', 'Pantas Aircond Services'];
        $works     = ['Electrical wiring', 'Scaffolding', 'Painting', 'Landscaping', 'Aircond servicing', 'Roof repair'];
        $names     = ['Ahmad Faizal', 'Muthu Kumar', 'Lim Wei Jie', 'Rahman Ali', 'Siti Aminah', 'Rajesh Nair', 'Tan Ah Kow', 'Nguyen Van An', 'Bambang Susilo', 'Mohd Hafiz',
                      'Arif Hakim', 'Chong Kah Wai', 'Suresh Pillai', 'Joko Widodo', 'Zainal Abidin', 'Wong Mei Ling', 'Kamal Hassan', 'Ravi Shankar'];

        // [status, payment status, from, to, workers]; Approved ones get passes, cards and visits
        $plan = [
            ['Approved', 'Paid', '-2 days 08:00', '+5 days 18:00', 4],
            ['Approved', 'Paid', 'today 07:00', '+2 days 19:00', 3],
            ['Approved', 'Paid', '-10 days 08:00', '-3 days 18:00', 3],   // expired: gate refuses it
            ['Approved', 'None', '-1 days 08:00', '+10 days 18:00', 2],
            ['Pending', 'Pending', '+1 days 08:00', '+4 days 18:00', 3],
            ['Pending', 'Paid', '+2 days 08:00', '+6 days 18:00', 2],
            ['Rejected', 'Pending', '+1 days 08:00', '+3 days 18:00', 2],
        ];

        $n = 0;
        foreach ($plan as $i => [$status, $pay, $from, $to, $count]) {
            $created = $at('-' . (12 - $i) . ' days 10:00');
            $foreign = $i % 3 === 2;
            $db->table('twp_requests')->insert([
                'company_id' => $clientId, 'app_no' => sprintf('DUMMY-WP-%04d', $i + 1), 'receipt_no' => $pay === 'Paid' ? sprintf('RCPT-DUMMY-%04d', $i + 1) : null,
                'app_date' => $created, 'requested_by' => $userId, 'company_name' => $companies[$i % 6], 'company_reg_id' => sprintf('2019010%05d', 1234 + $i),
                'resident' => 'LOCAL', 'ic_no' => sprintf('8%05d-10-%04d', 10101 + $i, 5000 + $i), 'full_name' => $names[$i] . ' (Supervisor)',
                'contact_no' => '01' . (2345670 + $i), 'email' => 'twp' . ($i + 1) . '@example.com', 'sub_type' => 'TEMPORARY WORK PERMIT', 'type_of_work' => $works[$i % 6],
                'location_access' => $access, 'date_from' => $at($from), 'date_to' => $at($to),
                'payment_type' => $pay === 'Paid' ? ($i % 2 ? 'CASH' : 'FPX') : ($pay === 'None' ? 'NONE' : null),
                'amount' => $pay === 'None' ? 0 : $count * 10, 'payment_status' => $pay, 'payment_ref' => $pay === 'Paid' && ! ($i % 2) ? 'FPXDUMMY' . ($i + 1) : null,
                'status' => $status, 'remark' => 'Dummy data', 'reject_reason' => $status === 'Rejected' ? 'Incomplete worker documents' : null,
                'reviewed_by' => $status === 'Pending' ? null : $userId, 'reviewed_at' => $status === 'Pending' ? null : $at('-' . (11 - $i) . ' days 09:00'),
                'created_at' => $created, 'updated_at' => $created,
            ]);
            $reqId = (int) $db->insertID();

            for ($w = 0; $w < $count; $w++, $n++) {
                $approved = $status === 'Approved';
                $card     = $approved && $i !== 2 && $nextCard < 14 ? $cards[$nextCard++] : null;
                $db->table('twp_workers')->insert([
                    'request_id' => $reqId, 'name' => $names[$n % count($names)], 'resident' => $foreign ? 'FOREIGNER' : 'LOCAL',
                    'ic_no' => $foreign ? null : sprintf('9%05d-14-%04d', 20202 + $n, 6000 + $n), 'passport_no' => $foreign ? sprintf('A%08d', 1000 + $n) : null,
                    'contact_no' => '01' . (3456780 + $n), 'vehicle_no' => $w === 0 ? sprintf('WXY %04d', 1000 + $n) : null,
                    'pass_no' => $approved ? sprintf('DUMMY-TWP-P%04d', $n + 1) : null, 'qr_string' => $approved ? md5('dummy-twp-' . $n) : null,
                    'status' => $status, 'card_id' => $card, 'card_status' => $card ? 'Issued' : ($approved && $i === 2 ? 'Returned' : 'Not Issued'),
                    'issued_at' => $card ? $at($from) : null, 'issued_by' => $card ? $userId : null, 'returned_at' => $approved && $i === 2 ? $at($to) : null,
                    'created_at' => $created, 'updated_at' => $created,
                ]);
                $workerId = (int) $db->insertID();
                if (! $approved || $i === 3) {
                    continue;
                }

                // visits: past days are closed; on today some workers are still inside
                $days = $i === 2 ? [-9, -7, -5, -4] : ($i === 1 ? [0] : [-2, -1, 0]);
                foreach ($days as $k => $d) {
                    $in  = $at("{$d} days " . (7 + ($n + $k) % 3) . ':' . sprintf('%02d', (13 * $n) % 60));
                    $out = $d === 0 && $w % 2 === 0 ? null : date('Y-m-d H:i:s', strtotime($in) + (6 + ($n + $k) % 4) * 3600);
                    if (strtotime($in) > time()) {
                        continue;
                    }
                    $db->table('twp_visits')->insert(['worker_id' => $workerId, 'card_id' => $card, 'check_in_time' => $in, 'check_out_time' => $out,
                        'check_in_lane_id' => $lane($n + $k), 'check_out_lane_id' => $out ? $lane($n + $k + 1) : null, 'created_at' => $in, 'updated_at' => $out ?? $in]);
                    $db->table('twp_card_logs')->insert(['worker_id' => $workerId, 'card_id' => $card, 'action' => 'checkin', 'lane_id' => $lane($n + $k), 'message' => 'Dummy data', 'scanned_at' => $in]);
                    if ($out) {
                        $db->table('twp_card_logs')->insert(['worker_id' => $workerId, 'card_id' => $card, 'action' => 'checkout', 'lane_id' => $lane($n + $k + 1), 'message' => 'Dummy data', 'scanned_at' => $out]);
                    }
                }
            }
        }

        // a couple of refused taps (expired pass) for the denied log
        $expired = $db->query("SELECT w.id FROM twp_workers w JOIN twp_requests r ON r.id = w.request_id WHERE r.app_no = 'DUMMY-WP-0003' LIMIT 2")->getResultArray();
        foreach ($expired as $k => $e) {
            $db->table('twp_card_logs')->insert(['worker_id' => (int) $e['id'], 'card_id' => null, 'action' => 'denied', 'lane_id' => $lane($k),
                'message' => 'Dummy data: pass expired', 'scanned_at' => $at('-1 days 08:1' . $k)]);
        }

        echo "Added 7 dummy TWP requests, {$n} workers, 15 cards with visits and tap logs.\n";
    }
}
