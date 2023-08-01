<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EngineerHistoryResource;
use App\Models\Engineer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EngineerHistoryController extends Controller
{
    public function index(Engineer $engineer): AnonymousResourceCollection
    {
        $history = $engineer->history()->orderBy('created_at', 'desc')->limit(100)->get();

        return EngineerHistoryResource::collection($history);
    }
}
