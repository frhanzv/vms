<?php

namespace App\Controllers;

class Config extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'System Configuration - SafeG'
        ];

        return view('config/index', $data);
    }
}
