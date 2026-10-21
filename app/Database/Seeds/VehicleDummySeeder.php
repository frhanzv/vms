<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Dummy vehicles + gate movements, so the Vehicle pages, Monitoring and the Vehicle dashboard have something to show.
 *
 *   php spark db:seed VehicleDummySeeder       add the dummy data (running it twice does nothing the second time)
 *   php spark db:seed VehicleDummyCleanup      remove exactly what this seeder added
 *
 * It uses the staff and vendor passes that already exist in your database (it does not create people),
 * and your own vehicle types (it only adds four basic ones if you have none).
 * Everything is marked: vehicles have app_no "DUMMY-V-xxxx", visits have version = 99, vehicle types are named "DUMMY ...".
 */
class VehicleDummySeeder extends Seeder
{
    private const MARK_VISIT = 99;

    public function run()
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        foreach (['vehicles', 'staff', 'vendors', 'staff_visits', 'vendor_visits'] as $t) {
            if (! $db->tableExists($t)) {
                echo "Table {$t} is missing - run php spark migrate first.\n";
                return;
            }
        }
        if ($db->table('vehicles')->like('app_no', 'DUMMY-V-', 'after')->countAllResults() > 0) {
            echo "Dummy vehicles are already there. Run VehicleDummyCleanup first if you want to start again.\n";
            return;
        }

        $staff   = $db->table('staff')->select('id, full_name, ic_passport, staff_no, company_id, contact_number, department')->orderBy('id')->limit(12)->get()->getResultArray();
        $vendors = $db->table('vendors')->select('id, full_name, ic_no, passport_no, company_id, contact_no, vendor_company_name')->orderBy('id')->limit(12)->get()->getResultArray();
        if (! $staff && ! $vendors) {
            echo "No staff or vendor passes exist yet, so there is nobody to attach vehicles to. Add some first.\n";
            return;
        }

