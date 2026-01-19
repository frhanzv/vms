<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepartmentsTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
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
        $this->forge->addKey('status');
        $this->forge->createTable('departments');

        // Insert sample departments
        $data = [
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Handles employee relations, recruitment, and benefits',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Manages technology infrastructure and support',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Handles financial planning and accounting',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Manages marketing campaigns and brand strategy',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Oversees daily operations and logistics',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Sales',
                'code' => 'SLS',
                'description' => 'Manages sales activities and customer relationships',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Customer Service',
                'code' => 'CS',
                'description' => 'Provides customer support and handles inquiries',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Research and Development',
                'code' => 'RND',
                'description' => 'Focuses on innovation and product development',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Legal',
                'code' => 'LEG',
                'description' => 'Handles legal matters and compliance',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Administration',
                'code' => 'ADM',
                'description' => 'Manages administrative functions and office operations',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('departments')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('departments');
    }
}
