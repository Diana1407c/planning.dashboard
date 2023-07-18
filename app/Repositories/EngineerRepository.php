<?php

namespace App\Repositories;

use App\Http\Requests\TeamRequest;
use App\Models\Engineer;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EngineerRepository
{
    public static function withoutTeam(): Collection|array
    {
        return Engineer::query()->whereNull('team_id')->get();
    }

    public static function team(int $team_id)
    {
        return Engineer::query()->where('team_id', $team_id)->get();
    }
}
