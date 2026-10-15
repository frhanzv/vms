<?php

namespace App\Libraries;

/**
 * The License Class dropdown used by the Staff / Vendor request and Card Info pages.
 * It reads Config > License Class (active classes of the logged-in client plus the shared ones).
 * Until a class has been added there, the old fixed list is shown so the forms are never empty.
 */
class LicenseClasses
{
    public const FALLBACK = ['B', 'B2', 'C', 'D', 'DA', 'E', 'E1', 'E2'];

    /** @return string[] */
    public static function options(): array
    {
        try {
            helper('feature');
            $db = \Config\Database::connect();
            if ($db->tableExists('license_classes')) {
                $cid = function_exists('current_client_id') ? (int) current_client_id() : 0;
                $b   = $db->table('license_classes')->select('name')->where('status', 'active');
                $cid > 0 ? $b->groupStart()->where('client_id', $cid)->orWhere('client_id IS NULL', null, false)->groupEnd()
                         : $b->where('client_id IS NULL', null, false);
                $names = array_values(array_unique(array_column($b->orderBy('name')->get()->getResultArray(), 'name')));
                if ($names) {
                    return $names;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'LicenseClasses::options ' . $e->getMessage());
        }

        return self::FALLBACK;
    }
}
