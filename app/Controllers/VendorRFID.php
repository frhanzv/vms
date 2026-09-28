<?php

namespace App\Controllers;

use App\Models\VisitorCardModel;
use CodeIgniter\RESTful\ResourceController;

/**
 * Vendor gate scanning. Reuses the shared physical card pool (VisitorCardModel /
 * visitor_cards) since a card reader doesn't care which module the card is
 * assigned to — only the *record it's currently bound to* differs (a vendor
 * here, instead of an invitation).
 *
 * Mirrors RFID::scan()'s toggle pattern: first scan = check-in, second scan
 * (while still checked in) = check-out. Kept deliberately smaller than
 * RFID::scan() — no security-alert insertion — to match the scope of what's
 * been built for vendors so far; can be extended the same way later.
 */
class VendorRFID extends ResourceController
{
    protected $format = 'json';
    protected $visitorCardModel;

    public function __construct()
    {
        $this->visitorCardModel = new VisitorCardModel();
    }

    /**
     * GET /api/vendor-rfid/scan?card_epc=...
     */
    public function scan()
    {
        $cardEpc = $this->request->getGet('card_epc');

        if (empty($cardEpc)) {
            return $this->failValidationError('Card EPC is required');
        }

        $card = $this->visitorCardModel->where('card_id', $cardEpc)->first();

        if (!$card) {
            log_message('warning', 'Unknown card scanned (vendor gate): ' . $cardEpc);
            return $this->respond(['success' => false, 'message' => 'Card not registered in system', 'card_epc' => $cardEpc]);
        }

        if (!in_array($card['status'], ['active', 'in_use'], true)) {
            return $this->respond(['success' => false, 'message' => 'Card is ' . $card['status'], 'card_epc' => $cardEpc]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Find the open visit cycle for a vendor currently bound to this card.
            $visit = $db->query(
                'SELECT vv.*, vv.id AS visit_id, v.full_name AS vendor_name, v.vendor_company_name,
                        v.id AS vendor_id, v.status AS vendor_status, v.pass_expiry
                 FROM vendor_visits vv
                 JOIN vendors v ON v.id = vv.vendor_id
                 WHERE vv.vendor_card_id = ?
                 AND vv.check_out_time IS NULL
                 FOR UPDATE',
                [$card['id']]
            )->getRowArray();

            if ($visit) {
                // --- CHECK-OUT ---
                $db->table('vendor_visits')
                    ->where('id', $visit['visit_id'])
                    ->where('check_out_time IS NULL')
                    ->update([
                        'check_out_time' => date('Y-m-d H:i:s'),
                        'version'        => ($visit['version'] ?? 1) + 1,
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);

                if ($db->affectedRows() === 0) {
                    $db->transRollback();
                    return $this->respond(['success' => false, 'message' => 'Vendor has already been checked out', 'card_epc' => $cardEpc]);
                }

                $this->visitorCardModel->update($card['id'], ['status' => 'active']);

                $db->table('vendor_card_logs')->insert([
                    'vendor_card_id' => (int) $card['id'],
                    'vendor_id'      => (int) $visit['vendor_id'],
                    'action'         => 'checkout',
                    'scanned_at'     => date('Y-m-d H:i:s'),
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);

                $db->transComplete();

                return $this->respond([
                    'success'        => true,
                    'access_granted' => true,
                    'action'         => 'checkout',
                    'message'        => 'Checked out: ' . $visit['vendor_name'],
                    'vendor_name'    => $visit['vendor_name'],
                    'card_epc'       => $cardEpc,
                ]);
            }

            // --- CHECK-IN: is this card currently assigned to an Approved, non-expired vendor? ---
            $vendor = $db->query(
                'SELECT v.* FROM vendors v
                 WHERE v.card_id = ?
                 AND v.status = ?
                 FOR UPDATE',
                [$card['id'], 'Approved']
            )->getRowArray();

            if (!$vendor) {
                $db->transRollback();
                return $this->respond([
                    'success'        => false,
                    'access_granted' => false,
                    'action'         => 'denied',
                    'message'        => 'Access denied: Card is not assigned to any approved vendor pass.',
                    'card_epc'       => $cardEpc,
                ]);
            }

            if (!empty($vendor['pass_expiry']) && strtotime($vendor['pass_expiry']) < strtotime(date('Y-m-d'))) {
                $db->transRollback();
                return $this->respond([
                    'success'        => false,
                    'access_granted' => false,
                    'action'         => 'denied',
                    'message'        => 'Access denied: Vendor pass has expired.',
                    'card_epc'       => $cardEpc,
                ]);
            }

            $db->table('vendor_visits')->insert([
                'vendor_id'      => (int) $vendor['id'],
                'vendor_card_id' => (int) $card['id'],
                'check_in_time'  => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            $this->visitorCardModel->update($card['id'], ['status' => 'in_use']);

            $db->table('vendor_card_logs')->insert([
                'vendor_card_id' => (int) $card['id'],
                'vendor_id'      => (int) $vendor['id'],
                'action'         => 'checkin',
                'scanned_at'     => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            return $this->respond([
                'success'        => true,
                'access_granted' => true,
                'action'         => 'checkin',
                'message'        => 'Checked in: ' . $vendor['full_name'],
                'vendor_name'    => $vendor['full_name'],
                'card_epc'       => $cardEpc,
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'VendorRFID::scan error: ' . $e->getMessage());
            return $this->respond(['success' => false, 'message' => 'Scan failed, please try again.', 'card_epc' => $cardEpc]);
        }
    }
}
