<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVendorWorkflowSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('vendor_workflow_settings')) {
            return;
        }
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'client_id'     => ['type' => 'INT', 'unsigned' => true, 'default' => 0], // 0 = platform default
            'step_key'      => ['type' => 'VARCHAR', 'constraint' => 40],
            'step_name'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'trigger_event' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['client_id', 'step_key']);
        $this->forge->createTable('vendor_workflow_settings');
    }

    public function down()
    {
        $this->forge->dropTable('vendor_workflow_settings', true);
    }
}
