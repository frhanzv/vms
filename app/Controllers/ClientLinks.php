<?php

namespace App\Controllers;

/**
 * Platform superadmin only: each client's own link code and "site group".
 *
 *  - code        -> /c/{CODE}/login and /c/{CODE}/register
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
            return redirect()->to(base_url('dashboard'))->with('error', 'Only the platform administrator can manage client links.');
        }

        return null;
    }

    public function index()
    {
        if ($g = $this->guard()) {
            return $g;
        }

        $db      = \Config\Database::connect();
        $clients = $db->table('clients')->select('id, name, code, site_group, status')->orderBy('name')->get()->getResultArray();
        $locs    = [];
        foreach ($db->table('vendor_locations')->select('client_id, COUNT(*) AS n', false)->where('client_id IS NOT NULL', null, false)->groupBy('client_id')->get()->getResultArray() as $r) {
            $locs[(int) $r['client_id']] = (int) $r['n'];
        }

        $gates = $db->tableExists('locations') && $db->fieldExists('client_id', 'locations')
            ? $db->table('locations')->select('id, branch, location_access, client_id, status')->orderBy('branch')->orderBy('location_access')->get()->getResultArray()
            : [];

        return view('config/client_links', [
            'pageTitle' => 'Client Links & Sharing - SafeG',
            'clients'   => $clients,
            'locCounts' => $locs,
            'gates'     => $gates,
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

        $code  = strtoupper(trim((string) $this->request->getPost('code')));
        $group = trim((string) $this->request->getPost('site_group'));

        if ($code === '' || ! preg_match('/^[A-Z0-9_-]{2,30}$/', $code)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Code must be 2-30 letters, digits, - or _.']);
        }
        $dupe = $db->table('clients')->where('UPPER(code)', $code)->where('id !=', (int) $id)->countAllResults();
        if ($dupe > 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Another client already uses that code.']);
        }
        if (strlen($group) > 50) {
            return $this->response->setJSON(['success' => false, 'message' => 'Site group is too long.']);
        }

        $db->table('clients')->where('id', (int) $id)->update(['code' => $code, 'site_group' => $group !== '' ? $group : null]);

        helper('client_link');

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Saved.',
            'code'     => $code,
            'login'    => client_link_url($code, 'login'),
            'register' => client_link_url($code, 'register'),
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
