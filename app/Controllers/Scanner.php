<?php

namespace App\Controllers;

class Scanner extends BaseController
{
    /**
     * Public QR scanner kiosk page.
     * No authentication required — used at security checkpoints.
     */
    public function index()
    {
        $db    = \Config\Database::connect();
        $lanes = $db->query(
            'SELECT sl.id, sl.name AS lane, NULL AS scan_type
             FROM sub_locations sl
             WHERE sl.status = "active"
             ORDER BY sl.name ASC'
        )->getResultArray();

        return view('scanner/index', [
            'pageTitle' => 'Card Scanner — SafeG',
            'lanes'     => $lanes,
        ]);
    }

    /**
     * JSON list of sub-locations used as scanner doors.
     * GET /scanner/lanes
     */
    public function getLanes()
    {
        $db    = \Config\Database::connect();
        $lanes = $db->query(
            'SELECT sl.id, sl.name AS lane, NULL AS scan_type
             FROM sub_locations sl
             WHERE sl.status = "active"
             ORDER BY sl.name ASC'
        )->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $lanes,
        ]);
    }
}
