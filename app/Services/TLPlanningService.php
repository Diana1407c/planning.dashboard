<?php

namespace App\Services;

use App\Models\TLPlanning;
use App\Services\Base\FilterBase;
use Illuminate\Support\Collection;

class TLPlanningService
{
    use FilterBase;
    public static  function hoursByEngineer(array $filters): Collection
    {
        $query = TLPlanning::query();

        self::filter($query, $filters);

        return $query->pluck('hours', 'engineer_id');
    }
    public static  function sumHoursByProjectAndDate(array $filters): Collection
    {
        $query = TLPlanning::query()->selectRaw('SUM(hours) as sum_hours, project_id, year, week');

        self::filter($query, $filters);

        return $query->groupBy(['project_id', 'year', 'week'])->get();
    }

    protected static function filter(&$query, array $filters): void
    {
        if(isset($filters['range'])){
            self::filterRange($query, $filters['range']);
        }
        else if(isset($filters['week']) && isset($filters['year'])) {
            self::filterWeek($query, $filters['week'], $filters['year']);
        }

        if(isset($filters['project_id'])){
            self::filterProject($query, $filters['project_id']);
        }
    }
}
