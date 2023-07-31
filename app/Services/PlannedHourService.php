<?php

namespace App\Services;

use App\Models\Engineer;
use App\Models\PlannedHour;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
            ->selectRaw('IF(projects.no_performance=1, hours,  ROUND(hours * IF(engineers.performance>0, engineers.performance, IF(levels.performance>0, levels.performance, 100)) / 100)) as performance_hours')
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

    public function calcPerformanceHours(int $hours, int $engineerId): int
    {
        if (!$hours) {
            return 0;
        }

        $engineer = Engineer::find($engineerId);

        if (!$engineer) {
            return $hours;
        }

        if ($percent = $engineer->performancePercent()) {
            return round($hours * $percent / 100);
        }

        return $hours;
    }

    public function groupedHoursByFilter(array $filter)
    {
        $query = PlannedHour::query()->select([
            'project_id',
            'planable_type',
            'year',
            'period_number',
        ])
            ->selectRaw('SUM(hours) as sum_hours')
            ->selectRaw('SUM(performance_hours) as sum_performance_hours');

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
            ->selectRaw('SUM(IF(projects.no_performance=1, hours,  ROUND(hours * IF(engineers.performance>0, engineers.performance, IF(levels.performance>0, levels.performance, 100)) / 100))) as sum_performance_hours')
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

    public function resetPerformanceHours(Engineer $engineer, int $percent, Carbon $from, Carbon $to = null)
    {
        $exceptProjects = Project::query()->where('no_performance', 1)->pluck('id');

        $from->startOfMonth();
        $query = $engineer->plannedHours()
            ->whereNotIn('project_id', $exceptProjects)
            ->where('period_type', PlannedHour::MONTH_PERIOD_TYPE)
            ->where(function ($q) use ($from) {
                $q->where(function ($qYear) use ($from) {
                    $qYear->where('year', '=', $from->year)
                        ->where('period_number', '>=', $from->month);
                })->orWhere('year', '>', $from->year);
            });

        if ($to) {
            $to->endOfMonth();
            $query->where(function ($q) use ($to) {
                $q->where(function ($qYear) use ($to) {
                    $qYear->where('year', '=', $to->year)
                        ->where('period_number', '<=', $to->month);
                })->orWhere('year', '<', $to->year);
            });
        }

        $query->update([
            'performance_hours' => DB::raw("ROUND($percent*hours/100)")
        ]);
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
