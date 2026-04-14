<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVersionColumnsForConcurrency extends Migration
{
    public function up()
    {
        $tables = [
            'invitations',
            'invitation_visitors',
            'visitor_cards',
            'security_alerts',
            'roles',
            'companies',
            'sub_companies',
            'countries',
            'states',
            'cities',
            'departments',
            'designations',
            'locations',
            'lanes',
            'reject_reasons',
            'videos',
            'visit_reasons',
            'visitor_types',
            'device_assignments',
            'users',
        ];

        foreach ($tables as $table) {
            if ($this->db->tableExists($table) && !$this->db->fieldExists('version', $table)) {
                $this->forge->addColumn($table, [
                    'version' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'default' => 1,
                        'null' => false,
                        'after' => 'updated_at',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        $tables = [
            'invitations',
            'invitation_visitors',
            'visitor_cards',
            'security_alerts',
            'roles',
            'companies',
            'sub_companies',
            'countries',
            'states',
            'cities',
            'departments',
            'designations',
            'locations',
            'lanes',
            'reject_reasons',
            'videos',
            'visit_reasons',
            'visitor_types',
            'device_assignments',
            'users',
        ];

        foreach ($tables as $table) {
            if ($this->db->tableExists($table) && $this->db->fieldExists('version', $table)) {
                $this->forge->dropColumn($table, 'version');
            }
        }
    }
}
