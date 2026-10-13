<?php

namespace App\Controllers;

use App\Models\ClientFormFieldModel;
use App\Models\ClientModel;

/**
 * Config > Page Settings — per client and per page, choose which table columns
 * and which buttons that page shows. Dynamic Form Fields stays for forms only.
 *
 * Columns are stored in client_list_columns (as List Columns always did).
 * Buttons keep using the flags the controllers already read (client_form_fields,
 * under the page's 'form' — vendor_pass_request or staff_pass_request), so a
 * button switched off here disappears from its page without any controller
 * needing to change.
 */
class PageSettings extends BaseController
{
    private const DEFAULT_BUTTON_FORM = 'vendor_pass_request';

    /** Form type whose client_form_fields rows hold this page's button flags. */
    private function buttonForm(array $def): string
    {
        return (string) ($def['form'] ?? self::DEFAULT_BUTTON_FORM);
    }

    private function json(array $payload, int $status = 200)
    {
        return $this->response->setStatusCode($status)->setJSON($payload);
    }

    /** The client this request may configure, or 0 when not allowed / not chosen. */
    private function targetClientId($requested): int
    {
        helper(['role', 'feature']);
        $clientId = is_platform_superadmin() ? (int) $requested : (int) current_client_id();
        if ($clientId <= 0 || ! client_config_section_allowed('page_settings', $clientId)) {
            return 0;
        }

        return $clientId;
    }

    public function data()
    {
        try {
            return $this->loadData();
        } catch (\Throwable $e) {
            log_message('error', 'PageSettings::data ' . $e->getMessage());

            return $this->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 200);
        }
    }

    private function loadData()
    {
        helper(['role', 'feature', 'list_columns']);
        if (! client_config_section_allowed('page_settings')) {
            return $this->json(['success' => false, 'message' => 'You are not allowed to use Page Settings.'], 403);
        }

        $registry = list_column_registry();
        $pages    = [];
        foreach ($registry as $key => $def) {
            $pages[] = ['key' => $key, 'title' => $def['title'], 'group' => $def['group'] ?? ''];
        }

        $out = ['success' => true, 'pages' => $pages, 'platform' => is_platform_superadmin()];
        $clientModel = new ClientModel();
        if (is_platform_superadmin()) {
            $out['clients'] = array_map(
                static fn($c) => ['id' => (int) $c['id'], 'name' => $c['name']],
                $clientModel->where('status', 'active')->orderBy('name', 'ASC')->findAll()
            );
        } else {
            $own = $clientModel->find((int) current_client_id());
            $out['clients'] = $own ? [['id' => (int) $own['id'], 'name' => $own['name']]] : [];
        }

        $page     = (string) $this->request->getGet('page');
        $clientId = $this->targetClientId($this->request->getGet('client_id'));
        if ($page === '' || $clientId <= 0) {
            if ($page !== '' && $clientId <= 0) {
                return $this->json(['success' => false, 'message' => 'You are not allowed to configure this client (only the platform superadmin can).'] + $out);
            }

            return $this->json($out);
        }
        if (! isset($registry[$page])) {
            return $this->json(['success' => false, 'message' => 'Unknown page'], 400);
        }

        $def     = $registry[$page];
        $choices = list_column_choices($clientId, $page);
        $columns = [];
        foreach ($def['columns'] as $key => $col) {
            $locked    = ! empty($col['locked']);
            $columns[] = [
                'key'     => $key,
                'label'   => $col['label'],
                'locked'  => $locked,
                'visible' => $locked ? true : ($choices[$key] ?? true),
            ];
        }

        $flags = [];
        foreach ((new ClientFormFieldModel())->getForCompanyForm($clientId, $this->buttonForm($def)) as $row) {
            $flags[$row['field_key']] = (int) $row['is_enabled'] === 1;
        }
        $buttons = [];
        foreach ($def['buttons'] as $key => $label) {
            $buttons[] = ['key' => $key, 'label' => $label, 'enabled' => $flags[$key] ?? true];
        }

        $out['client'] = $clientId;
        $out['page']   = ['key' => $page, 'title' => $def['title'], 'columns' => $columns, 'buttons' => $buttons];

        return $this->json($out);
    }

    public function save()
    {
        helper(['role', 'feature', 'list_columns']);

        $input    = $this->request->getJSON(true) ?? [];
        $page     = (string) ($input['page'] ?? '');
        $registry = list_column_registry();
        $clientId = $this->targetClientId($input['client_id'] ?? 0);

        if ($clientId <= 0 && ! client_config_section_allowed('page_settings')) {
            return $this->json(['success' => false, 'message' => 'You are not allowed to use Page Settings.'], 403);
        }
        if ($clientId <= 0 || ! isset($registry[$page])) {
            return $this->json(['success' => false, 'message' => 'Choose a client and a page first.'], 400);
        }

        $def = $registry[$page];
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $db->transStart();
        foreach ((array) ($input['columns'] ?? []) as $key => $visible) {
            if (! isset($def['columns'][$key]) || ! empty($def['columns'][$key]['locked'])) {
                continue;
            }
            $where  = ['client_id' => $clientId, 'list_key' => $page, 'column_key' => $key];
            $exists = $db->table('client_list_columns')->where($where)->countAllResults() > 0;
            $data   = ['is_visible' => $visible ? 1 : 0, 'updated_at' => $now];
            if ($exists) {
                $db->table('client_list_columns')->where($where)->update($data);
            } else {
                $db->table('client_list_columns')->insert($where + $data + ['created_at' => $now]);
            }
        }
        $db->transComplete();

        $buttons = [];
        foreach ((array) ($input['buttons'] ?? []) as $key => $enabled) {
            if (isset($def['buttons'][$key])) {
                $buttons[$key] = (bool) $enabled;
            }
        }
        if ($buttons) {
            (new ClientFormFieldModel())->saveForCompanyForm($clientId, $this->buttonForm($def), $buttons);
        }

        if (! $db->transStatus()) {
            return $this->json(['success' => false, 'message' => 'Could not save. Please try again.'], 500);
        }

        return $this->json(['success' => true, 'message' => $def['title'] . ' settings saved']);
    }
}
