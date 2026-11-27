<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('partner.dashboard');
    }
}