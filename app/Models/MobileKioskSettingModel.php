<?php

namespace App\Models;

use CodeIgniter\Model;

class MobileKioskSettingModel extends Model
{
    protected $table         = 'mobile_kiosk_settings';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['client_id', 'setting_key', 'setting_value', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Global kiosk settings (client_id IS NULL), newest row wins per setting_key.
     */
    public function getGlobalConfigMap(): array
    {
        $rows = $this->db->table($this->table)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $config = [];
        foreach ($rows as $row) {
            $key = $row['setting_key'] ?? '';
            if ($key !== '' && ! array_key_exists($key, $config)) {
                $config[$key] = $row['setting_value'];
            }
        }

        return $config;
    }

    public function saveGlobalSetting(string $key, string $value): void
    {
        $now = date('Y-m-d H:i:s');

        $rows = $this->db->table($this->table)
            ->where('setting_key', $key)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        if ($rows === []) {
            $this->insert([
                'client_id'     => null,
                'setting_key'   => $key,
                'setting_value' => $value,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);

            return;
        }

        $keepId = (int) $rows[0]['id'];

        $this->db->table($this->table)
            ->where('id', $keepId)
            ->update([
                'setting_value' => $value,
                'updated_at'    => $now,
            ]);

        if (count($rows) > 1) {
            $this->db->table($this->table)
                ->where('setting_key', $key)
                ->where('id !=', $keepId)
                ->delete();
        }
    }
}