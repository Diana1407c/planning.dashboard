<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DateService;
use App\Services\PMPlanningService;
use App\Services\ProjectService;
use App\Services\TLPlanningPlanning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function comparison(Request $request): JsonResponse
    {
        $projects = ProjectService::filter($request);
        $dates = DateService::convertDatesToWeek($request->get('start_date'), $request->get('end_date'));
        $datesArray = DateService::generateYearWeekArray($dates);
        $PMPlannings = PMPlanningService::filter($request, $dates);
        $TLPlannings = TLPlanningPlanning::filter($request, $dates);

        $rawDates = [];
        $report = [];
        foreach ($datesArray as $date){
            $rawDates[$date['index']] = $date['formatted'];

            foreach ($projects as $project) {
                $report[$project->id][$date['index']]['PM']
                    = intval($PMPlannings->where('project_id', $project->id)
                        ->where('year', $date['year'])
                        ->where('week', $date['week'])
                        ->value('sum_hours')) ?? 0;

                $report[$project->id][$date['index']]['TL']
                    = intval($TLPlannings->where('project_id', $project->id)
                        ->where('year', $date['year'])
                        ->where('week', $date['week'])
                        ->value('sum_hours')) ?? 0;
            }
        }

        return response()->json([
            'dates' => $rawDates,
            'projects' => $projects,
            'report' => $report
        ]);
    }
}
