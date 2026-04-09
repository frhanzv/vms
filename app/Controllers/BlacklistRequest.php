<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class BlacklistRequest extends BaseController
{
    public function requestList()
    {
        $data = [
            'pageTitle' => 'Blacklist Individual Request List',
            'blacklist' => [],
        ];

        return view('blacklist/blacklistrequest', $data);
    }
}