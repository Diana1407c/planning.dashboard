<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TLPlanningRequest;
use App\Http\Resources\EngineerTLPlanningResource;
use App\Http\Resources\TeamTLPlanningResource;
use App\Models\Engineer;
use App\Models\Team;
use App\Models\TLPlanning;
use App\Services\EngineerService;
use App\Services\ProjectService;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamLeadPlanningController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $engineers = EngineerService::filter($request);
        $projects = ProjectService::filter($request);

        $table = [];

        foreach ($engineers as $engineer){
            $currentPlannings = collect($engineer->teamLeadPlannings
                ->where('year', $request->get('year'))
                ->where('week', $request->get('week'))
                ->all());

            foreach ($projects as $project){
                $table[$engineer->id][$project->id] =
                    $currentPlannings->where('project_id', $project->id)->first()->hours ?? 0;
            }
        }

        return response()->json(['table' => $table]);
    }

    public function teamIndex(Request $request): AnonymousResourceCollection
    {
        return TeamTLPlanningResource::collection(TeamService::filter($request));
    }

    public function engineerIndex(): AnonymousResourceCollection
    {
        return EngineerTLPlanningResource::collection(Engineer::all());
    }

    public function teamShow(Team $team): TeamTLPlanningResource
    {
        return TeamTLPlanningResource::make($team);
    }

    public function engineerShow(Engineer $engineer): EngineerTLPlanningResource
    {
        return EngineerTLPlanningResource::make($engineer);
    }

    public function storeOrUpdate(TLPlanningRequest $request): JsonResponse
    {
        $data = $request->validated();
        $unique = array_diff_key($data, ['hours' => '']);

        $planned = TLPlanning::query()
            ->updateOrCreate($unique, [
                'hours' => $request->get('hours')
            ])->first();

        return response()->json([
            'message' => 'success',
            'hours' => $planned->hours
        ]);
    }

    public function delete(TLPlanning $team_lead_planning): JsonResponse
    {
        $team = $team_lead_planning->engineer->team;
        $team_lead_planning->delete();

        return response()->json([
            'message' => 'success',
            'planned' => TeamTLPlanningResource::make($team)
        ]);
    }
}
