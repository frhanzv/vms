<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSecurityAlertPrioritiesTable extends Migration
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
            'alert_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high'],
                'default' => 'medium',
            ],
            'response_time' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'default' => 'Within 15 minutes',
            ],
            'notification_scope' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Security Team',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'version' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 1,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('alert_name');
        $this->forge->createTable('security_alert_priorities');

        $now = date('Y-m-d H:i:s');
        $this->db->table('security_alert_priorities')->insertBatch([
            [
                'alert_name' => 'Access Denied Incidents',
                'priority' => 'high',
                'response_time' => 'Immediate',
                'notification_scope' => 'All Staff',
                'created_at' => $now,
                'updated_at' => $now,
                'version' => 1,
            ],
            [
                'alert_name' => 'Visitor Overstay Alerts',
                'priority' => 'medium',
                'response_time' => 'Within 15 minutes',
                'notification_scope' => 'Security Team',
                'created_at' => $now,
                'updated_at' => $now,
                'version' => 1,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('security_alert_priorities', true);
    }
}
