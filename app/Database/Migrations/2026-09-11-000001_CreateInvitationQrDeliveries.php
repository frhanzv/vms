<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateInvitationQrDeliveries extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'invitation_id' => ['type' => 'INT', 'unsigned' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'pending'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('invitation_id', true);
        $this->forge->createTable('invitation_qr_deliveries', true);
    }
    public function down(): void
    {
        $this->forge->dropTable('invitation_qr_deliveries', true);
    }
}
