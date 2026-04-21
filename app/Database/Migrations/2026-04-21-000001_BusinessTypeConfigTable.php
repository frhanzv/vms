<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBusinessTypesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'business_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'reg_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            // Boolean/Flag fields based on your table checkmarks
            'ledger'                    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'haulier'                   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'lpk_license_no'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'lpk_license_no_optional'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'lpk_ancillary_contractor'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'customs_license_no'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sst_reg_no'                => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'business_vol'              => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'trade_ref_no'              => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'bank_info'                 => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'operator_code'             => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'copy_board_director_ic'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'apad_certificate_no'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'license_expiry_date'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'warehouse_info'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'nature_of_business'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'pli'                       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'ACTIVE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('business_types');
    }

    public function down()
    {
        $this->forge->dropTable('business_types');
    }
}