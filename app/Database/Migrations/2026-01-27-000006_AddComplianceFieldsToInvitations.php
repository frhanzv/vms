<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddComplianceFieldsToInvitations extends Migration
{
    public function up()
    {
        $fields = [
            'video_watched' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'profile_photo_path'
            ],
            'video_watched_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'video_watched'
            ],
            'video_completion_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'video_watched_at'
            ],
            'facial_verification_image' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'video_completion_percentage'
            ],
            'facial_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'facial_verification_image'
            ],
            'checked_in_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'facial_verified_at'
            ],
        ];

        $this->forge->addColumn('invitations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('invitations', [
            'video_watched',
            'video_watched_at',
            'video_completion_percentage',
            'facial_verification_image',
            'facial_verified_at',
            'checked_in_at'
        ]);
    }
}
