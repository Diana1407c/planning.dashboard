<?php

namespace App\Services;

use App\Models\PMPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PMPlanningService
{
    public static function filter(Request $request, array $dates): Collection|array
    {
        $query = PMPlanning::query()
            ->selectRaw('SUM(hours) as sum_hours, project_id, year, week');

        if($dates['week']){
            $query->where(function($subQuery) use($dates){
                $subQuery->where(function($andQuery) use($dates){
                    $andQuery->where('week', '>=', $dates['week'])
                        ->where('year', $dates['year']);
                });
                $subQuery->orWhere('year', '>', $dates['year']);
            });
        }

        if($dates['end_week']){
            $query->where(function($subQuery) use($dates){
                $subQuery->where(function($andQuery) use($dates){
                    $andQuery->where('week', '<=', $dates['end_week'])
                        ->where('year', $dates['end_year']);
                });
                $subQuery->orWhere('year', '<', $dates['end_year']);
            });
        }

        return $query->groupBy(['project_id', 'year', 'week'])->get();
    }
}
