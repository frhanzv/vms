<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Visitor module, KPK fields:
 *  - invitations.location_visited / meeting_session_id: KPK New Invitation "Location Visited" and, for a
 *    meeting room, the "Meeting Session" it books (meeting_sessions.num_visitor_reserve counts the seats)
 *  - invitations.last_access_at: KPK "Last Access", when the visitor last opened the invitation link
 */
class VisitorKpkFields extends Migration
{
    private const COLS = [
        'location_visited'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'location'],
        'meeting_session_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'location_visited'],
        'last_access_at'     => ['type' => 'DATETIME', 'null' => true, 'after' => 'link_expiry'],
    ];

    public function up()
    {
        foreach (self::COLS as $name => $def) {
            if (! $this->db->fieldExists($name, 'invitations')) {
                $this->forge->addColumn('invitations', [$name => $def]);
            }
        }
    }

    public function down()
    {
        foreach (array_keys(self::COLS) as $name) {
            if ($this->db->fieldExists($name, 'invitations')) {
                $this->forge->dropColumn('invitations', $name);
            }
        }
    }
}
