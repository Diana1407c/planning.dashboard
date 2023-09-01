<?php

namespace App\Services;

use App\Models\Engineer;
use App\Models\PlannedHour;
use App\Models\Project;
use App\Support\Interval\GenericInterval;
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

    public function resetEngineerPerformanceHours(Engineer $engineer, int $percent, Carbon $from, Carbon $to = null): void
    {
        $exceptProjects = Project::query()->where('no_performance', 1)->pluck('id');
        $from->startOfMonth();
        $queryMonth = $engineer->plannedHours()
            ->whereNotIn('project_id', $exceptProjects);

        $queryWeek = clone $queryMonth;
        $this->queryFromPeriod($queryMonth, $from->year, $from->month);
        $this->queryFromPeriod($queryWeek, $from->year, $from->weekOfYear);
        if ($to) {
            $to->endOfMonth();
            $this->queryToPeriod($queryMonth, $from->year, $to->month);
            $this->queryToPeriod($queryWeek, $from->year, $to->weekOfYear);
        }

        $queryMonth->update([
            'performance_hours' => DB::raw("ROUND($percent*hours/100)")
        ]);

        $queryWeek->update([
            'performance_hours' => DB::raw("ROUND($percent*hours/100)")
        ]);
    }

    public function plannedHoursCollection(array $filters, GenericInterval $interval)
    {
        $query = PlannedHour::query()
            ->select([
                'planned_hours.planable_type',
                'planned_hours.year',
                'planned_hours.period_type',
                'planned_hours.period_number',
            ])
            ->where('period_type', $interval->type)
            ->groupBy(['planable_type', 'year', 'period_number'])
            ->selectRaw('SUM(hours) as sum_hours')
            ->selectRaw('SUM(performance_hours) as sum_performance_hours');

        if (!empty($filters['project_types'])) {
            $query->join('projects', 'projects.id', '=', 'planned_hours.project_id')
                ->whereIn('projects.type', $filters['project_types']);
        }

        if (!empty($filters['project_ids'])) {
            $query->whereIn('planned_hours.project_id', $filters['project_ids']);
        }

        $this->queryFromPeriod($query, $interval->from->date->year, $interval->from->periodNumber());
        $this->queryToPeriod($query, $interval->to->date->year, $interval->to->periodNumber());

        return $query->get();
    }

    public function hoursPerProjectType(array $filters, GenericInterval $interval)
    {
        $query = PlannedHour::query()
            ->select([
                'projects.type',
            ])
            ->join('projects', 'projects.id', '=', 'planned_hours.project_id')
            ->where('planned_hours.period_type', $interval->type)
            ->where('planned_hours.planable_type', PlannedHour::ENGINEER_TYPE)
            ->groupBy(['projects.type'])
            ->selectRaw('SUM(planned_hours.performance_hours) as sum_performance_hours');

        if (!empty($filters['project_states'])) {
            $query->whereIn('projects.state', $filters['project_states']);
        }

        if (!empty($filters['project_ids'])) {
            $query->whereIn('planned_hours.project_id', $filters['project_ids']);
        }

        if (!empty($filters['team_ids'])) {
            $query->join('engineers', 'engineers.id', '=', 'planned_hours.planable_id')
                ->whereIn('engineers.team_id', $filters['team_ids']);
        }

        if (!empty($filters['engineer_ids'])) {
            $query->whereIn('planned_hours.planable_id', $filters['engineer_ids']);
        }

        $this->queryFromPeriod($query, $interval->from->date->year, $interval->from->periodNumber());
        $this->queryToPeriod($query, $interval->to->date->year, $interval->to->periodNumber());

        return $query->pluck('sum_performance_hours', 'type');
    }

    public function capacityHoursPerProject(array $filters, GenericInterval $interval)
    {
        $query = PlannedHour::query()
            ->select([
                'projects.name',
            ])
            ->join('projects', 'projects.id', '=', 'planned_hours.project_id')
            ->where('planned_hours.period_type', $interval->type)
            ->where('planned_hours.planable_type', PlannedHour::ENGINEER_TYPE)
            ->groupBy(['project_id'])
            ->selectRaw('SUM(planned_hours.performance_hours) as sum_performance_hours');

        if (!empty($filters['team_ids'])) {
            $query->join('engineers', 'engineers.id', '=', 'planned_hours.planable_id')
                ->whereIn('engineers.team_id', $filters['team_ids']);
        }

        if (!empty($filters['engineer_ids'])) {
            $query->whereIn('planned_hours.planable_id', $filters['engineer_ids']);
        }

        $this->queryFromPeriod($query, $interval->from->date->year, $interval->from->periodNumber());
        $this->queryToPeriod($query, $interval->to->date->year, $interval->to->periodNumber());

        return $query->pluck('sum_performance_hours', 'name');
    }

    protected function queryFromPeriod($query, int $year, int $periodNumber): void
    {
        $query->where(function ($q) use ($year, $periodNumber) {
            $q->where(function ($qYear) use ($year, $periodNumber) {
                $qYear->where('year', '=', $year)
                    ->where('period_number', '>=', $periodNumber);
            })->orWhere('year', '>', $year);
        });
    }

    protected function queryToPeriod($query, int $year, int $periodNumber): void
    {
        $query->where(function ($q) use ($year, $periodNumber) {
            $q->where(function ($qYear) use ($year, $periodNumber) {
                $qYear->where('year', '=', $year)
                    ->where('period_number', '<=', $periodNumber);
            })->orWhere('year', '<', $year);
        });
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

        if (!empty($filter['project_ids'])) {
            $query->whereIn('project_id', $filter['project_ids']);
        }

        if (!empty($filter['project_states'])) {
            $query->whereHas('project', function ($project) use ($filter) {
                $project->whereIn('state', $filter['project_states']);
            });
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
