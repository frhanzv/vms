<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientMessagingCredentialsTable extends Migration
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
                'comment'    => 'whatsapp, telegram',
            ],
            'phone_number_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'WhatsApp Business phone number ID',
            ],
            'access_token' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'WhatsApp access token / Telegram bot token',
            ],
            'is_active' => [
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
        $this->forge->addUniqueKey(['company_id', 'channel']);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('client_messaging_credentials');
    }

    public function down()
    {
        $this->forge->dropTable('client_messaging_credentials', true);
    }
}
