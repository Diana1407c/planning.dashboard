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
use App\Matrix\PMWeeklyPlannedHoursMatrix;
use App\Matrix\TLMonthlyPlannedHoursMatrix;
use App\Matrix\TLWeeklyPlannedHoursMatrix;
use App\Models\PlannedHour;
use App\Models\Project;
use App\Services\HolidayService;
use App\Services\PlannedHourService;
use App\Support\Filters\PlannedHoursFilter;
use Illuminate\Http\JsonResponse;

class PlannedHourController extends Controller
{
    public function __construct(protected PlannedHourService $plannedHourService, protected HolidayService $holidayService)
    {
    }

    public function tlWeekly(WeeklyTLPlanningFilterRequest $request)
    {
        $filter = PlannedHoursFilter::fromArray($request->filter());
        $matrix = new TLWeeklyPlannedHoursMatrix($filter);

        return response()->json([
            'table' => $matrix->matrix(),
            'can_edit' => $this->plannedHourService->canEditPeriodByFilter($request->filter()),
        ]);
    }

    public function tlMonthly(MonthlyTLPlanningFilterRequest $request)
    {
        $filter = PlannedHoursFilter::fromArray($request->filter());
        $matrix = new TLMonthlyPlannedHoursMatrix($filter);

        return response()->json([
            'table' => $matrix->matrix(),
            'can_edit' => $this->plannedHourService->canEditPeriodByFilter($request->filter()),
            'hours_count' => $this->holidayService->monthWorkHours($filter->period->from, $filter->period->to),
        ]);
    }

    public function pmWeekly(WeeklyPMPlanningFilterRequest $request)
    {
        $filter = PlannedHoursFilter::fromArray($request->filter());
        $matrix = new PMWeeklyPlannedHoursMatrix($filter);

        return response()->json([
            'table' => $matrix->matrix(),
            'can_edit' => $this->plannedHourService->canEditPeriodByFilter($request->filter()),
        ]);
    }

    public function pmMonthly(MonthlyPMPlanningFilterRequest $request)
    {
        $filter = PlannedHoursFilter::fromArray($request->filter());
        $matrix = new PMMonthlyPlannedHoursMatrix($filter);

        return response()->json([
            'table' => $matrix->matrix(),
            'can_edit' => $this->plannedHourService->canEditPeriodByFilter($request->filter()),
            'hours_count' => $this->holidayService->monthWorkHours($filter->period->from, $filter->period->to),
        ]);
    }

    public function tlStore(TLPlanningRequest $request): JsonResponse
    {
        if (!$this->plannedHourService->canEditPeriodByFilter($request->only(['year', 'period_type', 'period_number']))) {
            return response()->json([
                'message' => 'This period cannot be edited',
            ], 400);
        }

        $isPerformanceProject = Project::where([
            'id' => $request->get('project_id'),
            'no_performance' => 0
        ])->exists();

        $performanceHours = $request->get('hours');
        if ($isPerformanceProject) {
            $performanceHours = $this->plannedHourService->calcPerformanceHours($request->get('hours'), $request->get('engineer_id'));
        }

        $this->plannedHourService->storeHours([
            'planable_type' => PlannedHour::ENGINEER_TYPE,
            'project_id' => $request->get('project_id'),
            'planable_id' => $request->get('engineer_id'),
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ], [
            'hours' => $request->get('hours'),
            'performance_hours' => $performanceHours
        ]);

        $filter = PlannedHoursFilter::fromArray([
            'planable_type' => PlannedHour::ENGINEER_TYPE,
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ]);

        $matrix = $request->get('period_type') == PlannedHour::MONTH_PERIOD_TYPE ? new TLMonthlyPlannedHoursMatrix($filter) : new TLWeeklyPlannedHoursMatrix($filter);

        return response()->json([
            'message' => 'Successfully changed',
            'table' => $matrix->matrix(),
        ]);
    }

    public function pmStore(PMPlanningRequest $request): JsonResponse
    {
        if (!$this->plannedHourService->canEditPeriodByFilter($request->only(['year', 'period_type', 'period_number']))) {
            return response()->json([
                'message' => 'This period cannot be edited',
            ], 400);
        }

        $this->plannedHourService->storeHours([
            'planable_type' => PlannedHour::TECHNOLOGY_TYPE,
            'project_id' => $request->get('project_id'),
            'planable_id' => $request->get('technology_id'),
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ], ['hours' => $request->get('hours')]);

        $filter = PlannedHoursFilter::fromArray([
            'planable_type' => PlannedHour::TECHNOLOGY_TYPE,
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ]);

        $matrix = $request->get('period_type') == PlannedHour::MONTH_PERIOD_TYPE ? new PMMonthlyPlannedHoursMatrix($filter) : new PMWeeklyPlannedHoursMatrix($filter);

        return response()->json([
            'message' => 'Successfully changed',
            'table' => $matrix->matrix(),
        ]);
    }
}
