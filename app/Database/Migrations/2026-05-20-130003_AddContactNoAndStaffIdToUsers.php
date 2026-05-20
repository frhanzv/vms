<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactNoAndStaffIdToUsers extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('users');

        if (! in_array('staff_id', $fields)) {
            $this->forge->addColumn('users', [
                'staff_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'company_id',
                ],
            ]);
        }

        if (! in_array('contact_no', $fields)) {
            $this->forge->addColumn('users', [
                'contact_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'email',
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('users');

        if (in_array('contact_no', $fields)) {
            $this->forge->dropColumn('users', 'contact_no');
        }

        if (in_array('staff_id', $fields)) {
            $this->forge->dropColumn('users', 'staff_id');
        }
    }
}
