<?php

namespace App\Controllers;

use App\Models\VendorLocationModel;

/**
 * Admin CRUD for the location codes offered everywhere "Location Access" is
 * asked for in the Vendor module (request form, detail page, Card Info,
 * Process Detail) — see VendorLocationModel::getActiveOptions(). Replaces
 * the old hardcoded LOCATION_OPTIONS const: add one here, it shows up on
 * every one of those pages immediately, no code change needed.
 */
class VendorLocations extends BaseController
{
    private function denyUnlessAllowed()
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'manage_locations')) {
            return redirect()->to(base_url('vendors'))->with('error', 'You are not allowed to manage locations.');
        }
        return null;
    }

    public function index()
    {
        if ($denied = $this->denyUnlessAllowed()) {
            return $denied;
        }

        helper(['feature', 'role']);
        $model = new VendorLocationModel();
        $all   = $model->getAllOrdered();
        $isSa  = is_platform_superadmin();

        // A client admin manages its own client's locations (plus sees the ones nobody owns yet).
        if (! $isSa) {
            $me  = (int) current_company_id();
            $all = array_values(array_filter($all, static fn($l) => empty($l['client_id']) || (int) $l['client_id'] === $me));
        }

        $clients = $isSa ? \Config\Database::connect()->table('clients')->select('id, name')->orderBy('name')->get()->getResultArray() : [];

        return view('vendors/locations', [
            'pageTitle'    => 'Vendor Locations - SafeG',
            'locations'    => $all,
            'clients'      => $clients,
            'isSuperadmin' => $isSa,
            'clientNames'  => array_column(\Config\Database::connect()->table('clients')->select('id, name')->get()->getResultArray(), 'name', 'id'),
        ]);
    }

    public function create()
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'manage_locations')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $label = trim((string) $this->request->getPost('label'));
        if ($label === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter a location name.']);
        }

        $model = new VendorLocationModel();

        $codeInput = trim((string) $this->request->getPost('code'));
        $code      = VendorLocationModel::slugifyCode($codeInput !== '' ? $codeInput : $label);
        if ($code === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Could not derive a code from that name — try a simpler name or set a code.']);
        }

        // Auto-suffix on collision rather than failing outright — keeps
        // "add a location" a one-field, no-friction action for the common
        // case (typing a label only).
        $baseCode = $code;
        $suffix   = 2;
        while ($model->where('code', $code)->first()) {
            $code = $baseCode . '_' . $suffix;
            $suffix++;
        }

        $maxOrder = (int) ($model->selectMax('sort_order')->first()['sort_order'] ?? 0);

        helper(['feature', 'role']);
        // Each location belongs to exactly one client. A client admin always creates
        // for its own client; the platform superadmin picks (blank = not owned yet).
        $ownerId = is_platform_superadmin()
            ? ((int) $this->request->getPost('client_id') ?: null)
            : (int) current_company_id();

        $ok = $model->insert([
            'client_id'  => $ownerId,
            'code'       => $code,
            'label'      => $label,
            'sort_order' => $maxOrder + 1,
            'is_active'  => 1,
        ]);

        if (! $ok) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to add location.', 'errors' => $model->errors()]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Location added.', 'data' => $model->find($model->getInsertID())]);
    }

    public function update($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'manage_locations')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $model    = new VendorLocationModel();
        $location = $model->find((int) $id);
        if (! $location) {
            return $this->response->setJSON(['success' => false, 'message' => 'Location not found.']);
        }

        $update = [];
        if ($this->request->getPost('label') !== null) {
            $label = trim((string) $this->request->getPost('label'));
            if ($label === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'Name cannot be empty.']);
            }
            $update['label'] = $label;
        }
        if ($this->request->getPost('is_active') !== null) {
            $update['is_active'] = ((int) $this->request->getPost('is_active')) ? 1 : 0;
        }

        helper(['feature', 'role']);
        if ($this->request->getPost('client_id') !== null) {
            if (! is_platform_superadmin()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Only the platform administrator can change which client owns a location.']);
            }
            $update['client_id'] = ((int) $this->request->getPost('client_id')) ?: null;
        }
        // A client admin may only edit its own client's locations.
        if (! is_platform_superadmin() && ! empty($location['client_id']) && (int) $location['client_id'] !== (int) current_company_id()) {
            return $this->response->setJSON(['success' => false, 'message' => 'That location belongs to another client.']);
        }

        if (empty($update)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nothing to update.']);
        }

        $model->update((int) $id, $update);

        // Changing the owner changes which clients are involved in passes using this location.
        if (array_key_exists('client_id', $update)) {
            $db = \Config\Database::connect();
            foreach ($db->table('vendors')->select('id, location_access')->like('location_access', $location['code'])->get()->getResultArray() as $v) {
                if (in_array($location['code'], array_map('trim', explode(',', (string) $v['location_access'])), true)) {
                    \App\Libraries\VendorClientApprovals::sync($db, (int) $v['id'], (string) $v['location_access']);
                }
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Location updated.']);
    }

    public function delete($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'manage_locations')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $model    = new VendorLocationModel();
        $location = $model->find((int) $id);
        if (! $location) {
            return $this->response->setJSON(['success' => false, 'message' => 'Location not found.']);
        }

        helper(['feature', 'role']);
        if (! is_platform_superadmin() && ! empty($location['client_id']) && (int) $location['client_id'] !== (int) current_company_id()) {
            return $this->response->setJSON(['success' => false, 'message' => 'That location belongs to another client.']);
        }

        // Soft-disable instead of a hard delete — a vendor record that
        // already stored this code in its comma-separated location_access
        // should keep showing the real label (on the read-only detail page,
        // Card Info, etc.) instead of turning into a bare unresolved code.
        $model->update((int) $id, ['is_active' => 0]);

        return $this->response->setJSON(['success' => true, 'message' => 'Location removed from the active list.']);
    }
}
