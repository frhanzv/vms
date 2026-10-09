<?php

/**
 * Which columns of a list page a client may see (Config > List Columns).
 *
 * list_column_registry() is the single place that names the columns of each
 * configurable list. To make another list configurable later: add it to the
 * registry, then wrap its <th>/<td> pairs in `if (list_col('<list>', '<key>'))`.
 *
 * "locked" columns can't be switched off (the row would be unusable without
 * them). With no saved choice every column is visible.
 */

if (! function_exists('list_column_registry')) {
    /** @return array<string, array{title:string, columns:array<string, array{label:string, locked?:bool}>}> */
    function list_column_registry(): array
    {
        return [
            'vendor_pass_list' => [
                'title'   => 'Vendor Pass List',
                'columns' => [
                    'no'                  => ['label' => 'No', 'locked' => true],
                    'action'              => ['label' => 'Action', 'locked' => true],
                    'date'                => ['label' => 'Date'],
                    'app_no'              => ['label' => 'App No'],
                    'full_name'           => ['label' => 'Full Name', 'locked' => true],
                    'ic_passport'         => ['label' => 'IC / Passport No'],
                    'vendor_company_name' => ['label' => 'Vendor Company'],
                    'status'              => ['label' => 'Status', 'locked' => true],
                    'pass_expiry'         => ['label' => 'Pass Expiry'],
                ],
            ],
        ];
    }
}

if (! function_exists('list_column_choices')) {
    /**
     * Saved choices for one client + list: [column_key => bool]. Empty when the
     * client has never been configured (or the table doesn't exist yet).
     */
    function list_column_choices(int $clientId, string $list): array
    {
        static $cache = [];
        $k = $clientId . '|' . $list;
        if (isset($cache[$k])) {
            return $cache[$k];
        }

        $map = [];
        try {
            $db = \Config\Database::connect();
            if ($clientId > 0 && $db->tableExists('client_list_columns')) {
                foreach ($db->table('client_list_columns')->where('client_id', $clientId)->where('list_key', $list)->get()->getResultArray() as $r) {
                    $map[$r['column_key']] = (int) $r['is_visible'] === 1;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'list_column_choices: ' . $e->getMessage());
        }

        return $cache[$k] = $map;
    }
}

if (! function_exists('list_col')) {
    /** Should this column be shown to the logged-in user's client? */
    function list_col(string $list, string $column, ?int $clientId = null): bool
    {
        $def = list_column_registry()[$list]['columns'][$column] ?? null;
        if ($def === null || ! empty($def['locked'])) {
            return true;
        }

        helper('feature');
        $clientId ??= (function_exists('current_client_id') ? (int) current_client_id() : 0);

        // Platform superadmin (no client) always sees everything.
        if ($clientId <= 0) {
            return true;
        }

        $choices = list_column_choices($clientId, $list);

        return $choices[$column] ?? true;
    }
}

if (! function_exists('list_col_count')) {
    /** Number of visible columns — for colspan on empty-state rows. */
    function list_col_count(string $list): int
    {
        $n = 0;
        foreach (array_keys(list_column_registry()[$list]['columns'] ?? []) as $key) {
            if (list_col($list, $key)) {
                $n++;
            }
        }

        return max(1, $n);
    }
}
