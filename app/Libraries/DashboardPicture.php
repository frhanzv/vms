<?php

namespace App\Libraries;

/**
 * KPK Config > Dashboard Upload: the picture shown on the dashboard (KPK getDashboardPic shows the first
 * active upload). A client's own active picture wins over the shared one (client_id NULL).
 */
class DashboardPicture
{
    public const DIR = 'uploads/dashboard'; // under FCPATH (public/)

    public static function url(?int $clientId): ?string
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('dashboard_uploads')) {
            return null;
        }
        $b = $db->table('dashboard_uploads')->where('status', 'active');
        if ($clientId) {
            $b->groupStart()->where('client_id', $clientId)->orWhere('client_id IS NULL', null, false)->groupEnd()->orderBy('client_id IS NULL', 'ASC', false);
        } else {
            $b->where('client_id IS NULL', null, false);
        }
        $row = $b->orderBy('id', 'DESC')->get(1)->getRowArray();
        if (! $row || ! is_file(FCPATH . $row['file_path'])) {
            return null;
        }

        return base_url($row['file_path']);
    }

    /** For the signed-in user (platform superadmin = shared picture). */
    public static function forCurrentUser(): ?string
    {
        helper(['role', 'feature']);

        return self::url(is_platform_superadmin() ? null : ((int) current_client_id() ?: null));
    }
}
