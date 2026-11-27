<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class DriverController extends Controller
{
    public function index()
    {
        return view('partner.drivers.index');
    }
}