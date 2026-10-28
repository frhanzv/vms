<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Kurita-style report templates: chosen sections + columns, a schedule, a format and email recipients. */
class ReportTemplates extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('report_templates')) {
            return;
        }
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'company_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 150],
            'description'       => ['type' => 'TEXT', 'null' => true],
            'sections'          => ['type' => 'JSON', 'null' => true],
            'report_layout'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'sectioned'],
            'frequency'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'manual'],
            'schedule_config'   => ['type' => 'JSON', 'null' => true],
            'format'            => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'xlsx'],
            'recipients'        => ['type' => 'JSON', 'null' => true],
            'is_active'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_by'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'last_generated_at' => ['type' => 'DATETIME', 'null' => true],
            'last_sent_at'      => ['type' => 'DATETIME', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('company_id');
        $this->forge->createTable('report_templates');
    }

    public function down()
    {
        $this->forge->dropTable('report_templates', true);
    }
}
