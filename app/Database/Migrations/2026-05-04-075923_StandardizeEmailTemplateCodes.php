<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StandardizeEmailTemplateCodes extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('email_templates')) {
            return;
        }

        // We will truncate the table and insert the 5 standard codes to ensure 
        // all environments (like the 'other device') have the exact same list.
        $this->db->query("TRUNCATE TABLE email_templates");

        $now = date('Y-m-d H:i:s');
        $seedCodes = [
            'APPROVAL',
            'INVITATION',
            'REGISTRATION',
            'REJECTION',
            'REMINDER',
        ];

        $defaults = [
            'APPROVAL' => [
                'subject' => 'Visitor Request Approved',
                'body' => "Dear {{visitor_name}},\n\nYour visitor request to {{company}} has been approved.\n\nThank you.",
            ],
            'INVITATION' => [
                'subject' => 'Visitor Invitation - SafeG',
                'body' => "Dear {{visitor_name}},\n\nYou have been invited to visit {{company}}. Please complete your registration by clicking the button below:\n\n{{registration_link}}\n\nThank you.",
            ],
            'REGISTRATION' => [
                'subject' => 'Registration Submitted',
                'body' => "Dear {{visitor_name}},\n\nYour registration has been submitted successfully for {{company}}.\n\nThank you.",
            ],
            'REJECTION' => [
                'subject' => 'Visitor Request Update',
                'body' => "Dear {{visitor_name}},\n\nYour visitor request to {{company}} could not be approved at this time.\n\nThank you.",
            ],
            'REMINDER' => [
                'subject' => 'Reminder: Complete Your Registration',
                'body' => "Dear {{visitor_name}},\n\nThis is a reminder to complete your visitor registration for {{company}}.\n\nThank you.",
            ],
        ];

        foreach ($seedCodes as $code) {
            $mapped = $defaults[$code] ?? null;
            $subject = $mapped ? $mapped['subject'] : ucwords(strtolower(str_replace('_', ' ', $code)));
            $body = $mapped ? $mapped['body'] : "Dear {{visitor_name}},\n\n[Write your email content here]\n\nThank you.";
            
            $this->db->table('email_templates')->insert([
                'code' => $code,
                'subject' => $subject,
                'body' => $body,
                'primary_color' => '#137FEC',
                'content_bg_color' => '#F8F9FA',
                'text_color' => '#333333',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        // No-op
    }
}
