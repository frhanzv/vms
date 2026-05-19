<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReplaceCompanyDetailsSectionWithCompanyRegIdMigration extends Migration
{
    public function up(): void
    {
        // Rename the section-level toggle to a field-level toggle for Company Registration ID.
        // The company name field becomes unconditionally visible; only the reg ID is gated.
        $this->db->table('email_template_form_fields')
            ->where('field_key', 'company_details_section')
            ->update([
                'field_key'  => 'company_reg_id',
                'label'      => 'Company Registration ID',
                'field_type' => 'text',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        // Migrate any existing per-company overrides in client_form_fields.
        if ($this->db->tableExists('client_form_fields')) {
            $this->db->table('client_form_fields')
                ->where('form_type', 'visitor_registration')
                ->where('field_key', 'company_details_section')
                ->update([
                    'field_key'  => 'company_reg_id',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }

    public function down(): void
    {
        $this->db->table('email_template_form_fields')
            ->where('field_key', 'company_reg_id')
            ->where('label', 'Company Registration ID')
            ->update([
                'field_key'  => 'company_details_section',
                'label'      => 'Company Details Section',
                'field_type' => 'section',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($this->db->tableExists('client_form_fields')) {
            $this->db->table('client_form_fields')
                ->where('form_type', 'visitor_registration')
                ->where('field_key', 'company_reg_id')
                ->update([
                    'field_key'  => 'company_details_section',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }
}
