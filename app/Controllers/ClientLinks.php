<?php

namespace App\Controllers;

/**
 * Platform superadmin only: "site group" sharing and gate owners.
 *
 *  - site_group  -> clients with the same group share one building / product:
 *                   their users and vendors can pick each other's locations,
 *                   which is how one vendor ends up under several clients.
 */
class ClientLinks extends BaseController
{
    private function guard()
    {
        helper(['feature', 'role']);
        if (! is_platform_superadmin()) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Only the platform administrator can manage site sharing.');
        }

        return null;
    }

    public function index()
    {
        if ($g = $this->guard()) {
            return $g;
        }

        $db      = \Config\Database::connect();

        $clients = $db->table('clients')->select('id, name, site_group, status')->orderBy('name')->get()->getResultArray();

        // Sharing status: which clients are grouped, and how many passes already span more than one client.
        $groups = [];
        foreach ($clients as $c) {
            $g = trim((string) ($c['site_group'] ?? ''));
            if ($g !== '') {
                $groups[$g][] = $c['name'];
            }
        }
        $ungrouped = array_values(array_map(static fn($c) => $c['name'], array_filter($clients, static fn($c) => trim((string) ($c['site_group'] ?? '')) === '')));
        $sharedPasses = 0;
        if ($db->tableExists('vendor_client_approvals')) {
            $sharedPasses = (int) count($db->query('SELECT vendor_id FROM vendor_client_approvals GROUP BY vendor_id HAVING COUNT(DISTINCT client_id) > 1')->getResultArray());
        }
        $locs    = [];
        foreach ($db->table('vendor_locations')->select('client_id, COUNT(*) AS n', false)->where('client_id IS NOT NULL', null, false)->groupBy('client_id')->get()->getResultArray() as $r) {
            $locs[(int) $r['client_id']] = (int) $r['n'];
        }

        $gates = $db->tableExists('locations') && $db->fieldExists('client_id', 'locations')
            ? $db->table('locations')->select('id, branch, location_access, client_id, status')->orderBy('branch')->orderBy('location_access')->get()->getResultArray()
            : [];

        return view('config/client_links', [
            'pageTitle' => 'Site Sharing - SafeG',
            'clients'   => $clients,
            'locCounts' => $locs,
            'gates'     => $gates,
            'groups'    => $groups,
            'ungrouped' => $ungrouped,
            'sharedPasses' => $sharedPasses,
            'embed'     => (bool) $this->request->getGet('embed'),
        ]);
    }

    public function save($id)
    {
        if ($this->guard()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $db     = \Config\Database::connect();
        $client = $db->table('clients')->where('id', (int) $id)->get()->getRowArray();
        if (! $client) {
            return $this->response->setJSON(['success' => false, 'message' => 'Client not found.']);
        }

        $group = trim((string) $this->request->getPost('site_group'));

        if (strlen($group) > 50) {
            return $this->response->setJSON(['success' => false, 'message' => 'Site group is too long.']);
        }

        $db->table('clients')->where('id', (int) $id)->update(['site_group' => $group !== '' ? $group : null]);

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Saved.',
        ]);
    }

    /** Set the owner client of a gate / Location Access row (Staff + Visitor). Blank = not assigned. */
    public function setGateOwner($id)
    {
        if ($this->guard()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $db = \Config\Database::connect();
        if (! $db->table('locations')->where('id', (int) $id)->countAllResults()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Location not found.']);
        }
        $owner = (int) $this->request->getPost('client_id');
        if ($owner > 0 && ! $db->table('clients')->where('id', $owner)->countAllResults()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Client not found.']);
        }

        $db->table('locations')->where('id', (int) $id)->update(['client_id' => $owner > 0 ? $owner : null]);

        return $this->response->setJSON(['success' => true, 'message' => 'Owner saved.']);
    }
}
