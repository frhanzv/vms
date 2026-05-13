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
            'SELECT l.id, l.lane, da.type AS scan_type
             FROM lanes l
             LEFT JOIN device_assignments da ON da.location_id = l.id
             ORDER BY l.lane ASC'
        )->getResultArray();

        // Deduplicate lanes that appear more than once due to multiple device_assignments
        $seen  = [];
        $clean = [];
        foreach ($lanes as $row) {
            if (!isset($seen[$row['id']])) {
                $seen[$row['id']] = true;
                $clean[]          = $row;
            }
        }

        return view('scanner/index', [
            'pageTitle' => 'QR Scanner — SafeG',
            'lanes'     => $clean,
        ]);
    }

    /**
     * JSON list of lanes (used by the scanner JS to populate the selector).
     * GET /scanner/lanes
     */
    public function getLanes()
    {
        $db    = \Config\Database::connect();
        $lanes = $db->query(
            'SELECT l.id, l.lane, da.type AS scan_type
             FROM lanes l
             LEFT JOIN device_assignments da ON da.location_id = l.id
             ORDER BY l.lane ASC'
        )->getResultArray();

        $seen  = [];
        $clean = [];
        foreach ($lanes as $row) {
            if (!isset($seen[$row['id']])) {
                $seen[$row['id']] = true;
                $clean[]          = $row;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $clean,
        ]);
    }
}
