<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class BlacklistEntry extends BaseController
{
    public function index()
    {
        return view('blacklist/entry', [
            'pageTitle' => 'Blacklist Entry'
        ]);
    }
}
