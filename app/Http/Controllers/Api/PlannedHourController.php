<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Planning\MonthlyPMPlanningFilterRequest;
use App\Http\Requests\Planning\MonthlyTLPlanningFilterRequest;
use App\Http\Requests\Planning\PlanningFilterInterface;
use App\Http\Requests\Planning\WeeklyPMPlanningFilterRequest;
use App\Http\Requests\Planning\WeeklyTLPlanningFilterRequest;
use App\Http\Requests\PMPlanningRequest;
use App\Http\Requests\TLPlanningRequest;
use App\Models\PlannedHour;
use App\Models\Technology;
use App\Services\EngineerService;
use App\Services\PlannedHourService;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;

class PlannedHourController extends Controller
{
    public function __construct(protected PlannedHourService $plannedHourService)
    {
    }

    public function tlWeekly(WeeklyTLPlanningFilterRequest $request)
    {
        return response()->json(['table' => $this->tlHours($request)]);
    }

    public function tlMonthly(MonthlyTLPlanningFilterRequest $request)
    {
        return response()->json(['table' => $this->tlHours($request)]);
    }

    public function pmWeekly(WeeklyPMPlanningFilterRequest $request)
    {
        return response()->json(['table' => $this->pmHours($request)]);
    }

    public function pmMonthly(MonthlyPMPlanningFilterRequest $request)
    {
        return response()->json(['table' => $this->pmHours($request)]);
    }

    protected function tlHours(PlanningFilterInterface $request)
    {
        $engineers = EngineerService::filter([
            'team_ids' => $request->get('team_ids')
        ]);
        $projects = ProjectService::filter($request);

        $data = [];

        $hours = $this->plannedHourService->hoursByFilter($request);

        foreach ($engineers as $engineer) {
            foreach ($projects as $project) {
                $data[$engineer->id][$project->id] =
                    $hours->where('project_id', $project->id)
                        ->where('planable_id', $engineer->id)
                        ->first()->hours ?? 0;
            }
        }

        return $data;
    }

    protected function pmHours(PlanningFilterInterface $request)
    {
        $technologies = Technology::all();
        $projects = ProjectService::filter($request);

        $data = [];

        $hours = $this->plannedHourService->hoursByFilter($request);

        foreach ($projects as $project) {
            foreach ($technologies as $technology) {
                $data[$project->id][$technology->id] =
                    $hours->where('project_id', $project->id)
                        ->where('planable_id', $technology->id)
                        ->first()->hours ?? 0;
            }
        }

        return $data;
    }

    public function tlStore(TLPlanningRequest $request): JsonResponse
    {
        $attributes = [
            'planable_type' => PlannedHour::ENGINEER_TYPE,
            'project_id' => $request->get('project_id'),
            'planable_id' => $request->get('engineer_id'),
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ];
        $values = [
            'hours' => $request->get('hours'),
        ];

        if ($request->get('hours') == 0) {
            PlannedHour::where($attributes)->delete();

            $hours = $request->get('hours');
        } else {
            $planned = PlannedHour::query()->updateOrCreate($attributes, $values);

            $hours = $planned->hours;
        }

        return response()->json([
            'message' => 'Successfully changed',
            'hours' => $hours
        ]);
    }

    public function pmStore(PMPlanningRequest $request): JsonResponse
    {
        $attributes = [
            'planable_type' => PlannedHour::TECHNOLOGY_TYPE,
            'project_id' => $request->get('project_id'),
            'planable_id' => $request->get('technology_id'),
            'year' => $request->get('year'),
            'period_type' => $request->get('period_type'),
            'period_number' => $request->get('period_number'),
        ];
        $values = [
            'hours' => $request->get('hours'),
        ];

        if ($request->get('hours') == 0) {
            PlannedHour::where($attributes)->delete();

            $hours = $request->get('hours');
        } else {
            $planned = PlannedHour::query()->updateOrCreate($attributes, $values);

            $hours = $planned->hours;
        }

        return response()->json([
            'message' => 'Successfully changed',
            'hours' => $hours
        ]);
    }
}
