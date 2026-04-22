<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class BusinessTypeConfig extends Migration
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
                'constraint' => 100,
                'null'       => false,
            ],
            'reg_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'ledger' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'haulier' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'lpk_license_no' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'lpk_license_no_optional' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'lpk_ancillary_contractor' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'customs_license_no' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'sst_reg_no' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'business_vol' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'trade_ref_no' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'bank_info' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'operator_code' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'copy_board_director_ic' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'apad_certificate_no' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'license_expiry_date' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'warehouse_info' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'nature_of_business' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'pli' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'null'    => true,
                'default' => 0,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'Active',
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'extra'   => 'on update CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('businesstype', true);
    }

    public function down()
    {
        $this->forge->dropTable('businesstype', true);
    }
}