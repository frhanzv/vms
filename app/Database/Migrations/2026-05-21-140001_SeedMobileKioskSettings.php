<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedMobileKioskSettings extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('mobile_kiosk_settings')) {
            return;
        }

        $visitorFields = json_encode([
            'contact_number'  => ['show' => true,  'required' => true],
            'company_name'    => ['show' => true,  'required' => true],
            'email'           => ['show' => true,  'required' => false],
            'vehicle_reg_no'  => ['show' => true,  'required' => false],
            'address'         => ['show' => true,  'required' => true],
            'date_of_birth'   => ['show' => false, 'required' => false],
            'postal_code'     => ['show' => false, 'required' => false],
            'state'           => ['show' => false, 'required' => false],
            'city'            => ['show' => false, 'required' => false],
            'cardholder_name' => ['show' => true,  'required' => true],
            'ic_number'       => ['show' => true,  'required' => true],
            'country'         => ['show' => true,  'required' => true],
        ]);

        $defaults = [
            ['setting_key' => 'kiosk_walk_in',        'setting_value' => 'true'],
            ['setting_key' => 'kiosk_invitation',     'setting_value' => 'true'],
            ['setting_key' => 'kiosk_collect_card',   'setting_value' => 'true'],
            ['setting_key' => 'kiosk_vvip',           'setting_value' => 'true'],
            ['setting_key' => 'kiosk_welcome_text',   'setting_value' => 'Welcome'],
            ['setting_key' => 'kiosk_primary_color',  'setting_value' => '#1A73E8'],
            ['setting_key' => 'kiosk_logo_url',       'setting_value' => ''],
            ['setting_key' => 'kiosk_visitor_fields', 'setting_value' => $visitorFields],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($defaults as $row) {
            $exists = $this->db->table('mobile_kiosk_settings')
                ->where('setting_key', $row['setting_key'])
                ->where('client_id', null)
                ->countAllResults() > 0;

            if ($exists) {
                continue;
            }

            $row['client_id']  = null;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $this->db->table('mobile_kiosk_settings')->insert($row);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('mobile_kiosk_settings')) {
            return;
        }

        $keys = [
            'kiosk_walk_in',
            'kiosk_invitation',
            'kiosk_collect_card',
            'kiosk_vvip',
            'kiosk_welcome_text',
            'kiosk_primary_color',
            'kiosk_logo_url',
            'kiosk_visitor_fields',
        ];

        $this->db->table('mobile_kiosk_settings')
            ->whereIn('setting_key', $keys)
            ->where('client_id', null)
            ->delete();
    }
}
