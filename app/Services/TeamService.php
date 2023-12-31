<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TeamService
{
    public static function filter(Request $request): Collection|array
    {
        $query = Team::query()->with(['technologies', 'members', 'members.performance', 'members.performance.level']);

        if ($team_ids = $request->get('team_ids')) {
            $query->whereIn('id', $team_ids);
        }

        return $query->get();
    }

    public function teamsByEngineers(array|Collection $engineerIds)
    {
        return Team::query()->select('teams.*')
            ->join('engineers', function ($join) use ($engineerIds) {
                $join->on('teams.id', 'engineers.team_id')
                    ->whereIn('engineers.id', $engineerIds);
            })->with(['members', 'technologies'])->distinct('teams.id')->get();
    }
}
