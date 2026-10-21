<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Dummy Cargo | Non-Cargo vehicles (vehicle_group = cnc), in every stage the lists show.
 *
 *   php spark db:seed VehicleDummyCncSeeder    add them (running it twice does nothing the second time)
 *   php spark db:seed VehicleDummyCleanup      removes these too (they are "DUMMY-V-C0001" ...)
 *
 * Uses the vendors / companies that already exist and your own vehicle types (truck / van types first).
 * No gate visits are added for these, so the dashboards and Monitoring stay as they are.
 */
class VehicleDummyCncSeeder extends Seeder
{
    public function run()
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        if (! $db->tableExists('vehicles') || ! $db->fieldExists('vehicle_group', 'vehicles')) {
            echo "Run php spark migrate first (the vehicle tables are missing).\n";
            return;
        }
        if ($db->table('vehicles')->like('app_no', 'DUMMY-V-C', 'after')->countAllResults() > 0) {
            echo "Dummy Cargo | Non-Cargo vehicles are already there. Run VehicleDummyCleanup first to start again.\n";
            return;
        }

        $vendors = $db->tableExists('vendors') ? $db->table('vendors')->select('id, full_name, ic_no, passport_no, company_id, contact_no, vendor_company_name')->orderBy('id')->limit(10)->get()->getResultArray() : [];
        $types   = $db->table('vehicle_types')->select('id, name, category')->where('status', 'active')->get()->getResultArray();
        if (! $types) {
            foreach ([['DUMMY Van', 'van'], ['DUMMY Truck', 'truck']] as [$n, $c]) {
                $db->table('vehicle_types')->insert(['name' => $n, 'category' => $c, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
                $types[] = ['id' => (int) $db->insertID(), 'name' => $n, 'category' => $c];
            }
        }
        // trucks and vans first: they are what Cargo | Non-Cargo is for
        usort($types, static fn($a, $b) => (int) in_array($b['category'], ['truck', 'van'], true) <=> (int) in_array($a['category'], ['truck', 'van'], true));

        $companies = ['Azreen Berhad', 'Conrad Sdn Bhd', 'Harbour Logistics Sdn Bhd', 'Delta Haulage Sdn Bhd'];
        $brands    = ['Hino 500', 'Isuzu NPR', 'Mitsubishi Fuso', 'Toyota Hiace', 'Nissan Urvan', 'Scania P360', 'Volvo FM', 'Hyundai Porter'];
        $ops       = ['Cargo', 'Non-Cargo'];
        $fields    = $db->getFieldNames('vehicles');
        $flow      = ['Draft', 'Pending', 'Approved', 'Rejected', 'Issued', 'Issued', 'Issued', 'Terminated'];

        for ($i = 0; $i < 16; $i++) {
            $v     = $vendors ? $vendors[$i % count($vendors)] : null;
            $type  = $types[$i % count($types)];
            $truck = in_array($type['category'], ['truck'], true);
            $status = $flow[$i % count($flow)];
            $when  = date('Y-m-d H:i:s', strtotime('-' . (($i * 29) % 280) . ' days'));
            $isCo  = $i % 2 === 0;
            $row = [
                'client_id'       => $v ? ((int) ($v['company_id'] ?? 0) ?: null) : null,
                'app_no'          => sprintf('DUMMY-V-C%04d', $i + 1),
                'app_date'        => substr($when, 0, 10),
                'type_of_app'     => 'New',
                'owner_type'      => $isCo ? 'Company' : 'Individual',
                'owner_name'      => $v['full_name'] ?? null,
                'owner_ic'        => $v ? (($v['ic_no'] ?? '') ?: ($v['passport_no'] ?? null)) : null,
                'company_name'    => $isCo ? $companies[$i % count($companies)] : ($v['vendor_company_name'] ?? null),
                'vendor_id'       => $v ? (int) $v['id'] : null,
                'contact_no'      => $v['contact_no'] ?? null,
                'vehicle_type_id' => (int) $type['id'],
                'plate_no'        => 'DMC' . (2001 + $i),
                'brand'           => $brands[$i % count($brands)],
                'year_made'       => (string) (2012 + ($i % 12)),
                'colour'          => ['White', 'Blue', 'Red', 'Silver'][$i % 4],
                'road_tax_expiry' => date('Y-m-d', strtotime('+' . (20 + $i * 25) . ' days')),
                'insurance_expiry' => date('Y-m-d', strtotime('+' . (45 + $i * 20) . ' days')),
                'vehicle_operation' => $ops[$i % 2],
                'truck_head_number' => $truck ? sprintf('TH-%04d', 100 + $i) : null,
                'trailer_number'  => $truck ? sprintf('TR-%04d', 200 + $i) : null,
                'chassis_number'  => sprintf('CHS%08d', 5000 + $i * 7),
                'working_load'    => $truck ? (string) (8 + $i % 12) . ' ton' : null,
                'truck_weight'    => $truck ? (string) (6 + $i % 9) . ' ton' : null,
                'status'          => $status,
                'reject_reason'   => $status === 'Rejected' ? 'Dummy: documents incomplete' : null,
                'remark'          => 'Dummy data',
                'payment_required' => 0,
                'payment_status'  => 'None',
                'pass_for'        => 'vendor',
                'vehicle_group'   => 'cnc',
                'created_at'      => $when,
                'updated_at'      => $when,
            ];
            if (in_array($status, ['Approved', 'Issued', 'Terminated', 'Rejected'], true)) {
                $row += ['approved_by' => 'Dummy seeder', 'approved_at' => $when];
            }
            if (in_array($status, ['Issued', 'Terminated'], true)) {
                $row += ['sticker_serial' => sprintf('DMC-S%04d', $i + 1), 'sticker_year' => date('Y'), 'pass_expiry' => date('Y-12-31'),
                    'issued_at' => $when, 'issued_by' => 'Dummy seeder', 'issue_date' => substr($when, 0, 10), 'collect_date' => substr($when, 0, 10)];
            }
            if ($status === 'Terminated') {
                $row['terminated_at'] = $when;
            }
            $db->table('vehicles')->insert(array_intersect_key($row, array_flip($fields)));
        }

        echo "Added 16 dummy Cargo | Non-Cargo vehicles (Draft, Pending, Approved, Rejected, Issued, Terminated).\n";
    }
}
