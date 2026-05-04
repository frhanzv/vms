<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWhatsappTemplatesTable extends Migration
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
            'company_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'notification_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'invitation_sent, request_approved, request_rejected, registration_submitted, reminder',
            ],
            'template_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Template name as registered in Meta Business Manager',
            ],
            'language_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'en_US',
                'comment'    => 'e.g. en_US, ms_MY',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['company_id', 'notification_type']);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('whatsapp_templates');
    }

    public function down()
    {
        $this->forge->dropTable('whatsapp_templates', true);
    }
}
