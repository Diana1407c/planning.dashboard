<?php

namespace App\Services;

use App\Models\Engineer;
use App\Services\Base\FilterBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EngineerService
{
    use FilterBase;

    public static function applyFilters(Request $request): Collection|array
    {
        return self::filter([
            'team_ids' => $request->get('team_ids'),
            'project_ids' => $request->get('project_ids'),
            'dates' => DateService::rangeToWeeks($request->get('start_date'), $request->get('end_date')),
            'with_planning' => $request->get('with_planning'),
            'min_hours' => $request->get('min_hours'),
            'max_hours' => $request->get('max_hours'),
        ]);
    }

    public static function withTeams(): Collection|array
    {
        $query = Engineer::query();

        self::filterTeams($query);

        return $query->get();
    }
    public static function filter(array $filter): Collection|array
    {
        $query = Engineer::query();

        self::filterTeams($query, $filter['team_ids']);

        if(isset($filter['with_planning'])){
            self::filterPlanning($query, $filter);
        } else {
            $query->with('teamLeadPlannings');
        }

        return $query->get();
    }

    private static function filterTeams(Builder &$query, mixed $team_ids = []): void
    {
        if($team_ids){
            $query->whereIn('team_id', $team_ids);
        } else {
            $query->whereNotNull('team_id');
        }
    }

    private static function filterPlanning(Builder &$query, array $filter): void
    {
        if($filter['with_planning'] == 'with'){
            $query->withWhereHas('teamLeadPlannings', function ($plannings) use($filter){
                self::filterRange($plannings, $filter['dates']);
                self::filterProject($plannings, $filter['project_ids']);
            });

            if($filter['min_hours'] || $filter['max_hours']){
                $query->whereHas('teamLeadPlannings', function ($plannings) use($filter){
                    self::filterProject($plannings, $filter['project_ids']);
                    $plannings->groupBy('engineer_id');
                    if($filter['min_hours']){
                        $plannings->havingRaw('SUM(hours) > ?', [$filter['min_hours']]);
                    }

                    if($filter['max_hours']){
                        $plannings->havingRaw('SUM(hours) < ?', [$filter['max_hours']]);
                    }
                });
            }
        } else {
            $query->whereDoesntHave('teamLeadPlannings', function ($plannings) use($filter) {
                self::filterRange($plannings, $filter['dates']);
                self::filterProject($plannings, $filter['project_ids']);
            });
        }
    }
}
