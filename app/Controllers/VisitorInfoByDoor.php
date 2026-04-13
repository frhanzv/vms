<?php

namespace App\Controllers;

use App\Models\LocationModel;

class VisitorInfoByDoor extends BaseController
{
    protected $locationModel;

    public function __construct()
    {
        $this->locationModel = new LocationModel();
    }

    public function index()
    {
        return view('reports/visitor_info_by_door', [
            'pageTitle' => 'Visitor Info By Door - SafeG',
            'locations' => $this->locationModel->getAllActive(),
        ]);
    }

    public function generate()
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'No data source configured for this report yet.',
            'rows'    => [],
        ]);
    }
}
