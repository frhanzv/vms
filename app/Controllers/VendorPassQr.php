<?php

namespace App\Controllers;

class VendorPassQr extends BaseController
{
    /**
     * Staff-side: generate (or reuse) the QR string for a vendor pass and
     * show it. Matches KPK's getVendorPassQRById: ID-prefixed + random
     * uppercase-letter padding, stored once and reused after that.
     */
    public function generate($id)
    {
        $db     = \Config\Database::connect();
        $vendor = $db->table('vendors')->where('id', (int) $id)->get()->getRowArray();

        if (!$vendor) {
            return redirect()->to(base_url('vendors'))->with('error', 'Vendor pass record not found.');
        }

        if (empty($vendor['qr_string'])) {
            $qrLength   = 20; // matches KPK's configured qrStringLength
            $idPart     = (string) $vendor['id'];
            $padding    = max(0, $qrLength - strlen($idPart));

            // KPK uses RandomStringUtils.random(size, useLetters=true, useNumbers=false) — letters only.
            $alphabet   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $randomPart = '';
            for ($i = 0; $i < $padding; $i++) {
                $randomPart .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $qrString = $idPart . $randomPart;

            $db->table('vendors')->where('id', (int) $id)->update(['qr_string' => $qrString]);
            $vendor['qr_string'] = $qrString;
        }

        $publicUrl = base_url('vendor-pass-qr/' . $vendor['qr_string']);

        return view('vendors/qr_generate', [
            'pageTitle' => 'Vendor Pass QR - SafeG',
            'vendor'    => $vendor,
            'publicUrl' => $publicUrl,
        ]);
    }

    /**
     * Public, no-login page — matches KPK's getVendorPassByQR. This is what
     * opens when someone scans the pass at a gate/checkpoint.
     */
    public function show($qrString)
    {
        helper('privacy');
        $db     = \Config\Database::connect();
        $vendor = $db->table('vendors')->where('qr_string', $qrString)->get()->getRowArray();

        return view('vendors/qr_public', [
            'pageTitle' => 'Vendor Pass Verification - SafeG',
            'vendor'    => $vendor, // null if not found — the view handles that
        ]);
    }
}
