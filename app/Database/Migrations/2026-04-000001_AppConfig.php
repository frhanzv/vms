<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AppConfig extends Migration
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
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'active' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'Active',
            ],
            'day_to_send_first_alert' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
            ],
            'day_to_send_second_alert' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
            ],
            'day_to_block' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => 'CURRENT_TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => 'CURRENT_TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'extra'   => 'on update CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('settings', true);
    }
}