<?php

namespace App\Repositories;

use App\Models\Engineer;
use Illuminate\Database\Eloquent\Collection;


class EngineerRepository
{
    public static function all(): Collection|array
    {
        return Engineer::all();
    }

    public static function withoutTeam(): Collection|array
    {
        return Engineer::query()->whereNull('team_id')->get();
    }

    public static function team(int $team_id)
    {
        return Engineer::query()->where('team_id', $team_id)->get();
    }
}
