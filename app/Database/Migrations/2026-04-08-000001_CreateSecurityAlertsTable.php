<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSecurityAlertsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'incident_type' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'comment' => 'e.g. Unauthorized Access Attempt, Visitor Overstay, Blackisted Visitor, Tailgating',
            ],
            'severity' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'critical'],
                'default' => 'medium',
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'invitation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'visitor_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'is_acknowledged' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'acknowledged_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'acknowledged_at' => [
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
        $this->forge->addKey(['severity', 'is_acknowledged']);
        $this->forge->addKey('created_at');
        $this->forge->createTable('security_alerts');
    }

    public function down()
    {
        $this->forge->dropTable('security_alerts');
    }
}
