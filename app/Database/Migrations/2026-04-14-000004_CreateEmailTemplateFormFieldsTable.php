<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailTemplateFormFieldsTable extends Migration
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
            'field_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'label' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'field_type' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'text',
            ],
            'placeholder' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'options' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_required' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'is_system' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addUniqueKey('field_key');
        $this->forge->createTable('email_template_form_fields');

        $seedRows = [
            ['field_key' => 'staff_id', 'label' => 'Staff ID Of Person Visited'],
            ['field_key' => 'host_contact', 'label' => 'Contact No Of Person Visited'],
            ['field_key' => 'company_visited', 'label' => 'Name Of Company Visited'],
            ['field_key' => 'visit_reason', 'label' => 'Reason'],
            ['field_key' => 'resident', 'label' => 'Resident', 'is_required' => 1],
            ['field_key' => 'ic_number', 'label' => 'IC Number', 'is_required' => 1],
            ['field_key' => 'date_of_birth', 'label' => 'Date of Birth', 'is_required' => 1],
            ['field_key' => 'sex', 'label' => 'Sex', 'is_required' => 1],
            ['field_key' => 'full_name', 'label' => 'Full Name', 'is_required' => 1],
            ['field_key' => 'contact_number', 'label' => 'Contact Number', 'is_required' => 1],
            ['field_key' => 'email', 'label' => 'Email Address', 'is_required' => 1, 'field_type' => 'email'],
            ['field_key' => 'address_1', 'label' => 'Address 1'],
            ['field_key' => 'address_2', 'label' => 'Address 2'],
            ['field_key' => 'address_3', 'label' => 'Address 3'],
            ['field_key' => 'city', 'label' => 'City'],
            ['field_key' => 'state', 'label' => 'State'],
            ['field_key' => 'postal_code', 'label' => 'Postal Code'],
            ['field_key' => 'country', 'label' => 'Country'],
            ['field_key' => 'category', 'label' => 'Vehicle Category'],
            ['field_key' => 'vehicle_type', 'label' => 'Type Of Vehicle'],
            ['field_key' => 'vehicle_registration', 'label' => 'Vehicle Registration Number'],
            ['field_key' => 'driving_license_section', 'label' => 'Driving License Section', 'field_type' => 'section'],
            ['field_key' => 'company_details_section', 'label' => 'Company Details Section', 'field_type' => 'section'],
            ['field_key' => 'asset_equipment_section', 'label' => 'Asset/Equipment Section', 'field_type' => 'section'],
            ['field_key' => 'document_upload_section', 'label' => 'Document Upload Section', 'field_type' => 'section'],
            ['field_key' => 'profile_photo_section', 'label' => 'Profile Photo Section', 'field_type' => 'section'],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($seedRows as $index => $row) {
            $row['field_type'] = $row['field_type'] ?? 'text';
            $row['is_required'] = $row['is_required'] ?? 0;
            $row['is_enabled'] = 1;
            $row['sort_order'] = $index + 1;
            $row['is_system'] = 1;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $this->db->table('email_template_form_fields')->insert($row);
        }
    }

    public function down()
    {
        $this->forge->dropTable('email_template_form_fields');
    }
}
