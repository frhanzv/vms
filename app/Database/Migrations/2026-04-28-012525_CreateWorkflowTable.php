<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkflowTable extends Migration
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
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'step_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'step_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'step_order' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('workflows');
    }

    public function down()
    {
        $this->forge->dropTable('workflows');
    }
}
