<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompaniesTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'registration_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'address' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'contact_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->addKey('name');
        $this->forge->addKey('status');
        $this->forge->createTable('companies');

        // Insert sample data
        $data = [
            [
                'name' => 'ABC Construction Sdn Bhd',
                'registration_no' => '202301234-V',
                'address' => 'No. 123, Jalan Teknologi, Kuala Lumpur',
                'contact_no' => '03-12345678',
                'email' => 'info@abcconstruction.com',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Express Delivery Services',
                'registration_no' => '201905678-K',
                'address' => 'No. 45, Jalan Perindustrian, Selangor',
                'contact_no' => '03-87654321',
                'email' => 'support@expressdelivery.com',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Tech Engineering & Consultant',
                'registration_no' => '201712345-W',
                'address' => 'No. 78, Jalan Industri, Johor',
                'contact_no' => '07-22334455',
                'email' => 'contact@techeng.com',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('companies')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('companies');
    }
}
