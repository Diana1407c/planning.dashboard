<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\StackResource;
use App\Models\Stack;
use App\Services\StackService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StackController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StackResource::collection(StackService::filter($request));
    }

    public function all(): AnonymousResourceCollection
    {
        return StackResource::collection(Stack::all());
    }
}
