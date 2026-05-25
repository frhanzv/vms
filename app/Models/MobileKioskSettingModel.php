<?php
namespace App\Models;
use CodeIgniter\Model;

class MobileKioskSettingModel extends Model
{
    protected $table      = 'mobile_kiosk_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['client_id', 'setting_key', 'setting_value', 'created_at', 'updated_at'];
    protected $useTimestamps = false;
}