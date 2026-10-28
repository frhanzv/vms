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
    /**
     * Every page that Config > Page Settings can control.
     *
     * 'columns' => [key => ['label', 'locked'?]]   table columns (locked = always shown)
     * 'buttons' => [key => label]                  buttons / sections on the page; the key is
     *                                              the same flag the controllers already read,
     *                                              so no controller needs to know about this.
     * 'form'    => form type the button flags are stored under (default vendor_pass_request)
     *
     * @return array<string, array{title:string, group:string, form?:string, columns:array<string, array{label:string, locked?:bool}>, buttons:array<string,string>}>
     */
    function list_column_registry(): array
    {
        $vendorCols = [
            'no'                  => ['label' => 'No', 'locked' => true],
            'action'              => ['label' => 'Action', 'locked' => true],
            'date'                => ['label' => 'Date'],
            'app_no'              => ['label' => 'App No'],
            'full_name'           => ['label' => 'Full Name', 'locked' => true],
            'ic_passport'         => ['label' => 'IC / Passport No'],
            'vendor_company_name' => ['label' => 'Vendor Company'],
            'status'              => ['label' => 'Status', 'locked' => true],
            'pass_expiry'         => ['label' => 'Pass Expiry'],
        ];

        $staffCols = [
            'no'          => ['label' => 'No', 'locked' => true],
            'action'      => ['label' => 'Action', 'locked' => true],
            'date'        => ['label' => 'Date'],
            'app_no'      => ['label' => 'App No'],
            'type'        => ['label' => 'Type'],
            'staff_no'    => ['label' => 'Staff No'],
            'full_name'   => ['label' => 'Full Name', 'locked' => true],
            'ic_passport' => ['label' => 'IC / Passport No'],
            'department'  => ['label' => 'Department'],
            'status'      => ['label' => 'Status', 'locked' => true],
            'pass_expiry' => ['label' => 'Pass Expiry'],
        ];

        return [
            // Staff Pass — same pages as Vendor Pass. Buttons write the staff form
            // (client_form_fields, form staff_pass_request), which the staff
            // controllers read through StaffPassBase::cfg().
            // Keys stay as before so column choices already saved carry over.
            'staff_pass_list' => [
                'title'   => 'Staff List',
                'group'   => 'Staff Pass',
                'form'    => 'staff_pass_request',
                'columns' => $staffCols + ['current_location' => ['label' => 'Current Location']],
                'buttons' => [
                    'request_button'  => 'Request button',
                    'import_button'   => 'Import button',
                    'template_button' => 'Template button',
                    'export_button'   => 'Export button',
                    'print_button'    => 'Print button',
                ],
            ],
            'staff_request_list' => [
                'title'   => 'Request List',
                'group'   => 'Staff Pass',
                'form'    => 'staff_pass_request',
                'columns' => $staffCols,
                'buttons' => [
                    'approve_button' => 'Approve action',
                    'reject_button'  => 'Reject action',
                    'edit_button'    => 'Edit action',
                    'delete_button'  => 'Delete action',
                ],
            ],
            'staff_process_list' => [
                'title'   => 'Process List',
                'group'   => 'Staff Pass',
                'form'    => 'staff_pass_request',
                'columns' => [
                    'no'          => ['label' => 'No', 'locked' => true],
                    'app_no'      => ['label' => 'App No'],
                    'full_name'   => ['label' => 'Full Name', 'locked' => true],
                    'department'  => ['label' => 'Department'],
                    'ic_passport' => ['label' => 'IC / Passport'],
                    'staff_no'    => ['label' => 'Staff No'],
                    'card_type'   => ['label' => 'Worker Type'],
                    'card_expiry' => ['label' => 'Card Expiry'],
                    'ready'       => ['label' => 'Ready To Print'],
                    'action'      => ['label' => 'Action', 'locked' => true],
                ],
                'buttons' => [],
            ],
            'staff_process_detail' => [
                'title'   => 'Process Detail (opened from Process / Printing List)',
                'group'   => 'Staff Pass',
                'form'    => 'staff_pass_request',
                'columns' => [],
                'buttons' => [
                    'process_update_button'       => 'Update button',
                    'process_reject_button'       => 'Reject button',
                    'process_upload_photo_button' => 'Upload Photo button',
                    'process_rfid_section'        => 'Physical Card / RFID section',
                    // The staff Issuance List page was removed; cards are issued from here.
                    'issuance_issue_button'       => 'Issue button',
                ],
            ],
            'staff_printing_list' => [
                'title'   => 'Printing List',
                'group'   => 'Staff Pass',
                'form'    => 'staff_pass_request',
                'columns' => [
                    'select'      => ['label' => 'Select (checkbox)', 'locked' => true],
                    'no'          => ['label' => 'No', 'locked' => true],
                    'photo'       => ['label' => 'Photo'],
                    'staff_no'    => ['label' => 'Staff No'],
                    'full_name'   => ['label' => 'Full Name', 'locked' => true],
                    'ic_passport' => ['label' => 'IC / Passport'],
                    'department'  => ['label' => 'Department'],
                    'action'      => ['label' => 'Action', 'locked' => true],
                ],
                'buttons' => ['printing_generate_button' => 'Print / Generate Serial buttons'],
            ],
            'staff_closed_list' => [
                'title'   => 'Closed List',
                'group'   => 'Staff Pass',
                'form'    => 'staff_pass_request',
                'columns' => [
                    'no'          => ['label' => 'No', 'locked' => true],
                    'action'      => ['label' => 'Action', 'locked' => true],
                    'app_no'      => ['label' => 'App No'],
                    'receipt_no'  => ['label' => 'Receipt No'],
                    'app_date'    => ['label' => 'App Date'],
                    'department'  => ['label' => 'Department'],
                    'full_name'   => ['label' => 'Full Name', 'locked' => true],
                    'ic_passport' => ['label' => 'IC / Passport No'],
                    'staff_no'    => ['label' => 'Staff No'],
                    'card_type'   => ['label' => 'Card Type'],
                    'card_id'     => ['label' => 'Card ID'],
                    'card_status' => ['label' => 'Card Status'],
                    'card_expiry' => ['label' => 'Card Expiry'],
                    'collected_by' => ['label' => 'Collected By'],
                    'issued_at'   => ['label' => 'Issued At'],
                ],
                'buttons' => [
                    'closed_export_button'       => 'Export button',
                    'closed_card_details_button' => 'Card Details action',
                    'renew_button'               => 'Renew action',
                ],
            ],
            'staff_card_info' => [
                'title'   => 'Card Info (opened from Closed List)',
                'group'   => 'Staff Pass',
                'form'    => 'staff_pass_request',
                'columns' => [],
                'buttons' => [
                    'card_info_add_license_button'   => 'Add License button',
                    'card_info_edit_location_button' => 'Edit Location Access button',
                    'card_info_activate_button'      => 'Activate Card button',
                    'card_info_terminate_button'     => 'Terminate Card button',
                    'suspend_button'                 => 'Suspend / Unsuspend button',
                ],
            ],
            // 'vendor_pass_list' keeps its old key so columns saved under Config > List Columns carry over.
            'vendor_pass_list' => [
                'title'   => 'Vendor List',
                'group'   => 'Vendor Pass',
                'columns' => $vendorCols + ['current_location' => ['label' => 'Current Location']],
                'buttons' => [
                    'request_button'  => 'Request button',
                    'import_button'   => 'Import button',
                    'template_button' => 'Template button',
                    'export_button'   => 'Export button',
                ],
            ],
            'vendor_request_list' => [
                'title'   => 'Request List',
                'group'   => 'Vendor Pass',
                'columns' => $vendorCols,
                'buttons' => [
                    'approve_button' => 'Approve action',
                    'reject_button'  => 'Reject action',
                    'edit_button'    => 'Edit action',
                    'delete_button'  => 'Delete action',
                ],
            ],
            'vendor_process_list' => [
                'title'   => 'Process List',
                'group'   => 'Vendor Pass',
                'columns' => [
                    'no'                  => ['label' => 'No', 'locked' => true],
                    'app_no'              => ['label' => 'App No'],
                    'full_name'           => ['label' => 'Full Name', 'locked' => true],
                    'vendor_company_name' => ['label' => 'Vendor Company'],
                    'ic_passport'         => ['label' => 'IC / Passport'],
                    'card_type'           => ['label' => 'Worker Type'],
                    'pass_expiry'         => ['label' => 'Pass Expiry'],
                    'action'              => ['label' => 'Action', 'locked' => true],
                ],
                'buttons' => [],
            ],
            'vendor_process_detail' => [
                'title'   => 'Process Detail (opened from Process List)',
                'group'   => 'Vendor Pass',
                'columns' => [],
                'buttons' => [
                    'process_update_button'         => 'Update button',
                    'process_reject_button'         => 'Reject button',
                    'process_upload_photo_button'   => 'Upload Photo button',
                    'process_rfid_section'          => 'Physical Card / RFID section',
                    'process_card_printing_section' => 'Card Printing section',
                ],
            ],
            'vendor_printing_list' => [
                'title'   => 'Printing List',
                'group'   => 'Vendor Pass',
                'columns' => [
                    'select'              => ['label' => 'Select (checkbox)', 'locked' => true],
                    'no'                  => ['label' => 'No', 'locked' => true],
                    'photo'               => ['label' => 'Photo'],
                    'full_name'           => ['label' => 'Full Name', 'locked' => true],
                    'vendor_company_name' => ['label' => 'Vendor Company'],
                    'ic_passport'         => ['label' => 'IC / Passport'],
                    'card_type'           => ['label' => 'Card Type'],
                    'action'              => ['label' => 'Action', 'locked' => true],
                ],
                'buttons' => ['printing_generate_button' => 'Print / Generate Serial buttons', 'issuance_issue_button' => 'Issue Card button'],
            ],
            'vendor_closed_list' => [
                'title'   => 'Closed List',
                'group'   => 'Vendor Pass',
                'columns' => [
                    'no'                  => ['label' => 'No', 'locked' => true],
                    'action'              => ['label' => 'Action', 'locked' => true],
                    'app_no'              => ['label' => 'App No'],
                    'receipt_no'          => ['label' => 'Receipt No'],
                    'app_date'            => ['label' => 'App Date'],
                    'vendor_company_name' => ['label' => 'Vendor Company'],
                    'full_name'           => ['label' => 'Full Name', 'locked' => true],
                    'ic_passport'         => ['label' => 'IC / Passport No'],
                    'vehicle_registration' => ['label' => 'Vehicle Registration'],
                    'card_type'           => ['label' => 'Card Type'],
                    'card_id'             => ['label' => 'Card ID'],
                    'card_status'         => ['label' => 'Card Status'],
                    'card_expiry'         => ['label' => 'Card Expiry'],
                    'collected_by'        => ['label' => 'Collected By'],
                    'issued_at'           => ['label' => 'Issued At'],
                ],
                'buttons' => [
                    'closed_export_button'       => 'Export button',
                    'closed_card_details_button' => 'Card Details action',
                    'qr_button'                  => 'QR / Vendor Detail action',
                ],
            ],
            'vendor_card_info' => [
                'title'   => 'Card Info (opened from Closed List)',
                'group'   => 'Vendor Pass',
                'columns' => [],
                'buttons' => [
                    'card_info_add_license_button'   => 'Add License button',
                    'card_info_edit_location_button' => 'Edit Location Access button',
                    'card_info_upload_photo_button'  => 'Upload Photo button',
                    'card_info_activate_button'      => 'Activate Card button',
                    'card_info_terminate_button'     => 'Terminate Card button',
                ],
            ],
        ] + vehicle_registry_pages();
    }
}

