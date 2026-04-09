<?php

namespace App\Controllers;

class BlacklistClosedList extends BaseController
{
    public function index()
    {
        return view('blacklist/closedlist');
    }
}