        // ---- vehicle types
        $types = $db->table('vehicle_types')->select('id, name, category')->where('status', 'active')->get()->getResultArray();
        if (! $types) {
            foreach ([['DUMMY Car', 'car'], ['DUMMY Motorbike', 'motorbike'], ['DUMMY Van', 'van'], ['DUMMY Truck', 'truck']] as [$n, $c]) {
                $db->table('vehicle_types')->insert(['name' => $n, 'category' => $c, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
                $types[] = ['id' => (int) $db->insertID(), 'name' => $n, 'category' => $c];
            }
        }

        // ---- lanes (for the in / out lane columns)
        $lanes = $db->tableExists('lanes') ? array_column($db->table('lanes')->select('id')->limit(6)->get()->getResultArray(), 'id') : [];
        $lane  = static fn(int $i) => $lanes ? (int) $lanes[$i % count($lanes)] : null;

        $brands  = ['Toyota Hilux', 'Perodua Myvi', 'Proton X50', 'Honda City', 'Isuzu D-Max', 'Yamaha Y15', 'Nissan Navara', 'Hyundai Starex', 'Mitsubishi Triton', 'Ford Ranger', 'Honda RS150', 'Toyota Hiace'];
        $colours = ['White', 'Black', 'Silver', 'Red', 'Blue', 'Grey'];
        $fields  = $db->getFieldNames('vehicles');
        $ins     = static fn(array $row, array $cols) => array_intersect_key($row, array_flip($cols));

        $made = [];   // person key => [plate, vehicle id]
        $n    = 0;
        $people = [];
        foreach ($staff as $s)   { $people[] = ['staff', $s]; }
        foreach ($vendors as $v) { $people[] = ['vendor', $v]; }

        foreach ($people as $i => [$kind, $p]) {
            $n++;
            $type  = $types[$i % count($types)];
            $plate = 'DMY' . (1000 + $n);
            $age   = $i % 4 === 0 ? 0 : (($i * 37) % 300);   // some registered today, the rest spread over the year
            $when  = date('Y-m-d H:i:s', strtotime("-{$age} days"));
            $status = $i % 7 === 5 ? 'Pending' : ($i % 7 === 6 ? 'Approved' : 'Issued');
            $row = [
                'client_id'       => (int) ($p['company_id'] ?? 0) ?: null,
                'app_no'          => sprintf('DUMMY-V-%04d', $n),
                'app_date'        => substr($when, 0, 10),
                'type_of_app'     => 'New',
                'owner_type'      => $kind === 'staff' ? 'Staff' : 'Individual',
                'owner_name'      => $p['full_name'],
                'owner_ic'        => $kind === 'staff' ? ($p['ic_passport'] ?? null) : (($p['ic_no'] ?? '') ?: ($p['passport_no'] ?? null)),
                'company_name'    => $kind === 'vendor' ? ($p['vendor_company_name'] ?? null) : null,
                'staff_id'        => $kind === 'staff' ? (int) $p['id'] : null,
                'vendor_id'       => $kind === 'vendor' ? (int) $p['id'] : null,
                'contact_no'      => $kind === 'staff' ? ($p['contact_number'] ?? null) : ($p['contact_no'] ?? null),
                'vehicle_type_id' => (int) $type['id'],
                'plate_no'        => $plate,
                'brand'           => $brands[$i % count($brands)],
                'year_made'       => (string) (2015 + ($i % 10)),
                'colour'          => $colours[$i % count($colours)],
                'road_tax_expiry' => date('Y-m-d', strtotime('+' . (30 + $i * 20) . ' days')),
                'insurance_expiry' => date('Y-m-d', strtotime('+' . (60 + $i * 15) . ' days')),
                'status'          => $status,
                'remark'          => 'Dummy data',
                'payment_required' => 0,
                'payment_status'  => 'None',
                'pass_for'        => $kind === 'staff' ? 'staff' : 'vendor',
                'vehicle_group'   => 'public',
                'staff_no'        => $kind === 'staff' ? ($p['staff_no'] ?? null) : null,
                'department'      => $kind === 'staff' ? ($p['department'] ?? null) : null,
                'created_at'      => $when,
                'updated_at'      => $when,
            ];
            if ($status === 'Issued') {
                $row += ['sticker_serial' => sprintf('DMY-S%04d', $n), 'sticker_year' => date('Y'), 'pass_expiry' => date('Y-12-31'), 'issued_at' => $when, 'issued_by' => 'Dummy seeder', 'issue_date' => substr($when, 0, 10)];
            }
            $db->table('vehicles')->insert($ins($row, $fields));
            $vid = (int) $db->insertID();
            $made[] = [$kind, (int) $p['id'], $plate, $vid, $status];

            if ($kind === 'vendor' && $db->tableExists('vehicle_drivers')) {
                $db->table('vehicle_drivers')->insert(['vehicle_id' => $vid, 'vendor_id' => (int) $p['id'], 'created_at' => $when]);
            }
        }

        // ---- gate movements for issued vehicles: the last 12 months plus today
        $visits = 0;
        foreach ($made as $k => [$kind, $pid, $plate, $vid, $status]) {
            if ($status !== 'Issued') {
                continue;
            }
            $count = 6 + ($k * 5) % 14;
            for ($j = 0; $j < $count; $j++) {
                $daysAgo = $j === 0 && $k % 2 === 0 ? 0 : (($j * 23 + $k * 11) % 340);
                $in      = strtotime(sprintf('-%d days %02d:%02d:00', $daysAgo, 6 + ($j + $k) % 9, ($j * 13) % 60));
                if ($in > time()) {
                    $in = time() - 3600 * (1 + $j % 3);
                }
                $open  = $daysAgo === 0 && $j === 0 && $k % 4 === 0;                 // still inside
                $out   = $open ? null : $in + 3600 * (1 + ($j + $k) % 9) + 60 * (($j * 7) % 60);
                if ($out !== null && $out > time()) {
                    $out = $open ? null : time() - 60;
                }
                $inAt  = date('Y-m-d H:i:s', $in);
                $outAt = $out ? date('Y-m-d H:i:s', $out) : null;

                if ($kind === 'staff') {
                    $db->table('staff_visits')->insert(['staff_id' => $pid, 'check_in_time' => $inAt, 'check_out_time' => $outAt,
                        'check_in_lane_id' => $lane($j), 'check_out_lane_id' => $outAt ? $lane($j + 1) : null, 'version' => self::MARK_VISIT, 'created_at' => $inAt]);
                } else {
                    $db->table('vendor_visits')->insert(['vendor_id' => $pid, 'check_in_time' => $inAt, 'check_out_time' => $outAt, 'version' => self::MARK_VISIT, 'created_at' => $inAt]);
                    if ($db->tableExists('vendor_card_logs')) {
                        $db->table('vendor_card_logs')->insert(['vendor_id' => $pid, 'action' => 'checkin', 'lane_id' => $lane($j), 'scanned_at' => $inAt, 'created_at' => $inAt]);
                        if ($outAt) {
                            $db->table('vendor_card_logs')->insert(['vendor_id' => $pid, 'action' => 'checkout', 'lane_id' => $lane($j + 1), 'scanned_at' => $outAt, 'created_at' => $outAt]);
                        }
                    }
                }
                $visits++;
            }
        }

        echo 'Added ' . count($made) . " dummy vehicles and {$visits} gate visits.\n";
    }
}
