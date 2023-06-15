<?php

namespace App\Services;

use App\Models\PMPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PMPlanningService
{
    public static function filterDetail(array $filters): Collection
    {
        $query = PMPlanning::query();

        if(isset($filters['range'])){
            self::filterRange($query, $filters['range']);
        }
        else if(isset($filters['week']) && isset($filters['year'])) {
            self::filterDate($query, $filters['week'], $filters['year']);
        }

        if(isset($filters['project_id'])) {
            self::filterProject($query, $filters['project_id']);
        }

        return $query->pluck('hours', 'stack_id');
    }

    public static  function filter(array $filters): Collection
    {
        $query = PMPlanning::query()
            ->selectRaw('SUM(hours) as sum_hours, project_id, year, week');

        if(isset($filters['range'])){
            self::filterRange($query, $filters['range']);
        }
        else if(isset($filters['week']) && isset($filters['year'])) {
            self::filterDate($query, $filters['week'], $filters['year']);
        }

        if(isset($filters['project_id'])){
            self::filterProject($query, $filters['project_id']);
        }

        return $query->groupBy(['project_id', 'year', 'week'])->get();
    }

    protected static function filterRange(&$query, array $range): void
    {
        if(isset($range['start_week'])){
            $query->where(function($subQuery) use($range){
                $subQuery->where(function($andQuery) use($range){
                    $andQuery->where('week', '>=', $range['start_week'])
                        ->where('year', $range['start_year']);
                });
                $subQuery->orWhere('year', '>', $range['start_year']);
            });
        }

        if(isset($range['end_week'])){
            $query->where(function($subQuery) use($range){
                $subQuery->where(function($andQuery) use($range){
                    $andQuery->where('week', '<=', $range['end_week'])
                        ->where('year', $range['end_year']);
                });
                $subQuery->orWhere('year', '<', $range['end_year']);
            });
        }
    }

    protected static function filterDate(&$query, int $week, int $year): void
    {
        $query->where('week', $week)->where('year', $year);
    }

    protected static function filterProject(&$query, int $project_id): void
    {
        $query->where('project_id', $project_id);
    }
}
