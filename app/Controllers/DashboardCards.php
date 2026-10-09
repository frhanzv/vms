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

        $ok = $this->store('client', $clientId, (array) $this->request->getPost('cards'));
        $to = base_url('config/dashboard-cards') . (is_platform_superadmin() ? '?client_id=' . $clientId : '');

        return $ok
            ? redirect()->to($to)->with('success', 'Saved. ' . $client['name'] . "'s dashboards now show the selected cards.")
            : redirect()->to($to)->with('error', 'Could not save. Please try again.');
    }

    /** A user's own choice for one dashboard (the Customize panel). */
    public function saveMine()
    {
        helper(['dashboard_nav', 'dashboard_cards', 'feature', 'access', 'vendor_company', 'role']);

        $dash = (string) $this->request->getPost('dashboard');
        if (! isset(dash_card_registry()[$dash]) || ! dashboard_tab_allowed($dash)) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not allowed to customise that dashboard.');
        }

        $userId = (int) session()->get('user_id');
        $posted = (array) ($this->request->getPost('cards')[$dash] ?? []);
        if ($this->request->getPost('reset')) {
            $posted = array_keys(dash_card_registry()[$dash]['cards']);
        }

        // Only cards the client allows can be switched on by a user.
        $this->store('user', $userId, [$dash => array_values(array_filter($posted, static fn($k) => dash_card_allowed($dash, (string) $k)))], $dash);

        return redirect()->to(base_url($dash === 'visitor' ? 'dashboard' : 'dashboard/' . $dash));
    }

    /** @param array<string, list<string>> $shownByDash  dashboard => [shown card keys] */
    private function store(string $scopeType, int $scopeId, array $shownByDash, ?string $only = null): bool
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $db->transStart();
        foreach (dash_card_registry() as $dash => $def) {
            if ($only !== null && $dash !== $only) {
                continue;
            }
            $shown = (array) ($shownByDash[$dash] ?? []);
            foreach (array_keys($def['cards']) as $key) {
                $where   = ['scope_type' => $scopeType, 'scope_id' => $scopeId, 'dashboard_key' => $dash, 'card_key' => $key];
                $visible = in_array($key, $shown, true) ? 1 : 0;
                if ($db->table('dashboard_card_settings')->where($where)->countAllResults() > 0) {
                    $db->table('dashboard_card_settings')->where($where)->update(['is_visible' => $visible, 'updated_at' => $now]);
                } else {
                    $db->table('dashboard_card_settings')->insert($where + ['is_visible' => $visible, 'created_at' => $now, 'updated_at' => $now]);
                }
            }
        }
        $db->transComplete();

        return $db->transStatus();
    }
}
