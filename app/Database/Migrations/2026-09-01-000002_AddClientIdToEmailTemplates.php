<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClientIdToEmailTemplates extends Migration
{
    public function up(): void
    {
        $indexes = $this->db->query('SHOW INDEX FROM email_templates WHERE Non_unique = 0')->getResultArray();
        foreach ($indexes as $index) {
            if (($index['Column_name'] ?? '') === 'code' && ($index['Key_name'] ?? '') !== 'PRIMARY') {
                $keyName = str_replace('`', '``', (string) $index['Key_name']);
                $this->db->query("ALTER TABLE email_templates DROP INDEX `{$keyName}`");
                break;
            }
        }

        if (! $this->db->fieldExists('client_id', 'email_templates')) {
            $this->forge->addColumn('email_templates', [
                'client_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'id',
                ],
            ]);
            $this->db->query('CREATE UNIQUE INDEX uq_email_templates_client_code ON email_templates (client_id, code)');
        }
    }

    public function down(): void
    {
        if ($this->db->fieldExists('client_id', 'email_templates')) {
            $this->db->query('DROP INDEX uq_email_templates_client_code ON email_templates');
            $this->forge->dropColumn('email_templates', 'client_id');
            $this->db->query('CREATE UNIQUE INDEX code ON email_templates (code)');
        }
    }
}
