<?php

namespace App\Services\Teamwork;

use App\Models\Engineer;
use App\Models\PlannedHour;
use App\Models\Project;
use App\Models\TeamworkTime;
use App\Support\GenericPeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TeamworkService
{
    public static function getLoggedTime($filters): Collection|array
    {
        $query = TeamworkTime::query()
            ->select(
                'engineers.id as engineer_id',
                'engineers.first_name',
                'engineers.last_name',
                'technologies.id as technology_id',
                'stacks.id as stack_id'
            )
            ->selectRaw('YEAR(teamwork_time.date) as year, MONTH(teamwork_time.date) as month, sum(teamwork_time.hours) as hours')
            ->join('engineers', 'engineers.id', '=', 'teamwork_time.engineer_id')
            ->join('teams', 'teams.id', '=', 'engineers.team_id')
            ->join('technologies', 'technologies.id', '=', 'teams.technology_id')
            ->join('stacks', 'stacks.id', '=', 'technologies.stack_id')
            ->groupBy(['stacks.id', 'technologies.id', 'engineers.id', 'engineers.first_name', 'engineers.last_name', 'year', 'month']);

        if ($filters['type'] == 'billable') {
            $query->where('teamwork_time.billable', true);
        } else if ($filters['type'] == 'non_billable') {
            $query->where('teamwork_time.billable', false);
        }

        if ($filters['engineer_ids']) {
            $query->whereIn('engineers.id', $filters['engineer_ids']);
        }

        if ($filters['technology_ids']) {
            $query->whereIn('technologies.id', $filters['technology_ids']);
        }

        if ($filters['stack_ids']) {
            $query->whereIn('stacks.id', $filters['stack_ids']);
        }

        if ($range = $filters['range']) {
            if (isset($range['start_month'])) {
                $query->where(function ($subQuery) use ($range) {
                    $subQuery->where(function ($andQuery) use ($range) {
                        $andQuery->whereRaw('MONTH(teamwork_time.date) >= ' . $range['start_month'])
                            ->whereRaw('YEAR(teamwork_time.date) = ' . $range['start_year']);
                    });
                    $subQuery->orWhereRaw("YEAR(teamwork_time.date) > " . $range['start_year']);
                });
            }

            if (isset($range['end_month'])) {
                $query->where(function ($subQuery) use ($range) {
                    $subQuery->where(function ($andQuery) use ($range) {
                        $andQuery->whereRaw('MONTH(teamwork_time.date) <= ' . $range['end_month'])
                            ->whereRaw('YEAR(teamwork_time.date) = ' . $range['end_year']);
                    });
                    $subQuery->orWhereRaw('YEAR(teamwork_time.date) < ' . $range['end_year']);
                });
            }
        }

        return $query->get();
    }

    public static function syncEngineers(): void
    {
        $engineers = (new TeamworkProxy())->getPeople();

        foreach ($engineers as $engineer) {
            $id = $engineer['id'];
            $data = array_diff_key($engineer, ['id' => '']);

            Engineer::query()->updateOrCreate(
                ['id' => $id],
                $data
            );
        }
    }

    public static function syncEngineer(int $id): bool
    {
        $engineer = (new TeamworkProxy())->getPerson($id);
        if ($engineer) {
            $id = $engineer['id'];
            $data = array_diff_key($engineer, ['id' => '']);

            Engineer::query()->updateOrCreate(
                ['id' => $id],
                $data
            );

            return true;
        }

        return false;
    }

    public static function syncProject(int $id): bool
    {
        $project = (new TeamworkProxy())->getProject($id);
        if ($project) {
            $id = $project['id'];
            $data = array_diff_key($project, ['id' => '']);

            Project::query()->updateOrCreate(
                ['id' => $id],
                $data
            );

            return true;
        }

        return false;
    }

    public static function syncProjects(): void
    {
        $projects = (new TeamworkProxy())->getProjects();

        foreach ($projects as $project) {
            $id = $project['id'];
            $data = array_diff_key($project, ['id' => '']);

            Project::query()->updateOrCreate(
                ['id' => $id],
                $data
            );
        }
    }

    public static function syncTimeEntries(Carbon $fromDate, Carbon $toDate): void
    {
        $entries = (new TeamworkProxy())->getTimeEntries($fromDate, $toDate);
        foreach ($entries as $entry) {
            $id = $entry['id'];
            $data = array_diff_key($entry, ['id' => '']);

            try {
                TeamworkTime::query()->updateOrCreate(
                    ['id' => $id],
                    $data
                );
            } catch (\Exception $exception) {
                if (self::syncEngineer(intval($entry['engineer_id'])) && self::syncProject(intval($entry['project_id']))) {
                    TeamworkTime::query()->updateOrCreate(
                        ['id' => $id],
                        $data
                    );
                }
            }

        }
    }

    public static function groupedHours(array $filters, string $periodType): Collection|array
    {
        $query = TeamworkTime::query()
            ->select([
                'project_id',
            ])
            ->selectRaw('SUM(hours) as sum_hours')
            ->selectRaw('YEAR(date) as year');

        if ($periodType == PlannedHour::WEEK_PERIOD_TYPE) {
            $query->selectRaw('WEEK(date) as period_number');
        } else {
            $query->selectRaw('MONTH(date) as period_number');
        }

        self::applyFilter($query, $filters);

        $query->groupBy([
            'project_id',
            'year',
            'period_number',
        ]);

        return $query->get();
    }

    public static function technologiesHours(array $filters): Collection|array
    {
        $query = TeamworkTime::query()
            ->select([
                'project_id',
                'technology_id',
            ])
            ->selectRaw('SUM(hours) as sum_hours')
            ->join('engineers', 'engineers.id', '=', 'teamwork_time.engineer_id')
            ->join('team_technology', 'engineers.team_id', '=', 'team_technology.team_id');

        self::applyFilter($query, $filters);

        $query->groupBy([
            'project_id',
            'technology_id',
        ]);

        return $query->get();
    }

    public static function engineersHours(array $filters): Collection|array
    {
        $query = TeamworkTime::query()
            ->select([
                'project_id',
                'engineer_id',
            ])
            ->selectRaw('SUM(hours) as sum_hours');

        self::applyFilter($query, $filters);

        $query->groupBy([
            'project_id',
            'engineer_id',
        ]);

        return $query->get();
    }

    public function projectHours(Project $project, GenericPeriod $period)
    {
        return $project->teamworkTime()
            ->select(['engineer_id', 'billable'])
            ->selectRaw('SUM(hours) as sum_hours')
            ->whereBetween('date', [$period->from, $period->to])
            ->groupBy(['engineer_id', 'billable'])->get();
    }

    protected static function applyFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['projects_ids'])) {
            $query->whereIn('project_id', $filters['projects_ids']);
        }

        if (!empty($filters['from_date'])) {
            $query->where('date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('date', '<=', $filters['to_date']);
        }
    }
}
