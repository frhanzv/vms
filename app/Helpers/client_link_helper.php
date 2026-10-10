<?php

/**
 * Per-client product links.
 *
 *   /c/{CODE}/login      the client's own login page (shows the client's name)
 *   /c/{CODE}/register   vendor company registration, client fixed to {CODE}
 *                        (add ?ssm=... to pre-fill the company)
 *
 * clients.code is the {CODE} (unique, upper-case letters/digits/-/_).
 */

if (! function_exists('client_link_url')) {
    /** @param array|string $client client row (needs 'code') or the code itself */
    function client_link_url($client, string $page = 'login', ?string $ssm = null): string
    {
        $code = is_array($client) ? (string) ($client['code'] ?? '') : (string) $client;
        if ($code === '') {
            $url = base_url($page === 'register' ? 'register' : 'login');
        } else {
            $url = base_url('c/' . rawurlencode($code) . '/' . ($page === 'register' ? 'register' : 'login'));
        }

        return $ssm !== null && $ssm !== '' ? $url . '?ssm=' . rawurlencode($ssm) : $url;
    }
}

if (! function_exists('client_by_code')) {
    /** Active client row for a link code (case-insensitive), or null. */
    function client_by_code(string $code): ?array
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }
        $row = \Config\Database::connect()->table('clients')
            ->where('UPPER(code)', strtoupper($code))
            ->where('LOWER(status)', 'active')
            ->get()->getRowArray();

        return $row ?: null;
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
