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

        $model = new VendorLocationModel();

        return view('vendors/locations', [
            'pageTitle' => 'Vendor Locations - SafeG',
            'locations' => $model->getAllOrdered(),
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

        $ok = $model->insert([
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

        if (empty($update)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nothing to update.']);
        }

        $model->update((int) $id, $update);

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

        // Soft-disable instead of a hard delete — a vendor record that
        // already stored this code in its comma-separated location_access
        // should keep showing the real label (on the read-only detail page,
        // Card Info, etc.) instead of turning into a bare unresolved code.
        $model->update((int) $id, ['is_active' => 0]);

        return $this->response->setJSON(['success' => true, 'message' => 'Location removed from the active list.']);
    }
}
