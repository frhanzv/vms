<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomFormDataToInvitations extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('custom_form_data', 'invitations')) {
            $this->forge->addColumn('invitations', [
                'custom_form_data' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'checked_in_at',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('custom_form_data', 'invitations')) {
            $this->forge->dropColumn('invitations', 'custom_form_data');
        }
    }
}
