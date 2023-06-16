<?php

namespace App\Services\Base;

trait FilterBase
{
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

    private static function filterProject(&$query, mixed $project_ids = []): void
    {
        if($project_ids){
            if(is_array($project_ids)){
                $query->whereIn('project_id', $project_ids);
                return;
            }

            $query->where('project_id', $project_ids);
        }
    }

    protected static function filterWeek(&$query, int $week, int $year): void
    {
        $query->where('week', $week)->where('year', $year);
    }
}
