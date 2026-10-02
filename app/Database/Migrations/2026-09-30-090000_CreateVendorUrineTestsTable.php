<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Real KPK Process List tracks a urine test history per vendor
 * (viewUrineHistoryDetails / viewUrineAttachment in process-list.component.ts)
 * — a subsystem VMS didn't have at all. This is a new, small table: one row
 * per test, with an optional attachment (lab slip / photo of the result).
 */
class CreateVendorUrineTestsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('vendor_urine_tests')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'vendor_id' => [
                'type'     => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'test_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'result' => [
                'type'       => 'ENUM',
                'constraint' => ['Negative', 'Positive'],
                'null'       => true,
            ],
            'remark' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('vendor_id');
        $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vendor_urine_tests');
    }

    public function down()
    {
        $this->forge->dropTable('vendor_urine_tests');
    }
}
