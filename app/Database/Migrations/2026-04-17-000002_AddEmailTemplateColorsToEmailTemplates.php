<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailTemplateColorsToEmailTemplates extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('email_templates')) {
            return;
        }

        $fields = [];
        if (! $this->db->fieldExists('primary_color', 'email_templates')) {
            $fields['primary_color'] = [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => true,
                'after' => 'body',
            ];
        }
        if (! $this->db->fieldExists('content_bg_color', 'email_templates')) {
            $fields['content_bg_color'] = [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => true,
                'after' => 'primary_color',
            ];
        }
        if (! $this->db->fieldExists('text_color', 'email_templates')) {
            $fields['text_color'] = [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => true,
                'after' => 'content_bg_color',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('email_templates', $fields);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('email_templates')) {
            return;
        }

        if ($this->db->fieldExists('primary_color', 'email_templates')) {
            $this->forge->dropColumn('email_templates', 'primary_color');
        }
        if ($this->db->fieldExists('content_bg_color', 'email_templates')) {
            $this->forge->dropColumn('email_templates', 'content_bg_color');
        }
        if ($this->db->fieldExists('text_color', 'email_templates')) {
            $this->forge->dropColumn('email_templates', 'text_color');
        }
    }
}

