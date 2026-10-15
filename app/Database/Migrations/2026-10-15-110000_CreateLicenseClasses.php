<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Config > License Class (same as KPK): a master list of driving licence classes with a minimum age.
 * client_id NULL = shared by every client (managed by the platform admin); otherwise the client's own.
 * Nothing is seeded - the list starts empty and each client adds its own.
 */
class CreateLicenseClasses extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('license_classes')) {
            return;
        }
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'min_age'    => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 0],
            'status'     => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('client_id');
        $this->forge->createTable('license_classes');
    }

    public function down()
    {
        $this->forge->dropTable('license_classes', true);
    }
}
