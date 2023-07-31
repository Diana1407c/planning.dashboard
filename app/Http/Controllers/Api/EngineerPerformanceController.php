<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EngineerPerformanceRequest;
use App\Http\Resources\EngineerPerformanceResource;
use App\Models\Engineer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EngineerPerformanceController extends Controller
{
    public function index(Engineer $engineer): AnonymousResourceCollection
    {
        return EngineerPerformanceResource::collection($engineer->performances);
    }

    public function store(Engineer $engineer, EngineerPerformanceRequest $request)
    {
        $engineer->performances()->create($request->validated());

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function update(Engineer $engineer, int $id, EngineerPerformanceRequest $request)
    {
        $engineer->performances()->where('id', $id)->update($request->validated());

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function delete(Engineer $engineer, int $id)
    {
        $engineer->performances()->where('id', $id)->delete();

        return response()->json([], 204);
    }
}
