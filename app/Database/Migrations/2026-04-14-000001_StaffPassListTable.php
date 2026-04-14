<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStaffPassRequestTable extends Migration
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
            'app_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Application number',
            ],
            'date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'ic_passport' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'comment'    => 'IC or Passport number',
            ],
            'staff_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'suspension_period' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'e.g. 2024-01-01 to 2024-06-01',
            ],
            'next_action' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'card_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Active', 'Inactive'],
                'null'       => true,
            ],
            'card_expiry' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'remark' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('app_no');
        $this->forge->addKey('staff_no');
        $this->forge->addKey('ic_passport');
        $this->forge->addKey('card_status');
        $this->forge->addKey('date');
        $this->forge->createTable('staff_pass_requests');
    }

    public function down()
    {
        $this->forge->dropTable('staff_pass_requests');
    }
}