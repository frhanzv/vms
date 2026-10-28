<?php

namespace App\Controllers;

/**
 * Issue Card: hands the vendor card over (card_status = Active, which moves it to the Closed List)
 * and records who collected it, as KPK's issueCards() does. There is no Issuance List page; the
 * Issue button sits on the Process List and the Printing List rows.
 */
class VendorCardIssue extends BaseController
{
    /** Who may see / use the Issue button (role edit right + the client's form-field toggle + Vendor Workflow "card issuance"). */
    public static function allowed(): bool
    {
        helper(['access', 'feature']);

        return has_access('vendor_pass_list', 'edit')
            && (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'issuance_issue_button')
            && \App\Libraries\VendorWorkflow::enabled(\Config\Database::connect(), (int) current_company_id(), 'card_issuance');
    }

    public function issue($id)
    {
        if (! self::allowed()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $body                = $this->request->getJSON(true) ?? [];
        $collectorName       = trim((string) ($body['collector_name'] ?? ''));
        $collectorIcPassport = trim((string) ($body['collector_ic_passport'] ?? ''));
        if ($collectorName === '' || $collectorIcPassport === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter the collector\'s name and IC/passport before issuing the card.']);
        }

        $db      = \Config\Database::connect();
        $builder = $db->table('vendors')->where('id', (int) $id);
        helper('vendor_client_scope');
        vendor_client_scope($builder);
        $vendor = $builder->get()->getRowArray();
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }
        if (($vendor['status'] ?? '') !== 'Approved' || ($vendor['card_status'] ?? 'Inactive') === 'Active') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only an approved pass whose card has not been issued yet can be issued.']);
        }

        $db->table('vendors')->where('id', (int) $id)->update([
            'card_status'           => 'Active',
            'collector_name'        => $collectorName,
            'collector_ic_passport' => $collectorIcPassport,
            'issued_by'             => (string) (session()->get('full_name') ?: session()->get('username')),
            'issued_at'             => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Card issued to ' . $collectorName . ' — moved to Closed List.']);
    }
}
