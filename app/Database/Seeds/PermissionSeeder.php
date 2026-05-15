<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = \App\Libraries\PermissionRegistry::getPermissions();
        $db = \Config\Database::connect();
        $builder = $db->table('permissions');

        foreach ($permissions as $category => $items) {
            foreach ($items as $key => $label) {
                $existing = $builder->where('key', $key)->get()->getRow();
                $data = [
                    'key'        => $key,
                    'category'   => $category,
                    'label'      => $label,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($existing) {
                    $builder->where('id', $existing->id)->update($data);
                } else {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $builder->insert($data);
                }
            }
        }
    }
}
