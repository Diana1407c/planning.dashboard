<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DateService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function comparison(Request $request)
    {
        $dates = DateService::convertDatesToWeek($request->get('start_date'), $request->get('end_date'));
    }
}
