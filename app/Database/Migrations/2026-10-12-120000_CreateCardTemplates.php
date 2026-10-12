<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Card artwork that can be added / removed from Config > Card Template Management. */
class CreateCardTemplates extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'module'     => ['type' => 'VARCHAR', 'constraint' => 20],
            'slot'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 120],
            'file_path'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['module', 'slot', 'client_id']);
        $this->forge->createTable('card_templates', true);
    }

    public function down()
    {
        $this->forge->dropTable('card_templates', true);
    }
}
