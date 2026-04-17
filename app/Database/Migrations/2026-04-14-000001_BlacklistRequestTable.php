<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BlacklistRequestTable extends Migration
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
            'created_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'blacklist_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'ic_passport_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'staff_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'pending', 'closed'],
                'default'    => 'pending',
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Type of blacklist entry',
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
        $this->forge->addKey('ic_passport_no');
        $this->forge->addKey('staff_id');
        $this->forge->addKey('status');
        $this->forge->addKey('blacklist_date');
        $this->forge->createTable('blacklist');
    }

    public function down()
    {
        $this->forge->dropTable('blacklist');
    }
}