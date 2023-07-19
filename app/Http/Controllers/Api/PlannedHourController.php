<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planning\MonthlyPMPlanningFilterRequest;
use App\Http\Requests\Planning\MonthlyTLPlanningFilterRequest;
use App\Http\Requests\Planning\WeeklyPMPlanningFilterRequest;
use App\Http\Requests\Planning\WeeklyTLPlanningFilterRequest;
use App\Http\Requests\PMPlanningRequest;
use App\Http\Requests\TLPlanningRequest;
use App\Matrix\PMMonthlyPlannedHoursMatrix;
use App\Matrix\TLMonthlyPlannedHoursMatrix;
use App\Matrix\TLWeeklyPlannedHoursMatrix;
use App\Models\PlannedHour;
use App\Services\PlannedHourService;
use Illuminate\Http\JsonResponse;

class PlannedHourController extends Controller
{
    public function tlWeekly(WeeklyTLPlanningFilterRequest $request)
    {
        $matrix = new TLWeeklyPlannedHoursMatrix($request->filter());

        return response()->json(['table' => $matrix->matrix()]);
    }

    public function tlMonthly(MonthlyTLPlanningFilterRequest $request)
    {
        $matrix = new TLMonthlyPlannedHoursMatrix($request->filter());

        return response()->json(['table' => $matrix->matrix()]);
    }

    public function pmWeekly(WeeklyPMPlanningFilterRequest $request)
    {
        $matrix = new PMMonthlyPlannedHoursMatrix($request->filter());

        return response()->json(['table' => $matrix->matrix()]);
    }

    public function pmMonthly(MonthlyPMPlanningFilterRequest $request)
    {
        $matrix = new PMMonthlyPlannedHoursMatrix($request->filter());

        return response()->json(['table' => $matrix->matrix()]);
    }

    public function tlStore(PlannedHourService $plannedHourService, TLPlanningRequest $request): JsonResponse
    {
        $plannedHourService->storeHours([
            'planable_type' => PlannedHour::ENGINEER_TYPE,
            'project_id' => $request->get('project_id'),
            'planable_id' => $request->get('engineer_id'),
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ], ['hours' => $request->get('hours')]);

        $filter = [
            'planable_type' => PlannedHour::ENGINEER_TYPE,
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ];

        $matrix = $request->get('period_type') == PlannedHour::MONTH_PERIOD_TYPE ? new TLMonthlyPlannedHoursMatrix($filter) : new TLWeeklyPlannedHoursMatrix($filter);

        return response()->json([
            'message' => 'Successfully changed',
            'table' => $matrix->matrix(),
        ]);
    }

    public function pmStore(PlannedHourService $plannedHourService, PMPlanningRequest $request): JsonResponse
    {
        $plannedHourService->storeHours([
            'planable_type' => PlannedHour::TECHNOLOGY_TYPE,
            'project_id' => $request->get('project_id'),
            'planable_id' => $request->get('technology_id'),
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ], ['hours' => $request->get('hours')]);

        $matrix = new PMMonthlyPlannedHoursMatrix([
            'planable_type' => PlannedHour::TECHNOLOGY_TYPE,
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ]);

        return response()->json([
            'message' => 'Successfully changed',
            'table' => $matrix->matrix(),
        ]);
    }
}
