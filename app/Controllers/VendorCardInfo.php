<?php

namespace App\Controllers;

use App\Models\VendorLocationModel;

/**
 * "Card Info" panel — KPK's card-management screen, traced from the real
 * Closed List detail screenshot: a Driving License section (repeatable,
 * "Add License"), a Card Info block (serial, card id, card expiry,
 * collector details), Location Access checkboxes, and the action row:
 * Back, Add License, Edit Location Access, Upload Photo, Activate Card,
 * QR Code, Reprint, Card Terminate, Update.
 *
 * Reachable both from Closed List (an issued/active card) and from Process
 * Detail's "Card Details" button (before it's been issued at all) — same
 * screen either way, same as KPK reusing one card-management view for both.
 */
class VendorCardInfo extends BaseController
{
    private function loadScopedVendor($id): ?array
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('vendors')->where('id', (int) $id);
        helper('vendor_client_scope');
        vendor_client_scope($builder);
        return $builder->get()->getRowArray() ?: null;
    }

    public function view($id)
    {
        helper(['access', 'privacy', 'role']);
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return redirect()->to(base_url('vendors'))->with('error', 'Record not found.');
        }

        $db = \Config\Database::connect();

        $licenses = $db->table('vendor_driving_licenses')
            ->where('vendor_id', (int) $id)
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $printLogs = $db->table('vendor_card_print_logs')
            ->where('vendor_id', (int) $id)
            ->orderBy('printed_at', 'DESC')
            ->get()->getResultArray();

        $boundCard = $vendor['card_id']
            ? $db->table('visitor_cards')->where('id', (int) $vendor['card_id'])->get()->getRowArray()
            : null;

        $selectedLocations = $vendor['location_access'] ? explode(',', $vendor['location_access']) : [];

        $formFieldModel = new \App\Models\ClientFormFieldModel();
        $companyId      = current_company_id();
        $cfg            = fn(string $key) => $formFieldModel->isEnabled($companyId, 'vendor_pass_request', $key);
        $canEditBase    = has_access('vendor_pass_list', 'edit');

        return view('vendors/card_info', [
            'pageTitle'         => 'Vendor Pass Card Info - SafeG',
            'vendor'            => $vendor,
            'icPassport'        => mask_ic_passport($vendor['ic_no'] ?: ($vendor['passport_no'] ?? ''), 'N/A'),
            'licenses'          => $licenses,
            'printLogs'         => $printLogs,
            'boundCard'         => $boundCard,
            'locationOptions'   => (new VendorLocationModel())->getOptionsForUser(true),
            'selectedLocations' => $selectedLocations,
            'canEdit'           => $canEditBase,
            'canAddLicense'     => $canEditBase && $cfg('card_info_add_license_button'),
            'canEditLocation'   => $canEditBase && $cfg('card_info_edit_location_button'),
            'canUploadPhoto'    => $canEditBase && $cfg('card_info_upload_photo_button'),
            'canActivate'       => $canEditBase && $cfg('card_info_activate_button'),
            'canTerminate'      => $canEditBase && $cfg('card_info_terminate_button'),
        ]);
    }

    /** addLicense() — KPK's addLicense(). Repeatable; a vendor can hold more than one. */
    public function addLicense($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')
            || ! (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'card_info_add_license_button')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $body   = $this->request->getJSON(true) ?? [];
        $class  = trim((string) ($body['license_class'] ?? ''));
        $expiry = trim((string) ($body['license_expiry'] ?? ''));
        if ($class === '' || $expiry === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Please fill in the license class and expiry.']);
        }

        \Config\Database::connect()->table('vendor_driving_licenses')->insert([
            'vendor_id'      => (int) $id,
            'license_class'  => $class,
            'license_expiry' => $expiry,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Driving license added.']);
    }

    /** updateLocationAccess() — KPK's editLocationAccess(). */
    public function updateLocationAccess($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')
            || ! (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'card_info_edit_location_button')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $body     = $this->request->getJSON(true) ?? [];
        $visible  = array_keys((new VendorLocationModel())->getOptionsForUser(true));
        $selected = array_values(array_intersect((array) ($body['locations'] ?? []), $visible));
        $csv      = \App\Libraries\VendorClientApprovals::mergeLocations((string) ($vendor['location_access'] ?? ''), $selected, $visible);
        if ($csv === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Location Access is mandatory — choose at least one.']);
        }

        $db = \Config\Database::connect();
        $db->table('vendors')->where('id', (int) $id)->update(['location_access' => $csv]);
        \App\Libraries\VendorClientApprovals::sync($db, (int) $id, $csv);

        return $this->response->setJSON(['success' => true, 'message' => 'Location access updated.']);
    }

    /** uploadPhoto() — same idea as VendorProcessDetail::uploadPhoto(), kept independent so this page works standalone from Closed List. */
    public function uploadPhoto($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')
            || ! (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'card_info_upload_photo_button')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $file = $this->request->getFile('photo');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No photo received.']);
        }

        $newName = $file->getRandomName();
        $file->move('uploads/facial_photos', $newName);

        \Config\Database::connect()->table('vendors')->where('id', (int) $id)->update(['facial_photo' => $newName]);

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Photo saved.',
            'photo_url' => base_url('uploads/facial_photos/' . $newName),
        ]);
    }

    /**
     * activateCard() — KPK's activateCard(). A card can only be activated
     * once it's actually been printed (has a receipt_no) — activating an
     * un-printed record would show a green "ACTIVE" status for a pass that
     * has no physical card yet.
     */
    public function activateCard($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')
            || ! (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'card_info_activate_button')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }
        if (empty($vendor['receipt_no'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'This card has not been printed yet — print it from Printing List first.']);
        }

        \Config\Database::connect()->table('vendors')->where('id', (int) $id)->update([
            'card_status'   => 'Active',
            'terminated_at' => null,
            'terminated_by' => null,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Card activated.']);
    }

    /**
     * terminateCard() — KPK's terminateCard(). Marks the pass's card
     * terminated and releases the bound physical card back to the shared
     * pool (same release step VendorProcessDetail::readCard() uses when
     * swapping a vendor onto a different card).
     */
    public function terminateCard($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')
            || ! (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'card_info_terminate_button')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('vendors')->where('id', (int) $id)->update([
            'card_status'   => 'Terminated',
            'terminated_at' => date('Y-m-d H:i:s'),
            'terminated_by' => (string) (session()->get('full_name') ?: session()->get('username')),
        ]);

        if ($vendor['card_id']) {
            $db->table('visitor_cards')->where('id', (int) $vendor['card_id'])->where('status !=', 'lost')->update(['status' => 'active']);
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Could not terminate the card — please try again.']);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Card terminated and the physical card released back to the pool.']);
    }

    /** update() — saves the editable Card Info fields (card expiry + collector details). */
    public function update($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $body   = $this->request->getJSON(true) ?? [];
        $update = [];
        foreach (['pass_expiry', 'collector_name', 'collector_ic_passport'] as $field) {
            if (array_key_exists($field, $body)) {
                $value          = trim((string) $body[$field]);
                $update[$field] = $value !== '' ? $value : null;
            }
        }
        if (empty($update)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nothing to update.']);
        }

        \Config\Database::connect()->table('vendors')->where('id', (int) $id)->update($update);

        return $this->response->setJSON(['success' => true, 'message' => 'Card info saved.']);
    }
}
