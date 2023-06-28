<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TLPlanningRequest;
use App\Models\TLPlanning;
use App\Services\EngineerService;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamLeadPlanningController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $engineers = EngineerService::filter([
            'team_ids' => $request->get('team_ids')
        ]);
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

    public function storeOrUpdate(TLPlanningRequest $request): JsonResponse
    {
        $data = $request->validated();
        $unique = array_diff_key($data, ['hours' => '']);

        if($request->get('hours') == 0){
            TLPlanning::where($unique)->delete();

            $hours = $request->get('hours');
        } else {
            $planned = TLPlanning::updateOrCreate($unique, [
                'hours' => $request->get('hours')
            ]);

            $hours = $planned->hours;
        }

        return response()->json([
            'message' => 'Successfully changed',
            'hours' => $hours
        ]);
    }
}
