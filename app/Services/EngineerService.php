<?php

namespace App\Services;

use App\Models\Engineer;
use App\Models\PlannedHour;
use App\Services\Base\FilterBase;
use Carbon\Carbon;
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
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'with_planning' => $request->get('with_planning'),
            'min_hours' => $request->get('min_hours'),
            'max_hours' => $request->get('max_hours'),
        ]);
    }

    public function accountantEngineers(array $filter)
    {
        $query = Engineer::query()
            ->with(['team', 'team.technologies', 'performance', 'performance.level']);

        self::filterTeams($query, $filter['team_ids'] ?? []);

        return $query->get();
    }

    public static function withTeams(): Collection|array
    {
        $query = Engineer::query();

        self::filterTeams($query);

        return $query->get();
    }

    public static function filter(array $filter): Collection|array
    {
        $query = Engineer::query()
            ->with(['plannedHours' => function ($query) use ($filter) {
                self::plannedHoursQuery($query, $filter);
            }]);

        self::filterTeams($query, $filter['team_ids']);

        if (isset($filter['with_planning'])) {
            self::filterPlanning($query, $filter);
        }

        return $query->groupBy('engineers.id')->get();
    }

    public function countEngineersByFilter(array $filters)
    {
        $query = Engineer::query()
            ->whereNotNull('team_id');

        if (!empty($filters['team_ids'])) {
            $query->whereIn('team_id', $filters['team_ids']);
        }
        if (!empty($filters['engineer_ids'])) {
            $query->whereIn('id', $filters['engineer_ids']);
        }

        return $query->count();
    }

    private static function filterTeams(Builder $query, mixed $team_ids = []): void
    {
        if ($team_ids) {
            $query->whereIn('team_id', $team_ids);
        } else {
            $query->whereNotNull('team_id');
        }
    }

    private static function filterPlanning(Builder $query, array $filter): void
    {
        $plannedHoursQuery = PlannedHour::query()
            ->where('planable_type', PlannedHour::ENGINEER_TYPE)
            ->where('period_type', PlannedHour::MONTH_PERIOD_TYPE);

        self::plannedHoursQuery($plannedHoursQuery, $filter);

        if (!empty($filter['min_hours'])) {
            $plannedHoursQuery->havingRaw('SUM(hours) >= ?', [$filter['min_hours']]);
        }

        if (!empty($filter['max_hours'])) {
            $plannedHoursQuery->havingRaw('SUM(hours) <= ?', [$filter['max_hours']]);
        }

        $engineersIds = $plannedHoursQuery->groupBy('planable_id')->pluck('planable_id');

        if ($filter['with_planning'] == 'with') {
            $query->whereIn('id', $engineersIds);

            return;
        }

        $query->whereNotIn('id', $engineersIds);
    }

    private static function plannedHoursQuery($query, array $filter): void
    {
        if (!empty($filter['project_ids'])) {
            $query->whereIn('project_id', $filter['project_ids']);
        }

        if (!empty($filter['start_date'])) {
            $date = Carbon::parse($filter['start_date']);

            $query->where(function (Builder $query) use ($date) {
                $query->where(function (Builder $query) use ($date) {
                    $query->where('year', '=', $date->year)
                        ->where('period_number', '>=', $date->month);
                })->orWhere('year', '>', $date->year);
            });
        }

        if (!empty($filter['end_date'])) {
            $date = Carbon::parse($filter['end_date']);

            $query->where(function (Builder $query) use ($date) {
                $query->where(function (Builder $query) use ($date) {
                    $query->where('year', '=', $date->year)
                        ->where('period_number', '<=', $date->month);
                })->orWhere('year', '<', $date->year);
            });
        }
    }
}
