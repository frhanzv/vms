<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubInviteColumnsToInvitations extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('invitations')) {
            return;
        }

        $fields = $db->getFieldNames('invitations');
        $toAdd = [];

        if (! in_array('allow_sub_invites', $fields, true)) {
            $toAdd['allow_sub_invites'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'When set, invitee may create additional invitations from registration link',
            ];
        }

        if (! in_array('parent_invitation_id', $fields, true)) {
            $toAdd['parent_invitation_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Set when this invitation was created by an invitee with allow_sub_invites',
            ];
        }

        if ($toAdd !== []) {
            $this->forge->addColumn('invitations', $toAdd);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('invitations')) {
            return;
        }

        $fields = $db->getFieldNames('invitations');
        $drop = [];
        if (in_array('allow_sub_invites', $fields, true)) {
            $drop[] = 'allow_sub_invites';
        }
        if (in_array('parent_invitation_id', $fields, true)) {
            $drop[] = 'parent_invitation_id';
        }
        if ($drop !== []) {
            $this->forge->dropColumn('invitations', $drop);
        }
    }
}
