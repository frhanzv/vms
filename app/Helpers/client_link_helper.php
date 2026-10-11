<?php

/**
 * Links in emails. This product has ONE address: the vendor registration and
 * login links always point to this installation (no per-client links). Which
 * client a vendor registers with is chosen on the register form.
 */

if (! function_exists('client_link_url')) {
    /**
     * @param mixed $client unused - kept so existing callers keep working
     */
    function client_link_url($client = null, string $page = 'login', ?string $ssm = null): string
    {
        $url = base_url($page === 'register' ? 'register' : 'login');

        return $ssm !== null && $ssm !== '' ? $url . '?ssm=' . rawurlencode($ssm) : $url;
    }
}

if (! function_exists('client_admin_recipients')) {
    /** Active admin users of a client who have an email: [['email'=>, 'full_name'=>], ...] */
    function client_admin_recipients(int $clientId): array
    {
        $rows = \Config\Database::connect()->table('users')
            ->select('email, full_name')
            ->where('client_id', $clientId)
            ->whereIn('role', ['clientsuperadmin', 'admin'])
            ->where('is_active', 1)
            ->where("email IS NOT NULL AND email != ''", null, false)
            ->get()->getResultArray();

        $seen = [];
        $out  = [];
        foreach ($rows as $r) {
            $k = strtolower($r['email']);
            if (! isset($seen[$k])) {
                $seen[$k] = true;
                $out[]    = $r;
            }
        }

        return $out;
    }
}
