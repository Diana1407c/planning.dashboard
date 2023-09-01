<?php

namespace App\Http\Controllers\Api;

use App\Exports\ComparisonExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TechnologyResource;
use App\Matrix\ComparisonMatrix;
use App\Models\PlannedHour;
use App\Models\Project;
use App\Models\Team;
use App\Models\Technology;
use App\Services\TeamService;
use App\Services\Teamwork\TeamworkService;
use App\Support\GenericPeriod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function comparison(Request $request): JsonResponse
    {
        $matrix = new ComparisonMatrix($request->all(), $request->get('period_type'));

        return response()->json($matrix->matrix());
    }

    public function comparisonDetail(TeamworkService $teamworkService, TeamService $teamService, Project $project, Request $request): JsonResponse
    {
        $plannedHours = PlannedHour::query()
            ->where('year', $request->get('year'))
            ->where('period_type', $request->get('period_type'))
            ->where('period_number', $request->get('period_number'))
            ->where('project_id', $project->id)
            ->get();

        $plannedTeams = $teamService->teamsByEngineers($plannedHours->where('planable_type', PlannedHour::ENGINEER_TYPE)->pluck('planable_id'));
        $technologies = Technology::query()->whereIn('id', $plannedHours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)->pluck('planable_id'))->get();

        $period = GenericPeriod::fromType($request->get('period_type'), $request->get('year'), $request->get('period_number'));
        $twHours = $teamworkService->projectHours($project, $period);
        $twTeams = $teamService->teamsByEngineers($twHours->pluck('engineer_id'));

        $hours = [];

        foreach ($twTeams as $team) {
            foreach ($team->members as $member) {
                $hours['tw'][$member->id] = [
                    'billable' => twHours($twHours->where('engineer_id', $member->id)
                        ->where('billable', 1)->sum('sum_hours')),
                    'no_billable' => twHours($twHours->where('engineer_id', $member->id)
                        ->where('billable', 0)->sum('sum_hours')),
                    'total' => twHours($twHours->where('engineer_id', $member->id)->sum('sum_hours')),
                ];
            }
        }
        $hours['tw']['total'] = [
            'billable' => twHours($twHours->where('billable', 1)->sum('sum_hours')),
            'no_billable' => twHours($twHours->where('billable', 0)->sum('sum_hours')),
            'total' => twHours($twHours->sum('sum_hours')),
        ];

        foreach ($plannedTeams as $team) {
            foreach ($team->members as $member) {
                $hours['tl'][$member->id] = $plannedHours->where('planable_type', PlannedHour::ENGINEER_TYPE)
                    ->where('planable_id', $member->id)->first()->performance_hours ?? 0;
            }
        }
        $hours['tl']['total'] = $plannedHours->where('planable_type', PlannedHour::ENGINEER_TYPE)->sum('performance_hours');

        foreach ($technologies as $technology) {
            $hours['pm'][$technology->id] = $plannedHours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)
                ->where('planable_id', $technology->id)->first()->hours ?? 0;
        }
        $hours['pm']['total'] = $plannedHours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)->sum('hours');


        return response()->json([
            'hours' => $hours,
            'teams' => TeamResource::collection($plannedTeams),
            'tw_teams' => TeamResource::collection($twTeams),
            'technologies' => TechnologyResource::collection($technologies),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $matrix = new ComparisonMatrix($request->all(), $request->get('period_type'));

        return Excel::download(new ComparisonExport($matrix->matrix()), 'comparison.xlsx');
    }
}
