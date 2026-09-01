<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInvitationQrCredentialsTable extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('invitation_qr_credentials')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'invitation_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'client_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'token_hash' => [
                'type' => 'CHAR',
                'constraint' => 64,
            ],
            'token_last4' => [
                'type' => 'CHAR',
                'constraint' => 4,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'revoked_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addUniqueKey('invitation_id');
        $this->forge->addUniqueKey('token_hash');
        $this->forge->addKey(['client_id', 'expires_at']);
        $this->forge->createTable('invitation_qr_credentials');
    }

    public function down(): void
    {
        $this->forge->dropTable('invitation_qr_credentials', true);
    }
}
