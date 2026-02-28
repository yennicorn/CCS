<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return view('master.settings');
    }
}

