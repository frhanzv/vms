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
     * Kiosk settings map. When $clientId is provided, client settings override global defaults.
     */
    public function getConfigMap(?int $clientId = null): array
    {
        $globalRows = $this->db->table($this->table)
            ->where('client_id IS NULL', null, false)
            ->orderBy('updated_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $config = $this->rowsToMap($globalRows);

        if ($clientId !== null && $clientId > 0) {
            $clientRows = $this->db->table($this->table)
                ->where('client_id', $clientId)
                ->orderBy('updated_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();

            $config = array_merge($config, $this->rowsToMap($clientRows));
        }

        return $config;
    }

    /**
     * Global kiosk settings (client_id IS NULL), newest row wins per setting_key.
     */
    public function getGlobalConfigMap(): array
    {
        return $this->getConfigMap(null);
    }

    public function getClientConfigMap(?int $clientId): array
    {
        return $this->getConfigMap($clientId);
    }

    private function rowsToMap(array $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            $key = $row['setting_key'] ?? '';
            if ($key !== '' && ! array_key_exists($key, $map)) {
                $map[$key] = $row['setting_value'];
            }
        }

        return $map;
    }

    public function saveGlobalSetting(string $key, string $value): void
    {
        $this->saveScopedSetting(null, $key, $value);
    }

    public function saveClientSetting(?int $clientId, string $key, string $value): void
    {
        $this->saveScopedSetting($clientId, $key, $value);
    }

    public function saveScopedSetting(?int $clientId, string $key, string $value): void
    {
        $now = date('Y-m-d H:i:s');

        $builder = $this->db->table($this->table)->where('setting_key', $key);
        if ($clientId !== null && $clientId > 0) {
            $builder->where('client_id', $clientId);
        } else {
            $builder->where('client_id IS NULL', null, false);
            $clientId = null;
        }

        $rows = $builder->orderBy('id', 'ASC')->get()->getResultArray();

        if ($rows === []) {
            $this->insert([
                'client_id'     => $clientId,
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
            $delete = $this->db->table($this->table)
                ->where('setting_key', $key)
                ->where('id !=', $keepId);

            if ($clientId !== null && $clientId > 0) {
                $delete->where('client_id', $clientId);
            } else {
                $delete->where('client_id IS NULL', null, false);
            }

            $delete->delete();
        }
    }
}
