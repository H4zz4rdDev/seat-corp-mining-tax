<?php

namespace pyTonicis\Seat\SeatCorpMiningTax\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
class CorpMiningEvents extends Controller
{
    public function getHome()
    {
        return view('corpminingtax::corpminingtaxhome');
    }
}