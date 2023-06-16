<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EngineerResource;
use App\Services\DateService;
use App\Services\EngineerService;
use Illuminate\Http\Request;

class EngineerController extends Controller
{
    public function index(Request $request)
    {
        $engineers = EngineerService::filter([
            'team_ids' => $request->get('team_ids'),
            'project_ids' => $request->get('project_ids'),
            'dates' => DateService::rangeToWeeks($request->get('start_date'), $request->get('end_date')),
            'with_planning' => $request->get('with_planning'),
            'min_hours' => $request->get('min_hours'),
            'max_hours' => $request->get('max_hours'),
        ]);

        return EngineerResource::collection($engineers);
    }
}
