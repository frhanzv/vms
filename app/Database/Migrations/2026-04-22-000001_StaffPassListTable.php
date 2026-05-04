<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateStaffTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'app_no' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'ic_passport' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'staff_no' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'suspension_period' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'next_action' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'card_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'card_expiry' => ['type' => 'DATE', 'null' => true],
            'remark' => ['type' => 'TEXT', 'null' => true],

            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],

            'location_access' => ['type' => 'TEXT', 'null' => true],
            'date_of_application' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'type_of_application' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'designation' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'resident' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'sub_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'date_of_birth' => ['type' => 'DATE', 'null' => true],
            'sex' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'name_on_staff_pass' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'contact_number' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'department' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],

            'address_1' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'address_2' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'address_3' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'city' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'state' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'postal_code' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'country' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],

            'csp_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'csp_expiry_date' => ['type' => 'DATE', 'null' => true],

            'evetting_date_of_application' => ['type' => 'DATE', 'null' => true],
            'evetting_date_of_result' => ['type' => 'DATE', 'null' => true],
            'evetting_result' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],

            'government_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'other_doc' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('staff', true);
    }

    public function down()
    {
        $this->forge->dropTable('staff');
    }
}