if (! function_exists('vehicle_registry_pages')) {
    /** Vehicle Pass lists for Config > Page Settings (columns, buttons and actions). The column set lives in VehicleRules::listColumns(). */
    function vehicle_registry_pages(): array
    {
        $pages = [];
        foreach (\App\Libraries\VehicleRules::LIST_PAGES as $stage => $title) {
            $cols = [];
            foreach (\App\Libraries\VehicleRules::listColumns($stage) as $key => $def) {
                $cols[$key] = ['label' => $def['label']] + (! empty($def['locked']) ? ['locked' => true] : []);
            }
            $pages['vehicle_' . $stage] = ['title' => $title, 'group' => 'Vehicle Pass', 'form' => 'vehicle_buttons', 'columns' => $cols, 'buttons' => \App\Libraries\VehicleRules::listButtons($stage)];
        }

        return $pages;
    }
}

if (! function_exists('page_managed_button_keys')) {
    /**
     * Button/section flags that now live in Page Settings (hidden from the
     * Dynamic Form Fields tabs), per form: [form_type => [field_key, ...]].
     * Pages without a 'form' write the vendor form.
     */
    function page_managed_button_keys(): array
    {
        $keys = [];
        foreach (list_column_registry() as $page) {
            $form = $page['form'] ?? 'vendor_pass_request';
            foreach (array_keys($page['buttons'] ?? []) as $k) {
                $keys[$form][] = $k;
            }
        }

        return $keys;
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
