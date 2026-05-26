<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnsureProfilePhotoOnUsers extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        if ($this->db->fieldExists('profile_photo', 'users')) {
            return;
        }

        $this->forge->addColumn('users', [
            'profile_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'full_name',
            ],
        ]);
    }

    public function down(): void
    {
        if ($this->db->tableExists('users')
            && $this->db->fieldExists('profile_photo', 'users')) {
            $this->forge->dropColumn('users', 'profile_photo');
        }
    }
}
