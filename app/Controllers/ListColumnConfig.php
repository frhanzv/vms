<?php

namespace App\Controllers;

use App\Models\ClientModel;

/**
 * Config > List Columns — the admin chooses which columns of each list page
 * a client can see. Platform superadmin picks the client; a client superadmin
 * manages their own client only.
 */
class ListColumnConfig extends BaseController
{
    private function targetClientId(): int
    {
        helper(['role', 'feature']);
        if (is_platform_superadmin()) {
            return (int) ($this->request->getGetPost('client_id') ?? 0);
        }

        return (int) current_client_id();
    }

    public function index()
    {
        helper(['role', 'feature', 'list_columns']);

        $platform = is_platform_superadmin();
        $clientId = $this->targetClientId();
        $clients  = $platform ? (new ClientModel())->where('status', 'active')->orderBy('name', 'ASC')->findAll() : [];

        $client = null;
        if ($clientId > 0) {
            $client = (new ClientModel())->find($clientId);
        }

        $choices = [];
        if ($client) {
            foreach (array_keys(list_column_registry()) as $list) {
                $choices[$list] = list_column_choices($clientId, $list);
            }
        }

        return view('config/list_columns', [
            'pageTitle' => 'List Columns - SafeG',
            'platform'  => $platform,
            'clients'   => $clients,
            'client'    => $client,
            'registry'  => list_column_registry(),
            'choices'   => $choices,
        ]);
    }

    public function save()
    {
        helper(['role', 'feature', 'list_columns']);

        $clientId = $this->targetClientId();
        $client   = $clientId > 0 ? (new ClientModel())->find($clientId) : null;
        if (! $client) {
            return redirect()->to(base_url('config/list-columns'))->with('error', 'Please choose a client first.');
        }

        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $db->transStart();
        foreach (list_column_registry() as $list => $def) {
            $shown = (array) ($this->request->getPost('cols')[$list] ?? []);
            foreach ($def['columns'] as $key => $col) {
                if (! empty($col['locked'])) {
                    continue;
                }
                $visible = in_array($key, $shown, true) ? 1 : 0;
                $where   = ['client_id' => $clientId, 'list_key' => $list, 'column_key' => $key];
                $exists  = $db->table('client_list_columns')->where($where)->countAllResults() > 0;
                if ($exists) {
                    $db->table('client_list_columns')->where($where)->update(['is_visible' => $visible, 'updated_at' => $now]);
                } else {
                    $db->table('client_list_columns')->insert($where + ['is_visible' => $visible, 'created_at' => $now, 'updated_at' => $now]);
                }
            }
        }
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Could not save. Please try again.');
        }

        $url = base_url('config/list-columns') . (is_platform_superadmin() ? '?client_id=' . $clientId : '');

        return redirect()->to($url)->with('success', 'Saved. ' . $client['name'] . ' now sees the selected columns.');
    }
}
