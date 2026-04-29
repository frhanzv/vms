<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientFormFieldsTable extends Migration
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
            'form_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'field_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'is_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['company_id', 'form_type', 'field_key']);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('client_form_fields');
    }

    public function down()
    {
        $this->forge->dropTable('client_form_fields', true);
    }
}
