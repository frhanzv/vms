<?php

/**
 * Configurable dashboard cards (Staff and Vendor dashboards).
 *
 * A card is shown when BOTH the client allows it (admin page: Config >
 * Dashboard Cards) AND the user has not switched it off for themselves
 * (Customize button on the dashboard). No saved choice = shown.
 * To add a card: add it to dash_card_registry() and wrap it in the view with
 * `if (dash_card('<dashboard>', '<key>'))`.
 */

if (! function_exists('dash_card_registry')) {
    /** @return array<string, array{title:string, cards:array<string, array{label:string, group:string}>}> */
    function dash_card_registry(): array
    {
        return [
            'staff' => [
                'title' => 'Staff Dashboard',
                'cards' => [
                    'kpi_total'         => ['label' => 'Total staff', 'group' => 'Number tiles'],
                    'kpi_cards_active'  => ['label' => 'Active pass cards', 'group' => 'Number tiles'],
                    'kpi_cards_expiring'=> ['label' => 'Cards expiring ≤ 30 days', 'group' => 'Number tiles'],
                    'kpi_docs_expiring' => ['label' => 'Documents expiring ≤ 30 days', 'group' => 'Number tiles'],
                    'chart_status'      => ['label' => 'Staff by status', 'group' => 'Charts'],
                    'chart_card_health' => ['label' => 'Pass card health', 'group' => 'Charts'],
                    'chart_monthly'     => ['label' => 'New staff per month', 'group' => 'Charts'],
                    'chart_departments' => ['label' => 'Top departments', 'group' => 'Charts'],
                    'list_attention'    => ['label' => 'Needs attention', 'group' => 'Lists'],
                    'list_expiring'     => ['label' => 'Cards expiring soon', 'group' => 'Lists'],
                    'list_recent'       => ['label' => 'Recent registrations', 'group' => 'Lists'],
                ],
            ],
            'vendor' => [
                'title' => 'Vendor Dashboard',
                'cards' => [
                    'kpi_total'         => ['label' => 'Total applications', 'group' => 'Number tiles'],
                    'kpi_active'        => ['label' => 'Active passes', 'group' => 'Number tiles'],
                    'kpi_expiring'      => ['label' => 'Expiring ≤ 30 days', 'group' => 'Number tiles'],
                    'kpi_awaiting'      => ['label' => 'Awaiting approval', 'group' => 'Number tiles'],
                    'chart_pipeline'    => ['label' => 'Pass pipeline', 'group' => 'Charts'],
                    'chart_monthly'     => ['label' => 'Applications per month', 'group' => 'Charts'],
                    'chart_worker_type' => ['label' => 'Worker type', 'group' => 'Charts'],
                    'chart_companies'   => ['label' => 'Top vendor companies (staff side)', 'group' => 'Charts'],
                    'list_action'       => ['label' => 'Waiting for approval / needs attention', 'group' => 'Lists'],
                    'list_expiring'     => ['label' => 'Passes expiring soon', 'group' => 'Lists'],
                    'list_recent'       => ['label' => 'Recent applications', 'group' => 'Lists'],
                ],
            ],
        ];
    }
}

if (! function_exists('dash_card_rows')) {
    /** Saved rows for one scope: [card_key => ['visible' => bool, 'order' => ?int]]. */
    function dash_card_rows(string $scopeType, int $scopeId, string $dash): array
    {
        static $cache = [];
        $k = "$scopeType|$scopeId|$dash";
        if (isset($cache[$k])) {
            return $cache[$k];
        }

        $map = [];
        try {
            $db = \Config\Database::connect();
            if ($scopeId > 0 && $db->tableExists('dashboard_card_settings')) {
                $hasOrder = $db->fieldExists('sort_order', 'dashboard_card_settings');
                $rows = $db->table('dashboard_card_settings')
                    ->where(['scope_type' => $scopeType, 'scope_id' => $scopeId, 'dashboard_key' => $dash])
                    ->get()->getResultArray();
                foreach ($rows as $r) {
                    $map[$r['card_key']] = [
                        'visible' => (int) $r['is_visible'] === 1,
                        'order'   => ($hasOrder && $r['sort_order'] !== null) ? (int) $r['sort_order'] : null,
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'dash_card_rows: ' . $e->getMessage());
        }

        return $cache[$k] = $map;
    }
}

if (! function_exists('dash_card_choices')) {
    /** Saved visibility for one scope: [card_key => bool]. */
    function dash_card_choices(string $scopeType, int $scopeId, string $dash): array
    {
        return array_map(static fn($r) => $r['visible'], dash_card_rows($scopeType, $scopeId, $dash));
    }
}

if (! function_exists('dash_card_order')) {
    /**
     * Card keys of a dashboard in display order: the user's own order, else the
     * client's, else the default (registry) order. Cards with no saved
     * position keep their default place after the positioned ones.
     *
     * @return list<string>
     */
    function dash_card_order(string $dash, ?int $clientId = null, ?int $userId = null): array
    {
        helper('feature');
        $clientId ??= (int) current_client_id();
        $userId   ??= (int) session()->get('user_id');

        $keys    = array_keys(dash_card_registry()[$dash]['cards'] ?? []);
        $default = array_flip($keys);
        $user    = dash_card_rows('user', $userId, $dash);
        $client  = dash_card_rows('client', $clientId, $dash);

        $pos = [];
        foreach ($keys as $key) {
            $o = $user[$key]['order'] ?? null;
            if ($o === null) {
                // no personal position -> only use the client's order when the user has none at all
                $o = array_filter(array_column($user, 'order'), static fn($v) => $v !== null) === [] ? ($client[$key]['order'] ?? null) : null;
            }
            $pos[$key] = $o;
        }

        usort($keys, static function ($a, $b) use ($pos, $default) {
            $pa = $pos[$a] ?? 1000 + $default[$a];
            $pb = $pos[$b] ?? 1000 + $default[$b];

            return $pa <=> $pb;
        });

        return $keys;
    }
}

if (! function_exists('dash_card_pos')) {
    /** CSS `order` value for a card (its place in the display order). */
    function dash_card_pos(string $dash, string $card): int
    {
        static $cache = [];
        $cache[$dash] ??= array_flip(dash_card_order($dash));

        return (int) ($cache[$dash][$card] ?? 999);
    }
}

if (! function_exists('dash_card_allowed')) {
    /** Does the client allow this card? (the admin's level) */
    function dash_card_allowed(string $dash, string $card, ?int $clientId = null): bool
    {
        helper('feature');
        $clientId ??= (int) current_client_id();

        return $clientId <= 0 ? true : (dash_card_choices('client', $clientId, $dash)[$card] ?? true);
    }
}

if (! function_exists('dash_card')) {
    /** Should this card be drawn for the logged-in user? */
    function dash_card(string $dash, string $card, ?int $clientId = null, ?int $userId = null): bool
    {
        if (! isset(dash_card_registry()[$dash]['cards'][$card])) {
            return true;
        }
        if (! dash_card_allowed($dash, $card, $clientId)) {
            return false;
        }
        $userId ??= (int) session()->get('user_id');

        return dash_card_choices('user', $userId, $dash)[$card] ?? true;
    }
}

if (! function_exists('dash_card_any')) {
    /** True when at least one card of the dashboard is visible. */
    function dash_card_any(string $dash): bool
    {
        foreach (array_keys(dash_card_registry()[$dash]['cards'] ?? []) as $key) {
            if (dash_card($dash, $key)) {
                return true;
            }
        }

        return false;
    }
}
