<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlacklistRequestModel;

class BlacklistClosedList extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new BlacklistRequestModel();
    }

    public function index()
    {
        // Get only CLOSED records
        $closed = $this->model
            ->where('status', 'closed')
            ->orderBy('blacklist_date', 'DESC')
            ->findAll();

        return view('blacklist/closedlist', [
            'pageTitle' => 'Blacklist Closed List',
            'closed_blacklist' => $closed
        ]);
    }
}
