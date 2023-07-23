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

    public function comparisonDetail(Project $project, Request $request): JsonResponse
    {
        $plannedHours = PlannedHour::query()
            ->where('year', $request->get('year'))
            ->where('period_type', $request->get('period_type'))
            ->where('period_number', $request->get('period_number'))
            ->where('project_id', $project->id)
            ->get();

        $teams = Team::query()->select('teams.*')
            ->join('engineers', function ($join) use ($plannedHours) {
                $join->on('teams.id', 'engineers.team_id')
                    ->whereIn('engineers.id', $plannedHours->where('planable_type', PlannedHour::ENGINEER_TYPE)->pluck('planable_id'));
            })->with('members')->distinct('teams.id')->get();

        $technologies = Technology::query()->whereIn('id', $plannedHours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)->pluck('planable_id'))->get();


        $hours = [];

        foreach ($teams as $team) {
            foreach ($team->members as $member) {
                $hours['tl'][$member->id] = $plannedHours->where('planable_type', PlannedHour::ENGINEER_TYPE)
                    ->where('planable_id', $member->id)->first()->hours ?? 0;
            }
        }

        foreach ($technologies as $technology) {
            $hours['pm'][$technology->id] = $plannedHours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)
                ->where('planable_id', $technology->id)->first()->hours ?? 0;
        }


        return response()->json([
            'hours' => $hours,
            'teams' => TeamResource::collection($teams),
            'technologies' => TechnologyResource::collection($technologies),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $matrix = new ComparisonMatrix($request->all(), $request->get('period_type'));

        return Excel::download(new ComparisonExport($matrix->matrix()), 'comparison.xlsx');
    }
}
