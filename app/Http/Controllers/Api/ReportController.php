<?php

namespace App\Http\Controllers\Api;

use App\Exports\ComparisonExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\StackResource;
use App\Http\Resources\TeamResource;
use App\Matrix\ComparisonMatrix;
use App\Models\PMPlanningPrices;
use App\Models\Project;
use App\Models\Stack;
use App\Models\Team;
use App\Services\PMPlanningService;
use App\Services\TLPlanningService;
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
        $PMPlannings = PMPlanningService::hoursByStack([
            'week' => $request->get('week'),
            'year' => $request->get('year'),
            'project_id' => $project->id
        ]);

        $TLPlannings = TLPlanningService::hoursByEngineer([
            'week' => $request->get('week'),
            'year' => $request->get('year'),
            'project_id' => $project->id
        ]);

        $teams = Team::query()->select('teams.*')
            ->join('engineers', function ($join) use ($TLPlannings) {
                $join->on('teams.id', 'engineers.team_id')
                    ->whereIn('engineers.id', $TLPlannings->keys());
            })->distinct('teams.id')->get();

        $stacks = Stack::query()->whereIn('id', $PMPlannings->keys())->get();

        $cost = PMPlanningPrices::query()
            ->where('project_id', $project->id)
            ->where('year', $request->get('year'))
            ->where('week', $request->get('week'))
            ->value('cost');

        return response()->json([
            'pm_planning' => $PMPlannings,
            'tl_planning' => $TLPlannings,
            'teams' => TeamResource::collection($teams),
            'stacks' => StackResource::collection($stacks),
            'cost' => $cost
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $matrix = new ComparisonMatrix($request->all(), $request->get('period_type'));

        return Excel::download(new ComparisonExport($matrix->matrix()), 'comparison.xlsx');
    }
}
