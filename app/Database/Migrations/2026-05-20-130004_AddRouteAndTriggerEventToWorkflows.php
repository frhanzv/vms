<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRouteAndTriggerEventToWorkflows extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('workflows');

        if (! in_array('route', $fields)) {
            $this->forge->addColumn('workflows', [
                'route' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'step_key',
                ],
            ]);
        }

        if (! in_array('trigger_event', $fields)) {
            $this->forge->addColumn('workflows', [
                'trigger_event' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'route',
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('workflows');

        if (in_array('trigger_event', $fields)) {
            $this->forge->dropColumn('workflows', 'trigger_event');
        }

        if (in_array('route', $fields)) {
            $this->forge->dropColumn('workflows', 'route');
        }
    }
}
