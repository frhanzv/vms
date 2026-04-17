<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillEmailTemplateDefaults extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('email_templates')) {
            return;
        }

        // Ensure columns exist (in case environments differ).
        $needsColors = (! $this->db->fieldExists('primary_color', 'email_templates'))
            || (! $this->db->fieldExists('content_bg_color', 'email_templates'))
            || (! $this->db->fieldExists('text_color', 'email_templates'));

        if ($needsColors) {
            return;
        }

        $defaults = [
            'INVITATION' => [
                'subject' => 'Visitor Invitation - SafeG',
                'body' => "Dear {{visitor_name}},\n\nYou have been invited to visit {{company}}. Please complete your registration by clicking the button below:\n\n{{registration_link}}\n\nThank you.",
            ],
            'VISITOR_INVITE' => [
                'subject' => 'Visitor Invitation - SafeG',
                'body' => "Dear {{visitor_name}},\n\nYou have been invited to visit {{company}}. Please complete your registration by clicking the button below:\n\n{{registration_link}}\n\nThank you.",
            ],
            'VISITOR_REQ_APPROVAL' => [
                'subject' => 'Visitor Request Approved',
                'body' => "Dear {{visitor_name}},\n\nYour visitor request to {{company}} has been approved.\n\nThank you.",
            ],
            'VISITOR_REQ_REJECT' => [
                'subject' => 'Visitor Request Update',
                'body' => "Dear {{visitor_name}},\n\nYour visitor request to {{company}} could not be approved at this time.\n\nThank you.",
            ],
            'PORT_PASS_APPROVE_REJECT_WITH_QR' => [
                'subject' => 'Port Pass Update',
                'body' => "Dear {{visitor_name}},\n\nYour port pass request has been updated. Please refer to the QR code / pass details provided.\n\nThank you.",
            ],
            'VISITOR_INVITATION_APPROVAL_PENDING' => [
                'subject' => 'Visitor Invitation Pending Approval',
                'body' => "Dear {{visitor_name}},\n\nYour invitation is pending approval.\n\nThank you.",
            ],
        ];

        $rows = $this->db->table('email_templates')
            ->select('id, code, subject, body, primary_color, content_bg_color, text_color')
            ->get()
            ->getResultArray();

        $now = date('Y-m-d H:i:s');
        foreach ($rows as $row) {
            $code = (string) ($row['code'] ?? '');
            if ($code === '') {
                continue;
            }

            $mapped = $defaults[$code] ?? null;
            $subject = trim((string) ($row['subject'] ?? ''));
            $body = trim((string) ($row['body'] ?? ''));

            $update = [];
            if ($subject === '') {
                if ($mapped) {
                    $update['subject'] = $mapped['subject'];
                } else {
                    $update['subject'] = ucwords(strtolower(str_replace('_', ' ', $code)));
                }
            }

            if ($body === '') {
                if ($mapped) {
                    $update['body'] = $mapped['body'];
                } else {
                    $update['body'] = "Dear {{visitor_name}},\n\n[Write your email content here]\n\nThank you.";
                }
            }

            if (trim((string) ($row['primary_color'] ?? '')) === '') {
                $update['primary_color'] = '#137FEC';
            }
            if (trim((string) ($row['content_bg_color'] ?? '')) === '') {
                $update['content_bg_color'] = '#F8F9FA';
            }
            if (trim((string) ($row['text_color'] ?? '')) === '') {
                $update['text_color'] = '#333333';
            }

            if ($update !== []) {
                $update['updated_at'] = $now;
                $this->db->table('email_templates')
                    ->where('id', (int) $row['id'])
                    ->update($update);
            }
        }
    }

    public function down()
    {
        // No-op: backfill cannot be reliably reverted.
    }
}

