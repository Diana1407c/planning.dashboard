<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EngineerPerformanceRequest;
use App\Http\Resources\EngineerPerformanceResource;
use App\Models\Engineer;
use App\Models\EngineerPerformance;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EngineerPerformanceController extends Controller
{
    public function index(Engineer $engineer): AnonymousResourceCollection
    {
        $performances = $engineer->performances()->orderBy('from_date', 'desc')->get();

        return EngineerPerformanceResource::collection($performances);
    }

    public function store(Engineer $engineer, EngineerPerformanceRequest $request)
    {
        $engineer->performances()->create($request->validated());

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function update(Engineer $engineer, EngineerPerformance $engineerPerformance, EngineerPerformanceRequest $request)
    {
        $engineerPerformance->fill($request->validated());
        $engineerPerformance->save();

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function delete(Engineer $engineer, EngineerPerformance $engineerPerformance)
    {
        $engineerPerformance->delete();

        return response()->json([], 204);
    }
}
