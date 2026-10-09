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

if (! function_exists('dash_card_choices')) {
    /** Saved choices for one scope: [card_key => bool]. */
    function dash_card_choices(string $scopeType, int $scopeId, string $dash): array
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
                $rows = $db->table('dashboard_card_settings')
                    ->where(['scope_type' => $scopeType, 'scope_id' => $scopeId, 'dashboard_key' => $dash])
                    ->get()->getResultArray();
                foreach ($rows as $r) {
                    $map[$r['card_key']] = (int) $r['is_visible'] === 1;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'dash_card_choices: ' . $e->getMessage());
        }

        return $cache[$k] = $map;
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
