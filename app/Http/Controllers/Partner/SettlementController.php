<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class SettlementController extends Controller
{
    public function index()
    {
        return view('partner.settlements.index');
    }
}