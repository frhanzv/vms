<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Which cards (tiles / charts / lists) each dashboard shows.
 * Two levels share this table:
 *   scope_type 'client' — the admin decides which cards a client has at all
 *   scope_type 'user'   — each person narrows that down for themselves
 * No row = the card is shown, so nothing changes until someone switches one off.
 */
class CreateDashboardCardSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('dashboard_card_settings')) {
            return;
        }

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'scope_type'    => ['type' => 'VARCHAR', 'constraint' => 10],
            'scope_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'dashboard_key' => ['type' => 'VARCHAR', 'constraint' => 30],
            'card_key'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'is_visible'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['scope_type', 'scope_id', 'dashboard_key', 'card_key']);
        $this->forge->createTable('dashboard_card_settings');
    }

    public function down()
    {
        $this->forge->dropTable('dashboard_card_settings', true);
    }
}
