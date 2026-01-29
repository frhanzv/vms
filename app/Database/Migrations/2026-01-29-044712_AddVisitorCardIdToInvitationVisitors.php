<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitorCardIdToInvitationVisitors extends Migration
{
    public function up()
    {
        // Add visitor_card_id column to invitation_visitors table
        $fields = [
            'visitor_card_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'invitation_id'
            ]
        ];
        
        $this->forge->addColumn('invitation_visitors', $fields);
        
        // Add foreign key constraint
        $this->db->query('
            ALTER TABLE invitation_visitors 
            ADD CONSTRAINT fk_invitation_visitors_card 
            FOREIGN KEY (visitor_card_id) REFERENCES visitor_cards(id) 
            ON DELETE SET NULL ON UPDATE CASCADE
        ');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE invitation_visitors DROP FOREIGN KEY fk_invitation_visitors_card');
        
        // Drop column
        $this->forge->dropColumn('invitation_visitors', 'visitor_card_id');
    }
}
