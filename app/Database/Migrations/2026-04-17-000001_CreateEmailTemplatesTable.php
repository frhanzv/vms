<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailTemplatesTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('email_templates')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'body' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('email_templates');

        // Seed a few common template codes (optional starter set).
        $now = date('Y-m-d H:i:s');
        $seedCodes = [
            'VISITOR_INVITE',
            'VISITOR_REQ_REJECT',
            'VISITOR_INVITATION_APPROVAL_PENDING',
            'PORT_PASS_APPROVE_REJECT_WITH_QR',
            'VISITOR_REQ_APPROVAL',
        ];

        foreach ($seedCodes as $code) {
            $this->db->table('email_templates')->insert([
                'code' => $code,
                'subject' => null,
                'body' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('email_templates');
    }
}

