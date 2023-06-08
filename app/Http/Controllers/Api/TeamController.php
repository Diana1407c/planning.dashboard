<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Short\ShortTeamsResource;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return TeamResource::collection(TeamService::filter($request));
    }

    public function shortIndex(): AnonymousResourceCollection
    {
        return ShortTeamsResource::collection(Team::all());
    }
}
