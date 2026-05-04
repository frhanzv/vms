<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientNotificationSettingsTable extends Migration
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
            'channel' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'email, whatsapp, telegram',
            ],
            'notification_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'invitation_sent, request_approved, request_rejected, registration_submitted, reminder',
            ],
            'enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
        $this->forge->addUniqueKey(['company_id', 'channel', 'notification_type']);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('client_notification_settings');
    }

    public function down()
    {
        $this->forge->dropTable('client_notification_settings', true);
    }
}
