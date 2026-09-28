<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fields found in KPK's real closed-list.component.html for VENDORPASS rows
 * that weren't captured in the original vendors table: receipt_no,
 * vehicle_registration, card_type (Permanent/Temporary — matches the
 * vendor_permanent.jpg / vendor_temporary.jpg template split in
 * process-list.component.ts), and card_status (Active/Inactive, a separate
 * concept from the application `status` workflow field).
 */
class AddClosedListFieldsToVendorsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vendors', [
            'receipt_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'app_no',
            ],
            'vehicle_registration' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'location_visited',
            ],
            'card_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Permanent', 'Temporary'],
                'null'       => true,
                'after'      => 'pass_expiry',
            ],
            'card_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Active', 'Inactive'],
                'default'    => 'Inactive',
                'null'       => false,
                'after'      => 'card_type',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', ['receipt_no', 'vehicle_registration', 'card_type', 'card_status']);
    }
}
