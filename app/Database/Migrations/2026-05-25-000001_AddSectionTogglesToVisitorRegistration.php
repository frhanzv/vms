<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSectionTogglesToVisitorRegistration extends Migration
{
    public function up(): void
    {
        $now = date('Y-m-d H:i:s');

        $sections = [
            ['field_key' => 'visit_info_section',      'label' => 'Visit Information Section'],
            ['field_key' => 'date_of_visit_section',   'label' => 'Date of Visit Section'],
            ['field_key' => 'details_of_visit_section','label' => 'Details of Visit Section'],
            ['field_key' => 'person_details_section',  'label' => 'Person Details Section'],
        ];

        $maxOrder = (int) $this->db->table('email_template_form_fields')
            ->selectMax('sort_order')
            ->get()
            ->getRowArray()['sort_order'];

        foreach ($sections as $i => $row) {
            $existing = $this->db->table('email_template_form_fields')
                ->where('field_key', $row['field_key'])
                ->get()
                ->getRowArray();

            if (!$existing) {
                $this->db->table('email_template_form_fields')->insert([
                    'field_key'  => $row['field_key'],
                    'label'      => $row['label'],
                    'field_type' => 'section',
                    'is_required'=> 0,
                    'is_enabled' => 1,
                    'sort_order' => $maxOrder + $i + 1,
                    'is_system'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $this->db->table('email_template_form_fields')
            ->whereIn('field_key', [
                'visit_info_section',
                'date_of_visit_section',
                'details_of_visit_section',
                'person_details_section',
            ])
            ->delete();
    }
}
