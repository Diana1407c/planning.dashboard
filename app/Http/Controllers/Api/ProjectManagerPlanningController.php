<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PMPlanningRequest;
use App\Models\PMPlanning;
use App\Services\ProjectService;
use App\Services\StackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectManagerPlanningController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stacks = StackService::filter($request);
        $projects = ProjectService::filter($request);

        $table = [];

        foreach ($projects as $project){
            $currentPlannings = collect($project->projectManagerPlannings
                ->where('year', $request->get('year'))
                ->where('week', $request->get('week'))
                ->all());

            foreach ($stacks as $stack){
                $table[$project->id][$stack->id] =
                    $currentPlannings->where('stack_id', $stack->id)->first()->hours ?? 0;
            }
        }

        return response()->json(['table' => $table]);
    }

    public function storeOrUpdate(PMPlanningRequest $request): JsonResponse
    {
        $data = $request->validated();
        $unique = array_diff_key($data, ['hours' => '']);

        $planned = PMPlanning::updateOrCreate($unique, [
                'hours' => $request->get('hours')
            ]);

        return response()->json([
            'message' => 'Successfully changed',
            'hours' => $planned->hours
        ]);
    }
}
