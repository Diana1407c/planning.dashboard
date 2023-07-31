<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EngineerLevelRequest;
use App\Http\Resources\EngineerLevelResource;
use App\Models\Engineer;
use App\Models\EngineerLevel;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EngineerLevelController extends Controller
{
    public function index(Engineer $engineer): AnonymousResourceCollection
    {
        return EngineerLevelResource::collection($engineer->levels);
    }

    public function store(Engineer $engineer, EngineerLevelRequest $request)
    {
        $engineer->levels()->attach($request->get('level_id'), [
            'from_date' => $request->get('from_date')
        ]);

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function update(Engineer $engineer, int $id, EngineerLevelRequest $request)
    {
        EngineerLevel::query()->where('id', $id)->update($request->validated());

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function delete(Engineer $engineer, int $id)
    {
        EngineerLevel::query()->where('id', $id)->delete();

        return response()->json([], 204);
    }
}
