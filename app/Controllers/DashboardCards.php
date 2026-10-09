<?php

namespace App\Controllers;

use App\Models\ClientModel;

/**
 * Dashboard card settings.
 *  - Config > Dashboard Cards (superadmin picks a client; client superadmin =
 *    own client): which cards that client's dashboards have at all.
 *  - "Customize" on a dashboard: each user hides cards for themselves, from
 *    among the ones the client allows.
 */
class DashboardCards extends BaseController
{
    private function adminClientId(): int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? (int) ($this->request->getGetPost('client_id') ?? 0) : (int) current_client_id();
    }

    public function index()
    {
        helper(['role', 'feature', 'dashboard_cards']);

        $platform = is_platform_superadmin();
        $clientId = $this->adminClientId();
        $client   = $clientId > 0 ? (new ClientModel())->find($clientId) : null;

        $choices = [];
        if ($client) {
            foreach (array_keys(dash_card_registry()) as $dash) {
                $choices[$dash] = dash_card_choices('client', $clientId, $dash);
            }
        }

        return view('config/dashboard_cards', [
            'pageTitle' => 'Dashboard Cards - SafeG',
            'platform'  => $platform,
            'clients'   => $platform ? (new ClientModel())->where('status', 'active')->orderBy('name', 'ASC')->findAll() : [],
            'client'    => $client,
            'registry'  => dash_card_registry(),
            'choices'   => $choices,
        ]);
    }

    public function save()
    {
        helper(['role', 'feature', 'dashboard_cards']);

        $clientId = $this->adminClientId();
        $client   = $clientId > 0 ? (new ClientModel())->find($clientId) : null;
        if (! $client) {
            return redirect()->to(base_url('config/dashboard-cards'))->with('error', 'Please choose a client first.');
        }

        $ok = true;
        foreach (dash_card_registry() as $dash => $def) {
            $shown  = (array) ($this->request->getPost('cards')[$dash] ?? []);
            $keys   = array_keys($def['cards']);
            $order  = dash_card_order($dash, $clientId, 0);          // keep the client's existing order
            $hidden = array_values(array_diff($keys, $shown));
            $ok     = $this->store('client', $clientId, $dash, $order, $hidden) && $ok;
        }
        $to = base_url('config/dashboard-cards') . (is_platform_superadmin() ? '?client_id=' . $clientId : '');

        return $ok
            ? redirect()->to($to)->with('success', 'Saved. ' . $client['name'] . "'s dashboards now show the selected cards.")
            : redirect()->to($to)->with('error', 'Could not save. Please try again.');
    }

    /**
     * The Customize drawer on a dashboard (JSON in, JSON out).
     *   scope "me"     — this user's own order / hidden cards
     *   scope "client" — the whole client's order / hidden cards (client superadmin only)
     *   reset          — forget this user's (or the client's) choices for the dashboard
     */
    public function saveMine()
    {
        helper(['dashboard_nav', 'dashboard_cards', 'feature', 'access', 'vendor_company', 'role']);

        $in   = $this->request->getJSON(true) ?: $this->request->getPost();
        $dash = (string) ($in['dashboard'] ?? '');
        $reg  = dash_card_registry();

        if (! isset($reg[$dash]) || ! dashboard_tab_allowed($dash)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'You are not allowed to customise that dashboard.']);
        }

        $scope = (string) ($in['scope'] ?? 'me');
        if ($scope === 'client') {
            if (! is_client_superadmin() || (int) current_client_id() <= 0) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Only the client administrator can change this for everyone.']);
            }
            $type = 'client';
            $id   = (int) current_client_id();
        } else {
            $type = 'user';
            $id   = (int) session()->get('user_id');
        }

        $db = \Config\Database::connect();
        if (! empty($in['reset'])) {
            $db->table('dashboard_card_settings')->where(['scope_type' => $type, 'scope_id' => $id, 'dashboard_key' => $dash])->delete();

            return $this->response->setJSON(['success' => true]);
        }

        $known  = array_keys($reg[$dash]['cards']);
        $order  = array_values(array_filter((array) ($in['order'] ?? []), static fn($k) => in_array($k, $known, true)));
        $hidden = array_values(array_filter((array) ($in['hidden'] ?? []), static fn($k) => in_array($k, $known, true)));

        // A user can only arrange cards the client allows.
        if ($type === 'user') {
            $order = array_values(array_filter($order, static fn($k) => dash_card_allowed($dash, $k)));
        }

        $ok = $this->store($type, $id, $dash, $order, $hidden);

        return $this->response->setJSON(['success' => $ok, 'message' => $ok ? '' : 'Could not save. Please try again.']);
    }

    /**
     * Write one scope's cards for a dashboard.
     *
     * @param list<string> $order   keys in display order (position = index)
     * @param list<string> $hidden  keys switched off
     */
    private function store(string $scopeType, int $scopeId, string $dash, array $order, array $hidden): bool
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $hasOrder = $db->fieldExists('sort_order', 'dashboard_card_settings');
        $pos = array_flip($order);

        $db->transStart();
        foreach ($order as $key) {
            $where = ['scope_type' => $scopeType, 'scope_id' => $scopeId, 'dashboard_key' => $dash, 'card_key' => $key];
            $data  = ['is_visible' => in_array($key, $hidden, true) ? 0 : 1, 'updated_at' => $now];
            if ($hasOrder) {
                $data['sort_order'] = $pos[$key];
            }
            if ($db->table('dashboard_card_settings')->where($where)->countAllResults() > 0) {
                $db->table('dashboard_card_settings')->where($where)->update($data);
            } else {
                $db->table('dashboard_card_settings')->insert($where + $data + ['created_at' => $now]);
            }
        }
        $db->transComplete();

        return $db->transStatus();
    }
}
