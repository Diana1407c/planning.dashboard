<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PMPlanningPricesRequest;
use App\Models\PMPlanningPrices;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PMPricesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $projects = ProjectService::filter($request);

        $prices = [];
        foreach ($projects as $project){
            $prices[$project->id] = $project->prices
                ->where('year', $request->get('year'))
                ->where('week', $request->get('week'))
                ->first()->cost ?? 0;
        }

        return response()->json(['prices' => $prices]);
    }

    public function storeOrUpdate(PMPlanningPricesRequest $request): JsonResponse
    {
        $data = $request->validated();
        $unique = array_diff_key($data, ['cost' => '']);

        $price = PMPlanningPrices::updateOrCreate($unique, [
            'cost' => $request->get('cost')
        ]);

        return response()->json([
            'message' => 'Successfully changed',
            'price' => $price->cost
        ]);
    }
}
