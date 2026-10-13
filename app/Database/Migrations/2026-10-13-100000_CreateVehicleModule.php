<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Vehicle pass module (ported from Safe-G KPK, annual vehicle pass only).
 * Payment is NOT handled here: payment_* columns are filled by the external
 * payment service (see App\Libraries\VehiclePayment).
 */
class CreateVehicleModule extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('vehicle_types')) {
            $this->forge->addField([
                'id'                   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'client_id'            => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'name'                 => ['type' => 'VARCHAR', 'constraint' => 100],
                'category'             => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'car'], // car, motorbike, van, truck, other
                'seater_req'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'working_height_req'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'working_load_req'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'truck_head_req'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'trailer_req'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'truck_weight_req'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'chassis_req'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'payment_required'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'duplicate_plate_ok'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'status'               => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'active'],
                'created_at'           => ['type' => 'DATETIME', 'null' => true],
                'updated_at'           => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('client_id');
            $this->forge->createTable('vehicle_types');
        }

        if (! $this->db->tableExists('vehicles')) {
            $this->forge->addField([
                'id'                 => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'client_id'          => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'app_no'             => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'app_date'           => ['type' => 'DATE', 'null' => true],
                'type_of_app'        => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'New'], // New / Renewal
                'owner_type'         => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'Individual'], // Individual / Company / Staff
                'owner_name'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'owner_ic'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'company_name'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'staff_id'           => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'vendor_id'          => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'contact_no'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'vehicle_type_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
                'plate_no'           => ['type' => 'VARCHAR', 'constraint' => 20],
                'brand'              => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
                'year_made'          => ['type' => 'VARCHAR', 'constraint' => 6, 'null' => true],
                'colour'             => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
                'road_tax_expiry'    => ['type' => 'DATE', 'null' => true],
                'insurance_expiry'   => ['type' => 'DATE', 'null' => true],
                'seater_number'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'working_height'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'working_load'       => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'truck_head_number'  => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
                'trailer_number'     => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
                'truck_weight'       => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'chassis_number'     => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
                'location_access'    => ['type' => 'TEXT', 'null' => true], // CSV of location codes
                'status'             => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'Draft'], // Draft, Pending, Approved, Rejected, Issued, Terminated
                'reject_reason'      => ['type' => 'TEXT', 'null' => true],
                'remark'             => ['type' => 'TEXT', 'null' => true],
                // payment (filled by the external payment service; null/None when the type needs no payment)
                'payment_required'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'payment_status'     => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'None'], // None, Pending, Paid, Failed
                'payment_ref'        => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
                'payment_type'       => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'receipt_no'         => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
                'amount'             => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
                // issuance
                'sticker_serial'     => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
                'sticker_year'       => ['type' => 'VARCHAR', 'constraint' => 6, 'null' => true],
                'pass_expiry'        => ['type' => 'DATE', 'null' => true],
                'issued_at'          => ['type' => 'DATETIME', 'null' => true],
                'issued_by'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'collector_name'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'requested_by'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'approved_by'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'approved_at'        => ['type' => 'DATETIME', 'null' => true],
                'created_at'         => ['type' => 'DATETIME', 'null' => true],
                'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('client_id');
            $this->forge->addKey('plate_no');
            $this->forge->addKey('status');
            $this->forge->createTable('vehicles');
        }

        if (! $this->db->tableExists('vehicle_files')) {
            $this->forge->addField([
                'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'vehicle_id'    => ['type' => 'INT', 'unsigned' => true],
                'label'         => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
                'file_path'     => ['type' => 'VARCHAR', 'constraint' => 255],
                'original_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('vehicle_id');
            $this->forge->createTable('vehicle_files');
        }

        if (! $this->db->tableExists('vehicle_status_logs')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'vehicle_id'  => ['type' => 'INT', 'unsigned' => true],
                'from_status' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true],
                'to_status'   => ['type' => 'VARCHAR', 'constraint' => 12],
                'note'        => ['type' => 'TEXT', 'null' => true],
                'done_by'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('vehicle_id');
            $this->forge->createTable('vehicle_status_logs');
        }
    }

    public function down()
    {
        foreach (['vehicle_status_logs', 'vehicle_files', 'vehicles', 'vehicle_types'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
