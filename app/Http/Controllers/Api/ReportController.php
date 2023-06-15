<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StackResource;
use App\Http\Resources\TeamResource;
use App\Models\Engineer;
use App\Models\PMPlanningPrices;
use App\Models\Project;
use App\Models\Stack;
use App\Models\Team;
use App\Services\DateService;
use App\Services\PMPlanningService;
use App\Services\ProjectService;
use App\Services\StackService;
use App\Services\TLPlanningPlanning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function comparison(Request $request): JsonResponse
    {
        $projects = ProjectService::filter($request);
        $dates = DateService::rangeToWeeks($request->get('start_date'), $request->get('end_date'));
        $datesArray = DateService::weeksDateArray($dates);

        $PMPlannings = PMPlanningService::filter(['range' => $dates]);
        $TLPlannings = TLPlanningPlanning::filter(['range' => $dates]);

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

    public function comparisonDetail(Project $project, Request $request): JsonResponse
    {
        $PMPlannings = PMPlanningService::filterDetail([
            'week' => $request->get('week'),
            'year' => $request->get('year'),
            'project_id' => $project->id
        ]);

        $TLPlannings = TLPlanningPlanning::filterDetail([
            'week' => $request->get('week'),
            'year' => $request->get('year'),
            'project_id' => $project->id
        ]);

        $teams = Team::query()->select('teams.*')
            ->join('engineers', function ($join) use($TLPlannings){
                $join->on('teams.id', 'engineers.team_id')
                    ->whereIn('engineers.id', $TLPlannings->keys());
            })->distinct('teams.id')->get();

        $stacks = Stack::query()->whereIn('id', $PMPlannings->keys())->get();

        $cost = PMPlanningPrices::query()
            ->where('project_id', $project->id)
            ->where('year',  $request->get('year'))
            ->where('week',  $request->get('week'))
            ->value('cost');

        return response()->json([
            'pm_planning' => $PMPlannings,
            'tl_planning' => $TLPlannings,
            'teams' => TeamResource::collection($teams) ,
            'stacks' => StackResource::collection($stacks),
            'cost' => $cost
        ]);
    }

    protected static function filterProject(&$query, int $project_id): void
    {
        $query->where('project-id', $project_id);
    }
}
