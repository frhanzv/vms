<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Per-client choice of which COLUMNS a list page shows (Config > List Columns).
 * One row per client / list / column; no row = the column is visible, so
 * nothing changes until an admin switches a column off.
 */
class CreateClientListColumns extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('client_list_columns')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'list_key'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'column_key' => ['type' => 'VARCHAR', 'constraint' => 80],
            'is_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['client_id', 'list_key', 'column_key']);
        $this->forge->createTable('client_list_columns');
    }

    public function down()
    {
        $this->forge->dropTable('client_list_columns', true);
    }
}
