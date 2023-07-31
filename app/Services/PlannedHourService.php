<?php

namespace App\Services;

use App\Models\PlannedHour;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PlannedHourService
{
    public function hoursByFilter(array $filter)
    {
        $query = PlannedHour::query();

        $this->filterToQuery($query, $filter);

        return $query->get();
    }

    public function hoursByFilterWithPerformance(array $filter)
    {
        $query = PlannedHour::query()
            ->select(['planned_hours.*'])
            ->selectRaw('IF(projects.no_performance=1, hours,  ROUND(hours * IF(engineers.performance>0, engineers.performance, IF(levels.performance>0, levels.performance, 100)) / 100)) as real_hours')
            ->join('engineers', 'engineers.id', '=', 'planned_hours.planable_id')
            ->join('projects', 'projects.id', '=', 'planned_hours.project_id')
            ->leftJoin('levels', 'levels.id', '=', 'engineers.level_id');

        $this->filterToQuery($query, $filter);

        return $query->get();
    }

    public function storeHours(array $attributes, array $values): void
    {
        if ($values['hours'] == 0) {
            PlannedHour::where($attributes)->delete();

            return;
        }

        PlannedHour::query()->updateOrCreate($attributes, $values);
    }

    public function groupedHoursByFilter(array $filter)
    {
        $query = PlannedHour::query()->select([
            'project_id',
            'planable_type',
            'year',
            'period_number',
        ])->selectRaw('SUM(hours) as sum_hours');

        $this->groupedHoursFilterToQuery($query, $filter);

        $query->groupBy([
            'project_id',
            'planable_type',
            'year',
            'period_number',
        ]);

        return $query->get();
    }

    public function groupedHoursByFilterWithPerformance(array $filter)
    {
        $query = PlannedHour::query()->select([
            'planned_hours.project_id',
            'planned_hours.year',
            'planned_hours.period_number',
        ])
            ->selectRaw('SUM(hours) as sum_hours')
            ->selectRaw('SUM(IF(projects.no_performance=1, hours,  ROUND(hours * IF(engineers.performance>0, engineers.performance, IF(levels.performance>0, levels.performance, 100)) / 100))) as sum_real_hours')
            ->join('projects', 'projects.id', '=', 'planned_hours.project_id')
            ->join('engineers', 'engineers.id', '=', 'planned_hours.planable_id')
            ->leftJoin('levels', 'levels.id', '=', 'engineers.level_id');

        $this->groupedHoursFilterToQuery($query, $filter);

        $query->groupBy([
            'project_id',
            'year',
            'period_number',
        ]);

        return $query->get();
    }

    public function canEditPeriodByFilter(array $filter): bool
    {
        return $this->canEditPeriod((int)$filter['year'], (int)$filter['period_number'], $filter['period_type']);
    }

    public function canEditPeriod(int $year, int $periodNumber, string $periodType): bool
    {
        if (!config('app.con_edit_conditions')) {
            return true;
        }

        if ($periodType == PlannedHour::WEEK_PERIOD_TYPE) {
            return Carbon::now()->setISODate($year, $periodNumber)->startOfWeek()->gt(Carbon::now());
        }

        return Carbon::now()->setYear($year)->setMonth($periodNumber)->startOfMonth()->gt(Carbon::now());
    }

    protected function filterToQuery(Builder $query, array $filter)
    {
        if (!empty($filter['year'])) {
            $query->where('year', $filter['year']);
        }

        if (!empty($filter['period_type'])) {
            $query->where('period_type', $filter['period_type']);
        }

        if (!empty($filter['period_number'])) {
            $query->where('period_number', $filter['period_number']);
        }

        if (!empty($filter['planable_type'])) {
            $query->where('planable_type', $filter['planable_type']);
        }

        if (!empty($filter['planable_ids'])) {
            $query->whereIn('planable_id', $filter['planable_ids']);
        }
    }

    protected function groupedHoursFilterToQuery(Builder $query, array $filter)
    {
        if (!empty($filter['planable_type'])) {
            $query->where('planable_type', $filter['planable_type']);
        }

        if (!empty($filter['from_year'])) {
            $query->where('year', '>=', $filter['from_year']);
        }

        if (!empty($filter['from_year']) && !empty($filter['from_period_number'])) {
            $query->where(function ($q) use ($filter) {
                $q->where(function ($qYear) use ($filter) {
                    $qYear->where('year', '=', $filter['from_year'])
                        ->where('period_number', '>=', $filter['from_period_number']);
                })->orWhere('year', '>', $filter['from_year']);
            });
        }

        if (!empty($filter['to_year']) && !empty($filter['to_period_number'])) {
            $query->where(function ($q) use ($filter) {
                $q->where(function ($qYear) use ($filter) {
                    $qYear->where('year', '=', $filter['to_year'])
                        ->where('period_number', '<=', $filter['to_period_number']);
                })->orWhere('year', '<', $filter['to_year']);
            });
        }

        if (!empty($filter['period_type'])) {
            $query->where('period_type', $filter['period_type']);
        }

        if (!empty($filter['projects_ids'])) {
            $query->whereIn('project_id', $filter['projects_ids']);
        }
    }
}
