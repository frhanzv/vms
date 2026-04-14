<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitorTypeIdToInvitations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('invitations', [
            'visitor_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'reason',
            ],
        ]);

        $this->db->query('
            ALTER TABLE invitations
            ADD CONSTRAINT fk_invitations_visitor_type
            FOREIGN KEY (visitor_type_id) REFERENCES visitor_types(id)
            ON DELETE SET NULL ON UPDATE CASCADE
        ');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE invitations DROP FOREIGN KEY fk_invitations_visitor_type');
        $this->forge->dropColumn('invitations', 'visitor_type_id');
    }
}
