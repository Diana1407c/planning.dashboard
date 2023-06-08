<?php

namespace App\Services;

use App\Models\Engineer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EngineerService
{
    public static function filter(Request $request): Collection|array
    {
        $query = Engineer::query()->with('teamLeadPlannings');

        if($team_ids = $request->get('teams_ids')){
            $query->whereIn('team_id', $team_ids);
        } else {
            $query->whereNotNull('team_id');
        }

        return $query->get();
    }
}
